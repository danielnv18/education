<?php

declare(strict_types=1);

use App\Actions\Users\CreateUserAction;
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

it('creates a user with basic data', function (): void {
    // Arrange
    $userData = [
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'password' => 'password123',
    ];

    $action = new CreateUserAction();

    // Act
    $user = $action->handle($userData);

    // Assert
    $user = User::findOrFail($user->id);
    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('John Doe')
        ->and($user->email)->toBe('john@example.com')
        ->and(Hash::check('password123', $user->password))->toBeTrue()
        ->and($user->email_verified_at)->toBeNull();
});

it('creates a user with verified email when specified', function (): void {
    // Arrange
    $userData = [
        'name' => 'Jane Doe',
        'email' => 'jane@example.com',
        'password' => 'password123',
        'email_verified' => true,
    ];

    $action = new CreateUserAction();

    // Act
    $user = $action->handle($userData);

    // Assert
    $user = User::findOrFail($user->id);
    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('Jane Doe')
        ->and($user->email)->toBe('jane@example.com')
        ->and($user->email_verified_at)->not->toBeNull();
});

it('assigns roles to a user when specified', function (): void {
    // Arrange
    $userData = [
        'name' => 'Admin User',
        'email' => 'admin@example.com',
        'password' => 'password123',
        'roles' => [UserRole::Admin->value],
    ];

    $action = new CreateUserAction();

    // Act
    $user = $action->handle($userData);

    // Assert
    $user = User::findOrFail($user->id);
    expect($user)->toBeInstanceOf(User::class)
        ->and($user->hasRole(UserRole::Admin))->toBeTrue();
});

it('assigns multiple roles to a user when specified', function (): void {
    // Arrange
    $userData = [
        'name' => 'Teacher Admin',
        'email' => 'teacher.admin@example.com',
        'password' => 'password123',
        'roles' => [UserRole::Teacher->value, UserRole::Admin->value],
    ];

    $action = new CreateUserAction();

    // Act
    $user = $action->handle($userData);

    // Assert
    $user = User::findOrFail($user->id);
    expect($user)->toBeInstanceOf(User::class)
        ->and($user->hasRole(UserRole::Teacher))->toBeTrue()
        ->and($user->hasRole(UserRole::Admin))->toBeTrue();
});

it('generates a password when not provided', function (): void {
    // Arrange
    $userData = [
        'name' => 'Auto Password User',
        'email' => 'auto.password@example.com',
    ];

    $action = new CreateUserAction();

    // Act
    $user = $action->handle($userData);

    // Assert
    $user = User::findOrFail($user->id);
    expect($user)->toBeInstanceOf(User::class)
        ->and($user->password)->not->toBeEmpty();
});

it('uses a database transaction', function (): void {
    // Arrange
    $userData = [
        'name' => 'Transaction Test',
        'email' => 'transaction@example.com',
        'password' => 'password123',
    ];

    // Mock DB facade to verify the transaction is used
    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(function ($callback) {
            return $callback();
        });

    $action = new CreateUserAction();

    // Act
    $action->handle($userData);

    // Assert is handled by the mock expectations
});
