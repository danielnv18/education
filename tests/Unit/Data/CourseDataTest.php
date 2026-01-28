<?php

declare(strict_types=1);

use App\Data\CourseData;
use App\Data\ModuleData;
use App\Enums\CourseStatus;
use Carbon\CarbonImmutable;

it('creates course data with modules', function (): void {
    $module = new ModuleData(
        id: 4,
        courseId: 40,
        title: 'Module Title',
        slug: 'module-title',
        description: null,
        order: 1,
        publishedAt: CarbonImmutable::parse('2026-01-03T00:00:00Z'),
        isPublished: false,
        metadata: [],
    );

    $course = new CourseData(
        id: 5,
        slug: 'course',
        title: 'Course Title',
        description: 'Desc',
        status: CourseStatus::Draft,
        publishedAt: CarbonImmutable::parse('2026-01-04T00:00:00Z'),
        startsAt: null,
        endsAt: null,
        metadata: [],
        teacherId: 7,
        modules: collect([$module]),
        isPublished: false,
    );

    expect($course->status)->toBe(CourseStatus::Draft)
        ->and($course->modules)->toHaveCount(1)
        ->and($course->teacherId)->toBe(7)
        ->and($course->isPublished)->toBeFalse();
});
