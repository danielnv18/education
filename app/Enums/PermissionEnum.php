<?php

declare(strict_types=1);

namespace App\Enums;

enum PermissionEnum: string
{
    // Course permissions
    case ViewAnyCourses = 'view any courses';
    case ViewCourse = 'view course';
    case CreateCourse = 'create course';
    case UpdateCourse = 'update course';
    case DeleteCourse = 'delete course';
    case RestoreCourse = 'restore course';
    case ForceDeleteCourse = 'force delete course';
    case ManageCourseContent = 'manage course content';
    case ViewCourseContent = 'view course content';

    // User permissions
    case ViewAnyUsers = 'view any users';
    case ViewUser = 'view user';
    case CreateUser = 'create user';
    case UpdateUser = 'update user';
    case DeleteUser = 'delete user';
    case ManageUsers = 'manage users';
}
