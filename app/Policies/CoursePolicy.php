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
        return $user->hasPermissionTo(PermissionEnum::VIEW_ANY_COURSES);
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Course $course): bool
    {
        // If user has general permission to view any courses
        if ($user->hasPermissionTo(PermissionEnum::VIEW_ANY_COURSES)) {
            return true;
        }

        // If user has permission to view course
        if ($user->hasPermissionTo(PermissionEnum::VIEW_COURSE)) {
            // If User is the teacher of the course
            if ($course->teacher_id === $user->id) {
                return true;
            }

            // If the User is enrolled in the course
            return $course->students()->where('user_id', $user->id)->exists();
        }

        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo(PermissionEnum::CREATE_COURSE);
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Course $course): bool
    {
        // If user has general permission to view any courses (admin)
        if ($user->hasPermissionTo(PermissionEnum::VIEW_ANY_COURSES)) {
            return true;
        }

        // If user has permission to update course
        if ($user->hasPermissionTo(PermissionEnum::UPDATE_COURSE)) {
            // Teacher can only update their own courses
            return $course->teacher_id === $user->id;
        }

        return false;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Course $course): bool
    {
        return $user->hasPermissionTo(PermissionEnum::DELETE_COURSE);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Course $course): bool
    {
        return $user->hasPermissionTo(PermissionEnum::RESTORE_COURSE);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Course $course): bool
    {
        return $user->hasPermissionTo(PermissionEnum::FORCE_DELETE_COURSE);
    }

    /**
     * Determine whether the user can manage course content.
     */
    public function manageContent(User $user, Course $course): bool
    {
        // If user has general permission to view any courses (admin)
        if ($user->hasPermissionTo(PermissionEnum::VIEW_ANY_COURSES)) {
            return true;
        }

        // If user has permission to manage course content
        if ($user->hasPermissionTo(PermissionEnum::MANAGE_COURSE_CONTENT)) {
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
        if ($user->hasPermissionTo(PermissionEnum::VIEW_ANY_COURSES)) {
            return true;
        }

        // If user has permission to view course content
        if ($user->hasPermissionTo(PermissionEnum::VIEW_COURSE_CONTENT)) {
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
