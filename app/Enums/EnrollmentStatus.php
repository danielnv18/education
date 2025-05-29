<?php

declare(strict_types=1);

namespace App\Enums;

enum EnrollmentStatus: string
{
    case ACTIVE = 'active';
    case COMPLETED = 'completed';
    case DROPPED = 'dropped';
}
