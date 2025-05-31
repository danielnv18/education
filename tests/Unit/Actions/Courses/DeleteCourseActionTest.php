<?php

declare(strict_types=1);

use App\Actions\Courses\DeleteCourseAction;
use App\Models\Course;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('deletes a course', function (): void {
    // Arrange
    $course = Course::factory()->create();
    $courseId = $course->id;

    $action = new DeleteCourseAction();

    // Act
    $action->handle($course);

    // Assert
    expect(Course::query()->find($courseId))->toBeNull();
});

it('uses a database transaction', function (): void {
    // Arrange
    $course = Course::factory()->create();

    // Mock DB facade to verify transaction is used
    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(function ($callback) {
            $callback();
        });

    $action = new DeleteCourseAction();

    // Act
    $action->handle($course);

    // Assert is handled by the mock expectations
});
