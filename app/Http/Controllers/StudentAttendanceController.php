<?php

namespace App\Http\Controllers;

use App\Models\Attendance;
use Illuminate\Http\Request;

class StudentAttendanceController extends Controller
{
    public function show(Attendance $attendance)
    {
        $record = $attendance->records()->where('user_id', auth()->id())->first();

        // Check if student has attendance record for this class.
        return view('student.attendances.show', compact('attendance', 'record'));
    }

    public function submit(Request $request, Attendance $attendance)
    {
        if (!$attendance->isCurrentlyOpen()) {
            return back()->with('error', 'Sesi presensi ini sedang tidak aktif (Bukan waktunya).');
        }

        $request->validate([
            'status' => 'required|in:hadir,izin,sakit'
        ]);

        $attendance->records()->updateOrCreate(
            ['user_id' => auth()->id()],
            ['status' => $request->status]
        );

        return back()->with('success', 'Presensi Anda berhasil dicatat.');
    }
}
