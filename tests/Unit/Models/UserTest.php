<?php

declare(strict_types=1);

use App\Models\User;

test('to array', function (): void {
    $user = User::factory()->create()->refresh();

    expect(array_keys($user->toArray()))
        ->toBe([
            'id',
            'name',
            'email',
            'email_verified_at',
            'two_factor_confirmed_at',
            'deleted_at',
            'created_at',
            'updated_at',
        ]);
});

test('soft deletes the user', function (): void {
    $user = User::factory()->create();

    $user->delete();

    expect(User::query()->find($user->id))->toBeNull()
        ->and(User::withTrashed()->find($user->id)?->trashed())->toBeTrue()
        ->and(User::withTrashed()->find($user->id)?->deleted_at)->not->toBeNull();
});
