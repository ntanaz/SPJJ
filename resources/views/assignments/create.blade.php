<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
            {{ __('Tugas Baru') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
            <h3 class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4 flex items-center gap-3">
                <svg class="h-6 w-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                Buat Tugas Penilaian
            </h3>
            
            <form action="{{ route('assignments.store') }}" method="POST" class="space-y-6">
                @csrf
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="course_id" class="block text-sm font-bold text-gray-700 mb-2">Pilih Mata Pelajaran</label>
                        <select id="course_id" name="course_id" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" required>
                            <option value="">Pilih Mapel...</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}" {{ old('course_id') == $course->id ? 'selected' : '' }}>{{ $course->name }}</option>
                            @endforeach
                        </select>
                        @error('course_id')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="deadline" class="block text-sm font-bold text-gray-700 mb-2">Batas Pengumpulan (Deadline)</label>
                        <input type="datetime-local" id="deadline" name="deadline" value="{{ old('deadline') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" required>
                        @error('deadline')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-bold text-gray-700 mb-2">Judul Tugas</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" placeholder="Cth: Tugas Praktik Membuat Website Portofolio" required>
                        @error('title')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Instruksi Tugas</label>
                        <textarea name="description" id="description" rows="5" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" placeholder="Tulis instruksi lengkap penugasan di sini..." required>{{ old('description') }}</textarea>
                        @error('description')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                    <a href="{{ route('assignments.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors hidden sm:block">
                        Batal
                    </a>
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-transform transform active:scale-95">
                        Simpan & Sebarkan Tugas
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
