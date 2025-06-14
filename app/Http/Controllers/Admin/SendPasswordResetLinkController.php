<?php

declare(strict_types=1);

namespace App\Http\Controllers\Admin;

use App\Actions\Users\SendPasswordResetLinkAction;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;

final class SendPasswordResetLinkController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(User $user, SendPasswordResetLinkAction $action): RedirectResponse
    {
        $action->handle($user);

        return back()->with('success', 'Password reset link sent successfully.');
    }
}
