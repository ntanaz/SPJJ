<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // 1. Add mind_map_path to materials
        Schema::table('materials', function (Blueprint $table) {
            $table->string('mind_map_path')->nullable();
        });

        // 2. Interactive Video Questions
        Schema::create('interactive_video_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->integer('timestamp'); // Time in seconds
            $table->text('question');
            $table->json('options'); // Multiple choice options array
            $table->string('correct_answer');
            $table->timestamps();
        });

        // 3. Video Participation Tracking
        Schema::create('video_participation_tracking', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('question_id')->constrained('interactive_video_questions')->cascadeOnDelete();
            $table->string('selected_answer');
            $table->boolean('is_correct');
            $table->integer('timestamp'); // When answered
            $table->json('activity_log')->nullable(); // Player activity log
            $table->timestamps();
        });

        // 4. Coding Quizzes
        Schema::create('coding_quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->string('title')->nullable();
            $table->text('instruction');
            $table->text('code_template'); // Code template with [blank] placeholder(s)
            $table->json('correct_answers'); // Array of correct answers for blanks
            $table->text('feedback_correct')->nullable();
            $table->text('feedback_incorrect')->nullable();
            $table->timestamps();
        });

        // 5. Coding Quiz Attempts & Reflection & Grading
        Schema::create('coding_quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('coding_quiz_id')->constrained()->cascadeOnDelete();
            $table->integer('percobaan_ke');
            $table->json('jawaban'); // Array of answers filled by student
            $table->boolean('hasil_validasi');
            $table->text('feedback')->nullable();
            $table->dateTime('waktu_submit')->nullable();
            $table->text('reflection')->nullable();
            $table->integer('correctness_grade')->nullable();
            $table->integer('reflection_grade')->nullable();
            $table->integer('final_grade')->nullable();
            $table->dateTime('graded_at')->nullable();
            $table->timestamps();
        });

        // 6. Material Step Progress for students
        Schema::create('material_step_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->string('step'); // 'mind_map', 'modul', 'video', 'coding', 'reflection'
            $table->boolean('is_completed')->default(false);
            $table->dateTime('completed_at')->nullable();
            $table->timestamps();

            $table->unique(['user_id', 'material_id', 'step']);
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('material_step_progress');
        Schema::dropIfExists('coding_quiz_attempts');
        Schema::dropIfExists('coding_quizzes');
        Schema::dropIfExists('video_participation_tracking');
        Schema::dropIfExists('interactive_video_questions');
        
        Schema::table('materials', function (Blueprint $table) {
            $table->dropColumn('mind_map_path');
        });
    }
};
