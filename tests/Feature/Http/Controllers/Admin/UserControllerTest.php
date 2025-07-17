<?php

declare(strict_types=1);

use App\Enums\UserRole;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;
use Illuminate\Support\Facades\Hash;
use Inertia\Testing\AssertableInertia;

beforeEach(function (): void {
    new DatabaseSeeder()->run();
});

test('index method returns users list for authorized users', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);

    User::factory()->count(3)->create();

    // Act
    $response = $this->actingAs($admin)->get(route('admin.users.index'));

    // Assert
    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page->component('admin/users/index')
        ->has('users')
    );
});

test('create method returns form for authorized users', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);

    // Act
    $response = $this->actingAs($admin)->get(route('admin.users.create'));

    // Assert
    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page->component('admin/users/create'));
});

test('store method creates a user and redirects for authorized users', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);

    $userData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'roles' => [UserRole::STUDENT->value],
    ];

    // Act
    $response = $this->actingAs($admin)
        ->post(route('admin.users.store'), $userData);

    // Assert
    $user = User::query()->where('email', 'test@example.com')->first();
    $this->assertNotNull($user);
    $this->assertEquals($userData['name'], $user->name);
    $this->assertTrue($user->hasRole(UserRole::STUDENT));

    // Since the controller returns a UserData object, we can't directly assert the redirect
    // Instead, we'll check that the response is a redirect and has the success message
    $response->assertStatus(302);
    $response->assertSessionHas('success', 'User created successfully.');
});

test('show method displays user details for authorized users', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);

    $user = User::factory()->create();
    $user->assignRole(UserRole::STUDENT);

    // Act
    $response = $this->actingAs($admin)->get(route('admin.users.show', $user));

    // Assert
    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page->component('admin/users/show')
        ->has('user')
    );
});

test('edit method returns form with user data for authorized users', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);

    $user = User::factory()->create();
    $user->assignRole(UserRole::STUDENT);

    // Act
    $response = $this->actingAs($admin)->get(route('admin.users.edit', $user));

    // Assert
    $response->assertStatus(200);
    $response->assertInertia(fn (AssertableInertia $page) => $page->component('admin/users/edit')
        ->has('user')
    );
});

test('update method updates a user and redirects for authorized users', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);

    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);
    $user->assignRole(UserRole::STUDENT);

    $updateData = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
        'roles' => [UserRole::TEACHER->value],
        'email_verified' => true,
    ];

    // Act
    $response = $this->actingAs($admin)
        ->put(route('admin.users.update', $user), $updateData);

    // Assert
    $response->assertRedirect(route('admin.users.show', $user));
    $response->assertSessionHas('success', 'User updated successfully.');

    // Verify the user was updated in the database
    $user->refresh();
    $this->assertEquals('Updated Name', $user->name);
    $this->assertEquals('updated@example.com', $user->email);
    $this->assertTrue(Hash::check('newpassword123', $user->password));
    $this->assertNotNull($user->email_verified_at);
    $this->assertTrue($user->hasRole(UserRole::TEACHER));
    $this->assertFalse($user->hasRole(UserRole::STUDENT));
});

test('update method works without changing password', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);

    $user = User::factory()->create([
        'name' => 'Original Name',
        'email' => 'original@example.com',
    ]);
    $originalPassword = $user->password;
    $user->assignRole(UserRole::STUDENT);

    $updateData = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'password' => '',
        'password_confirmation' => '',
        'roles' => [UserRole::STUDENT->value],
    ];

    // Act
    $response = $this->actingAs($admin)
        ->put(route('admin.users.update', $user), $updateData);

    // Assert
    $response->assertRedirect(route('admin.users.show', $user));

    // Verify the user was updated but password remains unchanged
    $user->refresh();
    $this->assertEquals('Updated Name', $user->name);
    $this->assertEquals('updated@example.com', $user->email);
    $this->assertEquals($originalPassword, $user->password);
});

test('destroy method deletes a user and redirects for authorized users', function (): void {
    // Arrange
    $admin = User::factory()->create();
    $admin->assignRole(UserRole::ADMIN);

    $user = User::factory()->create();
    $userId = $user->id;

    // Act
    $response = $this->actingAs($admin)
        ->delete(route('admin.users.destroy', $user));

    // Assert
    $this->assertNull(User::query()->find($userId));
    $response->assertRedirect(route('admin.users.index'));
    $response->assertSessionHas('success', 'User deleted successfully.');
});

test('unauthorized users cannot access user endpoints', function (): void {
    // Arrange
    $regularUser = User::factory()->create();
    $regularUser->assignRole(UserRole::STUDENT);

    $targetUser = User::factory()->create();

    // Act & Assert - Test index, create, and store endpoints
    $this->actingAs($regularUser)->get(route('admin.users.index'))->assertStatus(403);
    $this->actingAs($regularUser)->get(route('admin.users.create'))->assertStatus(403);

    // For store, we need to provide valid data to pass validation
    $storeData = [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password123',
        'password_confirmation' => 'password123',
        'roles' => [UserRole::STUDENT->value],
    ];
    $this->actingAs($regularUser)->post(route('admin.users.store'), $storeData)->assertStatus(403);

    // Test show, edit, update, and destroy endpoints with a specific user
    $this->actingAs($regularUser)->get(route('admin.users.show', $targetUser))->assertStatus(403);
    $this->actingAs($regularUser)->get(route('admin.users.edit', $targetUser))->assertStatus(403);

    // For update, we need to provide valid data
    $updateData = [
        'name' => 'Updated Name',
        'email' => 'updated@example.com',
        'password' => 'newpassword123',
        'password_confirmation' => 'newpassword123',
        'roles' => [UserRole::TEACHER->value],
    ];
    $this->actingAs($regularUser)->put(route('admin.users.update', $targetUser), $updateData)->assertStatus(403);

    $this->actingAs($regularUser)->delete(route('admin.users.destroy', $targetUser))->assertStatus(403);
});
