<?php

declare(strict_types=1);

namespace App\Enums;

enum CourseRole: string
{
    case Teacher = 'teacher';
    case Assistant = 'assistant';
    case Student = 'student';
}
