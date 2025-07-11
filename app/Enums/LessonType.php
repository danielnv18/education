<?php

declare(strict_types=1);

namespace App\Enums;

enum LessonType: string
{
    case TEXT = 'text';
    case VIDEO = 'video';
    case DOCUMENT = 'document';
    case LINK = 'link';
    case EMBED = 'embed';
}
