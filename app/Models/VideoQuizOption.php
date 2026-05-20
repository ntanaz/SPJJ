<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoQuizOption extends Model
{
    protected $fillable = ['video_quiz_id', 'option_text', 'is_correct'];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    public function quiz()
    {
        return $this->belongsTo(VideoQuiz::class, 'video_quiz_id');
    }
}
