<?php

namespace App\Http\Controllers;

use App\Models\Assignment;
use App\Models\Submission;
use Illuminate\Http\Request;
use Carbon\Carbon;

class StudentAssignmentController extends Controller
{
    public function show(Assignment $assignment)
    {
        $assignment->load('course');
        $submission = $assignment->submissions()->where('user_id', auth()->id())->first();
        
        $now = Carbon::now();
        $isPastDeadline = $now->isAfter($assignment->deadline);
        $timeLeft = $isPastDeadline ? 'Waktu habis' : $assignment->deadline->diffForHumans($now, ['parts' => 2, 'short' => true, 'syntax' => Carbon::DIFF_ABSOLUTE]);

        return view('student.assignments.show', compact('assignment', 'submission', 'isPastDeadline', 'timeLeft'));
    }

    public function submit(Request $request, Assignment $assignment)
    {
        if (Carbon::now()->isAfter($assignment->deadline)) {
            return back()->with('error', 'Maaf, batas waktu pengumpulan tugas ini telah berakhir.');
        }

        $request->validate([
            'file' => 'required|file|max:10240', // max 10MB
        ]);

        $path = $request->file('file')->store('submissions', 'public');

        $submission = Submission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'user_id' => auth()->id()],
            ['file_path' => $path]
        );

        return back()->with('success', 'Tugas berhasil dikumpulkan!');
    }
}
