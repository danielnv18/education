<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Data\UserData;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final readonly class UpdateUserAction
{
    /**
     * Update an existing user with the provided data.
     *
     * @param  User  $user  The user to update
     * @param  array<string, mixed>  $data  The data to update the user with
     */
    public function handle(User $user, array $data): UserData
    {
        return DB::transaction(function () use ($user, $data): UserData {
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            if (isset($data['password']) && $data['password']) {
                $userData['password'] = Hash::make($data['password']);
            }

            if (isset($data['email_verified'])) {
                $userData['email_verified_at'] = $data['email_verified'] ? $user->email_verified_at ?? now() : null;
            }

            $user->update($userData);

            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            $user->refresh();

            return UserData::from($user);
        });
    }
}
