<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;

it('creates models via factories', function (): void {
    $course = Course::factory()->create();
    $module = Module::factory()->for($course)->create();
    $lesson = Lesson::factory()->for($module)->create();

    expect($course->exists)->toBeTrue();
    expect($module->exists)->toBeTrue();
    expect($lesson->exists)->toBeTrue();
    expect($module->course->id)->toBe($course->id);
    expect($lesson->module->id)->toBe($module->id);

    // Trigger hasMany relations
    expect($course->modules)->toHaveCount(1);
    expect($module->lessons)->toHaveCount(1);
});

it('supports soft deletes across models', function (): void {
    $course = Course::factory()->create();
    $module = Module::factory()->for($course)->create();
    $lesson = Lesson::factory()->for($module)->create();

    $course->delete();
    $module->delete();
    $lesson->delete();

    expect($course->fresh()->trashed())->toBeTrue();
    expect($module->fresh()->trashed())->toBeTrue();
    expect($lesson->fresh()->trashed())->toBeTrue();
});

it('associates users to courses with roles', function (): void {
    $user = User::factory()->create();
    $course = Course::factory()->create();

    // Attach as student
    $course->users()->attach($user, ['role' => 'student', 'status' => 'active', 'enrolled_at' => now()]);

    expect($user->courses)->toHaveCount(1);
    expect($course->students)->toHaveCount(1);
    expect($course->teachers)->toHaveCount(0);
    expect($user->courses->first()->pivot->role)->toBe('student');

    // Attach another user as teacher
    $teacher = User::factory()->create();
    $course->users()->attach($teacher, ['role' => 'teacher', 'status' => 'active', 'enrolled_at' => now()]);

    // Refresh relational data
    // Refresh relational data
    $course->refresh();

    expect($course->teachers)->toHaveCount(1);
    expect($course->students)->toHaveCount(1);

    expect($teacher->teachingCourses)->toHaveCount(1);
    expect($user->enrolledCourses)->toHaveCount(1);
    expect($user->assistingCourses)->toHaveCount(0);
});

it('marks courses as published when status and publish_at permit', function (): void {
    $course = Course::factory()->create([
        'published_at' => now()->subDay(),
        'status' => 'published',
    ]);

    expect($course->is_published)->toBeTrue();

    $course->update([
        'published_at' => now()->addDay(),
    ]);

    expect($course->is_published)->toBeFalse();

    $course->update([
        'published_at' => now()->subDay(),
        'status' => 'draft',
    ]);

    expect($course->is_published)->toBeFalse();

    $course->update([
        'published_at' => null,
    ]);

    expect($course->is_published)->toBeFalse();
});

it('marks modules as published based on publish_at', function (): void {
    $module = Module::factory()->create([
        'published_at' => now()->subDay(),
    ]);
    expect($module->is_published)->toBeTrue();

    $module->update(['published_at' => now()->addDay()]);
    expect($module->is_published)->toBeFalse();

    $module->update([
        'published_at' => null,
    ]);
    expect($module->is_published)->toBeFalse();
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

it('tracks provenance relationships', function (): void {
    $user = User::factory()->create();
    $course = Course::factory()->create(['created_by_id' => $user->id, 'updated_by_id' => $user->id]);
    $module = Module::factory()->for($course)->create(['created_by_id' => $user->id, 'updated_by_id' => $user->id]);
    $lesson = Lesson::factory()->for($module)->create(['created_by_id' => $user->id, 'updated_by_id' => $user->id]);

    expect($course->createdBy->id)->toBe($user->id);
    expect($course->updatedBy->id)->toBe($user->id);

    expect($module->createdBy->id)->toBe($user->id);
    expect($module->updatedBy->id)->toBe($user->id);

    expect($lesson->createdBy->id)->toBe($user->id);
    expect($lesson->updatedBy->id)->toBe($user->id);
});

it('returns null when avatar is missing', function (): void {
    $user = User::factory()->create();
    expect($user->avatar)->toBeNull();
});

it('resolves the teacher relationship', function (): void {
    $user = User::factory()->create();
    $course = Course::factory()->create(['teacher_id' => $user->id]);

    expect($course->teacher->id)->toBe($user->id);
});
