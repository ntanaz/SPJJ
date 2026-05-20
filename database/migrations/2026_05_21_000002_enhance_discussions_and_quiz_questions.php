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
        // 1. Enhance discussions table with material_id (nullable, relation to materials)
        Schema::table('discussions', function (Blueprint $table) {
            $table->foreignId('material_id')->nullable()->after('course_id')->constrained()->cascadeOnDelete();
        });

        // 2. Enhance quiz_questions table with question_type, feedback, correct_answers (optional if JSON format etc.)
        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->string('question_type')->default('multiple_choice')->after('quiz_id'); // multiple_choice, true_false, short_answer, fill_blank, reflection, debugging
            $table->text('feedback')->nullable()->after('correct_answer');
            $table->json('options')->nullable()->change(); // Make it nullable as other question types don't need options
        });

        // 3. Enhance quiz_answers table with text_answer for free-text answers (short answer, reflection, debugging)
        Schema::table('quiz_answers', function (Blueprint $table) {
            $table->text('text_answer')->nullable()->after('selected_option');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('quiz_answers', function (Blueprint $table) {
            $table->dropColumn('text_answer');
        });

        Schema::table('quiz_questions', function (Blueprint $table) {
            $table->json('options')->nullable(false)->change();
            $table->dropColumn(['question_type', 'feedback']);
        });

        Schema::table('discussions', function (Blueprint $table) {
            $table->dropForeign(['material_id']);
            $table->dropColumn('material_id');
        });
    }
};
