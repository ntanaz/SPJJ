<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['course_id', 'title', 'description', 'time_limit_minutes', 'deadline', 'show_results'];

    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
            'show_results' => 'boolean',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }
}
