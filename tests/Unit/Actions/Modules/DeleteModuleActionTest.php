<?php

declare(strict_types=1);

use App\Actions\Modules\DeleteModuleAction;
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

it('deletes a module', function (): void {
    // Arrange
    $module = Module::factory()->create();
    $moduleId = $module->id;

    $action = new DeleteModuleAction();

    // Act
    $action->handle($module);

    // Assert
    $this->assertDatabaseMissing('modules', [
        'id' => $moduleId,
    ]);
});

it('deletes all lessons associated with the module', function (): void {
    // Arrange
    $module = Module::factory()->create();
    $lessons = Lesson::factory()->count(3)->create([
        'module_id' => $module->id,
    ]);

    $lessonIds = $lessons->pluck('id')->toArray();

    $action = new DeleteModuleAction();

    // Act
    $action->handle($module);

    // Assert
    $this->assertDatabaseMissing('modules', [
        'id' => $module->id,
    ]);

    foreach ($lessonIds as $lessonId) {
        $this->assertDatabaseMissing('lessons', [
            'id' => $lessonId,
        ]);
    }
});

it('uses a database transaction', function (): void {
    // Arrange
    $module = Module::factory()->create();

    // Mock DB facade to verify the transaction is used
    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(function ($callback) {
            return $callback();
        });

    $action = new DeleteModuleAction();

    // Act
    $action->handle($module);

    // Assert is handled by the mock expectations
});
