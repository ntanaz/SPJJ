<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\LearningResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class LearningResourceController extends Controller
{
    public function index(Request $request)
    {
        $query = LearningResource::latest();
        
        if ($request->has('search')) {
            $query->where('title', 'like', '%' . $request->search . '%')
                  ->orWhere('description', 'like', '%' . $request->search . '%');
        }

        if ($request->has('category') && $request->category != '') {
            $query->where('category', $request->category);
        }

        $resources = $query->paginate(10);
        return view('admin.learning_resources.index', compact('resources'));
    }

    public function create()
    {
        return view('admin.learning_resources.create');
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'file' => 'required|file|mimes:pdf,doc,docx,ppt,pptx,jpg,jpeg,png,zip|max:102400', // max 100MB
            'category' => 'nullable|string',
        ]);

        if ($request->hasFile('file')) {
            $file = $request->file('file');
            $path = $file->store('learning_resources', 'public');
            
            LearningResource::create([
                'title' => $validated['title'],
                'description' => $validated['description'],
                'file_path' => $path,
                'type' => $file->getClientOriginalExtension(),
                'category' => $validated['category'],
                'user_id' => auth()->id(),
            ]);
        }

        return redirect()->route('admin.learning-resources.index')->with('success', 'Materi/Bank Soal berhasil diunggah.');
    }

    public function destroy(LearningResource $learningResource)
    {
        if ($learningResource->file_path && Storage::disk('public')->exists($learningResource->file_path)) {
            Storage::disk('public')->delete($learningResource->file_path);
        }
        
        $learningResource->delete();
        return redirect()->route('admin.learning-resources.index')->with('success', 'Materi/Bank Soal berhasil dihapus.');
    }
}
