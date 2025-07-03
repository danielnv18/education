<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class CourseContentController extends Controller
{
    public function index(Course $course): Response
    {
        $course->load('modules', 'modules.lessons', 'modules.lessons');

        return Inertia::render('courses/content', [
            'course' => $course,
            'modules' => $course->modules,
        ]);
    }

    public function update(Request $request, Course $course, $contentId)
    {
        // Validate and update course content
    }

    public function destroy(Course $course, $contentId)
    {
        // Delete specific content for the course
    }
}
