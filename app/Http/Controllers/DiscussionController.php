<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Discussion;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:1000'
        ]);

        Discussion::query()->create([
            'course_id' => $course->id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content
        ]);

        return back()->with('success', 'Topik diskusi berhasil dibuat.');
    }

    public function show(Discussion $discussion)
    {
        $discussion->load(['course', 'user', 'replies.user' => function($query) {
            $query->orderBy('created_at', 'asc');
        }]);

        return view('discussions.show', compact('discussion'));
    }

    public function destroy(Discussion $discussion)
    {
        // Hanya yang nulis atau guru/admin yang bisa hapus
        if (auth()->id() === $discussion->user_id || auth()->user()->hasRole('guru|admin')) {
            $discussion->delete();
            return back()->with('success', 'Pesan diskusi berhasil dihapus.');
        }

        return abort(403);
    }
}
