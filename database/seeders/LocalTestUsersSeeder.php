<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

final class LocalTestUsersSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment(['local'])) {
            return;
        }

        DB::transaction(function (): void {
            // 5 Admins
            User::factory()->count(5)->asAdmin()->create();

            // 5 ContentManagers
            User::factory()->count(5)->asContentManager()->create();

            // 20 Teachers
            User::factory()->count(20)->asTeacher()->create();

            // 70 Students (regular users without any special role)
            User::factory()->count(70)->create();
        });
    }
}
