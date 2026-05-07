<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Material;

class StudentMaterialController extends Controller
{
    public function show(Material $material)
    {
        $material->load('course');
        
        // Progress check
        if ($material->requires_previous) {
            $previousMaterials = Material::where('course_id', $material->course_id)
                ->where('order', '<', $material->order)
                ->get();
            
            foreach ($previousMaterials as $prev) {
                $progress = \App\Models\MaterialProgress::where('material_id', $prev->id)
                    ->where('user_id', auth()->id())
                    ->where('is_completed', true)
                    ->first();
                if (!$progress) {
                    return back()->with('error', 'Anda harus menyelesaikan materi sebelumnya terlebih dahulu.');
                }
            }
        }

        // Record progress (mark as viewed/in-progress)
        \App\Models\MaterialProgress::firstOrCreate(
            ['material_id' => $material->id, 'user_id' => auth()->id()]
        );

        return view('student.materials.show', compact('material'));
    }

    public function complete(Material $material)
    {
        \App\Models\MaterialProgress::updateOrCreate(
            ['material_id' => $material->id, 'user_id' => auth()->id()],
            ['is_completed' => true, 'completed_at' => now()]
        );

        return back()->with('success', 'Materi ditandai sebagai selesai!');
    }
}
