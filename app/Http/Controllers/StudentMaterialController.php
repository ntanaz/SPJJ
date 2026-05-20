<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;
use App\Models\InteractiveVideoQuestion;
use App\Models\VideoParticipationTracking;
use App\Models\CodingQuiz;
use App\Models\CodingQuizAttempt;
use App\Models\MaterialStepProgress;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;

class StudentMaterialController extends Controller
{
    public function show(Material $material)
    {
        $material->load([
            'course',
            'interactiveVideoQuestions',
            'codingQuiz',
            'discussions.user',
            'discussions.replies',
            'stepProgress' => function($query) {
                $query->where('user_id', auth()->id());
            }
        ]);

        $user = auth()->user();

        // 1. If Teacher / Admin, show teacher management board
        if ($user->hasRole(['guru', 'teacher', 'admin'])) {
            $codingQuiz = $material->codingQuiz;
            $attempts = [];
            if ($codingQuiz) {
                $attempts = CodingQuizAttempt::where('coding_quiz_id', $codingQuiz->id)
                    ->with('user')
                    ->latest()
                    ->get();
            }

            $videoTrackings = VideoParticipationTracking::where('material_id', $material->id)
                ->with(['user', 'question'])
                ->latest()
                ->get();

            // Fetch students enrolled in this course through course classes
            $classes = \App\Models\CourseClass::where('course_id', $material->course_id)->get();
            $classIds = $classes->pluck('id');
            
            $enrolledStudentIds = \Illuminate\Support\Facades\DB::table('class_user')
                ->whereIn('course_class_id', $classIds)
                ->pluck('user_id');

            $students = User::whereIn('id', $enrolledStudentIds)->get();

            // Progress status for all students
            $studentProgress = [];
            foreach ($students as $student) {
                $steps = MaterialStepProgress::where('material_id', $material->id)
                    ->where('user_id', $student->id)
                    ->get()
                    ->pluck('is_completed', 'step')
                    ->toArray();

                $studentProgress[] = [
                    'student' => $student,
                    'steps' => $steps,
                    'is_material_completed' => \App\Models\MaterialProgress::where('material_id', $material->id)
                        ->where('user_id', $student->id)
                        ->where('is_completed', true)
                        ->exists()
                ];
            }

            return view('materials.teacher_manage', compact('material', 'attempts', 'videoTrackings', 'studentProgress'));
        }

        // 2. If Student, find corresponding LearningActivity and redirect to it
        $activities = \App\Models\LearningActivity::where('material_id', $material->id)
            ->orderBy('order_number')
            ->get();
        if ($activities->isNotEmpty()) {
            $activeActivity = $activities->first(function ($act) use ($user) {
                return !$act->progress->where('user_id', $user->id)->first()?->is_completed;
            }) ?: $activities->first();

            if ($activeActivity && $activeActivity->isUnlockedFor($user)) {
                return redirect()->route('student.activities.show', $activeActivity);
            }
        }

        // 3. Fallback: run standard course progress lock check
        if ($material->requires_previous) {
            $previousMaterials = Material::where('course_id', $material->course_id)
                ->where('order', '<', $material->order)
                ->get();
            
            foreach ($previousMaterials as $prev) {
                $progress = \App\Models\MaterialProgress::where('material_id', $prev->id)
                    ->where('user_id', $user->id)
                    ->where('is_completed', true)
                    ->first();
                if (!$progress) {
                    return back()->with('error', 'Anda harus menyelesaikan materi sebelumnya terlebih dahulu.');
                }
            }
        }

        // Initialize progress for first step (Mind Map) if not started yet
        MaterialStepProgress::firstOrCreate(
            ['material_id' => $material->id, 'user_id' => $user->id, 'step' => 'mind_map'],
            ['is_completed' => false]
        );

        // Fetch step progress as key-value array
        $stepsProgress = $material->stepProgress->pluck('is_completed', 'step')->toArray();

        // Calculate unlocked steps
        $unlockedSteps = [
            'mind_map' => true,
            'modul' => !empty($stepsProgress['mind_map']),
            'video' => !empty($stepsProgress['mind_map']) && !empty($stepsProgress['modul']),
            'coding' => !empty($stepsProgress['mind_map']) && !empty($stepsProgress['modul']) && !empty($stepsProgress['video']),
            'reflection' => !empty($stepsProgress['mind_map']) && !empty($stepsProgress['modul']) && !empty($stepsProgress['video']) && !empty($stepsProgress['coding']),
        ];

        // Determine active step (the first incomplete unlocked step)
        $activeStep = 'mind_map';
        if ($unlockedSteps['reflection']) {
            $activeStep = 'reflection'; // open reflection regardless of completion status
        } elseif ($unlockedSteps['coding']) {
            $activeStep = 'coding';
        } elseif ($unlockedSteps['video']) {
            $activeStep = 'video';
        } elseif ($unlockedSteps['modul']) {
            $activeStep = 'modul';
        }

        // Load coding quiz details
        $codingQuiz = $material->codingQuiz;
        $codingAttempts = [];
        $codingAttemptsCount = 0;
        $isCodingQuizLocked = false;
        $isCodingQuizSuccess = false;
        $correctAttempt = null;

        if ($codingQuiz) {
            $codingAttempts = CodingQuizAttempt::where('coding_quiz_id', $codingQuiz->id)
                ->where('user_id', $user->id)
                ->orderBy('percobaan_ke')
                ->get();
            
            $codingAttemptsCount = $codingAttempts->count();
            $isCodingQuizSuccess = $codingAttempts->where('hasil_validasi', true)->isNotEmpty();
            $isCodingQuizLocked = !$isCodingQuizSuccess && ($codingAttemptsCount >= 3);
            $correctAttempt = $codingAttempts->where('hasil_validasi', true)->first();
        }

        // Record basic material view progress (mark as viewed/in-progress)
        \App\Models\MaterialProgress::firstOrCreate(
            ['material_id' => $material->id, 'user_id' => $user->id]
        );

        return view('student.materials.show', compact(
            'material',
            'stepsProgress',
            'unlockedSteps',
            'activeStep',
            'codingQuiz',
            'codingAttempts',
            'codingAttemptsCount',
            'isCodingQuizSuccess',
            'isCodingQuizLocked',
            'correctAttempt'
        ));
    }

