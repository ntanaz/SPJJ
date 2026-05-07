<x-app-layout>
    <x-slot name="header">
        {{ __('Upload Sumber Belajar / Bank Soal') }}
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <form action="{{ route('admin.learning-resources.store') }}" method="POST" enctype="multipart/form-data" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-6">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Dokumen</label>
                <input type="text" name="title" value="{{ old('title') }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Kategori</label>
                <select name="category" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="materi" {{ old('category') == 'materi' ? 'selected' : '' }}>Materi Pembelajaran</option>
                    <option value="bank_soal" {{ old('category') == 'bank_soal' ? 'selected' : '' }}>Bank Soal</option>
                </select>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Singkat (Opsional)</label>
                <textarea name="description" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('description') }}</textarea>
                @error('description')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">File Dokumen</label>
                <input type="file" name="file" required accept=".pdf,.doc,.docx,.ppt,.pptx,.jpg,.jpeg,.png" class="w-full rounded-xl border border-gray-300 p-2 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                <p class="text-xs text-gray-500 mt-1">Format didukung: PDF, DOCX, PPTX, JPG, PNG. Maks 10MB.</p>
                @error('file')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('admin.learning-resources.index') }}" class="px-5 py-2.5 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700 transition-colors">Upload Dokumen</button>
            </div>
        </form>
    </div>
</x-app-layout>
