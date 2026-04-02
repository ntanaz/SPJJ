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
        $courses = Course::all();
        return view('quizzes.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
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
            'question' => 'required|string',
            'option_a' => 'required|string',
            'option_b' => 'required|string',
            'option_c' => 'required|string',
            'option_d' => 'required|string',
            'correct_answer' => 'required|in:A,B,C,D',
        ]);

        $quiz->questions()->create([
            'question' => $request->question,
            'options' => [
                'A' => $request->option_a,
                'B' => $request->option_b,
                'C' => $request->option_c,
                'D' => $request->option_d,
            ],
            'correct_answer' => $request->correct_answer,
        ]);

        return back()->with('success', 'Soal berhasil ditambahkan ke Kuis.');
    }

    public function destroyQuestion(\App\Models\QuizQuestion $question)
    {
        $question->delete();
        return back()->with('success', 'Soal berhasil dihapus.');
    }
}
