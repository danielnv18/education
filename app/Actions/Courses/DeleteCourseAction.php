<?php

declare(strict_types=1);

namespace App\Actions\Courses;

use App\Models\Course;
use Illuminate\Support\Facades\DB;

final class DeleteCourseAction
{
    /**
     * Handle the deletion of a course.
     */
    public function handle(Course $course): void
    {
        DB::transaction(function () use ($course): void {
            $course->delete();
        });
    }
}
