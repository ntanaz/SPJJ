<x-app-layout>
    <x-slot name="header">
        {{ __('Rekap & Export Nilai Siswa') }}
    </x-slot>

    <div class="space-y-6">
        @foreach($classes as $class)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
            <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                <div class="flex items-center space-x-3">
                    <div class="w-10 h-10 bg-indigo-100 text-indigo-600 rounded-xl flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    </div>
                    <div>
                        <h3 class="font-bold text-gray-800 text-lg">{{ $class->name }}</h3>
                        <p class="text-sm text-gray-500">Mapel: {{ $class->course->name }}</p>
                    </div>
                </div>
                <div>
                    <a href="{{ route('teacher.grade-recap.export', ['course_class_id' => $class->id]) }}" class="px-4 py-2 bg-green-600 hover:bg-green-700 text-white text-sm font-bold rounded-lg transition-colors flex items-center space-x-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/></svg>
                        <span>Export CSV</span>
                    </a>
                </div>
            </div>
            <div class="p-4 text-sm text-gray-600">
                <p>Klik tombol <strong>Export CSV</strong> di atas untuk mengunduh rekap nilai lengkap seluruh siswa di kelas ini beserta nilai rata-ratanya.</p>
            </div>
        </div>
        @endforeach

        @if($classes->isEmpty())
        <div class="p-8 text-center bg-white rounded-2xl border border-gray-100 shadow-sm text-gray-500">
            Anda belum memiliki kelas yang ditugaskan. Silakan buat kelas atau hubungi Admin.
        </div>
        @endif
    </div>
</x-app-layout>
