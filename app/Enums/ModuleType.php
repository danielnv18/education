<?php

declare(strict_types=1);

namespace App\Enums;

enum ModuleType: string
{
    case Content = 'content';
    case Assignment = 'assignment';
    case Exam = 'exam';
}
