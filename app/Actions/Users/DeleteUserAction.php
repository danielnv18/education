<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;

final class DeleteUserAction
{
    /**
     * Delete a user from the system.
     *
     * @param  User  $user  The user to delete
     */
    public function handle(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $user->delete();
        });
    }
}
