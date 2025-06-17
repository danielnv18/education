<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;
use App\Policies\UserPolicy;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

it('allows admin to view any users', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);
    // Admin role has VIEW_ANY_USERS permission assigned in PermissionSeeder

    $policy = new UserPolicy();

    // Act & Assert
    expect($policy->viewAny($admin))->toBeTrue();
});

it('does not allow user without view any users permission to view any users', function (): void {
    // Arrange
    $user = User::factory()->create();
    $user->assignRole(UserRole::TEACHER);
    // User doesn't have VIEW_ANY_USERS permission

    $policy = new UserPolicy();

    // Act & Assert
    expect($policy->viewAny($user))->toBeFalse();
});

it('allows user to view their own profile', function (): void {
    // Arrange
    $user = User::factory()->create();
    // Even without any permissions

    $policy = new UserPolicy();

    // Act & Assert
    expect($policy->view($user, $user))->toBeTrue();
});

it('allows admin to view other users', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);
    // Admin role has VIEW_USER permission assigned in PermissionSeeder

    $otherUser = User::factory()->create();

    $policy = new UserPolicy();

    // Act & Assert
    expect($policy->view($admin, $otherUser))->toBeTrue();
});

it('does not allow user without view user permission to view other users', function (): void {
    // Arrange
    $user = User::factory()->create();
    // User doesn't have VIEW_USER permission

    $otherUser = User::factory()->create();

    $policy = new UserPolicy();

    // Act & Assert
    expect($policy->view($user, $otherUser))->toBeFalse();
});

it('allows admin to create users', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);
    // Admin role has CREATE_USER permission assigned in PermissionSeeder

    $policy = new UserPolicy();

    // Act & Assert
    expect($policy->create($admin))->toBeTrue();
});

it('does not allow user without create user permission to create users', function (): void {
    // Arrange
    $user = User::factory()->create();
    // User doesn't have CREATE_USER permission

    $policy = new UserPolicy();

    // Act & Assert
    expect($policy->create($user))->toBeFalse();
});

it('allows user to update their own profile', function (): void {
    // Arrange
    $user = User::factory()->create();
    // Even without any permissions

    $policy = new UserPolicy();

    // Act & Assert
    expect($policy->update($user, $user))->toBeTrue();
});

it('allows admin to update other users', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);
    // Admin role has UPDATE_USER permission assigned in PermissionSeeder

    $otherUser = User::factory()->create();

    $policy = new UserPolicy();

    // Act & Assert
    expect($policy->update($admin, $otherUser))->toBeTrue();
});

it('does not allow user without update user permission to update other users', function (): void {
    // Arrange
    $user = User::factory()->create();
    // User doesn't have UPDATE_USER permission

    $otherUser = User::factory()->create();

    $policy = new UserPolicy();

    // Act & Assert
    expect($policy->update($user, $otherUser))->toBeFalse();
});

it('does not allow admin to delete themselves', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);
    // Admin role has DELETE_USER permission assigned in PermissionSeeder

    $policy = new UserPolicy();

    // Act & Assert
    expect($policy->delete($admin, $admin))->toBeFalse();
});

it('allows admin to delete other users', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);
    // Admin role has DELETE_USER permission assigned in PermissionSeeder

    $otherUser = User::factory()->create();

    $policy = new UserPolicy();

    // Act & Assert
    expect($policy->delete($admin, $otherUser))->toBeTrue();
});

it('does not allow user without delete user permission to delete other users', function (): void {
    // Arrange
    $user = User::factory()->create();
    // User doesn't have DELETE_USER permission

    $otherUser = User::factory()->create();

    $policy = new UserPolicy();

    // Act & Assert
    expect($policy->delete($user, $otherUser))->toBeFalse();
});

it('allows admin to restore users', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);
    // Admin role has DELETE_USER permission assigned in PermissionSeeder

    $otherUser = User::factory()->create();

    $policy = new UserPolicy();

    // Act & Assert
    expect($policy->restore($admin, $otherUser))->toBeTrue();
});

it('does not allow user without delete user permission to restore users', function (): void {
    // Arrange
    $user = User::factory()->create();
    // User doesn't have DELETE_USER permission

    $otherUser = User::factory()->create();

    $policy = new UserPolicy();

    // Act & Assert
    expect($policy->restore($user, $otherUser))->toBeFalse();
});

it('allows admin to force delete users', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);
    // Admin role has DELETE_USER permission assigned in PermissionSeeder

    $otherUser = User::factory()->create();

    $policy = new UserPolicy();

    // Act & Assert
    expect($policy->forceDelete($admin, $otherUser))->toBeTrue();
});

it('does not allow user without delete user permission to force delete users', function (): void {
    // Arrange
    $user = User::factory()->create();
    // User doesn't have DELETE_USER permission

    $otherUser = User::factory()->create();

    $policy = new UserPolicy();

    // Act & Assert
    expect($policy->forceDelete($user, $otherUser))->toBeFalse();
});
