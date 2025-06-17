<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission as PermissionModel;
use Spatie\Permission\Models\Role;

final class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Create permissions
        foreach (PermissionEnum::cases() as $permission) {
            PermissionModel::create(['name' => $permission->value]);
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles();
    }

    private function assignPermissionsToRoles(): void
    {
        // Admin role - gets all permissions
        $adminRole = Role::findByName(UserRole::ADMIN->value);
        $adminRole->givePermissionTo(PermissionEnum::cases());

        // Teacher role
        $teacherRole = Role::findByName(UserRole::TEACHER->value);
        $teacherRole->givePermissionTo([
            PermissionEnum::VIEW_ANY_COURSES,
            PermissionEnum::VIEW_COURSE,
            PermissionEnum::CREATE_COURSE,
            PermissionEnum::UPDATE_COURSE,
            PermissionEnum::MANAGE_COURSE_CONTENT,
            PermissionEnum::VIEW_COURSE_CONTENT,
        ]);

        // Student role
        $studentRole = Role::findByName(UserRole::STUDENT->value);
        $studentRole->givePermissionTo([
            PermissionEnum::VIEW_COURSE,
            PermissionEnum::VIEW_COURSE_CONTENT,
        ]);
    }
}
