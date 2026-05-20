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
        Schema::create('videos', function (Blueprint $table) {
            $table->id();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->string('video_path');
            $table->integer('duration')->default(0);
            $table->timestamps();
        });

        Schema::create('video_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_id')->constrained('videos')->cascadeOnDelete();
            $table->integer('timestamp_seconds');
            $table->text('question');
            $table->string('question_type')->default('multiple_choice');
            $table->text('feedback')->nullable();
            $table->timestamps();
        });

        Schema::create('video_quiz_options', function (Blueprint $table) {
            $table->id();
            $table->foreignId('video_quiz_id')->constrained('video_quizzes')->cascadeOnDelete();
            $table->string('option_text');
            $table->boolean('is_correct')->default(false);
            $table->timestamps();
        });

        Schema::create('video_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('video_id')->constrained('videos')->cascadeOnDelete();
            $table->integer('watched_duration')->default(0);
            $table->boolean('completed')->default(false);
            $table->json('answered_quiz')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'video_id']);
        });

        Schema::table('learning_activities', function (Blueprint $table) {
            $table->foreignId('video_id')->nullable()->constrained('videos')->nullOnDelete();
        });

        // Migrate existing video materials
        $materials = DB::table('materials')
            ->where('format', 'video')
            ->orWhere('type', 'video')
            ->orWhereNotNull('youtube_url')
            ->get();

        foreach ($materials as $material) {
            if (empty($material->module_id)) {
                continue;
            }

            // Find or create video record
            $videoPath = $material->file_path ?? $material->youtube_url ?? '';
            if (empty($videoPath)) {
                continue;
            }

            $videoId = DB::table('videos')->insertGetId([
                'module_id' => $material->module_id,
                'title' => $material->title,
                'video_path' => $videoPath,
                'duration' => 0,
                'created_at' => $material->created_at ?? now(),
                'updated_at' => $material->updated_at ?? now(),
            ]);

            // Update corresponding learning activity
            DB::table('learning_activities')
                ->where('material_id', $material->id)
                ->where('activity_type', 'video')
                ->update(['video_id' => $videoId]);

            // Migrate interactive questions to video_quizzes
            $questions = DB::table('interactive_video_questions')
                ->where('material_id', $material->id)
                ->get();

            foreach ($questions as $q) {
                $qType = $q->question_type ?? 'multiple_choice';
                
                $quizId = DB::table('video_quizzes')->insertGetId([
                    'video_id' => $videoId,
                    'timestamp_seconds' => $q->timestamp,
                    'question' => $q->question,
                    'question_type' => $qType,
                    'feedback' => $q->feedback ?? null,
                    'created_at' => $q->created_at ?? now(),
                    'updated_at' => $q->updated_at ?? now(),
                ]);

                // Migrate options
                $options = [];
                if (!empty($q->options)) {
                    $decoded = json_decode($q->options, true);
                    if (is_array($decoded)) {
                        $options = $decoded;
                    } else if (is_string($q->options)) {
                        $options = array_map('trim', explode(',', $q->options));
                    }
                }

                if ($qType === 'true_false' && empty($options)) {
                    $options = ['Benar', 'Salah'];
                }

                foreach ($options as $opt) {
                    if (empty($opt)) continue;
                    $isCorrect = (trim(strtolower($opt)) === trim(strtolower($q->correct_answer)));
                    
                    DB::table('video_quiz_options')->insert([
                        'video_quiz_id' => $quizId,
                        'option_text' => $opt,
                        'is_correct' => $isCorrect,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]);
                }
            }

            // Migrate video participation tracking
            $trackings = DB::table('video_participation_tracking')
                ->where('material_id', $material->id)
                ->get();

            foreach ($trackings as $track) {
                // Check if log already exists
                $exists = DB::table('video_activity_logs')
                    ->where('user_id', $track->user_id)
                    ->where('video_id', $videoId)
                    ->exists();

                if (!$exists) {
                    DB::table('video_activity_logs')->insert([
                        'user_id' => $track->user_id,
                        'video_id' => $videoId,
                        'watched_duration' => 0,
                        'completed' => true,
                        'answered_quiz' => json_encode([]),
                        'created_at' => $track->created_at ?? now(),
                        'updated_at' => $track->updated_at ?? now(),
                    ]);
                }
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('learning_activities', 'video_id')) {
            Schema::table('learning_activities', function (Blueprint $table) {
                $table->dropForeign(['video_id']);
                $table->dropColumn('video_id');
            });
        }

        Schema::dropIfExists('video_activity_logs');
        Schema::dropIfExists('video_quiz_options');
        Schema::dropIfExists('video_quizzes');
        Schema::dropIfExists('videos');
    }
};
