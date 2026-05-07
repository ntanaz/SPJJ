<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    protected $fillable = ['course_id', 'title', 'date', 'start_time', 'end_time', 'pre_test_quiz_id'];

    public function preTestQuiz()
    {
        return $this->belongsTo(Quiz::class, 'pre_test_quiz_id');
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function records()
    {
        return $this->hasMany(AttendanceRecord::class);
    }

    public function isCurrentlyOpen()
    {
        $now = now();
        $today = $now->format('Y-m-d');
        $currentTime = $now->format('H:i:s');

        return $this->date === $today && 
               $this->start_time <= $currentTime && 
               $this->end_time >= $currentTime;
    }
}
