<?php

declare(strict_types=1);

use App\Data\MediaData;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('creates preview url for image media', function (): void {
    config(['filesystems.default' => 'public']);
    Storage::fake('public');

    $user = User::factory()->create();

    $media = $user->addMedia(UploadedFile::fake()->image('avatar.jpg'))
        ->toMediaCollection('avatar');

    $data = MediaData::fromMedia($media);

    expect($data->previewUrl)->not->toBeNull()
        ->and($data->mimeType)->toContain('image/');
});

it('uses null preview for non-image media', function (): void {
    config(['filesystems.default' => 'public']);
    Storage::fake('public');

    $user = User::factory()->create();

    $media = $user->addMedia(UploadedFile::fake()->create('document.pdf', 100))
        ->toMediaCollection('temporary');

    $data = MediaData::fromMedia($media);

    expect($data->previewUrl)->toBeNull()
        ->and($data->mimeType)->not->toContain('image/');
});
