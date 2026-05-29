<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
            {{ __('Ubah Tugas Penilaian') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
            <h3 class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4 flex items-center gap-3">
                <svg class="h-6 w-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Ubah Detail Tugas: <span class="text-indigo-600 font-black">{{ $assignment->title }}</span>
            </h3>
            
            <form action="{{ route('assignments.update', $assignment) }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="course_id" class="block text-sm font-bold text-gray-700 mb-2">Pilih Mata Pelajaran</label>
                        <select id="course_id" name="course_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" required>
                            <option value="">Pilih Mapel...</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id', $assignment->course_id) == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                            @endforeach
                        </select>
                        @error('course_id')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="module_id" class="block text-sm font-bold text-gray-700 mb-2">Pilih Bab / Modul</label>
                        <select id="module_id" name="module_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" required>
                            <option value="">Pilih Bab...</option>
                        </select>
                        @error('module_id')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="deadline" class="block text-sm font-bold text-gray-700 mb-2">Batas Pengumpulan (Deadline)</label>
                        <input type="datetime-local" id="deadline" name="deadline" value="{{ old('deadline', $assignment->deadline ? $assignment->deadline->format('Y-m-d\TH:i') : '') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" required>
                        @error('deadline')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-bold text-gray-700 mb-2">Judul Tugas</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $assignment->title) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" placeholder="Cth: Tugas Praktik Membuat Website Portofolio" required>
                        @error('title')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Instruksi Tugas</label>
                        <textarea name="description" id="description" rows="5" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" placeholder="Tulis instruksi lengkap penugasan di sini..." required>{{ old('description', $assignment->description) }}</textarea>
                        @error('description')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="attachment" class="block text-sm font-bold text-gray-700 mb-2">Lampiran LKPD / File Tambahan (Maks 100MB)</label>
                        
                        @if($assignment->attachment)
                            @php
                                $filename = basename($assignment->attachment);
                                $ext = strtolower(pathinfo($assignment->attachment, PATHINFO_EXTENSION));
                            @endphp
                            <div class="mb-3 p-3 bg-indigo-50 border border-indigo-150 rounded-xl flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <svg class="w-5 h-5 text-indigo-550" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span class="text-xs font-bold text-indigo-900">{{ $filename }}</span>
                                </div>
                                <a href="{{ asset('storage/' . $assignment->attachment) }}" target="_blank" class="text-xs font-bold text-indigo-650 hover:underline">Lihat Lampiran</a>
                            </div>
                        @endif

                        <input type="file" name="attachment" id="attachment" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-xs text-gray-500 mt-1">Biarkan kosong jika tidak ingin mengubah. Format: PDF, DOC, DOCX.</p>
                        @error('attachment')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="max_score" class="block text-sm font-bold text-gray-700 mb-2">Nilai Maksimal</label>
                        <input type="number" name="max_score" id="max_score" value="{{ old('max_score', $assignment->max_score) }}" min="1" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" required>
                        @error('max_score')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>
                    
                    <div class="md:col-span-2 pt-2">
                        <label class="relative flex items-center cursor-pointer">
                            <input type="checkbox" name="is_published" value="1" class="sr-only peer" {{ old('is_published', $assignment->is_published) ? 'checked' : '' }}>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            <span class="ml-3 text-sm font-bold text-gray-900">Publish ke Siswa (Jangan jadikan draf)</span>
                        </label>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                    <a href="{{ route('assignments.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors hidden sm:block">
                        Batal
                    </a>
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-transform transform active:scale-95">
                        Simpan Perubahan Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const courseSelect = document.getElementById('course_id');
            const moduleSelect = document.getElementById('module_id');
            const courseModules = {!! json_encode($courses->mapWithKeys(fn($c) => [$c->id => $c->modules])) !!};
            const currentModuleId = {{ $assignment->module_id ?? 'null' }};

            function updateModules(courseId, selectedModuleId = null) {
                moduleSelect.innerHTML = '<option value="">Pilih Bab...</option>';
                if (!courseId || !courseModules[courseId]) return;

                courseModules[courseId].forEach(mod => {
                    const opt = document.createElement('option');
                    opt.value = mod.id;
                    opt.textContent = mod.title;
                    if (selectedModuleId && Number(mod.id) === Number(selectedModuleId)) {
                        opt.selected = true;
                    } else if (Number(mod.id) === Number('{{ old('module_id') }}')) {
                        opt.selected = true;
                    }
                    moduleSelect.appendChild(opt);
                });
            }

            courseSelect.addEventListener('change', function () {
                updateModules(this.value);
            });

            // Initial load check
            if (courseSelect.value) {
                updateModules(courseSelect.value, currentModuleId);
            }
        });
    </script>
</x-app-layout>
