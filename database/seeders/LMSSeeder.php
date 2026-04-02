<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Spatie\Permission\Models\Role;
use App\Models\Course;
use App\Models\Material;

class LMSSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        \Illuminate\Database\Eloquent\Model::unguard();

        // Define Roles
        $adminRole = Role::firstOrCreate(['name' => 'admin']);
        $guruRole = Role::firstOrCreate(['name' => 'guru']);
        $siswaRole = Role::firstOrCreate(['name' => 'siswa']);

        // Create Admin
        $admin = User::updateOrCreate(
            ['email' => 'admin@lms.com'],
            [
                'name' => 'Admin LMS',
                'password' => Hash::make('password'),
            ]
        );
        $admin->assignRole($adminRole);

        // Create Guru
        $guru = User::updateOrCreate(
            ['email' => 'guru@lms.com'],
            [
                'name' => 'Guru Budi',
                'password' => Hash::make('password'),
            ]
        );
        $guru->assignRole($guruRole);

        // Create Siswa
        $siswa = User::updateOrCreate(
            ['email' => 'siswa@lms.com'],
            [
                'name' => 'Siswa Andi',
                'password' => Hash::make('password'),
                'points' => 0
            ]
        );
        $siswa->assignRole($siswaRole);

        // Sample Course
        $course = Course::firstOrCreate(
            ['name' => 'Kecerdasan Artifisial (AI)'],
            ['description' => 'Mata pelajaran Dasar-dasar AI dan Machine Learning untuk siswa SMA.']
        );

        // Sample Class
        $class = \App\Models\CourseClass::firstOrCreate(
            ['name' => '10A - AI', 'course_id' => $course->id],
            ['teacher_id' => $guru->id]
        );

        // Assign Siswa to Class
        \Illuminate\Support\Facades\DB::table('class_user')->updateOrInsert(
            ['course_class_id' => $class->id, 'user_id' => $siswa->id],
            ['created_at' => now(), 'updated_at' => now()]
        );

        // Sample Material
        $material1 = Material::firstOrCreate(
            ['course_id' => $course->id, 'title' => 'Bab 1: Pengenalan AI'],
            [
                'description' => 'Sejarah dan konsep dasar Kecerdasan Artifisial.',
                'type' => 'pdf',
                'order' => 1,
                'is_locked' => false
            ]
        );

        $material2 = Material::firstOrCreate(
            ['course_id' => $course->id, 'title' => 'Bab 2: Machine Learning'],
            [
                'description' => 'Penerapan Machine learning sederhana.',
                'type' => 'video',
                'order' => 2,
                'is_locked' => true
            ]
        );

        // Sample Quiz
        $quiz = \App\Models\Quiz::firstOrCreate(
            ['course_id' => $course->id, 'title' => 'Kuis Dasar AI'],
            ['description' => 'Uji pemahaman dasar tentang Kecerdasan Artifisial.']
        );

        // Sample Questions
        $questions = [
            [
                'question' => 'Apa kepanjangan dari AI?',
                'options' => ['A' => 'Artificial Intelligence', 'B' => 'Automatic Interface', 'C' => 'Advanced Integration', 'D' => 'Actual Input'],
                'correct_answer' => 'A',
                'points' => 10
            ],
            [
                'question' => 'Siapa ilmuwan yang sering disebut sebagai bapak AI?',
                'options' => ['A' => 'Bill Gates', 'B' => 'Alan Turing', 'C' => 'Steve Jobs', 'D' => 'Elon Musk'],
                'correct_answer' => 'B',
                'points' => 10
            ],
            [
                'question' => 'Mana yang merupakan contoh Machine Learning?',
                'options' => ['A' => 'Kalkulator', 'B' => 'Sistem Rekomendasi Netflix', 'C' => 'Lampu Senter', 'D' => 'Jam Dinding'],
                'correct_answer' => 'B',
                'points' => 10
            ]
        ];

        foreach ($questions as $q) {
            $quiz->questions()->firstOrCreate(['question' => $q['question']], $q);
        }
    }
}
