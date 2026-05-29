<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizAttempt extends Model
{
    protected $fillable = ['quiz_id', 'user_id', 'score', 'status', 'started_at'];

    protected function casts(): array
    {
        return [
            'started_at' => 'datetime',
        ];
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function answers()
    {
        return $this->hasMany(QuizAnswer::class);
    }

    /**
     * Compute attempt score based on correct answers and question points.
     * Centralized source of truth for grading attempts.
     */
    public function calculateScore(): int
    {
        $quiz = $this->quiz()->with('questions')->first();
        if (!$quiz) {
            return 0;
        }

        $totalPossibleScore = $quiz->questions->sum('points');
        if ($totalPossibleScore <= 0) {
            return 0;
        }

        $totalScore = 0;
        $this->loadMissing('answers.question');

        foreach ($this->answers as $answer) {
            if ($answer->is_correct) {
                $points = $answer->question ? $answer->question->points : 0;
                $totalScore += $points;
            }
        }

        return round(($totalScore / $totalPossibleScore) * 100);
    }
}

