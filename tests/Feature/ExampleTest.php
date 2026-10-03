<?php

use Inertia\Testing\AssertableInertia as Assert;

test('returns a successful response', function () {
    $response = $this->get('/');

    $response->assertOk()
        ->assertSee('data-inertia', false)
        ->assertInertia(fn (Assert $page) => $page
            ->component('Home')
            ->where('example', 'Hello World!')
            ->where('auth.user', null)
            ->where('ziggy.routes', fn ($routes) => isset($routes['alr.details']))
        );
});
