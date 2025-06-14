<?php

declare(strict_types=1);

use App\Actions\Users\SendPasswordResetLinkAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('sends a password reset link to the user', function (): void {
    // Arrange
    $user = User::factory()->create([
        'email' => 'test@example.com',
    ]);

    // Mock Password facade to verify it's called with correct parameters
    Password::shouldReceive('sendResetLink')
        ->once()
        ->with(['email' => 'test@example.com'])
        ->andReturn(Password::RESET_LINK_SENT);

    $action = new SendPasswordResetLinkAction();

    // Act
    $action->handle($user);

    // Assert is handled by the mock expectations
});

it('uses a database transaction', function (): void {
    // Arrange
    $user = User::factory()->create();

    // Mock DB facade to verify transaction is used
    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(function ($callback) {
            return $callback();
        });

    // We need to mock the connection method as well since it's used by the Password broker
    DB::shouldReceive('connection')
        ->andReturn(DB::getFacadeRoot());

    // Mock Password facade to prevent actual reset link from being sent
    Password::shouldReceive('sendResetLink')
        ->andReturn(Password::RESET_LINK_SENT);

    $action = new SendPasswordResetLinkAction();

    // Act
    $action->handle($user);

    // Assert is handled by the mock expectations
});
