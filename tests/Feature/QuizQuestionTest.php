<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizQuestion;
use App\Models\User;
use Database\Seeders\AdminRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizQuestionTest extends TestCase
{
    use RefreshDatabase;

    protected $teacher;
    protected $quiz;

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $this->seed(AdminRoleSeeder::class);

        // Create teacher
        $this->teacher = User::factory()->create();
        $this->teacher->assignRole('teacher');

        // Create Course and Module
        $course = Course::create([
            'name' => 'Test Course',
            'description' => 'Test Description',
            'code' => 'TEST101',
        ]);

        $module = Module::create([
            'course_id' => $course->id,
            'title' => 'Test Module',
            'description' => 'Test Module Desc',
        ]);

        // Create Quiz
        $this->quiz = Quiz::create([
            'course_id' => $course->id,
            'module_id' => $module->id,
            'title' => 'Test Quiz',
            'description' => 'Test Quiz Desc',
        ]);
    }

    public function test_multiple_choice_question_can_be_stored_and_updated()
    {
        // 1. Store
        $response = $this->actingAs($this->teacher)
            ->post(route('quizzes.questions.store', $this->quiz), [
                'question_type' => 'multiple_choice',
                'question' => 'What is 2+2?',
                'points' => 10,
                'feedback' => 'It is 4.',
                'option_a' => '3',
                'option_b' => '4',
                'option_c' => '5',
                'option_d' => '6',
                'correct_answer' => 'B',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('quiz_questions', [
            'quiz_id' => $this->quiz->id,
            'question_type' => 'multiple_choice',
            'question' => 'What is 2+2?',
            'correct_answer' => 'B',
            'points' => 10,
            'feedback' => 'It is 4.',
        ]);

        $question = QuizQuestion::first();
        $this->assertEquals(['A' => '3', 'B' => '4', 'C' => '5', 'D' => '6'], $question->options);

        // 2. Update
        $response = $this->actingAs($this->teacher)
            ->put(route('quizzes.questions.update', $question), [
                'question_type' => 'multiple_choice',
                'question' => 'What is 3+3?',
                'points' => 15,
                'feedback' => 'It is 6.',
                'option_a' => '5',
                'option_b' => '6',
                'option_c' => '7',
                'option_d' => '8',
                'correct_answer' => 'B',
            ]);

        $response->assertRedirect();
        $question->refresh();
        $this->assertEquals('What is 3+3?', $question->question);
        $this->assertEquals(15, $question->points);
        $this->assertEquals('It is 6.', $question->feedback);
        $this->assertEquals('B', $question->correct_answer);
        $this->assertEquals(['A' => '5', 'B' => '6', 'C' => '7', 'D' => '8'], $question->options);
    }

    public function test_true_false_question_can_be_stored_and_updated()
    {
        // 1. Store
        $response = $this->actingAs($this->teacher)
            ->post(route('quizzes.questions.store', $this->quiz), [
                'question_type' => 'true_false',
                'question' => 'Earth is flat.',
                'points' => 10,
                'feedback' => 'It is round.',
                'correct_answer' => 'B',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('quiz_questions', [
            'quiz_id' => $this->quiz->id,
            'question_type' => 'true_false',
            'question' => 'Earth is flat.',
            'correct_answer' => 'B',
        ]);

        $question = QuizQuestion::first();
        $this->assertEquals(['A' => 'Benar', 'B' => 'Salah'], $question->options);

        // 2. Update
        $response = $this->actingAs($this->teacher)
            ->put(route('quizzes.questions.update', $question), [
                'question_type' => 'true_false',
                'question' => 'Earth is round.',
                'points' => 20,
                'feedback' => 'Correct.',
                'correct_answer' => 'A',
            ]);

        $response->assertRedirect();
        $question->refresh();
        $this->assertEquals('Earth is round.', $question->question);
        $this->assertEquals('A', $question->correct_answer);
        $this->assertEquals(20, $question->points);
    }

    public function test_short_answer_question_can_be_stored_and_updated()
    {
        // 1. Store
        $response = $this->actingAs($this->teacher)
            ->post(route('quizzes.questions.store', $this->quiz), [
                'question_type' => 'short_answer',
                'question' => 'Capital of France?',
                'points' => 10,
                'feedback' => 'Paris.',
                'correct_answer' => 'Paris',
                'keywords' => 'Paris, capital, France',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('quiz_questions', [
            'quiz_id' => $this->quiz->id,
            'question_type' => 'short_answer',
            'question' => 'Capital of France?',
            'correct_answer' => 'Paris',
        ]);

        $question = QuizQuestion::first();
        $this->assertEquals('Paris, capital, France', $question->options['keywords'] ?? null);

        // 2. Update
        $response = $this->actingAs($this->teacher)
            ->put(route('quizzes.questions.update', $question), [
                'question_type' => 'short_answer',
                'question' => 'Capital of Germany?',
                'points' => 10,
                'feedback' => 'Berlin.',
                'correct_answer' => 'Berlin',
                'keywords' => 'Berlin, capital, Germany',
            ]);

        $response->assertRedirect();
        $question->refresh();
        $this->assertEquals('Capital of Germany?', $question->question);
        $this->assertEquals('Berlin', $question->correct_answer);
        $this->assertEquals('Berlin, capital, Germany', $question->options['keywords'] ?? null);
    }

    public function test_fill_blank_question_can_be_stored_and_updated()
    {
        // 1. Store
        $response = $this->actingAs($this->teacher)
            ->post(route('quizzes.questions.store', $this->quiz), [
                'question_type' => 'fill_blank',
                'question' => '___ is a programming language.',
                'points' => 10,
                'feedback' => 'PHP.',
                'correct_answer' => 'PHP',
                'code_template' => 'echo [blank];',
                'blank_placeholder' => '[blank]',
                'feedback_correct' => 'Correct blank!',
                'feedback_incorrect' => 'Incorrect blank!',
                'max_attempts' => 5,
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('quiz_questions', [
            'quiz_id' => $this->quiz->id,
            'question_type' => 'fill_blank',
            'question' => '___ is a programming language.',
            'correct_answer' => 'PHP',
        ]);

        $question = QuizQuestion::first();
        $this->assertEquals('echo [blank];', $question->options['code_template'] ?? null);
        $this->assertEquals('[blank]', $question->options['blank_placeholder'] ?? null);
        $this->assertEquals('Correct blank!', $question->options['feedback_correct'] ?? null);
        $this->assertEquals('Incorrect blank!', $question->options['feedback_incorrect'] ?? null);
        $this->assertEquals(5, $question->options['max_attempts'] ?? null);

        // 2. Update
        $response = $this->actingAs($this->teacher)
            ->put(route('quizzes.questions.update', $question), [
                'question_type' => 'fill_blank',
                'question' => '___ is a web framework.',
                'points' => 10,
                'feedback' => 'Laravel.',
                'correct_answer' => 'Laravel',
                'code_template' => 'echo Laravel [blank];',
                'blank_placeholder' => '[blank]',
                'feedback_correct' => 'Correct PHP!',
                'feedback_incorrect' => 'Incorrect PHP!',
                'max_attempts' => 3,
            ]);

        $response->assertRedirect();
        $question->refresh();
        $this->assertEquals('___ is a web framework.', $question->question);
        $this->assertEquals('Laravel', $question->correct_answer);
        $this->assertEquals('echo Laravel [blank];', $question->options['code_template'] ?? null);
        $this->assertEquals('[blank]', $question->options['blank_placeholder'] ?? null);
        $this->assertEquals('Correct PHP!', $question->options['feedback_correct'] ?? null);
        $this->assertEquals('Incorrect PHP!', $question->options['feedback_incorrect'] ?? null);
        $this->assertEquals(3, $question->options['max_attempts'] ?? null);
    }

    public function test_reflection_question_can_be_stored_and_updated()
    {
        // 1. Store
        $response = $this->actingAs($this->teacher)
            ->post(route('quizzes.questions.store', $this->quiz), [
                'question_type' => 'reflection',
                'question' => 'What did you learn today?',
                'points' => 5,
                'feedback' => 'Reflect well.',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('quiz_questions', [
            'quiz_id' => $this->quiz->id,
            'question_type' => 'reflection',
            'question' => 'What did you learn today?',
        ]);

        $question = QuizQuestion::first();

        // 2. Update
        $response = $this->actingAs($this->teacher)
            ->put(route('quizzes.questions.update', $question), [
                'question_type' => 'reflection',
                'question' => 'What was the hardest part?',
                'points' => 5,
                'feedback' => 'Identify difficulties.',
            ]);

        $response->assertRedirect();
        $question->refresh();
        $this->assertEquals('What was the hardest part?', $question->question);
    }

    public function test_debugging_question_can_be_stored_and_updated()
    {
        // 1. Store
        $response = $this->actingAs($this->teacher)
            ->post(route('quizzes.questions.store', $this->quiz), [
                'question_type' => 'debugging',
                'question' => 'Fix the syntax error in echo "hello"',
                'points' => 15,
                'feedback' => 'Missing semicolon.',
                'correct_answer' => 'echo "hello";',
                'code_snippet' => 'echo "hello"',
                'bug_description' => 'Missing semicolon at the end of echo statement.',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('quiz_questions', [
            'quiz_id' => $this->quiz->id,
            'question_type' => 'debugging',
            'question' => 'Fix the syntax error in echo "hello"',
            'correct_answer' => 'echo "hello";',
        ]);

        $question = QuizQuestion::first();
        $this->assertEquals('echo "hello"', $question->options['code_snippet'] ?? null);
        $this->assertEquals('Missing semicolon at the end of echo statement.', $question->options['bug_description'] ?? null);

        // 2. Update
        $response = $this->actingAs($this->teacher)
            ->put(route('quizzes.questions.update', $question), [
                'question_type' => 'debugging',
                'question' => 'Fix print("hello")',
                'points' => 15,
                'feedback' => 'No error actually.',
                'correct_answer' => 'print("hello");',
                'code_snippet' => 'print("hello")',
                'bug_description' => 'Syntax error print.',
            ]);

        $response->assertRedirect();
        $question->refresh();
        $this->assertEquals('Fix print("hello")', $question->question);
        $this->assertEquals('print("hello");', $question->correct_answer);
        $this->assertEquals('print("hello")', $question->options['code_snippet'] ?? null);
        $this->assertEquals('Syntax error print.', $question->options['bug_description'] ?? null);
    }

    public function test_interactive_video_question_can_be_stored_and_updated()
    {
        // 1. Store
        $response = $this->actingAs($this->teacher)
            ->post(route('quizzes.questions.store', $this->quiz), [
                'question_type' => 'interactive_video',
                'question' => 'What is explained in the video?',
                'points' => 10,
                'feedback' => 'Watch carefully.',
                'video_url' => 'https://example.com/video.mp4',
                'timestamp' => 30,
                'video_question_type' => 'multiple_choice',
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                'correct_answer' => 'A',
            ]);

        $response->assertRedirect();
        $this->assertDatabaseHas('quiz_questions', [
            'quiz_id' => $this->quiz->id,
            'question_type' => 'interactive_video',
            'question' => 'What is explained in the video?',
            'correct_answer' => 'A',
        ]);

        $question = QuizQuestion::first();
        $this->assertArrayHasKey('video_url', $question->options);
        $this->assertEquals('https://example.com/video.mp4', $question->options['video_url']);
        $this->assertEquals(30, $question->options['timestamp']);
        $this->assertEquals('multiple_choice', $question->options['video_question_type']);
        $this->assertEquals(['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D'], $question->options['options']);

        // 2. Update
        $response = $this->actingAs($this->teacher)
            ->put(route('quizzes.questions.update', $question), [
                'question_type' => 'interactive_video',
                'question' => 'What is explained now?',
                'points' => 10,
                'feedback' => 'Watch again.',
                'video_url' => 'https://example.com/video2.mp4',
                'timestamp' => 45,
                'video_question_type' => 'true_false',
                'correct_answer' => 'B',
            ]);

        $response->assertRedirect();
        $question->refresh();
        $this->assertEquals('What is explained now?', $question->question);
        $this->assertEquals('B', $question->correct_answer);
        $this->assertEquals('https://example.com/video2.mp4', $question->options['video_url']);
        $this->assertEquals(45, $question->options['timestamp']);
        $this->assertEquals('true_false', $question->options['video_question_type']);
        $this->assertEquals(['A' => 'Benar', 'B' => 'Salah'], $question->options['options']);
    }

    public function test_update_question_validation_error_redirects_with_session_and_input()
    {
        // 1. Create a question
        $question = QuizQuestion::create([
            'quiz_id' => $this->quiz->id,
            'question_type' => 'multiple_choice',
            'question' => 'Original Question',
            'points' => 10,
            'correct_answer' => 'A',
            'options' => ['A' => 'A', 'B' => 'B', 'C' => 'C', 'D' => 'D'],
        ]);

        // 2. Update with invalid data (e.g. missing correct_answer for multiple_choice)
        $response = $this->actingAs($this->teacher)
            ->put(route('quizzes.questions.update', $question), [
                'question_type' => 'multiple_choice',
                'question' => 'Updated but invalid',
                'points' => 10,
                'option_a' => 'A',
                'option_b' => 'B',
                'option_c' => 'C',
                'option_d' => 'D',
                // correct_answer is omitted
            ]);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['correct_answer']);
        $response->assertSessionHas('error_edit_question_id', $question->id);
    }
}
