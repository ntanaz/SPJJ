<?php

namespace App\Http\Controllers;

use App\Models\Course;
use Illuminate\Http\Request;

class CourseController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $courses = Course::latest()->paginate(10);
        return view('courses.index', compact('courses'));
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        return view('courses.create');
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'code' => 'nullable|string|unique:courses,code',
            'cover_image' => 'nullable|image|max:2048',
            'banner_image' => 'nullable|image|max:2048',
            'is_leaderboard_enabled' => 'boolean'
        ]);

        $data = $request->all();
        $data['is_leaderboard_enabled'] = $request->has('is_leaderboard_enabled');

        if ($request->hasFile('cover_image')) {
            $data['cover_image'] = $request->file('cover_image')->store('courses/covers', 'public');
        }
        if ($request->hasFile('banner_image')) {
            $data['banner_image'] = $request->file('banner_image')->store('courses/banners', 'public');
        }

        $course = Course::create($data);

        // If the user is a teacher, automatically create a class for them
        if (auth()->user()->hasRole(['guru', 'teacher'])) {
            \App\Models\CourseClass::create([
                'name' => 'Kelas ' . $course->name,
                'course_id' => $course->id,
                'teacher_id' => auth()->id(),
            ]);
        }

        return redirect()->route('courses.index')->with('success', 'Mata Pelajaran berhasil ditambahkan.');
    }

    /**
     * Display the specified resource.
     */
    public function show(Course $course)
    {
        return view('courses.show', compact('course'));
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Course $course)
    {
        return view('courses.edit', compact('course'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Course $course)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string',
            'code' => 'nullable|string|unique:courses,code,' . $course->id,
            'cover_image' => 'nullable|image|max:2048',
            'banner_image' => 'nullable|image|max:2048',
            'is_leaderboard_enabled' => 'boolean'
        ]);

        $data = $request->all();
        $data['is_leaderboard_enabled'] = $request->has('is_leaderboard_enabled');

        if ($request->hasFile('cover_image')) {
            if ($course->cover_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($course->cover_image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($course->cover_image);
            }
            $data['cover_image'] = $request->file('cover_image')->store('courses/covers', 'public');
        }
        if ($request->hasFile('banner_image')) {
            if ($course->banner_image && \Illuminate\Support\Facades\Storage::disk('public')->exists($course->banner_image)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($course->banner_image);
            }
            $data['banner_image'] = $request->file('banner_image')->store('courses/banners', 'public');
        }

        $course->update($data);

        return redirect()->route('courses.index')->with('success', 'Mata Pelajaran berhasil diperbarui.');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Course $course)
    {
        $course->delete();
        return redirect()->route('courses.index')->with('success', 'Mata Pelajaran berhasil dihapus.');
    }
}