    public function complete(Material $material)
    {
        \App\Models\MaterialProgress::updateOrCreate(
            ['material_id' => $material->id, 'user_id' => auth()->id()],
            ['is_completed' => true, 'completed_at' => now()]
        );

        return back()->with('success', 'Materi ditandai sebagai selesai!');
    }

    /**
     * Submit progress for Mind Map, Modul, or Video step.
     */
    public function completeStep(Material $material, Request $request)
    {
        $request->validate([
            'step' => 'required|in:mind_map,modul,video'
        ]);

        $step = $request->step;
        $userId = auth()->id();

        // 1. Verification of previous step
        if ($step === 'modul') {
            $prev = MaterialStepProgress::where('material_id', $material->id)->where('user_id', $userId)->where('step', 'mind_map')->first();
            if (!$prev || !$prev->is_completed) {
                return response()->json(['error' => 'Selesaikan tahap Mind Map terlebih dahulu.'], 403);
            }
        } elseif ($step === 'video') {
            $prev = MaterialStepProgress::where('material_id', $material->id)->where('user_id', $userId)->where('step', 'modul')->first();
            if (!$prev || !$prev->is_completed) {
                return response()->json(['error' => 'Selesaikan tahap Modul terlebih dahulu.'], 403);
            }
        }

        // 2. Update step
        $progress = MaterialStepProgress::updateOrCreate(
            ['material_id' => $material->id, 'user_id' => $userId, 'step' => $step],
            ['is_completed' => true, 'completed_at' => now()]
        );

        // Award XP
        $xpType = $step === 'mind_map' ? 'mind_map' : ($step === 'modul' ? 'module_read' : 'video_watch');
        \App\Services\XpService::addXp(auth()->user(), $xpType, $material->module_id, 'MaterialStepProgress', $progress->id);

        // 3. Initialize next step
        $nextStep = $step === 'mind_map' ? 'modul' : ($step === 'modul' ? 'video' : 'coding');
        MaterialStepProgress::firstOrCreate(
            ['material_id' => $material->id, 'user_id' => $userId, 'step' => $nextStep],
            ['is_completed' => false]
        );

        return response()->json([
            'success' => true,
            'message' => 'Tahap berhasil diselesaikan!',
            'next_step' => $nextStep
        ]);
    }

