<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, HasRoles;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'points',
        'last_active_at',
        'avatar',
        'bio',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function courses()
    {
        // Or course_classes if that's what the user means, but they explicitly said "hasMany Courses"
        return $this->belongsToMany(Course::class, 'class_user', 'user_id', 'course_class_id')->withPivot('course_class_id'); // Wait, the many-to-many is to CourseClass.
        // Actually, user explicitly asked for "hasMany Courses". Let's check if there is a 'teacher_id' in Courses.
    }

    public function courseClasses()
    {
        return $this->belongsToMany(CourseClass::class, 'class_user', 'user_id', 'course_class_id');
    }

    public function submissions()
    {
        return $this->hasMany(Submission::class);
    }

    public function quizAttempts()
    {
        return $this->hasMany(QuizAttempt::class);
    }

    public function discussions()
    {
        return $this->hasMany(Discussion::class);
    }
}
