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
     * @param  Collection<User>  $students  A collection of students to enroll
     */
    public function handle(Course $course, Collection $students): void
    {
        DB::transaction(function () use ($course, $students): void {
            // Assign a student role to all students who don't have it
            $studentsWithoutRole = $students->reject(fn (User $student): bool => $student->hasRole(UserRole::STUDENT));

            foreach ($studentsWithoutRole as $student) {
                $student->assignRole(UserRole::STUDENT);
            }

            // Get existing student IDs to maintain their enrollment
            $existingStudentIds = $course->students()->pluck('user_id')->toArray();

            // Prepare enrollment data for all students
            $enrollmentData = [];
            foreach ($students as $student) {
                $enrollmentData[$student->id] = [
                    'status' => EnrollmentStatus::ACTIVE->value,
                    'enrolled_at' => now(),
                ];
            }

            // Merge with existing students to avoid removing them
            foreach ($existingStudentIds as $id) {
                if (! isset($enrollmentData[$id])) {
                    $existingPivot = $course->students()->where('user_id', $id)->first()->pivot;
                    $enrollmentData[$id] = [
                        'status' => $existingPivot->status,
                        'enrolled_at' => $existingPivot->enrolled_at,
                    ];
                }
            }

            // Sync the enrollments
            $course->students()->sync($enrollmentData);
        });
    }
}