    /**
     * Save student response to interactive video quiz question.
     * Uses updateOrCreate to prevent duplicate submissions for the same question.
     */
    public function submitVideoQuizAnswer(Material $material, Request $request)
    {
        $request->validate([
            'question_id'     => 'required|exists:interactive_video_questions,id',
            'selected_answer' => 'required|string',
            'timestamp'       => 'required|integer',
        ]);

        $question  = InteractiveVideoQuestion::findOrFail($request->question_id);
        $userId    = auth()->id();

        // For short_answer type, correct_answer check is case-insensitive, trimmed
        $isCorrect = (trim(strtolower($question->correct_answer)) === trim(strtolower($request->selected_answer)));

        // Prevent duplicate records – update if the student re-submits the same question
        $tracking = VideoParticipationTracking::updateOrCreate(
            [
                'user_id'     => $userId,
                'material_id' => $material->id,
                'question_id' => $question->id,
            ],
            [
                'selected_answer' => $request->selected_answer,
                'is_correct'      => $isCorrect,
                'timestamp'       => $request->timestamp,
                'activity_log'    => $request->activity_log ?? [],
                'answered_at'     => now(),
            ]
        );

        if ($isCorrect) {
            \App\Services\XpService::addXp(auth()->user(), 'video_quiz', $material->module_id, 'VideoParticipationTracking', $tracking->id);
        }

        $feedback = $isCorrect
            ? '✅ Jawaban Anda benar!'
            : '❌ Jawaban Anda salah. Jawaban yang benar adalah: ' . $question->correct_answer;

        return response()->json([
            'success'        => true,
            'is_correct'     => $isCorrect,
            'correct_answer' => $question->correct_answer,
            'feedback'       => $feedback,
            'question_type'  => $question->question_type,
        ]);
    }

