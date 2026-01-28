<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;

it('creates modules via factories', function (): void {
    $course = Course::factory()->create();
    $module = Module::factory()->for($course)->create();

    expect($module->exists)->toBeTrue()
        ->and($module->course->id)->toBe($course->id);
});

it('marks modules as published based on publish_at', function (): void {
    $module = Module::factory()->create([
        'published_at' => now()->subDay(),
    ]);

    expect($module->is_published)->toBeTrue();

    $module->update(['published_at' => now()->addDay()]);
    expect($module->is_published)->toBeFalse();

    $module->update(['published_at' => null]);
    expect($module->is_published)->toBeFalse();
});

it('supports soft deletes for modules', function (): void {
    $course = Course::factory()->create();
    $module = Module::factory()->for($course)->create();

    $module->delete();

    expect($module->fresh()->trashed())->toBeTrue();
});

it('tracks provenance relationships on modules', function (): void {
    $user = User::factory()->create();
    $course = Course::factory()->create(['created_by_id' => $user->id, 'updated_by_id' => $user->id]);
    $module = Module::factory()->for($course)->create(['created_by_id' => $user->id, 'updated_by_id' => $user->id]);

    expect($module->createdBy->id)->toBe($user->id)
        ->and($module->updatedBy->id)->toBe($user->id);
});

it('orders lessons through the module relation', function (): void {
    $module = Module::factory()->create();
    Lesson::factory()->for($module)->create(['order' => 2]);
    Lesson::factory()->for($module)->create(['order' => 1]);

    expect($module->lessons->pluck('order')->all())->toBe([1, 2]);
});
