<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Inertia\Testing\AssertableInertia;

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

test('index method returns students management page for authorized users', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::TEACHER);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $students = User::factory()->count(3)->create();
    foreach ($students as $student) {
        $student->assignRole(UserRole::STUDENT);
        $student->email_verified_at = now();
        $student->save();
    }

    // Enroll one student
    $course->students()->attach($students[0]->id);

    // Act
    $response = $this->actingAs($teacher)->get(route('courses.students.index', $course));

    // Assert
    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('courses/students')
        ->has('course')
        ->has('availableStudents')
        ->where('availableStudents', function ($availableStudents) use ($students) {
            // Check that the two non-enrolled students are in the available students list
            return collect($availableStudents)->contains('id', $students[1]->id) &&
                collect($availableStudents)->contains('id', $students[2]->id) &&
                ! collect($availableStudents)->contains('id', $students[0]->id);
        })
    );
});

test('unauthorized users cannot access students management page', function (): void {
    // Arrange
    $user = User::factory()->create();
    // No role assigned, so user has no permissions

    $course = Course::factory()->create();

    // Act & Assert
    $this->actingAs($user)->get(route('courses.students.index', $course))->assertStatus(403);
});

test('store method enrolls students in the course', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::TEACHER);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $students = User::factory()->count(2)->create();
    foreach ($students as $student) {
        $student->assignRole(UserRole::STUDENT);
    }

    $studentIds = $students->pluck('id')->toArray();

    // Act
    $response = $this->actingAs($teacher)
        ->post(route('courses.students.store', $course), [
            'student_ids' => $studentIds,
        ]);

    // Assert
    $response->assertRedirect(route('courses.students.index', $course));
    $response->assertSessionHas('success', 'students enrolled successfully.');

    // Refresh the course model to get the updated relationships
    $course->refresh();

    // Verify that the students were enrolled
    foreach ($students as $student) {
        $this->assertTrue($course->students->contains($student->id));
    }
});

test('unauthorized users cannot enroll students', function (): void {
    // Arrange
    $user = User::factory()->create();
    // No role assigned, so user has no permissions

    $course = Course::factory()->create();
    $students = User::factory()->count(2)->create();
    $studentIds = $students->pluck('id')->toArray();

    // Act & Assert
    $this->actingAs($user)
        ->post(route('courses.students.store', $course), [
            'student_ids' => $studentIds,
        ])
        ->assertStatus(403);
});

test('validation fails when no student_ids are provided', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::TEACHER);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    // Act
    $response = $this->actingAs($teacher)
        ->post(route('courses.students.store', $course), [
            'student_ids' => [],
        ]);

    // Assert
    $response->assertSessionHasErrors('student_ids');
});

test('only course teacher can manage students', function (): void {
    // Arrange
    $teacher1 = User::factory()->create();
    $teacher1->assignRole(UserRole::TEACHER);

    $teacher2 = User::factory()->create();
    $teacher2->assignRole(UserRole::TEACHER);

    $course = Course::factory()->create([
        'teacher_id' => $teacher1->id,
    ]);

    // Act & Assert - Another teacher cannot manage students
    $this->actingAs($teacher2)
        ->get(route('courses.students.index', $course))
        ->assertStatus(403);

    $this->actingAs($teacher2)
        ->post(route('courses.students.store', $course), [
            'student_ids' => [1, 2],
        ])
        ->assertStatus(403);
});
