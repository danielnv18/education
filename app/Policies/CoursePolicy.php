<?php

declare(strict_types=1);

namespace App\Policies;

use App\Enums\PermissionEnum;
use App\Models\Course;
use App\Models\User;

final class CoursePolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::ViewAnyCourses);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): bool
    {
        // If the user has general permission to view any courses
        if ($user->hasPermissionTo(PermissionEnum::ViewAnyCourses)) {
            return true;
        }

        // If User is the teacher of the course
        if ($course->teacher_id === $user->id) {
            return true;
        }

        if ($course->students()->where('user_id', $user->id)->exists()) {
            return true;
        }

        return $user->hasPermissionTo(PermissionEnum::ViewCourse);
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::CreateCourse);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): bool
    {
        // If user has general permission to view any courses (admin)
        if ($user->hasPermissionTo(PermissionEnum::ViewAnyCourses)) {
            return true;
        }

        if ($course->teacher_id === $user->id) {
            return true;
        }

        return $user->hasPermissionTo(PermissionEnum::UpdateCourse);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::DeleteCourse);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::RestoreCourse);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::ForceDeleteCourse);
    }

    /**
     * Determine whether the user can manage course content.
     */
    public function manageContent(User $user, Course $course): bool
    {
        // If the user has general permission to view any courses (admin)
        if ($user->hasPermissionTo(PermissionEnum::ViewAnyCourses)) {
            return true;
        }

        // If the user has permission to manage course content
        if ($user->hasPermissionTo(PermissionEnum::ManageCourseContent)) {
            // Teacher can only manage their own course content
            return $course->teacher_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can view course content.
     */
    public function viewContent(User $user, Course $course): bool
    {
        // If user has general permission to view any courses (admin)
        if ($user->hasPermissionTo(PermissionEnum::ViewAnyCourses)) {
            return true;
        }

        // If user has permission to view course content
        if ($user->hasPermissionTo(PermissionEnum::ViewCourseContent)) {
            // Teacher can only view content of their own courses
            if ($course->teacher_id === $user->id) {
                return true;
            }

            // Students can only view content of courses they're enrolled in
            return $course->students()->where('user_id', $user->id)->exists();
        }

        return false;
    }
}
