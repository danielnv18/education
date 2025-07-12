<?php

declare(strict_types=1);

namespace App\Actions\Lessons;

use App\Models\Lesson;
use Illuminate\Support\Facades\DB;

final readonly class UpdateLessonAction
{
    /**
     * Execute the action.
     *
     * @param  array<string, string|bool|int>  $data
     */
    public function handle(Lesson $lesson, array $data): Lesson
    {
        return DB::transaction(function () use ($lesson, $data): Lesson {
            $lesson->update($data);
            $lesson->refresh();

            return $lesson;
        });
    }
}
