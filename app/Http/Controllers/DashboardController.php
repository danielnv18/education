<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

final class DashboardController extends Controller
{
    /**
     * Handle the incoming request.
     */
    public function __invoke(Request $request): Response
    {
        // Ensure the user is authenticated
        $request->validate([
            'user' => 'required|exists:users,id',
        ]);

        $user = $request->user();

        // Get courses where the user is a teacher
        $teachingCourses = Course::query()
            ->where('teacher_id', $user->id)
            ->with(['thumbnail'])
            ->latest()
            ->get();

        // Get courses where the user is enrolled as a student
        $enrolledCourses = $user->enrolledCourses()
            ->with(['teacher', 'thumbnail'])
            ->latest()
            ->get();

        return Inertia::render('dashboard', [
            'teachingCourses' => $teachingCourses,
            'enrolledCourses' => $enrolledCourses,
        ]);
    }
}
