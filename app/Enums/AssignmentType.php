<?php

declare(strict_types=1);

namespace App\Enums;

enum AssignmentType: string
{
    case Essay = 'essay';
    case Upload = 'upload';
    case Quiz = 'quiz';
    case Project = 'project';
}
