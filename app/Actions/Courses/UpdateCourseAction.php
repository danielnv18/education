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
            // Check if cover field exists in the data (to distinguish between not provided and explicitly null)
            $coverProvided = array_key_exists('cover', $data);
            $cover = $data['cover'] ?? null;

            unset($data['cover']);

            $course->update($data);

            // Handle cover image
            if ($coverProvided) {
                if ($cover === null) {
                    // Cover was explicitly set to null, so remove the existing cover
                    $course->clearMediaCollection('cover');
                } elseif ($cover) {
                    // A new cover was provided, replace the existing one
                    $course->clearMediaCollection('cover');
                    $course->addMedia($cover)
                        ->toMediaCollection('cover');
                }
            }

            return $course;
        });
    }
}
