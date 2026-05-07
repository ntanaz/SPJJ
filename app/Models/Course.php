<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['name', 'description', 'cover_image', 'banner_image', 'code', 'is_leaderboard_enabled'];

    public function materials()
    {
        return $this->hasMany(Material::class);
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class);
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }

    public function attendances()
    {
        return $this->hasMany(Attendance::class);
    }

    public function classes()
    {
        return $this->hasMany(CourseClass::class);
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'class_user', 'course_class_id', 'user_id')
            ->join('course_classes', 'class_user.course_class_id', '=', 'course_classes.id')
            ->where('course_classes.course_id', $this->id);
    }
}
