<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InteractiveVideoQuestion extends Model
{
    // Supported question types
    const TYPE_MULTIPLE_CHOICE = 'multiple_choice';
    const TYPE_TRUE_FALSE = 'true_false';
    const TYPE_SHORT_ANSWER = 'short_answer';

    protected $fillable = ['material_id', 'timestamp', 'question', 'options', 'correct_answer', 'question_type'];

    protected $casts = [
        'options' => 'array',
    ];

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function trackings()
    {
        return $this->hasMany(VideoParticipationTracking::class, 'question_id');
    }
}
