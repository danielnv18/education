<?php

declare(strict_types=1);

namespace App\Enums;

enum LessonType: string
{
    case Text = 'text';
    case Video = 'video';
    case Document = 'document';
    case Link = 'link';
    case Embed = 'embed';
}
