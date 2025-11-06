<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\RoleEnum;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class LocalRoleUsersSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local'])) {
            return;
        }

        DB::transaction(function (): void {
            foreach (RoleEnum::cases() as $role) {
                $user = User::query()->updateOrCreate(
                    ['email' => sprintf('%s@lms.com', $role->value)],
                    [
                        'name' => $role->value,
                        'password' => 'password1',
                        'email_verified_at' => now(),
                    ],
                );

                $user->syncRoles([$role->value]);
            }
        });
    }
}
