<?php

declare(strict_types=1);

use App\Actions\Lessons\DeleteLessonAction;
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

it('deletes a lesson', function (): void {
    // Arrange
    $module = Module::factory()->create();
    $lesson = Lesson::factory()->create([
        'module_id' => $module->id,
    ]);

    $lessonId = $lesson->id;
    $action = new DeleteLessonAction();

    // Act
    $action->handle($lesson);

    // Assert
    expect(Lesson::query()->find($lessonId))->toBeNull();
});

it('uses a database transaction', function (): void {
    // Arrange
    $lesson = Lesson::factory()->create();

    // Mock DB facade to verify the transaction is used
    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(function ($callback) {
            return $callback();
        });

    $action = new DeleteLessonAction();

    // Act
    $action->handle($lesson);

    // Assert is handled by the mock expectations
});

it('deletes a lesson with a specific type', function (): void {
    // Arrange
    $module = Module::factory()->create();
    $lesson = Lesson::factory()->asVideo()->create([
        'module_id' => $module->id,
    ]);

    $lessonId = $lesson->id;
    $action = new DeleteLessonAction();

    // Act
    $action->handle($lesson);

    // Assert
    expect(Lesson::query()->find($lessonId))->toBeNull();
});

it('deletes a published lesson', function (): void {
    // Arrange
    $module = Module::factory()->create();
    $lesson = Lesson::factory()->published()->create([
        'module_id' => $module->id,
    ]);

    $lessonId = $lesson->id;
    $action = new DeleteLessonAction();

    // Act
    $action->handle($lesson);

    // Assert
    expect(Lesson::query()->find($lessonId))->toBeNull();
});
