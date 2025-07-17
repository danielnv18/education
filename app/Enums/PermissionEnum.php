<?php

declare(strict_types=1);

namespace App\Enums;

enum PermissionEnum: string
{
    // Course permissions
    case VIEW_ANY_COURSES = 'view any courses';
    case VIEW_COURSE = 'view course';
    case CREATE_COURSE = 'create course';
    case UPDATE_COURSE = 'update course';
    case DELETE_COURSE = 'delete course';
    case RESTORE_COURSE = 'restore course';
    case FORCE_DELETE_COURSE = 'force delete course';
    case MANAGE_COURSE_CONTENT = 'manage course content';
    case VIEW_COURSE_CONTENT = 'view course content';

    // User permissions
    case VIEW_ANY_USERS = 'view any users';
    case VIEW_USER = 'view user';
    case CREATE_USER = 'create user';
    case UPDATE_USER = 'update user';
    case DELETE_USER = 'delete user';
    case MANAGE_USERS = 'manage users';
}
