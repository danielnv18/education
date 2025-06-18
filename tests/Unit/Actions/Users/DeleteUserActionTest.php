<?php

declare(strict_types=1);

use App\Actions\Users\DeleteUserAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('deletes a user', function (): void {
    // Arrange
    $user = User::factory()->create();
    $userId = $user->id;

    $action = new DeleteUserAction();

    // Act
    $action->handle($user);

    // Assert
    expect(User::find($userId))->toBeNull();
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

    $action = new DeleteUserAction();

    // Act
    $action->handle($user);

    // Assert is handled by the mock expectations
});
