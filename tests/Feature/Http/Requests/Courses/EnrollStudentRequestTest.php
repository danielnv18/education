<?php

declare(strict_types=1);

use App\Http\Requests\Courses\EnrollStudentRequest;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

it('always authorizes requests regardless of permissions', function (): void {
    // Arrange
    $user = User::factory()->create();
    // User with no roles or permissions

    $request = new EnrollStudentRequest();
    $request->setUserResolver(fn () => $user);

    // Act & Assert
    expect($request->authorize())->toBeTrue();
});

it('validates required fields', function (): void {
    // Arrange
    $validator = validator([], new EnrollStudentRequest()->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('student_ids'))->toBeTrue();
});

it('validates student_ids is an array', function (): void {
    // Arrange
    $data = [
        'student_ids' => 'not-an-array',
    ];

    $validator = validator($data, new EnrollStudentRequest()->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('student_ids'))->toBeTrue();
});

it('validates student_ids has at least one element', function (): void {
    // Arrange
    $data = [
        'student_ids' => [],
    ];

    $validator = validator($data, new EnrollStudentRequest()->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('student_ids'))->toBeTrue();
});

it('validates each student_id exists in the users table', function (): void {
    // Arrange
    $data = [
        'student_ids' => [999999], // Non-existent user ID
    ];

    $validator = validator($data, new EnrollStudentRequest()->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('student_ids.0'))->toBeTrue();
});

it('passes validation with valid data', function (): void {
    // Arrange
    $user = User::factory()->create();

    $data = [
        'student_ids' => [$user->id],
    ];

    $validator = validator($data, new EnrollStudentRequest()->rules());

    // Act & Assert
    expect($validator->fails())->toBeFalse();
});

it('passes validation with multiple valid student IDs', function (): void {
    // Arrange
    $users = User::factory()->count(3)->create();
    $userIds = $users->pluck('id')->toArray();

    $data = [
        'student_ids' => $userIds,
    ];

    $validator = validator($data, new EnrollStudentRequest()->rules());

    // Act & Assert
    expect($validator->fails())->toBeFalse();
});
