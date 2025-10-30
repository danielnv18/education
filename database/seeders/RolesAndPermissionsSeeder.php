<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\SystemPermission;
use App\Enums\SystemRole;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        app(PermissionRegistrar::class)->forgetCachedPermissions();

        DB::transaction(function (): void {
            foreach (SystemPermission::values() as $permission) {
                Permission::findOrCreate($permission, 'web');
            }

            $rolePermissions = [
                SystemRole::Admin->value => SystemPermission::values(),
                SystemRole::ContentManager->value => [
                    SystemPermission::CreateCourses->value,
                    SystemPermission::UpdateCourses->value,
                    SystemPermission::DeleteCourses->value,
                    SystemPermission::AssignCourseTeachers->value,
                    SystemPermission::AssignCourseAssistants->value,
                    SystemPermission::ManageEnrollments->value,
                    SystemPermission::PublishContent->value,
                    SystemPermission::RecordAttendance->value,
                    SystemPermission::ManageExams->value,
                ],
                SystemRole::Teacher->value => [
                    SystemPermission::UpdateCourses->value,
                    SystemPermission::ManageEnrollments->value,
                    SystemPermission::PublishContent->value,
                    SystemPermission::RecordAttendance->value,
                    SystemPermission::ManageExams->value,
                ],
            ];

            foreach ($rolePermissions as $role => $permissions) {
                $roleModel = Role::findOrCreate($role, 'web');

                $roleModel->syncPermissions($permissions);
            }
        });
    }
}
