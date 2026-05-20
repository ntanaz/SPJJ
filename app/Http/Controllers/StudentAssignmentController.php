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
        $request->validate([
            'text_content' => 'nullable|string',
            'files.*' => 'nullable|file|mimes:pdf,doc,docx,zip,jpeg,png,jpg,mp4,mov,avi,webm|max:51200', // max 50MB per file
        ]);

        if (!$request->filled('text_content') && !$request->hasFile('files')) {
            return back()->with('error', 'Silakan isi teks jawaban atau unggah file.');
        }

        $now = Carbon::now();
        $isLate = $now->isAfter($assignment->deadline);

        // Upload multiple files if present
        $attachments = [];
        if ($request->hasFile('files')) {
            foreach ($request->file('files') as $file) {
                $path = $file->store('submissions', 'public');
                $attachments[] = [
                    'name' => $file->getClientOriginalName(),
                    'path' => $path
                ];
            }
        }

        $submission = Submission::where('assignment_id', $assignment->id)
            ->where('user_id', auth()->id())
            ->first();

        // If existing submission has files, we can either append or replace. For simplicity, let's append.
        if ($submission && $submission->attachments) {
            $attachments = array_merge($submission->attachments, $attachments);
        }

        $submissionRecord = Submission::updateOrCreate(
            ['assignment_id' => $assignment->id, 'user_id' => auth()->id()],
            [
                'text_content' => $request->text_content ?? ($submission->text_content ?? null),
                'attachments' => $attachments,
                'status' => 'submitted',
                'is_late' => $isLate,
                // store the first file path to the legacy column if it exists to maintain compatibility
                'file_path' => count($attachments) > 0 ? $attachments[0]['path'] : null,
            ]
        );

        // Award XP using XpService
        \App\Services\XpService::addXp(auth()->user(), 'assignment', $assignment->module_id, 'Submission', $submissionRecord->id);

        // Notify teacher
        $assignment->load('course.classes.teacher');
        $teachers = collect();
        foreach ($assignment->course->classes as $class) {
            if ($class->teacher) {
                $teachers->push($class->teacher);
            }
        }
        \Illuminate\Support\Facades\Notification::send($teachers->unique('id'), new \App\Notifications\SubmissionReceivedNotification($submissionRecord));

        $message = $isLate ? 'Tugas berhasil dikumpulkan (Terlambat).' : 'Tugas berhasil dikumpulkan!';
        return back()->with('success', $message);
    }
}
