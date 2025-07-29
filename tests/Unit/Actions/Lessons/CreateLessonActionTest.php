<?php

declare(strict_types=1);

use App\Actions\Lessons\CreateLessonAction;
use App\Enums\LessonType;
use App\Models\Lesson;
use App\Models\Module;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

it('creates a lesson with valid data', function (): void {
    // Arrange
    $module = Module::factory()->create();
    $data = [
        'title' => 'Test Lesson',
        'content' => 'Test Content',
        'module_id' => $module->id,
        'order' => 1,
        'type' => LessonType::Text->value,
        'is_published' => false,
    ];

    $action = new CreateLessonAction();

    // Act
    $lesson = $action->handle($data);

    // Assert
    expect($lesson)->toBeInstanceOf(Lesson::class)
        ->and($lesson->title)->toBe('Test Lesson')
        ->and($lesson->content)->toBe('Test Content')
        ->and($lesson->module_id)->toBe($module->id)
        ->and($lesson->order)->toBe(1)
        ->and($lesson->type)->toBe(LessonType::Text)
        ->and($lesson->is_published)->toBeFalse();
});

it('creates a lesson with different types', function (): void {
    // Arrange
    $module = Module::factory()->create();
    $types = [
        LessonType::Text->value,
        LessonType::Video->value,
        LessonType::Document->value,
        LessonType::Link->value,
        LessonType::Embed->value,
    ];

    $action = new CreateLessonAction();

    // Act & Assert
    foreach ($types as $type) {
        $data = [
            'title' => "Test {$type} Lesson",
            'content' => "Test Content for {$type}",
            'module_id' => $module->id,
            'order' => 1,
            'type' => $type,
            'is_published' => false,
        ];

        $lesson = $action->handle($data);

        expect($lesson)->toBeInstanceOf(Lesson::class)
            ->and($lesson->type->value)->toBe($type);
    }
});

it('creates a published lesson', function (): void {
    // Arrange
    $module = Module::factory()->create();
    $data = [
        'title' => 'Test Lesson',
        'content' => 'Test Content',
        'module_id' => $module->id,
        'order' => 1,
        'type' => LessonType::Text->value,
        'is_published' => true,
    ];

    $action = new CreateLessonAction();

    // Act
    $lesson = $action->handle($data);

    // Assert
    expect($lesson)->toBeInstanceOf(Lesson::class)
        ->and($lesson->is_published)->toBeTrue();
});

it('uses a database transaction', function (): void {
    // Arrange
    $module = Module::factory()->create();
    $data = [
        'title' => 'Test Lesson',
        'content' => 'Test Content',
        'module_id' => $module->id,
        'order' => 1,
        'type' => LessonType::Text->value,
        'is_published' => false,
    ];

    // Mock DB facade to verify the transaction is used
    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(function ($callback) {
            return $callback();
        });

    $action = new CreateLessonAction();

    // Act
    $action->handle($data);

    // Assert is handled by the mock expectations
});
