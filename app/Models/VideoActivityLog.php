<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoActivityLog extends Model
{
    protected $fillable = ['user_id', 'video_id', 'watched_duration', 'completed', 'answered_quiz'];

    protected $casts = [
        'completed' => 'boolean',
        'answered_quiz' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }
}
