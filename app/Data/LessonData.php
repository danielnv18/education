<?php

declare(strict_types=1);

namespace App\Data;

use App\Enums\LessonContentType;
use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Attributes\Validation\Enum;
use Spatie\LaravelData\Data;

final class LessonData extends Data
{
    /**
     * @param  array<string, mixed>  $metadata
     */
    public function __construct(
        public int $id,
        public int $moduleId,
        public string $title,
        public string $slug,
        public ?string $summary,
        public ?string $content,
        #[Enum(LessonContentType::class)]
        public LessonContentType $contentType,
        public int $order,
        public ?int $durationMinutes,
        #[Date]
        public ?CarbonImmutable $publishedAt,
        public array $metadata,
        public bool $isPublished = false,
    ) {}
}
