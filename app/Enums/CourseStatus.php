<?php

declare(strict_types=1);

namespace App\Enums;

enum CourseStatus: string
{
    case DRAFT = 'draft';
    case ACTIVE = 'active';
    case ARCHIVED = 'archived';
}
