<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Submission;
use App\Models\QuizAttempt;
use App\Models\AttendanceRecord;
use App\Models\MaterialProgress;
use App\Models\CourseClass;

class AnalyticsController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $isTeacher = $user->hasRole(['guru', 'teacher']);
        
        $courseIds = [];
        if ($isTeacher) {
            $courseIds = CourseClass::where('teacher_id', $user->id)->pluck('course_id');
        } else {
            // Admin sees all
            $courseIds = \App\Models\Course::pluck('id');
        }

        $analytics = [
            'assignment_completion' => Submission::whereIn('assignment_id', function($q) use ($courseIds) {
                $q->select('id')->from('assignments')->whereIn('course_id', $courseIds);
            })->count(),
            'late_submissions' => Submission::where('is_late', true)->whereIn('assignment_id', function($q) use ($courseIds) {
                $q->select('id')->from('assignments')->whereIn('course_id', $courseIds);
            })->count(),
            'quiz_attempts' => QuizAttempt::whereIn('quiz_id', function($q) use ($courseIds) {
                $q->select('id')->from('quizzes')->whereIn('course_id', $courseIds);
            })->count(),
            'avg_quiz_score' => QuizAttempt::whereIn('quiz_id', function($q) use ($courseIds) {
                $q->select('id')->from('quizzes')->whereIn('course_id', $courseIds);
            })->avg('score') ?? 0,
            'attendance_rate' => AttendanceRecord::whereHas('attendance', function($q) use ($courseIds) {
                $q->whereIn('course_id', $courseIds);
            })->where('status', 'hadir')->count(),
            'material_views' => MaterialProgress::whereIn('material_id', function($q) use ($courseIds) {
                $q->select('id')->from('materials')->whereIn('course_id', $courseIds);
            })->count(),
        ];

        return view('analytics.index', compact('analytics'));
    }
}
