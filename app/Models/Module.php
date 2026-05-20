<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Module extends Model
{
    protected $fillable = ['course_id', 'title', 'description', 'order_number'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function materials()
    {
        return $this->hasMany(Material::class)->orderBy('order');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class)->orderBy('deadline');
    }

    public function videos()
    {
        return $this->hasMany(Video::class);
    }

    public function activities()
    {
        return $this->hasMany(LearningActivity::class)->orderBy('order_number');
    }
}
