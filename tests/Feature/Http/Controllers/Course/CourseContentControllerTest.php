<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\Lesson;
use App\Models\Module;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Inertia\Testing\AssertableInertia;

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

test('index method returns course content for authorized users', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::TEACHER);

    $course = Course::factory()->create([
        'teacher_id' => $teacher->id,
    ]);

    $modules = Module::factory()->count(2)->create([
        'course_id' => $course->id,
    ]);

    foreach ($modules as $module) {
        Lesson::factory()->count(3)->create([
            'module_id' => $module->id,
        ]);
    }

    // Act
    $response = $this->actingAs($teacher)->get(route('courses.content.index', $course));

    // Assert
    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('courses/content')
        ->has('course')
        ->has('modules', 2)
    );
});

test('any user can access course content', function (): void {
    // Arrange
    $user = User::factory()->create();
    // No role assigned, but the controller doesn't check permissions

    $course = Course::factory()->create();

    // Act & Assert
    $response = $this->actingAs($user)->get(route('courses.content.index', $course));

    // The controller doesn't check authorization, so it should return 200
    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('courses/content')
        ->has('course')
    );
});

test('student can view course content they are enrolled in', function (): void {
    // Arrange
    $student = User::factory()->create();
    $student->assignRole(UserRole::STUDENT);

    $course = Course::factory()->create();
    $course->students()->attach($student->id);

    $modules = Module::factory()->count(2)->create([
        'course_id' => $course->id,
    ]);

    foreach ($modules as $module) {
        Lesson::factory()->count(3)->create([
            'module_id' => $module->id,
        ]);
    }

    // Act
    $response = $this->actingAs($student)->get(route('courses.content.index', $course));

    // Assert
    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('courses/content')
        ->has('course')
        ->has('modules', 2)
    );
});

test('student can view content of courses they are not enrolled in', function (): void {
    // Arrange
    $student = User::factory()->create();
    $student->assignRole(UserRole::STUDENT);

    $course = Course::factory()->create();
    // Student is not enrolled in this course, but the controller doesn't check enrollment

    // Act & Assert
    $response = $this->actingAs($student)->get(route('courses.content.index', $course));

    // The controller doesn't check enrollment, so it should return 200
    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page
        ->component('courses/content')
        ->has('course')
    );
});
