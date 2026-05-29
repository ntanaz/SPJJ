<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CourseClass;
use App\Models\Submission;
use Illuminate\Support\Facades\DB;

class TeacherGradeRecapController extends Controller
{
    public function index()
    {
        $teacherId = auth()->id();
        $classes = CourseClass::where('teacher_id', $teacherId)->with('course')->get();
        return view('teacher.grade_recap', compact('classes'));
    }

    public function export(Request $request)
    {
        $classId = $request->course_class_id;
        $class = CourseClass::with(['course.assignments', 'course.quizzes'])->findOrFail($classId);
        
        // Ensure teacher owns this class
        if ($class->teacher_id !== auth()->id()) {
            abort(403);
        }

        $students = DB::table('class_user')
            ->join('users', 'class_user.user_id', '=', 'users.id')
            ->where('class_user.course_class_id', $classId)
            ->select('users.id', 'users.name')
            ->get();

        $assignments = $class->course->assignments;
        $quizzes = $class->course->quizzes;

        $fileName = 'rekap_nilai_' . str_replace(' ', '_', $class->name) . '.csv';
        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $columns = array('Nama Siswa');
        foreach ($assignments as $assignment) {
            $columns[] = 'Tugas: ' . $assignment->title;
        }
        foreach ($quizzes as $quiz) {
            $columns[] = 'Kuis: ' . $quiz->title;
        }
        $columns[] = 'Rata-rata';

        $callback = function() use($students, $assignments, $quizzes, $columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);

            foreach ($students as $student) {
                $row['Nama Siswa']  = $student->name;
                $totalScore = 0;
                $count = 0;

                foreach ($assignments as $assignment) {
                    $submission = Submission::where('assignment_id', $assignment->id)
                                            ->where('user_id', $student->id)
                                            ->first();
                    $score = $submission ? $submission->grade : 0;
                    $row['Tugas: ' . $assignment->title] = $score;
                    $totalScore += $score;
                    $count++;
                }

                foreach ($quizzes as $quiz) {
                    $attempt = \App\Models\QuizAttempt::where('quiz_id', $quiz->id)
                                            ->where('user_id', $student->id)
                                            ->where('status', 'completed')
                                            ->latest()
                                            ->first();
                    $score = $attempt ? $attempt->score : 0;
                    $row['Kuis: ' . $quiz->title] = $score;
                    $totalScore += $score;
                    $count++;
                }

                $row['Rata-rata'] = $count > 0 ? round($totalScore / $count, 2) : 0;

                fputcsv($file, array_values($row));
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }
}

