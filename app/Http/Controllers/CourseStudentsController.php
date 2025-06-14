<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

final class CourseStudentsController extends Controller
{
    /**
     * Display the students management page for the course.
     */
    public function __invoke(Course $course): Response
    {
        Gate::authorize('update', $course);

        $course->load(['teacher', 'thumbnail', 'students']);

        // Get users who are not already enrolled in the course
        $availableStudents = User::query()
            ->whereDoesntHave('enrolledCourses', function (Builder $query) use ($course): void {
                $query->where('course_id', $course->id);
            })
            ->orderBy('name')
            ->get(['id', 'name', 'email']);

        return Inertia::render('courses/students', [
            'course' => $course,
            'availableStudents' => $availableStudents,
        ]);
    }
}
