<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submission extends Model
{
    protected $fillable = ['assignment_id', 'user_id', 'file_path', 'text_content', 'status', 'is_late', 'attachments', 'grade', 'feedback', 'graded_at'];

    protected function casts(): array
    {
        return [
            'graded_at' => 'datetime',
            'attachments' => 'array',
            'is_late' => 'boolean',
        ];
    }

    public function assignment()
    {
        return $this->belongsTo(Assignment::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
