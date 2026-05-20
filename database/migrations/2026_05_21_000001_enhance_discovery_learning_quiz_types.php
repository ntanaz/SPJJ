<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Adds quiz type support to Discovery Learning components:
     * - interactive_video_questions: question_type (multiple_choice, true_false, short_answer)
     * - coding_quizzes: quiz_type (fill_blank, debugging, short_answer)
     */
    public function up(): void
    {
        // Add question_type to interactive video questions
        Schema::table('interactive_video_questions', function (Blueprint $table) {
            $table->string('question_type')->default('multiple_choice')->after('correct_answer');
            // question_type: multiple_choice | true_false | short_answer
        });

        // Add quiz_type to coding quizzes
        Schema::table('coding_quizzes', function (Blueprint $table) {
            $table->string('quiz_type')->default('fill_blank')->after('material_id');
            // quiz_type: fill_blank | debugging | short_answer
        });

        // Add answered_at to video_participation_tracking for better analytics
        Schema::table('video_participation_tracking', function (Blueprint $table) {
            $table->dateTime('answered_at')->nullable()->after('activity_log');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('video_participation_tracking', function (Blueprint $table) {
            $table->dropColumn('answered_at');
        });

        Schema::table('coding_quizzes', function (Blueprint $table) {
            $table->dropColumn('quiz_type');
        });

        Schema::table('interactive_video_questions', function (Blueprint $table) {
            $table->dropColumn('question_type');
        });
    }
};
