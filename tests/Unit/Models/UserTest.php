<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\File;
use App\Models\User;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('user has correct fillable attributes', function (): void {
    $user = new User();

    expect($user->getFillable())->toBe([
        'name',
        'email',
        'password',
        'email_verified_at',
    ]);
});

test('user has correct hidden attributes', function (): void {
    $user = new User();

    expect($user->getHidden())->toBe([
        'password',
        'remember_token',
    ]);
});

test('user has correct casts', function (): void {
    $user = new User();

    expect($user->getCasts())->toMatchArray([
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ]);
});

test('user has many files', function (): void {
    $user = User::factory()->create();

    $files = File::factory()->count(3)->create([
        'uploaded_by' => $user->id,
        'fileable_id' => $user->id,
        'fileable_type' => User::class,
    ]);

    expect($user->files())->toBeInstanceOf(HasMany::class)
        ->and($user->files)->toHaveCount(3)
        ->and($user->files->first())->toBeInstanceOf(File::class);
});

test('user has many teaching courses', function (): void {
    $user = User::factory()->create();

    $courses = Course::factory()->count(3)->create([
        'teacher_id' => $user->id,
    ]);

    expect($user->teachingCourses())->toBeInstanceOf(HasMany::class)
        ->and($user->teachingCourses)->toHaveCount(3)
        ->and($user->teachingCourses->first())->toBeInstanceOf(Course::class);
});

test('user has many enrolled courses', function (): void {
    $user = User::factory()->create();
    $courses = Course::factory()->count(3)->create();

    foreach ($courses as $course) {
        $user->enrolledCourses()->attach($course, [
            'enrolled_at' => now(),
            'status' => 'active',
        ]);
    }

    expect($user->enrolledCourses())->toBeInstanceOf(BelongsToMany::class)
        ->and($user->enrolledCourses)->toHaveCount(3)
        ->and($user->enrolledCourses->first())->toBeInstanceOf(Course::class)
        ->and($user->enrolledCourses->first()->pivot->enrolled_at)->not->toBeNull()
        ->and($user->enrolledCourses->first()->pivot->status)->toBe('active');
});

test('user factory creates a valid user', function (): void {
    $user = User::factory()->create();

    expect($user)->toBeInstanceOf(User::class)
        ->and($user->name)->not->toBeEmpty()
        ->and($user->email)->not->toBeEmpty()
        ->and($user->password)->not->toBeEmpty();
});
