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
        // 1. student_xp table
        Schema::create('student_xp', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->unique()->constrained()->cascadeOnDelete();
            $table->integer('total_xp')->default(0);
            $table->timestamps();
        });

        // 2. xp_logs table
        Schema::create('xp_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->nullable()->constrained()->cascadeOnDelete();
            $table->string('activity_type');
            $table->integer('xp_earned');
            $table->string('description');
            $table->string('reference_type')->nullable();
            $table->unsignedBigInteger('reference_id')->nullable();
            $table->timestamps();
        });

        // 3. student_progress table
        Schema::create('student_progress', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('module_id')->constrained()->cascadeOnDelete();
            $table->integer('progress_percentage')->default(0);
            $table->json('completed_activities')->nullable();
            $table->timestamps();
            
            $table->unique(['user_id', 'module_id']);
        });

        // Seed initial values for student_xp from legacy user points
        try {
            $students = \DB::table('users')
                ->whereExists(function ($query) {
                    $query->select(\DB::raw(1))
                        ->from('model_has_roles')
                        ->join('roles', 'model_has_roles.role_id', '=', 'roles.id')
                        ->whereColumn('model_has_roles.model_id', 'users.id')
                        ->where('roles.name', 'siswa');
                })
                ->get();

            foreach ($students as $student) {
                \DB::table('student_xp')->insertOrIgnore([
                    'user_id' => $student->id,
                    'total_xp' => $student->points ?? 0,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        } catch (\Exception $e) {
            // Log or ignore if table/roles not set up yet in standard tests
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('student_progress');
        Schema::dropIfExists('xp_logs');
        Schema::dropIfExists('student_xp');
    }
};
