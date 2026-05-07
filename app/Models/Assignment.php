<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Assignment extends Model
{
    protected $fillable = ['course_id', 'title', 'description', 'deadline', 'attachment', 'is_published', 'max_score'];

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

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }
}
