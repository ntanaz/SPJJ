<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

class LearningActivity extends Model
{
    protected $fillable = [
        'module_id',
        'activity_type',
        'title',
        'description',
        'order_number',
        'is_required',
        'material_id',
        'quiz_id',
        'assignment_id',
        'discussion_id',
        'video_id'
    ];

    protected $casts = [
        'is_required' => 'boolean',
    ];

    public function module()
    {
        return $this->belongsTo(Module::class);
    }

    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    public function quiz()
    {
        return $this->belongsTo(Quiz::class);
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function discussion()
    {
        return $this->belongsTo(Discussion::class);
    }

    public function video()
    {
        return $this->belongsTo(Video::class);
    }

    public function progress()
    {
        return $this->hasMany(LearningActivityProgress::class);
    }

    /**
     * Check if this activity is unlocked for a user.
     */
    public function isUnlockedFor(User $user): bool
    {
        // Teacher/admin bypass
        if ($user->hasRole(['guru', 'teacher', 'admin'])) {
            return true;
        }

        // Find the activity in the same module with the highest order_number less than this activity's order_number
        $previousActivity = self::where('module_id', $this->module_id)
            ->where('order_number', '<', $this->order_number)
            ->orderBy('order_number', 'desc')
            ->first();

        if (!$previousActivity) {
            return true;
        }

        // Check if the previous activity is completed
        return DB::table('learning_activity_progress')
            ->where('user_id', $user->id)
            ->where('learning_activity_id', $previousActivity->id)
            ->where('is_completed', true)
            ->exists();
    }
}
