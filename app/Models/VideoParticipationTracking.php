<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VideoParticipationTracking extends Model
{
    protected $table = 'video_participation_tracking';

    protected $fillable = [
        'user_id',
        'material_id',
        'question_id',
        'selected_answer',
        'is_correct',
        'timestamp',
        'activity_log'
    ];

    protected $casts = [
        'is_correct' => 'boolean',
        'activity_log' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function question()
    {
        return $this->belongsTo(InteractiveVideoQuestion::class, 'question_id');
    }
}
