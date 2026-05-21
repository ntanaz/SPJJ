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
        // 1. Add description to videos table if it doesn't exist
        if (!Schema::hasColumn('videos', 'description')) {
            Schema::table('videos', function (Blueprint $table) {
                $table->text('description')->nullable()->after('video_path');
            });
        }

        // 2. Create video_quiz_attempts table if it doesn't exist
        if (!Schema::hasTable('video_quiz_attempts')) {
            Schema::create('video_quiz_attempts', function (Blueprint $table) {
                $table->id();
                $table->foreignId('user_id')->constrained()->cascadeOnDelete();
                $table->foreignId('video_quiz_id')->constrained('video_quizzes')->cascadeOnDelete();
                $table->text('answer');
                $table->boolean('is_correct')->default(false);
                $table->timestamps();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('video_quiz_attempts');

        if (Schema::hasColumn('videos', 'description')) {
            Schema::table('videos', function (Blueprint $table) {
                $table->dropColumn('description');
            });
        }
    }
};
