<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('key')->unique();
            $table->text('value')->nullable();
            $table->string('description')->nullable();
            $table->string('type')->default('string'); // string, boolean, integer, etc.
            $table->timestamps();
        });

        Schema::create('announcements', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('content');
            $table->enum('urgency_level', ['normal', 'important', 'urgent'])->default('normal');
            $table->string('target_audience')->default('all'); // all, admin, teacher, student, etc.
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Author
            $table->timestamps();
        });

        Schema::create('learning_resources', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('file_path')->nullable();
            $table->string('type'); // pdf, docx, pptx, image, etc.
            $table->string('category')->nullable(); // bank soal, materi pembelajaran
            $table->foreignId('user_id')->constrained()->cascadeOnDelete(); // Uploader
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('learning_resources');
        Schema::dropIfExists('announcements');
        Schema::dropIfExists('settings');
    }
};
