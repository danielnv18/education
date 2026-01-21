<?php

declare(strict_types=1);

use App\Http\Requests\UpdateUserRequest;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

beforeEach(function (): void {
    config(['filesystems.default' => 'public']);
    Storage::fake('public');
});

it('passes validation with own avatar media in allowed collections', function (): void {
    $user = User::factory()->create(['email' => 'user@example.com']);

    $avatar = $user->addMedia(UploadedFile::fake()->image('avatar.jpg'))
        ->toMediaCollection('temporary');

    $request = new UpdateUserRequest();
    $request->setUserResolver(static fn () => $user);

    $validator = Validator::make([
        'name' => 'Updated Name',
        'email' => 'user@example.com',
        'avatar_media_id' => $avatar->id,
    ], $request->rules());

    expect($validator->passes())->toBeTrue();
});

it('fails when avatar media does not belong to the user', function (): void {
    $user = User::factory()->create(['email' => 'user@example.com']);
    $other = User::factory()->create();

    $foreignMedia = $other->addMedia(UploadedFile::fake()->image('other.jpg'))
        ->toMediaCollection('temporary');

    $request = new UpdateUserRequest();
    $request->setUserResolver(static fn () => $user);

    $validator = Validator::make([
        'name' => 'Updated Name',
        'email' => 'user@example.com',
        'avatar_media_id' => $foreignMedia->id,
    ], $request->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('avatar_media_id'))->toBeTrue();
});

it('enforces unique email', function (): void {
    $existing = User::factory()->create(['email' => 'taken@example.com']);
    $user = User::factory()->create(['email' => 'user@example.com']);

    $request = new UpdateUserRequest();
    $request->setUserResolver(static fn () => $user);

    $validator = Validator::make([
        'name' => 'Updated Name',
        'email' => $existing->email,
    ], $request->rules());

    expect($validator->fails())->toBeTrue()
        ->and($validator->errors()->has('email'))->toBeTrue();
});

it('authorizes authenticated users', function (): void {
    $user = User::factory()->make();

    $request = new UpdateUserRequest();
    $request->setUserResolver(static fn () => $user);

    expect($request->authorize())->toBeTrue();
});

it('accepts remove avatar flags', function (): void {
    $user = User::factory()->create(['email' => 'user@example.com']);

    $request = new UpdateUserRequest();
    $request->setUserResolver(static fn () => $user);

    $validator = Validator::make([
        'name' => 'Updated Name',
        'email' => 'user@example.com',
        'remove_avatar' => true,
    ], $request->rules());

    expect($validator->passes())->toBeTrue();
});
