<?php

declare(strict_types=1);

use App\Enums\LessonType;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('lesson has correct fillable attributes', function (): void {
    $lesson = new Lesson();

    expect($lesson->getFillable())->toBe([
        'title',
        'content',
        'module_id',
        'order',
        'type',
        'is_published',
    ]);
});

test('lesson has correct casts', function (): void {
    $lesson = new Lesson();

    expect($lesson->getCasts())->toMatchArray([
        'is_published' => 'boolean',
        'order' => 'integer',
        'type' => LessonType::class,
    ]);
});

test('lesson belongs to a module', function (): void {
    $module = Module::factory()->create();

    $lesson = Lesson::factory()->create([
        'module_id' => $module->id,
    ]);

    expect($lesson->module)->toBeInstanceOf(Module::class)
        ->and($lesson->module->id)->toBe($module->id);
});

test('lesson factory creates a valid lesson', function (): void {
    $lesson = Lesson::factory()->create();

    expect($lesson)->toBeInstanceOf(Lesson::class)
        ->and($lesson->title)->not->toBeEmpty()
        ->and($lesson->content)->not->toBeEmpty();
});

test('lesson factory can create a published lesson', function (): void {
    $lesson = Lesson::factory()->published()->create();

    expect($lesson->is_published)->toBeTrue();
});

test('lesson factory can create an unpublished lesson', function (): void {
    $lesson = Lesson::factory()->unpublished()->create();

    expect($lesson->is_published)->toBeFalse();
});

test('lesson factory can create a lesson with a specific order', function (): void {
    $order = 42;
    $lesson = Lesson::factory()->withOrder($order)->create();

    expect($lesson->order)->toBe($order);
});

test('lessons are ordered by order attribute', function (): void {
    $module = Module::factory()->create();

    // Create lessons in reverse order
    Lesson::factory()->withOrder(3)->create(['module_id' => $module->id]);
    Lesson::factory()->withOrder(1)->create(['module_id' => $module->id]);
    Lesson::factory()->withOrder(2)->create(['module_id' => $module->id]);

    $lessons = $module->lessons;

    expect($lessons)->toHaveCount(3)
        ->and($lessons[0]->order)->toBe(1)
        ->and($lessons[1]->order)->toBe(2)
        ->and($lessons[2]->order)->toBe(3);
});

test('lesson factory can create a text lesson', function (): void {
    $lesson = Lesson::factory()->asText()->create();

    expect($lesson->type)->toBe(LessonType::TEXT);
});

test('lesson factory can create a video lesson', function (): void {
    $lesson = Lesson::factory()->asVideo()->create();

    expect($lesson->type)->toBe(LessonType::VIDEO);
});

test('lesson factory can create a document lesson', function (): void {
    $lesson = Lesson::factory()->asDocument()->create();

    expect($lesson->type)->toBe(LessonType::DOCUMENT);
});

