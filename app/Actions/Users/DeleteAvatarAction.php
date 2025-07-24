<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Support\Facades\DB;

final readonly class DeleteAvatarAction
{
    /**
     * Execute the action.
     */
    public function handle(User $user): void
    {
        DB::transaction(function () use ($user): void {
            $user->clearMediaCollection('avatar');
        });
    }
}
