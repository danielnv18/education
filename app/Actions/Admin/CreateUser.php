<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

final readonly class CreateUser
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data): User
    {
        return DB::transaction(function () use ($data) {
            $user = User::query()->create([
                'name' => $data['name'],
                'email' => $data['email'],
                'password' => Hash::make(Str::password()),
            ]);

            if (isset($data['roles'])) {
                $user->assignRole($data['roles']);
            }

            event(new Registered($user));

            return $user;
        });
    }
}
