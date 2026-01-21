<?php

declare(strict_types=1);

use App\Actions\UpdateUser;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('may update a user', function (): void {
    $user = User::factory()->create([
        'name' => 'Old Name',
        'email' => 'old@email.com',
    ]);

    $action = resolve(UpdateUser::class);

    $action->handle($user, [
        'name' => 'New Name',
    ]);

    expect($user->refresh()->name)->toBe('New Name')
        ->and($user->email)->toBe('old@email.com');
});

it('resets email verification when email changes', function (): void {
    $user = User::factory()->create([
        'email' => 'old@email.com',
        'email_verified_at' => now(),
    ]);

    expect($user->email_verified_at)->not->toBeNull();

    $action = resolve(UpdateUser::class);

    $action->handle($user, [
        'email' => 'new@email.com',
    ]);

    expect($user->refresh()->email)->toBe('new@email.com')
        ->and($user->email_verified_at)->toBeNull();
});

it('keeps email verification when email stays the same', function (): void {
    $verifiedAt = now();

    $user = User::factory()->create([
        'email' => 'same@email.com',
        'email_verified_at' => $verifiedAt,
    ]);

    $action = resolve(UpdateUser::class);

    $action->handle($user, [
        'email' => 'same@email.com',
        'name' => 'Updated Name',
    ]);

    expect($user->refresh()->email_verified_at)->not->toBeNull()
        ->and($user->name)->toBe('Updated Name');
});

it('ignores missing avatar media', function (): void {
    config(['filesystems.default' => 'public']);
    Storage::fake('public');

    $user = User::factory()->create();

    $action = resolve(UpdateUser::class);

    $action->handle($user, [
        'avatar_media_id' => 9999,
    ]);

    expect($user->refresh()->getFirstMedia('avatar'))->toBeNull();
});

it('skips reattaching avatar when already in avatar collection', function (): void {
    config(['filesystems.default' => 'public']);
    Storage::fake('public');

    $user = User::factory()->create();

    $media = $user->addMedia(UploadedFile::fake()->image('avatar.jpg'))
        ->toMediaCollection('avatar');

    $action = resolve(UpdateUser::class);

    $action->handle($user, [
        'avatar_media_id' => $media->id,
    ]);

    expect($user->refresh()->getFirstMedia('avatar'))->not->toBeNull()
        ->and($user->getFirstMedia('avatar')?->id)->toBe($media->id);
});
