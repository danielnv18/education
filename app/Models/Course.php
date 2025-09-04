<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\CourseData;
use App\Enums\CourseStatus;
use Database\Factories\CourseFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\LaravelData\WithData;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

final class Course extends Model implements HasMedia
{
    /** @use HasFactory<CourseFactory> */
    use HasFactory, InteractsWithMedia;

    /** @use WithData<CourseData> */
    use WithData;

    protected string $dataClass = CourseData::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'published_at',
        'teacher_id',
        'start_at',
        'end_at',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * @return BelongsToMany<User, $this>
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_enrollments')
            ->withPivot(['enrolled_at', 'status'])
            ->withTimestamps();
    }

    /** @return Attribute<bool, void> */
    public function isPublished(): Attribute
    {
        return Attribute::make(
            get: fn (): bool => (bool) $this->published_at,
        );
    }

    /** @return Attribute<string, void> */
    public function cover(): Attribute
    {
        return Attribute::make(
            get: fn (): string => $this->getFirstMediaUrl('cover'),
        );
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('cover')
            ->acceptsMimeTypes(['image/jpeg', 'image/png', 'image/webp'])
            ->singleFile();
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
            'published_at' => 'immutable_datetime',
            'start_date' => 'immutable_datetime',
            'end_date' => 'immutable_datetime',
            'status' => CourseStatus::class,
        ];
    }
}
