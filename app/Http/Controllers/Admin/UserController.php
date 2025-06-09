<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Users\CreateUserAction;
use App\Actions\Users\DeleteUserAction;
use App\Actions\Users\SendPasswordResetLinkAction;
use App\Actions\Users\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::query()
            ->with('roles')
            ->latest()
            ->paginate(10);

        return Inertia::render('admin/users/index', [
            'users' => $users,
        ]);
    }

    public function create(): Response
    {
        return Inertia::render('admin/users/create');
    }

    public function store(CreateUserRequest $request, CreateUserAction $action): RedirectResponse
    {
        $user = $action->handle($request->validated());

        return to_route('admin.users.show', $user)
            ->with('success', 'User created successfully.');
    }

    public function show(User $user): Response
    {
        return Inertia::render('admin/users/show', [
            'user' => $user->load('roles'),
        ]);
    }

    public function edit(User $user): Response
    {
        return Inertia::render('admin/users/edit', [
            'user' => $user->load('roles'),
        ]);
    }

    public function update(UpdateUserRequest $request, User $user, UpdateUserAction $action): RedirectResponse
    {
        $action->handle($user, $request->validated());

        return to_route('admin.users.show', $user)
            ->with('success', 'User updated successfully.');
    }

    public function destroy(User $user, DeleteUserAction $action): RedirectResponse
    {
        $action->handle($user);

        return to_route('admin.users.index')
            ->with('success', 'User deleted successfully.');
    }

    public function sendPasswordResetLink(User $user, SendPasswordResetLinkAction $action): RedirectResponse
    {
        $action->handle($user);

        return back()->with('success', 'Password reset link sent successfully.');
    }
}
