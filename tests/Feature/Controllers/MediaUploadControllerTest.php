<?php

declare(strict_types=1);

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

it('requires authentication to upload media', function (): void {
    $response = $this->post(route('media.uploads.store'));

    $response->assertRedirectToRoute('login');
});

it('uploads an image for the current user', function (): void {
    config(['filesystems.default' => 'public']);
    Storage::fake('public');

    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('media.uploads.store'), [
            'file' => UploadedFile::fake()->image('avatar.jpg')->size(1024),
            'collection' => 'temporary',
        ]);

    $response->assertCreated()
        ->assertJsonStructure([
            'id',
            'uuid',
            'name',
            'mimeType',
            'size',
            'url',
            'previewUrl',
            'collection',
        ]);

    $media = Media::query()->first();

    expect($media)->not->toBeNull()
        ->and($media?->collection_name)->toBe('temporary')
        ->and($media?->model_id)->toBe($user->id)
        ->and(file_exists($media?->getPath() ?? ''))->toBeTrue();
});

it('uploads a document as well', function (): void {
    config(['filesystems.default' => 'public']);
    Storage::fake('public');

    $user = User::factory()->create();

    $file = UploadedFile::fake()
        ->create('doc.pdf')
        ->size(512)
        ->mimeType('application/pdf');

    $response = $this->actingAs($user)
        ->postJson(route('media.uploads.store'), [
            'file' => $file,
        ]);

    $response->assertCreated()
        ->assertJsonPath('collection', 'temporary');
});

it('validates upload size and type', function (): void {
    config(['filesystems.default' => 'public']);
    Storage::fake('public');

    $user = User::factory()->create();

    $response = $this->actingAs($user)
        ->postJson(route('media.uploads.store'), [
            'file' => UploadedFile::fake()->create('doc.pdf', 6000, 'application/pdf'),
        ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors('file');
});
