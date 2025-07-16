<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\LessonType;
use Spatie\LaravelData\Data;

final class LessonData extends Data
{
    public function __construct(
        public int $id,
        public string $title,
        public string $content,
        public LessonType $type,
        public int $order = 0,
    ) {}
}
