<?php

declare(strict_types=1);

use App\Actions\Lessons\UpdateLessonAction;
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

it('updates a lesson with valid data', function (): void {
    // Arrange
    $module = Module::factory()->create();
    $lesson = Lesson::factory()->create([
        'module_id' => $module->id,
        'title' => 'Original Title',
        'content' => 'Original Content',
        'order' => 1,
        'type' => LessonType::TEXT,
        'is_published' => false,
    ]);

    $data = [
        'title' => 'Updated Title',
        'content' => 'Updated Content',
        'order' => 2,
        'is_published' => true,
    ];

    $action = new UpdateLessonAction();

    // Act
    $updatedLesson = $action->handle($lesson, $data);

    // Assert
    expect($updatedLesson)->toBeInstanceOf(Lesson::class)
        ->and($updatedLesson->id)->toBe($lesson->id)
        ->and($updatedLesson->title)->toBe('Updated Title')
        ->and($updatedLesson->content)->toBe('Updated Content')
        ->and($updatedLesson->module_id)->toBe($module->id)
        ->and($updatedLesson->order)->toBe(2)
        ->and($updatedLesson->type)->toBe(LessonType::TEXT)
        ->and($updatedLesson->is_published)->toBeTrue();
});

it('updates a lesson type', function (): void {
    // Arrange
    $module = Module::factory()->create();
    $lesson = Lesson::factory()->asText()->create([
        'module_id' => $module->id,
    ]);

    $data = [
        'type' => LessonType::VIDEO->value,
    ];

    $action = new UpdateLessonAction();

    // Act
    $updatedLesson = $action->handle($lesson, $data);

    // Assert
    expect($updatedLesson)->toBeInstanceOf(Lesson::class)
        ->and($updatedLesson->id)->toBe($lesson->id)
        ->and($updatedLesson->type)->toBe(LessonType::VIDEO);
});

it('updates a lesson publication status', function (): void {
    // Arrange
    $module = Module::factory()->create();
    $lesson = Lesson::factory()->unpublished()->create([
        'module_id' => $module->id,
    ]);

    $data = [
        'is_published' => true,
    ];

    $action = new UpdateLessonAction();

    // Act
    $updatedLesson = $action->handle($lesson, $data);

    // Assert
    expect($updatedLesson)->toBeInstanceOf(Lesson::class)
        ->and($updatedLesson->id)->toBe($lesson->id)
        ->and($updatedLesson->is_published)->toBeTrue();
});

it('updates a lesson module', function (): void {
    // Arrange
    $originalModule = Module::factory()->create();
    $newModule = Module::factory()->create();
    $lesson = Lesson::factory()->create([
        'module_id' => $originalModule->id,
    ]);

    $data = [
        'module_id' => $newModule->id,
    ];

    $action = new UpdateLessonAction();

    // Act
    $updatedLesson = $action->handle($lesson, $data);

    // Assert
    expect($updatedLesson)->toBeInstanceOf(Lesson::class)
        ->and($updatedLesson->id)->toBe($lesson->id)
        ->and($updatedLesson->module_id)->toBe($newModule->id);
});

it('uses a database transaction', function (): void {
    // Arrange
    $lesson = Lesson::factory()->create();
    $data = [
        'title' => 'Updated Title',
    ];

    // Mock DB facade to verify the transaction is used
    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(function ($callback) {
            return $callback();
        });

    $action = new UpdateLessonAction();

    // Act
    $action->handle($lesson, $data);

    // Assert is handled by the mock expectations
});

it('persists the changes to the database', function (): void {
    // Arrange
    $module = Module::factory()->create();
    $lesson = Lesson::factory()->create([
        'module_id' => $module->id,
        'title' => 'Original Title',
    ]);

    $data = [
        'title' => 'Updated Title',
    ];

    $action = new UpdateLessonAction();

    // Act
    $action->handle($lesson, $data);

    // Assert - Fetch the lesson from the database to verify the changes were persisted
    $refreshedLesson = Lesson::query()->find($lesson->id);
    expect($refreshedLesson)->not->toBeNull()
        ->and($refreshedLesson->title)->toBe('Updated Title');
});
