<?php

declare(strict_types=1);

use App\Enums\LessonType;
use App\Http\Requests\Lessons\CreateLessonRequest;
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

    $request = new CreateLessonRequest();
    $request->setUserResolver(fn () => $user);

    // Act & Assert
    expect($request->authorize())->toBeTrue();
});

it('validates required fields', function (): void {
    // Arrange
    $validator = validator([], (new CreateLessonRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('title'))->toBeTrue()
        ->and($validator->errors()->has('module_id'))->toBeTrue()
        ->and($validator->errors()->has('type'))->toBeTrue();
});

it('validates title max length', function (): void {
    // Arrange
    $module = Module::factory()->create();
    $data = [
        'title' => str_repeat('a', 256), // 256 characters, max is 255
        'module_id' => $module->id,
        'type' => LessonType::TEXT->value,
    ];

    $validator = validator($data, (new CreateLessonRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('title'))->toBeTrue();
});

it('validates module_id exists', function (): void {
    // Arrange
    $data = [
        'title' => 'Test Lesson',
        'module_id' => 9999, // Non-existent module ID
        'type' => LessonType::TEXT->value,
    ];

    $validator = validator($data, (new CreateLessonRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('module_id'))->toBeTrue();
});

it('validates order is an integer and minimum 0', function (): void {
    // Arrange
    $module = Module::factory()->create();
    $data = [
        'title' => 'Test Lesson',
        'module_id' => $module->id,
        'type' => LessonType::TEXT->value,
        'order' => -1, // Invalid order
    ];

    $validator = validator($data, (new CreateLessonRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('order'))->toBeTrue();
});

it('validates type is a valid enum value', function (): void {
    // Arrange
    $module = Module::factory()->create();
    $data = [
        'title' => 'Test Lesson',
        'module_id' => $module->id,
        'type' => 'invalid_type', // Invalid type
    ];

    $validator = validator($data, (new CreateLessonRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('type'))->toBeTrue();
});

it('passes validation with valid data', function (): void {
    // Arrange
    $module = Module::factory()->create();
    $data = [
        'title' => 'Test Lesson',
        'content' => 'This is a test lesson',
        'module_id' => $module->id,
        'order' => 1,
        'type' => LessonType::TEXT->value,
        'is_published' => false,
    ];

    $validator = validator($data, (new CreateLessonRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeFalse();
});
