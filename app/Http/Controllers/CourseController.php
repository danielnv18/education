<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Courses\CreateCourseAction;
use App\Actions\Courses\DeleteCourseAction;
use App\Actions\Courses\UpdateCourseAction;
use App\Http\Requests\Courses\CreateCourseRequest;
use App\Http\Requests\Courses\UpdateCourseRequest;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

final class CourseController extends Controller
{
    /**
     * Display a listing of the courses.
     */
    public function index(): Response
    {
        Gate::authorize('viewAny', Course::class);

        $courses = Course::query()
            ->with(['teacher', 'thumbnail'])
            ->latest()
            ->paginate(10);

        return Inertia::render('courses/index', [
            'courses' => $courses,
        ]);
    }

    /**
     * Show the form for creating a new course.
     */
    public function create(): Response
    {
        Gate::authorize('create', Course::class);

        $teachers = User::query()
            ->whereHas('roles', function ($query): void {
                $query->where('name', 'teacher');
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('courses/create', [
            'teachers' => $teachers,
        ]);
    }

    /**
     * Store a newly created course in storage.
     */
    public function store(CreateCourseRequest $request, CreateCourseAction $action): RedirectResponse
    {
        Gate::authorize('create', Course::class);

        $course = $action->handle($request->validated());

        return to_route('courses.show', $course)
            ->with('success', 'Course created successfully.');
    }

    /**
     * Display the specified course.
     */
    public function show(Course $course): Response
    {
        Gate::authorize('view', $course);

        $course->load(['teacher', 'thumbnail', 'modules.lessons']);

        return Inertia::render('courses/show', [
            'course' => $course,
        ]);
    }

    /**
     * Show the form for editing the specified course.
     */
    public function edit(Course $course): Response
    {
        Gate::authorize('update', $course);

        $course->load(['teacher', 'thumbnail']);

        $teachers = User::query()
            ->whereHas('roles', function ($query): void {
                $query->where('name', 'teacher');
            })
            ->orderBy('name')
            ->get(['id', 'name']);

        return Inertia::render('courses/edit', [
            'course' => $course,
            'teachers' => $teachers,
        ]);
    }

    /**
     * Update the specified course in storage.
     */
    public function update(UpdateCourseRequest $request, Course $course, UpdateCourseAction $action): RedirectResponse
    {
        Gate::authorize('update', $course);

        $action->handle($course, $request->validated());

        return to_route('courses.show', $course)
            ->with('success', 'Course updated successfully.');
    }

    /**
     * Remove the specified course from storage.
     */
    public function destroy(Course $course, DeleteCourseAction $action): RedirectResponse
    {
        Gate::authorize('delete', $course);

        $action->handle($course);

        return to_route('courses.index')
            ->with('success', 'Course deleted successfully.');
    }
}
