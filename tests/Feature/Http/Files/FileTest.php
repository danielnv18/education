<?php

declare(strict_types=1);

use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('a user can create a file', function (): void {
    $user = User::factory()->create();

    $fileData = [
        'name' => 'test-file.pdf',
        'path' => 'files/test-file.pdf',
        'mime_type' => 'application/pdf',
        'extension' => 'pdf',
        'size' => 1024,
        'disk' => 'local',
        'uploaded_by' => $user->id,
        'fileable_id' => $user->id,
        'fileable_type' => User::class,
    ];

    $file = File::create($fileData);

    expect($file)->toBeInstanceOf(File::class)
        ->and($file->name)->toBe('test-file.pdf')
        ->and($file->path)->toBe('files/test-file.pdf')
        ->and($file->mime_type)->toBe('application/pdf')
        ->and($file->extension)->toBe('pdf')
        ->and($file->size)->toBe(1024)
        ->and($file->disk)->toBe('local')
        ->and($file->uploaded_by)->toBe($user->id)
        ->and($file->fileable_id)->toBe($user->id)
        ->and($file->fileable_type)->toBe(User::class);

    $this->assertDatabaseHas('files', $fileData);
});

test('a file can be retrieved', function (): void {
    $user = User::factory()->create();

    $file = File::factory()->create([
        'fileable_id' => $user->id,
        'fileable_type' => User::class,
    ]);

    $retrievedFile = File::find($file->id);

    expect($retrievedFile)->toBeInstanceOf(File::class)
        ->and($retrievedFile->id)->toBe($file->id)
        ->and($retrievedFile->name)->toBe($file->name)
        ->and($retrievedFile->path)->toBe($file->path)
        ->and($retrievedFile->mime_type)->toBe($file->mime_type)
        ->and($retrievedFile->extension)->toBe($file->extension)
        ->and($retrievedFile->size)->toBe($file->size)
        ->and($retrievedFile->disk)->toBe($file->disk)
        ->and($retrievedFile->uploaded_by)->toBe($file->uploaded_by)
        ->and($retrievedFile->fileable_id)->toBe($user->id)
        ->and($retrievedFile->fileable_type)->toBe(User::class);
});

test('a user can have multiple files', function (): void {
    $user = User::factory()->create();

    $files = File::factory()->count(3)->create([
        'uploaded_by' => $user->id,
        'fileable_id' => $user->id,
        'fileable_type' => User::class,
    ]);

    expect($user->files)->toHaveCount(3)
        ->and($user->files->pluck('id'))->toContain($files[0]->id)
        ->and($user->files->pluck('id'))->toContain($files[1]->id)
        ->and($user->files->pluck('id'))->toContain($files[2]->id);
});
