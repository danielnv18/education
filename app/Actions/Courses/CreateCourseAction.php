<?php

declare(strict_types=1);

namespace App\Actions\Courses;

use App\Models\Course;
use Illuminate\Support\Facades\DB;

final class CreateCourseAction
{
    /**
     * Handle the creation of a new course.
     *
     * @param  array<string, string|bool|int>  $data
     */
    public function handle(array $data): Course
    {
        return DB::transaction(fn (): Course => Course::query()->create($data));
    }
}
