<?php

declare(strict_types=1);

use App\Enums\CourseStatus;
use App\Http\Requests\Courses\CreateCourseRequest;
use App\Models\User;
use Database\Seeders\RoleSeeder;

beforeEach(function (): void {
    new RoleSeeder()->run();
});

it('always authorizes requests regardless of permissions', function (): void {
    // Arrange
    $user = User::factory()->create();
    // User with no roles or permissions

    $request = new CreateCourseRequest();
    $request->setUserResolver(fn () => $user);

    // Act & Assert
    expect($request->authorize())->toBeTrue();
});

it('validates required fields', function (): void {
    // Arrange
    $validator = validator([], (new CreateCourseRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('title'))->toBeTrue()
        ->and($validator->errors()->has('status'))->toBeTrue();
});

it('validates title max length', function (): void {
    // Arrange
    $data = [
        'title' => str_repeat('a', 256), // 256 characters, max is 255
        'status' => CourseStatus::DRAFT->value,
    ];

    $validator = validator($data, (new CreateCourseRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('title'))->toBeTrue();
});

it('validates status is a valid enum value', function (): void {
    // Arrange
    $data = [
        'title' => 'Test Course',
        'status' => 'invalid_status',
    ];

    $validator = validator($data, (new CreateCourseRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('status'))->toBeTrue();
});

it('validates end_date is after or equal to start_date', function (): void {
    // Arrange
    $data = [
        'title' => 'Test Course',
        'status' => CourseStatus::DRAFT->value,
        'start_date' => '2023-01-15',
        'end_date' => '2023-01-14', // Before start_date
    ];

    $validator = validator($data, (new CreateCourseRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('end_date'))->toBeTrue();
});

it('passes validation with valid data', function (): void {
    // Arrange
    $data = [
        'title' => 'Test Course',
        'description' => 'This is a test course',
        'status' => CourseStatus::DRAFT->value,
        'is_published' => false,
        'start_date' => '2023-01-15',
        'end_date' => '2023-01-20',
    ];

    $validator = validator($data, (new CreateCourseRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeFalse();
});
