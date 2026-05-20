<?php

namespace App\Services;

use App\Models\User;
use App\Models\Module;
use Illuminate\Support\Facades\DB;

class XpService
{
    const XP_MAP = [
        'mind_map' => 10,
        'module_read' => 20,
        'video_watch' => 20,
        'video_quiz' => 15,
        'quiz' => 30,
        'coding_quiz' => 50,
        'reflection' => 20,
        'assignment' => 40,
        'discussion_reply' => 10,
    ];

    /**
     * Award XP to a user for an activity.
     */
    public static function addXp(User $user, string $activityType, ?int $moduleId = null, ?string $referenceType = null, ?int $referenceId = null, ?string $customDesc = null): bool
    {
        // 1. Only award XP to students (role: 'siswa')
        if (!$user->hasRole('siswa')) {
            return false;
        }

        // 2. Prevent duplicate XP for same activity and reference
        if ($referenceType && $referenceId) {
            $exists = DB::table('xp_logs')
                ->where('user_id', $user->id)
                ->where('reference_type', $referenceType)
                ->where('reference_id', $referenceId)
                ->where('activity_type', $activityType)
                ->exists();
            if ($exists) {
                return false;
            }
        }

        $xpEarned = self::XP_MAP[$activityType] ?? 0;
        if ($xpEarned <= 0) {
            return false;
        }

        // 3. Generate description
        $description = $customDesc ?? self::getDefaultDescription($activityType);

        DB::transaction(function() use ($user, $moduleId, $activityType, $xpEarned, $description, $referenceType, $referenceId) {
            // Update legacy users points
            $user->increment('points', $xpEarned);

            // Update/Create student_xp
            $studentXp = DB::table('student_xp')->where('user_id', $user->id)->first();
            if ($studentXp) {
                DB::table('student_xp')->where('user_id', $user->id)->update([
                    'total_xp' => $studentXp->total_xp + $xpEarned,
                    'updated_at' => now()
                ]);
            } else {
                DB::table('student_xp')->insert([
                    'user_id' => $user->id,
                    'total_xp' => $xpEarned,
                    'created_at' => now(),
                    'updated_at' => now()
                ]);
            }

            // Insert into xp_logs
            DB::table('xp_logs')->insert([
                'user_id' => $user->id,
                'module_id' => $moduleId,
                'activity_type' => $activityType,
                'xp_earned' => $xpEarned,
                'description' => $description,
                'reference_type' => $referenceType,
                'reference_id' => $referenceId,
                'created_at' => now(),
                'updated_at' => now()
            ]);
        });

        // 4. Auto-complete learning activity
        if ($moduleId) {
            self::autoCompleteActivity($user, $activityType, $moduleId, $referenceType, $referenceId);
        }

        // 5. Update module progress
        if ($moduleId) {
            self::updateModuleProgress($user, $moduleId);
        }

        return true;
    }

