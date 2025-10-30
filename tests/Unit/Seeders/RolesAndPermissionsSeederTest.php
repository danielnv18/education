<?php

declare(strict_types=1);

use App\Enums\SystemPermission;
use App\Enums\SystemRole;
use Database\Seeders\RolesAndPermissionsSeeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

use function Pest\Laravel\seed;

it('seeds all permissions and roles with expected assignments', function (): void {
    seed(RolesAndPermissionsSeeder::class);

    $permissions = Permission::query()
        ->whereIn('name', SystemPermission::values())
        ->orderBy('name')
        ->pluck('name')
        ->all();

    expect($permissions)
        ->toHaveCount(count(SystemPermission::cases()))
        ->toEqualCanonicalizing(SystemPermission::values());

    $roles = Role::query()
        ->whereIn('name', SystemRole::values())
        ->orderBy('name')
        ->pluck('name')
        ->all();

    expect($roles)
        ->toHaveCount(count(SystemRole::cases()))
        ->toEqualCanonicalizing(SystemRole::values());

    $admin = Role::findByName(SystemRole::Admin->value);
    $contentManager = Role::findByName(SystemRole::ContentManager->value);
    $teacher = Role::findByName(SystemRole::Teacher->value);

    expect($admin->permissions->pluck('name')->all())
        ->toEqualCanonicalizing(SystemPermission::values());

    expect($contentManager->permissions->pluck('name')->all())->toEqualCanonicalizing([
        SystemPermission::CreateCourses->value,
        SystemPermission::UpdateCourses->value,
        SystemPermission::DeleteCourses->value,
        SystemPermission::AssignCourseTeachers->value,
        SystemPermission::AssignCourseAssistants->value,
        SystemPermission::ManageEnrollments->value,
        SystemPermission::PublishContent->value,
        SystemPermission::RecordAttendance->value,
        SystemPermission::ManageExams->value,
    ]);

    expect($teacher->permissions->pluck('name')->all())->toEqualCanonicalizing([
        SystemPermission::UpdateCourses->value,
        SystemPermission::ManageEnrollments->value,
        SystemPermission::PublishContent->value,
        SystemPermission::RecordAttendance->value,
        SystemPermission::ManageExams->value,
    ]);
});
