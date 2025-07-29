<?php

declare(strict_types=1);

use App\Data\LessonData;
use App\Data\ModuleData;
use App\Enums\LessonType;
use Illuminate\Support\Collection;
use Tests\TestCase;

uses(TestCase::class);

it('can be instantiated with required properties', function (): void {
    // Arrange & Act
    $moduleData = new ModuleData(
        id: 1,
        title: 'Introduction to Laravel',
        description: null,
    );

    // Assert
    expect($moduleData)->toBeInstanceOf(ModuleData::class)
        ->and($moduleData->id)->toBe(1)
        ->and($moduleData->title)->toBe('Introduction to Laravel')
        ->and($moduleData->description)->toBeNull()
        ->and($moduleData->order)->toBe(0) // Default value
        ->and($moduleData->lessons)->toBeInstanceOf(Collection::class)
        ->and($moduleData->lessons)->toBeEmpty();
});

it('can be instantiated with all properties', function (): void {
    // Arrange
    $lessons = new Collection([
        new LessonData(
            id: 1,
            title: 'Laravel Basics',
            content: 'Learn the basics of Laravel.',
            type: LessonType::Text,
            order: 1,
        ),
        new LessonData(
            id: 2,
            title: 'Laravel Routing',
            content: 'Learn about Laravel routing.',
            type: LessonType::Video,
            order: 2,
        ),
    ]);

    // Act
    $moduleData = new ModuleData(
        id: 2,
        title: 'Advanced Laravel',
        description: 'Advanced Laravel concepts and techniques.',
        order: 3,
        lessons: $lessons,
    );

    // Assert
    expect($moduleData)->toBeInstanceOf(ModuleData::class)
        ->and($moduleData->id)->toBe(2)
        ->and($moduleData->title)->toBe('Advanced Laravel')
        ->and($moduleData->description)->toBe('Advanced Laravel concepts and techniques.')
        ->and($moduleData->order)->toBe(3)
        ->and($moduleData->lessons)->toBeInstanceOf(Collection::class)
        ->and($moduleData->lessons)->toHaveCount(2)
        ->and($moduleData->lessons->first()->title)->toBe('Laravel Basics')
        ->and($moduleData->lessons->last()->title)->toBe('Laravel Routing');
});

it('can be converted to array', function (): void {
    // Arrange
    $moduleData = new ModuleData(
        id: 3,
        title: 'Laravel Testing',
        description: 'Learn how to test Laravel applications.',
        order: 4,
    );

    // Act
    $array = $moduleData->toArray();

    // Assert
    expect($array)->toBeArray()
        ->and($array)->toHaveKeys(['id', 'title', 'description', 'order', 'lessons'])
        ->and($array['id'])->toBe(3)
        ->and($array['title'])->toBe('Laravel Testing')
        ->and($array['description'])->toBe('Learn how to test Laravel applications.')
        ->and($array['order'])->toBe(4)
        ->and($array['lessons'])->toBeArray()
        ->and($array['lessons'])->toBeEmpty();
});

it('can be created from an array using from method', function (): void {
    // Arrange
    $lessons = [
        [
            'title' => 'Laravel Basics',
            'content' => 'Learn the basics of Laravel.',
            'type' => LessonType::Text->value,
            'order' => 1,
        ],
        [
            'title' => 'Laravel Routing',
            'content' => 'Learn about Laravel routing.',
            'type' => LessonType::Video->value,
            'order' => 2,
        ],
    ];

    $moduleData = [
        'id' => 4,
        'title' => 'Laravel Advanced Features',
        'description' => 'Explore advanced Laravel features',
        'order' => 5,
        'lessons' => $lessons,
    ];

    // Act
    $module = ModuleData::from($moduleData);

    // Assert
    expect($module)->toBeInstanceOf(ModuleData::class)
        ->and($module->id)->toBe(4)
        ->and($module->title)->toBe('Laravel Advanced Features')
        ->and($module->description)->toBe('Explore advanced Laravel features')
        ->and($module->order)->toBe(5)
        ->and($module->lessons)->toBeInstanceOf(Collection::class)
        ->and($module->lessons)->toHaveCount(2)
        ->and($module->lessons->first())->toBeInstanceOf(LessonData::class)
        ->and($module->lessons->first()->title)->toBe('Laravel Basics')
        ->and($module->lessons->first()->type)->toBe(LessonType::Text)
        ->and($module->lessons->last()->title)->toBe('Laravel Routing')
        ->and($module->lessons->last()->type)->toBe(LessonType::Video);
});
