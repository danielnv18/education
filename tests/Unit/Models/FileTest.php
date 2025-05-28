<?php

declare(strict_types=1);

use App\Models\File;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

uses(TestCase::class, RefreshDatabase::class);

test('file has correct fillable attributes', function (): void {
    $file = new File();

    expect($file->getFillable())->toBe([
        'name',
        'path',
        'mime_type',
        'extension',
        'size',
        'disk',
        'uploaded_by',
        'fileable_id',
        'fileable_type',
    ]);
});

test('file has correct casts', function (): void {
    $file = new File();

    expect($file->getCasts())->toMatchArray([
        'size' => 'integer',
        'uploaded_at' => 'datetime',
    ]);
});

test('file belongs to an uploader', function (): void {
    $user = User::factory()->create();

    $file = File::factory()->create([
        'uploaded_by' => $user->id,
        'fileable_id' => 1,
        'fileable_type' => User::class,
    ]);

    expect($file->uploader)->toBeInstanceOf(User::class)
        ->and($file->uploader->id)->toBe($user->id);
});

test('file has a fileable relationship', function (): void {
    $user = User::factory()->create();

    $file = File::factory()->create([
        'fileable_id' => $user->id,
        'fileable_type' => User::class,
    ]);

    expect($file->fileable)->toBeInstanceOf(User::class)
        ->and($file->fileable->id)->toBe($user->id);
});
