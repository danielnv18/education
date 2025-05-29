<?php

declare(strict_types=1);

namespace App\Models;

use App\Enums\CourseStatus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphMany;

final class Course extends Model
{
    /** @use HasFactory<\Database\Factories\CourseFactory> */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'description',
        'status',
        'is_published',
        'teacher_id',
        'thumbnail_id',
        'start_date',
        'end_date',
    ];

    /**
     * @return BelongsTo<User, $this>
     */
    public function teacher(): BelongsTo
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    /**
     * @return BelongsToMany<User>
     */
    public function students(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'course_enrollments')
            ->withPivot(['enrolled_at', 'status'])
            ->withTimestamps();
    }

    /**
     * @return HasMany<Module, $this>
     */
    public function modules(): HasMany
    {
        return $this->hasMany(Module::class)->orderBy('order');
    }

    /**
     * @return MorphMany<File, $this>
     */
    public function files(): MorphMany
    {
        return $this->morphMany(File::class, 'fileable');
    }

    /**
     * @return BelongsTo<File, $this>
     */
    public function thumbnail(): BelongsTo
    {
        return $this->belongsTo(File::class, 'thumbnail_id');
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'is_published' => 'boolean',
            'start_date' => 'datetime',
            'end_date' => 'datetime',
            'status' => CourseStatus::class,
        ];
    }
}
