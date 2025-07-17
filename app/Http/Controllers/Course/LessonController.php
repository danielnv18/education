<?php

declare(strict_types=1);

namespace App\Http\Controllers\Course;

use App\Actions\Lessons\CreateLessonAction;
use App\Actions\Lessons\DeleteLessonAction;
use App\Actions\Lessons\UpdateLessonAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lessons\CreateLessonRequest;
use App\Http\Requests\Lessons\UpdateLessonRequest;
use App\Models\Course;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

final class LessonController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateLessonRequest $request, Course $course, CreateLessonAction $action): RedirectResponse
    {
        Gate::authorize('manageContent', $course);

        $data = $request->validated();
        $action->handle($data);

        return to_route('courses.content.index', ['course' => $course->id])
            ->with('success', 'Lesson created successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLessonRequest $request, Course $course, Lesson $lesson, UpdateLessonAction $action): RedirectResponse
    {
        Gate::authorize('manageContent', $course);

        $data = $request->validated();
        $action->handle($lesson, $data);

        return to_route('courses.content.index', ['course' => $course->id])
            ->with('success', 'Lesson updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course, Lesson $lesson, DeleteLessonAction $action): RedirectResponse
    {
        Gate::authorize('manageContent', $course);

        $action->handle($lesson);

        return to_route('courses.content.index', ['course' => $course->id])
            ->with('success', 'Lesson updated successfully');
    }
}
