<?php

declare(strict_types=1);

namespace App\Enums;

enum QuestionType: string
{
    case SingleChoice = 'single_choice';
    case MultipleChoice = 'multiple_choice';
    case RichText = 'rich_text';
}
