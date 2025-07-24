<?php

declare(strict_types=1);

namespace App\Actions\Users;

use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

final readonly class UpdateAvatarAction
{
    /**
     * Execute the action.
     */
    public function handle(User $user, UploadedFile $avatar): void
    {
        DB::transaction(function () use ($user, $avatar): void {
            // Add or replace avatar
            $user->addMedia($avatar)
                ->toMediaCollection('avatar');
        });
    }
}
