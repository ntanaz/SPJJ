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
}
