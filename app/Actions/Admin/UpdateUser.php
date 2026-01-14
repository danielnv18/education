<?php

declare(strict_types=1);

namespace App\Actions\Admin;

use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class UpdateUser
{
    /**
     * @param  array<string, mixed>  $data
     */
    public function handle(User $user, array $data): User
    {
        return DB::transaction(function () use ($user, $data): User {
            $user->update([
                'name' => $data['name'],
                'email' => $data['email'],
            ]);

            if (isset($data['roles'])) {
                $user->syncRoles($data['roles']);
            }

            return $user;
        });
    }
}
