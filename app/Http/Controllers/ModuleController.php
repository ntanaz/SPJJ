<?php

namespace App\Http\Controllers;

use App\Models\Course;
use App\Models\Module;
use Illuminate\Http\Request;

class ModuleController extends Controller
{
    public function store(Request $request, Course $course)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_number' => 'required|integer|min:1',
        ]);

        $course->modules()->create([
            'title' => $request->title,
            'description' => $request->description,
            'order_number' => $request->order_number,
        ]);

        return back()->with('success', 'Bab / Modul pembelajaran berhasil ditambahkan.');
    }

    public function update(Request $request, Module $module)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'order_number' => 'required|integer|min:1',
        ]);

        $module->update([
            'title' => $request->title,
            'description' => $request->description,
            'order_number' => $request->order_number,
        ]);

        return back()->with('success', 'Bab / Modul pembelajaran berhasil diperbarui.');
    }

    public function destroy(Module $module)
    {
        $module->delete();
        return back()->with('success', 'Bab / Modul pembelajaran berhasil dihapus beserta seluruh isinya.');
    }
}
