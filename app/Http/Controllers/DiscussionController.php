<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Discussion;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();

        if ($user->hasRole('admin')) {
            $courses = \App\Models\Course::with([
                'discussions' => function($q) {
                    $q->withCount('replies')->orderBy('is_pinned', 'desc')->orderBy('updated_at', 'desc');
                },
                'discussions.user',
                'discussions.replies',
                'discussions.material'
            ])->get();
        } elseif ($user->hasRole(['guru', 'teacher'])) {
            $courseIds = \App\Models\CourseClass::where('teacher_id', $user->id)->pluck('course_id');
            $courses = \App\Models\Course::whereIn('id', $courseIds)
                ->with([
                    'discussions' => function($q) {
                        $q->withCount('replies')->orderBy('is_pinned', 'desc')->orderBy('updated_at', 'desc');
                    },
                    'discussions.user',
                    'discussions.replies',
                    'discussions.material'
                ])->get();
        } else {
            // Student
            $classIds = \DB::table('class_user')->where('user_id', $user->id)->pluck('course_class_id');
            $courseIds = \App\Models\CourseClass::whereIn('id', $classIds)->pluck('course_id');
            $courses = \App\Models\Course::whereIn('id', $courseIds)
                ->with([
                    'discussions' => function($q) {
                        $q->withCount('replies')->orderBy('is_pinned', 'desc')->orderBy('updated_at', 'desc');
                    },
                    'discussions.user',
                    'discussions.replies',
                    'discussions.material'
                ])->get();
        }

        return view('discussions.index', compact('courses'));
    }

    public function store(Request $request, Course $course)
    {
        if (!auth()->user()->hasRole(['guru', 'teacher', 'admin'])) {
            abort(403, 'Siswa tidak diperbolehkan membuat topik diskusi utama.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:1000',
            'module_id' => 'nullable|exists:modules,id'
        ]);

        Discussion::query()->create([
            'course_id' => $course->id,
            'module_id' => $request->module_id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content
        ]);

        return back()->with('success', 'Topik diskusi berhasil dibuat.');
    }

    public function storeForMaterial(Request $request, \App\Models\Material $material)
    {
        if (!auth()->user()->hasRole(['guru', 'teacher', 'admin'])) {
            abort(403, 'Siswa tidak diperbolehkan membuat topik diskusi utama.');
        }

        $request->validate([
            'title' => 'required|string|max:255',
            'content' => 'required|string|max:1000'
        ]);

        Discussion::query()->create([
            'course_id' => $material->course_id,
            'module_id' => $material->module_id,
            'material_id' => $material->id,
            'user_id' => auth()->id(),
            'title' => $request->title,
            'content' => $request->content
        ]);

        return back()->with('success', 'Topik diskusi materi berhasil dibuat.');
    }

    public function show(Discussion $discussion)
    {
        $discussion->load(['course', 'material', 'user', 'replies.user' => function($query) {
            $query->orderBy('created_at', 'asc');
        }]);

        return view('discussions.show', compact('discussion'));
    }

    public function destroy(Discussion $discussion)
    {
        // Hanya yang nulis atau guru/admin yang bisa hapus
        if (auth()->id() === $discussion->user_id || auth()->user()->hasRole(['guru', 'teacher', 'admin'])) {
            $discussion->delete();
            return back()->with('success', 'Pesan diskusi berhasil dihapus.');
        }

        return abort(403);
    }

    public function pin(Discussion $discussion)
    {
        if (auth()->user()->hasRole(['guru', 'teacher', 'admin'])) {
            $discussion->update(['is_pinned' => !$discussion->is_pinned]);
            return back()->with('success', 'Status sematkan (pin) diskusi diperbarui.');
        }
        return abort(403);
    }

    public function lock(Discussion $discussion)
    {
        if (auth()->user()->hasRole(['guru', 'teacher', 'admin'])) {
            $discussion->update(['is_locked' => !$discussion->is_locked]);
            return back()->with('success', 'Status kunci (lock) diskusi diperbarui.');
        }
        return abort(403);
    }
}
