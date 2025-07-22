<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Attributes\MapInputName;
use Spatie\LaravelData\Data;
use Spatie\LaravelData\Mappers\SnakeCaseMapper;

#[MapInputName(SnakeCaseMapper::class)]
final class MediaData extends Data
{
    public function __construct(
        public ?int $id = null,
        public string $modelType = '',
        public int $modelId = 0,
        public ?string $uuid = null,
        public string $collectionName = '',
        public string $name = '',
        public string $file_name = '',
        public ?string $mime_type = null,
        public string $disk = '',
        public ?string $conversionsDisk = null,
        public int $size = 0,
        public array $manipulations = [],
        public array $customProperties = [],
        public array $generatedConversions = [],
        public array $responsiveImages = [],
        public ?int $orderColumn = null,
        public ?string $createdAt = null,
        public ?string $updatedAt = null
    ) {}
}
