<?php

declare(strict_types=1);

namespace App\Models;

use App\Data\UserData;
use Carbon\CarbonInterface;
use Database\Factories\UserFactory;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Spatie\Image\Enums\Fit;
use Spatie\LaravelData\WithData;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Permission\Traits\HasRoles;

/**
 * @property-read int $id
 * @property-read string $name
 * @property-read string $email
 * @property-read CarbonInterface|null $email_verified_at
 * @property-read string $password
 * @property-read string|null $remember_token
 * @property-read string|null $two_factor_secret
 * @property-read string|null $two_factor_recovery_codes
 * @property-read CarbonInterface|null $two_factor_confirmed_at
 * @property-read CarbonInterface $created_at
 * @property-read CarbonInterface $updated_at
 * @property-read CarbonInterface|null $deleted_at
 */
final class User extends Authenticatable implements HasMedia, MustVerifyEmail
{
    /**
     * @use HasFactory<UserFactory>
     */
    use HasFactory;

    use HasRoles;
    use InteractsWithMedia;
    use Notifiable;
    use SoftDeletes;
    use TwoFactorAuthenticatable;

    /**
     * @use WithData<UserData>
     */
    use WithData;

    /**
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_secret',
        'two_factor_recovery_codes',
    ];

    protected string $dataClass = UserData::class;

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('avatar')->singleFile();
        $this->addMediaCollection('temporary');
    }

    public function registerMediaConversions(?Media $media = null): void
    {
        $conversion = $this->addMediaConversion('avatar_thumb')
            ->performOnCollections('avatar')
            ->nonQueued();

        $conversion->fit(Fit::Crop, 256, 256);
    }

    /**
     * @return array<string, string>
     */
    public function casts(): array
    {
        return [
            'id' => 'integer',
            'name' => 'string',
            'email' => 'string',
            'email_verified_at' => 'immutable_datetime',
            'password' => 'hashed',
            'remember_token' => 'string',
            'two_factor_secret' => 'string',
            'two_factor_recovery_codes' => 'string',
            'two_factor_confirmed_at' => 'immutable_datetime',
            'created_at' => 'immutable_datetime',
            'updated_at' => 'immutable_datetime',
            'deleted_at' => 'immutable_datetime',
        ];
    }

    /**
     * @return BelongsToMany<Course, $this, CourseUser>
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class)
            ->using(CourseUser::class)
            ->withPivot(['role', 'status', 'enrolled_at', 'invited_at', 'invitation_id'])
            ->withTimestamps();
    }

    /**
     * @return BelongsToMany<Course, $this, CourseUser>
     */
    public function teachingCourses(): BelongsToMany
    {
        return $this->courses()->wherePivot('role', 'teacher');
    }

    /**
     * @return BelongsToMany<Course, $this, CourseUser>
     */
    public function assistingCourses(): BelongsToMany
    {
        return $this->courses()->wherePivot('role', 'assistant');
    }

    /**
     * @return BelongsToMany<Course, $this, CourseUser>
     */
    public function enrolledCourses(): BelongsToMany
    {
        return $this->courses()->wherePivot('role', 'student');
    }

    /**
     * Get the user's avatar.
     *
     * @return Attribute<string|null, never>
     */
    public function avatar(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->getFirstMediaUrl('avatar') ?: null,
        );
    }
}
