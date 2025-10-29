<?php

declare(strict_types=1);

namespace App\Enums;

enum SubmissionEventType: string
{
    case Autosave = 'autosave';
    case Comment = 'comment';
    case StatusChange = 'status_change';
}
