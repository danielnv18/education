<?php

declare(strict_types=1);

use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;

it('creates lessons via factories', function (): void {
    $module = Module::factory()->create();
    $lesson = Lesson::factory()->for($module)->create();

    expect($lesson->exists)->toBeTrue()
        ->and($lesson->module->id)->toBe($module->id);
});

it('marks lessons as published based on publish_at', function (): void {
    $lesson = Lesson::factory()->create([
        'published_at' => now()->subDay(),
    ]);

    expect($lesson->is_published)->toBeTrue();

    $lesson->update([
        'published_at' => now()->addDay(),
    ]);

    expect($lesson->is_published)->toBeFalse();

    $lesson->update([
        'published_at' => null,
    ]);

    expect($lesson->is_published)->toBeFalse();
});

it('supports soft deletes for lessons', function (): void {
    $lesson = Lesson::factory()->create();

    $lesson->delete();

    expect($lesson->fresh()->trashed())->toBeTrue();
});

it('tracks provenance relationships on lessons', function (): void {
    $user = User::factory()->create();
    $lesson = Lesson::factory()->create(['created_by_id' => $user->id, 'updated_by_id' => $user->id]);

    expect($lesson->createdBy->id)->toBe($user->id)
        ->and($lesson->updatedBy->id)->toBe($user->id);
});
