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

        foreach ($quiz->questions as $question) {
            $selected = $answers[$question->id] ?? null;
            $selectedOption = null;
            $textAnswer = null;

            if ($question->question_type === 'multiple_choice' || $question->question_type === 'true_false') {
                $selectedOption = $selected;
            } elseif ($question->question_type === 'interactive_video') {
                $videoQType = $question->options['video_question_type'] ?? 'multiple_choice';
                if ($videoQType === 'multiple_choice' || $videoQType === 'true_false') {
                    $selectedOption = $selected;
                } else {
                    $textAnswer = $selected;
                }
            } else {
                $textAnswer = $selected;
            }

            $isCorrect = $question->isAnswerCorrect($selected);

            $attempt->answers()->updateOrCreate(
                ['quiz_question_id' => $question->id],
                [
                    'selected_option' => $selectedOption,
                    'text_answer' => $textAnswer,
                    'is_correct' => $isCorrect
                ]
            );
        }

        // Calculate and save the scaled final score using the centralized scoring engine
        $attempt->load('answers.question');
        $finalScore = $attempt->calculateScore();

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
