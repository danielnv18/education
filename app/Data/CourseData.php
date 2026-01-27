<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\CourseStatus;
use Carbon\CarbonImmutable;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Attributes\DataCollectionOf;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Data;

final class CourseData extends Data
{
    /**
     * @param  array<string, mixed>  $metadata
     * @param  Collection<int, ModuleData>|null  $modules
     */
    public function __construct(
        public int $id,
        public string $slug,
        public string $title,
        public ?string $description,
        #[Enum(CourseStatus::class)]
        public CourseStatus $status,
        #[Date]
        public ?CarbonImmutable $publishedAt,
        #[Date]
        public ?CarbonImmutable $startsAt,
        #[Date]
        public ?CarbonImmutable $endsAt,
        public array $metadata,
        public int $teacherId,
        #[DataCollectionOf(ModuleData::class)]
        public ?Collection $modules = null,
        public bool $isPublished = false,
    ) {}
}
