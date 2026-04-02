<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Course extends Model
{
    protected $fillable = ['name', 'description', 'cover_image'];

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
}
