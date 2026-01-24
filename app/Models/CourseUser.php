<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * @property-read int $id
 * @property-read int $course_id
 * @property-read int $user_id
 * @property-read string $role
 * @property-read \Carbon\CarbonInterface|null $enrolled_at
 * @property-read \Carbon\CarbonInterface|null $invited_at
 * @property-read int|null $invitation_id
 * @property-read string $status
 * @property-read array<string, mixed> $metadata
 * @property-read \Carbon\CarbonInterface $created_at
 * @property-read \Carbon\CarbonInterface $updated_at
 * @property-read \Carbon\CarbonInterface|null $deleted_at
 */
final class CourseUser extends Pivot
{
    use SoftDeletes;

    public $incrementing = true;

    protected $table = 'course_user';

    protected $casts = [
        'enrolled_at' => 'immutable_datetime',
        'invited_at' => 'immutable_datetime',
        'metadata' => 'array',
    ];
}
