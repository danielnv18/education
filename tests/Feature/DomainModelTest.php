<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;

test('models can be created via factories', function (): void {
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

test('models support soft deletes', function (): void {
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

test('user can have courses and roles', function (): void {
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

test('module publishing logic', function (): void {
    $module = Module::factory()->create([
        'published_at' => now()->subDay(),
        'unpublish_at' => null,
    ]);
    expect($module->is_published)->toBeTrue();

    $module->update(['published_at' => now()->addDay()]);
    expect($module->is_published)->toBeFalse();

    $module->update([
        'published_at' => now()->subDay(),
        'unpublish_at' => now()->subHour(),
    ]);
    expect($module->is_published)->toBeFalse();
});

test('provenance relationships', function (): void {
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

test('user avatar accessor', function (): void {
    $user = User::factory()->create();
    expect($user->avatar)->toBeNull();
});

test('course owner relationship', function (): void {
    $user = User::factory()->create();
    $course = Course::factory()->create(['owner_id' => $user->id]);

    expect($course->owner->id)->toBe($user->id);
});
