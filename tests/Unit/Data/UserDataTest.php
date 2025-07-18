<?php

declare(strict_types=1);

use App\Data\RoleData;
use App\Data\UserData;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Tests\TestCase;

uses(TestCase::class);

it('can be instantiated with required properties', function (): void {
    // Arrange
    $now = CarbonImmutable::now();

    // Act
    $userData = new UserData(
        id: 1,
        name: 'John Doe',
        email: 'john@example.com',
        avatar: null,
        createdAt: $now,
        emailVerifiedAt: null,
    );

    // Assert
    expect($userData)->toBeInstanceOf(UserData::class)
        ->and($userData->id)->toBe(1)
        ->and($userData->name)->toBe('John Doe')
        ->and($userData->email)->toBe('john@example.com')
        ->and($userData->createdAt)->toBe($now)
        ->and($userData->emailVerifiedAt)->toBeNull()
        ->and($userData->roles)->toBeInstanceOf(Collection::class)
        ->and($userData->roles)->toBeEmpty();
});

it('can be instantiated with all properties including roles', function (): void {
    // Arrange
    $now = CarbonImmutable::now();
    $roles = new Collection([
        new RoleData(name: 'admin'),
        new RoleData(name: 'teacher'),
    ]);

    // Act
    $userData = new UserData(
        id: 2,
        name: 'Jane Doe',
        email: 'jane@example.com',
        avatar: '/avatars/jane.jpg',
        createdAt: $now,
        emailVerifiedAt: $now,
        roles: $roles,
    );

    // Assert
    expect($userData)->toBeInstanceOf(UserData::class)
        ->and($userData->id)->toBe(2)
        ->and($userData->name)->toBe('Jane Doe')
        ->and($userData->email)->toBe('jane@example.com')
        ->and($userData->createdAt)->toBe($now)
        ->and($userData->emailVerifiedAt)->toBe($now)
        ->and($userData->roles)->toBeInstanceOf(Collection::class)
        ->and($userData->roles)->toHaveCount(2)
        ->and($userData->roles->first()->name)->toBe('admin')
        ->and($userData->roles->last()->name)->toBe('teacher');
});

it('can be converted to array', function (): void {
    // Arrange
    $now = CarbonImmutable::now();

    // Act
    $userData = new UserData(
        id: 3,
        name: 'Bob Smith',
        email: 'bob@example.com',
        avatar: '/bob.jpg',
        createdAt: $now,
        emailVerifiedAt: $now,
    );

    $array = $userData->toArray();

    // Assert
    expect($array)->toBeArray()
        ->and($array)->toHaveKeys(['id', 'name', 'email', 'createdAt', 'emailVerifiedAt', 'roles'])
        ->and($array['id'])->toBe(3)
        ->and($array['name'])->toBe('Bob Smith')
        ->and($array['email'])->toBe('bob@example.com')
        ->and(new CarbonImmutable($array['createdAt']))->equalTo($now)
        ->and(new CarbonImmutable($array['emailVerifiedAt']))->equalTo($now)
        ->and($array['roles'])->toBeArray()
        ->and($array['roles'])->toBeEmpty();
});

it('can be created from an array using from method', function (): void {
    // Arrange
    $now = CarbonImmutable::now();
    $roles = [
        ['name' => 'admin'],
        ['name' => 'teacher'],
    ];

    // Act
    $userData = UserData::from([
        'id' => 4,
        'name' => 'Alice Johnson',
        'email' => 'alice@example.com',
        'created_at' => $now,
        'email_verified_at' => $now,
        'roles' => $roles,
    ]);

    // Assert
    expect($userData)->toBeInstanceOf(UserData::class)
        ->and($userData->id)->toBe(4)
        ->and($userData->name)->toBe('Alice Johnson')
        ->and($userData->email)->toBe('alice@example.com')
        ->and($userData->createdAt)->toEqual($now)
        ->and($userData->emailVerifiedAt)->toEqual($now)
        ->and($userData->roles)->toBeInstanceOf(Collection::class)
        ->and($userData->roles)->toHaveCount(2)
        ->and($userData->roles->first())->toBeInstanceOf(RoleData::class)
        ->and($userData->roles->first()->name)->toBe('admin')
        ->and($userData->roles->last()->name)->toBe('teacher');
});
