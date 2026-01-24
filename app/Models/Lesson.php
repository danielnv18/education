<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property-read int $id
 * @property-read int $module_id
 * @property-read string $title
 * @property-read string $slug
 * @property-read string|null $summary
 * @property-read string|null $content
 * @property-read string $content_type
 * @property-read int $order
 * @property-read int|null $duration_minutes
 * @property-read string $status
 * @property-read \Carbon\CarbonInterface|null $published_at
 * @property-read \Carbon\CarbonInterface|null $unpublish_at
 * @property-read array<string, mixed> $metadata
 * @property-read int|null $created_by_id
 * @property-read int|null $updated_by_id
 * @property-read \Carbon\CarbonInterface $created_at
 * @property-read \Carbon\CarbonInterface $updated_at
 * @property-read \Carbon\CarbonInterface|null $deleted_at
 */
final class Lesson extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\LessonFactory> */
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $casts = [
        'metadata' => 'array',
        'published_at' => 'immutable_datetime',
        'unpublish_at' => 'immutable_datetime',
    ];

    /**
     * @return BelongsTo<Module, $this>
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function updatedBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'updated_by_id');
    }
}
