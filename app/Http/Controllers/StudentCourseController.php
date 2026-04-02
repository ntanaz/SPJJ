<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class StudentCourseController extends Controller
{
    public function index()
    {
        $courses = Course::with('materials')->paginate(10);
        return view('student.courses', compact('courses'));
    }

    public function show(Course $course)
    {
        $course->load([
            'materials' => function($query) {
                $query->orderBy('order');
            },
            'assignments' => function($query) {
                $query->orderBy('deadline');
            },
            'quizzes',
            'attendances',
            'discussions' => function($query) {
                $query->latest();
            },
            'discussions.user'
        ]);
        return view('student.course_details', compact('course'));
    }
}
