<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Data\CourseData;
use App\Models\Course;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

final class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     *
     * @throws Throwable
     */
    public function __invoke(Request $request): Response
    {
        $user = $request->user();
        throw_if(
            ! $user,
            new AuthenticationException('Unauthenticated.')
        );

        // Get courses where the user is a teacher
        $teachingCourses = Course::query()
            ->where('teacher_id', $user->id)
            ->latest()
            ->get();

        // Get courses where the user is enrolled as a student
        $enrolledCourses = $user->enrolledCourses()
            ->with(['teacher'])
            ->latest()
            ->get();

        return Inertia::render('dashboard', [
            'teachingCourses' => CourseData::collect($teachingCourses),
            'enrolledCourses' => CourseData::collect($enrolledCourses),
        ]);
    }
}
