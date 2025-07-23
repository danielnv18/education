<?php

declare(strict_types=1);

use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\Module;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('course has correct fillable attributes', function (): void {
    $course = new Course();

    expect($course->getFillable())->toBe([
        'title',
        'description',
        'status',
        'is_published',
        'teacher_id',
        'start_date',
        'end_date',
    ]);
});

test('course has correct casts', function (): void {
    $course = new Course();

    expect($course->getCasts())->toMatchArray([
        'is_published' => 'boolean',
        'start_date' => 'datetime',
        'end_date' => 'datetime',
        'status' => CourseStatus::class,
    ]);
});

test('course belongs to a teacher', function (): void {
    $teacher = User::factory()->create();

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    expect($course->teacher)->toBeInstanceOf(User::class)
        ->and($course->teacher->id)->toBe($teacher->id);
});

test('course can have a null teacher', function (): void {
    $course = Course::factory()->withoutTeacher()->create();

    expect($course->teacher)->toBeNull();
});

test('course has many modules', function (): void {
    $course = Course::factory()->create();
    $modules = Module::factory()->count(3)->create([
        'course_id' => $course->id,
    ]);

    expect($course->modules)->toHaveCount(3)
        ->and($course->modules->first())->toBeInstanceOf(Module::class);
});

test('course belongs to many students', function (): void {
    $course = Course::factory()->create();
    $students = User::factory()->count(3)->create();

    $course->students()->attach($students, [
        'status' => 'active',
        'enrolled_at' => now(),
    ]);

    expect($course->students)->toHaveCount(3)
        ->and($course->students->first())->toBeInstanceOf(User::class);
});

test('course factory creates a valid course', function (): void {
    $course = Course::factory()->create();

    expect($course)->toBeInstanceOf(Course::class)
        ->and($course->title)->not->toBeEmpty()
        ->and($course->description)->not->toBeEmpty();
});

test('course factory can create a published course', function (): void {
    $course = Course::factory()->published()->create();

    expect($course->is_published)->toBeTrue();
});

test('course factory can create a draft course', function (): void {
    $course = Course::factory()->draft()->create();

    expect($course->is_published)->toBeFalse()
        ->and($course->status)->toBe(CourseStatus::DRAFT);
});

test('course factory can create an active course', function (): void {
    $course = Course::factory()->active()->create();

    expect($course->is_published)->toBeTrue()
        ->and($course->status)->toBe(CourseStatus::ACTIVE);
});

test('course factory can create an archived course', function (): void {
    $course = Course::factory()->archived()->create();

    expect($course->status)->toBe(CourseStatus::ARCHIVED);
});
