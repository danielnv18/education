<?php

declare(strict_types=1);

namespace App\Http\Controllers\Course;

use App\Actions\Lessons\CreateLessonAction;
use App\Actions\Lessons\DeleteLessonAction;
use App\Actions\Modules\UpdateModuleAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Lessons\CreateLessonRequest;
use App\Http\Requests\Lessons\UpdateLessonRequest;
use App\Models\Lesson;
use Illuminate\Http\RedirectResponse;

final class LessonController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(CreateLessonRequest $request, CreateLessonAction $action): RedirectResponse
    {
        $data = $request->validated();
        $lesson = $action->handle($data);

        $lesson->load('module');

        return to_route('courses.content.index', ['course' => $lesson->module->course_id])
            ->with('success', 'Lesson created successfully');
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateLessonRequest $request, Lesson $lesson, UpdateModuleAction $action): RedirectResponse
    {
        $data = $request->validated();
        $action->handle($lesson, $data);

        return to_route('courses.content.index', ['course' => $lesson->module->course_id])
            ->with('success', 'Lesson updated successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Lesson $lesson, DeleteLessonAction $action): RedirectResponse
    {
        $courseId = $lesson->module->course_id;
        $action->handle($lesson);

        return to_route('courses.content.index', ['course' => $courseId])
            ->with('success', 'Lesson updated successfully');
    }
}
