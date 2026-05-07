<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Quiz;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'users' => User::count(),
            'courses' => Course::count(),
            'classes' => \App\Models\CourseClass::count(),
            'assignments' => Assignment::count(),
            'submissions' => Submission::count(),
            'quizzes' => Quiz::count(),
            'discussions' => \App\Models\Discussion::count(),
        ];

        // Simple activity log using the latest users, courses, assignments created
        $recentUsers = User::latest()->take(5)->get();
        $recentCourses = Course::latest()->take(5)->get();

        return view('admin.dashboard', compact('stats', 'recentUsers', 'recentCourses'));
    }
}
