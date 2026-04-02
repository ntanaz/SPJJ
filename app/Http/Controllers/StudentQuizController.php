<?php

namespace App\Http\Controllers;

use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizAnswer;
use Illuminate\Http\Request;

class StudentQuizController extends Controller
{
    public function show(Quiz $quiz)
    {
        $quiz->load('questions');
        $attempt = $quiz->attempts()->where('user_id', auth()->id())->latest()->first();
        
        return view('student.quizzes.show', compact('quiz', 'attempt'));
    }

    public function start(Quiz $quiz)
    {
        // Check if already attempted
        $attempt = $quiz->attempts()->where('user_id', auth()->id())->first();
        
        if (!$attempt) {
            $attempt = $quiz->attempts()->create([
                'user_id' => auth()->id(),
                'score' => 0
            ]);
        }

        return redirect()->route('student.quizzes.attempt', $attempt);
    }

    public function attempt(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Sesi kuis tidak valid.');
        }

        $quiz = $attempt->quiz()->with('questions')->first();
        
        return view('student.quizzes.attempt', compact('quiz', 'attempt'));
    }

    public function submit(Request $request, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        $quiz = $attempt->quiz()->with('questions')->first();
        $answers = $request->input('answers', []);
        
        $totalScore = 0;
        $maxScore = $quiz->questions->sum('points');
        
        $pointsEarned = 0;

        foreach ($quiz->questions as $question) {
            $selected = $answers[$question->id] ?? null;
            $isCorrect = $selected === $question->correct_answer;
            
            if ($isCorrect) {
                $pointsEarned += $question->points;
            }
            
            $attempt->answers()->updateOrCreate(
                ['quiz_question_id' => $question->id],
                [
                    'selected_option' => $selected,
                    'is_correct' => $isCorrect
                ]
            );
        }
        
        // Scale score to 100
        $finalScore = $maxScore > 0 ? round(($pointsEarned / $maxScore) * 100) : 0;

        $attempt->update([
            'score' => $finalScore
        ]);

        // Award points to user if they haven't earned them for this quiz yet (simplified)
        // For now, let's just add the pointsEarned to the user's total points.
        $user = auth()->user();
        $user->increment('points', $pointsEarned);

        return redirect()->route('student.quizzes.result', $attempt)
            ->with('success', 'Kuis berhasil diselesaikan!');
    }

    public function result(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }

        $quiz = $attempt->quiz()->with('questions')->first();
        $attempt->load('answers');
        
        return view('student.quizzes.result', compact('quiz', 'attempt'));
    }
}
