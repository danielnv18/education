<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

it('authorizes only admin users', function (): void {
    // Arrange
    $adminUser = User::factory()->create();
    $adminUser->assignRole('admin');

    $nonAdminUser = User::factory()->create();

    // Admin user
    $request = new UpdateUserRequest();
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
    $user = User::factory()->create();
    $request = new UpdateUserRequest();
    $request->user = $user;

    $validator = validator([], $request->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('name'))->toBeTrue()
        ->and($validator->errors()->has('email'))->toBeTrue()
        ->and($validator->errors()->has('roles'))->toBeTrue();
});

it('validates name max length', function (): void {
    // Arrange
    $user = User::factory()->create();
    $request = new UpdateUserRequest();
    $request->user = $user;

    $data = [
        'name' => str_repeat('a', 256), // 256 characters, max is 255
        'email' => 'test@example.com',
        'roles' => [UserRole::STUDENT->value],
    ];

    $validator = validator($data, $request->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('name'))->toBeTrue();
});

it('validates email format', function (): void {
    // Arrange
    $user = User::factory()->create();
    $request = new UpdateUserRequest();
    $request->user = $user;

    $data = [
        'name' => 'Test User',
        'email' => 'invalid-email',
        'roles' => [UserRole::STUDENT->value],
    ];

    $validator = validator($data, $request->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('email'))->toBeTrue();
});

it('validates email uniqueness but ignores current user', function (): void {
    // Arrange
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);
    $userToUpdate = User::factory()->create(['email' => 'user@example.com']);

    $request = new UpdateUserRequest();
    $request->user = $userToUpdate;

    // Test with another user's email
    $data = [
        'name' => 'Test User',
        'email' => 'existing@example.com', // Already exists for another user
        'roles' => [UserRole::STUDENT->value],
    ];

    $validator = validator($data, $request->rules());

    // Act & Assert - Should fail with another user's email
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('email'))->toBeTrue();

    // Test with current user's own email
    $data = [
        'name' => 'Test User',
        'email' => 'user@example.com', // Current user's email
        'roles' => [UserRole::STUDENT->value],
    ];

    $validator = validator($data, $request->rules());

    // Act & Assert - Should pass with current user's email
    expect($validator->fails())->toBeFalse();
});

it('validates password confirmation', function (): void {
    // Arrange
    $user = User::factory()->create();
    $request = new UpdateUserRequest();
    $request->user = $user;

    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'different-password',
        'roles' => [UserRole::STUDENT->value],
    ];

    $validator = validator($data, $request->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('password'))->toBeTrue();
});

it('validates roles array is required', function (): void {
    // Arrange
    $user = User::factory()->create();
    $request = new UpdateUserRequest();
    $request->user = $user;

    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
    ];

    $validator = validator($data, $request->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('roles'))->toBeTrue();
});

it('validates roles array type', function (): void {
    // Arrange
    $user = User::factory()->create();
    $request = new UpdateUserRequest();
    $request->user = $user;

    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'roles' => 'not-an-array',
    ];

    $validator = validator($data, $request->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('roles'))->toBeTrue();
});

it('validates roles enum values', function (): void {
    // Arrange
    $user = User::factory()->create();
    $request = new UpdateUserRequest();
    $request->user = $user;

    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'roles' => ['invalid-role'],
    ];

    $validator = validator($data, $request->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('roles.0'))->toBeTrue();
});

it('passes validation with valid data', function (): void {
    // Arrange
    $user = User::factory()->create();
    $request = new UpdateUserRequest();
    $request->user = $user;

    $data = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'Password123!',
        'password_confirmation' => 'Password123!',
        'roles' => [UserRole::STUDENT->value],
        'email_verified' => true,
    ];

    $validator = validator($data, $request->rules());

    // Act & Assert
    expect($validator->fails())->toBeFalse();
});
