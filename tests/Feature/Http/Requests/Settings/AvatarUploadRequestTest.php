<?php

declare(strict_types=1);

use App\Http\Requests\Settings\AvatarUploadRequest;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

it('allows avatar to be nullable', function (): void {
    // Arrange
    $data = [];
    $validator = validator($data, (new AvatarUploadRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeFalse();
});

it('validates avatar is an image', function (): void {
    // Arrange
    Storage::fake('avatars');
    $nonImageFile = UploadedFile::fake()->create('document.pdf', 100);

    $data = [
        'avatar' => $nonImageFile,
    ];

    $validator = validator($data, (new AvatarUploadRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('avatar'))->toBeTrue();
});

it('validates avatar has correct mime type', function (): void {
    // Arrange
    Storage::fake('avatars');
    $invalidMimeFile = UploadedFile::fake()->create('avatar.gif', 100);

    $data = [
        'avatar' => $invalidMimeFile,
    ];

    $validator = validator($data, (new AvatarUploadRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('avatar'))->toBeTrue();
});

it('validates avatar size is within limits', function (): void {
    // Arrange
    Storage::fake('avatars');
    $oversizedFile = UploadedFile::fake()->image('avatar.jpg')->size(11000); // 11MB, over the 10MB limit

    $data = [
        'avatar' => $oversizedFile,
    ];

    $validator = validator($data, (new AvatarUploadRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('avatar'))->toBeTrue();
});

it('passes validation with valid image', function (): void {
    // Arrange
    Storage::fake('avatars');
    $validFile = UploadedFile::fake()->image('avatar.jpg')->size(1000); // 1MB, under the limit

    $data = [
        'avatar' => $validFile,
    ];

    $validator = validator($data, (new AvatarUploadRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeFalse();
});

it('passes validation with valid png image', function (): void {
    // Arrange
    Storage::fake('avatars');
    $validFile = UploadedFile::fake()->image('avatar.png')->size(1000);

    $data = [
        'avatar' => $validFile,
    ];

    $validator = validator($data, (new AvatarUploadRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeFalse();
});

it('passes validation with valid webp image', function (): void {
    // Arrange
    Storage::fake('avatars');
    // Create a simple file with webp mime type for validation testing
    $validFile = UploadedFile::fake()->create(
        'avatar.webp',
        100, // 100KB
        'image/webp'
    );

    $data = [
        'avatar' => $validFile,
    ];

    $validator = validator($data, (new AvatarUploadRequest())->rules());

    // Act & Assert
    expect($validator->fails())->toBeFalse();
});
