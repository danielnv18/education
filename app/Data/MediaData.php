<?php

declare(strict_types=1);

namespace App\Data;

use Carbon\CarbonImmutable;
use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Attributes\Validation\Date;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class MediaData extends Data
{
    public function __construct(
        public ?int $id = null,
        public ?string $uuid = null,
        public string $collectionName = '',
        public string $name = '',
        public string $fileName = '',
        public ?string $mimeType = null,
        public int $size = 0,
        public array $manipulations = [],
        public array $customProperties = [],
        public array $generatedConversions = [],
        public array $responsiveImages = [],
        public ?int $orderColumn = null,
        #[Date]
        public ?CarbonImmutable $createdAt = null,
        #[Date]
        public ?CarbonImmutable $updatedAt = null
    ) {}
}
