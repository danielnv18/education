<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Date;

/**
 * @property-read int $id
 * @property-read int $course_id
 * @property-read string $title
 * @property-read string $slug
 * @property-read string|null $description
 * @property-read int $order
 * @property-read string $status
 * @property-read \Carbon\CarbonInterface|null $published_at
 * @property-read \Carbon\CarbonInterface|null $unpublish_at
 * @property-read bool $is_published
 * @property-read array<string, mixed> $metadata
 * @property-read int|null $created_by_id
 * @property-read int|null $updated_by_id
 * @property-read \Carbon\CarbonInterface $created_at
 * @property-read \Carbon\CarbonInterface $updated_at
 * @property-read \Carbon\CarbonInterface|null $deleted_at
 */
final class Module extends Model
{
    /** @use HasFactory<\Database\Factories\ModuleFactory> */
    use HasFactory, SoftDeletes;

    protected $casts = [
        'metadata' => 'array',
        'published_at' => 'immutable_datetime',
        'unpublish_at' => 'immutable_datetime',
    ];

    /**
     * @return BelongsTo<Course, $this>
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * @return HasMany<Lesson, $this>
     */
    public function lessons(): HasMany
    {
        return $this->hasMany(Lesson::class)->orderBy('order');
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

    /**
     * Determine if the module is published.
     *
     * @return Attribute<bool, never>
     */
    public function isPublished(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): bool {
                $publishedAt = isset($attributes['published_at']) ? Date::parse($attributes['published_at']) : null;
                $unpublishAt = isset($attributes['unpublish_at']) ? Date::parse($attributes['unpublish_at']) : null;
                $now = now();

                return $publishedAt instanceof \Carbon\CarbonInterface
                    && $publishedAt->lte($now)
                    && (! $unpublishAt instanceof \Carbon\CarbonInterface || $unpublishAt->gt($now));
            },
        );
    }
}
