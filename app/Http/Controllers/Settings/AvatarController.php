<?php

declare(strict_types=1);

namespace App\Http\Controllers\Settings;

use App\Actions\Users\DeleteAvatarAction;
use App\Actions\Users\UpdateAvatarAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Settings\AvatarUploadRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

final class AvatarController extends Controller
{
    /**
     * Update the user's avatar.
     */
    public function update(AvatarUploadRequest $request, UpdateAvatarAction $action): RedirectResponse
    {
        Gate::authorize('update', $request->user());
        /** @phpstan-ignore-next-line  */
        $action->handle($request->user(), $request->file('avatar'));

        return to_route('profile.edit')->with('status', 'avatar-updated');
    }

    /**
     * Remove the user's avatar.
     */
    public function destroy(AvatarUploadRequest $request, DeleteAvatarAction $action): RedirectResponse
    {
        Gate::authorize('update', $request->user());
        /** @phpstan-ignore-next-line  */
        $action->handle($request->user());

        return to_route('profile.edit')->with('status', 'avatar-removed');
    }
}
