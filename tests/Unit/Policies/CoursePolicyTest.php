<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;
use App\Policies\CoursePolicy;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

it('allows user with admin role to view any courses', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);
    // Admin role has VIEW_ANY_COURSES permission assigned in PermissionSeeder

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->viewAny($admin))->toBeTrue();
});

it('allows user with admin role to view any courses even with multiple roles', function (): void {
    // Arrange
    $user = User::factory()->create();
    $user->assignRole(UserRole::ADMIN);
    $user->assignRole(UserRole::TEACHER);
    // Admin role has VIEW_ANY_COURSES permission assigned in PermissionSeeder

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->viewAny($user))->toBeTrue();
});

it('does not allow user without view any courses permission to view any courses', function (): void {
    // Arrange
    $user = User::factory()->create();
    // Create a user without any roles or permissions

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->viewAny($user))->toBeFalse();
});

it('allows user with admin role to view a course', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);
    // Admin role has VIEW_ANY_COURSES permission assigned in PermissionSeeder

    $course = Course::factory()->create();

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->view($admin, $course))->toBeTrue();
});

it('allows teacher to view their own course', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::TEACHER);
    // Teacher role has VIEW_COURSE permission assigned in PermissionSeeder

    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->view($teacher, $course))->toBeTrue();
});

it('does not allow teacher to view other teachers courses', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::TEACHER);
    // Teacher role has VIEW_COURSE permission assigned in PermissionSeeder

    $otherTeacher = User::factory()->create();
    $course = Course::factory()->create(['teacher_id' => $otherTeacher->id]);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->view($teacher, $course))->toBeFalse();
});

it('allows enrolled student to view a course', function (): void {
    // Arrange
    $student = User::factory()->create();
    $student->assignRole(UserRole::STUDENT);
    // Student role has VIEW_COURSE permission assigned in PermissionSeeder

    $course = Course::factory()->create();
    $course->students()->attach($student->id, ['enrolled_at' => now(), 'status' => 'active']);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->view($student, $course))->toBeTrue();
});

it('does not allow non-enrolled user without a role to view a course', function (): void {
    // Arrange
    $user = User::factory()->create();
    // User doesn't have any role with VIEW_COURSE permission

    $course = Course::factory()->create();

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->view($user, $course))->toBeFalse();
});

it('allows teacher to create a course', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::TEACHER);
    // Teacher role has CREATE_COURSE permission assigned in PermissionSeeder

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->create($teacher))->toBeTrue();
});

it('does not allow user without a role that has create course permission to create a course', function (): void {
    // Arrange
    $user = User::factory()->create();
    $user->assignRole(UserRole::STUDENT);
    // Student role doesn't have CREATE_COURSE permission

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->create($user))->toBeFalse();
});

it('allows admin to update any course', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);
    // Admin role has VIEW_ANY_COURSES permission assigned in PermissionSeeder

    $course = Course::factory()->create();

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->update($admin, $course))->toBeTrue();
});

it('allows teacher to update their own course', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::TEACHER);
    // Teacher role has UPDATE_COURSE permission assigned in PermissionSeeder

    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->update($teacher, $course))->toBeTrue();
});

it('allows user with teacher role to update their own course', function (): void {
    // Arrange
    $user = User::factory()->create();
    $user->assignRole(UserRole::TEACHER);
    // Teacher role has UPDATE_COURSE and VIEW_COURSE permissions assigned in PermissionSeeder

    $course = Course::factory()->create(['teacher_id' => $user->id]);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->update($user, $course))->toBeTrue();
});

it('does not allow teacher to update other teachers courses', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::TEACHER);
    // Teacher role has UPDATE_COURSE permission but not VIEW_ANY_COURSES permission

    $otherTeacher = User::factory()->create();
    $course = Course::factory()->create(['teacher_id' => $otherTeacher->id]);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->update($teacher, $course))->toBeFalse();
});

it('does not allow student to update a course', function (): void {
    // Arrange
    $student = User::factory()->create();
    $student->assignRole(UserRole::STUDENT);
    // Student role doesn't have UPDATE_COURSE permission

    $course = Course::factory()->create();

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->update($student, $course))->toBeFalse();
});

