<?php

declare(strict_types=1);

namespace App\Enums;

enum EnrollmentStatus: string
{
    case INVITED = 'invited';
    case ACTIVE = 'active';
    case DROPPED = 'dropped';
}
