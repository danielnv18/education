<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('module has correct fillable attributes', function (): void {
    $module = new Module();

    expect($module->getFillable())->toBe([
        'title',
        'description',
        'course_id',
        'order',
        'is_published',
    ]);
});

test('module has correct casts', function (): void {
    $module = new Module();

    expect($module->getCasts())->toMatchArray([
        'is_published' => 'boolean',
        'order' => 'integer',
    ]);
});

test('module belongs to a course', function (): void {
    $course = Course::factory()->create();

    $module = Module::factory()->create([
        'course_id' => $course->id,
    ]);

    expect($module->course)->toBeInstanceOf(Course::class)
        ->and($module->course->id)->toBe($course->id);
});

test('module has many lessons', function (): void {
    $module = Module::factory()->create();
    $lessons = Lesson::factory()->count(3)->create([
        'module_id' => $module->id,
    ]);

    expect($module->lessons)->toHaveCount(3)
        ->and($module->lessons->first())->toBeInstanceOf(Lesson::class);
});

test('module factory creates a valid module', function (): void {
    $module = Module::factory()->create();

    expect($module)->toBeInstanceOf(Module::class)
        ->and($module->title)->not->toBeEmpty()
        ->and($module->description)->not->toBeEmpty();
});

test('module factory can create a published module', function (): void {
    $module = Module::factory()->published()->create();

    expect($module->is_published)->toBeTrue();
});

test('module factory can create an unpublished module', function (): void {
    $module = Module::factory()->unpublished()->create();

    expect($module->is_published)->toBeFalse();
});

test('module factory can create a module with a specific order', function (): void {
    $order = 42;
    $module = Module::factory()->withOrder($order)->create();

    expect($module->order)->toBe($order);
});

test('modules are ordered by order attribute', function (): void {
    $course = Course::factory()->create();

    // Create modules in reverse order
    Module::factory()->withOrder(3)->create(['course_id' => $course->id]);
    Module::factory()->withOrder(1)->create(['course_id' => $course->id]);
    Module::factory()->withOrder(2)->create(['course_id' => $course->id]);

    $modules = $course->modules;

    expect($modules)->toHaveCount(3)
        ->and($modules[0]->order)->toBe(1)
        ->and($modules[1]->order)->toBe(2)
        ->and($modules[2]->order)->toBe(3);
});