    /**
     * Submit coding quiz and evaluate answers.
     * Supports quiz types: fill_blank, debugging, short_answer.
     */
    public function submitCodingQuiz(Material $material, Request $request)
    {
        $codingQuiz = $material->codingQuiz;
        if (!$codingQuiz) {
            return back()->with('error', 'Kuis koding tidak ditemukan untuk materi ini.');
        }

        $userId    = auth()->id();
        $quizType  = $codingQuiz->quiz_type ?? CodingQuiz::TYPE_FILL_BLANK;

        // 1. Check attempt limit
        $attemptsCount = CodingQuizAttempt::where('coding_quiz_id', $codingQuiz->id)
            ->where('user_id', $userId)
            ->count();

        if ($attemptsCount >= 3) {
            return back()->with('error', 'Kuis koding terkunci karena Anda telah mencapai batas maksimal 3 kali percobaan.');
        }

        // Check if already successful
        $alreadySuccess = CodingQuizAttempt::where('coding_quiz_id', $codingQuiz->id)
            ->where('user_id', $userId)
            ->where('hasil_validasi', true)
            ->exists();
        if ($alreadySuccess) {
            return back()->with('success', 'Anda telah menyelesaikan kuis koding ini sebelumnya.');
        }

        // 2. Validate answers based on quiz type
        $isCorrect      = false;
        $studentAnswers = [];

        if ($quizType === CodingQuiz::TYPE_SHORT_ANSWER) {
            // Short answer: single textarea input
            $singleAnswer   = trim($request->input('short_answer', ''));
            $studentAnswers = [$singleAnswer];
            $correctAnswers = $codingQuiz->correct_answers;
            // Match any correct answer (case-insensitive)
            foreach ($correctAnswers as $ca) {
                if (strcasecmp($singleAnswer, trim($ca)) === 0) {
                    $isCorrect = true;
                    break;
                }
            }
        } elseif ($quizType === CodingQuiz::TYPE_DEBUGGING) {
            // Debugging: student submits corrected code as a single text block
            $debugAnswer    = trim($request->input('debug_answer', ''));
            $studentAnswers = [$debugAnswer];
            $correctAnswers = $codingQuiz->correct_answers;
            // Compare normalised (strip extra spaces, lowercase)
            $normStudent = preg_replace('/\s+/', ' ', strtolower($debugAnswer));
            foreach ($correctAnswers as $ca) {
                $normCorrect = preg_replace('/\s+/', ' ', strtolower(trim($ca)));
                if ($normStudent === $normCorrect) {
                    $isCorrect = true;
                    break;
                }
            }
        } else {
            // Default: fill_blank — array of blank answers
            $studentAnswers = $request->input('answers', []);
            $correctAnswers = $codingQuiz->correct_answers;
            $isCorrect      = true;
            foreach ($correctAnswers as $index => $correctAnswer) {
                $studentAnswer = isset($studentAnswers[$index]) ? trim($studentAnswers[$index]) : '';
                if (strcasecmp($studentAnswer, trim($correctAnswer)) !== 0) {
                    $isCorrect = false;
                }
            }
        }

        $feedback = $isCorrect
            ? ($codingQuiz->feedback_correct    ?? '✅ Jawaban Anda Benar! Kode berjalan dengan sukses.')
            : ($codingQuiz->feedback_incorrect  ?? '❌ Jawaban Anda Kurang Tepat! Silakan cek kembali kode Anda.');

        // 3. Save attempt
        $attempt = CodingQuizAttempt::create([
            'user_id'        => $userId,
            'coding_quiz_id' => $codingQuiz->id,
            'percobaan_ke'   => $attemptsCount + 1,
            'jawaban'        => $studentAnswers,
            'hasil_validasi' => $isCorrect,
            'feedback'       => $feedback,
            'waktu_submit'   => now(),
        ]);

        if ($isCorrect) {
            // Mark coding step completed
            MaterialStepProgress::updateOrCreate(
                ['material_id' => $material->id, 'user_id' => $userId, 'step' => 'coding'],
                ['is_completed' => true, 'completed_at' => now()]
            );

            // Award XP
            \App\Services\XpService::addXp(auth()->user(), 'coding_quiz', $material->module_id, 'CodingQuizAttempt', $attempt->id);

            // Unlock reflection step
            MaterialStepProgress::firstOrCreate(
                ['material_id' => $material->id, 'user_id' => $userId, 'step' => 'reflection'],
                ['is_completed' => false]
            );

            return back()->with('success', '🎉 Kuis berhasil diselesaikan! Lanjutkan ke tahap Refleksi.');
        }

        $sisa = 3 - ($attemptsCount + 1);
        if ($sisa <= 0) {
            // Mark coding step completed (failed attempt, but let them progress to reflection)
            MaterialStepProgress::updateOrCreate(
                ['material_id' => $material->id, 'user_id' => $userId, 'step' => 'coding'],
                ['is_completed' => true, 'completed_at' => now()]
            );

            // Find and complete the coding quiz learning activity
            $activity = \App\Models\LearningActivity::where('module_id', $material->module_id)
                ->where('activity_type', 'coding_quiz')
                ->where('material_id', $material->id)
                ->first();
            if ($activity) {
                \App\Models\LearningActivityProgress::updateOrCreate(
                    ['user_id' => $userId, 'learning_activity_id' => $activity->id],
                    ['is_completed' => true, 'completed_at' => now()]
                );
            }

            // Unlock reflection step
            MaterialStepProgress::firstOrCreate(
                ['material_id' => $material->id, 'user_id' => $userId, 'step' => 'reflection'],
                ['is_completed' => false]
            );

            return back()->with('error', '❌ Jawaban salah. Percobaan habis! Kuis terkunci, namun Anda sekarang dapat melanjutkan ke tahap Refleksi.');
        }
        return back()->with('error', '❌ Jawaban salah. Sisa percobaan: ' . $sisa . ' kali.');
    }

