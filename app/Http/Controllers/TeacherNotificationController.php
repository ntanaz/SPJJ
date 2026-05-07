<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TeacherNotificationController extends Controller
{
    public function index()
    {
        $notifications = auth()->user()->notifications()->paginate(15);
        return view('teacher.notifications', compact('notifications'));
    }

    public function markAsRead($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        return back()->with('success', 'Notifikasi ditandai sebagai dibaca.');
    }
}
