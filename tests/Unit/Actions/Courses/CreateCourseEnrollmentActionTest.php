<?php

declare(strict_types=1);

use App\Actions\Courses\CreateCourseEnrollmentAction;
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
    $students = [$student->id];

    $action = new CreateCourseEnrollmentAction();

    // Act
    $action->handle($course, $students);

    // Assert
    expect($student->hasRole(UserRole::Student))->toBeTrue()
        ->and($course->students()->where('user_id', $student->id)->exists())->toBeTrue()
        ->and($course->students()->where('user_id', $student->id)->first()->pivot->status)
        ->toBe(EnrollmentStatus::Active->value);
});

it('assigns student role if not already assigned', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $student = User::factory()->create();
    $students = [$student->id];

    // Verify the student doesn't have the role yet
    expect($student->hasRole(UserRole::Student))->toBeFalse();

    $action = new CreateCourseEnrollmentAction();

    // Act
    $action->handle($course, $students);
    $student->refresh();

    // Assert
    expect($student->hasRole(UserRole::Student))->toBeTrue();
});

it('does not enroll a student twice', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $student = User::factory()->create();
    $students = [$student->id];

    $action = new CreateCourseEnrollmentAction();

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
    $students = [$student->id];

    // Mock DB facade to verify the transaction is used
    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(function ($callback) {
            return $callback();
        });

    $action = new CreateCourseEnrollmentAction();

    // Act
    $action->handle($course, $students);

    // Assert is handled by the mock expectations
});

it('enrolls multiple students in a course', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $students = User::factory()->count(3)->create();
    $studentIds = $students->pluck('id')->toArray();

    $action = new CreateCourseEnrollmentAction();

    // Act
    $action->handle($course, $studentIds);

    // Assert
    foreach ($students as $student) {
        expect($student->hasRole(UserRole::Student))->toBeTrue()
            ->and($course->students()->where('user_id', $student->id)->exists())->toBeTrue()
            ->and($course->students()->where('user_id', $student->id)->first()->pivot->status)
            ->toBe(EnrollmentStatus::Active->value);
    }

    expect($course->students()->count())->toBe(3);
});

it('preserves existing enrollments when adding new students', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $existingStudents = User::factory()->count(2)->create();
    $existingStudentsIds = $existingStudents->pluck('id')->toArray();

    $newStudents = User::factory()->count(3)->create();
    $newStudentsIds = $newStudents->pluck('id')->toArray();

    $action = new CreateCourseEnrollmentAction();

    // First, enroll existing students
    $action->handle($course, $existingStudentsIds);

    // Verify existing students are enrolled
    expect($course->students()->count())->toBe(2);

    // Act - enroll new students
    $action->handle($course, $newStudentsIds);

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

it('handles empty student IDs array gracefully', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $emptyStudentIds = [];

    $action = new CreateCourseEnrollmentAction();

    // Act
    $action->handle($course, $emptyStudentIds);

    // Assert - no students should be enrolled
    expect($course->students()->count())->toBe(0);
});

it('handles non-existent student IDs gracefully', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $nonExistentStudentIds = [999999, 999998]; // IDs that don't exist

    $action = new CreateCourseEnrollmentAction();

    // Act
    $action->handle($course, $nonExistentStudentIds);

    // Assert - no students should be enrolled
    expect($course->students()->count())->toBe(0);
});
