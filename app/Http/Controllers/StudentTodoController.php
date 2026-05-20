<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Assignment;
use App\Models\Quiz;
use App\Models\CourseClass;
use App\Models\Submission;
use App\Models\QuizAttempt;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class StudentTodoController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Check if teacher or admin
        if ($user->hasRole(['guru', 'teacher', 'admin'])) {
            // Teacher View
            if ($user->hasRole('admin')) {
                $classes = CourseClass::with(['course', 'students'])->get();
            } else {
                $classes = CourseClass::where('teacher_id', $user->id)->with(['course', 'students'])->get();
            }

            $groupedData = [];

            foreach ($classes as $class) {
                $assignments = Assignment::where('course_id', $class->course_id)
                    ->withCount('submissions')
                    ->get()
                    ->map(function($assignment) use ($class) {
                        // Count pending grading
                        $pendingGrading = Submission::where('assignment_id', $assignment->id)
                            ->whereNull('grade')
                            ->count();
                        return [
                            'id' => $assignment->id,
                            'title' => $assignment->title,
                            'deadline' => $assignment->deadline,
                            'total_submissions' => $assignment->submissions_count,
                            'pending_grading' => $pendingGrading,
                            'total_students' => $class->students->count(),
                            'url' => route('teacher.assignments.submissions', $assignment),
                        ];
                    });

                $quizzes = Quiz::where('course_id', $class->course_id)
                    ->get()
                    ->map(function($quiz) use ($class) {
                        $totalAttempts = QuizAttempt::where('quiz_id', $quiz->id)->count();
                        return [
                            'id' => $quiz->id,
                            'title' => $quiz->title,
                            'deadline' => $quiz->deadline,
                            'total_attempts' => $totalAttempts,
                            'total_students' => $class->students->count(),
                            'url' => route('quizzes.show', $quiz),
                        ];
                    });

                $groupedData[] = [
                    'class_name' => $class->name,
                    'course_name' => $class->course->name,
                    'assignments' => $assignments,
                    'quizzes' => $quizzes,
                ];
            }

            return view('student.todos.index', compact('groupedData', 'user'));
        }

        // Student View
        $enrolledClassIds = DB::table('class_user')->where('user_id', $user->id)->pluck('course_class_id');
        $classes = CourseClass::whereIn('id', $enrolledClassIds)->with(['course'])->get();

        $filter = $request->get('filter', 'pending'); // pending, completed, overdue, all
        $groupedData = [];

        foreach ($classes as $class) {
            $classAssignments = Assignment::with(['submissions' => function($q) use ($user) {
                $q->where('user_id', $user->id);
            }])->where('course_id', $class->course_id)->get();

            $classQuizzes = Quiz::with(['attempts' => function($q) use ($user) {
                $q->where('user_id', $user->id);
            }])->where('course_id', $class->course_id)->get();

            $todos = collect();

            foreach ($classAssignments as $assignment) {
                $isCompleted = $assignment->submissions->isNotEmpty();
                $isOverdue = Carbon::now()->isAfter($assignment->deadline) && !$isCompleted;
                
                $status = 'pending';
                if ($isCompleted) $status = 'completed';
                elseif ($isOverdue) $status = 'overdue';

                $todos->push([
                    'id' => 'assignment_'.$assignment->id,
                    'type' => 'assignment',
                    'title' => $assignment->title,
                    'deadline' => $assignment->deadline,
                    'status' => $status,
                    'url' => route('student.assignments.show', $assignment),
                    'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2',
                    'color' => 'pink'
                ]);
            }

            foreach ($classQuizzes as $quiz) {
                $isCompleted = $quiz->attempts->isNotEmpty();
                $isOverdue = $quiz->deadline && Carbon::now()->isAfter($quiz->deadline) && !$isCompleted;
                
                $status = 'pending';
                if ($isCompleted) $status = 'completed';
                elseif ($isOverdue) $status = 'overdue';

                $todos->push([
                    'id' => 'quiz_'.$quiz->id,
                    'type' => 'quiz',
                    'title' => $quiz->title,
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

            if ($todos->isNotEmpty() || $filter === 'all') {
                $groupedData[] = [
                    'class_name' => $class->name,
                    'course_name' => $class->course->name,
                    'todos' => $todos,
                ];
            }
        }

        return view('student.todos.index', compact('groupedData', 'filter', 'user'));
    }
}
