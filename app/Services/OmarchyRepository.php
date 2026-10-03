<?php

namespace App\Services;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use PharData;
use RecursiveIteratorIterator;
use RuntimeException;
use Symfony\Component\Process\Process;

class OmarchyRepository
{
    public function packages(): array
    {
        // Search and details share the same database, refreshed on demand every 15 minutes.
        return Cache::remember('omarchy.stable.x86_64', 900, fn () => $this->fetchPackages());
    }

    private function fetchPackages(): array
    {
        $response = Http::connectTimeout(3)->timeout(10)
            ->get('https://pkgs.omarchy.org/stable/x86_64/omarchy.db')->throw();

        $path = tempnam(sys_get_temp_dir(), 'omarchy-');
        if ($path === false) {
            throw new RuntimeException('Could not create a temporary Omarchy database file.');
        }

        try {
            file_put_contents($path, $response->body());
            $process = new Process(['zstd', '--decompress', '--stdout', $path]);
            $process->setTimeout(5)->mustRun();
            file_put_contents($path.'.tar', $process->getOutput());

            $packages = [];
            $archive = new PharData($path.'.tar');
            foreach (new RecursiveIteratorIterator($archive) as $file) {
                if ($file->getFilename() !== 'desc') {
                    continue;
                }

                $fields = $this->parseDescription($file->getContent());
                if (empty($fields['NAME'][0]) || empty($fields['VERSION'][0])) {
                    throw new RuntimeException('Invalid package in the Omarchy database.');
                }

                $name = $fields['NAME'][0];
                $packages[$name] = [
                    'name' => $name,
                    'version' => $fields['VERSION'][0],
                    'description' => $fields['DESC'][0] ?? '',
                    'arch' => $fields['ARCH'][0] ?? '',
                    'url' => $fields['URL'][0] ?? '',
                    'licenses' => $fields['LICENSE'] ?? [],
                    'compressed_size' => (int) ($fields['CSIZE'][0] ?? 0),
                    'installed_size' => (int) ($fields['ISIZE'][0] ?? 0),
                    'build_date' => isset($fields['BUILDDATE'][0]) ? gmdate('c', (int) $fields['BUILDDATE'][0]) : null,
                    'packager' => $fields['PACKAGER'][0] ?? '',
                    'depends' => $fields['DEPENDS'] ?? [],
                    'optdepends' => $fields['OPTDEPENDS'] ?? [],
                    'makedepends' => $fields['MAKEDEPENDS'] ?? [],
                ];
            }

            if (! $packages) {
                throw new RuntimeException('The Omarchy database contains no packages.');
            }

            ksort($packages);

            return $packages;
        } finally {
            unlink($path);
            if (file_exists($path.'.tar')) {
                unlink($path.'.tar');
            }
        }
    }

    private function parseDescription(string $description): array
    {
        $fields = [];
        $field = null;

        foreach (explode("\n", $description) as $line) {
            $line = rtrim($line, "\r");
            if (preg_match('/^%([A-Z0-9_]+)%$/', $line, $matches)) {
                $field = $matches[1];
                $fields[$field] = [];
            } elseif ($line !== '' && $field !== null) {
                $fields[$field][] = $line;
            }
        }

        return $fields;
    }
}
