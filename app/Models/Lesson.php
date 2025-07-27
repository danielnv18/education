<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\LessonData;
use App\Enums\LessonType;
use Database\Factories\LessonFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\LaravelData\WithData;

final class Lesson extends Model
{
    /** @use HasFactory<LessonFactory> */
    use HasFactory;

    /** @use WithData<LessonData> */
    use WithData;

    protected string $dataClass = LessonData::class;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'title',
        'content',
        'module_id',
        'order',
        'type',
        'is_published',
    ];

    /**
     * @return BelongsTo<Module, $this>
     */
    public function module(): BelongsTo
    {
        return $this->belongsTo(Module::class);
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
            'order' => 'integer',
            'type' => LessonType::class,
        ];
    }
}
