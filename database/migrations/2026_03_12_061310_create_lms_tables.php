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
        // 1. Courses (Mata Pelajaran)
        Schema::create('courses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->string('cover_image')->nullable();
            $table->timestamps();
        });

        // 2. Course Classes (Kelas)
        Schema::create('course_classes', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('teacher_id')->constrained('users')->cascadeOnDelete();
            $table->timestamps();
        });

        // Pivot table for Siswa in Kelas
        Schema::create('class_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_class_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });

        // 3. Materials (Materi)
        Schema::create('materials', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->enum('type', ['pdf', 'video', 'slide', 'file', 'meeting_link', 'video_post_class']);
            $table->string('file_path')->nullable();
            $table->integer('order')->default(0);
            $table->boolean('is_locked')->default(false);
            $table->timestamps();
        });

        // Student Progress tracking
        Schema::create('material_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('material_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->boolean('is_completed')->default(false);
            $table->timestamps();
        });

        // 4. Assignments
        Schema::create('assignments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->dateTime('deadline');
            $table->timestamps();
        });

        Schema::create('submissions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('assignment_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->string('file_path')->nullable();
            $table->integer('grade')->nullable();
            $table->text('feedback')->nullable();
            $table->timestamps();
        });

        // 5. Discussions
        Schema::create('discussions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->text('content');
            $table->timestamps();
        });

        // 6. Quizzes & Exams
        Schema::create('quizzes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->timestamps();
        });

        Schema::create('quiz_questions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->string('question');
            $table->json('options');
            $table->string('correct_answer');
            $table->integer('points')->default(10);
            $table->timestamps();
        });

        Schema::create('quiz_attempts', function (Blueprint $table) {
            $table->id();
            $table->foreignId('quiz_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->integer('score')->default(0);
            $table->timestamps();
        });

        // 7. Gamification Badges
        Schema::create('badges', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('icon')->nullable();
            $table->text('description')->nullable();
            $table->integer('required_points')->default(0);
            $table->timestamps();
        });

        Schema::create('badge_user', function (Blueprint $table) {
            $table->id();
            $table->foreignId('badge_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('badge_user');
        Schema::dropIfExists('badges');
        Schema::dropIfExists('quiz_attempts');
        Schema::dropIfExists('quiz_questions');
        Schema::dropIfExists('quizzes');
        Schema::dropIfExists('discussions');
        Schema::dropIfExists('submissions');
        Schema::dropIfExists('assignments');
        Schema::dropIfExists('material_user');
        Schema::dropIfExists('materials');
        Schema::dropIfExists('class_user');
        Schema::dropIfExists('course_classes');
        Schema::dropIfExists('courses');
    }
};
