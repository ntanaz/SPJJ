<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('student.courses.show', $activity->module->course) }}" class="p-2 bg-white rounded-full text-gray-500 hover:text-indigo-650 hover:bg-indigo-50 transition-colors shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
                        {{ $activity->title }}
                    </h2>
                    <p class="text-sm text-gray-500">{{ $activity->module->course->name }} &bull; {{ $activity->module->title }}</p>
                </div>
            </div>
            
            <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 font-bold rounded-xl border border-indigo-100 text-xs">
                <span>Aktivitas: {{ ucfirst(str_replace('_', ' ', $activity->activity_type)) }}</span>
                @if($activity->is_required)
                    <span class="ml-2 px-1.5 py-0.5 bg-red-100 text-red-700 rounded text-[9px] font-extrabold uppercase">Wajib</span>
                @endif
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4">
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

        <div class="grid grid-cols-1 lg:grid-cols-4 gap-8">
            <!-- LEFT PANEL: MAIN ACTIVITY PLAYER -->
            <div class="lg:col-span-3 space-y-6">
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 min-h-[500px] flex flex-col justify-between"
                     @if($activity->activity_type === 'video') x-data="videoHandler()" @endif>
                    <div>
                        <!-- ACTIVITY CONTENT RENDERER -->
                        @if($activity->activity_type === 'mind_map')
                            <!-- Step 1: Mind Map -->
                            <div class="space-y-6">
                                <div class="border-b border-gray-100 pb-4 text-left">
                                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Langkah DL: Stimulation & Problem Statement</span>
                                    <h3 class="text-2xl font-black text-gray-800 mt-1">Peta Pikiran (Mind Map)</h3>
                                    <p class="text-gray-500 text-sm mt-1">Pahami peta pikiran di bawah ini untuk merangsang pemikiran kritis Anda mengenai konsep materi.</p>
                                </div>
                                <div class="bg-gray-50 rounded-3xl p-4 flex items-center justify-center border border-gray-150 overflow-hidden">
                                    @if($material->mind_map_path)
                                        <img src="{{ asset('storage/' . $material->mind_map_path) }}" alt="Mind Map" class="max-h-[500px] object-contain rounded-2xl shadow-sm hover:scale-105 transition-transform duration-350 cursor-zoom-in">
                                    @else
                                        <div class="text-center py-12 text-gray-400">
                                            <svg class="w-16 h-16 mx-auto mb-2 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" /></svg>
                                            <p class="font-bold">Gambar peta pikiran tidak tersedia.</p>
                                        </div>
                                    @endif
                                </div>
                            </div>

                        @elseif($activity->activity_type === 'material')
                            <!-- Step 2: Modul -->
                            <div class="space-y-6 text-left">
                                <div class="border-b border-gray-100 pb-4">
                                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Langkah DL: Data Collection (Pengumpulan Data)</span>
                                    <h3 class="text-2xl font-black text-gray-800 mt-1">Modul Pembelajaran Utama</h3>
                                    <p class="text-gray-500 text-sm mt-1">Bacalah artikel materi di bawah ini dengan saksama.</p>
                                </div>
                                <div class="bg-gray-50 rounded-3xl p-6 border border-gray-100 leading-relaxed text-gray-700">
                                    @if($material->text_content)
                                        <div class="prose max-w-none text-gray-700 whitespace-pre-wrap">{!! nl2br(e($material->text_content)) !!}</div>
                                    @else
                                        <p class="text-gray-400 italic">Isi teks modul belum ditambahkan.</p>
                                    @endif
                                </div>
                                @if($material->file_path)
                                    @php
                                        $extension = strtolower(pathinfo($material->file_path, PATHINFO_EXTENSION));
                                        $isImage = in_array($extension, ['jpg', 'jpeg', 'png', 'gif', 'webp']);
                                        $isPdf = $extension === 'pdf';
                                    @endphp

                                    @if($isImage)
                                        <div class="mt-4 p-4 bg-white border border-gray-150 rounded-2xl shadow-sm">
                                            <span class="block text-xs font-bold text-gray-400 uppercase mb-2">Pratinjau Gambar:</span>
                                            <img src="{{ asset('storage/' . $material->file_path) }}" alt="Pratinjau Materi" class="max-w-full h-auto rounded-xl max-h-96 object-contain mx-auto shadow-sm">
                                        </div>
                                    @elseif($isPdf)
                                        <div class="mt-4 p-4 bg-white border border-gray-150 rounded-2xl shadow-sm">
                                            <span class="block text-xs font-bold text-gray-400 uppercase mb-2">Pratinjau PDF:</span>
                                            <iframe src="{{ asset('storage/' . $material->file_path) }}" class="w-full h-[500px] rounded-xl border border-gray-200" allowfullscreen></iframe>
                                        </div>
                                    @endif

                                    <div class="mt-4 bg-white border border-gray-150 p-5 rounded-2xl flex items-center justify-between">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 bg-indigo-50 text-indigo-600 flex items-center justify-center rounded-lg">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z" /></svg>
                                            </div>
                                            <div>
                                                <h5 class="text-sm font-bold text-gray-800">Lampiran Dokumen Tambahan</h5>
                                                <p class="text-xs text-gray-400">Silakan unduh lampiran untuk belajar mandiri.</p>
                                            </div>
                                        </div>
                                        <a href="{{ asset('storage/' . $material->file_path) }}" download class="px-4 py-2 bg-indigo-600 hover:bg-indigo-750 text-white rounded-xl text-xs font-bold transition-colors">
                                            Unduh Dokumen
                                        </a>
                                    </div>
                                @endif
                            </div>

                        @elseif($activity->activity_type === 'video')
                            <!-- Step 3: Video -->
                            <div class="space-y-6">
                                <div class="border-b border-gray-100 pb-4 text-left">
                                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Langkah DL: Data Processing (Pengolahan Data)</span>
                                    <h3 class="text-2xl font-black text-gray-800 mt-1">Video Pembelajaran & Kuis Pop-Up</h3>
                                    <p class="text-gray-500 text-sm mt-1">Tonton video interaktif berikut. Jawab setiap pertanyaan pop-up kuis yang muncul di menit tertentu.</p>
                                </div>

                                @php
                                    $videoUrl = null;
                                    if (!empty($video) && !empty($video->video_path)) {
                                        $videoUrl = route('videos.stream', $video->id);
                                    } elseif ($material) {
                                        $hasLocalVideo = $material->file_path && in_array($material->format ?? $material->type, ['video', 'video_post_class']);
                                        if ($hasLocalVideo) {
                                            $videoUrl = route('materials.stream_video', $material);
                                        }
                                    }
                                    
                                    $hasYoutube    = $material && !empty($material->youtube_url);
                                    $youtubeId     = null;
                                    if ($hasYoutube) {
                                        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $material->youtube_url, $ytMatch);
                                        $youtubeId = $ytMatch[1] ?? null;
                                    }
                                @endphp

                                <div class="relative rounded-3xl overflow-hidden bg-black aspect-video shadow-md max-w-4xl mx-auto border border-gray-850">
                                    @if($videoUrl)
                                        <video id="local-player" controls class="w-full h-full">
                                            <source src="{{ $videoUrl }}" type="video/mp4">
                                        </video>
                                    @elseif($hasYoutube && $youtubeId)
                                        <div id="yt-player" class="w-full h-full min-h-[400px]"></div>
                                    @else
                                        <div class="flex flex-col items-center justify-center h-64 text-gray-400">
                                            <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                                            <span class="font-bold">Video tidak tersedia.</span>
                                        </div>
                                    @endif
                                </div>

                                <!-- Interactive Question Modal -->
                                <div x-show="showQuizModal" style="display: none;" class="fixed inset-0 z-[100] flex items-center justify-center bg-gray-900/60 backdrop-blur-sm p-4">
                                    <div class="bg-white rounded-3xl p-8 max-w-md w-full shadow-2xl border border-gray-100 space-y-6 transform scale-100 transition-all duration-300">
                                        <div class="text-center">
                                            <span class="inline-block px-3 py-1 bg-rose-50 text-rose-600 rounded-full text-xs font-bold uppercase tracking-wider mb-2"
                                                x-text="activeQuestion.question_type === 'true_false' ? '✅ Benar / Salah' : (activeQuestion.question_type === 'short_answer' ? '✏️ Jawaban Singkat' : '🎯 Quiz Pop Up!')"></span>
                                            <h4 class="text-xl font-black text-gray-800" x-text="activeQuestion.question"></h4>
                                        </div>

                                        <div class="space-y-3" x-show="activeQuestion.question_type === 'multiple_choice' || activeQuestion.question_type === 'true_false' || !activeQuestion.question_type">
                                            <template x-for="(opt, idx) in activeQuestion.options" :key="idx">
                                                <button
                                                    @click="selectAnswer(opt)"
                                                    class="w-full text-left p-4 rounded-2xl border-2 transition-all font-bold text-sm"
                                                    :class="(selectedAnswer && selectedAnswer.id === opt.id) ? 'border-indigo-650 bg-indigo-50 text-indigo-755 text-indigo-700' : 'border-gray-100 hover:border-indigo-100 text-gray-750 hover:text-gray-900 bg-gray-50 hover:bg-white'">
                                                    <span class="mr-2" x-text="String.fromCharCode(65 + idx) + '.'"></span>
                                                    <span x-text="opt.option_text"></span>
                                                </button>
                                            </template>
                                        </div>

                                        <div x-show="activeQuestion.question_type === 'short_answer'" class="space-y-3">
                                            <input type="text" x-model="selectedAnswer" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm font-bold p-4" placeholder="Ketik jawaban Anda disini...">
                                        </div>

                                        <div x-show="questionFeedback" class="p-4 rounded-2xl border text-sm font-bold text-center"
                                            :class="isAnswerCorrect ? 'bg-emerald-50 border-emerald-250 text-emerald-800' : 'bg-rose-50 border-rose-250 text-rose-800'">
                                            <p x-text="questionFeedback"></p>
                                        </div>

                                        <div class="flex justify-end gap-3 pt-4 border-t border-gray-100">
                                            <button x-show="!questionFeedback" @click="submitAnswer()" :disabled="!selectedAnswer" class="w-full py-3.5 bg-indigo-600 hover:bg-indigo-700 disabled:bg-gray-200 disabled:text-gray-400 text-white font-bold rounded-2xl shadow-md transition-all active:scale-95 text-center text-sm">
                                                Kirim Jawaban
                                            </button>
                                            <button x-show="questionFeedback" @click="resumePlayback()" class="w-full py-3.5 bg-gray-800 hover:bg-gray-900 text-white font-bold rounded-2xl shadow-md transition-all active:scale-95 text-center text-sm">
                                                Lanjutkan Video
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>

                        @elseif($activity->activity_type === 'coding_quiz')
                            <!-- Step 4: Coding Quiz -->
                            @php
                                $attemptsCount = count($codingAttempts);
                                $isSuccess = collect($codingAttempts)->where('hasil_validasi', true)->isNotEmpty();
                                $isLocked = !$isSuccess && $attemptsCount >= 3;
                            @endphp
                            <div class="space-y-6 text-left">
                                <div class="border-b border-gray-100 pb-4">
                                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Langkah DL: Verification (Pembuktian)</span>
                                    <h3 class="text-2xl font-black text-gray-800 mt-1">Kuis Koding & Pembuktian</h3>
                                    <p class="text-gray-500 text-sm mt-1">Selesaikan kuis koding berikut untuk membuktikan pemahaman program Anda.</p>
                                </div>

                                @if($codingQuiz)
                                    <div class="bg-gray-50 rounded-3xl p-6 border border-gray-150 space-y-6">
                                        <div class="prose max-w-none text-gray-700">
                                            <h4 class="font-extrabold text-gray-850 text-base mb-2">Instruksi Soal:</h4>
                                            <p class="whitespace-pre-line text-sm leading-relaxed">{{ $codingQuiz->instruction }}</p>
                                        </div>

                                        <div class="flex items-center justify-between bg-white border border-gray-150 p-4 rounded-2xl">
                                            <div>
                                                <span class="text-xs font-bold text-gray-400 uppercase">Status Verifikasi:</span>
                                                <div class="mt-1 flex items-center gap-2">
                                                    @if($isSuccess)
                                                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-lg text-xs font-black uppercase tracking-wider">Lolos Verifikasi</span>
                                                    @elseif($isLocked)
                                                        <span class="px-2.5 py-1 bg-red-100 text-red-800 rounded-lg text-xs font-black uppercase tracking-wider">Terkunci (Kesempatan Habis)</span>
                                                    @else
                                                        <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-lg text-xs font-black uppercase tracking-wider">Sedang Dikerjakan</span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="text-right">
                                                <span class="text-xs font-bold text-gray-400 uppercase">Kesempatan Percobaan:</span>
                                                <p class="text-sm font-extrabold text-gray-700 mt-0.5">{{ $attemptsCount }} / 3 Kali</p>
                                            </div>
                                        </div>

                                        @if(!$isSuccess && !$isLocked)
                                            <form action="{{ route('student.materials.submit_coding_quiz', $material) }}" method="POST" class="space-y-4">
                                                @csrf
                                                @php
                                                    $qType = $codingQuiz->quiz_type ?? 'fill_blank';
                                                @endphp

                                                @if($qType === 'short_answer')
                                                    <div class="space-y-2">
                                                        <label class="block text-sm font-bold text-gray-750">Tulis Jawaban Singkat Anda:</label>
                                                        <textarea name="short_answer" rows="3" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors font-mono text-sm p-4" placeholder="Ketik jawaban di sini..." required></textarea>
                                                    </div>
                                                @elseif($qType === 'debugging')
                                                    <div class="space-y-2">
                                                        <label class="block text-sm font-bold text-gray-750">Perbaiki Kode di Bawah Ini:</label>
                                                        <textarea name="debug_answer" rows="8" class="w-full rounded-2xl border-gray-300 font-mono text-sm bg-gray-900 text-gray-100 p-4 focus:ring-indigo-500" required>{{ $codingQuiz->code_template }}</textarea>
                                                    </div>
                                                @else
                                                    <!-- Fill in the blank -->
                                                    <div class="p-6 bg-gray-900 text-gray-150 rounded-2xl font-mono text-sm overflow-x-auto border border-gray-800">
                                                        @php
                                                            $parts = explode('[blank]', $codingQuiz->code_template);
                                                            $blankIndex = 0;
                                                        @endphp
                                                        <div class="whitespace-pre leading-relaxed">
                                                            @foreach($parts as $partIndex => $part)
                                                                {!! e($part) !!}
                                                                @if($partIndex < count($parts) - 1)
                                                                    <input type="text" name="answers[{{ $blankIndex }}]" class="mx-1 h-7 w-28 bg-gray-800 text-white font-bold font-mono text-center rounded border-2 border-indigo-500 focus:outline-none focus:border-indigo-400 text-xs px-1.5 py-0" placeholder="[isi di sini]" required>
                                                                    @php $blankIndex++; @endphp
                                                                @endif
                                                            @endforeach
                                                        </div>
                                                    </div>
                                                @endif

                                                <div class="flex justify-end pt-2">
                                                    <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2">
                                                        Kirim &amp; Verifikasi Kode
                                                    </button>
                                                </div>
                                            </form>
                                        @endif

                                        @if(!empty($codingAttempts) && count($codingAttempts) > 0)
                                            <div class="space-y-3 pt-4 border-t border-gray-100">
                                                <h5 class="text-xs font-bold text-gray-400 uppercase tracking-widest">Riwayat Percobaan:</h5>
                                                <div class="space-y-2">
                                                    @foreach($codingAttempts as $attempt)
                                                        <div class="p-4 rounded-xl border flex items-center justify-between text-xs transition-all {{ $attempt->hasil_validasi ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800' }}">
                                                            <div>
                                                                <strong class="font-extrabold">Percobaan #{{ $attempt->percobaan_ke }}</strong>
                                                                <span class="mx-2">&bull;</span>
                                                                <span>{{ \Carbon\Carbon::parse($attempt->waktu_submit)->translatedFormat('d M Y, H:i') }} WIB</span>
                                                            </div>
                                                            <div class="flex items-center gap-2">
                                                                <span class="font-black uppercase">{{ $attempt->hasil_validasi ? 'Sukses' : 'Gagal' }}</span>
                                                                @if($attempt->feedback)
                                                                    <span class="text-gray-450 font-semibold">({{ $attempt->feedback }})</span>
                                                                @endif
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            </div>
                                        @endif
                                    </div>
                                @else
                                    <p class="text-gray-400 italic">Kuis koding tidak dikonfigurasi.</p>
                                @endif
							</div>

                        @elseif($activity->activity_type === 'reflection')
                            <!-- Step 5: Reflection -->
                            @php
                                $correctAttempt = collect($codingAttempts)->where('hasil_validasi', true)->first();
                            @endphp
                            <div class="space-y-6 text-left">
                                <div class="border-b border-gray-100 pb-4">
                                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Langkah DL: Generalization (Generalisasi)</span>
                                    <h3 class="text-2xl font-black text-gray-800 mt-1">Refleksi Koding & Analisis</h3>
                                    <p class="text-gray-500 text-sm mt-1">Tuliskan kesimpulan, analisis logika program, dan refleksi pembelajaran Anda pada bab ini.</p>
                                </div>

                                @if($isCompleted)
                                    <div class="p-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-3xl space-y-4 text-left">
                                        <div class="flex items-center gap-3">
                                            <div class="h-10 w-10 bg-emerald-500 text-white flex items-center justify-center rounded-full flex-shrink-0">
                                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                                            </div>
                                            <div>
                                                <h4 class="font-extrabold text-base">Refleksi Telah Dikirim!</h4>
                                                <p class="text-xs text-emerald-700">Aktivitas ini telah diselesaikan.</p>
                                            </div>
                                        </div>
                                        
                                        @if($correctAttempt && $correctAttempt->reflection)
                                            <div class="bg-white border border-emerald-150 p-4 rounded-2xl">
                                                <span class="text-xs font-bold text-gray-400 uppercase">Teks Refleksi Anda:</span>
                                                <p class="text-sm text-gray-700 mt-2 leading-relaxed whitespace-pre-wrap">{!! e($correctAttempt->reflection) !!}</p>
                                            </div>
                                            
                                            @if($correctAttempt->graded_at)
                                                <div class="bg-indigo-50 border border-indigo-150 p-4 rounded-2xl space-y-2">
                                                    <span class="text-xs font-bold text-indigo-750 uppercase">Penilaian Guru:</span>
                                                    <div class="grid grid-cols-3 gap-2 text-center text-xs">
                                                        <div class="bg-white p-2 rounded-xl border border-indigo-100">
                                                            <span class="text-gray-450 block mb-0.5">Correctness</span>
                                                            <strong class="text-indigo-800 text-sm font-black">{{ $correctAttempt->correctness_grade ?? '-' }}/100</strong>
                                                        </div>
                                                        <div class="bg-white p-2 rounded-xl border border-indigo-100">
                                                            <span class="text-gray-450 block mb-0.5">Refleksi</span>
                                                            <strong class="text-indigo-800 text-sm font-black">{{ $correctAttempt->reflection_grade ?? '-' }}/100</strong>
                                                        </div>
                                                        <div class="bg-white p-2 rounded-xl border border-indigo-100">
                                                            <span class="text-gray-450 block mb-0.5">Nilai Akhir</span>
                                                            <strong class="text-indigo-800 text-sm font-black">{{ $correctAttempt->final_grade ?? '-' }}/100</strong>
                                                        </div>
                                                    </div>
                                                </div>
                                            @else
                                                <div class="bg-amber-50 border border-amber-100 p-4 rounded-2xl text-xs font-semibold text-amber-800">
                                                    Menunggu penilaian dan umpan balik dari guru pengampu.
                                                </div>
                                            @endif
                                        @endif
                                    </div>
                                @else
                                    <form action="{{ route('student.materials.submit_reflection', $material) }}" method="POST" class="space-y-4">
                                        @csrf
                                        <div class="space-y-2">
                                            <label for="reflection" class="block text-sm font-bold text-gray-700">Analisis &amp; Refleksi Pembelajaran Anda:</label>
                                            <textarea name="reflection" id="reflection" rows="6" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors text-sm p-4" placeholder="Tuliskan refleksi pemahaman konsep Anda mengenai kode program atau materi ini..." required></textarea>
                                            @error('reflection')<p class="mt-1 text-xs text-red-650 font-bold">{{ $message }}</p>@enderror
                                        </div>

                                        <div class="bg-amber-50 border border-amber-100 p-4 rounded-2xl text-amber-800 text-xs font-medium">
                                            💡 Mengirimkan refleksi akan menandai materi ini sebagai lengkap dan Anda akan menerima reward XP Zenith.
                                        </div>

                                        <div class="flex justify-end pt-2">
                                            <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-750 text-white font-bold rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2">
                                                Kirim Refleksi &amp; Selesaikan Materi
                                            </button>
                                        </div>
                                    </form>
                                @endif
                            </div>
                        @endif
                    </div>

                    <!-- FOOTER ACTIONS & NAVIGATION -->
                    <div class="flex justify-between items-center pt-6 border-t border-gray-100 mt-8">
                        <div>
                            @if($activity->activity_type === 'video' && !$isCompleted)
                                @if(!empty($video))
                                    <button type="button" @click="submitVideoCompletion()" :disabled="!allQuestionsAnswered()" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-750 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed text-white font-bold rounded-xl shadow-sm transition-all flex items-center gap-2 text-sm">
                                        Selesaikan Video Kuis
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                @else
                                    <button type="button" onclick="submitVideoCompletionForm()" :disabled="!allQuestionsAnswered()" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-750 disabled:bg-gray-200 disabled:text-gray-400 disabled:cursor-not-allowed text-white font-bold rounded-xl shadow-sm transition-all flex items-center gap-2 text-sm">
                                        Selesaikan Video Kuis
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                    <form id="video-complete-form" action="{{ route('student.materials.complete_step', $material) }}" method="POST" style="display:none;">
                                        @csrf
                                        <input type="hidden" name="step" value="video">
                                    </form>
                                @endif
                            @elseif(in_array($activity->activity_type, ['mind_map', 'material']) && !$isCompleted)
                                <form action="{{ route('student.activities.complete', $activity) }}" method="POST">
                                    @csrf
                                    <button type="submit" class="px-6 py-3 bg-indigo-650 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm transition-transform active:scale-95 flex items-center gap-2 text-sm">
                                        Tandai Selesai & Lanjutkan
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </button>
                                </form>
                            @else
                                <span class="text-xs text-gray-400 font-bold">Aktivitas ini sudah selesai atau otomatis terverifikasi.</span>
                            @endif
                        </div>

                        <div>
                            @if($nextActivity)
                                @if($nextActivity->isUnlockedFor(auth()->user()))
                                    <a href="{{ route('student.activities.show', $nextActivity) }}" class="px-6 py-3 bg-gray-800 hover:bg-gray-900 text-white font-bold rounded-xl transition-all flex items-center gap-2 text-sm">
                                        Aktivitas Berikutnya
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </a>
                                @else
                                    <button disabled class="px-6 py-3 bg-gray-200 text-gray-400 font-bold rounded-xl cursor-not-allowed text-sm flex items-center gap-2">
                                        Aktivitas Berikutnya
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('student.courses.show', $activity->module->course) }}" class="px-6 py-3 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 font-bold rounded-xl transition-colors text-sm">
                                    Kembali ke Detail Kelas
                                </a>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Forum Diskusi Khusus Materi -->
                @if($activity->material_id)
                    <div class="mt-8 space-y-6 text-left">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                            Diskusi Mengenai Materi Ini
                        </h3>

                        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                            <!-- Input new discussion for material -->
                            <form action="{{ route('discussions.store_material', $material) }}" method="POST" class="mb-8">
                                @csrf
                                <div class="flex items-start gap-4">
                                    <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-650 flex items-center justify-center font-bold flex-shrink-0">
                                        {{ substr(auth()->user()->name, 0, 1) }}
                                    </div>
                                    <div class="flex-1 space-y-3">
                                        <input type="text" name="title" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors text-sm font-bold" placeholder="Judul / Topik Diskusi Materi" required>
                                        <textarea name="content" rows="3" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors text-sm" placeholder="Tanyakan sesuatu terkait materi ini..." required></textarea>
                                        <div class="mt-3 flex justify-end">
                                            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-sm transition-all flex items-center gap-2">
                                                Kirim Pertanyaan Diskusi
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </form>

                            <div class="space-y-4">
                                @forelse($material->discussions as $disc)
                                    <div class="p-5 rounded-2xl border border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-colors flex items-start gap-4 relative group text-left">
                                        <div class="h-10 w-10 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold flex-shrink-0">
                                            {{ substr($disc->user->name, 0, 1) }}
                                        </div>
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 flex-wrap mb-1">
                                                <span class="text-sm font-bold text-gray-800">{{ $disc->user->name }}</span>
                                                <span class="text-xs text-gray-400 font-medium">{{ $disc->created_at->diffForHumans() }}</span>
                                            </div>
                                            <h4 class="font-extrabold text-gray-900 text-base mb-1 hover:text-indigo-650">
                                                <a href="{{ route('discussions.show', $disc) }}">{{ $disc->title }}</a>
                                            </h4>
                                            <p class="text-sm text-gray-600 line-clamp-2 leading-relaxed mb-3">{{ $disc->content }}</p>
                                            
                                            <div class="flex items-center gap-4">
                                                <a href="{{ route('discussions.show', $disc) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-850">
                                                    {{ $disc->replies->count() }} Komentar
                                                </a>
                                            </div>
                                        </div>
                                    </div>
                                @empty
                                    <div class="text-center py-6 text-gray-400">
                                        <p class="text-sm font-semibold">Belum ada diskusi untuk materi ini.</p>
                                    </div>
                                @endforelse
                            </div>
                        </div>
                    </div>
                @endif
            </div>

            <!-- RIGHT PANEL: SIDEBAR COURSERA PLAYER LIST -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 space-y-6 text-left">
                    <div>
                        <h4 class="font-black text-gray-800 text-base mb-1">Bab / Modul Pembelajaran</h4>
                        <p class="text-xs text-gray-400 font-bold uppercase tracking-wider">{{ $activity->module->title }}</p>
                    </div>

                    <div class="space-y-3">
                        @foreach($activity->module->activities as $act)
                            @php
                                $actCompleted = $act->progress->where('user_id', auth()->id())->first()?->is_completed ?? false;
                                $actUnlocked = $act->isUnlockedFor(auth()->user());
                                $isCurrent = $act->id === $activity->id;
                            @endphp

                            @if(!$actUnlocked)
                                <div class="flex items-start gap-3 p-3 rounded-2xl border border-gray-50 bg-gray-50/50 opacity-60">
                                    <div class="h-8 w-8 rounded-lg bg-gray-200 text-gray-400 flex items-center justify-center flex-shrink-0">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-xs font-bold text-gray-400 block truncate">{{ $act->title }}</span>
                                        <span class="text-[9px] text-gray-400 font-extrabold uppercase">Terkunci</span>
                                    </div>
                                </div>
                            @else
                                <a href="{{ route('student.activities.show', $act) }}" 
                                   class="flex items-start gap-3 p-3 rounded-2xl border {{ $isCurrent ? 'bg-indigo-50 border-indigo-200 text-indigo-850' : 'bg-white border-gray-100 hover:border-indigo-100 text-gray-700' }} transition-all">
                                    <div class="h-8 w-8 rounded-lg {{ $actCompleted ? 'bg-emerald-500 text-white' : ($isCurrent ? 'bg-indigo-600 text-white' : 'bg-gray-100 text-gray-500') }} flex items-center justify-center flex-shrink-0">
                                        @if($actCompleted)
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                        @else
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                        @endif
                                    </div>
                                    <div class="min-w-0">
                                        <span class="text-xs font-bold block truncate {{ $isCurrent ? 'text-indigo-850' : 'text-gray-800' }}">{{ $act->title }}</span>
                                        <span class="text-[9px] font-extrabold uppercase {{ $actCompleted ? 'text-emerald-600' : 'text-indigo-500' }}">
                                            {{ $actCompleted ? 'Selesai' : 'Buka' }}
                                        </span>
                                    </div>
                                </a>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    @if($activity->activity_type === 'video')
        <!-- Interactive Video Javascript Handler -->
        @if(($material && $material->interactiveVideoQuestions->isNotEmpty()) || !empty($video))
        <script>
            function videoHandler() {
                return {
                    questions: @json($video ? $videoQuizzes->map(fn($q) => [
                        'id'             => $q->id,
                        'timestamp'      => $q->timestamp_seconds,
                        'question_type'  => $q->question_type ?? 'multiple_choice',
                        'question'       => $q->question,
                        'options'        => $q->options->map(fn($o) => [
                            'id' => $o->id,
                            'option_text' => $o->option_text
                        ])->toArray(),
                    ]) : ($material ? $material->interactiveVideoQuestions->map(fn($q) => [
                        'id'             => $q->id,
                        'timestamp'      => $q->timestamp,
                        'question_type'  => $q->question_type ?? 'multiple_choice',
                        'question'       => $q->question,
                        'options'        => collect($q->options ?? [])->map(fn($text, $idx) => [
                            'id' => $text,
                            'option_text' => $text
                        ])->toArray(),
                    ]) : [])),
                    showQuizModal:        false,
                    activeQuestion:       { question: '', options: [], question_type: 'multiple_choice' },
                    selectedAnswer:       '',
                    questionFeedback:     '',
                    isAnswerCorrect:      false,
                    answeredQuestionIds:  @json($video ? ($videoLog && is_array($videoLog->answered_quiz) ? array_map('intval', array_keys($videoLog->answered_quiz)) : []) : ($material ? \App\Models\VideoParticipationTracking::where('material_id', $material->id)->where('user_id', auth()->id())->pluck('question_id')->map('intval')->toArray() : [])),
                    lastTriggeredTime:    -1,
                    checkInterval:        null,
                    player:               null,
                    localVideo:           null,
                    watchLogInterval:     null,
                    supposedCurrentTime:  {{ $videoLog ? $videoLog->watched_duration : 0 }},

                    init() {
                        const self = this;
                        const localEl = document.getElementById('local-player');
                        if (localEl) {
                            self.localVideo = localEl;

                            // Resume watching position if saved
                            if (self.supposedCurrentTime > 0) {
                                self.localVideo.currentTime = self.supposedCurrentTime;
                            }

                            // Keep track of watched duration to block fast-forward
                            self.localVideo.addEventListener('timeupdate', () => {
                                self.checkVideoTime(self.localVideo.currentTime);
                                if (!self.localVideo.seeking) {
                                    if (self.localVideo.currentTime > self.supposedCurrentTime) {
                                        if (self.localVideo.currentTime - self.supposedCurrentTime < 2) {
                                            self.supposedCurrentTime = self.localVideo.currentTime;
                                        }
                                    }
                                }
                            });

                            // Prevent forward skipping
                            self.localVideo.addEventListener('seeking', () => {
                                let delta = self.localVideo.currentTime - self.supposedCurrentTime;
                                if (delta > 2) {
                                    self.localVideo.currentTime = self.supposedCurrentTime;
                                }
                            });

                            self.localVideo.addEventListener('ended', () => {
                                self.sendProgressLog(true);
                            });

                            // Periodic watch progress logging for local video
                            if ({{ $video ? 'true' : 'false' }}) {
                                self.watchLogInterval = setInterval(() => {
                                    if (!self.localVideo.paused && !self.localVideo.ended) {
                                        self.sendProgressLog(false);
                                    }
                                }, 5000);
                            }
                            return;
                        }
                        const ytEl = document.getElementById('yt-player');
                        if (ytEl) {
                            if (window.YT && window.YT.Player) {
                                self.initYoutubePlayer();
                            } else {
                                window.onYouTubeIframeAPIReady = () => self.initYoutubePlayer();
                            }
                        }
                    },

                    initYoutubePlayer() {
                        const self = this;
                        self.player = new YT.Player('yt-player', {
                            videoId: '{{ $youtubeId }}',
                            playerVars: {
                                start: Math.floor(self.supposedCurrentTime)
                            },
                            events: {
                                onStateChange: (event) => {
                                    if (event.data === YT.PlayerState.PLAYING) self.startTimer();
                                    else self.stopTimer();
                                }
                            }
                        });
                    },

                    startTimer() {
                        const self = this;
                        self.checkInterval = setInterval(() => {
                            if (self.player && typeof self.player.getCurrentTime === 'function') {
                                const currentTime = self.player.getCurrentTime();
                                
                                // Prevent forward skipping on YouTube
                                if (currentTime > self.supposedCurrentTime) {
                                    if (currentTime - self.supposedCurrentTime > 2) {
                                        self.player.seekTo(self.supposedCurrentTime, true);
                                    } else {
                                        self.supposedCurrentTime = currentTime;
                                    }
                                }

                                self.checkVideoTime(Math.floor(currentTime));
                            }
                        }, 500);
                    },

                    stopTimer() {
                        clearInterval(this.checkInterval);
                    },

                    checkVideoTime(currentTime) {
                        const t = Math.floor(currentTime);
                        if (t === this.lastTriggeredTime) return;
                        const match = this.questions.find(q => q.timestamp === t);
                        if (match && !this.answeredQuestionIds.includes(match.id)) {
                            this.lastTriggeredTime = t;
                            this.triggerQuizPopup(match);
                        }
                    },

                    triggerQuizPopup(question) {
                        this.activeQuestion    = question;
                        this.selectedAnswer    = '';
                        this.questionFeedback  = '';
                        this.showQuizModal     = true;
                        if (this.player)      this.player.pauseVideo();
                        if (this.localVideo)  this.localVideo.pause();
                    },

                    selectAnswer(opt) {
                        if (this.questionFeedback) return;
                        this.selectedAnswer = opt;
                    },

                    submitAnswer() {
                        if (!this.selectedAnswer) return;
                        const self = this;
                        
                        let payload = {};
                        let url = '';
                        
                        if ({{ $video ? 'true' : 'false' }}) {
                            url = '{{ $video ? route("student.videos.quiz_submit", $video->id) : "" }}';
                            let ansStr = '';
                            if (self.activeQuestion.question_type === 'short_answer') {
                                ansStr = self.selectedAnswer;
                            } else {
                                ansStr = self.selectedAnswer.option_text;
                            }
                            payload = {
                                quiz_id: self.activeQuestion.id,
                                answer: ansStr
                            };
                        } else {
                            url = '{{ $material ? route("student.materials.submit_video_quiz", $material) : "" }}';
                            payload = {
                                question_id:      self.activeQuestion.id,
                                selected_answer:  typeof self.selectedAnswer === 'object' ? self.selectedAnswer.option_text : self.selectedAnswer,
                                timestamp:        self.activeQuestion.timestamp
                            };
                        }

                        fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify(payload)
                        })
                        .then(res => res.json())
                        .then(data => {
                            if (data.is_correct !== undefined) {
                                self.isAnswerCorrect = data.is_correct;
                                self.questionFeedback = data.feedback;
                                self.answeredQuestionIds.push(self.activeQuestion.id);
                            } else {
                                alert(data.error || 'Gagal menyimpan jawaban. Silakan coba kembali.');
                            }
                        })
                        .catch(err => {
                            console.error(err);
                            alert('Gagal menyimpan jawaban. Silakan coba kembali.');
                        });
                    },

                    sendProgressLog(isCompleted) {
                        if (!{{ $video ? 'true' : 'false' }}) return;
                        const self = this;
                        fetch('{{ $video ? route("student.videos.log", $video->id) : "" }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                watched_duration: Math.floor(self.localVideo ? self.localVideo.currentTime : 0),
                                completed: isCompleted
                            })
                        })
                        .then(res => res.json())
                        .then(data => {
                            console.log('Progress logged:', data);
                        })
                        .catch(err => console.error('Error logging progress:', err));
                    },

                    submitVideoCompletion() {
                        const self = this;
                        self.sendProgressLog(true);
                        // Redirect student back to course details
                        setTimeout(() => {
                            window.location.href = '{{ route("student.courses.show", $activity->module->course) }}';
                        }, 500);
                    },

                    resumePlayback() {
                        this.showQuizModal = false;
                        if (this.player)      this.player.playVideo();
                        if (this.localVideo)  this.localVideo.play();
                    },

                    allQuestionsAnswered() {
                        if (this.questions.length === 0) return true;
                        return this.questions.every(q => this.answeredQuestionIds.includes(q.id));
                    }
                }
            }

            function submitVideoCompletionForm() {
                document.getElementById('video-complete-form').submit();
            }
        </script>
        @if($hasYoutube && $youtubeId)
            <script src="https://www.youtube.com/iframe_api"></script>
        @endif
        @endif
    @endif
</x-app-layout>