    /**
     * Submit final reflection and complete material.
     * Works with or without a coding quiz (graceful fallback).
     */
    public function submitReflection(Material $material, Request $request)
    {
        $request->validate([
            'reflection' => 'required|string|min:10'
        ]);

        $userId     = auth()->id();
        $codingQuiz = $material->codingQuiz;

        // Save reflection onto the latest attempt if coding quiz exists
        if ($codingQuiz) {
            $attempt = CodingQuizAttempt::where('coding_quiz_id', $codingQuiz->id)
                ->where('user_id', $userId)
                ->latest()
                ->first();

            if ($attempt) {
                $attempt->update(['reflection' => $request->reflection]);
            }
        }

        // Mark reflection step completed
        $reflectionProgress = MaterialStepProgress::updateOrCreate(
            ['material_id' => $material->id, 'user_id' => $userId, 'step' => 'reflection'],
            ['is_completed' => true, 'completed_at' => now()]
        );

        // Directly mark the reflection activity completed in learning_activity_progress
        $activity = \App\Models\LearningActivity::where('module_id', $material->module_id)
            ->where('activity_type', 'reflection')
            ->where('material_id', $material->id)
            ->first();
        if ($activity) {
            \App\Models\LearningActivityProgress::updateOrCreate(
                ['user_id' => $userId, 'learning_activity_id' => $activity->id],
                ['is_completed' => true, 'completed_at' => now()]
            );
        }

        // Mark entire material completed
        \App\Models\MaterialProgress::updateOrCreate(
            ['material_id' => $material->id, 'user_id' => $userId],
            ['is_completed' => true, 'completed_at' => now()]
        );

        // Award XP for completing the reflection
        \App\Services\XpService::addXp(auth()->user(), 'reflection', $material->module_id, 'MaterialStepProgress', $reflectionProgress->id);

        return redirect()->route('student.courses.show', $material->course_id)
            ->with('success', '🎉 Selamat! Anda telah menyelesaikan seluruh rangkaian Discovery Learning dan mendapatkan +20 XP!');
    }

    /**
     * Teacher saving/updating coding quiz.
     * Supports quiz_type: fill_blank, debugging, short_answer.
     */
    public function saveCodingQuiz(Material $material, Request $request)
    {
        $request->validate([
            'quiz_type'         => 'required|in:fill_blank,debugging,short_answer',
            'instruction'       => 'required|string',
            'code_template'     => 'nullable|string',
            'correct_answers'   => 'required|string',
            'feedback_correct'   => 'nullable|string',
            'feedback_incorrect' => 'nullable|string',
        ]);

        // Correct answers: comma-separated list
        $answers = array_map('trim', explode(',', $request->correct_answers));

        $material->codingQuiz()->updateOrCreate(
            ['material_id' => $material->id],
            [
                'quiz_type'          => $request->quiz_type,
                'instruction'        => $request->instruction,
                'code_template'      => $request->code_template ?? '',
                'correct_answers'    => $answers,
                'feedback_correct'   => $request->feedback_correct,
                'feedback_incorrect' => $request->feedback_incorrect,
            ]
        );

        return back()->with('success', 'Kuis Koding berhasil disimpan.');
    }

