<?php

declare(strict_types=1);

use App\Models\User;

it('returns null when avatar is missing', function (): void {
    $user = User::factory()->create();

    expect($user->avatar)->toBeNull();
});
