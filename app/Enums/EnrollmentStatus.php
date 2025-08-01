<?php

declare(strict_types=1);

namespace App\Enums;

enum EnrollmentStatus: string
{
    case Invited = 'invited';
    case Active = 'active';
    case Dropped = 'dropped';
}
