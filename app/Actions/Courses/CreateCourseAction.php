<?php

declare(strict_types=1);

namespace App\Actions\Courses;

use App\Models\Course;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

final readonly class CreateCourseAction
{
    /**
     * Handle the creation of a new course.
     *
     * @param  array<string, string|bool|int|UploadedFile>  $data
     */
    public function handle(array $data): Course
    {
        return DB::transaction(function () use ($data): Course {
            // Extract cover image from data if present
            /** @var UploadedFile|null $cover */
            $cover = $data['cover'] ?? null;

            unset($data['cover']);

            $course = Course::query()->create($data);

            // Add a cover image if provided
            if ($cover) {
                $course->addMedia($cover)
                    ->toMediaCollection('cover');
            }

            $course->refresh();

            return $course;
        });
    }
}
