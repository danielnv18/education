<?php

declare(strict_types=1);

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;

it('renders the admin user index page', function (): void {
    $admin = User::factory()->asAdmin()->create();

    User::factory()->create();

    $response = $this->actingAs($admin)
        ->fromRoute('dashboard')
        ->get(route('admin.users.index'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/users/index')
            ->has('users'));
});

it('forbids non-admin users from admin routes', function (): void {
    $user = User::factory()->asTeacher()->create();

    $response = $this->actingAs($user)
        ->fromRoute('dashboard')
        ->get(route('admin.users.index'));

    $response->assertForbidden();
});

it('renders the admin user create page', function (): void {
    $admin = User::factory()->asAdmin()->create();

    $response = $this->actingAs($admin)
        ->fromRoute('admin.users.index')
        ->get(route('admin.users.create'));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/users/create')
            ->has('availableRoles'));
});

it('stores a new user from the admin panel', function (): void {
    Event::fake([Registered::class]);

    $admin = User::factory()->asAdmin()->create();
    $role = Role::query()->where('name', RoleEnum::Teacher->value)->firstOrFail();

    $response = $this->actingAs($admin)
        ->fromRoute('admin.users.create')
        ->post(route('admin.users.store'), [
            'name' => 'New User',
            'email' => 'new.user@example.com',
            'roles' => [$role->name],
            'avatar' => 'https://example.com/avatar.png',
        ]);

    $response->assertRedirectToRoute('admin.users.index');

    $user = User::query()->where('email', 'new.user@example.com')->first();

    expect($user)->not->toBeNull()
        ->and($user->name)->toBe('New User')
        ->and($user->roles->pluck('name')->all())->toBe([$role->name]);

    Event::assertDispatched(Registered::class);
});

it('requires a name when storing a user', function (): void {
    $admin = User::factory()->asAdmin()->create();

    $response = $this->actingAs($admin)
        ->fromRoute('admin.users.create')
        ->post(route('admin.users.store'), [
            'email' => 'new.user@example.com',
        ]);

    $response->assertRedirectToRoute('admin.users.create')
        ->assertSessionHasErrors('name');
});

it('requires a valid role when storing a user', function (): void {
    $admin = User::factory()->asAdmin()->create();

    $response = $this->actingAs($admin)
        ->fromRoute('admin.users.create')
        ->post(route('admin.users.store'), [
            'name' => 'New User',
            'email' => 'new.user@example.com',
            'roles' => ['missing-role'],
        ]);

    $response->assertRedirectToRoute('admin.users.create')
        ->assertSessionHasErrors('roles.0');
});

it('renders the admin user edit page', function (): void {
    $admin = User::factory()->asAdmin()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($admin)
        ->fromRoute('admin.users.index')
        ->get(route('admin.users.edit', $user));

    $response->assertOk()
        ->assertInertia(fn ($page) => $page
            ->component('admin/users/edit')
            ->has('user')
            ->has('availableRoles'));
});

it('updates a user from the admin panel', function (): void {
    $admin = User::factory()->asAdmin()->create();
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old.user@example.com',
    ]);
    $role = Role::query()->where('name', RoleEnum::Teacher->value)->firstOrFail();

    $response = $this->actingAs($admin)
        ->fromRoute('admin.users.edit', $user)
        ->put(route('admin.users.update', $user), [
            'name' => 'Updated Name',
            'email' => 'updated.user@example.com',
            'roles' => [$role->name],
            'avatar' => 'https://example.com/avatar.png',
        ]);

    $response->assertRedirectToRoute('admin.users.index');

    expect($user->refresh()->name)->toBe('Updated Name')
        ->and($user->email)->toBe('updated.user@example.com')
        ->and($user->roles->pluck('name')->all())->toBe([$role->name]);
});

it('requires a unique email when updating a user', function (): void {
    $admin = User::factory()->asAdmin()->create();
    $existingUser = User::factory()->create(['email' => 'existing@example.com']);
    $user = User::factory()->create(['email' => 'old.user@example.com']);

    $response = $this->actingAs($admin)
        ->fromRoute('admin.users.edit', $user)
        ->put(route('admin.users.update', $user), [
            'name' => 'Updated Name',
            'email' => $existingUser->email,
        ]);

    $response->assertRedirectToRoute('admin.users.edit', $user)
        ->assertSessionHasErrors('email');
});

it('deletes a user from the admin panel', function (): void {
    $admin = User::factory()->asAdmin()->create();
    $user = User::factory()->create();

    $response = $this->actingAs($admin)
        ->fromRoute('admin.users.index')
        ->delete(route('admin.users.destroy', $user));

    $response->assertRedirectToRoute('admin.users.index');

    $this->assertSoftDeleted($user);
});
