<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CodingQuizAttempt extends Model
{
    protected $fillable = [
        'user_id',
        'coding_quiz_id',
        'percobaan_ke',
        'jawaban',
        'hasil_validasi',
        'feedback',
        'waktu_submit',
        'reflection',
        'correctness_grade',
        'reflection_grade',
        'final_grade',
        'graded_at'
    ];

    protected $casts = [
        'jawaban' => 'array',
        'hasil_validasi' => 'boolean',
        'waktu_submit' => 'datetime',
        'graded_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function codingQuiz()
    {
        return $this->belongsTo(CodingQuiz::class);
    }
}
