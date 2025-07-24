<?php

declare(strict_types=1);

namespace App\Actions\Courses;

use App\Models\Course;
use Illuminate\Support\Facades\DB;

final readonly class UpdateCourseAction
{
    /**
     * Handle the update of a course.
     *
     * @param  array<string, mixed>  $data
     */
    public function handle(Course $course, array $data): Course
    {
        return DB::transaction(function () use ($course, $data): Course {
            $course->update($data);

            return $course;
        });
    }
}
