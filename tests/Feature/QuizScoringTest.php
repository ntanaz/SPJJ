<?php

namespace Tests\Feature;

use App\Models\Course;
use App\Models\Module;
use App\Models\Quiz;
use App\Models\QuizAttempt;
use App\Models\QuizQuestion;
use App\Models\User;
use Database\Seeders\AdminRoleSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class QuizScoringTest extends TestCase
{
    use RefreshDatabase;

    protected $student;
    protected $quiz;
    protected $questions = [];

    protected function setUp(): void
    {
        parent::setUp();

        // Seed roles
        $this->seed(AdminRoleSeeder::class);

        // Create student
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'student']);
        \Spatie\Permission\Models\Role::firstOrCreate(['name' => 'siswa']);
        
        $this->student = User::factory()->create();
        $this->student->assignRole('student');
        $this->student->assignRole('siswa');

        // Create Course and Module
        $course = Course::create([
            'name' => 'Math Course',
            'description' => 'Math Description',
            'code' => 'MATH101',
        ]);

        $module = Module::create([
            'course_id' => $course->id,
            'title' => 'Math Module',
            'description' => 'Math Module Desc',
        ]);

        // Create Quiz
        $this->quiz = Quiz::create([
            'course_id' => $course->id,
            'module_id' => $module->id,
            'title' => 'Weighted Math Quiz',
            'description' => 'Math Quiz Desc',
        ]);

        // Create 5 questions with different types and custom point weights
        // Total points: 20 + 20 + 10 + 30 + 20 = 100

        // 1. Multiple Choice (20 points)
        $this->questions[1] = QuizQuestion::create([
            'quiz_id' => $this->quiz->id,
            'question_type' => 'multiple_choice',
            'question' => 'What is 5 + 5?',
            'points' => 20,
            'correct_answer' => 'A',
            'options' => ['A' => '10', 'B' => '11', 'C' => '12', 'D' => '13', 'E' => '14'],
        ]);

        // 2. True/False (20 points)
        $this->questions[2] = QuizQuestion::create([
            'quiz_id' => $this->quiz->id,
            'question_type' => 'true_false',
            'question' => 'Water boils at 100 degrees Celsius.',
            'points' => 20,
            'correct_answer' => 'A',
            'options' => ['A' => 'Benar', 'B' => 'Salah'],
        ]);

        // 3. Short Answer with keywords (10 points)
        $this->questions[3] = QuizQuestion::create([
            'quiz_id' => $this->quiz->id,
            'question_type' => 'short_answer',
            'question' => 'Who wrote Hamlet?',
            'points' => 10,
            'correct_answer' => 'William Shakespeare',
            'options' => ['keywords' => 'Shakespeare, Hamlet, William'],
        ]);

        // 4. Debugging (30 points)
        $this->questions[4] = QuizQuestion::create([
            'quiz_id' => $this->quiz->id,
            'question_type' => 'debugging',
            'question' => 'Fix this code',
            'points' => 30,
            'correct_answer' => 'return $x + 1;',
            'options' => ['code_snippet' => 'return $x + 1', 'bug_description' => 'Missing semicolon'],
        ]);

        // 5. Reflection (20 points)
        $this->questions[5] = QuizQuestion::create([
            'quiz_id' => $this->quiz->id,
            'question_type' => 'reflection',
            'question' => 'What did you think of the test?',
            'points' => 20,
            'correct_answer' => '',
            'options' => [],
        ]);
    }

    public function test_perfect_score_calculation()
    {
        // Student starts attempt
        $attempt = QuizAttempt::create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->student->id,
            'score' => 0,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // Student answers everything perfectly
        $answers = [
            $this->questions[1]->id => 'A', // MC correct
            $this->questions[2]->id => 'A', // TF correct
            $this->questions[3]->id => 'Shakespeare', // Keyword match correct
            $this->questions[4]->id => 'return    $x   +   1;', // Whitespace debugging correct
            $this->questions[5]->id => 'It was very thought provoking.', // Reflection non-empty correct
        ];

        foreach ($this->quiz->questions as $question) {
            $val = $answers[$question->id];
            $attempt->answers()->create([
                'quiz_question_id' => $question->id,
                'selected_option' => in_array($question->question_type, ['multiple_choice', 'true_false']) ? $val : null,
                'text_answer' => !in_array($question->question_type, ['multiple_choice', 'true_false']) ? $val : null,
                'is_correct' => $question->isAnswerCorrect($val),
            ]);
        }

        $finalScore = $attempt->calculateScore();

        // 100/100
        $this->assertEquals(100, $finalScore);
    }

    public function test_partial_score_calculation_with_incorrect_answers()
    {
        // Student starts attempt
        $attempt = QuizAttempt::create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->student->id,
            'score' => 0,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // Student answers some correctly and some incorrectly
        // Question 1 (20pt): Correct answer is A. Student gives B (Incorrect) -> 0 pts
        // Question 2 (20pt): Correct answer is A. Student gives A (Correct) -> 20 pts
        // Question 3 (10pt): Correct answer is William Shakespeare. Student gives "William" (Correct via keyword) -> 10 pts
        // Question 4 (30pt): Correct answer is "return $x + 1;". Student gives "return $x + 1" (Incorrect - missing semicolon) -> 0 pts
        // Question 5 (20pt): Reflection. Student gives "Good" (Correct - non-empty) -> 20 pts
        // Expected Points Earned: 0 + 20 + 10 + 0 + 20 = 50 pts
        // Expected Score: (50 / 100) * 100 = 50

        $answers = [
            $this->questions[1]->id => 'B',
            $this->questions[2]->id => 'A',
            $this->questions[3]->id => 'William',
            $this->questions[4]->id => 'return $x + 1',
            $this->questions[5]->id => 'Good',
        ];

        foreach ($this->quiz->questions as $question) {
            $val = $answers[$question->id];
            $attempt->answers()->create([
                'quiz_question_id' => $question->id,
                'selected_option' => in_array($question->question_type, ['multiple_choice', 'true_false']) ? $val : null,
                'text_answer' => !in_array($question->question_type, ['multiple_choice', 'true_false']) ? $val : null,
                'is_correct' => $question->isAnswerCorrect($val),
            ]);
        }

        $finalScore = $attempt->calculateScore();

        $this->assertEquals(50, $finalScore);
    }

    public function test_zero_score_calculation()
    {
        // Student starts attempt
        $attempt = QuizAttempt::create([
            'quiz_id' => $this->quiz->id,
            'user_id' => $this->student->id,
            'score' => 0,
            'status' => 'in_progress',
            'started_at' => now(),
        ]);

        // Student answers everything wrong
        $answers = [
            $this->questions[1]->id => 'D', // Wrong
            $this->questions[2]->id => 'B', // Wrong
            $this->questions[3]->id => 'Albert Einstein', // Wrong
            $this->questions[4]->id => 'echo "hello";', // Wrong
            $this->questions[5]->id => '', // Empty reflection -> 0 points
        ];

        foreach ($this->quiz->questions as $question) {
            $val = $answers[$question->id];
            $attempt->answers()->create([
                'quiz_question_id' => $question->id,
                'selected_option' => in_array($question->question_type, ['multiple_choice', 'true_false']) ? $val : null,
                'text_answer' => !in_array($question->question_type, ['multiple_choice', 'true_false']) ? $val : null,
                'is_correct' => $question->isAnswerCorrect($val),
            ]);
        }

        $finalScore = $attempt->calculateScore();

        $this->assertEquals(0, $finalScore);
    }
}
