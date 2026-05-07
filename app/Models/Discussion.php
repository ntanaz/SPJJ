<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    protected $fillable = ['course_id', 'user_id', 'title', 'content', 'is_pinned', 'is_locked'];

    protected function casts(): array
    {
        return [
            'is_pinned' => 'boolean',
            'is_locked' => 'boolean',
        ];
    }

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(DiscussionReply::class);
    }
}
