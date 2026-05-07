<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('submissions', function (Blueprint $table) {
            $table->text('text_content')->nullable();
            $table->string('status')->default('submitted');
            $table->boolean('is_late')->default(false);
            $table->json('attachments')->nullable(); // For multiple files
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->integer('time_limit_minutes')->nullable();
            $table->dateTime('deadline')->nullable();
            $table->boolean('show_results')->default(false);
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->string('status')->default('completed'); // 'in_progress', 'completed', 'timeout'
            $table->dateTime('started_at')->nullable();
        });

        Schema::table('attendances', function (Blueprint $table) {
            $table->foreignId('pre_test_quiz_id')->nullable()->constrained('quizzes')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('attendances', function (Blueprint $table) {
            $table->dropForeign(['pre_test_quiz_id']);
            $table->dropColumn('pre_test_quiz_id');
        });

        Schema::table('quiz_attempts', function (Blueprint $table) {
            $table->dropColumn(['status', 'started_at']);
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropColumn(['time_limit_minutes', 'deadline', 'show_results']);
        });

        Schema::table('submissions', function (Blueprint $table) {
            $table->dropColumn(['text_content', 'status', 'is_late', 'attachments']);
        });
    }
};
