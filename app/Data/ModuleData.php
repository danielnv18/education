<?php

declare(strict_types=1);

namespace App\Data;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class ModuleData extends Data
{
    public function __construct(
        public ?int $id,
        public string $title,
        public ?string $description,
        public int $order = 0,
        public bool $isPublished = false,
        /** @var Collection<int, LessonData> */
        public Collection $lessons = new Collection(),
    ) {}
}
