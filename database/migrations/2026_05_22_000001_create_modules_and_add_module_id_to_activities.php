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
        // 1. Create modules table
        Schema::create('modules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('course_id')->constrained()->cascadeOnDelete();
            $table->string('title');
            $table->text('description')->nullable();
            $table->integer('order_number')->default(1);
            $table->timestamps();
        });

        // 2. Add module_id to materials, quizzes, discussions, assignments
        Schema::table('materials', function (Blueprint $table) {
            $table->foreignId('module_id')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->foreignId('module_id')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::table('discussions', function (Blueprint $table) {
            $table->foreignId('module_id')->nullable()->constrained()->cascadeOnDelete();
        });

        Schema::table('assignments', function (Blueprint $table) {
            $table->foreignId('module_id')->nullable()->constrained()->cascadeOnDelete();
        });

        // 3. For each existing Course, create a default module and migrate existing data
        $courses = DB::table('courses')->get();
        foreach ($courses as $course) {
            $moduleId = DB::table('modules')->insertGetId([
                'course_id' => $course->id,
                'title' => 'Bab 1 - Modul Utama',
                'description' => 'Modul default untuk kelas ini.',
                'order_number' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            // Update activities belonging to this course
            DB::table('materials')->where('course_id', $course->id)->update(['module_id' => $moduleId]);
            DB::table('quizzes')->where('course_id', $course->id)->update(['module_id' => $moduleId]);
            DB::table('discussions')->where('course_id', $course->id)->update(['module_id' => $moduleId]);
            DB::table('assignments')->where('course_id', $course->id)->update(['module_id' => $moduleId]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('assignments', function (Blueprint $table) {
            $table->dropForeign(['module_id']);
            $table->dropColumn('module_id');
        });

        Schema::table('discussions', function (Blueprint $table) {
            $table->dropForeign(['module_id']);
            $table->dropColumn('module_id');
        });

        Schema::table('quizzes', function (Blueprint $table) {
            $table->dropForeign(['module_id']);
            $table->dropColumn('module_id');
        });

        Schema::table('materials', function (Blueprint $table) {
            $table->dropForeign(['module_id']);
            $table->dropColumn('module_id');
        });

        Schema::dropIfExists('modules');
    }
};