    /**
     * Teacher saving/updating interactive video quiz questions.
     * Supports question_type: multiple_choice, true_false, short_answer.
     */
    public function saveInteractiveVideoQuestions(Material $material, Request $request)
    {
        $request->validate([
            'questions'                    => 'nullable|array',
            'questions.*.timestamp'        => 'required|integer|min:0',
            'questions.*.question_type'    => 'required|in:multiple_choice,true_false,short_answer',
            'questions.*.question'         => 'required|string',
            'questions.*.options'          => 'nullable|string', // comma-separated; not required for short_answer
            'questions.*.correct_answer'   => 'required|string',
        ]);

        // 1. Save to legacy table for backwards compatibility
        $material->interactiveVideoQuestions()->delete();

        if ($request->has('questions')) {
            foreach ($request->questions as $q) {
                if (empty($q['question'])) continue;

                $qType   = $q['question_type'] ?? 'multiple_choice';
                $options = [];

                if ($qType === 'multiple_choice') {
                    $options = array_map('trim', explode(',', $q['options'] ?? ''));
                } elseif ($qType === 'true_false') {
                    $options = ['Benar', 'Salah'];
                }

                $material->interactiveVideoQuestions()->create([
                    'timestamp'     => $q['timestamp'],
                    'question_type' => $qType,
                    'question'      => $q['question'],
                    'options'       => $options,
                    'correct_answer' => trim($q['correct_answer']),
                ]);
            }
        }

        // 2. Synchronize to the new Video and VideoQuiz tables
        $activity = \App\Models\LearningActivity::where('material_id', $material->id)
            ->where('activity_type', 'video')
            ->first();

        if (!$activity) {
            $maxOrder = \App\Models\LearningActivity::where('module_id', $material->module_id)->max('order_number') ?? 0;
            $activity = \App\Models\LearningActivity::create([
                'module_id' => $material->module_id,
                'material_id' => $material->id,
                'activity_type' => 'video',
                'title' => 'Video Pembelajaran - ' . $material->title,
                'description' => 'Tonton video pembelajaran interaktif: ' . $material->title,
                'order_number' => $maxOrder + 1,
                'is_required' => true,
            ]);
        }

        $video = null;
        if ($activity->video_id) {
            $video = \App\Models\Video::find($activity->video_id);
        }

        $videoPath = $material->file_path ?? $material->youtube_url ?? '';
        if (!$video) {
            $video = \App\Models\Video::create([
                'module_id' => $material->module_id,
                'title' => $material->title,
                'video_path' => $videoPath,
                'duration' => 0,
            ]);
            $activity->update(['video_id' => $video->id]);
        } else {
            $video->update([
                'title' => $material->title,
                'video_path' => $videoPath,
            ]);
        }

        // Delete old quizzes to re-sync
        $video->quizzes()->delete();

        if ($request->has('questions')) {
            foreach ($request->questions as $q) {
                if (empty($q['question'])) continue;

                $qType = $q['question_type'] ?? 'multiple_choice';
                $feedback = $q['feedback'] ?? null;

                $videoQuiz = $video->quizzes()->create([
                    'timestamp_seconds' => $q['timestamp'],
                    'question' => $q['question'],
                    'question_type' => $qType,
                    'feedback' => $feedback,
                ]);

                // Create options
                $options = [];
                if ($qType === 'multiple_choice') {
                    $options = array_map('trim', explode(',', $q['options'] ?? ''));
                } elseif ($qType === 'true_false') {
                    $options = ['Benar', 'Salah'];
                } elseif ($qType === 'short_answer') {
                    $options = [trim($q['correct_answer'])];
                }

                foreach ($options as $optText) {
                    if (empty($optText)) continue;
                    $isCorrect = (trim(strtolower($optText)) === trim(strtolower($q['correct_answer'])));
                    if ($qType === 'short_answer') {
                        $isCorrect = true;
                    }

                    $videoQuiz->options()->create([
                        'option_text' => $optText,
                        'is_correct' => $isCorrect,
                    ]);
                }
            }
        }

        return back()->with('success', 'Pertanyaan Video Interaktif berhasil diperbarui.');
    }

