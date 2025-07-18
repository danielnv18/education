<?php

declare(strict_types=1);

namespace App\Http\Controllers\Course;

use App\Data\CourseData;
use App\Data\ModuleData;
use App\Http\Controllers\Controller;
use App\Models\Course;
use Illuminate\Support\Facades\Gate;
use Inertia\Inertia;
use Inertia\Response;

final class CourseContentController extends Controller
{
    public function index(Course $course): Response
    {
        Gate::authorize('manageContent', $course);

        $course->load('modules', 'modules.lessons');

        return Inertia::render('courses/content', [
            'course' => CourseData::from($course),
            'modules' => ModuleData::collect($course->modules),
        ]);
    }
}