it('allows admin to delete a course', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);
    // Admin role has DELETE_COURSE permission assigned in PermissionSeeder

    $course = Course::factory()->create();

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->delete($admin, $course))->toBeTrue();
});

it('does not allow teacher to delete a course', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::TEACHER);
    // Teacher role doesn't have DELETE_COURSE permission

    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->delete($teacher, $course))->toBeFalse();
});

it('allows admin to restore a course', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);
    // Admin role has RESTORE_COURSE permission assigned in PermissionSeeder

    $course = Course::factory()->create();

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->restore($admin, $course))->toBeTrue();
});

it('does not allow teacher to restore a course', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::TEACHER);
    // Teacher role doesn't have RESTORE_COURSE permission

    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->restore($teacher, $course))->toBeFalse();
});

it('allows admin to force delete a course', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);
    // Admin role has FORCE_DELETE_COURSE permission assigned in PermissionSeeder

    $course = Course::factory()->create();

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->forceDelete($admin, $course))->toBeTrue();
});

it('does not allow teacher to force delete a course', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::TEACHER);
    // Teacher role doesn't have FORCE_DELETE_COURSE permission

    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->forceDelete($teacher, $course))->toBeFalse();
});

it('allows admin to manage content of any course', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);
    // Admin role has VIEW_ANY_COURSES permission assigned in PermissionSeeder

    $course = Course::factory()->create();

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->manageContent($admin, $course))->toBeTrue();
});

it('allows teacher to manage their own course content', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::TEACHER);
    // Teacher role has MANAGE_COURSE_CONTENT permission assigned in PermissionSeeder

    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->manageContent($teacher, $course))->toBeTrue();
});

it('does not allow teacher to manage other teachers course content', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::TEACHER);
    // Teacher role has MANAGE_COURSE_CONTENT permission assigned in PermissionSeeder

    $otherTeacher = User::factory()->create();
    $course = Course::factory()->create(['teacher_id' => $otherTeacher->id]);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->manageContent($teacher, $course))->toBeFalse();
});

it('does not allow student to manage course content', function (): void {
    // Arrange
    $student = User::factory()->create();
    $student->assignRole(UserRole::STUDENT);
    // Student role doesn't have MANAGE_COURSE_CONTENT permission

    $course = Course::factory()->create();

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->manageContent($student, $course))->toBeFalse();
});

it('allows admin to view content of any course', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);
    // Admin role has VIEW_ANY_COURSES permission assigned in PermissionSeeder

    $course = Course::factory()->create();

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->viewContent($admin, $course))->toBeTrue();
});

it('allows teacher to view their own course content', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::TEACHER);
    // Teacher role has VIEW_COURSE_CONTENT permission assigned in PermissionSeeder

    $course = Course::factory()->create(['teacher_id' => $teacher->id]);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->viewContent($teacher, $course))->toBeTrue();
});

it('allows enrolled student to view course content', function (): void {
    // Arrange
    $student = User::factory()->create();
    $student->assignRole(UserRole::STUDENT);
    // Student role has VIEW_COURSE_CONTENT permission assigned in PermissionSeeder

    $course = Course::factory()->create();
    $course->students()->attach($student->id, ['enrolled_at' => now(), 'status' => 'active']);

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->viewContent($student, $course))->toBeTrue();
});

it('does not allow non-enrolled student to view course content', function (): void {
    // Arrange
    $student = User::factory()->create();
    $student->assignRole(UserRole::STUDENT);
    // Student role has VIEW_COURSE_CONTENT permission assigned in PermissionSeeder

    $course = Course::factory()->create();
    // Student is not enrolled in the course

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->viewContent($student, $course))->toBeFalse();
});

it('does not allow user without a role to view course content', function (): void {
    // Arrange
    $user = User::factory()->create();
    // User doesn't have any role with VIEW_COURSE_CONTENT permission

    $course = Course::factory()->create();

    $policy = new CoursePolicy();

    // Act & Assert
    expect($policy->viewContent($user, $course))->toBeFalse();
});
