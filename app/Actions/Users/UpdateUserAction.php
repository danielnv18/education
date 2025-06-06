<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

final class UpdateUserAction
{
    public function handle(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
            ];

            if (isset($data['password']) && $data['password']) {
                $userData['password'] = Hash::make($data['password']);
            }

            if (isset($data['email_verified'])) {
                if ($data['email_verified']) {
                    $userData['email_verified_at'] = $user->email_verified_at ?? now();
                } else {
                    $userData['email_verified_at'] = null;
                }
            }

            $user->update($userData);

            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            return $user;
        });
    }
}
