<?php

declare(strict_types=1);

namespace App\Actions\Lessons;

use Illuminate\Support\Facades\DB;

final readonly class UpdateLessonAction
{
    /**
     * Execute the action.
     */
    public function handle(): void
    {
        DB::transaction(function (): void {
            //
        });
    }
}
