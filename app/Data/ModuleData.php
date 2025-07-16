<?php

declare(strict_types=1);

namespace App\Data;

use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

final class ModuleData extends Data
{
    public function __construct(
        public ?int $id,
        public string $title,
        public ?string $description,
        public int $order = 0,
        /** @var Collection<int, LessonData> */
        public Collection $lessons = new Collection(),
    ) {}
}
