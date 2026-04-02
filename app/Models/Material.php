<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    protected $fillable = ['course_id', 'title', 'description', 'type', 'file_path', 'order', 'is_locked'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }
}
