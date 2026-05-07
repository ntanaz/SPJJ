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
            'attachment' => 'nullable|file|max:20480',
            'is_published' => 'boolean',
            'max_score' => 'required|integer|min:1'
        ]);

        $data = $request->except('attachment');
        $data['is_published'] = $request->has('is_published');
        
        if ($request->hasFile('attachment')) {
            $data['attachment'] = $request->file('attachment')->store('assignments', 'public');
        }

        $assignment = Assignment::create($data);

        // Notify students
        if ($assignment->is_published) {
            $students = \App\Models\User::whereHas('courseClasses', function($q) use ($assignment) {
                $q->where('course_id', $assignment->course_id);
            })->get();
            
            \Illuminate\Support\Facades\Notification::send($students, new \App\Notifications\NewAssignmentNotification($assignment));
        }

        return redirect()->route('assignments.index')->with('success', 'Tugas berhasil dibuat.');
    }

    public function update(Request $request, Assignment $assignment)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'deadline' => 'required|date',
            'attachment' => 'nullable|file|max:20480',
            'max_score' => 'required|integer|min:1'
        ]);

        $data = $request->except('attachment');
        $data['is_published'] = $request->has('is_published');
        
        if ($request->hasFile('attachment')) {
            if ($assignment->attachment && \Illuminate\Support\Facades\Storage::disk('public')->exists($assignment->attachment)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($assignment->attachment);
            }
            $data['attachment'] = $request->file('attachment')->store('assignments', 'public');
        }

        $assignment->update($data);

        return redirect()->route('assignments.index')->with('success', 'Tugas berhasil diperbarui.');
    }

    public function submissions(Assignment $assignment)
    {
        $submissions = $assignment->submissions()->with('user')->paginate(20);
        return view('assignments.submissions', compact('assignment', 'submissions'));
    }

    public function gradeSubmission(Request $request, \App\Models\Submission $submission)
    {
        $request->validate([
            'grade' => 'required|numeric|min:0|max:' . $submission->assignment->max_score,
            'feedback' => 'nullable|string',
        ]);

        $submission->update([
            'grade' => $request->grade,
            'feedback' => $request->feedback,
            'graded_at' => now(),
        ]);

        return back()->with('success', 'Nilai berhasil diberikan kepada ' . $submission->user->name);
    }

    public function destroy(Assignment $assignment)
    {
        $assignment->delete();
        return redirect()->route('assignments.index')->with('success', 'Tugas berhasil dihapus.');
    }
}
