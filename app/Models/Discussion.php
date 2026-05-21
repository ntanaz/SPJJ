<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Discussion extends Model
{
    protected $fillable = ['course_id', 'module_id', 'material_id', 'user_id', 'title', 'content', 'is_pinned', 'is_locked'];

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

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function replies()
    {
        return $this->hasMany(DiscussionReply::class);
    }

    protected static function booted()
    {
        static::saved(function ($discussion) {
            if ($discussion->module_id) {
                $activity = \App\Models\LearningActivity::where('module_id', $discussion->module_id)
                    ->where('discussion_id', $discussion->id)
                    ->first();

                if (!$activity) {
                    $maxOrder = \App\Models\LearningActivity::where('module_id', $discussion->module_id)->max('order_number') ?? 0;
                    \App\Models\LearningActivity::create([
                        'module_id' => $discussion->module_id,
                        'discussion_id' => $discussion->id,
                        'activity_type' => 'discussion',
                        'title' => 'Forum Diskusi: ' . $discussion->title,
                        'description' => $discussion->content,
                        'order_number' => $maxOrder + 1,
                        'is_required' => true,
                    ]);
                }
            }
        });

        static::deleted(function ($discussion) {
            \App\Models\LearningActivity::where('discussion_id', $discussion->id)->delete();
        });
    }
}
