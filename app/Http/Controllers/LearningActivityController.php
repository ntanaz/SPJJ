<?php

namespace App\Http\Controllers;

use App\Models\LearningActivity;
use App\Models\LearningActivityProgress;
use App\Models\Module;
use App\Models\Material;
use App\Services\XpService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LearningActivityController extends Controller
{
    /**
     * Display the specified learning activity for the student.
     */
    public function show(LearningActivity $activity)
    {
        $user = auth()->user();

        // Check if unlocked
        if (!$activity->isUnlockedFor($user)) {
            return back()->with('error', 'Aktivitas ini masih terkunci. Anda harus menyelesaikan aktivitas sebelumnya terlebih dahulu.');
        }

        // Redirect standard activities that have their own dedicated page
        if ($activity->activity_type === 'quiz' && $activity->quiz_id) {
            return redirect()->route('student.quizzes.show', $activity->quiz_id);
        }
        if ($activity->activity_type === 'assignment' && $activity->assignment_id) {
            return redirect()->route('student.assignments.show', $activity->assignment_id);
        }
        if ($activity->activity_type === 'discussion' && $activity->discussion_id) {
            return redirect()->route('discussions.show', $activity->discussion_id);
        }

        // Focused student viewer for: mind_map, material, video, coding_quiz, reflection
        $material = $activity->material;
        $video = null;
        $videoLog = null;
        $videoQuizzes = [];
        $codingQuiz = null;
        $codingAttempts = [];
        $videoTrackings = [];

        if ($activity->activity_type === 'video') {
            if ($activity->video_id) {
                $video = \App\Models\Video::with('quizzes.options')->find($activity->video_id);
            }
            if ($video) {
                $videoLog = \App\Models\VideoActivityLog::where('video_id', $video->id)
                    ->where('user_id', $user->id)
                    ->first();
                $videoQuizzes = $video->quizzes;
            }
        }

        if ($activity->activity_type !== 'video' || !$video) {
            if (!$material) {
                return back()->with('error', 'Materi terkait tidak ditemukan.');
            }

            $codingQuiz = $material->codingQuiz;
            if ($codingQuiz) {
                $codingAttempts = \App\Models\CodingQuizAttempt::where('coding_quiz_id', $codingQuiz->id)
                    ->where('user_id', $user->id)
                    ->orderBy('created_at', 'desc')
                    ->get();
            }

            $videoTrackings = \App\Models\VideoParticipationTracking::where('material_id', $material->id)
                ->where('user_id', $user->id)
                ->get()
                ->pluck('is_correct', 'question_id')
                ->toArray();
        }

        // Get step progress for specific steps
        $stepMap = [
            'mind_map' => 'mind_map',
            'material' => 'modul',
            'video' => 'video',
            'coding_quiz' => 'coding',
            'reflection' => 'reflection'
        ];
        $currentStep = $stepMap[$activity->activity_type] ?? 'mind_map';

        // Check completion status
        $isCompleted = LearningActivityProgress::where('user_id', $user->id)
            ->where('learning_activity_id', $activity->id)
            ->where('is_completed', true)
            ->exists();

        // Get next activity if any to show navigation
        $nextActivity = LearningActivity::where('module_id', $activity->module_id)
            ->where('order_number', '>', $activity->order_number)
            ->orderBy('order_number', 'asc')
            ->first();

        return view('student.activities.show', compact(
            'activity',
            'material',
            'codingQuiz',
            'codingAttempts',
            'videoTrackings',
            'isCompleted',
            'currentStep',
            'nextActivity',
            'video',
            'videoLog',
            'videoQuizzes'
        ));
    }

    /**
     * Mark simple activities (mind_map, material) as completed.
     */
    public function complete(LearningActivity $activity)
    {
        $user = auth()->user();

        if (!$activity->isUnlockedFor($user)) {
            return back()->with('error', 'Aktivitas ini masih terkunci.');
        }

        if (!in_array($activity->activity_type, ['mind_map', 'material'])) {
            return back()->with('error', 'Aktivitas ini memerlukan pengerjaan khusus.');
        }

        // Complete step in the legacy table to keep compatibility
        if ($activity->material_id) {
            $step = $activity->activity_type === 'mind_map' ? 'mind_map' : 'modul';
            $progress = \App\Models\MaterialStepProgress::firstOrCreate(
                ['material_id' => $activity->material_id, 'user_id' => $user->id, 'step' => $step],
                ['is_completed' => true, 'completed_at' => now()]
            );
            if (!$progress->is_completed) {
                $progress->update(['is_completed' => true]);
            }

            // Award XP (triggers learning_activity_progress update in XpService)
            $xpType = $activity->activity_type === 'mind_map' ? 'mind_map' : 'module_read';
            XpService::addXp($user, $xpType, $activity->module_id, 'MaterialStepProgress', $progress->id);
        } else {
            // Standalone completion
            DB::table('learning_activity_progress')->updateOrInsert(
                ['user_id' => $user->id, 'learning_activity_id' => $activity->id],
                ['is_completed' => true, 'completed_at' => now()]
            );
            XpService::updateModuleProgress($user, $activity->module_id);
        }

        return redirect()->route('student.courses.show', $activity->module->course_id)
            ->with('success', 'Aktivitas "' . $activity->title . '" berhasil diselesaikan!');
    }

    /**
     * Store a new activity inside a module (for Teacher/Admin).
     */
    public function store(Request $request, Module $module)
    {
        $request->validate([
            'activity_type' => 'required|string',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'is_required' => 'boolean',
        ]);

        $maxOrder = LearningActivity::where('module_id', $module->id)->max('order_number') ?? 0;

        $activity = LearningActivity::create([
            'module_id' => $module->id,
            'activity_type' => $request->activity_type,
            'title' => $request->title,
            'description' => $request->description,
            'order_number' => $maxOrder + 1,
            'is_required' => $request->has('is_required'),
            'material_id' => $request->material_id ?: null,
            'quiz_id' => $request->quiz_id ?: null,
            'assignment_id' => $request->assignment_id ?: null,
            'discussion_id' => $request->discussion_id ?: null,
            'video_id' => $request->video_id ?: null,
        ]);

        return back()->with('success', 'Aktivitas berhasil ditambahkan ke Bab ini.');
    }

    /**
     * Update an activity (for Teacher/Admin).
     */
    public function update(Request $request, LearningActivity $activity)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_number' => 'required|integer|min:1',
            'is_required' => 'boolean',
        ]);

        $activity->update([
            'title' => $request->title,
            'description' => $request->description,
            'order_number' => $request->order_number,
            'is_required' => $request->has('is_required'),
        ]);

        // If order number changed, reorder other items in module to keep sequence clean
        $this->resolveOrderGaps($activity->module_id);

        return back()->with('success', 'Aktivitas berhasil diperbarui.');
    }

    /**
     * Delete an activity (for Teacher/Admin).
     */
    public function destroy(LearningActivity $activity)
    {
        $moduleId = $activity->module_id;
        $activity->delete();

        // Resolve order gaps after delete
        $this->resolveOrderGaps($moduleId);

        return back()->with('success', 'Aktivitas berhasil dihapus.');
    }

    /**
     * Reorder activities in a module (for Teacher/Admin).
     */
    public function reorder(Request $request, Module $module)
    {
        $request->validate([
            'orders' => 'required|array',
            'orders.*' => 'integer|exists:learning_activities,id',
        ]);

        foreach ($request->orders as $index => $id) {
            LearningActivity::where('id', $id)
                ->where('module_id', $module->id)
                ->update(['order_number' => $index + 1]);
        }

        return response()->json(['success' => true, 'message' => 'Urutan aktivitas berhasil disimpan.']);
    }

    /**
     * Helper to resolve ordering gaps.
     */
    private function resolveOrderGaps(int $moduleId)
    {
        $activities = LearningActivity::where('module_id', $moduleId)
            ->orderBy('order_number')
            ->orderBy('updated_at', 'desc')
            ->get();

        foreach ($activities as $index => $activity) {
            $activity->update(['order_number' => $index + 1]);
        }
    }
}
