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

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8" x-data="{ questionType: 'multiple_choice', videoQType: 'multiple_choice' }">
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

                        <!-- Dropdown Jenis Soal -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jenis Pertanyaan</label>
                            <select name="question_type" x-model="questionType" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm font-bold">
                                <option value="multiple_choice">Pilihan Ganda (Multiple Choice)</option>
                                <option value="true_false">Benar / Salah (True / False)</option>
                                <option value="short_answer">Jawaban Singkat (Short Answer)</option>
                                <option value="fill_blank">Isian Rumpang (Fill in the Blank Coding)</option>
                                <option value="reflection">Refleksi Mandiri (Reflection)</option>
                                <option value="debugging">Uji Debugging (Debugging Quiz)</option>
                                <option value="interactive_video">Video Pembelajaran Interaktif (Video Popup Quiz)</option>
                            </select>
                        </div>

                        <!-- Pertanyaan -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Pertanyaan / Instruksi Soal</label>
                            <textarea name="question" rows="3" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm font-medium" required placeholder="Ketik soal atau instruksi disini..."></textarea>
                        </div>

                        <!-- Input Spesifik per Jenis Soal -->

                        <!-- 1. MULTIPLE CHOICE -->
                        <div x-show="questionType === 'multiple_choice'" class="space-y-3 pt-2 border-t border-gray-100">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi A</label>
                                <input type="text" name="option_a" x-bind:required="questionType === 'multiple_choice'" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi B</label>
                                <input type="text" name="option_b" x-bind:required="questionType === 'multiple_choice'" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi C</label>
                                <input type="text" name="option_c" x-bind:required="questionType === 'multiple_choice'" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi D</label>
                                <input type="text" name="option_d" x-bind:required="questionType === 'multiple_choice'" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">
                            </div>

                            <div class="pt-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Jawaban Benar</label>
                                <div class="flex gap-4">
                                    @foreach(['A', 'B', 'C', 'D'] as $opt)
                                        <label class="flex items-center gap-2 cursor-pointer p-2 border border-gray-200 rounded-lg hover:bg-indigo-50 hover:border-indigo-200 transition-colors">
                                            <input type="radio" name="correct_answer" value="{{ $opt }}" x-bind:required="questionType === 'multiple_choice'" class="text-indigo-600 focus:ring-indigo-500">
                                            <span class="font-bold text-sm text-gray-700">{{ $opt }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- 2. TRUE OR FALSE -->
                        <div x-show="questionType === 'true_false'" class="pt-2 border-t border-gray-100">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Pernyataan Benar atau Salah?</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer p-3 border border-gray-200 rounded-xl hover:bg-indigo-50 hover:border-indigo-200 transition-colors">
                                    <input type="radio" name="correct_answer" value="A" x-bind:required="questionType === 'true_false'" class="text-indigo-600 focus:ring-indigo-500">
                                    <span class="font-bold text-sm text-gray-700">Benar</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 border border-gray-200 rounded-xl hover:bg-indigo-50 hover:border-indigo-200 transition-colors">
                                    <input type="radio" name="correct_answer" value="B" x-bind:required="questionType === 'true_false'" class="text-indigo-600 focus:ring-indigo-500">
                                    <span class="font-bold text-sm text-gray-700">Salah</span>
                                </label>
                            </div>
                        </div>

                        <!-- 3. SHORT ANSWER -->
                        <div x-show="questionType === 'short_answer'" class="pt-2 border-t border-gray-100">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kunci Jawaban Singkat</label>
                            <input type="text" name="correct_answer" placeholder="Masukkan jawaban yang benar..." x-bind:required="questionType === 'short_answer'" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">
                            <p class="text-xs text-gray-500 mt-1">Jawaban siswa akan dicocokkan secara case-insensitive.</p>
                        </div>

                        <!-- 4. FILL IN THE BLANK (CODING) -->
                        <div x-show="questionType === 'fill_blank'" class="pt-2 border-t border-gray-100">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kunci Jawaban Rumpang (Isian)</label>
                            <input type="text" name="correct_answer" placeholder="Cth: print" x-bind:required="questionType === 'fill_blank'" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">
                            <p class="text-xs text-gray-500 mt-1">Gunakan kata yang tepat untuk mengisi kekosongan kode di atas.</p>
                        </div>

                        <!-- 5. REFLECTION (No correct answer required) -->

                        <!-- 6. DEBUGGING -->
                        <div x-show="questionType === 'debugging'" class="pt-2 border-t border-gray-100">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kunci Solusi Kode Benar</label>
                            <textarea name="correct_answer" rows="4" placeholder="Ketik kode solusi yang benar di sini..." x-bind:required="questionType === 'debugging'" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm font-mono"></textarea>
                            <p class="text-xs text-gray-500 mt-1">Jawaban kode siswa akan dicocokkan dengan mengabaikan whitespace.</p>
                        </div>

                        <!-- 7. INTERACTIVE VIDEO -->
                        <div x-show="questionType === 'interactive_video'" class="space-y-4 pt-2 border-t border-gray-100">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">URL Video Pembelajaran</label>
                                <input type="text" name="video_url" placeholder="Cth: /videos/html_intro.mp4 atau link Youtube" x-bind:required="questionType === 'interactive_video'" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Timestamp Muncul (detik)</label>
                                <input type="number" name="timestamp" min="0" placeholder="Cth: 45" x-bind:required="questionType === 'interactive_video'" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tipe Soal Video</label>
                                <select name="video_question_type" x-model="videoQType" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">
                                    <option value="multiple_choice">Pilihan Ganda (MC)</option>
                                    <option value="true_false">Benar / Salah (TF)</option>
                                    <option value="short_answer">Jawaban Teks Singkat</option>
                                </select>
                            </div>

                            <!-- Opsi Sub-Tipe Video MC -->
                            <div x-show="videoQType === 'multiple_choice'" class="space-y-3 pl-4 border-l-2 border-indigo-100">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi A</label>
                                    <input type="text" name="option_a" x-bind:required="questionType === 'interactive_video' && videoQType === 'multiple_choice'" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi B</label>
                                    <input type="text" name="option_b" x-bind:required="questionType === 'interactive_video' && videoQType === 'multiple_choice'" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi C</label>
                                    <input type="text" name="option_c" x-bind:required="questionType === 'interactive_video' && videoQType === 'multiple_choice'" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi D</label>
                                    <input type="text" name="option_d" x-bind:required="questionType === 'interactive_video' && videoQType === 'multiple_choice'" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Jawaban Benar</label>
                                    <div class="flex gap-4">
                                        @foreach(['A', 'B', 'C', 'D'] as $opt)
                                            <label class="flex items-center gap-2 cursor-pointer p-2 border border-gray-200 rounded-lg">
                                                <input type="radio" name="correct_answer" value="{{ $opt }}" x-bind:required="questionType === 'interactive_video' && videoQType === 'multiple_choice'" class="text-indigo-600 focus:ring-indigo-500">
                                                <span class="font-bold text-sm text-gray-700">{{ $opt }}</span>
                                            </label>
                                        @endforeach
                                    </div>
                                </div>
                            </div>

                            <!-- Opsi Sub-Tipe Video TF -->
                            <div x-show="videoQType === 'true_false'" class="space-y-3 pl-4 border-l-2 border-indigo-100">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Pernyataan Benar/Salah?</label>
                                <div class="flex gap-4">
                                    <label class="flex items-center gap-2 cursor-pointer p-3 border border-gray-200 rounded-xl">
                                        <input type="radio" name="correct_answer" value="A" x-bind:required="questionType === 'interactive_video' && videoQType === 'true_false'" class="text-indigo-600 focus:ring-indigo-500">
                                        <span class="font-bold text-sm text-gray-700">Benar</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer p-3 border border-gray-200 rounded-xl">
                                        <input type="radio" name="correct_answer" value="B" x-bind:required="questionType === 'interactive_video' && videoQType === 'true_false'" class="text-indigo-600 focus:ring-indigo-500">
                                        <span class="font-bold text-sm text-gray-700">Salah</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Opsi Sub-Tipe Video Short Answer -->
                            <div x-show="videoQType === 'short_answer'" class="space-y-3 pl-4 border-l-2 border-indigo-100">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Kunci Jawaban Singkat Video</label>
                                <input type="text" name="correct_answer" placeholder="Masukkan jawaban video..." x-bind:required="questionType === 'interactive_video' && videoQType === 'short_answer'" class="w-full rounded-xl border-gray-300 shadow-sm text-sm">
                            </div>
                        </div>

                        <!-- Feedback Pembahasan -->
                        <div class="pt-2 border-t border-gray-100">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Pembahasan / Feedback Jawaban (Opsional)</label>
                            <textarea name="feedback" rows="2" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm" placeholder="Tulis penjelasan jawaban di sini..."></textarea>
                        </div>

                        <!-- Poin Soal -->
                        <div class="pt-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Beban Nilai / Poin</label>
                            <input type="number" name="points" min="1" value="10" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm font-bold">
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
                        <p class="text-2xl font-black">{{ $quiz->questions->sum('points') }} Poin</p>
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
                                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 uppercase">
                                            {{ str_replace('_', ' ', $question->question_type) }}
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-emerald-50 text-emerald-700">
                                            {{ $question->points }} Poin
                                        </span>
                                    </div>
                                    
                                    <p class="font-bold text-gray-800 text-lg mb-4">{{ $question->question }}</p>

                                    <!-- Render MC / TF options -->
                                    @if(in_array($question->question_type, ['multiple_choice', 'true_false']))
                                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                                            @foreach($question->options as $key => $text)
                                                <div class="flex items-center gap-3 p-3 rounded-xl border {{ $question->correct_answer === $key ? 'border-emerald-500 bg-emerald-50 ring-2 ring-emerald-200' : 'border-gray-200 bg-white' }}">
                                                    <span class="w-6 h-6 flex items-center justify-center rounded-md text-xs font-bold {{ $question->correct_answer === $key ? 'bg-emerald-500 text-white' : 'bg-gray-100 text-gray-500' }}">
                                                        {{ $key }}
                                                    </span>
                                                    <span class="text-sm font-medium {{ $question->correct_answer === $key ? 'text-emerald-800' : 'text-gray-600' }}">
                                                        {{ $text }}
                                                    </span>
                                                </div>
                                            @endforeach
                                        </div>
                                    @endif

                                    <!-- Render Short Answer / Fill Blank keys -->
                                    @if(in_array($question->question_type, ['short_answer', 'fill_blank']))
                                        <div class="p-3 bg-gray-50 rounded-xl border border-gray-150 text-sm font-semibold text-gray-700 mb-4">
                                            Kunci Jawaban Benar: <span class="text-indigo-600 font-mono">{{ $question->correct_answer }}</span>
                                        </div>
                                    @endif

                                    <!-- Render Debugging solutions -->
                                    @if($question->question_type === 'debugging')
                                        <div class="space-y-2 mb-4">
                                            <p class="text-xs font-bold text-gray-400 uppercase">Kunci Solusi Benar:</p>
                                            <pre class="p-3 bg-gray-900 text-green-400 rounded-xl font-mono text-xs overflow-x-auto">{{ $question->correct_answer }}</pre>
                                        </div>
                                    @endif

                                    <!-- Render Reflection -->
                                    @if($question->question_type === 'reflection')
                                        <div class="p-3 bg-blue-50 rounded-xl border border-blue-150 text-sm font-medium text-blue-800 mb-4">
                                            ℹ️ Pertanyaan reflektif. Siswa dapat mengutarakan pendapat bebas mereka.
                                        </div>
                                    @endif

                                    <!-- Render Interactive Video parameters -->
                                    @if($question->question_type === 'interactive_video')
                                        <div class="p-4 bg-purple-50 border border-purple-100 rounded-xl text-sm mb-4 space-y-2">
                                            <p class="font-bold text-purple-900">Pop-up Video Detail:</p>
                                            <p class="text-xs text-purple-700">URL: <span class="font-mono">{{ $question->options['video_url'] ?? '' }}</span></p>
                                            <p class="text-xs text-purple-700">Timestamp: <span class="font-bold">{{ $question->options['timestamp'] ?? 0 }} detik</span></p>
                                            <p class="text-xs text-purple-700">Tipe Popup: <span class="font-bold uppercase">{{ $question->options['video_question_type'] ?? '' }}</span></p>
                                            <p class="text-xs text-purple-700">Kunci Jawaban: <span class="font-mono bg-white px-2 py-0.5 rounded">{{ $question->correct_answer }}</span></p>
                                        </div>
                                    @endif

                                    @if($question->feedback)
                                        <div class="p-3 bg-yellow-50/55 rounded-xl border border-yellow-100 text-xs text-yellow-800">
                                            <strong>Pembahasan:</strong> {{ $question->feedback }}
                                        </div>
                                    @endif
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
