<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuizQuestion extends Model
{
    // Question Types
    const TYPE_MC = 'multiple_choice';
    const TYPE_TF = 'true_false';
    const TYPE_SHORT = 'short_answer';
    const TYPE_BLANK = 'fill_blank';
    const TYPE_REFLECTION = 'reflection';
    const TYPE_DEBUGGING = 'debugging';
    const TYPE_VIDEO = 'interactive_video';

    protected $fillable = ['quiz_id', 'question_type', 'question', 'options', 'correct_answer', 'feedback', 'points'];

    protected $casts = [
        'options' => 'array',
        'points' => 'integer',
    ];

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    /**
     * Determine if a student's answer for this question is correct.
     * Centralized source of truth for answer evaluation.
     */
    public function isAnswerCorrect($answer): bool
    {
        if ($answer === null || $answer === '') {
            return false;
        }

        $type = $this->question_type;
        $correct = $this->correct_answer;

        if ($type === 'multiple_choice' || $type === 'true_false') {
            return strtolower(trim($answer)) === strtolower(trim($correct));
        } elseif ($type === 'short_answer') {
            $isCorrect = strtolower(trim($answer)) === strtolower(trim($correct));
            if (!$isCorrect && !empty($this->options['keywords'])) {
                $keywords = array_map('trim', explode(',', $this->options['keywords']));
                foreach ($keywords as $keyword) {
                    if ($keyword !== '' && stripos($answer, $keyword) !== false) {
                        return true;
                    }
                }
            }
            return $isCorrect;
        } elseif ($type === 'fill_blank') {
            return strtolower(trim($answer)) === strtolower(trim($correct));
        } elseif ($type === 'reflection') {
            return !empty(trim($answer));
        } elseif ($type === 'debugging') {
            $cleanInput = preg_replace('/\s+/', '', $answer);
            $cleanCorrect = preg_replace('/\s+/', '', $correct);
            return strtolower($cleanInput) === strtolower($cleanCorrect);
        } elseif ($type === 'interactive_video') {
            $videoQType = $this->options['video_question_type'] ?? 'multiple_choice';
            if ($videoQType === 'multiple_choice' || $videoQType === 'true_false') {
                return strtolower(trim($answer)) === strtolower(trim($correct));
            } else {
                return strtolower(trim($answer)) === strtolower(trim($correct));
            }
        }

        return false;
    }
}

