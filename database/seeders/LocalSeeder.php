<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Seeder;

final class LocalSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $this->localUsers();
        $this->localCourse();
    }

    private function localUsers(): void
    {
        User::factory()->create([
            'name' => 'Admin User',
            'email' => 'admin@lms.com',
            'password' => bcrypt('password'),
            'email_verified_at' => now(),
        ])->assignRole(UserRole::Admin);

        User::factory()->count(5)->create([
            'email_verified_at' => now(),
        ])->each(function (User $user): void {
            $user->assignRole(UserRole::Admin, UserRole::Teacher);
        });

        // Teachers
        User::factory()->count(5)->create([
            'email_verified_at' => now(),
        ])->each(function (User $user): void {
            $user->assignRole(UserRole::Teacher);
        });

        // Students
        User::factory()->count(30)->create([
            'email_verified_at' => now(),
        ]);
    }

    private function localCourse(): void
    {
        Course::factory()->withModulesAndLessons()->create();
    }
}
