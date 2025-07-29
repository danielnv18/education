<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

test('it sends password reset link and redirects with success message', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::Admin);

    $user = User::factory()->create();

    // Act
    $response = $this->actingAs($admin)
        ->post(route('admin.users.send-password-reset-link', $user));

    // Assert
    $response->assertRedirect();
    $response->assertSessionHas('success', 'Password reset link sent successfully.');
});

test('unauthorized users cannot send password reset links', function (): void {
    // Arrange
    $regularUser = User::factory()->create();
    $targetUser = User::factory()->create();

    // Act & Assert
    $this->actingAs($regularUser)
        ->post(route('admin.users.send-password-reset-link', $targetUser))
        ->assertStatus(403);
});

test('unauthenticated users are redirected to login', function (): void {
    // Arrange
    $user = User::factory()->create();

    // Act & Assert
    $this->post(route('admin.users.send-password-reset-link', $user))
        ->assertRedirect(route('login'));
});

test('teacher cannot send password reset links', function (): void {
    // Arrange
    $teacher = User::factory()->create();
    $teacher->assignRole(UserRole::Teacher);

    $user = User::factory()->create();

    // Act & Assert
    $this->actingAs($teacher)
        ->post(route('admin.users.send-password-reset-link', $user))
        ->assertStatus(403);
});
