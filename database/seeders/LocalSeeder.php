<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\User;
use Illuminate\Database\Seeder;

final class LocalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@lms.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ])->assignRole(UserRole::ADMIN);

        User::factory()->count(5)->create([
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ])->each(function (User $user): void {
            $user->assignRole(UserRole::ADMIN, UserRole::TEACHER);
        });

        // Teachers
        User::factory()->count(5)->create([
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ])->each(function (User $user): void {
            $user->assignRole(UserRole::TEACHER);
        });

        // Students
        User::factory()->count(80)->create([
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ]);
    }
}
