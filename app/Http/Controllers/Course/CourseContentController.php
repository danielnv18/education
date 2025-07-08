<?php

declare(strict_types=1);

namespace App\Http\Controllers\Course;

use App\Http\Controllers\Controller;
use App\Models\Course;
use Inertia\Inertia;
use Inertia\Response;

final class CourseContentController extends Controller
{
    public function index(Course $course): Response
    {
        $course->load('modules', 'modules.lessons');

        return Inertia::render('courses/content', [
            'course' => $course,
            'modules' => $course->modules,
        ]);
    }
}
