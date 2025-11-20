<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Inertia\Inertia;
use Inertia\Response;

class PackageController extends Controller
{
    public function alrDetails(Request $request): Response
    {
        $query = $request->input('value');

        if (! $query) {
            // Keep UX simple: render with empty data so the page shows a friendly empty state
            return Inertia::render('PackageViewALR', [
                'query' => $query,
                'data' => [
                    'version' => 0,
                    'limit' => 0,
                    'valid' => false,
                    'results' => [],
                    'type' => '',
                    'num_pages' => 0,
                    'page' => 0,
                ],
            ]);
        }

        try {
            $data = $this->fetchALRPackageInfo($query);
        } catch (\Throwable $error) {
            Log::error('ALR details fetch failed', ['error' => $error->getMessage()]);
            $data = [
                'version' => 0,
                'limit' => 0,
                'valid' => false,
                'results' => [],
                'type' => '',
                'num_pages' => 0,
                'page' => 0,
            ];
        }

        return Inertia::render('PackageViewALR', [
            'query' => $query,
            'data' => $data,
        ]);
    }

    public function aurDetails(Request $request): Response
    {
        $query = $request->input('value');

        if (! $query) {
            return Inertia::render('PackageViewAUR', [
                'query' => $query,
                'data' => [
                    'resultcount' => 0,
                    'results' => [],
                    'type' => '',
                    'version' => 0,
                ],
            ]);
        }

        try {
            $data = $this->fetchAURPackageInfo($query);

            // Normalize date fields to ISO8601 like other controller does
            if (isset($data['results'][0])) {
                $data['results'][0]['LastModified'] = $this->convertEpochToISO8601($data['results'][0]['LastModified'] ?? null);
                $data['results'][0]['OutOfDate'] = $this->convertEpochToISO8601($data['results'][0]['OutOfDate'] ?? null);
            }
        } catch (\Throwable $error) {
            Log::error('AUR details fetch failed', ['error' => $error->getMessage()]);
            $data = [
                'resultcount' => 0,
                'results' => [],
                'type' => '',
                'version' => 0,
            ];
        }

        return Inertia::render('PackageViewAUR', [
            'query' => $query,
            'data' => $data,
        ]);
    }

    /**
     * Fetch package info from ALR repository
     */
    private function fetchALRPackageInfo(string $value): array
    {
        $url = "https://archlinux.org/packages/search/json/?name=$value";

        return Http::get($url)->json();
    }

    /**
     * Fetch package info from AUR repository
     */
    private function fetchAURPackageInfo(string $value): array
    {
        $url = "https://aur.archlinux.org/rpc/v5/info/$value";

        return Http::get($url)->json();
    }

    /**
     * Convert epoch timestamp to ISO8601 format
     */
    private function convertEpochToISO8601($timestamp): ?string
    {
        if (! $timestamp) {
            return null;
        }

        return date('c', $timestamp);
    }
}
