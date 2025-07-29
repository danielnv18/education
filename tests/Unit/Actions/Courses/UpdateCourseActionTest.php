<?php

declare(strict_types=1);

use App\Actions\Courses\UpdateCourseAction;
use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('updates a course with the provided data', function (): void {
    // Arrange
    $course = Course::factory()->create([
        'title' => 'Original Title',
        'description' => 'Original Description',
        'status' => CourseStatus::Draft->value,
    ]);

    $data = [
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'status' => CourseStatus::Active->value,
    ];

    $action = new UpdateCourseAction();

    // Act
    $updatedCourse = $action->handle($course, $data);

    // Assert
    expect($updatedCourse)->toBeInstanceOf(Course::class)
        ->and($updatedCourse->id)->toBe($course->id)
        ->and($updatedCourse->title)->toBe($data['title'])
        ->and($updatedCourse->description)->toBe($data['description'])
        ->and($updatedCourse->status)->toBe(CourseStatus::Active);
});

it('updates a course teacher', function (): void {
    // Arrange
    $originalTeacher = User::factory()->create();
    $newTeacher = User::factory()->create();

    $course = Course::factory()->create([
        'teacher_id' => $originalTeacher->id,
    ]);

    $data = [
        'teacher_id' => $newTeacher->id,
    ];

    $action = new UpdateCourseAction();

    // Act
    $updatedCourse = $action->handle($course, $data);

    // Assert
    expect($updatedCourse)->toBeInstanceOf(Course::class)
        ->and($updatedCourse->teacher_id)->toBe($newTeacher->id)
        ->and($updatedCourse->teacher_id)->not->toBe($originalTeacher->id);
});

it('updates course dates', function (): void {
    // Arrange
    $originalStartDate = now();
    $originalEndDate = now()->addDays(10);

    $course = Course::factory()->create([
        'start_date' => $originalStartDate,
        'end_date' => $originalEndDate,
    ]);

    $newStartDate = now()->addDays(5);
    $newEndDate = now()->addDays(30);

    $data = [
        'start_date' => $newStartDate,
        'end_date' => $newEndDate,
    ];

    $action = new UpdateCourseAction();

    // Act
    $updatedCourse = $action->handle($course, $data);

    // Assert
    expect($updatedCourse)->toBeInstanceOf(Course::class)
        ->and($updatedCourse->start_date->toDateString())->toBe($newStartDate->toDateString())
        ->and($updatedCourse->end_date->toDateString())->toBe($newEndDate->toDateString())
        ->and($updatedCourse->start_date->toDateString())->not->toBe($originalStartDate->toDateString())
        ->and($updatedCourse->end_date->toDateString())->not->toBe($originalEndDate->toDateString());
});

it('uses a database transaction', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $data = ['title' => 'Transaction Test'];

    // Mock DB facade to verify transaction is used
    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(function ($callback) {
            return $callback();
        });

    $action = new UpdateCourseAction();

    // Act
    $action->handle($course, $data);

    // Assert is handled by the mock expectations
});
