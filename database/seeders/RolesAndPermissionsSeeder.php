<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\UserRole;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Seed roles and permissions.
     */
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        $guard = 'web';

        // Create all permissions from the enum
        foreach (PermissionEnum::cases() as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission->value, 'guard_name' => $guard]
            );
        }

        // Create roles
        $admin = Role::firstOrCreate(['name' => UserRole::Admin->value, 'guard_name' => $guard]);
        $teacher = Role::firstOrCreate(['name' => UserRole::Teacher->value, 'guard_name' => $guard]);
        $student = Role::firstOrCreate(['name' => UserRole::Student->value, 'guard_name' => $guard]);

        // Assign permissions to roles
        // Admin: everything
        $admin->syncPermissions(Permission::all());

        // Teacher: course management + viewing
        $teacherPermissions = [
            PermissionEnum::ViewCourse->value,
            PermissionEnum::UpdateCourse->value,
            PermissionEnum::ManageCourseContent->value,
            PermissionEnum::ViewCourseContent->value,
        ];
        $teacher->syncPermissions($teacherPermissions);

        // Student: view-only permissions
        $studentPermissions = [
            PermissionEnum::ViewAnyCourses->value,
            PermissionEnum::ViewCourse->value,
            PermissionEnum::ViewCourseContent->value,
        ];
        $student->syncPermissions($studentPermissions);
    }
}
