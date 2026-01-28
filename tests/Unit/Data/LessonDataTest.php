<?php

declare(strict_types=1);

use App\Data\LessonData;
use App\Enums\LessonContentType;
use Carbon\CarbonImmutable;

it('creates lesson data with publish flag', function (): void {
    $lesson = new LessonData(
        id: 1,
        moduleId: 10,
        title: 'Intro',
        slug: 'intro',
        summary: 'Summary',
        content: 'Content',
        contentType: LessonContentType::Markdown,
        order: 1,
        durationMinutes: 15,
        publishedAt: CarbonImmutable::parse('2026-01-01T00:00:00Z'),
        metadata: ['defaultTab' => 'content'],
        isPublished: true,
    );

    expect($lesson->isPublished)->toBeTrue()
        ->and($lesson->contentType)->toBe(LessonContentType::Markdown)
        ->and($lesson->metadata['defaultTab'])->toBe('content');
});
