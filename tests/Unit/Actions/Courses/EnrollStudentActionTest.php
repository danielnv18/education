<?php

declare(strict_types=1);

use App\Actions\Courses\EnrollStudentAction;
use App\Enums\EnrollmentStatus;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

it('enrolls a student in a course', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $student = User::factory()->create();
    $students = collect([$student]);

    $action = new EnrollStudentAction();

    // Act
    $action->handle($course, $students);

    // Assert
    expect($student->hasRole(UserRole::STUDENT))->toBeTrue()
        ->and($course->students()->where('user_id', $student->id)->exists())->toBeTrue()
        ->and($course->students()->where('user_id', $student->id)->first()->pivot->status)
        ->toBe(EnrollmentStatus::ACTIVE->value);
});

it('assigns student role if not already assigned', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $student = User::factory()->create();
    $students = collect([$student]);

    // Verify student doesn't have the role yet
    expect($student->hasRole(UserRole::STUDENT))->toBeFalse();

    $action = new EnrollStudentAction();

    // Act
    $action->handle($course, $students);

    // Assert
    expect($student->hasRole(UserRole::STUDENT))->toBeTrue();
});

it('does not enroll a student twice', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $student = User::factory()->create();
    $students = collect([$student]);

    $action = new EnrollStudentAction();

    // Enroll once
    $action->handle($course, $students);

    // Get the initial count of enrollments
    $initialCount = $course->students()->count();

    // Act - try to enroll again
    $action->handle($course, $students);

    // Assert - count should remain the same
    expect($course->students()->count())->toBe($initialCount);
});

it('uses a database transaction', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $student = User::factory()->create();
    $students = collect([$student]);

    // Mock DB facade to verify transaction is used
    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(function ($callback) {
            return $callback();
        });

    $action = new EnrollStudentAction();

    // Act
    $action->handle($course, $students);

    // Assert is handled by the mock expectations
});

it('enrolls multiple students in a course', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $students = User::factory()->count(3)->create();

    $action = new EnrollStudentAction();

    // Act
    $action->handle($course, $students);

    // Assert
    foreach ($students as $student) {
        expect($student->hasRole(UserRole::STUDENT))->toBeTrue()
            ->and($course->students()->where('user_id', $student->id)->exists())->toBeTrue()
            ->and($course->students()->where('user_id', $student->id)->first()->pivot->status)
            ->toBe(EnrollmentStatus::ACTIVE->value);
    }

    expect($course->students()->count())->toBe(3);
});

it('preserves existing enrollments when adding new students', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $existingStudents = User::factory()->count(2)->create();
    $newStudents = User::factory()->count(3)->create();

    $action = new EnrollStudentAction();

    // First, enroll existing students
    $action->handle($course, $existingStudents);

    // Verify existing students are enrolled
    expect($course->students()->count())->toBe(2);

    // Act - enroll new students
    $action->handle($course, $newStudents);

    // Assert - all students should be enrolled (existing + new)
    expect($course->students()->count())->toBe(5);

    // Verify all existing students are still enrolled
    foreach ($existingStudents as $student) {
        expect($course->students()->where('user_id', $student->id)->exists())->toBeTrue();
    }

    // Verify all new students are enrolled
    foreach ($newStudents as $student) {
        expect($course->students()->where('user_id', $student->id)->exists())->toBeTrue();
    }
});
