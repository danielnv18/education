<?php

declare(strict_types=1);

use App\Actions\Courses\CreateCourseAction;
use App\Enums\CourseStatus;
use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('creates a course with the provided data', function (): void {
    // Arrange
    $data = [
        'title' => 'Test Course',
        'description' => 'This is a test course',
        'status' => CourseStatus::Draft->value,
        'is_published' => false,
    ];

    $action = new CreateCourseAction();

    // Act
    $course = $action->handle($data);

    // Assert
    expect($course)->toBeInstanceOf(Course::class)
        ->and($course->title)->toBe($data['title'])
        ->and($course->description)->toBe($data['description'])
        ->and($course->status)->toBe(CourseStatus::Draft)
        ->and($course->is_published)->toBe($data['is_published']);
});

it('creates a course with a teacher', function (): void {
    // Arrange
    $teacher = User::factory()->create();

    $data = [
        'title' => 'Course with Teacher',
        'description' => 'This course has a teacher assigned',
        'status' => CourseStatus::Active->value,
        'is_published' => true,
        'teacher_id' => $teacher->id,
    ];

    $action = new CreateCourseAction();

    // Act
    $course = $action->handle($data);

    // Assert
    expect($course)->toBeInstanceOf(Course::class)
        ->and($course->teacher_id)->toBe($teacher->id);
});

it('creates a course with dates', function (): void {
    // Arrange
    $startDate = now()->addDays(5);
    $endDate = now()->addDays(30);

    $data = [
        'title' => 'Course with Dates',
        'description' => 'This course has start and end dates',
        'status' => CourseStatus::Active->value,
        'is_published' => true,
        'start_date' => $startDate,
        'end_date' => $endDate,
    ];

    $action = new CreateCourseAction();

    // Act
    $course = $action->handle($data);

    // Assert
    expect($course)->toBeInstanceOf(Course::class)
        ->and($course->start_date->toDateString())->toBe($startDate->toDateString())
        ->and($course->end_date->toDateString())->toBe($endDate->toDateString());
});

it('creates a course with a cover image', function (): void {
    // Arrange
    $cover = UploadedFile::fake()->image('cover.jpg');

    $data = [
        'title' => 'Course with Cover Image',
        'description' => 'This course has a cover image',
        'status' => CourseStatus::Active->value,
        'is_published' => true,
        'cover' => $cover,
    ];

    $action = new CreateCourseAction();

    // Act
    $course = $action->handle($data);

    // Assert
    expect($course)->toBeInstanceOf(Course::class)
        ->and($course->getFirstMedia('cover'))->not->toBeNull()
        ->and($course->getFirstMedia('cover')->file_name)->toBe('cover.jpg');
});

it('uses a database transaction', function (): void {
    // Arrange
    $data = [
        'title' => 'Transaction Test Course',
        'status' => CourseStatus::Draft->value,
    ];

    // Mock DB facade to verify transaction is used
    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(function ($callback) {
            return $callback();
        });

    $action = new CreateCourseAction();

    // Act
    $action->handle($data);

    // Assert is handled by the mock expectations
});
