<?php

declare(strict_types=1);

namespace App\Actions\Courses;

use App\Enums\EnrollmentStatus;
use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

final class EnrollStudentAction
{
    /**
     * Enroll students in a course.
     *
     * @param  Course  $course  The course to enroll students in
     * @param  Collection<int, User>  $students  A collection of students to enroll
     */
    public function handle(Course $course, Collection $students): void
    {
        DB::transaction(function () use ($course, $students): void {
            // Assign a student role to all students who don't have it
            $studentsWithoutRole = $students->reject(fn (User $student): bool => $student->hasRole(UserRole::STUDENT));

            $studentsWithoutRole->each(fn (User $student) => $student->assignRole(UserRole::STUDENT));

            // Sync the enrollments
            $now = now();
            $course->students()->sync(
                $students->mapWithKeys(fn (User $student) => [
                    $student->id => [
                        'status' => EnrollmentStatus::ACTIVE,
                        'enrolled_at' => $now,
                    ],
                ])->toArray()
            );
        });
    }
}
