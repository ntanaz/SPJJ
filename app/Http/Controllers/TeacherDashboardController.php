<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Course;
use App\Models\Assignment;
use App\Models\Submission;
use App\Models\Discussion;

class TeacherDashboardController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();
        
        // Find courses where this user is the teacher via course_classes
        // Note: Assuming CourseClass has teacher_id and Course has many CourseClasses
        // Or if we simplified it and added teacher_id to course, we can use that.
        // But since we didn't add teacher_id to course, let's use CourseClass.
        $classes = \App\Models\CourseClass::where('teacher_id', $teacherId)->with('course')->get();
        $courseIds = $classes->pluck('course_id')->unique();

        $stats = [
            'total_classes' => $classes->count(),
            'total_students' => \Illuminate\Support\Facades\DB::table('class_user')->whereIn('course_class_id', $classes->pluck('id'))->count(),
            'pending_assignments' => Submission::whereIn('assignment_id', function($query) use ($courseIds) {
                $query->select('id')->from('assignments')->whereIn('course_id', $courseIds);
            })->whereNull('grade')->count(),
        ];

        $recentSubmissions = Submission::whereIn('assignment_id', function($query) use ($courseIds) {
                $query->select('id')->from('assignments')->whereIn('course_id', $courseIds);
            })
            ->with(['user', 'assignment'])
            ->latest()
            ->take(5)
            ->get();

        $recentDiscussions = Discussion::whereIn('course_id', $courseIds)
            ->with(['user', 'course'])
            ->latest()
            ->take(5)
            ->get();

        $upcomingDeadlines = Assignment::whereIn('course_id', $courseIds)
            ->where('deadline', '>', now())
            ->orderBy('deadline', 'asc')
            ->take(5)
            ->get();

        return view('teacher.dashboard', compact('stats', 'recentSubmissions', 'recentDiscussions', 'classes', 'upcomingDeadlines'));
    }
}
