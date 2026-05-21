<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quiz extends Model
{
    protected $fillable = ['course_id', 'module_id', 'title', 'description', 'time_limit_minutes', 'deadline', 'show_results'];

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

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function questions()
    {
        return $this->hasMany(QuizQuestion::class);
    }

    public function attempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    protected static function booted()
    {
        static::saved(function ($quiz) {
            if ($quiz->module_id) {
                $activity = \App\Models\LearningActivity::where('module_id', $quiz->module_id)
                    ->where('quiz_id', $quiz->id)
                    ->first();

                if (!$activity) {
                    $maxOrder = \App\Models\LearningActivity::where('module_id', $quiz->module_id)->max('order_number') ?? 0;
                    \App\Models\LearningActivity::create([
                        'module_id' => $quiz->module_id,
                        'quiz_id' => $quiz->id,
                        'activity_type' => 'quiz',
                        'title' => 'Kuis: ' . $quiz->title,
                        'description' => $quiz->description,
                        'order_number' => $maxOrder + 1,
                        'is_required' => true,
                    ]);
                }
            }
        });

        static::deleted(function ($quiz) {
            \App\Models\LearningActivity::where('quiz_id', $quiz->id)->delete();
        });
    }
}
