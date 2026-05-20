<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use App\Models\DiscussionReply;
use Illuminate\Http\Request;

class DiscussionReplyController extends Controller
{
    public function store(Request $request, Discussion $discussion)
    {
        $request->validate([
            'content' => 'required|string|max:2000'
        ]);

        $reply = $discussion->replies()->create([
            'user_id' => auth()->id(),
            'content' => $request->content
        ]);

        // Award XP using XpService
        \App\Services\XpService::addXp(auth()->user(), 'discussion_reply', $discussion->module_id, 'DiscussionReply', $reply->id);

        return back()->with('success', 'Tanggapan berhasil dikirim.');
    }

    public function grade(Request $request, DiscussionReply $reply)
    {
        if (!auth()->user()->hasRole(['guru', 'teacher', 'admin'])) {
            abort(403);
        }

        $request->validate([
            'grade' => 'required|integer|min:0|max:100'
        ]);

        $reply->update(['grade' => $request->grade]);

        return back()->with('success', 'Penilaian berhasil disimpan.');
    }

    public function destroy(DiscussionReply $reply)
    {
        if (auth()->id() === $reply->user_id || auth()->user()->hasRole(['guru', 'teacher', 'admin'])) {
            $reply->delete();
            return back()->with('success', 'Balasan berhasil dihapus.');
        }

        abort(403);
    }
}
