<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Http\Requests\Users\CreateUserRequest;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Validation\Rules\Password;

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

it('authorizes only admin users', function (): void {
    // Arrange
    $adminUser = User::factory()->create();
    $adminUser->assignRole('admin');

    $nonAdminUser = User::factory()->create();

    // Admin user
    $request = new CreateUserRequest();
    $request->setUserResolver(fn () => $adminUser);

    // Act & Assert - Admin user
    expect($request->authorize())->toBeTrue();

    // Non-admin user
    $request->setUserResolver(fn () => $nonAdminUser);

    // Act & Assert - Non-admin user
    expect($request->authorize())->toBeFalse();
});

it('validates required fields', function (): void {
    // Arrange
    $validator = validator([], (new CreateUserRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('name'))->toBeTrue()
        ->and($validator->errors()->has('email'))->toBeTrue();
});

it('validates name max length', function (): void {
    // Arrange
    $data = [
        'name' => str_repeat('a', 256), // 256 characters, max is 255
        'email' => 'test@example.com',
    ];

    $validator = validator($data, (new CreateUserRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('name'))->toBeTrue();
});

it('validates email format', function (): void {
    // Arrange
    $data = [
        'name' => 'Test User',
        'email' => 'invalid-email',
    ];

    $validator = validator($data, (new CreateUserRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('email'))->toBeTrue();
});

it('validates email uniqueness', function (): void {
    // Arrange
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);

    $data = [
        'name' => 'Test User',
        'email' => 'existing@example.com', // Already exists
    ];

    $validator = validator($data, (new CreateUserRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('email'))->toBeTrue();
});

it('validates password confirmation', function (): void {
    // Arrange
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different-password',
    ];

    $validator = validator($data, (new CreateUserRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('password'))->toBeTrue();
});

it('validates roles array', function (): void {
    // Arrange
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'roles' => 'not-an-array',
    ];

    $validator = validator($data, (new CreateUserRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('roles'))->toBeTrue();
});

it('validates roles enum values', function (): void {
    // Arrange
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'roles' => ['invalid-role'],
    ];

    $validator = validator($data, (new CreateUserRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('roles.0'))->toBeTrue();
});

it('passes validation with valid data', function (): void {
    // Arrange
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'roles' => [UserRole::STUDENT->value],
        'email_verified' => true,
    ];

    $validator = validator($data, (new CreateUserRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeFalse();
});

it('passes validation with minimal valid data', function (): void {
    // Arrange - Only required fields
    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ];

    $validator = validator($data, (new CreateUserRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeFalse();
});
