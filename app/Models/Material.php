<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['course_id', 'title', 'description', 'type', 'file_path', 'order', 'is_locked', 'format', 'youtube_url', 'text_content', 'publish_at', 'is_published', 'requires_previous'];

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

    public function progress()
    {
        return $this->hasMany(MaterialProgress::class);
    }
}
