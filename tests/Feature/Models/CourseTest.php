<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\Module;
use App\Models\User;

it('creates courses via factories', function (): void {
    $course = Course::factory()->create();

    expect($course->exists)->toBeTrue();
});

it('associates users to courses with roles', function (): void {
    $user = User::factory()->create();
    $course = Course::factory()->create();

    $course->users()->attach($user, ['role' => 'student', 'status' => 'active', 'enrolled_at' => now()]);

    expect($user->courses)->toHaveCount(1)
        ->and($course->students)->toHaveCount(1)
        ->and($course->teachers)->toHaveCount(0)
        ->and($user->courses->first()->pivot->role)->toBe('student');

    $teacher = User::factory()->create();
    $course->users()->attach($teacher, ['role' => 'teacher', 'status' => 'active', 'enrolled_at' => now()]);
    $course->refresh();

    expect($course->teachers)->toHaveCount(1)
        ->and($course->students)->toHaveCount(1)
        ->and($teacher->teachingCourses)->toHaveCount(1)
        ->and($user->enrolledCourses)->toHaveCount(1)
        ->and($user->assistingCourses)->toHaveCount(0);
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

it('resolves the teacher relationship', function (): void {
    $user = User::factory()->create();
    $course = Course::factory()->create(['teacher_id' => $user->id]);

    expect($course->teacher->id)->toBe($user->id);
});

it('supports soft deletes for courses', function (): void {
    $course = Course::factory()->create();

    $course->delete();

    expect($course->fresh()->trashed())->toBeTrue();
});

it('tracks provenance relationships on courses', function (): void {
    $user = User::factory()->create();
    $course = Course::factory()->create(['created_by_id' => $user->id, 'updated_by_id' => $user->id]);

    expect($course->createdBy->id)->toBe($user->id)
        ->and($course->updatedBy->id)->toBe($user->id);
});

it('orders modules through the course relation', function (): void {
    $course = Course::factory()->create();
    Module::factory()->for($course)->create(['order' => 2]);
    Module::factory()->for($course)->create(['order' => 1]);

    expect($course->modules->pluck('order')->all())->toBe([1, 2]);
});
