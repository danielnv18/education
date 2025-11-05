<?php

declare(strict_types=1);

namespace App\Enums;

enum LessonContentType: string
{
    case Markdown = 'markdown';
    case VideoEmbed = 'video_embed';
    case DocumentBundle = 'document_bundle';
}
