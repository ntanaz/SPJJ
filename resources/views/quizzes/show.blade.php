<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('quizzes.index') }}" class="p-2 bg-white rounded-full text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
                Pengelolaan Kuis: {{ $quiz->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8">
        <!-- Notifikasi -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl flex items-center shadow-sm">
                <svg class="w-6 h-6 mr-3 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Kolom Kiri: Form Add Soal -->
            <div class="lg:col-span-1">
                <div class="bg-white p-6 rounded-3xl shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                        Tambah Soal Baru
                    </h3>

                    <form action="{{ route('quizzes.questions.store', $quiz) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Pertanyaan</label>
                            <textarea name="question" rows="3" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm font-medium" required placeholder="Ketik soal disini..."></textarea>
                        </div>

                        <div class="space-y-3 pt-2 border-t border-gray-100">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi A</label>
                                <input type="text" name="option_a" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi B</label>
                                <input type="text" name="option_b" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi C</label>
                                <input type="text" name="option_c" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm" required>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi D</label>
                                <input type="text" name="option_d" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm" required>
                            </div>
                        </div>

                        <div class="pt-2 border-t border-gray-100">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jawaban Benar</label>
                            <div class="flex gap-4">
                                @foreach(['A', 'B', 'C', 'D'] as $opt)
                                    <label class="flex items-center gap-2 cursor-pointer p-2 border border-gray-200 rounded-lg hover:bg-indigo-50 hover:border-indigo-200 transition-colors">
                                        <input type="radio" name="correct_answer" value="{{ $opt }}" class="text-indigo-600 focus:ring-indigo-500" required>
                                        <span class="font-bold text-sm text-gray-700">{{ $opt }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>

                        <div class="pt-4">
                            <button type="submit" class="w-full py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-sm transition-transform active:scale-95 flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                Simpan Soal
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Kolom Kanan: Daftar Soal -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-indigo-600 rounded-3xl p-6 text-white shadow-lg relative overflow-hidden flex items-center justify-between">
                    <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
                    <div class="relative z-10">
                        <span class="text-indigo-200 font-bold uppercase tracking-widest text-xs">Total Soal</span>
                        <h3 class="text-4xl font-black mt-1">{{ $quiz->questions->count() }} <span class="text-lg font-medium text-indigo-200">Butir</span></h3>
                    </div>
                    <div class="relative z-10 text-right">
                        <p class="text-sm text-indigo-100 font-medium">Beban Skor Maksimal:</p>
                        <p class="text-2xl font-black">{{ $quiz->questions->count() * 10 }} Poin</p> <!-- Misal 10 poin per soal -->
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse($quiz->questions as $index => $question)
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative group">
                            <div class="absolute top-6 right-6 opacity-0 group-hover:opacity-100 transition-opacity">
                                <form action="{{ route('quizzes.questions.destroy', $question) }}" method="POST" onsubmit="return confirm('Hapus soal ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors" title="Hapus Soal">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            </div>

                            <div class="flex gap-4">
                                <div class="h-10 w-10 flex-shrink-0 bg-indigo-100 text-indigo-700 rounded-xl font-black flex items-center justify-center text-lg">
                                    {{ $index + 1 }}
                                </div>
                                <div class="flex-1 w-full">
                                    <p class="font-bold text-gray-800 text-lg mb-4">{{ $question->question }}</p>
                                    
                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                                        @foreach($question->options as $key => $text)
                                            <div class="flex items-center gap-3 p-3 rounded-xl border {{ $question->correct_answer === $key ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-200' : 'border-gray-200 bg-white' }}">
                                                <span class="w-6 h-6 flex items-center justify-center rounded-md text-xs font-bold {{ $question->correct_answer === $key ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-500' }}">
                                                    {{ $key }}
                                                </span>
                                                <span class="text-sm font-medium {{ $question->correct_answer === $key ? 'text-emerald-800' : 'text-gray-600' }}">
                                                    {{ $text }}
                                                </span>
                                                @if($question->correct_answer === $key)
                                                    <svg class="w-5 h-5 text-emerald-500 ml-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                @endif
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-8 text-center bg-gray-50 border-2 border-dashed border-gray-200 rounded-3xl">
                            <div class="w-16 h-16 bg-white rounded-2xl flex items-center justify-center text-gray-400 mx-auto mb-4 shadow-sm border border-gray-100">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 mb-1">Belum Ada Soal</h4>
                            <p class="text-sm text-gray-500 font-medium">Kuis ini belum memiliki soal. Silakan tambahkan soal melalui form di sebelah kiri.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
