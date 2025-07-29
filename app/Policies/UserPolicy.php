<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\User;

final class UserPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::ViewAnyUsers);
    }

    public function view(User $user, User $model): bool
    {
        // Users can always view their own profile
        if ($user->id === $model->id) {
            return true;
        }

        return $user->hasPermissionTo(PermissionEnum::ViewUser);
    }

    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::CreateUser);
    }

    public function update(User $user, User $model): bool
    {
        // Users can always update their own profile
        if ($user->id === $model->id) {
            return true;
        }

        return $user->hasPermissionTo(PermissionEnum::UpdateUser);
    }

    public function delete(User $user, User $model): bool
    {
        // Prevent users from deleting themselves
        if ($user->id === $model->id) {
            return false;
        }

        return $user->hasPermissionTo(PermissionEnum::DeleteUser);
    }

    public function restore(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::DeleteUser);
    }

    public function forceDelete(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::DeleteUser);
    }

    public function manageUsers(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::ManageUsers);
    }
}
