<?php

declare(strict_types=1);

namespace App\Data;

use Spatie\LaravelData\Attributes\Validation\Max;
use Spatie\LaravelData\Data;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final class MediaData extends Data
{
    public function __construct(
        public int $id,
        public string $uuid,
        public string $name,
        public string $mimeType,
        #[Max(5120)]
        public int $size,
        public string $url,
        public ?string $previewUrl,
        public string $collection,
    ) {}

    public static function fromMedia(Media $media): self
    {
        $isImage = str_starts_with((string) $media->mime_type, 'image/');

        $previewUrl = $isImage && $media->hasGeneratedConversion('avatar_thumb')
            ? $media->getUrl('avatar_thumb')
            : ($isImage ? $media->getUrl() : null);

        return new self(
            id: $media->id,
            uuid: $media->uuid,
            name: $media->file_name,
            mimeType: $media->mime_type ?? 'application/octet-stream',
            size: $media->size,
            url: $media->getUrl(),
            previewUrl: $previewUrl,
            collection: $media->collection_name,
        );
    }
}
