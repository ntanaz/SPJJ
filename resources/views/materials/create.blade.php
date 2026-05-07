<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
            {{ __('Unggah Materi') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
            <h3 class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4 flex items-center gap-3">
                <svg class="h-6 w-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                Tambah Modul Belajar
            </h3>
            
            <form action="{{ route('materials.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
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
                        <label for="format" class="block text-sm font-bold text-gray-700 mb-2">Format Materi</label>
                        <select id="format" name="format" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" required onchange="toggleFormatFields(this.value)">
                            <option value="document">Dokumen / File (PDF, DOCX, PPTX, Image)</option>
                            <option value="video">Video (Lokal / Embed YouTube)</option>
                            <option value="link">Link Eksternal / Meeting</option>
                            <option value="text">Teks / Artikel</option>
                        </select>
                        @error('format')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="title" class="block text-sm font-bold text-gray-700 mb-2">Judul Pembelajaran</label>
                        <input type="text" name="title" id="title" value="{{ old('title') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" placeholder="Cth: Bab 1 - Pengenalan" required>
                        @error('title')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2" id="file_field">
                        <label for="file" class="block text-sm font-bold text-gray-700 mb-2">Upload File</label>
                        <input type="file" name="file" id="file" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        @error('file')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2 hidden" id="youtube_field">
                        <label for="youtube_url" class="block text-sm font-bold text-gray-700 mb-2">YouTube URL</label>
                        <input type="url" name="youtube_url" id="youtube_url" value="{{ old('youtube_url') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500" placeholder="https://youtube.com/...">
                        @error('youtube_url')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2 hidden" id="text_field">
                        <label for="text_content" class="block text-sm font-bold text-gray-700 mb-2">Konten Teks</label>
                        <textarea name="text_content" id="text_content" rows="6" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500" placeholder="Tuliskan materi di sini...">{{ old('text_content') }}</textarea>
                        @error('text_content')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2">
                        <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Singkat (Opsional)</label>
                        <textarea name="description" id="description" rows="2" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" placeholder="Tulis instruksi atau keterangan singkat mengenai materi ini...">{{ old('description') }}</textarea>
                        @error('description')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="order" class="block text-sm font-bold text-gray-700 mb-2">Urutan Materi (Order)</label>
                        <input type="number" name="order" id="order" value="{{ old('order', 1) }}" min="1" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" required>
                        @error('order')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>
                    
                    <div>
                        <label for="publish_at" class="block text-sm font-bold text-gray-700 mb-2">Jadwal Publish (Opsional)</label>
                        <input type="datetime-local" name="publish_at" id="publish_at" value="{{ old('publish_at') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors">
                        @error('publish_at')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div class="md:col-span-2 flex space-x-6 pt-4">
                        <label class="relative flex items-center cursor-pointer">
                            <input type="checkbox" name="is_published" value="1" class="sr-only peer" checked>
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            <span class="ml-3 text-sm font-bold text-gray-900">Publish Langsung</span>
                        </label>
                        
                        <label class="relative flex items-center cursor-pointer">
                            <input type="checkbox" name="requires_previous" value="1" class="sr-only peer">
                            <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                            <span class="ml-3 text-sm font-bold text-gray-900 group-hover:text-indigo-600">Progress Lock (Wajib Selesai Materi Sebelumnya)</span>
                        </label>
                    </div>

                    <script>
                        function toggleFormatFields(format) {
                            document.getElementById('file_field').classList.add('hidden');
                            document.getElementById('youtube_field').classList.add('hidden');
                            document.getElementById('text_field').classList.add('hidden');

                            if (format === 'document') {
                                document.getElementById('file_field').classList.remove('hidden');
                            } else if (format === 'video') {
                                document.getElementById('file_field').classList.remove('hidden');
                                document.getElementById('youtube_field').classList.remove('hidden');
                            } else if (format === 'text') {
                                document.getElementById('text_field').classList.remove('hidden');
                            } else if (format === 'link') {
                                document.getElementById('youtube_field').classList.remove('hidden');
                            }
                        }
                        
                        // Run on load
                        document.addEventListener("DOMContentLoaded", function() {
                            toggleFormatFields(document.getElementById('format').value);
                        });
                    </script>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-6 border-t border-gray-100">
                    <a href="{{ route('materials.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors hidden sm:block">
                        Batal
                    </a>
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-transform transform active:scale-95">
                        Simpan & Publikasikan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
