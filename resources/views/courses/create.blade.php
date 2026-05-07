<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
            {{ __('Tambah Mata Pelajaran') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
            <h3 class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4">Detail Mata Pelajaran</h3>
            
            <form action="{{ route('courses.store') }}" method="POST" class="space-y-6" enctype="multipart/form-data">
                @csrf
                
                <div>
                    <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Nama Mata Pelajaran</label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" placeholder="Cth: Kecerdasan Artifisial" required>
                    @error('name')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="code" class="block text-sm font-bold text-gray-700 mb-2">Kode Kelas (Opsional)</label>
                    <input type="text" name="code" id="code" value="{{ old('code') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" placeholder="Cth: AI-101">
                    @error('code')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Deskripsi / Penjelasan Singkat</label>
                    <textarea name="description" id="description" rows="4" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" placeholder="Tuliskan deskripsi mata pelajaran..." required>{{ old('description') }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="cover_image" class="block text-sm font-bold text-gray-700 mb-2">Thumbnail / Cover Image</label>
                        <input type="file" name="cover_image" id="cover_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-xs text-gray-500 mt-1">Format: JPG/PNG/WEBP, Maks 2MB.</p>
                        @error('cover_image')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="banner_image" class="block text-sm font-bold text-gray-700 mb-2">Banner Image (Opsional)</label>
                        <input type="file" name="banner_image" id="banner_image" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-xs text-gray-500 mt-1">Format: JPG/PNG/WEBP, Maks 2MB. Resolusi disarankan 1920x400.</p>
                        @error('banner_image')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                    </div>
                </div>

                <div class="pt-4">
                    <label class="relative flex items-center cursor-pointer">
                        <input type="checkbox" name="is_leaderboard_enabled" value="1" class="sr-only peer" checked>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none peer-focus:ring-4 peer-focus:ring-indigo-300 rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-indigo-600"></div>
                        <span class="ml-3 text-sm font-bold text-gray-900">Aktifkan Leaderboard (Peringkat Siswa Berdasarkan Nilai)</span>
                    </label>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('courses.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors hidden sm:block">
                        Batal
                    </a>
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-transform transform active:scale-95">
                        Simpan Mata Pelajaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
