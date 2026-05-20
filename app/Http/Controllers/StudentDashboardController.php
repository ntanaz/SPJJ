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

            // Gamification & Modular Progress Integration
            $totalXp = DB::table('student_xp')->where('user_id', $user->id)->value('total_xp') ?? $user->points ?? 0;
            $completedActivitiesCount = DB::table('xp_logs')->where('user_id', $user->id)->count();

            $modulesProgress = DB::table('modules')
                ->whereIn('course_id', $enrolledCourseIds)
                ->leftJoin('student_progress', function($join) use ($user) {
                    $join->on('modules.id', '=', 'student_progress.module_id')
                         ->where('student_progress.user_id', '=', $user->id);
                })
                ->select('modules.id', 'modules.title', 'student_progress.progress_percentage')
                ->get()
                ->map(function($item) {
                    return [
                        'title' => $item->title,
                        'percentage' => $item->progress_percentage ?? 0
                    ];
                });

            $overallProgress = $modulesProgress->count() > 0 
                ? round($modulesProgress->avg('percentage')) 
                : $progress;

            // Badge evaluation
            $badge = 'Pemula';
            $badgeIcon = '🌟';
            if ($totalXp >= 600) {
                $badge = 'Zenith Master';
                $badgeIcon = '🏆';
            } elseif ($totalXp >= 300) {
                $badge = 'Cendekiawan';
                $badgeIcon = '🧠';
            } elseif ($totalXp >= 100) {
                $badge = 'Penjelajah';
                $badgeIcon = '🚀';
            }

            return view('dashboard', compact(
                'upcomingAssignments', 
                'activeQuizzes', 
                'recentAssignments', 
                'todaySchedule', 
                'progress', 
                'totalXp',
                'completedActivitiesCount',
                'modulesProgress',
                'overallProgress',
                'badge',
                'badgeIcon',
                'announcements'
            ));
        }

        return view('dashboard', compact('announcements'));
    }
}
