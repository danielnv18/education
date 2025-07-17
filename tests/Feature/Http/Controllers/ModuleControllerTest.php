<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\Module;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

test('store method creates a module and redirects for authorized users', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::TEACHER);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $moduleData = [
        'title' => 'Test Module',
        'description' => 'Test Description',
        'course_id' => $course->id,
        'order' => 1,
        'is_published' => false,
    ];

    // Act
    $response = $this->actingAs($teacher)
        ->post(route('courses.modules.store', $course), $moduleData);

    // Assert
    $module = Module::query()->latest('id')->first();
    $this->assertNotNull($module);
    $this->assertEquals($moduleData['title'], $module->title);
    $this->assertEquals($moduleData['course_id'], $module->course_id);

    $response->assertRedirect(route('courses.content.index', $course));
    $response->assertSessionHas('success', 'Module created successfully');
});

test('update method updates a module and redirects for authorized users', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::TEACHER);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $module = Module::factory()->create([
        'course_id' => $course->id,
        'title' => 'Original Title',
        'description' => 'Original Description',
        'order' => 1,
        'is_published' => false,
    ]);

    $updateData = [
        'title' => 'Updated Module Title',
        'description' => 'Updated Description',
        'course_id' => $course->id,
        'order' => 2,
        'is_published' => true,
    ];

    // Act
    $response = $this->actingAs($teacher)
        ->put(route('courses.modules.update', [$course, $module]), $updateData);

    // Assert
    $response->assertRedirect(route('courses.content.index', $course));
    $response->assertSessionHas('success', 'Module Update successfully');

    // Verify the module was updated in the database
    $this->assertDatabaseHas('modules', [
        'id' => $module->id,
        'title' => 'Updated Module Title',
        'description' => 'Updated Description',
        'order' => 2,
        'is_published' => true,
    ]);
});

test('destroy method deletes a module and redirects for authorized users', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::TEACHER);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $module = Module::factory()->create([
        'course_id' => $course->id,
    ]);

    // Create some lessons for the module
    $module->lessons()->create([
        'title' => 'Test Lesson',
        'content' => 'Test Content',
        'order' => 1,
    ]);

    $moduleId = $module->id;

    // Act
    $response = $this->actingAs($teacher)
        ->delete(route('courses.modules.destroy', [$course, $module]));

    // Assert
    $this->assertNull(Module::query()->find($moduleId));
    $response->assertRedirect(route('courses.content.index', $course));
    $response->assertSessionHas('success', 'Module delete successfully');

    // Verify that the lessons were also deleted
    $this->assertDatabaseMissing('lessons', [
        'module_id' => $moduleId,
    ]);
});

test('unauthenticated users are redirected to login', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $module = Module::factory()->create([
        'course_id' => $course->id,
    ]);

    // For store, we need to provide valid data to pass validation
    $storeData = [
        'title' => 'Test Module',
        'description' => 'Test Description',
        'course_id' => $course->id,
        'order' => 1,
        'is_published' => false,
    ];

    // Act & Assert
    $this->post(route('courses.modules.store', $course), $storeData)
        ->assertRedirect(route('login'));

    // For update, we need to provide valid data
    $updateData = [
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'course_id' => $course->id,
        'order' => 2,
        'is_published' => true,
    ];

    $this->put(route('courses.modules.update', [$course, $module]), $updateData)
        ->assertRedirect(route('login'));

    $this->delete(route('courses.modules.destroy', [$course, $module]))
        ->assertRedirect(route('login'));
});

test('authenticated users without permission cannot access', function (): void {
    // Arrange
    $user = User::factory()->create();
    // No role assigned, so the user has no permissions

    $course = Course::factory()->create();
    $module = Module::factory()->create([
        'course_id' => $course->id,
    ]);

    // For store, we need to provide valid data to pass validation
    $storeData = [
        'title' => 'Test Module',
        'description' => 'Test Description',
        'course_id' => $course->id,
        'order' => 1,
        'is_published' => false,
    ];

    // Act & Assert
    $this->actingAs($user)
        ->post(route('courses.modules.store', $course), $storeData)
        ->assertStatus(403);

    // For update, we need to provide valid data
    $updateData = [
        'title' => 'Updated Title',
        'description' => 'Updated Description',
        'course_id' => $course->id,
        'order' => 2,
        'is_published' => true,
    ];

    $this->actingAs($user)
        ->put(route('courses.modules.update', [$course, $module]), $updateData)
        ->assertStatus(403);

    $this->actingAs($user)
        ->delete(route('courses.modules.destroy', [$course, $module]))
        ->assertStatus(403);
});
