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
                
                if ($material->file_path || $material->text_content) {
                    $steps['material'] = 'Membaca Modul - ' . $material->title;
                } else {
                    \App\Models\LearningActivity::where('material_id', $material->id)->where('activity_type', 'material')->delete();
                }
                
                if ($material->youtube_url) {
                    $steps['video'] = 'Video Pembelajaran - ' . $material->title;
                } else {
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
                    $maxOrder = \App\Models\LearningActivity::where('module_id', $material->module_id)->max('order_number') ?? 0;
                    
                    \App\Models\LearningActivity::updateOrCreate(
                        ['module_id' => $material->module_id, 'material_id' => $material->id, 'activity_type' => $type],
                        [
                            'title' => $title,
                            'description' => 'Aktivitas untuk ' . $material->title,
                            'order_number' => \App\Models\LearningActivity::where('module_id', $material->module_id)->where('material_id', $material->id)->where('activity_type', $type)->value('order_number') ?: ($maxOrder + 1),
                            'is_required' => true,
                        ]
                    );
                }
            }
        });

        static::deleted(function ($material) {
            \App\Models\LearningActivity::where('material_id', $material->id)->delete();
        });
    }
}
