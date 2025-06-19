<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Courses\EnrollStudentAction;
use App\Http\Requests\Courses\EnrollStudentRequest;
use App\Models\Course;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Gate;

final class EnrollStudentController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(EnrollStudentRequest $request, Course $course, EnrollStudentAction $action): RedirectResponse
    {
        Gate::authorize('manageContent', $course);

        $studentIds = $request->validated('student_ids');
        $students = User::whereIn('id', $studentIds)->get();

        $action->handle($course, $students);

        $count = $students->count();
        $message = $count === 1
            ? '1 student enrolled successfully.'
            : "$count students enrolled successfully.";

        return to_route('courses.students', $course)
            ->with('success', $message);
    }
}