    /**
     * Auto-completes the corresponding learning activity when XP is awarded.
     */
    public static function autoCompleteActivity(User $user, string $activityType, int $moduleId, ?string $referenceType, ?int $referenceId): void
    {
        $query = DB::table('learning_activities')->where('module_id', $moduleId);

        if ($activityType === 'mind_map' && $referenceType === 'MaterialStepProgress') {
            $materialId = DB::table('material_step_progress')->where('id', $referenceId)->value('material_id');
            $query->where('activity_type', 'mind_map')->where('material_id', $materialId);
        } elseif ($activityType === 'module_read' && $referenceType === 'MaterialStepProgress') {
            $materialId = DB::table('material_step_progress')->where('id', $referenceId)->value('material_id');
            $query->where('activity_type', 'material')->where('material_id', $materialId);
        } elseif ($activityType === 'video_watch' && $referenceType === 'MaterialStepProgress') {
            $materialId = DB::table('material_step_progress')->where('id', $referenceId)->value('material_id');
            $query->where('activity_type', 'video')->where('material_id', $materialId);
        } elseif ($activityType === 'video_watch' && $referenceType === 'VideoActivityLog') {
            $videoId = DB::table('video_activity_logs')->where('id', $referenceId)->value('video_id');
            $query->where('activity_type', 'video')->where('video_id', $videoId);
        } elseif ($activityType === 'video_quiz' && $referenceType === 'VideoParticipationTracking') {
            $materialId = DB::table('video_participation_tracking')->where('id', $referenceId)->value('material_id');
            $query->where('activity_type', 'video')->where('material_id', $materialId);
        } elseif ($activityType === 'video_quiz' && $referenceType === 'VideoActivityLog') {
            $videoId = DB::table('video_activity_logs')->where('id', $referenceId)->value('video_id');
            $query->where('activity_type', 'video')->where('video_id', $videoId);
        } elseif ($activityType === 'coding_quiz' && $referenceType === 'CodingQuizAttempt') {
            $materialId = DB::table('coding_quiz_attempts')
                ->join('coding_quizzes', 'coding_quiz_attempts.coding_quiz_id', '=', 'coding_quizzes.id')
                ->where('coding_quiz_attempts.id', $referenceId)
                ->value('coding_quizzes.material_id');
            $query->where('activity_type', 'coding_quiz')->where('material_id', $materialId);
        } elseif ($activityType === 'reflection' && $referenceType === 'MaterialStepProgress') {
            $materialId = DB::table('material_step_progress')->where('id', $referenceId)->value('material_id');
            $query->where('activity_type', 'reflection')->where('material_id', $materialId);
        } elseif ($activityType === 'quiz' && $referenceType === 'QuizAttempt') {
            $quizId = DB::table('quiz_attempts')->where('id', $referenceId)->value('quiz_id');
            $query->where('activity_type', 'quiz')->where('quiz_id', $quizId);
        } elseif ($activityType === 'assignment' && $referenceType === 'Submission') {
            $assignmentId = DB::table('submissions')->where('id', $referenceId)->value('assignment_id');
            $query->where('activity_type', 'assignment')->where('assignment_id', $assignmentId);
        } elseif ($activityType === 'discussion_reply' && $referenceType === 'DiscussionReply') {
            $discussionId = DB::table('discussion_replies')->where('id', $referenceId)->value('discussion_id');
            $query->where('activity_type', 'discussion')->where('discussion_id', $discussionId);
        } else {
            return;
        }

        $activity = $query->first();
        if ($activity) {
            DB::table('learning_activity_progress')->updateOrInsert(
                ['user_id' => $user->id, 'learning_activity_id' => $activity->id],
                ['is_completed' => true, 'completed_at' => now()]
            );
        }
    }

    /**
     * Dynamically update the student's progress for a module (BAB).
     */
    public static function updateModuleProgress(User $user, int $moduleId): void
    {
        $activities = DB::table('learning_activities')->where('module_id', $moduleId)->orderBy('order_number')->get();
        if ($activities->isEmpty()) {
            DB::table('student_progress')->updateOrInsert(
                ['user_id' => $user->id, 'module_id' => $moduleId],
                [
                    'progress_percentage' => 0,
                    'completed_activities' => json_encode([]),
                    'updated_at' => now()
                ]
            );
            return;
        }

        $totalActivities = $activities->count();
        $completedActivities = [];

        foreach ($activities as $activity) {
            $isCompleted = DB::table('learning_activity_progress')
                ->where('user_id', $user->id)
                ->where('learning_activity_id', $activity->id)
                ->where('is_completed', true)
                ->exists();

            if ($isCompleted) {
                $completedActivities[] = "activity_{$activity->id}";
            }
        }

        $completedCount = count($completedActivities);
        $percentage = round(($completedCount / $totalActivities) * 100);

        DB::table('student_progress')->updateOrInsert(
            ['user_id' => $user->id, 'module_id' => $moduleId],
            [
                'progress_percentage' => $percentage,
                'completed_activities' => json_encode($completedActivities),
                'updated_at' => now()
            ]
        );
    }

    /**
     * Get default description based on activity type.
     */
    private static function getDefaultDescription(string $type): string
    {
        switch ($type) {
            case 'mind_map':
                return 'Menyelesaikan Mind Map';
            case 'module_read':
                return 'Membaca Modul Interaktif';
            case 'video_watch':
                return 'Menonton Video Pembelajaran';
            case 'video_quiz':
                return 'Menjawab Kuis Video Interaktif dengan Benar';
            case 'quiz':
                return 'Menyelesaikan Kuis';
            case 'coding_quiz':
                return 'Menyelesaikan Kuis Koding dengan Benar';
            case 'reflection':
                return 'Mengirimkan Refleksi Pembelajaran';
            case 'assignment':
                return 'Mengumpulkan Tugas';
            case 'discussion_reply':
                return 'Memberikan Reply Diskusi';
            default:
                return 'Aktivitas pembelajaran selesai';
        }
    }
}
