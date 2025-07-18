<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\LessonType;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class LessonData extends Data
{
    public function __construct(
        public ?int $id,
        public string $title,
        public string $content,
        public LessonType $type,
        public ModuleData $module,
        public int $order = 0,
        public bool $isPublished = false,
    ) {}
}
