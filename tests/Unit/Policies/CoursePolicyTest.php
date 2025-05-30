<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\User;
use App\Policies\CoursePolicy;
use Database\Seeders\RoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    new RoleSeeder()->run();
});

it('allows admin to view any courses', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->viewAny($admin))->toBeTrue();
});

it('allows user with multiple roles to view any courses if they have admin role', function (): void {
    // Arrange
    $user = User::factory()->create();
    $user->assignRole('admin');
    $user->assignRole('teacher');  // User has both admin and teacher roles

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->viewAny($user))->toBeTrue();
});

it('does not allow non-admin to view any courses', function (): void {
    // Arrange
    $user = User::factory()->create();
    // User with no roles

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->viewAny($user))->toBeFalse();
});

it('allows admin to view a course', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $course = Course::factory()->create();

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->view($admin, $course))->toBeTrue();
});

it('allows teacher to view their own course', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');

    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->view($teacher, $course))->toBeTrue();
});

it('does not allow teacher to view other teachers courses', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');

    $otherTeacher = User::factory()->create();
    $course = Course::factory()->create(['teacher_id' => $otherTeacher->id]);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->view($teacher, $course))->toBeFalse();
});

it('allows enrolled user to view a course', function (): void {
    // Arrange
    $user = User::factory()->create();
    // User with no roles, but enrolled in the course

    $course = Course::factory()->create();
    $course->students()->attach($user->id, ['enrolled_at' => now(), 'status' => 'active']);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->view($user, $course))->toBeTrue();
});

it('does not allow non-enrolled user to view a course', function (): void {
    // Arrange
    $user = User::factory()->create();
    // User with no roles and not enrolled in the course

    $course = Course::factory()->create();

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->view($user, $course))->toBeFalse();
});

it('allows admin to create a course', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->create($admin))->toBeTrue();
});

it('does not allow non-admin to create a course', function (): void {
    // Arrange
    $user = User::factory()->create();
    $user->assignRole('teacher');  // Even a teacher can't create a course without admin role

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->create($user))->toBeFalse();
});

it('allows admin to update any course', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $course = Course::factory()->create();

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->update($admin, $course))->toBeTrue();
});

it('allows teacher to update their own course', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');

    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->update($teacher, $course))->toBeTrue();
});

it('allows user with multiple roles to update their own course if they have teacher role', function (): void {
    // Arrange
    $user = User::factory()->create();
    $user->assignRole('teacher');  // User needs teacher role to update their own course
    // Additional role that doesn't affect this permission
    $user->assignRole('admin');  // User has both teacher and admin roles

    $course = Course::factory()->create(['teacher_id' => $user->id]);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->update($user, $course))->toBeTrue();
});

it('does not allow teacher to update other teachers courses', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');

    $otherTeacher = User::factory()->create();
    $course = Course::factory()->create(['teacher_id' => $otherTeacher->id]);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->update($teacher, $course))->toBeFalse();
});

it('does not allow regular user to update a course', function (): void {
    // Arrange
    $user = User::factory()->create();
    // User with no roles

    $course = Course::factory()->create();

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->update($user, $course))->toBeFalse();
});

it('allows admin to delete a course', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $course = Course::factory()->create();

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->delete($admin, $course))->toBeTrue();
});

it('does not allow non-admin to delete a course', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');

    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->delete($teacher, $course))->toBeFalse();
});

it('allows admin to restore a course', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $course = Course::factory()->create();

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->restore($admin, $course))->toBeTrue();
});

it('does not allow non-admin to restore a course', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');

    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->restore($teacher, $course))->toBeFalse();
});

it('allows admin to force delete a course', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole('admin');

    $course = Course::factory()->create();

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->forceDelete($admin, $course))->toBeTrue();
});

it('does not allow non-admin to force delete a course', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole('teacher');

    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->forceDelete($teacher, $course))->toBeFalse();
});
