<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Material;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index()
    {
        $materials = Material::with('course')->orderBy('course_id')->orderBy('order')->paginate(15);
        return view('materials.index', compact('materials'));
    }

    public function create()
    {
        $courses = Course::all();
        return view('materials.create', compact('courses'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'title' => 'required|string|max:255',
            'type' => 'required|in:pdf,video,slide,file,meeting_link,video_post_class',
            'order' => 'required|integer',
            'description' => 'nullable|string',
            'is_locked' => 'boolean'
        ]);

        $data = $request->all();
        $data['is_locked'] = $request->has('is_locked');

        Material::create($data);

        return redirect()->route('materials.index')->with('success', 'Materi berhasil diunggah dan dibagikan ke siswa.');
    }

    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('materials.index')->with('success', 'Materi berhasil dihapus.');
    }
}
