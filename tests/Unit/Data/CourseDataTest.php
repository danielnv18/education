<?php

declare(strict_types=1);

use App\Data\CourseData;
use App\Data\ModuleData;
use App\Data\UserData;
use App\Enums\CourseStatus;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Tests\TestCase;

uses(TestCase::class);

it('can be instantiated with required properties', function (): void {
    // Arrange & Act
    $courseData = new CourseData(
        title: 'PHP Fundamentals',
        description: null,
        status: CourseStatus::DRAFT,
        modules: new Collection(),
        endDate: null,
        startDate: null,
        instructor: null,
    );

    // Assert
    expect($courseData)->toBeInstanceOf(CourseData::class)
        ->and($courseData->title)->toBe('PHP Fundamentals')
        ->and($courseData->description)->toBeNull()
        ->and($courseData->status)->toBe(CourseStatus::DRAFT)
        ->and($courseData->modules)->toBeInstanceOf(Collection::class)
        ->and($courseData->modules)->toBeEmpty()
        ->and($courseData->endDate)->toBeNull()
        ->and($courseData->startDate)->toBeNull()
        ->and($courseData->instructor)->toBeNull()
        ->and($courseData->students)->toBeInstanceOf(Collection::class)
        ->and($courseData->students)->toBeEmpty();
});

it('can be instantiated with all properties', function (): void {
    // Arrange
    $now = CarbonImmutable::now();
    $startDate = $now->addDays(1);
    $endDate = $now->addDays(30);

    $instructor = new UserData(
        id: 1,
        name: 'John Doe',
        email: 'john@example.com',
        createdAt: $now,
        emailVerifiedAt: $now,
    );

    $students = new Collection([
        new UserData(
            id: 2,
            name: 'Jane Doe',
            email: 'jane@example.com',
            createdAt: $now,
            emailVerifiedAt: $now,
        ),
        new UserData(
            id: 3,
            name: 'Bob Smith',
            email: 'bob@example.com',
            createdAt: $now,
            emailVerifiedAt: null,
        ),
    ]);

    $modules = new Collection([
        new ModuleData(
            id: 1,
            title: 'Introduction',
            description: 'Introduction to the course',
            order: 1,
        ),
        new ModuleData(
            id: 2,
            title: 'Advanced Topics',
            description: 'Advanced topics in the course',
            order: 2,
        ),
    ]);

    // Act
    $courseData = new CourseData(
        title: 'Advanced PHP',
        description: 'Learn advanced PHP concepts',
        status: CourseStatus::ACTIVE,
        modules: $modules,
        endDate: $endDate,
        startDate: $startDate,
        instructor: $instructor,
        students: $students,
    );

    // Assert
    expect($courseData)->toBeInstanceOf(CourseData::class)
        ->and($courseData->title)->toBe('Advanced PHP')
        ->and($courseData->description)->toBe('Learn advanced PHP concepts')
        ->and($courseData->status)->toBe(CourseStatus::ACTIVE)
        ->and($courseData->modules)->toBeInstanceOf(Collection::class)
        ->and($courseData->modules)->toHaveCount(2)
        ->and($courseData->modules->first()->title)->toBe('Introduction')
        ->and($courseData->modules->last()->title)->toBe('Advanced Topics')
        ->and($courseData->endDate)->toBe($endDate)
        ->and($courseData->startDate)->toBe($startDate)
        ->and($courseData->instructor)->toBeInstanceOf(UserData::class)
        ->and($courseData->instructor->name)->toBe('John Doe')
        ->and($courseData->students)->toBeInstanceOf(Collection::class)
        ->and($courseData->students)->toHaveCount(2)
        ->and($courseData->students->first()->name)->toBe('Jane Doe')
        ->and($courseData->students->last()->name)->toBe('Bob Smith');
});

