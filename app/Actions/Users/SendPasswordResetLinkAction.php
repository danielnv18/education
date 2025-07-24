<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Password;

final readonly class SendPasswordResetLinkAction
{
    /**
     * Send a password reset link to the user.
     *
     * @param  User  $user  The user to send the reset link to
     */
    public function handle(User $user): void
    {
        DB::transaction(function () use ($user): void {
            Password::sendResetLink([
                'email' => $user->email,
            ]);
        });
    }
}
