<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
            {{ __('Mata Pelajaran Anda') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mb-6">
            <h3 class="text-xl font-bold text-gray-800">Lanjutkan Pembelajaran Anda</h3>
            <p class="text-gray-500 text-sm">Pilih mata pelajaran yang ingin Anda akses hari ini.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($courses as $course)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-lg transition-all relative transform hover:-translate-y-1">
                    <div class="h-32 bg-gradient-to-br from-blue-500 to-indigo-600 relative overflow-hidden flex items-center justify-center">
                        <div class="absolute inset-0 opacity-20 mix-blend-overlay bg-[url('https://www.transparenttextures.com/patterns/black-scales.png')]"></div>
                        <svg class="w-16 h-16 text-white/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="text-xl font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">{{ $course->name }}</h4>
                        </div>
                        <p class="text-gray-600 text-sm line-clamp-2 mb-6">{{ $course->description }}</p>
                        
                        <div class="flex justify-between items-center text-xs text-indigo-500 font-bold mb-2">
                            <span>Sisa Modul: {{ $course->materials->count() }} Bab</span>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2 mb-6">
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-500 h-2 rounded-full" style="width: 25%"></div>
                        </div>

                        <a href="{{ route('student.courses.show', $course) }}" class="block w-full text-center py-3 bg-indigo-50 text-indigo-600 font-bold rounded-xl hover:bg-indigo-600 hover:text-white transition-colors shadow-sm">
                            Masuk Kelas
                        </a>
                    </div>
                </div>
            @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-3 py-12 text-center bg-white rounded-2xl border border-gray-100 border-dashed">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    <h3 class="text-lg font-bold text-gray-800">Tidak ada Kelas Tersedia</h3>
                    <p class="text-gray-500 mt-1">Anda belum didaftarkan ke mata pelajaran apapun.</p>
                </div>
            @endforelse
        </div>
        
        @if($courses->hasPages())
            <div class="mt-8">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
