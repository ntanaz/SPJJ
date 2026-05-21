<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
            {{ __('Ubah Ujian / Kuis') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
            <h3 class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4 flex items-center gap-3">
                <svg class="h-6 w-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/></svg>
                Edit Evaluasi Siswa
            </h3>
            
            <form action="{{ route('quizzes.update', $quiz) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="course_id" class="block text-sm font-bold text-gray-700 mb-2">Pilih Mata Pelajaran</label>
                        <select id="course_id" name="course_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" required>
                            <option value="">Pilih Mapel...</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ (old('course_id', $quiz->course_id) == $course->id) ? 'selected' : '' }}>{{ $course->name }}</option>
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
                        <label for="title" class="block text-sm font-bold text-gray-700 mb-2">Judul Kuis / Ujian</label>
                        <input type="text" name="title" id="title" value="{{ old('title', $quiz->title) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" placeholder="Cth: Ujian Tengah Semester (UTS) WebDev" required>
                        @error('title')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Deskripsi / Peraturan Kuis</label>
                        <textarea name="description" id="description" rows="5" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" placeholder="Tulis instruksi atau aturan mengerjakannya di sini..." required>{{ old('description', $quiz->description) }}</textarea>
                        @error('description')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                    <a href="{{ route('quizzes.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors">
                        Batal
                    </a>
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-transform transform active:scale-95">
                        Simpan Perubahan
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

            function updateModules(courseId, selectedModuleId = null) {
                moduleSelect.innerHTML = '<option value="">Pilih Bab...</option>';
                if (!courseId || !courseModules[courseId]) return;

                courseModules[courseId].forEach(mod => {
                    const opt = document.createElement('option');
                    opt.value = mod.id;
                    opt.textContent = mod.title;
                    if (selectedModuleId && Number(mod.id) === Number(selectedModuleId)) {
                        opt.selected = true;
                    } else if (Number(mod.id) === Number('{{ old('module_id', $quiz->module_id) }}')) {
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
                updateModules(courseSelect.value, '{{ old('module_id', $quiz->module_id) }}');
            }
        });
    </script>
</x-app-layout>
