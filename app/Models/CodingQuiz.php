<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodingQuiz extends Model
{
    // Supported quiz types
    const TYPE_FILL_BLANK = 'fill_blank';
    const TYPE_DEBUGGING  = 'debugging';
    const TYPE_SHORT_ANSWER = 'short_answer';

    protected $fillable = [
        'material_id',
        'quiz_type',
        'title',
        'instruction',
        'code_template',
        'correct_answers',
        'feedback_correct',
        'feedback_incorrect'
    ];

    protected $casts = [
        'correct_answers' => 'array',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function attempts()
    {
        return $this->hasMany(CodingQuizAttempt::class);
    }

    protected static function booted()
    {
        static::saved(function ($codingQuiz) {
            if ($codingQuiz->material) {
                $codingQuiz->material->touch();
            }
        });

        static::deleted(function ($codingQuiz) {
            if ($codingQuiz->material) {
                $codingQuiz->material->touch();
            }
        });
    }
}