    /**
     * Teacher uploads Mind Map image.
     */
    public function uploadMindMap(Material $material, Request $request)
    {
        $request->validate([
            'mind_map_image' => 'required|image|max:4096', // Max 4MB
        ]);

        if ($request->hasFile('mind_map_image')) {
            // Delete old mind map if it exists
            if ($material->mind_map_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($material->mind_map_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($material->mind_map_path);
            }

            $path = $request->file('mind_map_image')->store('materials/mindmaps', 'public');
            $material->update(['mind_map_path' => $path]);
        }

        return back()->with('success', 'Gambar Peta Pikiran berhasil diunggah.');
    }

    /**
     * Teacher grading student coding attempt and reflection.
     */
    public function gradeAttempt(CodingQuizAttempt $attempt, Request $request)
    {
        $request->validate([
            'correctness_grade' => 'required|integer|min:0|max:100',
            'reflection_grade'  => 'required|integer|min:0|max:100',
            'final_grade'       => 'required|integer|min:0|max:100',
        ]);

        $attempt->update([
            'correctness_grade' => $request->correctness_grade,
            'reflection_grade'  => $request->reflection_grade,
            'final_grade'       => $request->final_grade,
            'graded_at'         => now(),
        ]);

        return back()->with('success', '✅ Penilaian berhasil disimpan!');
    }

    /**
     * Teacher uploads a local video file directly from the management panel.
     * This is the primary video source for Discovery Learning interactive quizzes.
     */
    public function uploadVideo(Material $material, Request $request)
    {
        $request->validate([
            'video_file' => 'required|file|mimes:mp4,mov,avi,webm|max:512000', // max 500MB
        ]);

        // Delete old video if it exists and was locally uploaded
        if ($material->file_path && Storage::disk('public')->exists($material->file_path)) {
            Storage::disk('public')->delete($material->file_path);
        }

        $path = $request->file('video_file')->store('materials/videos', 'public');

        $material->update([
            'file_path' => $path,
            'format'    => 'video',
            'type'      => 'video',
        ]);

        // Automatically sync to Video model and LearningActivity
        $activity = \App\Models\LearningActivity::where('material_id', $material->id)
            ->where('activity_type', 'video')
            ->first();

        if (!$activity) {
            $maxOrder = \App\Models\LearningActivity::where('module_id', $material->module_id)->max('order_number') ?? 0;
            $activity = \App\Models\LearningActivity::create([
                'module_id' => $material->module_id,
                'material_id' => $material->id,
                'activity_type' => 'video',
                'title' => 'Video Pembelajaran - ' . $material->title,
                'description' => 'Tonton video pembelajaran interaktif: ' . $material->title,
                'order_number' => $maxOrder + 1,
                'is_required' => true,
            ]);
        }

        $video = null;
        if ($activity->video_id) {
            $video = \App\Models\Video::find($activity->video_id);
        }

        if (!$video) {
            $video = \App\Models\Video::create([
                'module_id' => $material->module_id,
                'title' => $material->title,
                'video_path' => $path,
                'duration' => 0,
            ]);
            $activity->update(['video_id' => $video->id]);
        } else {
            $video->update([
                'title' => $material->title,
                'video_path' => $path,
            ]);
        }

        return back()->with('success', '🎬 Video pembelajaran berhasil diunggah dan siap digunakan.');
    }

    public function streamVideo(Material $material)
    {
        $path = $material->file_path;
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
