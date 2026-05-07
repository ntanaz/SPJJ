<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LearningResource extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'description',
        'file_path',
        'type',
        'category',
        'user_id',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
