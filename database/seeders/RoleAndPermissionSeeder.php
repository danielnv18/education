<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission as PermissionModel;
use Spatie\Permission\Models\Role;

final class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Insert roles into the database
        foreach (UserRole::cases() as $role) {
            Role::create(['name' => $role->value]);
        }

        // Create permissions
        $permissionModels = [];
        foreach (PermissionEnum::cases() as $permission) {
            $permissionModels[] = PermissionModel::create(['name' => $permission->value]);
        }

        // Assign permissions to roles
        $this->assignPermissionsToRoles($permissionModels);
    }

    /**
     * @param  array<PermissionModel>  $permissionModels
     */
    private function assignPermissionsToRoles(array $permissionModels): void
    {
        // Admin role - gets all permissions
        $adminRole = Role::findByName(UserRole::ADMIN->value);
        $adminRole->givePermissionTo($permissionModels);

        // Teacher role
        $teacherRole = Role::findByName(UserRole::TEACHER->value);
        $teacherPermissions = PermissionModel::whereIn('name', [
            PermissionEnum::VIEW_COURSE->value,
            PermissionEnum::CREATE_COURSE->value,
            PermissionEnum::UPDATE_COURSE->value,
            PermissionEnum::MANAGE_COURSE_CONTENT->value,
            PermissionEnum::VIEW_COURSE_CONTENT->value,
        ])->get();
        $teacherRole->givePermissionTo($teacherPermissions);

        // Student role
        $studentRole = Role::findByName(UserRole::STUDENT->value);
        $studentPermissions = PermissionModel::whereIn('name', [
            PermissionEnum::VIEW_COURSE->value,
            PermissionEnum::VIEW_COURSE_CONTENT->value,
        ])->get();
        $studentRole->givePermissionTo($studentPermissions);
    }
}
