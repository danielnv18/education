<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Users\CreateUserAction;
use App\Actions\Users\DeleteUserAction;
use App\Actions\Users\UpdateUserAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\UserResource;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

final class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::with(['roles'])->get();

        return Inertia::render('admin/users/index', [
            'users' => UserResource::collection($users),
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
        $user->load('roles');

        return Inertia::render('admin/users/show', [
            'user' => new UserResource($user),
        ]);
    }

    public function edit(User $user): Response
    {
        $user->load('roles');

        return Inertia::render('admin/users/edit', [
            'user' => new UserResource($user),
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
}
