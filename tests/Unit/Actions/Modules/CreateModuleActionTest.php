<?php

declare(strict_types=1);

use App\Actions\Modules\CreateModuleAction;
use App\Models\Course;
use App\Models\Module;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

it('creates a module', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $data = [
        'title' => 'Test Module',
        'description' => 'This is a test module',
        'course_id' => $course->id,
        'order' => 1,
        'is_published' => true,
    ];

    $action = new CreateModuleAction();

    // Act
    $module = $action->handle($data);

    // Assert
    expect($module)->toBeInstanceOf(Module::class)
        ->and($module->title)->toBe('Test Module')
        ->and($module->description)->toBe('This is a test module')
        ->and($module->course_id)->toBe($course->id)
        ->and($module->order)->toBe(1)
        ->and($module->is_published)->toBeTrue();
});

it('uses a database transaction', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $data = [
        'title' => 'Test Module',
        'description' => 'This is a test module',
        'course_id' => $course->id,
        'order' => 1,
        'is_published' => true,
    ];

    // Mock DB facade to verify the transaction is used
    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(function ($callback) {
            return $callback();
        });

    $action = new CreateModuleAction();

    // Act
    $action->handle($data);

    // Assert is handled by the mock expectations
});

it('persists the module to the database', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $data = [
        'title' => 'Test Module',
        'description' => 'This is a test module',
        'course_id' => $course->id,
        'order' => 1,
        'is_published' => true,
    ];

    $action = new CreateModuleAction();

    // Act
    $module = $action->handle($data);

    // Assert
    $this->assertDatabaseHas('modules', [
        'id' => $module->id,
        'title' => 'Test Module',
        'description' => 'This is a test module',
        'course_id' => $course->id,
        'order' => 1,
        'is_published' => 1, // Boolean true is stored as 1 in the database
    ]);
});
