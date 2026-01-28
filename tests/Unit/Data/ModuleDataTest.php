<?php

declare(strict_types=1);

use App\Data\LessonData;
use App\Data\ModuleData;
use App\Enums\LessonContentType;
use Carbon\CarbonImmutable;

it('creates module data with lessons', function (): void {
    $lesson = new LessonData(
        id: 2,
        moduleId: 20,
        title: 'Lesson',
        slug: 'lesson',
        summary: null,
        content: null,
        contentType: LessonContentType::Markdown,
        order: 2,
        durationMinutes: null,
        publishedAt: null,
        metadata: [],
        isPublished: false,
    );

    $module = new ModuleData(
        id: 3,
        courseId: 30,
        title: 'Module',
        slug: 'module',
        description: 'Description',
        order: 5,
        publishedAt: CarbonImmutable::parse('2026-01-02T00:00:00Z'),
        isPublished: true,
        metadata: ['visibilityNote' => 'all'],
        lessons: collect([$lesson]),
    );

    expect($module->isPublished)->toBeTrue()
        ->and($module->lessons)->toHaveCount(1)
        ->and($module->lessons?->first()?->id)->toBe(2);
});
