<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['course_id', 'module_id', 'title', 'description', 'type', 'file_path', 'order', 'is_locked', 'format', 'youtube_url', 'text_content', 'publish_at', 'is_published', 'requires_previous', 'mind_map_path'];

    protected function casts(): array
    {
        return [
            'publish_at' => 'datetime',
            'is_published' => 'boolean',
            'requires_previous' => 'boolean',
            'is_locked' => 'boolean',
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

    public function progress()
    {
        return $this->hasMany(MaterialProgress::class);
    }

    public function interactiveVideoQuestions()
    {
        return $this->hasMany(InteractiveVideoQuestion::class)->orderBy('timestamp');
    }

    public function codingQuiz()
    {
        return $this->hasOne(CodingQuiz::class);
    }

    public function stepProgress()
    {
        return $this->hasMany(MaterialStepProgress::class);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    protected static function booted()
    {
        static::saved(function ($material) {
            if ($material->module_id) {
                $steps = [];
                if ($material->mind_map_path) {
                    $steps['mind_map'] = 'Peta Pikiran - ' . $material->title;
                } else {
                    \App\Models\LearningActivity::where('material_id', $material->id)->where('activity_type', 'mind_map')->delete();
                }
                
                if (($material->file_path && !in_array($material->format ?? $material->type, ['video', 'video_post_class'])) || $material->text_content) {
                    $steps['material'] = 'Membaca Modul - ' . $material->title;
                } else {
                    \App\Models\LearningActivity::where('material_id', $material->id)->where('activity_type', 'material')->delete();
                }
                
                $hasVideo = $material->youtube_url || ($material->file_path && in_array($material->format ?? $material->type, ['video', 'video_post_class']));
                if ($hasVideo) {
                    $steps['video'] = 'Video Pembelajaran - ' . $material->title;
                } else {
                    $videoAct = \App\Models\LearningActivity::where('material_id', $material->id)->where('activity_type', 'video')->first();
                    if ($videoAct && $videoAct->video_id) {
                        \App\Models\Video::where('id', $videoAct->video_id)->delete();
                    }
                    \App\Models\LearningActivity::where('material_id', $material->id)->where('activity_type', 'video')->delete();
                }
                
                $hasCoding = \App\Models\CodingQuiz::where('material_id', $material->id)->exists();
                if ($hasCoding) {
                    $steps['coding_quiz'] = 'Kuis Koding - ' . $material->title;
                } else {
                    \App\Models\LearningActivity::where('material_id', $material->id)->where('activity_type', 'coding_quiz')->delete();
                }
                
                $steps['reflection'] = 'Refleksi Mandiri - ' . $material->title;
                
                foreach ($steps as $type => $title) {
                    $activity = \App\Models\LearningActivity::where('module_id', $material->module_id)
                        ->where('material_id', $material->id)
                        ->where('activity_type', $type)
                        ->first();

                    if (!$activity) {
                        $maxOrder = \App\Models\LearningActivity::where('module_id', $material->module_id)->max('order_number') ?? 0;
                        $activity = \App\Models\LearningActivity::create([
                            'module_id' => $material->module_id,
                            'material_id' => $material->id,
                            'activity_type' => $type,
                            'title' => $title,
                            'description' => 'Aktivitas untuk ' . $material->title,
                            'order_number' => $maxOrder + 1,
                            'is_required' => true,
                        ]);
                    }

                    if ($type === 'video') {
                        $video = null;
                        if ($activity->video_id) {
                            $video = \App\Models\Video::find($activity->video_id);
                        }
                        $videoPath = $material->file_path ?? $material->youtube_url ?? '';
                        if (!$video) {
                            $video = \App\Models\Video::create([
                                'module_id' => $material->module_id,
                                'title' => $material->title,
                                'video_path' => $videoPath,
                                'duration' => 0,
                            ]);
                            $activity->update(['video_id' => $video->id]);
                        } else {
                            $video->update([
                                'title' => $material->title,
                                'video_path' => $videoPath,
                            ]);
                        }
                    }
                }
            }
        });

        static::deleted(function ($material) {
            \App\Models\LearningActivity::where('material_id', $material->id)->delete();
        });
    }
}
