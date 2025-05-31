<?php

declare(strict_types=1);

use App\Enums\CourseStatus;
use App\Http\Requests\Courses\CreateCourseRequest;
use App\Models\Course;
use App\Models\User;
use Database\Seeders\RoleSeeder;
use Illuminate\Support\Facades\Gate;

beforeEach(function (): void {
    new RoleSeeder()->run();
});

it('authorizes request when user has create permission', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    // Mock Gate to return true for 'create' ability on Course
    Gate::shouldReceive('check')
        ->with('create', Course::class)
        ->once()
        ->andReturn(true);

    $request = new CreateCourseRequest();
    $request->setUserResolver(fn () => $admin);

    // Act & Assert
    expect($request->authorize())->toBeTrue();
});

it('does not authorize request when user does not have create permission', function (): void {
    // Arrange
    $user = User::factory()->create();
    $user->assignRole('teacher'); // Teacher can't create courses

    // Mock Gate to return false for 'create' ability on Course
    Gate::shouldReceive('check')
        ->with('create', Course::class)
        ->once()
        ->andReturn(false);

    $request = new CreateCourseRequest();
    $request->setUserResolver(fn () => $user);

    // Act & Assert
    expect($request->authorize())->toBeFalse();
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
