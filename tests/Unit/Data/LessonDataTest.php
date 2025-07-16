<?php

declare(strict_types=1);

use App\Data\LessonData;
use App\Enums\LessonType;
use Tests\TestCase;

uses(TestCase::class);

it('can be instantiated with required properties', function (): void {
    // Arrange & Act
    $lessonData = new LessonData(
        id: null,
        title: 'Introduction to PHP',
        content: 'PHP is a server-side scripting language.',
        type: LessonType::TEXT,
    );

    // Assert
    expect($lessonData)->toBeInstanceOf(LessonData::class)
        ->and($lessonData->id)->toBeNull()
        ->and($lessonData->title)->toBe('Introduction to PHP')
        ->and($lessonData->content)->toBe('PHP is a server-side scripting language.')
        ->and($lessonData->type)->toBe(LessonType::TEXT)
        ->and($lessonData->order)->toBe(0); // Default value
});

it('can be instantiated with all properties', function (): void {
    // Arrange & Act
    $lessonData = new LessonData(
        id: 1,
        title: 'Advanced PHP',
        content: 'Learn advanced PHP concepts.',
        type: LessonType::VIDEO,
        order: 2,
    );

    // Assert
    expect($lessonData)->toBeInstanceOf(LessonData::class)
        ->and($lessonData->id)->toBe(1)
        ->and($lessonData->title)->toBe('Advanced PHP')
        ->and($lessonData->content)->toBe('Learn advanced PHP concepts.')
        ->and($lessonData->type)->toBe(LessonType::VIDEO)
        ->and($lessonData->order)->toBe(2);
});

it('can be converted to array', function (): void {
    // Arrange
    $lessonData = new LessonData(
        id: 1,
        title: 'PHP Testing',
        content: 'Learn how to test PHP applications.',
        type: LessonType::DOCUMENT,
        order: 3,
    );

    // Act
    $array = $lessonData->toArray();

    // Assert
    expect($array)->toBeArray()
        ->and($array)->toHaveKeys(['id', 'title', 'content', 'type', 'order'])
        ->and($array['id'])->toBe(1)
        ->and($array['title'])->toBe('PHP Testing')
        ->and($array['content'])->toBe('Learn how to test PHP applications.')
        ->and($array['type'])->toBe(LessonType::DOCUMENT->value)
        ->and($array['order'])->toBe(3);
});

it('can be created from an array using from method', function (): void {
    // Arrange
    $lessonData = [
        'title' => 'Laravel Basics',
        'content' => 'Introduction to Laravel framework',
        'type' => LessonType::VIDEO->value,
        'order' => 4,
    ];

    // Act
    $lesson = LessonData::from($lessonData);

    // Assert
    expect($lesson)->toBeInstanceOf(LessonData::class)
        ->and($lesson->id)->toBeNull()
        ->and($lesson->title)->toBe('Laravel Basics')
        ->and($lesson->content)->toBe('Introduction to Laravel framework')
        ->and($lesson->type)->toBe(LessonType::VIDEO)
        ->and($lesson->order)->toBe(4);
});
