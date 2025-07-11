<?php

declare(strict_types=1);

namespace App\Actions\Lessons;

use App\Models\Lesson;
use Illuminate\Support\Facades\DB;

final readonly class CreateLessonAction
{
    /**
     * Handle the creation of a module.
     *
     * @param  array<string, string|bool|int>  $data
     */
    public function handle(array $data): Lesson
    {
        return DB::transaction(fn (): Lesson => Lesson::query()->create($data));
    }
}
