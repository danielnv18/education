<?php

declare(strict_types=1);

use App\Enums\LessonType;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

test('store method creates a lesson and redirects for authorized users', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::Teacher);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $module = Module::factory()->create([
        'course_id' => $course->id,
    ]);

    $lessonData = [
        'title' => 'Test Lesson',
        'content' => 'Test Content',
        'module_id' => $module->id,
        'order' => 1,
        'type' => LessonType::Text->value,
        'is_published' => true,
    ];

    // Act
    $response = $this->actingAs($teacher)
        ->post(route('courses.lessons.store', $course), $lessonData);

    // Assert
    $response->assertRedirect(route('courses.content.index', $course));
    $response->assertSessionHas('success', 'Lesson created successfully');

    // Verify the lesson was created in the database
    $this->assertDatabaseHas('lessons', [
        'title' => 'Test Lesson',
        'content' => 'Test Content',
        'module_id' => $module->id,
        'order' => 1,
        'type' => LessonType::Text->value,
        'is_published' => true,
    ]);
});

test('update method updates a lesson and redirects for authorized users', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::Teacher);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $module = Module::factory()->create([
        'course_id' => $course->id,
    ]);

    $lesson = Lesson::factory()->create([
        'module_id' => $module->id,
        'title' => 'Original Title',
        'content' => 'Original Content',
        'type' => LessonType::Text,
    ]);

    $updateData = [
        'title' => 'Updated Title',
        'content' => 'Updated Content',
        'type' => LessonType::Video->value,
    ];

    // Act
    $response = $this->actingAs($teacher)
        ->put(route('courses.lessons.update', [$course, $lesson]), $updateData);

    // Assert
    $response->assertRedirect(route('courses.content.index', $course));
    $response->assertSessionHas('success', 'Lesson updated successfully');

    // Verify the lesson was updated in the database
    $this->assertDatabaseHas('lessons', [
        'id' => $lesson->id,
        'title' => 'Updated Title',
        'content' => 'Updated Content',
        'type' => LessonType::Video->value,
    ]);
});

test('destroy method deletes a lesson and redirects for authorized users', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::Teacher);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $module = Module::factory()->create([
        'course_id' => $course->id,
    ]);

    $lesson = Lesson::factory()->create([
        'module_id' => $module->id,
    ]);

    $lessonId = $lesson->id;

    // Act
    $response = $this->actingAs($teacher)
        ->delete(route('courses.lessons.destroy', [$course, $lesson]));

    // Assert
    $response->assertRedirect(route('courses.content.index', $course));
    $response->assertSessionHas('success', 'Lesson updated successfully');

    // Verify the lesson was deleted from the database
    $this->assertDatabaseMissing('lessons', [
        'id' => $lessonId,
    ]);
});

test('any user cannot create lessons', function (): void {
    // Arrange
    $user = User::factory()->create();
    // No role assigned, but the controller doesn't check permissions

    $course = Course::factory()->create();
    $module = Module::factory()->create([
        'course_id' => $course->id,
    ]);

    $lessonData = [
        'title' => 'Test Lesson',
        'content' => 'Test Content',
        'module_id' => $module->id,
        'order' => 1,
        'type' => LessonType::Text->value,
        'is_published' => true,
    ];

    // Act
    $response = $this->actingAs($user)
        ->post(route('courses.lessons.store', $course), $lessonData);

    // Assert
    $response->assertStatus(403);
});

test('any user cannot update lessons', function (): void {
    // Arrange
    $user = User::factory()->create();
    // No role assigned, but the controller doesn't check permissions

    $course = Course::factory()->create();
    $module = Module::factory()->create([
        'course_id' => $course->id,
    ]);
    $lesson = Lesson::factory()->create([
        'module_id' => $module->id,
    ]);

    $updateData = [
        'title' => 'Updated Title',
    ];

    // Act
    $response = $this->actingAs($user)
        ->put(route('courses.lessons.update', [$course, $lesson]), $updateData);

    // Assert
    $response->assertStatus(403);
});

test('any user cannot delete lessons', function (): void {
    // Arrange
    $user = User::factory()->create();
    // No role assigned, but the controller doesn't check permissions

    $course = Course::factory()->create();
    $module = Module::factory()->create([
        'course_id' => $course->id,
    ]);
    $lesson = Lesson::factory()->create([
        'module_id' => $module->id,
    ]);

    $lessonId = $lesson->id;

    // Act
    $response = $this->actingAs($user)
        ->delete(route('courses.lessons.destroy', [$course, $lesson]));

    // Assert
    $response->assertStatus(403);

});

test('validation fails when required fields are missing for store', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::Teacher);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    // Act
    $response = $this->actingAs($teacher)
        ->post(route('courses.lessons.store', $course), [
            // Missing required fields
        ]);

    // Assert
    $response->assertSessionHasErrors(['title', 'module_id', 'type']);
});

test('validation fails when required fields are invalid for store', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::Teacher);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    // Act
    $response = $this->actingAs($teacher)
        ->post(route('courses.lessons.store', $course), [
            'title' => '',
            'module_id' => 999, // Non-existent module
            'type' => 'invalid-type',
            'order' => 'not-an-integer',
        ]);

    // Assert
    $response->assertSessionHasErrors(['title', 'module_id', 'type', 'order']);
});

test('only course teacher can manage lessons', function (): void {
    // Arrange
    $teacher1 = User::factory()->create();
    $teacher1->assignRole(UserRole::Teacher);

    $teacher2 = User::factory()->create();
    $teacher2->assignRole(UserRole::Teacher);

    $course = Course::factory()->create([
        'teacher_id' => $teacher1->id,
    ]);

    $module = Module::factory()->create([
        'course_id' => $course->id,
    ]);

    $lesson = Lesson::factory()->create([
        'module_id' => $module->id,
        'title' => 'Original Title',
    ]);

    // Test that another teacher (not the course teacher) can create a lesson
    $lessonData = [
        'title' => 'Test Lesson by Another Teacher',
        'content' => 'Test Content',
        'module_id' => $module->id,
        'type' => LessonType::Text->value,
    ];

    // Act - Create
    $this->actingAs($teacher2)->post(route('courses.lessons.store', $course), $lessonData)->assertStatus(403);
    $this->actingAs($teacher2)->put(route('courses.lessons.update', [$course, $lesson]), ['title' => 'Updated by Another Teacher'])->assertStatus(403);

    // Create a new lesson for delete test
    $lessonToDelete = Lesson::factory()->create([
        'module_id' => $module->id,
    ]);
    // Act - Delete
    $this->actingAs($teacher2)->delete(route('courses.lessons.destroy', [$course, $lessonToDelete]))->assertStatus(403);
});
