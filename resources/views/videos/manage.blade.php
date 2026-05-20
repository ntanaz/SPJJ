<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('student.courses.show', $video->learningActivity->module->course_id) }}" class="p-2 bg-white rounded-full text-gray-500 hover:text-indigo-650 hover:bg-indigo-50 transition-colors shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
                        Kelola Video Interaktif: {{ $video->learningActivity->title }}
                    </h2>
                    <p class="text-sm text-gray-500">
                        {{ $video->learningActivity->module->course->name }} &bull; {{ $video->learningActivity->module->title }}
                    </p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4" x-data="videoManagement()">
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-250 text-emerald-800 rounded-2xl flex items-center shadow-sm">
                <svg class="w-6 h-6 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-250 text-red-800 rounded-2xl flex items-center shadow-sm">
                <svg class="w-6 h-6 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <span class="font-bold text-sm">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- LEFT PANEL: Video Upload & Player Preview -->
            <div class="lg:col-span-2 space-y-6">
                <!-- Video Player Card -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 space-y-4">
                    <h3 class="font-black text-gray-800 text-lg">Pemutar Video & Penjelajah Timestamp</h3>
                    <p class="text-xs text-gray-400">Putar video di bawah ini. Anda dapat menjeda video pada menit tertentu untuk menyalin posisi detiknya secara langsung ke formulir kuis.</p>

                    @if($video->video_path)
                        <div class="relative rounded-2xl overflow-hidden bg-black aspect-video shadow-inner">
                            <video id="preview-player" controls class="w-full h-full">
                                <source src="{{ route('videos.stream', $video->id) }}" type="video/mp4">
                            </video>
                        </div>
                        <div class="flex justify-between items-center bg-gray-50 p-4 rounded-2xl">
                            <div>
                                <span class="text-xs text-gray-450 block font-bold">Waktu Saat Ini (Detik)</span>
                                <strong class="text-xl text-gray-800" x-text="currentTimeFormatted">00:00</strong>
                                <span class="text-xs text-indigo-600 font-bold block" x-text="'(' + currentTime + ' detik)'"></span>
                            </div>
                            <button type="button" @click="captureTimestamp()" class="px-4 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-xl text-xs font-bold transition-all shadow-sm active:scale-95">
                                Use Current Position
                            </button>
                        </div>
                    @else
                        <div class="flex flex-col items-center justify-center border-2 border-dashed border-gray-200 rounded-3xl p-12 bg-gray-50 text-center">
                            <svg class="w-16 h-16 text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12" /></svg>
                            <h4 class="font-extrabold text-gray-700 mb-1">Video Belum Diunggah</h4>
                            <p class="text-xs text-gray-400 max-w-sm mb-6">Unggah file pembelajaran video format .mp4 lokal terlebih dahulu agar Anda dapat mengonfigurasi pertanyaan kuis timestamp.</p>
                        </div>
                    @endif
                </div>

                <!-- Video File Upload Card -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 space-y-4">
                    <h3 class="font-black text-gray-800 text-lg">Unggah Video Baru (.mp4)</h3>
                    <form action="{{ route('videos.upload_file', $video->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4" @submit="isUploading = true">
                        @csrf
                        <div class="flex items-center gap-4">
                            <div class="flex-1">
                                <input type="file" name="video_file" accept="video/mp4" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-black file:bg-indigo-50 file:text-indigo-755 file:transition-colors hover:file:bg-indigo-150" required>
                                <span class="text-[10px] text-gray-400 mt-1 block">Ukuran file maksimal: 100 MB. Hanya menerima file berformat .mp4</span>
                            </div>
                            <button type="submit" class="px-5 py-2.5 bg-gray-800 hover:bg-gray-900 text-white rounded-xl text-xs font-bold transition-all flex items-center gap-2 active:scale-95" :disabled="isUploading">
                                <span x-show="!isUploading">Unggah File</span>
                                <span x-show="isUploading" style="display:none;" class="flex items-center gap-1">
                                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    Uploading...
                                </span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- RIGHT PANEL: Quiz Manager Form & List -->
            <div class="space-y-6">
                <!-- Add Quiz Card -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 space-y-4">
                    <h3 class="font-black text-gray-800 text-lg" x-text="editQuizId ? 'Sunting Pertanyaan Kuis' : 'Tambah Pertanyaan Baru'">Tambah Pertanyaan Baru</h3>
                    
                    <form :action="editQuizId ? `/video-quizzes/${editQuizId}` : '{{ route('videos.quizzes.store', $video->id) }}'" method="POST" class="space-y-4">
                        @csrf
                        <template x-if="editQuizId">
                            <input type="hidden" name="_method" value="PUT">
                        </template>

                        <!-- Question Input -->
                        <div class="space-y-1">
                            <label class="block text-xs font-extrabold text-gray-400 uppercase tracking-wider">Pertanyaan</label>
                            <input type="text" name="question" x-model="formData.question" class="w-full text-sm font-semibold rounded-xl border-gray-200 focus:border-indigo-500 focus:ring focus:ring-indigo-150 transition-all" placeholder="Tuliskan pertanyaan kuis..." required>
                        </div>

                        <!-- Timestamp Input -->
                        <div class="grid grid-cols-2 gap-4">
                            <div class="space-y-1">
                                <label class="block text-xs font-extrabold text-gray-400 uppercase tracking-wider">Timestamp (Detik)</label>
                                <input type="number" name="timestamp_seconds" min="0" x-model="formData.timestamp_seconds" class="w-full text-sm font-semibold rounded-xl border-gray-200 focus:border-indigo-500 focus:ring focus:ring-indigo-150" placeholder="Contoh: 45" required>
                            </div>
                            <div class="space-y-1">
                                <label class="block text-xs font-extrabold text-gray-400 uppercase tracking-wider">Tipe Soal</label>
                                <select name="question_type" x-model="formData.question_type" class="w-full text-sm font-semibold rounded-xl border-gray-200 focus:border-indigo-500 focus:ring focus:ring-indigo-150">
                                    <option value="multiple_choice">Pilihan Ganda</option>
                                    <option value="true_false">Benar / Salah</option>
                                    <option value="short_answer">Jawaban Singkat</option>
                                </select>
                            </div>
                        </div>

                        <!-- Dynamic Options Helper based on type -->
                        <div class="space-y-4 border-t border-gray-50 pt-3">
                            <div x-show="formData.question_type === 'multiple_choice'">
                                <div class="space-y-2">
                                    <label class="block text-xs font-extrabold text-gray-400 uppercase tracking-wider">Pilihan Jawaban (Pisahkan Koma)</label>
                                    <textarea name="options" x-model="formData.options" rows="2" class="w-full text-xs font-medium rounded-xl border-gray-200 focus:border-indigo-500 focus:ring focus:ring-indigo-150" placeholder="Pilihan A, Pilihan B, Pilihan C, Pilihan D"></textarea>
                                    <span class="text-[10px] text-gray-400 block leading-tight">Pastikan salah satu pilihan sama persis dengan 'Jawaban Benar' di bawah.</span>
                                </div>
                            </div>

                            <div x-show="formData.question_type === 'true_false'" style="display:none;" class="p-3 bg-gray-50 rounded-xl text-xs text-gray-500 font-semibold leading-relaxed">
                                Pilihan "Benar" dan "Salah" akan dibuat otomatis secara sistematis. Tentukan 'Jawaban Benar' di bawah dengan mengetik "Benar" atau "Salah".
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-extrabold text-gray-400 uppercase tracking-wider">Jawaban Benar</label>
                                <input type="text" name="correct_answer" x-model="formData.correct_answer" class="w-full text-sm font-bold rounded-xl border-gray-200 focus:border-indigo-500 focus:ring focus:ring-indigo-150" placeholder="Masukkan teks jawaban benar..." required>
                            </div>

                            <div class="space-y-1">
                                <label class="block text-xs font-extrabold text-gray-400 uppercase tracking-wider">Feedback Koreksi (Opsional)</label>
                                <input type="text" name="feedback" x-model="formData.feedback" class="w-full text-xs font-medium rounded-xl border-gray-200 focus:border-indigo-500" placeholder="Jawaban Anda Benar! Bagus sekali...">
                            </div>
                        </div>

                        <div class="flex justify-end gap-2 pt-2">
                            <button type="button" x-show="editQuizId" @click="resetForm()" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-xs font-bold rounded-xl transition-colors">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-755 text-white text-xs font-bold rounded-xl shadow-sm transition-transform active:scale-95">
                                Simpan Kuis
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Quizzes List Card -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 space-y-4">
                    <h3 class="font-black text-gray-800 text-lg">Daftar Kuis Terkonfigurasi</h3>
                    
                    <div class="space-y-3 max-h-[400px] overflow-y-auto pr-1">
                        @forelse($video->quizzes->sortBy('timestamp_seconds') as $quiz)
                            <div class="p-4 bg-gray-50 border border-gray-100 rounded-2xl space-y-3 relative group">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <span class="inline-block px-2 py-0.5 bg-indigo-100 text-indigo-800 rounded-md text-[10px] font-black uppercase tracking-wider">
                                            Detik {{ $quiz->timestamp_seconds }}
                                        </span>
                                        <span class="ml-1 inline-block px-2 py-0.5 bg-gray-200 text-gray-600 rounded-md text-[10px] font-extrabold capitalize">
                                            {{ str_replace('_', ' ', $quiz->question_type) }}
                                        </span>
                                    </div>
                                    <div class="flex items-center gap-1.5">
                                        <button type="button" @click="editQuiz(@json($quiz))" class="text-indigo-600 hover:text-indigo-850 p-1 bg-white hover:bg-indigo-50 rounded-lg shadow-sm transition-colors">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" /></svg>
                                        </button>
                                        <form action="{{ route('videos.quizzes.destroy', $quiz->id) }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pertanyaan ini?')" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-rose-600 hover:text-rose-850 p-1 bg-white hover:bg-rose-50 rounded-lg shadow-sm transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    </div>
                                </div>
                                
                                <h4 class="font-extrabold text-sm text-gray-800 leading-snug">{{ $quiz->question }}</h4>
                                
                                <div class="text-[11px] text-gray-500 space-y-1 bg-white p-2.5 rounded-xl border border-gray-100 font-medium">
                                    <div><span class="text-gray-400 font-bold uppercase tracking-wider block text-[9px] mb-0.5">Jawaban Benar:</span> <strong class="text-indigo-850 font-extrabold">{{ $quiz->options->where('is_correct', true)->first()?->option_text ?? '-' }}</strong></div>
                                    @if($quiz->question_type === 'multiple_choice')
                                        <div class="pt-1 border-t border-gray-50 mt-1">
                                            <span class="text-gray-400 font-bold uppercase tracking-wider block text-[9px] mb-0.5">Pilihan:</span>
                                            <span>{{ $quiz->options->pluck('option_text')->implode(', ') }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8 text-gray-400">
                                <p class="text-xs font-semibold">Belum ada pertanyaan popup kuis.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function videoManagement() {
            return {
                currentTime: 0,
                currentTimeFormatted: '00:00',
                isUploading: false,
                editQuizId: null,
                formData: {
                    timestamp_seconds: 0,
                    question: '',
                    question_type: 'multiple_choice',
                    options: '',
                    correct_answer: '',
                    feedback: ''
                },

                init() {
                    const self = this;
                    const player = document.getElementById('preview-player');
                    if (player) {
                        player.addEventListener('timeupdate', () => {
                            self.currentTime = Math.floor(player.currentTime);
                            
                            // Format to MM:SS
                            const mins = Math.floor(self.currentTime / 60);
                            const secs = self.currentTime % 60;
                            self.currentTimeFormatted = 
                                String(mins).padStart(2, '0') + ':' + String(secs).padStart(2, '0');
                        });
                    }
                },

                captureTimestamp() {
                    this.formData.timestamp_seconds = this.currentTime;
                },

                editQuiz(quiz) {
                    this.editQuizId = quiz.id;
                    this.formData.timestamp_seconds = quiz.timestamp_seconds;
                    this.formData.question = quiz.question;
                    this.formData.question_type = quiz.question_type;
                    this.formData.feedback = quiz.feedback || '';
                    
                    // Options map
                    if (quiz.question_type === 'multiple_choice' && quiz.options) {
                        this.formData.options = quiz.options.map(o => o.option_text).join(', ');
                    } else {
                        this.formData.options = '';
                    }

                    // Correct answer
                    const correctOpt = quiz.options.find(o => o.is_correct);
                    this.formData.correct_answer = correctOpt ? correctOpt.option_text : '';
                },

                resetForm() {
                    this.editQuizId = null;
                    this.formData = {
                        timestamp_seconds: 0,
                        question: '',
                        question_type: 'multiple_choice',
                        options: '',
                        correct_answer: '',
                        feedback: ''
                    };
                }
            }
        }
    </script>
</x-app-layout>
