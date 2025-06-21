<?php

declare(strict_types=1);

use App\Http\Requests\Modules\UpdateModuleRequest;
use App\Models\Course;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

it('always authorizes requests regardless of permissions', function (): void {
    // Arrange
    $user = User::factory()->create();
    // User with no roles or permissions

    $request = new UpdateModuleRequest();
    $request->setUserResolver(fn () => $user);

    // Act & Assert
    expect($request->authorize())->toBeTrue();
});

it('validates title max length', function (): void {
    // Arrange
    $data = [
        'title' => str_repeat('a', 256), // 256 characters, max is 255
    ];

    $validator = validator($data, (new UpdateModuleRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('title'))->toBeTrue();
});

it('validates course_id exists', function (): void {
    // Arrange
    $data = [
        'course_id' => 9999, // Non-existent course ID
    ];

    $validator = validator($data, (new UpdateModuleRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('course_id'))->toBeTrue();
});

it('validates order is an integer and minimum 0', function (): void {
    // Arrange
    $data = [
        'order' => -1, // Invalid order
    ];

    $validator = validator($data, (new UpdateModuleRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('order'))->toBeTrue();
});

it('passes validation with valid data', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $data = [
        'title' => 'Updated Module Title',
        'description' => 'This is an updated module description',
        'course_id' => $course->id,
        'order' => 2,
        'is_published' => true,
    ];

    $validator = validator($data, (new UpdateModuleRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeFalse();
});

it('passes validation with partial data', function (): void {
    // Arrange - UpdateModuleRequest uses 'sometimes' rule, so partial updates are allowed
    $data = [
        'title' => 'Updated Module Title',
    ];

    $validator = validator($data, (new UpdateModuleRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeFalse();
});
