<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CourseStatus;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Date;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * @property-read int $id
 * @property-read string $slug
 * @property-read string $title
 * @property-read string|null $description
 * @property-read int|null $banner_media_id
 * @property-read int $teacher_id
 * @property-read CourseStatus $status
 * @property-read \Carbon\CarbonInterface|null $published_at
 * @property-read \Carbon\CarbonInterface|null $starts_at
 * @property-read \Carbon\CarbonInterface|null $ends_at
 * @property-read array<string, mixed> $metadata
 * @property-read bool $is_published
 * @property-read int|null $created_by_id
 * @property-read int|null $updated_by_id
 * @property-read \Carbon\CarbonInterface $created_at
 * @property-read \Carbon\CarbonInterface $updated_at
 * @property-read \Carbon\CarbonInterface|null $deleted_at
 */
final class Course extends Model implements HasMedia
{
    /** @use HasFactory<\Database\Factories\CourseFactory> */
    use HasFactory, InteractsWithMedia, SoftDeletes;

    protected $casts = [
        'status' => CourseStatus::class,
        'metadata' => 'array',
        'published_at' => 'immutable_datetime',
        'starts_at' => 'immutable_datetime',
        'ends_at' => 'immutable_datetime',
    ];

    /**
     * @return HasMany<Module, $this>
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    /**
     * @return BelongsToMany<User, $this, CourseUser>
     */
    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)
            ->using(CourseUser::class)
            ->withPivot(['role', 'status', 'enrolled_at', 'invited_at', 'invitation_id'])
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<User, $this, CourseUser>
     */
    public function teachers(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'teacher');
    }

    /**
     * @return BelongsTo<User, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * @return BelongsToMany<User, $this, CourseUser>
     */
    public function students(): BelongsToMany
    {
        return $this->users()->wherePivot('role', 'student');
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
     * Determine if the course is published.
     *
     * @return Attribute<bool, never>
     */
    public function isPublished(): Attribute
    {
        return Attribute::make(
            get: function (mixed $value, array $attributes): bool {
                $publishedAt = isset($attributes['published_at']) ? Date::parse($attributes['published_at']) : null;
                $status = $attributes['status'] ?? null;
                $now = now();

                return $publishedAt instanceof \Carbon\CarbonInterface
                    && $publishedAt->lte($now)
                    && $status === CourseStatus::Published->value;
            },
        );
    }
}
