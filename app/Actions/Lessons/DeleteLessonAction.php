<?php

declare(strict_types=1);

namespace App\Actions\Lessons;

use App\Models\Lesson;
use Illuminate\Support\Facades\DB;

final readonly class DeleteLessonAction
{
    /**
     * Execute the action.
     */
    public function handle(Lesson $lesson): void
    {
        DB::transaction(fn () => $lesson->delete());
    }
}
