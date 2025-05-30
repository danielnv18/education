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
     * @param  array<string, mixed>  $data
     */
    public function handle(array $data): Course
    {
        return DB::transaction(function () use ($data): Course {
            return Course::query()->create($data);
        });
    }
}
