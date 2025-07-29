<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission as PermissionModel;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        app()[PermissionRegistrar::class]->forgetCachedPermissions();

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

        app()[PermissionRegistrar::class]->forgetCachedPermissions();
    }

    /**
     * @param  array<PermissionModel>  $permissionModels
     */
    private function assignPermissionsToRoles(array $permissionModels): void
    {
        // Admin role - gets all permissions
        $adminRole = Role::findByName(UserRole::Admin->value);
        $adminRole->givePermissionTo($permissionModels);

        // Teacher role
        $teacherRole = Role::findByName(UserRole::Teacher->value);
        $teacherPermissions = PermissionModel::whereIn('name', [
            PermissionEnum::ViewCourse->value,
            PermissionEnum::CreateCourse->value,
            PermissionEnum::UpdateCourse->value,
            PermissionEnum::ManageCourseContent->value,
            PermissionEnum::ViewCourseContent->value,
        ])->get();
        $teacherRole->givePermissionTo($teacherPermissions);

        // Student role
        $studentRole = Role::findByName(UserRole::Student->value);
        $studentPermissions = PermissionModel::whereIn('name', [
            PermissionEnum::ViewCourse->value,
            PermissionEnum::ViewCourseContent->value,
        ])->get();
        $studentRole->givePermissionTo($studentPermissions);
    }
}
