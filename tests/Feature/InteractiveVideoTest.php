<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Module;
use App\Models\Video;
use App\Models\VideoQuiz;
use App\Models\VideoQuizOption;
use App\Models\VideoQuizAttempt;
use App\Models\VideoActivityLog;
use App\Models\User;
use App\Models\LearningActivity;
use Database\Seeders\AdminRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class InteractiveVideoTest extends TestCase
{
    use RefreshDatabase;

    protected $teacher;
    protected $student;
    protected $course;
    protected $module;
    protected $video;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $this->seed(AdminRoleSeeder::class);

        // Create teacher
        $this->teacher = User::factory()->create();
        $this->teacher->assignRole('teacher');

        // Create student
        $this->student = User::factory()->create();
        $this->student->assignRole('student');
        // Legacy system check: add role 'siswa' as well if it is used in the model
        // Add both just to be completely safe
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'siswa']);
        $this->student->assignRole('siswa');

        // Create Course and Module
        $this->course = Course::create([
            'name' => 'Kecerdasan Buatan',
            'description' => 'Mata pelajaran kecerdasan buatan.',
            'code' => 'KB101',
        ]);

        $this->module = Module::create([
            'course_id' => $this->course->id,
            'title' => 'Bab 1: Pengenalan AI',
            'description' => 'Dasar kecerdasan buatan.',
        ]);

        // Create Video
        $this->video = Video::create([
            'module_id' => $this->module->id,
            'title' => 'Pengenalan Kecerdasan Buatan',
            'video_path' => 'materials/videos/sample.mp4',
            'duration' => 120,
            'description' => 'Deskripsi video awal.',
        ]);
    }

    public function test_teacher_can_view_video_management_page()
    {
        $response = $this->actingAs($this->teacher)
            ->get(route('videos.manage', $this->video));

        $response->assertStatus(200);
        $response->assertSee('Detail Video Pembelajaran');
        $response->assertSee('Rekap Jawaban Kuis Siswa');
    }

    public function test_teacher_can_update_video_details()
    {
        $response = $this->actingAs($this->teacher)
            ->put(route('videos.update', $this->video), [
                'title' => 'Pengenalan AI yang Diperbarui',
                'description' => 'Petunjuk menonton video AI dengan saksama.',
            ]);

        $response->assertRedirect();
        
        $this->video->refresh();
        $this->assertEquals('Pengenalan AI yang Diperbarui', $this->video->title);
        $this->assertEquals('Petunjuk menonton video AI dengan saksama.', $this->video->description);

        // Verify that LearningActivity has also been updated or created automatically via booted observer
        $this->assertDatabaseHas('learning_activities', [
            'video_id' => $this->video->id,
            'title' => 'Video Pembelajaran - Pengenalan AI yang Diperbarui',
            'description' => 'Petunjuk menonton video AI dengan saksama.',
        ]);
    }

    public function test_teacher_can_manage_video_interactive_quizzes()
    {
        // 1. Create a quiz question
        $response = $this->actingAs($this->teacher)
            ->post(route('videos.quizzes.store', $this->video), [
                'timestamp_seconds' => 30,
                'question' => 'Apakah AI adalah kecerdasan buatan?',
                'question_type' => 'true_false',
                'correct_answer' => 'Benar',
                'feedback' => 'Tepat sekali!',
            ]);

        $response->assertRedirect();

        $this->assertDatabaseHas('video_quizzes', [
            'video_id' => $this->video->id,
            'timestamp_seconds' => 30,
            'question' => 'Apakah AI adalah kecerdasan buatan?',
            'question_type' => 'true_false',
            'feedback' => 'Tepat sekali!',
        ]);

        $quiz = VideoQuiz::first();

        // True/False options are created automatically
        $this->assertDatabaseHas('video_quiz_options', [
            'video_quiz_id' => $quiz->id,
            'option_text' => 'Benar',
            'is_correct' => true,
        ]);
        $this->assertDatabaseHas('video_quiz_options', [
            'video_quiz_id' => $quiz->id,
            'option_text' => 'Salah',
            'is_correct' => false,
        ]);

        // 2. Update the quiz question to Multiple Choice
        $response = $this->actingAs($this->teacher)
            ->put(route('videos.quizzes.update', $quiz), [
                'timestamp_seconds' => 45,
                'question' => 'Kepanjangan dari AI adalah?',
                'question_type' => 'multiple_choice',
                'options' => 'Artificial Intelligence, Actual Integration, Auto Input, All Intel',
                'correct_answer' => 'Artificial Intelligence',
                'feedback' => 'Benar, Artificial Intelligence.',
            ]);

        $response->assertRedirect();

        $quiz->refresh();
        $this->assertEquals(45, $quiz->timestamp_seconds);
        $this->assertEquals('Kepanjangan dari AI adalah?', $quiz->question);
        $this->assertEquals('multiple_choice', $quiz->question_type);

        $this->assertDatabaseHas('video_quiz_options', [
            'video_quiz_id' => $quiz->id,
            'option_text' => 'Artificial Intelligence',
            'is_correct' => true,
        ]);

        // 3. Delete the quiz
        $response = $this->actingAs($this->teacher)
            ->delete(route('videos.quizzes.destroy', $quiz));

        $response->assertRedirect();
        $this->assertDatabaseMissing('video_quizzes', ['id' => $quiz->id]);
        $this->assertDatabaseMissing('video_quiz_options', ['video_quiz_id' => $quiz->id]);
    }

    public function test_student_can_submit_correct_answer_and_earn_xp()
    {
        // Setup a quiz question
        $quiz = VideoQuiz::create([
            'video_id' => $this->video->id,
            'timestamp_seconds' => 15,
            'question' => 'Model AI apa yang meniru cara kerja otak manusia?',
            'question_type' => 'short_answer',
            'feedback' => 'Ya, Jaringan Saraf Tiruan.',
        ]);

        VideoQuizOption::create([
            'video_quiz_id' => $quiz->id,
            'option_text' => 'Jaringan Saraf Tiruan',
            'is_correct' => true,
        ]);

        // Submit correct answer
        $response = $this->actingAs($this->student)
            ->postJson(route('student.videos.quiz_submit', $this->video), [
                'quiz_id' => $quiz->id,
                'answer' => 'Jaringan Saraf Tiruan',
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'is_correct' => true,
            'feedback' => 'Ya, Jaringan Saraf Tiruan.',
            'correct_answer' => 'Jaringan Saraf Tiruan',
        ]);

        // Assert attempt is recorded
        $this->assertDatabaseHas('video_quiz_attempts', [
            'user_id' => $this->student->id,
            'video_quiz_id' => $quiz->id,
            'answer' => 'Jaringan Saraf Tiruan',
            'is_correct' => true,
        ]);

        // Assert XP has been added (+15 XP for correct quiz answer)
        $this->assertEquals(15, $this->student->fresh()->points);
        $this->assertDatabaseHas('xp_logs', [
            'user_id' => $this->student->id,
            'activity_type' => 'video_quiz',
            'xp_earned' => 15,
        ]);
    }

    public function test_student_can_complete_video_and_earn_xp()
    {
        // Fetch the automatically created Learning Activity
        $learningActivity = LearningActivity::where('video_id', $this->video->id)->firstOrFail();

        // Submit video progress log as completed
        $response = $this->actingAs($this->student)
            ->postJson(route('student.videos.log', $this->video), [
                'watched_duration' => 120,
                'completed' => true,
            ]);

        $response->assertStatus(200);
        $response->assertJson([
            'status' => 'success',
            'completed' => true,
        ]);

        // Assert VideoActivityLog is updated
        $this->assertDatabaseHas('video_activity_logs', [
            'user_id' => $this->student->id,
            'video_id' => $this->video->id,
            'watched_duration' => 120,
            'completed' => true,
        ]);

        // Assert XP has been added (+20 XP for video complete)
        $this->assertEquals(20, $this->student->fresh()->points);
        $this->assertDatabaseHas('xp_logs', [
            'user_id' => $this->student->id,
            'activity_type' => 'video_watch',
            'xp_earned' => 20,
        ]);

        // Assert learning activity progress has been completed automatically
        $this->assertDatabaseHas('learning_activity_progress', [
            'user_id' => $this->student->id,
            'learning_activity_id' => $learningActivity->id,
            'is_completed' => true,
        ]);

        // Assert student progress percentage is recalculated
        $this->assertDatabaseHas('student_progress', [
            'user_id' => $this->student->id,
            'module_id' => $this->module->id,
            'progress_percentage' => 100,
        ]);
    }
}
