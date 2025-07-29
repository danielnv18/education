<?php

declare(strict_types=1);

use App\Actions\Users\UpdateUserAction;
use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

it('updates a user with basic data', function (): void {
    // Arrange
    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);

    $userData = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ];

    $action = new UpdateUserAction();

    // Act
    $result = $action->handle($user, $userData);

    // Assert
    $updatedUser = User::findOrFail($user->id);
    expect($updatedUser)->toBeInstanceOf(User::class)
        ->and($updatedUser->name)->toBe('Updated Name')
        ->and($updatedUser->email)->toBe('updated@example.com')
        ->and($result->name)->toBe('Updated Name')
        ->and($result->email)->toBe('updated@example.com');
});

it('updates a user password when provided', function (): void {
    // Arrange
    $user = User::factory()->create([
        'password' => Hash::make('original-password'),
    ]);

    $userData = [
        'name' => $user->name,
        'email' => $user->email,
        'password' => 'new-password123',
    ];

    $action = new UpdateUserAction();

    // Act
    $action->handle($user, $userData);

    // Assert
    $updatedUser = User::findOrFail($user->id);
    expect(Hash::check('new-password123', $updatedUser->password))->toBeTrue();
});

it('does not update password when not provided', function (): void {
    // Arrange
    $user = User::factory()->create([
        'password' => Hash::make('original-password'),
    ]);
    $originalPassword = $user->password;

    $userData = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
    ];

    $action = new UpdateUserAction();

    // Act
    $action->handle($user, $userData);

    // Assert
    $updatedUser = User::findOrFail($user->id);
    expect($updatedUser->password)->toBe($originalPassword);
});

it('updates email verification status when specified', function (): void {
    // Arrange
    $user = User::factory()->create([
        'email_verified_at' => null,
    ]);

    $userData = [
        'name' => $user->name,
        'email' => $user->email,
        'email_verified' => true,
    ];

    $action = new UpdateUserAction();

    // Act
    $action->handle($user, $userData);

    // Assert
    $updatedUser = User::findOrFail($user->id);
    expect($updatedUser->email_verified_at)->not->toBeNull();
});

it('removes email verification when specified', function (): void {
    // Arrange
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $userData = [
        'name' => $user->name,
        'email' => $user->email,
        'email_verified' => false,
    ];

    $action = new UpdateUserAction();

    // Act
    $action->handle($user, $userData);

    // Assert
    $updatedUser = User::findOrFail($user->id);
    expect($updatedUser->email_verified_at)->toBeNull();
});

it('updates user roles when specified', function (): void {
    // Arrange
    $user = User::factory()->create();
    $user->assignRole(UserRole::Student);

    $userData = [
        'name' => $user->name,
        'email' => $user->email,
        'roles' => [UserRole::Teacher->value],
    ];

    $action = new UpdateUserAction();

    // Act
    $action->handle($user, $userData);

    // Assert
    $updatedUser = User::findOrFail($user->id);
    $updatedUser->load('roles');

    expect($updatedUser->hasRole(UserRole::Student))->toBeFalse()
        ->and($updatedUser->hasRole(UserRole::Teacher))->toBeTrue();
});

it('uses a database transaction', function (): void {
    // Arrange
    $user = User::factory()->create();

    $userData = [
        'name' => 'Transaction Test',
        'email' => 'transaction@example.com',
    ];

    // Mock DB facade to verify the transaction is used
    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(function ($callback) {
            return $callback();
        });

    $action = new UpdateUserAction();

    // Act
    $action->handle($user, $userData);

    // Assert is handled by the mock expectations
});
