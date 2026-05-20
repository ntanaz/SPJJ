<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('learning_activities', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->string('activity_type');
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order_number')->default(1);
            $table->boolean('is_required')->default(true);
            $table->foreignId('material_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('quiz_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('assignment_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('discussion_id')->nullable()->constrained()->nullOnDelete();
            $table->timestamps();
        });

        Schema::create('learning_activity_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('learning_activity_id')->constrained('learning_activities')->cascadeOnDelete();
            $table->boolean('is_completed')->default(true);
            $table->timestamp('completed_at')->nullable();
            $table->unique(['user_id', 'learning_activity_id'], 'user_activity_unique');
            $table->timestamps();
        });

        // Seed initial learning activities from existing data
        $modules = DB::table('modules')->get();
        foreach ($modules as $module) {
            $order = 1;

            // 1. Discover materials in this module
            $materials = DB::table('materials')->where('module_id', $module->id)->orderBy('order')->get();
            foreach ($materials as $mat) {
                // Mind map activity
                if ($mat->mind_map_path) {
                    $actId = DB::table('learning_activities')->insertGetId([
                        'module_id' => $module->id,
                        'activity_type' => 'mind_map',
                        'title' => 'Peta Pikiran - ' . $mat->title,
                        'description' => 'Menganalisis mind map untuk materi ' . $mat->title,
                        'order_number' => $order++,
                        'is_required' => true,
                        'material_id' => $mat->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Migrate progress for mind_map
                    $completedSteps = DB::table('material_step_progress')
                        ->where('material_id', $mat->id)
                        ->where('step', 'mind_map')
                        ->where('is_completed', true)
                        ->get();
                    foreach ($completedSteps as $cs) {
                        DB::table('learning_activity_progress')->updateOrInsert(
                            ['user_id' => $cs->user_id, 'learning_activity_id' => $actId],
                            ['is_completed' => true, 'completed_at' => $cs->completed_at ?? now()]
                        );
                    }
                }

                // Material reading activity
                if ($mat->file_path || $mat->text_content) {
                    $actId = DB::table('learning_activities')->insertGetId([
                        'module_id' => $module->id,
                        'activity_type' => 'material',
                        'title' => 'Membaca Modul - ' . $mat->title,
                        'description' => 'Mempelajari modul pembelajaran untuk ' . $mat->title,
                        'order_number' => $order++,
                        'is_required' => true,
                        'material_id' => $mat->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Migrate progress for material/reading
                    $completedSteps = DB::table('material_step_progress')
                        ->where('material_id', $mat->id)
                        ->where('step', 'modul')
                        ->where('is_completed', true)
                        ->get();
                    foreach ($completedSteps as $cs) {
                        DB::table('learning_activity_progress')->updateOrInsert(
                            ['user_id' => $cs->user_id, 'learning_activity_id' => $actId],
                            ['is_completed' => true, 'completed_at' => $cs->completed_at ?? now()]
                        );
                    }
                }

                // Video watching activity
                if ($mat->youtube_url) {
                    $actId = DB::table('learning_activities')->insertGetId([
                        'module_id' => $module->id,
                        'activity_type' => 'video',
                        'title' => 'Video Pembelajaran - ' . $mat->title,
                        'description' => 'Menonton video pembelajaran interaktif untuk ' . $mat->title,
                        'order_number' => $order++,
                        'is_required' => true,
                        'material_id' => $mat->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Migrate progress for video
                    $completedSteps = DB::table('material_step_progress')
                        ->where('material_id', $mat->id)
                        ->where('step', 'video')
                        ->where('is_completed', true)
                        ->get();
                    foreach ($completedSteps as $cs) {
                        DB::table('learning_activity_progress')->updateOrInsert(
                            ['user_id' => $cs->user_id, 'learning_activity_id' => $actId],
                            ['is_completed' => true, 'completed_at' => $cs->completed_at ?? now()]
                        );
                    }
                }

                // Coding Quiz activity
                $codingQuizExists = DB::table('coding_quizzes')->where('material_id', $mat->id)->exists();
                if ($codingQuizExists) {
                    $actId = DB::table('learning_activities')->insertGetId([
                        'module_id' => $module->id,
                        'activity_type' => 'coding_quiz',
                        'title' => 'Kuis Koding - ' . $mat->title,
                        'description' => 'Menyelesaikan kuis koding interaktif untuk ' . $mat->title,
                        'order_number' => $order++,
                        'is_required' => true,
                        'material_id' => $mat->id,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);

                    // Migrate progress for coding
                    $completedSteps = DB::table('material_step_progress')
                        ->where('material_id', $mat->id)
                        ->where('step', 'coding')
                        ->where('is_completed', true)
                        ->get();
                    foreach ($completedSteps as $cs) {
                        DB::table('learning_activity_progress')->updateOrInsert(
                            ['user_id' => $cs->user_id, 'learning_activity_id' => $actId],
                            ['is_completed' => true, 'completed_at' => $cs->completed_at ?? now()]
                        );
                    }
                }

                // Reflection activity
                $actId = DB::table('learning_activities')->insertGetId([
                    'module_id' => $module->id,
                    'activity_type' => 'reflection',
                    'title' => 'Refleksi Mandiri - ' . $mat->title,
                    'description' => 'Mengisi kuesioner refleksi pembelajaran untuk ' . $mat->title,
                    'order_number' => $order++,
                    'is_required' => true,
                    'material_id' => $mat->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Migrate progress for reflection
                $completedSteps = DB::table('material_step_progress')
                    ->where('material_id', $mat->id)
                    ->where('step', 'reflection')
                    ->where('is_completed', true)
                    ->get();
                foreach ($completedSteps as $cs) {
                    DB::table('learning_activity_progress')->updateOrInsert(
                        ['user_id' => $cs->user_id, 'learning_activity_id' => $actId],
                        ['is_completed' => true, 'completed_at' => $cs->completed_at ?? now()]
                    );
                }
            }

            // 2. Discover quizzes
            $quizzes = DB::table('quizzes')->where('module_id', $module->id)->get();
            foreach ($quizzes as $quiz) {
                $actId = DB::table('learning_activities')->insertGetId([
                    'module_id' => $module->id,
                    'activity_type' => 'quiz',
                    'title' => 'Kuis: ' . $quiz->title,
                    'description' => $quiz->description,
                    'order_number' => $order++,
                    'is_required' => true,
                    'quiz_id' => $quiz->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Migrate progress for quiz attempts
                $attempts = DB::table('quiz_attempts')
                    ->where('quiz_id', $quiz->id)
                    ->where('status', 'completed')
                    ->get();
                foreach ($attempts as $attempt) {
                    DB::table('learning_activity_progress')->updateOrInsert(
                        ['user_id' => $attempt->user_id, 'learning_activity_id' => $actId],
                        ['is_completed' => true, 'completed_at' => $attempt->updated_at ?? now()]
                    );
                }
            }

            // 3. Discover assignments
            $assignments = DB::table('assignments')->where('module_id', $module->id)->get();
            foreach ($assignments as $assignment) {
                $actId = DB::table('learning_activities')->insertGetId([
                    'module_id' => $module->id,
                    'activity_type' => 'assignment',
                    'title' => 'Tugas: ' . $assignment->title,
                    'description' => $assignment->description,
                    'order_number' => $order++,
                    'is_required' => true,
                    'assignment_id' => $assignment->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Migrate progress for assignment submissions
                $submissions = DB::table('submissions')
                    ->where('assignment_id', $assignment->id)
                    ->where('status', 'submitted')
                    ->get();
                foreach ($submissions as $sub) {
                    DB::table('learning_activity_progress')->updateOrInsert(
                        ['user_id' => $sub->user_id, 'learning_activity_id' => $actId],
                        ['is_completed' => true, 'completed_at' => $sub->updated_at ?? now()]
                    );
                }
            }

            // 4. Discover discussions
            $discussions = DB::table('discussions')->where('module_id', $module->id)->get();
            foreach ($discussions as $discussion) {
                $actId = DB::table('learning_activities')->insertGetId([
                    'module_id' => $module->id,
                    'activity_type' => 'discussion',
                    'title' => 'Forum Diskusi: ' . $discussion->title,
                    'description' => $discussion->content,
                    'order_number' => $order++,
                    'is_required' => true,
                    'discussion_id' => $discussion->id,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);

                // Migrate progress for discussion participation (if student commented)
                $replies = DB::table('discussion_replies')
                    ->where('discussion_id', $discussion->id)
                    ->get();
                foreach ($replies as $rep) {
                    DB::table('learning_activity_progress')->updateOrInsert(
                        ['user_id' => $rep->user_id, 'learning_activity_id' => $actId],
                        ['is_completed' => true, 'completed_at' => $rep->created_at ?? now()]
                    );
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('learning_activity_progress');
        Schema::dropIfExists('learning_activities');
    }
};
