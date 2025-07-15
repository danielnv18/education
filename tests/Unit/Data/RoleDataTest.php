<?php

declare(strict_types=1);

use App\Data\RoleData;
use Tests\TestCase;

uses(TestCase::class);

it('can be instantiated with required properties', function (): void {
    // Arrange & Act
    $roleData = new RoleData(
        name: 'admin',
    );

    // Assert
    expect($roleData)->toBeInstanceOf(RoleData::class)
        ->and($roleData->name)->toBe('admin');
});

it('can be converted to array', function (): void {
    // Arrange
    $roleData = new RoleData(
        name: 'teacher',
    );

    // Act
    $array = $roleData->toArray();

    // Assert
    expect($array)->toBeArray()
        ->and($array)->toHaveKey('name')
        ->and($array['name'])->toBe('teacher');
});

it('can be created from an array using from method', function (): void {
    // Arrange
    $roleData = [
        'name' => 'student',
    ];

    // Act
    $role = RoleData::from($roleData);

    // Assert
    expect($role)->toBeInstanceOf(RoleData::class)
        ->and($role->name)->toBe('student');
});
