<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Global role names managed via spatie/laravel-permission.
 */
enum RoleEnum: string
{
    case Admin = 'admin';
    case ContentManager = 'content_manager';
    case Teacher = 'teacher';
}
