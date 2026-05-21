<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Video extends Model
{
    protected $fillable = ['module_id', 'title', 'video_path', 'duration', 'description'];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function quizzes()
    {
        return $this->hasMany(VideoQuiz::class)->orderBy('timestamp_seconds');
    }

    public function activityLogs()
    {
        return $this->hasMany(VideoActivityLog::class);
    }

    public function learningActivity()
    {
        return $this->hasOne(LearningActivity::class);
    }

    protected static function booted()
    {
        static::saved(function ($video) {
            if ($video->module_id) {
                $maxOrder = \App\Models\LearningActivity::where('module_id', $video->module_id)->max('order_number') ?? 0;
                
                \App\Models\LearningActivity::updateOrCreate(
                    ['module_id' => $video->module_id, 'video_id' => $video->id],
                    [
                        'activity_type' => 'video',
                        'title' => 'Video Pembelajaran - ' . $video->title,
                        'description' => $video->description ?: ('Tonton video pembelajaran interaktif: ' . $video->title),
                        'order_number' => \App\Models\LearningActivity::where('module_id', $video->module_id)->where('video_id', $video->id)->value('order_number') ?: ($maxOrder + 1),
                        'is_required' => true,
                    ]
                );
            }
        });

        static::deleted(function ($video) {
            \App\Models\LearningActivity::where('video_id', $video->id)->delete();
        });
    }
}
