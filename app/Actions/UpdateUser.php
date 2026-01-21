<?php

declare(strict_types=1);

namespace App\Actions;

use App\Models\User;
use Illuminate\Support\Facades\DB;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

final readonly class UpdateUser
{
    /**
     * @param  array<string, mixed>  $attributes
     */
    public function handle(User $user, array $attributes): void
    {
        DB::transaction(function () use ($user, $attributes): void {
            $email = $attributes['email'] ?? null;

            $user->update([
                'name' => $attributes['name'] ?? $user->name,
                'email' => $attributes['email'] ?? $user->email,
                ...$user->email === $email ? [] : ['email_verified_at' => null],
            ]);

            if (isset($attributes['avatar_media_id'])) {
                $this->updateAvatar($user, (int) $attributes['avatar_media_id']);

                return;
            }

            if (! empty($attributes['remove_avatar'])) {
                $user->clearMediaCollection('avatar');
            }
        });
    }

    private function updateAvatar(User $user, int $mediaId): void
    {
        $media = Media::query()
            ->whereKey($mediaId)
            ->where('model_type', $user::class)
            ->where('model_id', $user->id)
            ->whereIn('collection_name', ['temporary', 'avatar'])
            ->first();

        if ($media === null) {
            return;
        }

        if ($media->collection_name === 'avatar') {
            return;
        }

        $user->clearMediaCollection('avatar');
        $media->copy($user, 'avatar');
        $media->delete();
    }
}
