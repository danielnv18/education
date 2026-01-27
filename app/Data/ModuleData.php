<?php

declare(strict_types=1);

namespace App\Data;

use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Data;

final class ModuleData extends Data
{
    /**
     * @param  array<string, mixed>  $metadata
     * @param  Collection<int, LessonData>|null  $lessons
     */
    public function __construct(
        public int $id,
        public int $courseId,
        public string $title,
        public string $slug,
        public ?string $description,
        public int $order,
        #[Date]
        public ?CarbonImmutable $publishedAt,
        public bool $isPublished,
        public array $metadata,
        #[DataCollectionOf(LessonData::class)]
        public ?Collection $lessons = null,
    ) {}
}
