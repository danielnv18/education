<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

test('it enrolls students in a course', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);

    $course = Course::factory()->create();
    $students = User::factory()->count(3)->create();

    $studentIds = $students->pluck('id')->toArray();

    // Act
    $response = $this->actingAs($admin)
        ->post(route('courses.enroll', $course), [
            'student_ids' => $studentIds,
        ]);

    // Assert
    $response->assertRedirect(route('courses.students', $course));
    $response->assertSessionHas('success', '3 students enrolled successfully.');

    // Verify students are enrolled
    foreach ($students as $student) {
        $this->assertTrue($student->fresh()->hasRole(UserRole::STUDENT));
        $this->assertTrue($course->students()->where('user_id', $student->id)->exists());
    }
});

test('it enrolls a single student in a course', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);

    $course = Course::factory()->create();
    $student = User::factory()->create();

    // Act
    $response = $this->actingAs($admin)
        ->post(route('courses.enroll', $course), [
            'student_ids' => [$student->id],
        ]);

    // Assert
    $response->assertRedirect(route('courses.students', $course));
    $response->assertSessionHas('success', '1 student enrolled successfully.');

    // Verify student is enrolled
    $this->assertTrue($student->fresh()->hasRole(UserRole::STUDENT));
    $this->assertTrue($course->students()->where('user_id', $student->id)->exists());
});

test('it requires authentication', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $student = User::factory()->create();

    // Act & Assert
    $this->post(route('courses.enroll', $course), [
        'student_ids' => [$student->id],
    ])->assertRedirect(route('login'));
});

test('it requires authorization', function (): void {
    // Arrange
    $regularUser = User::factory()->create();
    $course = Course::factory()->create();
    $student = User::factory()->create();

    // Act & Assert - Regular user without admin role
    $this->actingAs($regularUser)
        ->post(route('courses.enroll', $course), [
            'student_ids' => [$student->id],
        ])->assertStatus(403);
});

test('it validates student_ids', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);

    $course = Course::factory()->create();

    // Act & Assert - Empty student_ids
    $this->actingAs($admin)
        ->post(route('courses.enroll', $course), [
            'student_ids' => [],
        ])->assertSessionHasErrors('student_ids');

    // Act & Assert - Invalid student_ids
    $this->actingAs($admin)
        ->post(route('courses.enroll', $course), [
            'student_ids' => ['not-an-id'],
        ])->assertSessionHasErrors('student_ids.0');
});
