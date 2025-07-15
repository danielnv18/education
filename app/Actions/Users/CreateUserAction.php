<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Data\UserData;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final class CreateUserAction
{
    /**
     * Create a new user with the provided data.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data): UserData
    {
        return DB::transaction(function () use ($data): UserData {
            $userData = [
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make($data['password'] ?? Str::password()),
            ];

            if (isset($data['email_verified']) && $data['email_verified']) {
                $userData['email_verified_at'] = now();
            }

            $user = User::query()->create($userData);

            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            $user->refresh();
            $user->load('roles');

            return UserData::from($user);
        });
    }
}
