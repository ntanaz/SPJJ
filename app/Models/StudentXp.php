<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentXp extends Model
{
    protected $table = 'student_xp';

    protected $fillable = ['user_id', 'total_xp'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
