<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\Course;
use Illuminate\Http\Request;

class QuizController extends Controller
{
    public function index()
    {
        $quizzes = Quiz::with('course')->latest()->paginate(15);
        return view('quizzes.index', compact('quizzes'));
    }

    public function create()
    {
        $courses = Course::with('modules')->get();
        return view('quizzes.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'module_id' => 'required|exists:modules,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
        ]);

        Quiz::create($request->all());

        return redirect()->route('quizzes.index')->with('success', 'Kuis/Ujian berhasil dibuat.');
    }

    public function show(Quiz $quiz)
    {
        $quiz->load('questions');
        return view('quizzes.show', compact('quiz'));
    }

    public function destroy(Quiz $quiz)
    {
        $quiz->delete();
        return redirect()->route('quizzes.index')->with('success', 'Kuis/Ujian berhasil dihapus.');
    }

    public function storeQuestion(Request $request, Quiz $quiz)
    {
        $request->validate([
            'question_type' => 'required|in:multiple_choice,true_false,short_answer,fill_blank,reflection,debugging,interactive_video',
            'question' => 'required|string',
            'points' => 'required|integer|min:1',
            'feedback' => 'nullable|string',
        ]);
        
        $type = $request->question_type;
        $options = null;
        $correctAnswer = '';

        if ($type === 'multiple_choice') {
            $request->validate([
                'option_a' => 'required|string',
                'option_b' => 'required|string',
                'option_c' => 'required|string',
                'option_d' => 'required|string',
                'correct_answer' => 'required|in:A,B,C,D',
            ]);
            $options = [
                'A' => $request->option_a,
                'B' => $request->option_b,
                'C' => $request->option_c,
                'D' => $request->option_d,
            ];
            $correctAnswer = $request->correct_answer;
        } elseif ($type === 'true_false') {
            $request->validate([
                'correct_answer' => 'required|in:A,B',
            ]);
            $options = [
                'A' => 'Benar',
                'B' => 'Salah',
            ];
            $correctAnswer = $request->correct_answer;
        } elseif ($type === 'short_answer') {
            $request->validate([
                'correct_answer' => 'required|string',
            ]);
            $correctAnswer = $request->correct_answer;
        } elseif ($type === 'fill_blank') {
            $request->validate([
                'correct_answer' => 'required|string',
            ]);
            $correctAnswer = $request->correct_answer;
        } elseif ($type === 'reflection') {
            $correctAnswer = '';
        } elseif ($type === 'debugging') {
            $request->validate([
                'correct_answer' => 'required|string',
            ]);
            $correctAnswer = $request->correct_answer;
        } elseif ($type === 'interactive_video') {
            $request->validate([
                'video_url' => 'required|string',
                'timestamp' => 'required|integer|min:0',
                'video_question_type' => 'required|in:multiple_choice,true_false,short_answer',
            ]);

            $options = [
                'video_url' => $request->video_url,
                'timestamp' => intval($request->timestamp),
                'video_question_type' => $request->video_question_type,
            ];

            if ($request->video_question_type === 'multiple_choice') {
                $request->validate([
                    'option_a' => 'required|string',
                    'option_b' => 'required|string',
                    'option_c' => 'required|string',
                    'option_d' => 'required|string',
                    'correct_answer' => 'required|in:A,B,C,D',
                ]);
                $options['options'] = [
                    'A' => $request->option_a,
                    'B' => $request->option_b,
                    'C' => $request->option_c,
                    'D' => $request->option_d,
                ];
                $correctAnswer = $request->correct_answer;
            } elseif ($request->video_question_type === 'true_false') {
                $request->validate([
                    'correct_answer' => 'required|in:A,B',
                ]);
                $options['options'] = [
                    'A' => 'Benar',
                    'B' => 'Salah',
                ];
                $correctAnswer = $request->correct_answer;
            } else {
                $request->validate([
                    'correct_answer' => 'required|string',
                ]);
                $correctAnswer = $request->correct_answer;
            }
        }

        $quiz->questions()->create([
            'question_type' => $type,
            'question' => $request->question,
            'options' => $options,
            'correct_answer' => $correctAnswer,
            'feedback' => $request->feedback,
            'points' => $request->points ?? 10,
        ]);

        return back()->with('success', 'Soal berhasil ditambahkan ke Kuis.');
    }

    public function destroyQuestion(\App\Models\QuizQuestion $question)
    {
        $question->delete();
        return back()->with('success', 'Soal berhasil dihapus.');
    }
}
