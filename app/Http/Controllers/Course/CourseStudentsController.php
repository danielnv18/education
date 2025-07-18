<?php

declare(strict_types=1);

namespace App\Http\Controllers\Course;

use App\Actions\Courses\CreateCourseEnrollmentAction;
use App\Data\CourseData;
use App\Data\UserData;
use App\Http\Controllers\Controller;
use App\Http\Requests\Courses\EnrollStudentRequest;
use App\Models\Course;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

final class CourseStudentsController extends Controller
{
    /**
     * Display the student's management page for the course.
     */
    public function index(Course $course): Response
    {
        Gate::authorize('update', $course);

        $course->load(['teacher', 'thumbnail', 'students']);

        // Get users who are not already enrolled in the course
        $availableStudents = User::query()
            ->whereDoesntHave('enrolledCourses', fn (Builder $query) => $query->where('course_id', $course->id))
            ->whereNotIn('id', [$course->teacher_id, auth()->id()]) // Exclude the course teacher and current user
            ->whereNotNull('email_verified_at') // Only include users with verified emails
            ->orderBy('name')
            ->get();

        return Inertia::render('courses/students', [
            'course' => CourseData::from($course),
            'availableStudents' => UserData::collect($availableStudents),
        ]);
    }

    /**
     * Enroll students in the course.
     */
    public function store(EnrollStudentRequest $request, Course $course, CreateCourseEnrollmentAction $action): RedirectResponse
    {
        Gate::authorize('manageContent', $course);

        $action->handle($course, $request->validated('student_ids'));

        $message = 'students enrolled successfully.';

        return to_route('courses.students.index', $course)
            ->with('success', $message);
    }
}
