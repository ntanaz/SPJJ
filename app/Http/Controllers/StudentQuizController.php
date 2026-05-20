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
        // Check deadline
        if ($quiz->deadline && \Carbon\Carbon::now()->isAfter($quiz->deadline)) {
            return back()->with('error', 'Kuis sudah ditutup.');
        }

        // Check if already attempted
        $attempt = $quiz->attempts()->where('user_id', auth()->id())->first();
        
        if (!$attempt) {
            $attempt = $quiz->attempts()->create([
                'user_id' => auth()->id(),
                'score' => 0,
                'status' => 'in_progress',
                'started_at' => \Carbon\Carbon::now(),
            ]);
        }

        return redirect()->route('student.quizzes.attempt', $attempt);
    }

    public function attempt(QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403, 'Sesi kuis tidak valid.');
        }

        if ($attempt->status === 'completed') {
            return redirect()->route('student.quizzes.result', $attempt);
        }

        $quiz = $attempt->quiz()->with('questions')->first();
        
        // Auto-submit if time is up
        if ($quiz->time_limit_minutes && $attempt->started_at) {
            $endTime = \Carbon\Carbon::parse($attempt->started_at)->addMinutes($quiz->time_limit_minutes);
            if (\Carbon\Carbon::now()->isAfter($endTime)) {
                return $this->processSubmission(new Request(), $attempt, true);
            }
        }
        
        return view('student.quizzes.attempt', compact('quiz', 'attempt'));
    }

    public function submit(Request $request, QuizAttempt $attempt)
    {
        if ($attempt->user_id !== auth()->id()) {
            abort(403);
        }
        
        if ($attempt->status === 'completed') {
            return redirect()->route('student.quizzes.result', $attempt);
        }

        return $this->processSubmission($request, $attempt);
    }

    private function processSubmission(Request $request, QuizAttempt $attempt, $isTimeout = false)
    {
        $quiz = $attempt->quiz()->with('questions')->first();
        $answers = $request->input('answers', []);
        
        $totalScore = 0;
        $maxScore = $quiz->questions->sum('points');
        
        $pointsEarned = 0;

        foreach ($quiz->questions as $question) {
            $selected = $answers[$question->id] ?? null;
            $isCorrect = false;
            $selectedOption = null;
            $textAnswer = null;

            $type = $question->question_type;

            if ($type === 'multiple_choice' || $type === 'true_false') {
                $selectedOption = $selected;
                $isCorrect = strtolower(trim($selectedOption)) === strtolower(trim($question->correct_answer));
            } elseif ($type === 'short_answer' || $type === 'fill_blank') {
                $textAnswer = $selected;
                $isCorrect = strtolower(trim($textAnswer)) === strtolower(trim($question->correct_answer));
            } elseif ($type === 'reflection') {
                $textAnswer = $selected;
                $isCorrect = !empty(trim($textAnswer));
            } elseif ($type === 'debugging') {
                $textAnswer = $selected;
                $cleanInput = preg_replace('/\s+/', '', $textAnswer);
                $cleanCorrect = preg_replace('/\s+/', '', $question->correct_answer);
                $isCorrect = strtolower($cleanInput) === strtolower($cleanCorrect);
            } elseif ($type === 'interactive_video') {
                $videoQType = $question->options['video_question_type'] ?? 'multiple_choice';
                if ($videoQType === 'multiple_choice' || $videoQType === 'true_false') {
                    $selectedOption = $selected;
                    $isCorrect = strtolower(trim($selectedOption)) === strtolower(trim($question->correct_answer));
                } else {
                    $textAnswer = $selected;
                    $isCorrect = strtolower(trim($textAnswer)) === strtolower(trim($question->correct_answer));
                }
            }

            if ($isCorrect) {
                $pointsEarned += $question->points;
            }

            $attempt->answers()->updateOrCreate(
                ['quiz_question_id' => $question->id],
                [
                    'selected_option' => $selectedOption,
                    'text_answer' => $textAnswer,
                    'is_correct' => $isCorrect
                ]
            );
        }
        
        // Scale score to 100
        $finalScore = $maxScore > 0 ? round(($pointsEarned / $maxScore) * 100) : 0;

        $attempt->update([
            'score' => $finalScore,
            'status' => 'completed',
        ]);

        // Award XP using XpService
        $user = auth()->user();
        \App\Services\XpService::addXp($user, 'quiz', $quiz->module_id, 'QuizAttempt', $attempt->id);

        // Pre-test Attendance Integration
        $attendance = \App\Models\Attendance::where('pre_test_quiz_id', $quiz->id)
            ->whereDate('date', \Carbon\Carbon::today())
            ->first();
            
        if ($attendance) {
            \App\Models\AttendanceRecord::updateOrCreate(
                ['attendance_id' => $attendance->id, 'user_id' => $user->id],
                ['status' => 'hadir']
            );
        }

        $msg = $isTimeout ? 'Waktu habis! Kuis otomatis diselesaikan.' : 'Kuis berhasil diselesaikan!';
        return redirect()->route('student.quizzes.result', $attempt)->with('success', $msg);
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
