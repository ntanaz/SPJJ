<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\Quiz;
use App\Models\Attendance;
use App\Models\CourseClass;
use Illuminate\Support\Facades\DB;

class StudentDashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userRole = $user->roles->first()?->name ?? 'siswa';
        
        $announcements = \App\Models\Announcement::whereIn('target_audience', ['all', $userRole])
            ->latest()
            ->take(3)
            ->get();

        if ($user->hasRole('siswa')) {
            // Get student's enrolled courses
            $enrolledClassIds = DB::table('class_user')->where('user_id', $user->id)->pluck('course_class_id');
            $enrolledCourseIds = CourseClass::whereIn('id', $enrolledClassIds)->pluck('course_id');

            $upcomingAssignments = Assignment::with('course')
                ->whereIn('course_id', $enrolledCourseIds)
                ->where('deadline', '>=', now())
                ->orderBy('deadline', 'asc')
                ->take(5)
                ->get();

            $activeQuizzes = Quiz::with('course')
                ->whereIn('course_id', $enrolledCourseIds)
                ->where(function($q) {
                    $q->whereNull('deadline')->orWhere('deadline', '>=', now());
                })
                ->latest()
                ->take(5)
                ->get();

            $recentAssignments = Assignment::with('course')
                ->whereIn('course_id', $enrolledCourseIds)
                ->latest()
                ->take(5)
                ->get();

            $todaySchedule = Attendance::with('course')
                ->whereIn('course_id', $enrolledCourseIds)
                ->whereDate('date', now()->toDateString())
                ->orderBy('start_time', 'asc')
                ->get();

            // Progress Belajar (simple metric: submitted assignments / total assignments * 100)
            $totalAssignments = Assignment::whereIn('course_id', $enrolledCourseIds)->count();
            $submittedAssignments = \App\Models\Submission::where('user_id', $user->id)
                ->whereIn('assignment_id', function($q) use ($enrolledCourseIds) {
                    $q->select('id')->from('assignments')->whereIn('course_id', $enrolledCourseIds);
                })->count();

            $progress = $totalAssignments > 0 ? round(($submittedAssignments / $totalAssignments) * 100) : 0;

            return view('dashboard', compact(
                'upcomingAssignments', 
                'activeQuizzes', 
                'recentAssignments', 
                'todaySchedule', 
                'progress', 
                'announcements'
            ));
        }

        return view('dashboard', compact('announcements'));
    }
}
