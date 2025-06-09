<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

final class SendPasswordResetLinkAction
{
    public function handle(User $user): void
    {
        DB::transaction(function () use ($user): void {
            Password::sendResetLink([
                'email' => $user->email,
            ]);
        });
    }
}
