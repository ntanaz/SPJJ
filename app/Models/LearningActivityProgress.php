<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LearningActivityProgress extends Model
{
    protected $table = 'learning_activity_progress';

    protected $fillable = [
        'user_id',
        'learning_activity_id',
        'is_completed',
        'completed_at'
    ];

    protected $casts = [
        'is_completed' => 'boolean',
        'completed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function activity()
    {
        return $this->belongsTo(LearningActivity::class, 'learning_activity_id');
    }
}
