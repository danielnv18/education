<?php

declare(strict_types=1);

use App\Models\User;
use Inertia\Testing\AssertableInertia;

test('verified user is redirected to dashboard', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->get(route('verification.notice'));

    $response->assertRedirect(route('dashboard'));
});

test('unverified user sees verification prompt', function () {
    $user = User::factory()->unverified()->create();

    $response = $this->actingAs($user)
        ->get(route('verification.notice'));

    $response->assertInertia(fn (AssertableInertia $page) => $page->component('auth/verify-email')
    );
});

test('verification prompt includes status from session', function () {
    $user = User::factory()->unverified()->create();
    $status = 'custom-status';

    $response = $this->actingAs($user)
        ->session(['status' => $status])
        ->get(route('verification.notice'));

    $response->assertInertia(fn (AssertableInertia $page) => $page->component('auth/verify-email')
        ->where('status', $status)
    );
});
