<x-app-layout>
    <x-slot name="header">
        {{ __('Edit Pengumuman') }}
    </x-slot>

    <div class="max-w-3xl mx-auto">
        <form action="{{ route('admin.announcements.update', $announcement) }}" method="POST" class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6 space-y-6">
            @csrf @method('PUT')

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Judul Pengumuman</label>
                <input type="text" name="title" value="{{ old('title', $announcement->title) }}" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                @error('title')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konten / Isi Pengumuman</label>
                <textarea name="content" rows="5" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('content', $announcement->content) }}</textarea>
                @error('content')<p class="text-red-500 text-xs mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Target Audiens</label>
                    <select name="target_audience" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="all" {{ old('target_audience', $announcement->target_audience) == 'all' ? 'selected' : '' }}>Semua Pengguna</option>
                        <option value="admin" {{ old('target_audience', $announcement->target_audience) == 'admin' ? 'selected' : '' }}>Hanya Admin</option>
                        <option value="guru" {{ old('target_audience', $announcement->target_audience) == 'guru' ? 'selected' : '' }}>Hanya Guru</option>
                        <option value="siswa" {{ old('target_audience', $announcement->target_audience) == 'siswa' ? 'selected' : '' }}>Hanya Siswa</option>
                    </select>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Tingkat Urgensi</label>
                    <select name="urgency_level" required class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="normal" {{ old('urgency_level', $announcement->urgency_level) == 'normal' ? 'selected' : '' }}>Normal</option>
                        <option value="important" {{ old('urgency_level', $announcement->urgency_level) == 'important' ? 'selected' : '' }}>Penting (Important)</option>
                        <option value="urgent" {{ old('urgency_level', $announcement->urgency_level) == 'urgent' ? 'selected' : '' }}>Mendesak (Urgent)</option>
                    </select>
                </div>
            </div>

            <div class="pt-4 flex justify-end gap-3">
                <a href="{{ route('admin.announcements.index') }}" class="px-5 py-2.5 text-gray-600 bg-gray-100 hover:bg-gray-200 rounded-xl font-medium transition-colors">Batal</a>
                <button type="submit" class="px-5 py-2.5 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700 transition-colors">Perbarui Pengumuman</button>
            </div>
        </form>
    </div>
</x-app-layout>
