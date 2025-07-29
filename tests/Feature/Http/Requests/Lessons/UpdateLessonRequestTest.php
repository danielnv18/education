<?php

declare(strict_types=1);

use App\Enums\LessonType;
use App\Http\Requests\Lessons\UpdateLessonRequest;
use App\Models\Module;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

it('always authorizes requests regardless of permissions', function (): void {
    // Arrange
    $user = User::factory()->create();
    // User with no roles or permissions

    $request = new UpdateLessonRequest();
    $request->setUserResolver(fn () => $user);

    // Act & Assert
    expect($request->authorize())->toBeTrue();
});

it('validates title max length', function (): void {
    // Arrange
    $data = [
        'title' => str_repeat('a', 256), // 256 characters, max is 255
    ];

    $validator = validator($data, (new UpdateLessonRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('title'))->toBeTrue();
});

it('validates module_id exists', function (): void {
    // Arrange
    $data = [
        'module_id' => 9999, // Non-existent module ID
    ];

    $validator = validator($data, (new UpdateLessonRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('module_id'))->toBeTrue();
});

it('validates order is an integer and minimum 0', function (): void {
    // Arrange
    $data = [
        'order' => -1, // Invalid order
    ];

    $validator = validator($data, (new UpdateLessonRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('order'))->toBeTrue();
});

it('validates type is a valid enum value', function (): void {
    // Arrange
    $data = [
        'type' => 'invalid_type', // Invalid type
    ];

    $validator = validator($data, (new UpdateLessonRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('type'))->toBeTrue();
});

it('passes validation with valid data', function (): void {
    // Arrange
    $module = Module::factory()->create();
    $data = [
        'title' => 'Updated Lesson Title',
        'content' => 'This is an updated lesson content',
        'module_id' => $module->id,
        'order' => 2,
        'type' => LessonType::Video->value,
        'is_published' => true,
    ];

    $validator = validator($data, (new UpdateLessonRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeFalse();
});

it('passes validation with partial data', function (): void {
    // Arrange - UpdateLessonRequest uses 'sometimes' rule, so partial updates are allowed
    $data = [
        'title' => 'Updated Lesson Title',
    ];

    $validator = validator($data, (new UpdateLessonRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeFalse();
});
