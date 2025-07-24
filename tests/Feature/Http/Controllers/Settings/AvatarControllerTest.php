<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;

beforeEach(function (): void {
    Storage::fake('avatars');
});

test('update method updates avatar and redirects for authorized users', function (): void {
    // Arrange
    $user = User::factory()->create();
    $avatar = UploadedFile::fake()->image('avatar.jpg');

    // Mock the Gate facade to allow the action
    Gate::shouldReceive('authorize')
        ->once()
        ->with('update', $user)
        ->andReturn(true);

    // Act
    $response = $this->actingAs($user)
        ->post(route('avatar.update'), [
            'avatar' => $avatar,
        ]);

    // Assert
    $response->assertRedirect(route('profile.edit'));
    $response->assertSessionHas('status', 'avatar-updated');
});

test('update method returns 403 for unauthorized users', function (): void {
    // Arrange
    $user = User::factory()->create();
    $avatar = UploadedFile::fake()->image('avatar.jpg');

    // Mock the Gate facade to deny the action
    Gate::shouldReceive('authorize')
        ->once()
        ->with('update', $user)
        ->andThrow(new Illuminate\Auth\Access\AuthorizationException());

    // Act & Assert
    $this->actingAs($user)
        ->post(route('avatar.update'), [
            'avatar' => $avatar,
        ])
        ->assertStatus(403);
});

test('destroy method removes avatar and redirects for authorized users', function (): void {
    // Arrange
    $user = User::factory()->create();

    // Mock the Gate facade to allow the action
    Gate::shouldReceive('authorize')
        ->once()
        ->with('update', $user)
        ->andReturn(true);

    // Act
    $response = $this->actingAs($user)
        ->delete(route('avatar.destroy'));

    // Assert
    $response->assertRedirect(route('profile.edit'));
    $response->assertSessionHas('status', 'avatar-removed');
});

test('destroy method returns 403 for unauthorized users', function (): void {
    // Arrange
    $user = User::factory()->create();

    // Mock the Gate facade to deny the action
    Gate::shouldReceive('authorize')
        ->once()
        ->with('update', $user)
        ->andThrow(new Illuminate\Auth\Access\AuthorizationException());

    // Act & Assert
    $this->actingAs($user)
        ->delete(route('avatar.destroy'))
        ->assertStatus(403);
});
