<?php

declare(strict_types=1);

namespace Database\Seeders;

use App\Enums\PermissionEnum;
use App\Enums\RoleEnum;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Spatie\Permission\PermissionRegistrar;

final class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        resolve(PermissionRegistrar::class)->forgetCachedPermissions();

        DB::transaction(function (): void {
            $permissions = array_map(static fn (PermissionEnum $case): string => $case->value, PermissionEnum::cases());

            foreach ($permissions as $permission) {
                Permission::findOrCreate($permission, 'web');
            }

            $rolePermissions = [
                RoleEnum::Admin->value => $permissions,
                RoleEnum::ContentManager->value => [
                    PermissionEnum::ResendInvitations->value,
                    PermissionEnum::RevokeInvitations->value,
                    PermissionEnum::AssignCourseStaff->value,
                    PermissionEnum::ManageEnrollments->value,
                    PermissionEnum::ViewUserDirectory->value,
                    PermissionEnum::ManageCourses->value,
                    PermissionEnum::PublishCourses->value,
                    PermissionEnum::ManageCourseMetadata->value,
                    PermissionEnum::AccessInactiveCourses->value,
                    PermissionEnum::ManageModules->value,
                    PermissionEnum::ManageLessons->value,
                    PermissionEnum::ManageAssignments->value,
                    PermissionEnum::GradeAssignments->value,
                    PermissionEnum::ManageExams->value,
                    PermissionEnum::GradeExams->value,
                    PermissionEnum::CreateAttendanceSessions->value,
                    PermissionEnum::RecordAttendance->value,
                    PermissionEnum::ViewAttendanceAnalytics->value,
                    PermissionEnum::ViewAssessmentAnalytics->value,
                    PermissionEnum::ManagePlatformSettings->value,
                    PermissionEnum::ManageNotificationChannels->value,
                    PermissionEnum::ManageQueues->value,
                    PermissionEnum::ViewDashboards->value,
                ],
                RoleEnum::Teacher->value => [
                    PermissionEnum::ResendInvitations->value,
                    PermissionEnum::RevokeInvitations->value,
                    PermissionEnum::AssignCourseStaff->value,
                    PermissionEnum::ManageEnrollments->value,
                    PermissionEnum::ViewUserDirectory->value,
                    PermissionEnum::ManageCourses->value,
                    PermissionEnum::PublishCourses->value,
                    PermissionEnum::ManageCourseMetadata->value,
                    PermissionEnum::AccessInactiveCourses->value,
                    PermissionEnum::ManageModules->value,
                    PermissionEnum::ManageLessons->value,
                    PermissionEnum::ManageAssignments->value,
                    PermissionEnum::GradeAssignments->value,
                    PermissionEnum::ManageExams->value,
                    PermissionEnum::GradeExams->value,
                    PermissionEnum::CreateAttendanceSessions->value,
                    PermissionEnum::RecordAttendance->value,
                    PermissionEnum::ViewAttendanceAnalytics->value,
                    PermissionEnum::ViewAssessmentAnalytics->value,
                    PermissionEnum::ViewDashboards->value,
                ],
            ];

            foreach ($rolePermissions as $role => $permissions) {
                Role::findOrCreate($role, 'web')->syncPermissions($permissions);
            }
        });
    }
}
