<?php

declare(strict_types=1);

use App\Enums\CourseStatus;
use App\Http\Requests\Courses\UpdateCourseRequest;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

it('always authorizes requests regardless of permissions', function (): void {
    // Arrange
    $user = User::factory()->create();
    // User with no roles or permissions

    $request = new UpdateCourseRequest();
    $request->setUserResolver(fn () => $user);

    // Act & Assert
    expect($request->authorize())->toBeTrue();
});

it('validates title max length', function (): void {
    // Arrange
    $data = [
        'title' => str_repeat('a', 256), // 256 characters, max is 255
    ];

    $validator = validator($data, (new UpdateCourseRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('title'))->toBeTrue();
});

it('validates status is a valid enum value', function (): void {
    // Arrange
    $data = [
        'status' => 'invalid_status',
    ];

    $validator = validator($data, (new UpdateCourseRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('status'))->toBeTrue();
});

it('validates end_date is after or equal to start_date', function (): void {
    // Arrange
    $data = [
        'start_date' => '2023-01-15',
        'end_date' => '2023-01-14', // Before start_date
    ];

    $validator = validator($data, (new UpdateCourseRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('end_date'))->toBeTrue();
});

it('passes validation with valid data', function (): void {
    // Arrange
    $data = [
        'title' => 'Updated Course Title',
        'description' => 'This is an updated course description',
        'status' => CourseStatus::Active->value,
        'is_published' => true,
        'start_date' => '2023-01-15',
        'end_date' => '2023-01-20',
    ];

    $validator = validator($data, (new UpdateCourseRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeFalse();
});

it('passes validation with partial data', function (): void {
    // Arrange - UpdateCourseRequest uses 'sometimes' rule, so partial updates are allowed
    $data = [
        'title' => 'Updated Course Title',
    ];

    $validator = validator($data, (new UpdateCourseRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeFalse();
});
