<?php

use App\Http\Middleware\HandleInertiaRequests;
use Illuminate\Support\Facades\Http;

beforeEach(function () {
    $this->withHeaders([
        'X-Inertia' => 'true',
        'X-Inertia-Version' => app(HandleInertiaRequests::class)->version(request()) ?? '',
    ]);
});

test('search returns packages from both repositories on an Inertia visit', function () {
    Http::preventStrayRequests();
    Http::fake([
        'archlinux.org/packages/search/json/*' => Http::response([
            'results' => [
                ['pkgname' => 'bash', 'pkgver' => '5.3', 'repo' => 'core'],
            ],
        ]),
        'aur.archlinux.org/rpc/v5/search/*' => Http::response([
            'results' => [
                ['Name' => 'bash-git', 'Version' => '5.3', 'LastModified' => 1700000000],
            ],
        ]),
    ]);

    $this->get('/search?value=bash')
        ->assertOk()
        ->assertHeader('X-Inertia', 'true')
        ->assertJsonPath('component', 'SearchResults')
        ->assertJsonCount(2, 'props.data')
        ->assertJsonPath('props.data.0.name', 'bash')
        ->assertJsonPath('props.data.0.source', 'ALR')
        ->assertJsonPath('props.data.1.name', 'bash-git')
        ->assertJsonPath('props.data.1.source', 'AUR');

    Http::assertSentCount(2);
});

test('package detail pages render their empty state on an Inertia visit', function (string $url, string $component) {
    Http::preventStrayRequests();

    $this->get($url)
        ->assertOk()
        ->assertHeader('X-Inertia', 'true')
        ->assertJsonPath('component', $component)
        ->assertJsonPath('props.query', null)
        ->assertJsonPath('props.data.results', []);

    Http::assertNothingSent();
})->with([
    'official repository' => ['/alr-details', 'PackageViewALR'],
    'user repository' => ['/aur-details', 'PackageViewAUR'],
]);
