<?php

declare(strict_types=1);

use App\Models\Course;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
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

test('user has many teaching courses', function (): void {
    $user = User::factory()->create();

    // Create courses taught by the user
    Course::factory()->count(3)->create([
        'teacher_id' => $user->id,
    ]);

    expect($user->teachingCourses)->toHaveCount(3)
        ->and($user->teachingCourses->first())->toBeInstanceOf(Course::class);
});

test('user belongs to many enrolled courses', function (): void {
    $user = User::factory()->create();
    $courses = Course::factory()->count(3)->create();

    // Enroll the user in courses
    foreach ($courses as $course) {
        $user->enrolledCourses()->attach($course, [
            'enrolled_at' => now(),
            'status' => 'active',
        ]);
    }

    expect($user->enrolledCourses)->toHaveCount(3)
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

test('user registers media collections correctly', function (): void {
    $user = User::factory()->create();

    // Add a test avatar to the user
    $avatar = $user->addMedia(UploadedFile::fake()->image('avatar.jpg'))
        ->toMediaCollection('avatar');

    // Test that the avatar was added to the collection
    expect($user->getMedia('avatar'))->toHaveCount(1)
        ->and($avatar->collection_name)->toBe('avatar');

    // Test that adding a second avatar replaces the first one (singleFile)
    $newAvatar = $user->addMedia(UploadedFile::fake()->image('new-avatar.png'))
        ->toMediaCollection('avatar');

    expect($user->getMedia('avatar'))->toHaveCount(1)
        ->and($user->getFirstMedia('avatar')->file_name)->toBe('new-avatar.png');

    // Test that non-image files are rejected
    // This would throw an exception if the mime type validation fails
    // We're not testing the exception here as that's handled by the media library
});

test('user avatar attribute returns correct url', function (): void {
    $user = User::factory()->create();

    // Initially, with no avatar, it should return an empty string
    expect($user->avatar)->toBe('');

    // Add an avatar
    $user->addMedia(UploadedFile::fake()->image('avatar.jpg'))
        ->toMediaCollection('avatar');

    // Refresh the model to ensure we get the latest data
    $user->refresh();

    // Now it should return a URL
    expect($user->avatar)->not->toBe('')
        ->and($user->avatar)->toBe($user->getFirstMediaUrl('avatar'));
});
