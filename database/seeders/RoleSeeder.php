<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

final class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Define roles
        $roles = [
            ['name' => UserRole::ADMIN],
            ['name' => UserRole::TEACHER],
        ];

        // Insert roles into the database
        foreach ($roles as $role) {
            Role::create($role);
        }
    }
}
