<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\User;

final class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::VIEW_ANY_USERS);
    }

    public function view(User $user, User $model): bool
    {
        // Users can always view their own profile
        if ($user->id === $model->id) {
            return true;
        }

        return $user->hasPermissionTo(PermissionEnum::VIEW_USER);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::CREATE_USER);
    }

    public function update(User $user, User $model): bool
    {
        // Users can always update their own profile
        if ($user->id === $model->id) {
            return true;
        }

        return $user->hasPermissionTo(PermissionEnum::UPDATE_USER);
    }

    public function delete(User $user, User $model): bool
    {
        // Prevent users from deleting themselves
        if ($user->id === $model->id) {
            return false;
        }

        return $user->hasPermissionTo(PermissionEnum::DELETE_USER);
    }

    public function restore(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::DELETE_USER);
    }

    public function forceDelete(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::DELETE_USER);
    }
}
