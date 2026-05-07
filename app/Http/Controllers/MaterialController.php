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
            'format' => 'required|in:document,video,link,text',
            'order' => 'required|integer',
            'description' => 'nullable|string',
            'is_locked' => 'boolean',
            'requires_previous' => 'boolean',
            'is_published' => 'boolean',
            'publish_at' => 'nullable|date',
            'file' => 'nullable|file|max:20480', // max 20MB
            'youtube_url' => 'nullable|url',
            'text_content' => 'nullable|string'
        ]);

        $data = $request->except('file');
        $data['is_locked'] = $request->has('is_locked');
        $data['requires_previous'] = $request->has('requires_previous');
        $data['is_published'] = $request->has('is_published');
        // Kept type for legacy
        $data['type'] = $request->format == 'document' ? 'file' : ($request->format == 'video' ? 'video' : 'slide');

        if ($request->hasFile('file')) {
            $data['file_path'] = $request->file('file')->store('materials', 'public');
        }

        Material::create($data);

        return redirect()->route('materials.index')->with('success', 'Materi berhasil diunggah dan dibagikan ke siswa.');
    }
    
    public function update(Request $request, Material $material)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'format' => 'required|in:document,video,link,text',
            'order' => 'required|integer',
            'description' => 'nullable|string',
            'file' => 'nullable|file|max:20480',
            'youtube_url' => 'nullable|url',
            'text_content' => 'nullable|string'
        ]);

        $data = $request->except('file');
        $data['is_locked'] = $request->has('is_locked');
        $data['requires_previous'] = $request->has('requires_previous');
        $data['is_published'] = $request->has('is_published');
        $data['type'] = $request->format == 'document' ? 'file' : ($request->format == 'video' ? 'video' : 'slide');

        if ($request->hasFile('file')) {
            if ($material->file_path && \Illuminate\Support\Facades\Storage::disk('public')->exists($material->file_path)) {
                \Illuminate\Support\Facades\Storage::disk('public')->delete($material->file_path);
            }
            $data['file_path'] = $request->file('file')->store('materials', 'public');
        }

        $material->update($data);

        return redirect()->route('materials.index')->with('success', 'Materi berhasil diperbarui.');
    }

    public function destroy(Material $material)
    {
        $material->delete();
        return redirect()->route('materials.index')->with('success', 'Materi berhasil dihapus.');
    }
}
