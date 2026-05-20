<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoQuiz extends Model
{
    protected $fillable = ['video_id', 'timestamp_seconds', 'question', 'question_type', 'feedback'];

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function options()
    {
        return $this->hasMany(VideoQuizOption::class);
    }
}
