<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Course;
use Illuminate\Http\Request;

class AssignmentController extends Controller
{
    public function index()
    {
        $assignments = Assignment::with('course')->withCount('submissions')->latest()->paginate(15);
        return view('assignments.index', compact('assignments'));
    }

    public function create()
    {
        $courses = Course::all();
        return view('assignments.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date',
        ]);

        Assignment::create($request->all());

        return redirect()->route('assignments.index')->with('success', 'Tugas berhasil dibuat.');
    }

    public function destroy(Assignment $assignment)
    {
        $assignment->delete();
        return redirect()->route('assignments.index')->with('success', 'Tugas berhasil dihapus.');
    }
}
