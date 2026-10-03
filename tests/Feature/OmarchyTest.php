<?php

use App\Services\OmarchyRepository;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Inertia\Testing\AssertableInertia as Assert;
use Symfony\Component\Process\Process;

function omarchyTestDatabase(array $descriptions): string
{
    $path = tempnam(sys_get_temp_dir(), 'omarchy-test-');

    try {
        $archive = new PharData($path.'.tar');
        foreach ($descriptions as $index => $description) {
            $archive->addFromString("package-$index/desc", $description);
        }
        unset($archive);

        return (new Process(['zstd', '--compress', '--stdout', $path.'.tar']))->mustRun()->getOutput();
    } finally {
        unlink($path);
        unlink($path.'.tar');
    }
}

beforeEach(function () {
    $this->withoutVite();
    config([
        'app.key' => 'base64:'.base64_encode(str_repeat('x', 32)),
        'inertia.ssr.enabled' => false,
    ]);
    Cache::flush();
    Http::preventStrayRequests();

    $this->demoDescription = file_get_contents(base_path('tests/Fixtures/omarchy/demo.desc'));
    $this->database = omarchyTestDatabase([
        $this->demoDescription,
        file_get_contents(base_path('tests/Fixtures/omarchy/companion.desc')),
    ]);
    $this->omarchyBody = $this->database;
    $this->omarchyStatus = 200;

    Http::fake([
        'pkgs.omarchy.org/*' => fn () => Http::response($this->omarchyBody, $this->omarchyStatus),
        'archlinux.org/*' => Http::response(['results' => [
            ['pkgname' => 'omarchy-demo', 'pkgver' => '1.0', 'repo' => 'extra'],
        ]]),
        'aur.archlinux.org/*' => Http::response(['results' => [
            ['Name' => 'omarchy-demo', 'Version' => '1.1'],
        ]]),
    ]);
});

test('reads package metadata and refreshes the shared cache after fifteen minutes', function () {
    $repository = app(OmarchyRepository::class);
    $packages = $repository->packages();

    expect($packages['omarchy-demo'])
        ->version->toBe('1:1.2.3-4')
        ->licenses->toBe(['MIT', 'BSD-3-Clause'])
        ->depends->toBe(['glibc>=2.38', 'gum'])
        ->optdepends->toBe(['git: version control'])
        ->makedepends->toBe(['meson'])
        ->compressed_size->toBe(2048)
        ->installed_size->toBe(4096)
        ->build_date->toBe('2023-11-14T22:13:20+00:00');
    expect($packages['other-tool']['depends'])->toBe([]);
    expect($packages['other-tool']['build_date'])->toBeNull();

    $this->travel(14)->minutes();
    $this->get('/omarchy-details?value=omarchy-demo')->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('PackageViewOmarchy')
            ->where('package.version', '1:1.2.3-4')
            ->where('error', null));
    Http::assertSentCount(1);

    $updated = omarchyTestDatabase([str_replace('1:1.2.3-4', '1:1.2.4-1', $this->demoDescription)]);
    $this->omarchyBody = $updated;
    $this->travel(2)->minutes();

    $this->get('/omarchy-details?value=omarchy-demo')->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('package.version', '1:1.2.4-1'));
    Http::assertSentCount(2);
    expect($repository->packages()['omarchy-demo']['version'])->toBe('1:1.2.4-1');
    Http::assertSentCount(2);
});

test('searches Omarchy names and descriptions alongside Arch and AUR', function () {
    $this->get('/search?value=OMARCHY')->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->component('SearchResults')
            ->has('data', 4)
            ->where('data.0.source', 'ALR')
            ->where('data.1.source', 'AUR')
            ->where('data.2.source', 'Omarchy')
            ->where('data.2.name', 'omarchy-demo')
            ->where('data.2.repo', 'stable / x86_64')
            ->where('data.2.last_updated_date', null)
            ->where('data.2.build_date', '2023-11-14T22:13:20+00:00')
            ->where('data.2.flagged_date', null)
            ->where('data.3.name', 'other-tool')
            ->where('omarchyError', null));

    $this->get('/search?value=desktop')->assertOk()
        ->assertInertia(fn (Assert $page) => $page->has('data', 3)->where('data.2.name', 'omarchy-demo'));
    Http::assertSentCount(5);
});

test('keeps Arch and AUR results when an expired Omarchy refresh fails and retries next request', function () {
    app(OmarchyRepository::class)->packages();
    $this->travel(16)->minutes();
    $this->omarchyStatus = 503;

    $this->get('/search?value=omarchy')->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->has('data', 2)
            ->where('omarchyError', 'Omarchy packages are temporarily unavailable. Please try again.'));

    $this->omarchyStatus = 200;
    $this->get('/search?value=omarchy')->assertOk()
        ->assertInertia(fn (Assert $page) => $page->has('data', 4)->where('omarchyError', null));
});

test('does not cache a broken database and can recover', function () {
    $this->omarchyBody = 'This is not a package database';

    $this->get('/omarchy-details?value=omarchy-demo')->assertOk()
        ->assertInertia(fn (Assert $page) => $page
            ->where('package', null)
            ->where('error', 'Omarchy packages are temporarily unavailable. Please try again.'));

    $this->omarchyBody = $this->database;
    $this->get('/omarchy-details?value=omarchy-demo')->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('package.name', 'omarchy-demo')->where('error', null));
});

test('shows a missing package without treating it as a repository failure', function () {
    $this->get('/omarchy-details')->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('package', null)->where('error', null));
    Http::assertNothingSent();

    $this->get('/omarchy-details?value=missing')->assertOk()
        ->assertInertia(fn (Assert $page) => $page->where('package', null)->where('error', null));
});
