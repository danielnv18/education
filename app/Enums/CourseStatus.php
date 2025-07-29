<?php

declare(strict_types=1);

namespace App\Enums;

enum CourseStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Archived = 'archived';
}