it('can be converted to array', function (): void {
    // Arrange
    $now = CarbonImmutable::now();
    $startDate = $now->addDays(1);
    $endDate = $now->addDays(30);

    $courseData = new CourseData(
        title: 'PHP Testing',
        description: 'Learn how to test PHP applications',
        status: CourseStatus::DRAFT,
        modules: new Collection(),
        endDate: $endDate,
        startDate: $startDate,
        instructor: null,
    );

    // Act
    $array = $courseData->toArray();

    // Assert
    expect($array)->toBeArray()
        ->and($array)->toHaveKeys(['title', 'description', 'status', 'modules', 'endDate', 'startDate', 'instructor', 'students'])
        ->and($array['title'])->toBe('PHP Testing')
        ->and($array['description'])->toBe('Learn how to test PHP applications')
        ->and($array['status'])->toBe(CourseStatus::DRAFT->value)
        ->and($array['modules'])->toBeArray()
        ->and($array['modules'])->toBeEmpty()
        ->and(new CarbonImmutable($array['endDate']))->equalTo($endDate)
        ->and(new CarbonImmutable($array['startDate']))->equalTo($startDate)
        ->and($array['instructor'])->toBeNull()
        ->and($array['students'])->toBeArray()
        ->and($array['students'])->toBeEmpty();
});

it('can be created from an array using from method', function (): void {
    // Arrange
    $now = CarbonImmutable::now();
    $startDate = $now->addDays(1);
    $endDate = $now->addDays(30);

    $instructor = [
        'id' => 1,
        'name' => 'John Doe',
        'email' => 'john@example.com',
        'created_at' => $now,
        'email_verified_at' => $now,
    ];

    $students = [
        [
            'id' => 2,
            'name' => 'Jane Doe',
            'email' => 'jane@example.com',
            'created_at' => $now,
            'email_verified_at' => $now,
        ],
        [
            'id' => 3,
            'name' => 'Bob Smith',
            'email' => 'bob@example.com',
            'created_at' => $now,
            'email_verified_at' => null,
        ],
    ];

    $modules = [
        [
            'id' => 1,
            'title' => 'Introduction',
            'description' => 'Introduction to the course',
            'order' => 1,
        ],
        [
            'id' => 2,
            'title' => 'Advanced Topics',
            'description' => 'Advanced topics in the course',
            'order' => 2,
        ],
    ];

    // Act
    $courseData = CourseData::from([
        'title' => 'Laravel Mastery',
        'description' => 'Master Laravel framework',
        'status' => CourseStatus::ACTIVE->value,
        'modules' => $modules,
        'end_date' => $endDate,
        'start_date' => $startDate,
        'instructor' => $instructor,
        'students' => $students,
    ]);

    // Assert
    expect($courseData)->toBeInstanceOf(CourseData::class)
        ->and($courseData->title)->toBe('Laravel Mastery')
        ->and($courseData->description)->toBe('Master Laravel framework')
        ->and($courseData->status)->toBe(CourseStatus::ACTIVE)
        ->and($courseData->modules)->toBeInstanceOf(Collection::class)
        ->and($courseData->modules)->toHaveCount(2)
        ->and($courseData->modules->first())->toBeInstanceOf(ModuleData::class)
        ->and($courseData->modules->first()->title)->toBe('Introduction')
        ->and($courseData->modules->last()->title)->toBe('Advanced Topics')
        ->and($courseData->endDate)->toEqual($endDate)
        ->and($courseData->startDate)->toEqual($startDate)
        ->and($courseData->instructor)->toBeInstanceOf(UserData::class)
        ->and($courseData->instructor->name)->toBe('John Doe')
        ->and($courseData->students)->toBeInstanceOf(Collection::class)
        ->and($courseData->students)->toHaveCount(2)
        ->and($courseData->students->first())->toBeInstanceOf(UserData::class)
        ->and($courseData->students->first()->name)->toBe('Jane Doe')
        ->and($courseData->students->last()->name)->toBe('Bob Smith');
});
