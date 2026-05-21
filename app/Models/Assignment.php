<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = ['course_id', 'module_id', 'title', 'description', 'deadline', 'attachment', 'is_published', 'max_score'];

    // Cast the deadline to a carbon instance
    protected function casts(): array
    {
        return [
            'deadline' => 'datetime',
            'is_published' => 'boolean',
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

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    protected static function booted()
    {
        static::saved(function ($assignment) {
            if ($assignment->module_id) {
                $activity = \App\Models\LearningActivity::where('module_id', $assignment->module_id)
                    ->where('assignment_id', $assignment->id)
                    ->first();

                if (!$activity) {
                    $maxOrder = \App\Models\LearningActivity::where('module_id', $assignment->module_id)->max('order_number') ?? 0;
                    \App\Models\LearningActivity::create([
                        'module_id' => $assignment->module_id,
                        'assignment_id' => $assignment->id,
                        'activity_type' => 'assignment',
                        'title' => 'Tugas: ' . $assignment->title,
                        'description' => $assignment->description,
                        'order_number' => $maxOrder + 1,
                        'is_required' => true,
                    ]);
                }
            }
        });

        static::deleted(function ($assignment) {
            \App\Models\LearningActivity::where('assignment_id', $assignment->id)->delete();
        });
    }
}
