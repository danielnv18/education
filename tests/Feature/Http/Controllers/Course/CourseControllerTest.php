<?php

declare(strict_types=1);

use App\Enums\CourseStatus;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Inertia\Testing\AssertableInertia;

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

test('index method returns courses list for authorized users', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);

    Course::factory()->count(3)->create();

    // Act
    $response = $this->actingAs($admin)->get(route('courses.index'));

    // Assert
    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page->component('courses/index')
        ->has('courses', 3)
    );
});

test('create method returns form with teachers for authorized users', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);

    $teachers = User::factory()->count(3)->create();
    foreach ($teachers as $teacher) {
        $teacher->assignRole(UserRole::TEACHER);
    }

    // Act
    $response = $this->actingAs($admin)->get(route('courses.create'));

    // Assert
    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page->component('courses/create')
        ->has('teachers', 3)
    );
});

test('store method creates a course and redirects for authorized users', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);

    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::TEACHER);

    $courseData = [
        'title' => 'Test Course',
        'description' => 'Test Description',
        'status' => CourseStatus::DRAFT->value,
        'is_published' => false,
        'teacher_id' => $teacher->id,
    ];

    // Act
    $response = $this->actingAs($admin)
        ->post(route('courses.store'), $courseData);

    // Assert
    $course = Course::query()->latest('id')->first();
    $this->assertNotNull($course);
    $this->assertEquals($courseData['title'], $course->title);

    $response->assertRedirect(route('courses.show', $course));
    $response->assertSessionHas('success', 'Course created successfully.');
});

test('show method displays course details for authorized users', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);

    $course = Course::factory()->create();

    // Act
    $response = $this->actingAs($admin)->get(route('courses.show', $course));

    // Assert
    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page->component('courses/show')
        ->has('course')
    );
});

test('edit method returns form with course and teachers for authorized users', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);

    $course = Course::factory()->create();
    $teachers = User::factory()->count(3)->create();
    foreach ($teachers as $teacher) {
        $teacher->assignRole(UserRole::TEACHER);
    }

    // Act
    $response = $this->actingAs($admin)->get(route('courses.edit', $course));

    // Assert
    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page->component('courses/edit')
        ->has('course')
        ->has('teachers')
    );
});

test('update method updates a course and redirects for authorized users', function (): void {
    $this->withoutExceptionHandling();

    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);

    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::TEACHER);

    $course = Course::factory()->create([
        'title' => 'Original Title',
        'description' => 'Original Description',
        'status' => CourseStatus::DRAFT,
        'teacher_id' => $teacher->id,
    ]);

    $updateData = [
        'title' => 'Updated Course Title',
        'description' => 'Updated Description',
        'status' => CourseStatus::ACTIVE->value,
        'teacher_id' => $teacher->id,
        'is_published' => false,
    ];

    // Act
    $response = $this->actingAs($admin)
        ->put(route('courses.update', $course), $updateData);

    // Assert
    $response->assertRedirect(route('courses.show', $course));
    $response->assertSessionHas('success', 'Course updated successfully.');

    // Verify the course was updated in the database
    $this->assertDatabaseHas('courses', [
        'id' => $course->id,
        'title' => 'Updated Course Title',
        'description' => 'Updated Description',
        'status' => CourseStatus::ACTIVE->value,
    ]);
});

test('destroy method deletes a course and redirects for authorized users', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);

    $course = Course::factory()->create();
    $courseId = $course->id;

    // Act
    $response = $this->actingAs($admin)
        ->delete(route('courses.destroy', $course));

    // Assert
    $this->assertNull(Course::query()->find($courseId));
    $response->assertRedirect(route('courses.index'));
    $response->assertSessionHas('success', 'Course deleted successfully.');
});

test('unauthorized users cannot access course endpoints', function (): void {
    // Arrange
    $user = User::factory()->create();
    // No role assigned, so user has no permissions

    $course = Course::factory()->create();

    // Act & Assert - Test index, create, and store endpoints
    $this->actingAs($user)->get(route('courses.index'))->assertStatus(403);
    $this->actingAs($user)->get(route('courses.create'))->assertStatus(403);

    // For store, we need to provide valid data to pass validation
    $storeData = [
        'title' => 'Test Course',
        'description' => 'Test Description',
        'status' => CourseStatus::DRAFT->value,
        'is_published' => false,
    ];
    $this->actingAs($user)->post(route('courses.store'), $storeData)->assertStatus(403);

    // Test show, edit, update, and destroy endpoints with a specific course
    $this->actingAs($user)->get(route('courses.show', $course))->assertStatus(403);
    $this->actingAs($user)->get(route('courses.edit', $course))->assertStatus(403);

    // For update, we need to provide valid data
    $updateData = [
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'status' => CourseStatus::ACTIVE->value,
        'teacher_id' => $course->teacher_id,
        'is_published' => $course->is_published,
    ];
    $this->actingAs($user)->put(route('courses.update', $course), $updateData)->assertStatus(403);

    $this->actingAs($user)->delete(route('courses.destroy', $course))->assertStatus(403);
});
