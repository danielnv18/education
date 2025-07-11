<?php

declare(strict_types=1);

namespace App\Actions\Courses;

use App\Enums\UserRole;
use App\Models\Course;
use App\Models\User;
use Illuminate\Support\Facades\DB;

final class CreateCourseEnrollmentAction
{
    /**
     * Enroll students in a course.
     *
     * @param  Course  $course  The course to enroll students in
     * @param  array<int>  $studentIds
     */
    public function handle(Course $course, array $studentIds): void
    {
        DB::transaction(function () use ($course, $studentIds): void {
            $students = User::whereIn('id', $studentIds)->get();

            if ($students->isEmpty()) {
                return; // No students to enroll
            }

            // Assign a student role to all students who don't have it
            $studentsWithoutRole = $students->reject(fn (User $student): bool => $student->hasRole(UserRole::STUDENT));

            // Assign the student role to those who don't have it
            $studentsWithoutRole->each(fn (User $student) => $student->assignRole(UserRole::STUDENT));

            // Sync the enrollments
            $course->students()->syncWithoutDetaching($students->pluck('id')->toArray());
        });
    }
}
