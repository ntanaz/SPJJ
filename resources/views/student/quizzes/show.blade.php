<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.courses.show', $quiz->course) }}" class="p-2 bg-white rounded-full text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
                {{ $quiz->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white rounded-3xl overflow-hidden shadow-xl border border-gray-100">
            <!-- Hero / Header -->
            <div class="bg-gradient-to-r from-indigo-600 to-purple-600 p-10 text-center relative text-white">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
                <div class="relative z-10">
                    <div class="w-20 h-20 bg-white/20 rounded-full flex items-center justify-center mx-auto mb-6 backdrop-blur-md shadow-inner border border-white/30">
                        <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    </div>
                    <h1 class="text-4xl font-black mb-2">{{ $quiz->title }}</h1>
                    <p class="text-indigo-100 text-lg font-medium">{{ $quiz->course->name }}</p>
                </div>
            </div>

            <div class="p-10">
                <div class="prose max-w-none text-gray-600 mb-10 text-lg font-medium">
                    {{ $quiz->description }}
                </div>

                <div class="bg-indigo-50/50 rounded-2xl p-6 border border-indigo-100 mb-10 flex items-center justify-between shadow-sm">
                    <div class="flex flex-col">
                        <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1">Total Soal</span>
                        <span class="text-3xl font-black text-indigo-800">{{ $quiz->questions->count() }} <span class="text-sm font-bold text-indigo-700">Pertanyaan</span></span>
                    </div>
                    <div class="h-12 w-px bg-indigo-200"></div>
                    <div class="flex flex-col text-right">
                        <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest mb-1">Sistem Penilaian</span>
                        <span class="text-xl font-black text-indigo-800">{{ $quiz->questions->count() * 10 }} Poin <span class="text-sm font-bold text-indigo-700">Maksimal</span></span>
                    </div>
                </div>

                @if(auth()->user()->hasRole(['guru', 'teacher', 'admin']))
                    <div class="mb-8 p-4 bg-amber-50 border border-amber-200 text-amber-800 rounded-2xl flex items-center gap-3 shadow-sm">
                        <div class="h-10 w-10 bg-amber-100 rounded-xl flex items-center justify-center text-amber-600 flex-shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                        </div>
                        <div>
                            <h4 class="text-sm font-black text-amber-900">Mode Pratinjau Kuis (Guru / Admin)</h4>
                            <p class="text-xs text-amber-700 font-medium">Anda dapat mengerjakan kuis ini sebagai uji coba pratinjau. Jawaban dan skor Anda tidak akan memengaruhi statistik siswa kelas.</p>
                        </div>
                    </div>
                @endif

                @if($attempt && $attempt->status === 'completed')
                    <div class="text-center p-8 bg-gray-50 border-2 border-dashed border-gray-200 rounded-3xl">
                        <p class="text-gray-500 mb-2 text-lg font-bold">Anda sudah menyelesaikan Kuis ini.</p>
                        <p class="text-4xl hover:scale-110 transition-transform font-black text-emerald-600">Skor: {{ $attempt->score }}</p>
                        <a href="{{ route('student.quizzes.result', $attempt) }}" class="inline-block mt-6 px-8 py-3 bg-white text-emerald-600 border border-emerald-200 rounded-xl font-bold shadow-sm hover:bg-emerald-50 transition-colors">Lihat Hasil Detail</a>
                    </div>
                @else
                    <div class="text-center">
                        <form method="POST" action="{{ route('student.quizzes.start', $quiz) }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center justify-center gap-3 px-12 py-5 bg-gradient-to-r from-indigo-600 to-purple-600 hover:from-indigo-700 hover:to-purple-700 active:scale-95 transition-all text-white font-black text-xl rounded-2xl shadow-xl shadow-indigo-500/30 w-full sm:w-auto">
                                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" /></svg>
                                {{ auth()->user()->hasRole(['guru', 'teacher', 'admin']) ? 'Mulai Pratinjau Kuis' : ($attempt && $attempt->status === 'in_progress' ? 'Lanjutkan Kuis' : 'Mulai Kerjakan Sekarang') }}
                            </button>
                        </form>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
