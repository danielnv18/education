<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Admin\CreateUser;
use App\Actions\Admin\UpdateUser;
use App\Data\RoleData;
use App\Data\UserData;
use App\Http\Requests\Admin\StoreUserRequest;
use App\Http\Requests\Admin\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Spatie\Permission\Models\Role;

final readonly class UserController
{
    public function index(): Response
    {
        $users = User::query()->with('roles')->latest('updated_at')->get();

        return Inertia::render('admin/users/index', [
            'users' => UserData::collect($users),
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/users/create', [
            'availableRoles' => RoleData::collect(Role::all()),
        ]);
    }

    public function store(StoreUserRequest $request, CreateUser $action): RedirectResponse
    {
        $action->handle($request->validated());

        return to_route('admin.users.index');
    }

    public function edit(User $user): Response
    {
        return Inertia::render('admin/users/edit', [
            'user' => UserData::from($user->load('roles')),
            'availableRoles' => RoleData::collect(Role::all()),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUser $action): RedirectResponse
    {
        $action->handle($user, $request->validated());

        return to_route('admin.users.index');
    }

    public function destroy(User $user): RedirectResponse
    {
        $user->delete();

        return to_route('admin.users.index');
    }
}
