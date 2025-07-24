<?php

declare(strict_types=1);

use App\Actions\Users\UpdateAvatarAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('updates the user avatar', function (): void {
    // Arrange
    $user = User::factory()->create();
    $avatar = UploadedFile::fake()->image('new-avatar.jpg');

    // Add an initial avatar to the user
    $user->addMedia(UploadedFile::fake()->image('old-avatar.jpg'))
        ->toMediaCollection('avatar');

    // Get the initial avatar filename
    $oldAvatarFilename = $user->getFirstMedia('avatar')->file_name;

    $action = new UpdateAvatarAction();

    // Act
    $action->handle($user, $avatar);

    // Assert
    // Refresh the user model to get the latest data
    $user->refresh();

    // Verify that the avatar has been updated
    $newAvatar = $user->getFirstMedia('avatar');
    expect($newAvatar)->not->toBeNull()
        ->and($newAvatar->file_name)->toBe('new-avatar.jpg')
        ->and($newAvatar->file_name)->not->toBe($oldAvatarFilename);
});

it('uses a database transaction', function (): void {
    // Arrange
    $user = User::factory()->create();
    $avatar = UploadedFile::fake()->image('avatar.jpg');

    // Mock DB facade to verify transaction is used
    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(function ($callback) {
            return $callback();
        });

    $action = new UpdateAvatarAction();

    // Act
    $action->handle($user, $avatar);

    // Assert is handled by the mock expectations
});
