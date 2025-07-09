<?php

declare(strict_types=1);

use App\Actions\Modules\UpdateModuleAction;
use App\Models\Module;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

it('updates a module', function (): void {
    // Arrange
    $module = Module::factory()->create([
        'title' => 'Original Title',
        'description' => 'Original Description',
        'is_published' => false,
        'order' => 1,
    ]);

    $data = [
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'is_published' => true,
        'order' => 2,
    ];

    $action = new UpdateModuleAction();

    // Act
    $updatedModule = $action->handle($module, $data);

    // Assert
    expect($updatedModule)->toBeInstanceOf(Module::class)
        ->and($updatedModule->id)->toBe($module->id)
        ->and($updatedModule->title)->toBe('Updated Title')
        ->and($updatedModule->description)->toBe('Updated Description')
        ->and($updatedModule->is_published)->toBeTrue()
        ->and($updatedModule->order)->toBe(2);
});

it('persists the updated module to the database', function (): void {
    // Arrange
    $module = Module::factory()->create([
        'title' => 'Original Title',
        'description' => 'Original Description',
        'is_published' => false,
        'order' => 1,
    ]);

    $data = [
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'is_published' => true,
        'order' => 2,
    ];

    $action = new UpdateModuleAction();

    // Act
    $action->handle($module, $data);

    // Assert
    $this->assertDatabaseHas('modules', [
        'id' => $module->id,
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'is_published' => 1, // Boolean true is stored as 1 in the database
        'order' => 2,
    ]);
});

it('uses a database transaction', function (): void {
    // Arrange
    $module = Module::factory()->create();
    $data = [
        'title' => 'Updated Title',
    ];

    // Mock DB facade to verify the transaction is used
    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(function ($callback) {
            return $callback();
        });

    $action = new UpdateModuleAction();

    // Act
    $action->handle($module, $data);

    // Assert is handled by the mock expectations
});

it('only updates the provided fields', function (): void {
    // Arrange
    $module = Module::factory()->create([
        'title' => 'Original Title',
        'description' => 'Original Description',
        'is_published' => false,
        'order' => 1,
    ]);

    $data = [
        'title' => 'Updated Title',
        // description is not provided
    ];

    $action = new UpdateModuleAction();

    // Act
    $updatedModule = $action->handle($module, $data);

    // Assert
    expect($updatedModule)->toBeInstanceOf(Module::class)
        ->and($updatedModule->id)->toBe($module->id)
        ->and($updatedModule->title)->toBe('Updated Title')
        ->and($updatedModule->description)->toBe('Original Description') // Should remain unchanged
        ->and($updatedModule->is_published)->toBeFalse() // Should remain unchanged
        ->and($updatedModule->order)->toBe(1); // Should remain unchanged
});
