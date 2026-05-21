<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoQuizAttempt extends Model
{
    protected $table = 'video_quiz_attempts';

    protected $fillable = ['user_id', 'video_quiz_id', 'answer', 'is_correct'];

    protected $casts = [
        'is_correct' => 'boolean',
    ];

    /**
     * Get the student who made this attempt.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Get the video quiz this attempt is associated with.
     */
    public function quiz()
    {
        return $this->belongsTo(VideoQuiz::class, 'video_quiz_id');
    }
}
