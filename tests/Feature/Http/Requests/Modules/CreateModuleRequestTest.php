<?php

declare(strict_types=1);

use App\Http\Requests\Modules\CreateModuleRequest;
use App\Models\User;
use App\Models\Course;
use Database\Seeders\DatabaseSeeder;

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

it('always authorizes requests regardless of permissions', function (): void {
    // Arrange
    $user = User::factory()->create();
    // User with no roles or permissions

    $request = new CreateModuleRequest();
    $request->setUserResolver(fn () => $user);

    // Act & Assert
    expect($request->authorize())->toBeTrue();
});

it('validates required fields', function (): void {
    // Arrange
    $validator = validator([], (new CreateModuleRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('title'))->toBeTrue()
        ->and($validator->errors()->has('course_id'))->toBeTrue();
});

it('validates title max length', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $data = [
        'title' => str_repeat('a', 256), // 256 characters, max is 255
        'course_id' => $course->id,
    ];

    $validator = validator($data, (new CreateModuleRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('title'))->toBeTrue();
});

it('validates course_id exists', function (): void {
    // Arrange
    $data = [
        'title' => 'Test Module',
        'course_id' => 9999, // Non-existent course ID
    ];

    $validator = validator($data, (new CreateModuleRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('course_id'))->toBeTrue();
});

it('validates order is an integer and minimum 0', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $data = [
        'title' => 'Test Module',
        'course_id' => $course->id,
        'order' => -1, // Invalid order
    ];

    $validator = validator($data, (new CreateModuleRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('order'))->toBeTrue();
});

it('passes validation with valid data', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $data = [
        'title' => 'Test Module',
        'description' => 'This is a test module',
        'course_id' => $course->id,
        'order' => 1,
        'is_published' => false,
    ];

    $validator = validator($data, (new CreateModuleRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeFalse();
});
