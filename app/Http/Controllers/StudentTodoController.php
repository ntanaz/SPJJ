<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\Quiz;
use App\Models\CourseClass;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentTodoController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        $enrolledClassIds = DB::table('class_user')->where('user_id', $user->id)->pluck('course_class_id');
        $enrolledCourseIds = CourseClass::whereIn('id', $enrolledClassIds)->pluck('course_id');

        $filter = $request->get('filter', 'pending'); // pending, completed, overdue, all

        $assignments = Assignment::with(['course', 'submissions' => function($q) use ($user) {
            $q->where('user_id', $user->id);
        }])->whereIn('course_id', $enrolledCourseIds)->get();

        $quizzes = Quiz::with(['course', 'attempts' => function($q) use ($user) {
            $q->where('user_id', $user->id);
        }])->whereIn('course_id', $enrolledCourseIds)->get();

        $todos = collect();

        foreach ($assignments as $assignment) {
            $isCompleted = $assignment->submissions->isNotEmpty();
            $isOverdue = Carbon::now()->isAfter($assignment->deadline) && !$isCompleted;
            
            $status = 'pending';
            if ($isCompleted) $status = 'completed';
            elseif ($isOverdue) $status = 'overdue';

            $todos->push([
                'id' => 'assignment_'.$assignment->id,
                'type' => 'assignment',
                'title' => $assignment->title,
                'course' => $assignment->course->name,
                'deadline' => $assignment->deadline,
                'status' => $status,
                'url' => route('student.assignments.show', $assignment),
                'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                'color' => 'pink'
            ]);
        }

        foreach ($quizzes as $quiz) {
            $isCompleted = $quiz->attempts->isNotEmpty();
            $isOverdue = $quiz->deadline && Carbon::now()->isAfter($quiz->deadline) && !$isCompleted;
            
            $status = 'pending';
            if ($isCompleted) $status = 'completed';
            elseif ($isOverdue) $status = 'overdue';

            $todos->push([
                'id' => 'quiz_'.$quiz->id,
                'type' => 'quiz',
                'title' => $quiz->title,
                'course' => $quiz->course->name,
                'deadline' => $quiz->deadline,
                'status' => $status,
                'url' => route('student.quizzes.show', $quiz),
                'icon' => 'M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z',
                'color' => 'amber'
            ]);
        }

        // Apply filter
        if ($filter !== 'all') {
            $todos = $todos->filter(function($todo) use ($filter) {
                return $todo['status'] === $filter;
            });
        }

        // Sort by deadline
        $todos = $todos->sortBy(function($todo) {
            return $todo['deadline'] ? Carbon::parse($todo['deadline'])->timestamp : 9999999999;
        })->values();

        return view('student.todos.index', compact('todos', 'filter'));
    }
}
