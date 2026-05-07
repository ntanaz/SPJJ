<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CourseClass extends Model
{
    protected $fillable = ['name', 'course_id', 'teacher_id'];

    public function course()
    {
        return $this->belongsTo(Course::class);
    }

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function students()
    {
        return $this->belongsToMany(User::class, 'class_user');
    }

    // Following the user's specific request "CourseClass hasMany Materials, Assignments, Quizzes, Discussions"
    // Though physically they are tied to course_id. 
    // To cleanly map this without a massive migration, we can query via course_id.
    public function materials()
    {
        return $this->hasMany(Material::class, 'course_id', 'course_id');
    }

    public function assignments()
    {
        return $this->hasMany(Assignment::class, 'course_id', 'course_id');
    }

    public function quizzes()
    {
        return $this->hasMany(Quiz::class, 'course_id', 'course_id');
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class, 'course_id', 'course_id');
    }
}
