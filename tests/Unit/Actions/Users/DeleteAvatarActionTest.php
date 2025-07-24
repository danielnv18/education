<?php

declare(strict_types=1);

use App\Actions\Users\DeleteAvatarAction;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

it('deletes the user avatar', function (): void {
    // Arrange
    $user = User::factory()->create();

    // Add a test avatar to the user
    $user->addMedia(UploadedFile::fake()->image('avatar.jpg'))
        ->toMediaCollection('avatar');

    // Verify the user has an avatar
    expect($user->getFirstMedia('avatar'))->not->toBeNull();

    $action = new DeleteAvatarAction();

    // Act
    $action->handle($user);

    // Assert
    // Refresh the user model to get the latest data
    $user->refresh();
    expect($user->getFirstMedia('avatar'))->toBeNull();
});

it('uses a database transaction', function (): void {
    // Arrange
    $user = User::factory()->create();

    // Mock DB facade to verify transaction is used
    DB::shouldReceive('transaction')
        ->once()
        ->andReturnUsing(function ($callback) {
            return $callback();
        });

    $action = new DeleteAvatarAction();

    // Act
    $action->handle($user);

    // Assert is handled by the mock expectations
});
