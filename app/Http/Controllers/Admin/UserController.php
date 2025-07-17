<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Users\CreateUserAction;
use App\Actions\Users\DeleteUserAction;
use App\Actions\Users\UpdateUserAction;
use App\Data\UserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

final class UserController extends Controller
{
    public function index(): Response
    {
        Gate::authorize('manageUsers', Auth::user());

        $users = UserData::collect(User::with(['roles'])->get());

        return Inertia::render('admin/users/index', [
            'users' => $users,
        ]);
    }

    public function create(): Response
    {
        Gate::authorize('manageUsers', Auth::user());

        return Inertia::render('admin/users/create');
    }

    public function store(CreateUserRequest $request, CreateUserAction $action): RedirectResponse
    {
        Gate::authorize('manageUsers', Auth::user());

        $user = $action->handle($request->validated());

        return to_route('admin.users.show', $user->id)
            ->with('success', 'User created successfully.');
    }

    public function show(User $user): Response
    {
        Gate::authorize('manageUsers', Auth::user());

        $user->load('roles');

        return Inertia::render('admin/users/show', [
            'user' => UserData::from($user),
        ]);
    }

    public function edit(User $user): Response
    {
        Gate::authorize('manageUsers', Auth::user());

        $user->load('roles');

        return Inertia::render('admin/users/edit', [
            'user' => UserData::from($user),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action): RedirectResponse
    {
        Gate::authorize('manageUsers', Auth::user());

        $action->handle($user, $request->validated());

        return to_route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user, DeleteUserAction $action): RedirectResponse
    {
        Gate::authorize('manageUsers', Auth::user());

        $action->handle($user);

        return to_route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }
}
