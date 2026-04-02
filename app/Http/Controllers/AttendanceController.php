<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use App\Models\Course;
use Illuminate\Http\Request;

class AttendanceController extends Controller
{
    public function create(Request $request)
    {
        $courses = Course::all();
        $selectedCourse = $request->query('course_id');
        return view('attendances.create', compact('courses', 'selectedCourse'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'date' => 'required|date',
            'start_time' => 'required|date_format:H:i',
            'end_time' => 'required|date_format:H:i|after:start_time',
        ]);

        Attendance::create($request->all());

        return redirect()->route('student.courses.show', $request->course_id)
            ->with('success', 'Sesi absensi berhasil dibuat.');
    }

    public function show(Attendance $attendance)
    {
        $attendance->load('records.user');
        return view('attendances.show', compact('attendance'));
    }

    public function destroy(Attendance $attendance)
    {
        $course_id = $attendance->course_id;
        $attendance->delete();

        return redirect()->route('student.courses.show', $course_id)
            ->with('success', 'Sesi absensi berhasil dihapus.');
    }
}
