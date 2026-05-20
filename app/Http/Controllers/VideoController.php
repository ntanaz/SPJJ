<?php

namespace App\Http\Controllers;

use App\Models\Video;
use App\Models\VideoQuiz;
use App\Models\VideoQuizOption;
use App\Models\VideoActivityLog;
use App\Models\LearningActivity;
use App\Models\Module;
use App\Services\XpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class VideoController extends Controller
{
    /**
     * Store a newly created video resource.
     */
    public function store(Module $module, Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'video_file' => 'required|file|mimes:mp4,mov,avi,webm|max:512000', // Max 500MB
        ]);

        $path = $request->file('video_file')->store('materials/videos', 'public');

        $video = Video::create([
            'module_id' => $module->id,
            'title' => $request->title,
            'video_path' => $path,
            'duration' => 0,
        ]);

        return redirect()->route('videos.manage', $video)->with('success', '🎬 Video pembelajaran berhasil dibuat. Silakan tambahkan pertanyaan kuis interaktif.');
    }

    /**
     * Show the management panel for a video.
     */
    public function manage(Video $video)
    {
        $video->load(['quizzes.options', 'module.course']);
        return view('videos.manage', compact('video'));
    }

    /**
     * Upload / replace video file.
     */
    public function uploadFile(Video $video, Request $request)
    {
        $request->validate([
            'video_file' => 'required|file|mimes:mp4,mov,avi,webm|max:512000',
        ]);

        // Delete old video file
        if ($video->video_path && Storage::disk('public')->exists($video->video_path)) {
            Storage::disk('public')->delete($video->video_path);
        }

        $path = $request->file('video_file')->store('materials/videos', 'public');
        $video->update(['video_path' => $path]);

        return back()->with('success', '🎬 File video berhasil diperbarui.');
    }

    /**
     * Store a new interactive video quiz timestamp.
     */
    public function storeQuiz(Video $video, Request $request)
    {
        $request->validate([
            'timestamp_seconds' => 'required|integer|min:0',
            'question' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,short_answer',
            'feedback' => 'nullable|string',
            'correct_answer' => 'required|string',
            'options' => 'nullable|string', // comma-separated for multiple_choice
        ]);

        DB::transaction(function () use ($video, $request) {
            $quiz = VideoQuiz::create([
                'video_id' => $video->id,
                'timestamp_seconds' => $request->timestamp_seconds,
                'question' => $request->question,
                'question_type' => $request->question_type,
                'feedback' => $request->feedback,
            ]);

            $this->saveOptionsForQuiz($quiz, $request);
        });

        return back()->with('success', '✅ Pertanyaan kuis berhasil ditambahkan pada timestamp.');
    }

    /**
     * Update an interactive video quiz.
     */
    public function updateQuiz(VideoQuiz $quiz, Request $request)
    {
        $request->validate([
            'timestamp_seconds' => 'required|integer|min:0',
            'question' => 'required|string',
            'question_type' => 'required|in:multiple_choice,true_false,short_answer',
            'feedback' => 'nullable|string',
            'correct_answer' => 'required|string',
            'options' => 'nullable|string',
        ]);

        DB::transaction(function () use ($quiz, $request) {
            $quiz->update([
                'timestamp_seconds' => $request->timestamp_seconds,
                'question' => $request->question,
                'question_type' => $request->question_type,
                'feedback' => $request->feedback,
            ]);

            // Re-create options
            $quiz->options()->delete();
            $this->saveOptionsForQuiz($quiz, $request);
        });

        return back()->with('success', '✅ Pertanyaan kuis berhasil diperbarui.');
    }

    /**
     * Delete a video quiz.
     */
    public function destroyQuiz(VideoQuiz $quiz)
    {
        $quiz->delete();
        return back()->with('success', '🗑️ Pertanyaan kuis berhasil dihapus.');
    }

    /**
     * Save/generate option records helper.
     */
    private function saveOptionsForQuiz(VideoQuiz $quiz, Request $request)
    {
        $type = $request->question_type;
        $correct = trim($request->correct_answer);

        if ($type === 'multiple_choice') {
            $rawOptions = array_map('trim', explode(',', $request->options ?? ''));
            foreach ($rawOptions as $opt) {
                if (empty($opt)) continue;
                VideoQuizOption::create([
                    'video_quiz_id' => $quiz->id,
                    'option_text' => $opt,
                    'is_correct' => (strcasecmp($opt, $correct) === 0),
                ]);
            }
        } elseif ($type === 'true_false') {
            foreach (['Benar', 'Salah'] as $opt) {
                VideoQuizOption::create([
                    'video_quiz_id' => $quiz->id,
                    'option_text' => $opt,
                    'is_correct' => (strcasecmp($opt, $correct) === 0),
                ]);
            }
        } elseif ($type === 'short_answer') {
            VideoQuizOption::create([
                'video_quiz_id' => $quiz->id,
                'option_text' => $correct,
                'is_correct' => true,
            ]);
        }
    }

    /**
     * Student log watching progress.
     */
    public function logProgress(Video $video, Request $request)
    {
        $request->validate([
            'watched_duration' => 'required|integer|min:0',
            'completed' => 'required|boolean',
        ]);

        $userId = Auth::id();

        $log = VideoActivityLog::updateOrCreate(
            ['user_id' => $userId, 'video_id' => $video->id],
            [
                'watched_duration' => $request->watched_duration,
                'completed' => $request->completed ? true : DB::raw('completed'),
            ]
        );

        // If completed just now and it wasn't completed before, award completion
        $wasCompleted = $log->completed;
        if ($request->completed && !$wasCompleted) {
            $log->update(['completed' => true]);
            
            if (Auth::user()->hasRole('siswa')) {
                XpService::addXp(Auth::user(), 'video_watch', $video->module_id, 'VideoActivityLog', $log->id, 'Menonton Video Pembelajaran');
            }
        }

        return response()->json([
            'status' => 'success',
            'completed' => $log->completed,
        ]);
    }

    /**
     * Student submits popup quiz answer.
     */
    public function submitQuizAnswer(Video $video, Request $request)
    {
        $request->validate([
            'quiz_id' => 'required|exists:video_quizzes,id',
            'answer' => 'required|string',
        ]);

        $userId = Auth::id();
        $quiz = VideoQuiz::findOrFail($request->quiz_id);
        $userAnswer = trim($request->answer);

        // Determine if correct
        $isCorrect = false;
        $correctAnswerText = '';

        if ($quiz->question_type === 'short_answer') {
            // Find option
            $opt = $quiz->options()->first();
            $correctAnswerText = $opt ? $opt->option_text : '';
            $isCorrect = (strcasecmp($userAnswer, $correctAnswerText) === 0);
        } else {
            // For multiple choice / true-false, the answer could be option ID or option text
            $opt = VideoQuizOption::where('video_quiz_id', $quiz->id)
                ->where('is_correct', true)
                ->first();
            
            if ($opt) {
                $correctAnswerText = $opt->option_text;
                // Accept either matching option_text or option ID
                $isCorrect = (strcasecmp($userAnswer, $opt->option_text) === 0 || $userAnswer == $opt->id);
            }
        }

        // Get log
        $log = VideoActivityLog::firstOrCreate(
            ['user_id' => $userId, 'video_id' => $video->id],
            ['answered_quiz' => []]
        );

        $answered = $log->answered_quiz ?? [];
        
        // Check if already answered correctly to avoid duplicate XP
        $alreadyCorrect = isset($answered[$quiz->id]) && $answered[$quiz->id]['is_correct'];

        // Save answer status
        $answered[$quiz->id] = [
            'answer' => $userAnswer,
            'is_correct' => $isCorrect,
            'answered_at' => now()->toDateTimeString(),
        ];
        $log->update(['answered_quiz' => $answered]);

        // Award XP if correct and not already awarded
        if ($isCorrect && !$alreadyCorrect && Auth::user()->hasRole('siswa')) {
            // Interactive Video Quiz gives +15 XP
            XpService::addXp(Auth::user(), 'video_quiz', $video->module_id, 'VideoActivityLog', $log->id, 'Menjawab Video Quiz dengan Benar');
        }

        return response()->json([
            'is_correct' => $isCorrect,
            'feedback' => $quiz->feedback ?? ($isCorrect ? 'Jawaban Anda Benar!' : 'Jawaban kurang tepat. Coba perhatikan materi video kembali.'),
            'correct_answer' => $correctAnswerText,
        ]);
    }

    public function stream(Video $video)
    {
        $path = $video->video_path;
        if (!$path || !Storage::disk('public')->exists($path)) {
            abort(404);
        }

        $fullPath = Storage::disk('public')->path($path);
        $file = fopen($fullPath, 'rb');
        $size = filesize($fullPath);
        $length = $size;
        $start = 0;
        $end = $size - 1;

        $headers = [
            'Content-Type' => 'video/mp4',
            'Accept-Ranges' => 'bytes',
        ];

        if (request()->headers->has('Range')) {
            $range = request()->header('Range');
            preg_match('/bytes=(\d+)-(\d+)?/', $range, $matches);
            
            $start = intval($matches[1]);
            if (isset($matches[2]) && $matches[2] !== '') {
                $end = intval($matches[2]);
            }
            
            $length = $end - $start + 1;
            fseek($file, $start);
            
            $headers['Content-Range'] = "bytes {$start}-{$end}/{$size}";
            $status = 206;
        } else {
            $status = 200;
        }

        $headers['Content-Length'] = $length;

        return response()->stream(function () use ($file, $length) {
            $chunkSize = 8192;
            $bytesSent = 0;
            while (!feof($file) && $bytesSent < $length) {
                $toRead = min($chunkSize, $length - $bytesSent);
                $buffer = fread($file, $toRead);
                echo $buffer;
                flush();
                $bytesSent += strlen($buffer);
            }
            fclose($file);
        }, $status, $headers);
    }
}
