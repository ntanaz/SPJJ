<x-app-layout>
    <x-slot name="header">
        {{ __('Pengaturan Sistem Zenith') }}
    </x-slot>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <form action="{{ route('admin.settings.update') }}" method="POST" class="p-6">
            @csrf
            
            <div class="space-y-6">
                <!-- App Name -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Aplikasi</label>
                    <input type="text" name="app_name" value="{{ $settings['app_name']->value ?? config('app.name') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <p class="mt-1 text-xs text-gray-500">Nama yang akan tampil pada header dan title bar.</p>
                </div>

                <!-- App Description -->
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Deskripsi Aplikasi</label>
                    <textarea name="app_description" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ $settings['app_description']->value ?? '' }}</textarea>
                </div>

                <!-- Academic Year/Semester -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tahun Ajaran</label>
                        <input type="text" name="academic_year" value="{{ $settings['academic_year']->value ?? '2025/2026' }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Semester</label>
                        <select name="semester" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                            <option value="Ganjil" {{ ($settings['semester']->value ?? '') == 'Ganjil' ? 'selected' : '' }}>Ganjil</option>
                            <option value="Genap" {{ ($settings['semester']->value ?? '') == 'Genap' ? 'selected' : '' }}>Genap</option>
                        </select>
                    </div>
                </div>

                <hr class="border-gray-100">

                <h3 class="text-lg font-bold text-gray-900">Toggle Fitur</h3>

                <div class="space-y-4">
                    <!-- Feature: Discussions -->
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="hidden" name="feature_discussions" value="0">
                        <input type="checkbox" name="feature_discussions" value="1" {{ ($settings['feature_discussions']->value ?? '1') == '1' ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500 h-5 w-5">
                        <span class="text-gray-900 font-medium">Aktifkan Forum Diskusi</span>
                    </label>

                    <!-- Feature: Gamification -->
                    <label class="flex items-center space-x-3 cursor-pointer">
                        <input type="hidden" name="feature_gamification" value="0">
                        <input type="checkbox" name="feature_gamification" value="1" {{ ($settings['feature_gamification']->value ?? '1') == '1' ? 'checked' : '' }} class="rounded text-indigo-600 focus:ring-indigo-500 h-5 w-5">
                        <span class="text-gray-900 font-medium">Aktifkan Sistem Poin/Gamifikasi</span>
                    </label>
                </div>
            </div>

            <div class="mt-8 flex justify-end">
                <button type="submit" class="px-6 py-2.5 bg-indigo-600 text-white rounded-xl font-medium hover:bg-indigo-700 focus:ring-4 focus:ring-indigo-100 transition-colors">
                    Simpan Pengaturan
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
