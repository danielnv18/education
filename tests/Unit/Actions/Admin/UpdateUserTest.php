<?php

declare(strict_types=1);

use App\Actions\Admin\UpdateUser;
use App\Enums\RoleEnum;
use App\Models\User;
use Spatie\Permission\Models\Role;

it('updates user details and roles', function (): void {
    $role = Role::query()->where('name', RoleEnum::Teacher->value)->firstOrFail();

    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    $action = resolve(UpdateUser::class);

    $action->handle($user, [
        'name' => 'New Name',
        'email' => 'new@example.com',
        'roles' => [$role->name],
    ]);

    expect($user->refresh()->name)->toBe('New Name')
        ->and($user->email)->toBe('new@example.com')
        ->and($user->roles->pluck('name')->all())->toBe([$role->name]);
});

it('updates user without changing roles', function (): void {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@example.com',
    ]);

    $action = resolve(UpdateUser::class);

    $action->handle($user, [
        'name' => 'New Name',
        'email' => 'new@example.com',
    ]);

    expect($user->refresh()->name)->toBe('New Name')
        ->and($user->email)->toBe('new@example.com')
        ->and($user->roles)->toHaveCount(0);
});
