<?php

declare(strict_types=1);

use App\Actions\Admin\CreateUser;
use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Event;
use Spatie\Permission\Models\Role;

it('dispatches registered event when storing user', function (): void {
    Event::fake([Registered::class]);

    $action = resolve(CreateUser::class);

    $user = $action->handle([
        'name' => 'Admin User',
        'email' => 'admin@example.com',
    ]);

    expect($user)
        ->toBeInstanceOf(User::class)
        ->and($user->name)->toBe('Admin User')
        ->and($user->email)->toBe('admin@example.com')
        ->and($user->password)->not->toBeNull();

    Event::assertDispatched(fn (Registered $event): bool => $event->user->is($user));
});

it('assigns roles when provided', function (): void {
    Event::fake([Registered::class]);

    $role = Role::query()->where('name', RoleEnum::Teacher->value)->firstOrFail();

    $action = resolve(CreateUser::class);

    $user = $action->handle([
        'name' => 'Role User',
        'email' => 'role.user@example.com',
        'roles' => [$role->name],
    ]);

    expect($user->roles->pluck('name')->all())->toBe([$role->name]);

    Event::assertDispatched(Registered::class);
});
