<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('student.courses.show', $material->course) }}" class="p-2 bg-white rounded-full text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
                        {{ $material->title }}
                    </h2>
                    <p class="text-sm text-gray-500">{{ $material->course->name }} &bull; Discovery Learning Pathway</p>
                </div>
            </div>
            
            <div class="hidden sm:flex items-center gap-2 px-4 py-2 bg-indigo-50 text-indigo-700 font-bold rounded-xl border border-indigo-100 text-xs">
                <span>XP Reward: +100 XP</span>
                <svg class="w-4 h-4 text-indigo-500 animate-bounce" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L13.82 8.767l-2.034-2.8a1 1 0 00-1.175 0L7.812 8.768l-2.8-2.034c-.784-.57-1.838.197-1.539 1.118l1.07 3.292a1 1 0 00-.364 1.118L1.18 8.767c-.784-.57-.383-1.81.587-1.81H5.23a1 1 0 00.95-.69L7.25 2.927z"/></svg>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto px-4" x-data="{ currentTab: '{{ $activeStep }}' }">
        
        <!-- STEP PROGRESS INDICATOR -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 mb-8">
            <div class="flex items-center justify-between max-w-3xl mx-auto relative">
                <!-- Progress Line Behind -->
                <div class="absolute left-0 right-0 top-1/2 h-1 bg-gray-200 -translate-y-1/2 z-0"></div>
                
                <!-- Progress Line Active -->
                @php
                    $stepsList = ['mind_map', 'modul', 'video', 'coding', 'reflection'];
                    $completedCount = 0;
                    foreach($stepsList as $s) {
                        if(!empty($stepsProgress[$s])) $completedCount++;
                    }
                    $pct = ($completedCount / 4) * 100;
                @endphp
                <div class="absolute left-0 top-1/2 h-1 bg-indigo-500 -translate-y-1/2 z-0 transition-all duration-500" style="width: {{ min(100, $pct) }}%"></div>

                <!-- Steps -->
                @php
                    $stepsData = [
                        'mind_map' => ['label' => 'Mind Map', 'icon' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7'],
                        'modul' => ['label' => 'Modul', 'icon' => 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253'],
                        'video' => ['label' => 'Video Kuis', 'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                        'coding' => ['label' => 'Kuis Koding', 'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'],
                        'reflection' => ['label' => 'Refleksi', 'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
                    ];
                @endphp
                @foreach($stepsList as $idx => $stepName)
                    @php
                        $sd = $stepsData[$stepName];
                        $isUnlocked = !empty($unlockedSteps[$stepName]);
                        $isCompleted = !empty($stepsProgress[$stepName]);
                    @endphp
                    <div class="flex flex-col items-center z-10">
                        <button 
                            @click="currentTab = '{{ $stepName }}'"
                            :disabled="!{{ $isUnlocked ? 'true' : 'false' }}"
                            class="h-12 w-12 rounded-full flex items-center justify-center border-2 transition-all duration-300 shadow-sm
                                   {{ $isCompleted ? 'bg-emerald-500 border-emerald-500 text-white hover:bg-emerald-600' : 
                                      ($isUnlocked ? 'bg-white border-indigo-500 text-indigo-600 hover:bg-indigo-50' : 'bg-gray-100 border-gray-300 text-gray-400 cursor-not-allowed') }}"
                            :class="currentTab === '{{ $stepName }}' ? 'ring-4 ring-indigo-200 scale-110' : ''"
                            title="{{ $sd['label'] }}"
                        >
                            @if($isCompleted)
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                            @else
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $sd['icon'] }}"/></svg>
                            @endif
                        </button>
                        <span class="text-[10px] font-bold mt-2 uppercase tracking-wider 
                                     {{ $isCompleted ? 'text-emerald-650' : ($isUnlocked ? 'text-indigo-600' : 'text-gray-400') }}">
                            {{ $sd['label'] }}
                        </span>
                    </div>
                @endforeach
            </div>
        </div>

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

        <!-- TAB CONTENTS -->
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 min-h-[400px]">
            
            <!-- STEP 1: MIND MAP -->
            <div x-show="currentTab === 'mind_map'" class="space-y-6">
                <div class="border-b border-gray-100 pb-4 text-left">
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Tahap 1: Stimulation (Stimulasi)</span>
                    <h3 class="text-2xl font-black text-gray-800 mt-1">Peta Pikiran &amp; Konsep</h3>
                    <p class="text-gray-500 text-sm mt-1">Pelajari gambaran umum topik ini melalui peta pikiran di bawah ini sebelum melanjutkan.</p>
                </div>
                
                @if($material->mind_map_path)
                    <div class="flex justify-center p-4 bg-gray-50 rounded-2xl border border-gray-100">
                        <img src="{{ asset('storage/' . $material->mind_map_path) }}" alt="Peta Pikiran" class="max-h-[500px] object-contain rounded-xl shadow-sm">
                    </div>
                @else
                    <div class="text-center py-12 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-250">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        <p class="text-gray-400 font-bold">Peta pikiran belum diunggah oleh guru.</p>
                    </div>
                @endif
                
                <div class="flex justify-end pt-4 border-t border-gray-100">
                    @if(!empty($stepsProgress['mind_map']))
                        <button @click="currentTab = 'modul'" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-750 transition-colors flex items-center gap-2">
                            Lanjut ke Modul
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    @else
                        <button @click="completeStep('mind_map')" class="px-6 py-3 bg-indigo-650 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm transition-transform active:scale-95 flex items-center gap-2">
                            Selesaikan &amp; Lanjutkan
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </button>
                    @endif
                </div>
            </div>

            <!-- STEP 2: MODUL -->
            <div x-show="currentTab === 'modul'" class="space-y-6">
                <div class="border-b border-gray-100 pb-4 text-left">
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Tahap 2: Problem Statement &amp; Data Collection</span>
                    <h3 class="text-2xl font-black text-gray-800 mt-1">Modul &amp; Bahan Pembelajaran</h3>
                    <p class="text-gray-500 text-sm mt-1">Pelajari materi, definisikan pertanyaan Anda, dan kumpulkan informasi yang dibutuhkan.</p>
                </div>

                @if($material->description)
                <div class="prose max-w-none text-gray-700 whitespace-pre-line border-l-4 border-indigo-150 pl-4 mb-4 text-left leading-relaxed">
                    {{ $material->description }}
                </div>
                @endif

                <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                    @if($material->format === 'document' || in_array($material->type, ['pdf', 'slide']))
                        <div class="text-center py-6">
                            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-50 text-emerald-600 mb-4">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 mb-2">Dokumen Materi</h4>
                            @if($material->file_path)
                                <div class="flex justify-center gap-4">
                                    <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="px-5 py-2.5 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm transition-colors flex items-center gap-2 text-sm">
                                        Buka Dokumen
                                    </a>
                                    <a href="{{ asset('storage/' . $material->file_path) }}" download class="px-5 py-2.5 bg-white border border-emerald-600 text-emerald-700 hover:bg-emerald-50 font-bold rounded-xl shadow-sm transition-colors flex items-center gap-2 text-sm">
                                        Unduh File
                                    </a>
                                </div>
                            @else
                                <p class="text-red-500 text-sm font-bold">File dokumen tidak tersedia.</p>
                            @endif
                        </div>
                    @elseif($material->format === 'link' || str_contains($material->type, 'link'))
                        <div class="text-center py-6">
                            <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-blue-50 text-blue-500 mb-4">
                                <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                            </div>
                            <h4 class="text-lg font-bold text-gray-800 mb-2">Tautan Eksternal</h4>
                            @if($material->text_content || $material->youtube_url || $material->file_path)
                                @php
                                    $url = $material->youtube_url ?? $material->text_content ?? $material->file_path;
                                    if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                                        $url = "http://" . $url;
                                    }
                                @endphp
                                <a href="{{ $url }}" target="_blank" class="inline-flex px-5 py-2.5 bg-blue-650 hover:bg-blue-700 text-white font-bold rounded-xl shadow-sm transition-colors items-center gap-2 text-sm">
                                    Buka Tautan
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                                </a>
                            @else
                                <p class="text-red-500 text-sm font-bold">Tautan tidak tersedia.</p>
                            @endif
                        </div>
                    @else
                        <!-- Text/Default -->
                        <div class="prose max-w-none text-gray-750 text-left leading-relaxed">
                            @if($material->text_content)
                                <div class="whitespace-pre-wrap">{!! nl2br(e($material->text_content)) !!}</div>
                            @else
                                <p class="text-gray-500 italic text-sm">Konten teks tidak tersedia.</p>
                            @endif
                        </div>
                    @endif
                </div>

                <div class="flex justify-between pt-4 border-t border-gray-100">
                    <button @click="currentTab = 'mind_map'" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                        Kembali
                    </button>
                    @if(!empty($stepsProgress['modul']))
                        <button @click="currentTab = 'video'" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-750 transition-colors flex items-center gap-2">
                            Lanjut ke Video Kuis
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    @else
                        <button @click="completeStep('modul')" class="px-6 py-3 bg-indigo-650 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm transition-transform active:scale-95 flex items-center gap-2">
                            Selesaikan &amp; Lanjutkan
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </button>
                    @endif
                </div>
            </div>

            <!-- STEP 3: VIDEO KUIS -->
            <div x-show="currentTab === 'video'" class="space-y-6" x-data="videoHandler()">
                <div class="border-b border-gray-100 pb-4 text-left">
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Tahap 3: Data Processing (Pengolahan Data)</span>
                    <h3 class="text-2xl font-black text-gray-800 mt-1">Video Pembelajaran Interaktif</h3>
                    <p class="text-gray-500 text-sm mt-1">Tonton video di bawah ini. Jawab setiap pertanyaan pop-up kuis yang muncul di sepanjang video.</p>
                </div>

                @php
                    $hasLocalVideo = $material->file_path && in_array($material->format ?? $material->type, ['video', 'video_post_class']);
                    $hasYoutube    = !empty($material->youtube_url);
                    $youtubeId     = null;
                    if ($hasYoutube) {
                        preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $material->youtube_url, $ytMatch);
                        $youtubeId = $ytMatch[1] ?? null;
                    }
                @endphp

                <div class="relative rounded-3xl overflow-hidden bg-black aspect-video shadow-md max-w-4xl mx-auto border border-gray-850">
                    @if($hasLocalVideo)
                        <!-- Local Video Player -->
                        <video id="local-player" controls class="w-full h-full">
                            <source src="{{ asset('storage/' . $material->file_path) }}" type="video/mp4">
                        </video>
                    @elseif($hasYoutube && $youtubeId)
                        <!-- YouTube Video Iframe container -->
                        <div id="yt-player" class="w-full h-full min-h-[450px]"></div>
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

                        <!-- Multiple Choice & True/False options -->
                        <div class="space-y-3" x-show="activeQuestion.question_type === 'multiple_choice' || activeQuestion.question_type === 'true_false' || !activeQuestion.question_type">
                            <template x-for="(opt, idx) in activeQuestion.options" :key="idx">
                                <button
                                    @click="selectAnswer(opt)"
                                    class="w-full text-left p-4 rounded-2xl border-2 transition-all font-bold text-sm"
                                    :class="selectedAnswer === opt ? 'border-indigo-600 bg-indigo-50 text-indigo-700' : 'border-gray-100 hover:border-indigo-100 text-gray-700 bg-gray-50 hover:bg-white'">
                                    <span class="mr-2" x-text="String.fromCharCode(65 + idx) + '.'"></span>
                                    <span x-text="opt"></span>
                                </button>
                            </template>
                        </div>

                        <!-- Short Answer Field -->
                        <div x-show="activeQuestion.question_type === 'short_answer'" class="space-y-3">
                            <input type="text" x-model="selectedAnswer" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm font-bold p-4" placeholder="Ketik jawaban Anda disini...">
                        </div>

                        <!-- Question Feedback & Status -->
                        <div x-show="questionFeedback" class="p-4 rounded-2xl border text-sm font-bold text-center"
                            :class="isAnswerCorrect ? 'bg-emerald-50 border-emerald-250 text-emerald-800' : 'bg-rose-50 border-rose-250 text-rose-800'">
                            <p x-text="questionFeedback"></p>
                        </div>

                        <!-- Actions -->
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

                <div class="flex justify-between pt-4 border-t border-gray-100">
                    <button @click="currentTab = 'modul'" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                        Kembali
                    </button>
                    @if(!empty($stepsProgress['video']))
                        <button @click="currentTab = 'coding'" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-750 transition-colors flex items-center gap-2">
                            Lanjut ke Kuis Koding
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    @else
                        <button @click="completeStep('video')" :disabled="!allQuestionsAnswered()" class="px-6 py-3 bg-indigo-650 hover:bg-indigo-700 disabled:bg-gray-150 disabled:text-gray-400 disabled:cursor-not-allowed text-white font-bold rounded-xl shadow-sm transition-transform active:scale-95 flex items-center gap-2">
                            Selesaikan &amp; Lanjutkan
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </button>
                    @endif
                </div>
            </div>

            <!-- STEP 4: KUIS KODING -->
            <div x-show="currentTab === 'coding'" class="space-y-6">
                <div class="border-b border-gray-100 pb-4 text-left">
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Tahap 4: Verification (Pembuktian)</span>
                    <h3 class="text-2xl font-black text-gray-800 mt-1">Kuis Koding &amp; Pembuktian</h3>
                    <p class="text-gray-500 text-sm mt-1">Kerjakan tantangan pemrograman di bawah ini untuk membuktikan pemahaman kognitif Anda.</p>
                </div>

                @if($codingQuiz)
                    <div class="bg-gray-50/50 rounded-3xl p-6 border border-gray-150/50 space-y-6 text-left">
                        <div class="prose max-w-none text-gray-700">
                            <h4 class="font-extrabold text-gray-800 text-lg mb-2">Tugas/Instruksi Kuis:</h4>
                            <p class="whitespace-pre-line text-sm leading-relaxed">{{ $codingQuiz->instruction }}</p>
                        </div>

                        <!-- Attempts status -->
                        <div class="flex items-center justify-between bg-white border border-gray-150 p-4 rounded-2xl">
                            <div>
                                <span class="text-xs font-bold text-gray-400 uppercase">Status Penyelesaian:</span>
                                <div class="mt-1 flex items-center gap-2">
                                    @if($isCodingQuizSuccess)
                                        <span class="px-2.5 py-1 bg-emerald-100 text-emerald-800 rounded-lg text-xs font-black uppercase tracking-wider">Lolos Verifikasi</span>
                                    @elseif($isCodingQuizLocked)
                                        <span class="px-2.5 py-1 bg-red-100 text-red-800 rounded-lg text-xs font-black uppercase tracking-wider">Terkunci (Kesempatan Habis)</span>
                                    @else
                                        <span class="px-2.5 py-1 bg-amber-100 text-amber-800 rounded-lg text-xs font-black uppercase tracking-wider">Sedang Dikerjakan</span>
                                    @endif
                                </div>
                            </div>
                            <div class="text-right">
                                <span class="text-xs font-bold text-gray-400 uppercase">Kesempatan Percobaan:</span>
                                <p class="text-sm font-extrabold text-gray-700 mt-0.5">{{ $codingAttemptsCount }} / 3 Kali</p>
                            </div>
                        </div>

                        <!-- Coding Quiz Attempt Form -->
                        @if(!$isCodingQuizSuccess && !$isCodingQuizLocked)
                            <form action="{{ route('student.materials.submit_coding_quiz', $material) }}" method="POST" class="space-y-4">
                                @csrf

                                @php
                                    $qType = $codingQuiz->quiz_type ?? 'fill_blank';
                                @endphp

                                @if($qType === 'short_answer')
                                    <!-- Short Answer Input -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-bold text-gray-700">Tulis Jawaban Singkat Anda:</label>
                                        <textarea name="short_answer" rows="3" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors font-mono text-sm p-4" placeholder="Ketik jawaban di sini..." required></textarea>
                                    </div>
                                @elseif($qType === 'debugging')
                                    <!-- Debugging Input -->
                                    <div class="space-y-2">
                                        <label class="block text-sm font-bold text-gray-700">Perbaiki Kode di Bawah Ini:</label>
                                        <textarea name="debug_answer" rows="8" class="w-full rounded-2xl border-gray-300 font-mono text-sm bg-gray-900 text-gray-100 p-4 focus:ring-indigo-500" required>{{ $codingQuiz->code_template }}</textarea>
                                    </div>
                                @else
                                    <!-- Fill in the Blank Template rendering -->
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
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                        Kirim &amp; Verifikasi Kode
                                    </button>
                                </div>
                            </form>
                        @endif

                        <!-- Display Previous Attempts History -->
                        @if($codingAttempts->isNotEmpty())
                            <div class="space-y-3 pt-4 border-t border-gray-100">
                                <h5 class="text-xs font-bold text-gray-405 uppercase tracking-widest">Riwayat Percobaan:</h5>
                                <div class="space-y-2">
                                    @foreach($codingAttempts as $attempt)
                                        <div class="p-4 rounded-xl border flex items-center justify-between text-xs transition-all {{ $attempt->hasil_validasi ? 'bg-emerald-50 border-emerald-200 text-emerald-800' : 'bg-red-50 border-red-200 text-red-800' }}">
                                            <div>
                                                <strong class="font-extrabold">Percobaan #{{ $attempt->percobaan_ke }}</strong>
                                                <span class="mx-2">&bull;</span>
                                                <span>{{ $attempt->waktu_submit->translatedFormat('d M Y, H:i') }} WIB</span>
                                            </div>
                                            <div class="flex items-center gap-2">
                                                <span class="font-black uppercase">{{ $attempt->hasil_validasi ? 'Sukses' : 'Gagal' }}</span>
                                                @if($attempt->feedback)
                                                    <span class="text-gray-400 font-medium" title="{{ $attempt->feedback }}">({{ $attempt->feedback }})</span>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="text-center py-12 bg-gray-50 rounded-2xl border-2 border-dashed border-gray-250">
                        <svg class="w-16 h-16 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                        <p class="text-gray-400 font-bold">Kuis koding belum disiapkan oleh guru.</p>
                    </div>
                @endif

                <div class="flex justify-between pt-4 border-t border-gray-100">
                    <button @click="currentTab = 'video'" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                        Kembali
                    </button>
                    @if($isCodingQuizSuccess || !$codingQuiz)
                        <button @click="currentTab = 'reflection'" class="px-6 py-3 bg-indigo-600 text-white font-bold rounded-xl hover:bg-indigo-750 transition-colors flex items-center gap-2">
                            Lanjut ke Refleksi
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    @endif
                </div>
            </div>

            <!-- STEP 5: REFLEKSI -->
            <div x-show="currentTab === 'reflection'" class="space-y-6">
                <div class="border-b border-gray-100 pb-4 text-left">
                    <span class="text-xs font-bold text-indigo-600 uppercase tracking-widest">Tahap 5: Generalization (Generalisasi)</span>
                    <h3 class="text-2xl font-black text-gray-800 mt-1">Refleksi Koding &amp; Analisis</h3>
                    <p class="text-gray-500 text-sm mt-1">Tuliskan kesimpulan, analisis logika program, dan refleksi pembelajaran Anda pada materi ini.</p>
                </div>

                @php
                    $isCompleted = !empty($stepsProgress['reflection']);
                @endphp

                @if($isCompleted)
                    <div class="p-6 bg-emerald-50 border border-emerald-200 text-emerald-800 rounded-3xl space-y-4 text-left">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 bg-emerald-500 text-white flex items-center justify-center rounded-full flex-shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
                            </div>
                            <div>
                                <h4 class="font-extrabold text-base">Tahap Refleksi Selesai!</h4>
                                <p class="text-xs text-emerald-700">Anda telah melengkapi seluruh rangkaian pembelajaran Discovery Learning.</p>
                            </div>
                        </div>
                        
                        @if($correctAttempt && $correctAttempt->reflection)
                            <div class="bg-white border border-emerald-150 p-4 rounded-2xl">
                                <span class="text-xs font-bold text-gray-400 uppercase">Teks Refleksi Anda:</span>
                                <p class="text-sm text-gray-700 mt-2 leading-relaxed whitespace-pre-wrap">{!! e($correctAttempt->reflection) !!}</p>
                            </div>
                            
                            @if($correctAttempt->graded_at)
                                <div class="bg-indigo-50 border border-indigo-150 p-4 rounded-2xl space-y-2">
                                    <span class="text-xs font-bold text-indigo-700 uppercase">Penilaian Guru:</span>
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
                    <form action="{{ route('student.materials.submit_reflection', $material) }}" method="POST" class="space-y-4 text-left">
                        @csrf
                        <div class="space-y-2">
                            <label for="reflection" class="block text-sm font-bold text-gray-700">Analisis &amp; Refleksi Pembelajaran Anda:</label>
                            <textarea name="reflection" id="reflection" rows="6" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors text-sm p-4" placeholder="Tuliskan minimal 10 karakter refleksi pemahaman konsep Anda mengenai kode program atau materi ini..." required></textarea>
                            @error('reflection')<p class="mt-1 text-xs text-red-650 font-bold">{{ $message }}</p>@enderror
                        </div>

                        <div class="bg-amber-50 border border-amber-100 p-4 rounded-2xl text-amber-800 text-xs font-medium">
                            💡 Mengirimkan refleksi akan menandai materi pembelajaran ini sebagai **Lengkap/Selesai** secara keseluruhan dan Anda akan menerima reward **+100 XP** Zenith.
                        </div>

                        <div class="flex justify-end pt-2">
                            <button type="submit" class="px-6 py-3.5 bg-indigo-650 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2">
                                <svg class="w-5 h-5 animate-pulse" fill="currentColor" viewBox="0 0 20 20"><path d="M10.894 2.553a1 1 0 00-1.788 0l-7 14a1 1 0 001.169 1.409l5-1.429A1 1 0 009 15.571V11a1 1 0 112 0v4.571a1 1 0 00.725.962l5 1.428a1 1 0 001.17-1.408l-7-14z"/></svg>
                                Kirim Refleksi &amp; Selesaikan Materi
                            </button>
                        </div>
                    </form>
                @endif

                <div class="flex justify-between pt-4 border-t border-gray-100">
                    <button @click="currentTab = 'coding'" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-bold rounded-xl hover:bg-gray-200 transition-colors">
                        Kembali
                    </button>
                </div>
            </div>

        </div>

        <!-- Forum Diskusi Materi -->
        <div class="mt-8 space-y-6 text-left">
            <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                Forum Diskusi Materi
            </h3>

            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                <!-- Form Tambah Diskusi Materi (Hanya Guru/Admin) -->
                @role('guru|teacher|admin')
                <form action="{{ route('discussions.store_material', $material) }}" method="POST" class="mb-8">
                    @csrf
                    <div class="flex items-start gap-4">
                        <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold flex-shrink-0">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="flex-1 space-y-3">
                            <input type="text" name="title" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors text-sm font-bold" placeholder="Judul / Topik Diskusi Materi" required>
                            @error('title')<p class="mt-1 text-xs text-red-650 font-bold">{{ $message }}</p>@enderror
                            <textarea name="content" rows="3" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors text-sm" placeholder="Tanyakan atau bahas sesuatu terkait materi ini..." required></textarea>
                            @error('content')<p class="mt-1 text-xs text-red-650 font-bold">{{ $message }}</p>@enderror
                            <div class="mt-3 flex justify-end">
                                <button type="submit" class="px-5 py-2 bg-indigo-650 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-sm transition-transform active:scale-95 flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                    Kirim Diskusi
                                </button>
                            </div>
                        </div>
                    </div>
                </form>
                @endrole

                <div class="space-y-4">
                    @forelse($material->discussions as $disc)
                        <div class="p-5 rounded-2xl border border-gray-100 bg-gray-50/50 hover:bg-gray-50 transition-colors flex items-start gap-4 relative group text-left">
                            <div class="h-10 w-10 rounded-full bg-indigo-50 text-indigo-700 flex items-center justify-center font-bold flex-shrink-0">
                                {{ substr($disc->user->name, 0, 1) }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="flex items-center gap-2 flex-wrap mb-1">
                                    <span class="text-sm font-bold text-gray-800">{{ $disc->user->name }}</span>
                                    <span class="px-2 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-indigo-100 text-indigo-700">
                                        {{ $disc->user->roles->first()?->name ?? 'siswa' }}
                                    </span>
                                    <span class="text-xs text-gray-400 font-medium">
                                        {{ $disc->created_at->diffForHumans() }}
                                    </span>
                                </div>
                                <h4 class="font-extrabold text-gray-900 text-base mb-1 hover:text-indigo-650">
                                    <a href="{{ route('discussions.show', $disc) }}">{{ $disc->title }}</a>
                                </h4>
                                <p class="text-sm text-gray-600 line-clamp-2 leading-relaxed mb-3">{{ $disc->content }}</p>
                                
                                <div class="flex items-center gap-4">
                                    <a href="{{ route('discussions.show', $disc) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-850">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                        {{ $disc->replies->count() }} Komentar
                                    </a>
                                </div>
                            </div>
                            
                            @if(auth()->id() === $disc->user_id || auth()->user()->hasRole(['guru', 'teacher', 'admin']))
                            <div class="absolute top-5 right-5 opacity-0 group-hover:opacity-100 transition-opacity">
                                <form action="{{ route('discussions.destroy', $disc) }}" method="POST" onsubmit="return confirm('Hapus diskusi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                    </button>
                                </form>
                            </div>
                            @endif
                        </div>
                    @empty
                        <div class="text-center py-8 text-gray-400">
                            <p class="text-sm font-semibold">Belum ada diskusi untuk materi ini.</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>

    <!-- Interactive Video Quiz Javascript Handler -->
    {{-- Load handler for local video (primary) OR YouTube (fallback) --}}
    @php
        $hasLocalVideo = $material->file_path && in_array($material->format ?? $material->type, ['video', 'video_post_class']);
        $hasYoutube    = !empty($material->youtube_url);
        $youtubeId     = null;
        if ($hasYoutube) {
            preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $material->youtube_url, $ytMatch);
            $youtubeId = $ytMatch[1] ?? null;
        }
    @endphp
    @if($material->interactiveVideoQuestions->isNotEmpty())
    <script>
        function videoHandler() {
            return {
                questions: {!! json_encode($material->interactiveVideoQuestions->map(fn($q) => [
                    'id'             => $q->id,
                    'timestamp'      => $q->timestamp,
                    'question_type'  => $q->question_type ?? 'multiple_choice',
                    'question'       => $q->question,
                    'options'        => $q->options ?? [],
                    'correct_answer' => $q->correct_answer,
                ])) !!},
                showQuizModal:        false,
                activeQuestion:       { question: '', options: [], question_type: 'multiple_choice' },
                selectedAnswer:       '',
                questionFeedback:     '',
                isAnswerCorrect:      false,
                answeredQuestionIds:  {!! json_encode(\App\Models\VideoParticipationTracking::where('material_id', $material->id)->where('user_id', auth()->id())->pluck('question_id')) !!},
                lastTriggeredTime:    -1,
                checkInterval:        null,
                player:               null,
                localVideo:           null,

                init() {
                    const self = this;
                    // Primary: local video player
                    const localEl = document.getElementById('local-player');
                    if (localEl) {
                        self.localVideo = localEl;
                        self.localVideo.addEventListener('timeupdate', () => {
                            self.checkVideoTime(self.localVideo.currentTime);
                        });
                        return; // local takes priority, skip YouTube setup
                    }
                    // Fallback: YouTube embed
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
                            self.checkVideoTime(Math.floor(self.player.getCurrentTime()));
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
                    // Pause whichever player is active
                    if (this.player)      this.player.pauseVideo();
                    if (this.localVideo)  this.localVideo.pause();
                },

                selectAnswer(opt) {
                    if (this.questionFeedback) return; // already submitted
                    this.selectedAnswer = opt;
                },

                submitAnswer() {
                    if (!this.selectedAnswer) return;
                    const self = this;
                    fetch('{{ route("student.materials.submit_video_quiz", $material) }}', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        body: JSON.stringify({
                            question_id:      self.activeQuestion.id,
                            selected_answer:  self.selectedAnswer,
                            timestamp:        self.activeQuestion.timestamp
                        })
                    })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            self.isAnswerCorrect = data.is_correct;
                            self.questionFeedback = data.feedback;
                            self.answeredQuestionIds.push(self.activeQuestion.id);
                        } else {
                            alert(data.error || 'Gagal menyimpan jawaban. Silakan coba kembali.');
                        }
                    })
                    .catch(err => {
                        alert('Gagal menyimpan jawaban. Silakan coba kembali.');
                    });
                },

                resumePlayback() {
                    this.showQuizModal = false;
                    if (this.player)      this.player.playVideo();
                    if (this.localVideo)  this.localVideo.play();
                },

                allQuestionsAnswered() {
                    if (this.questions.length === 0) return true; // no questions = can proceed
                    return this.questions.every(q => this.answeredQuestionIds.includes(q.id));
                }
            }
        }
    </script>
    @if($hasYoutube && $youtubeId)
    <script src="https://www.youtube.com/iframe_api"></script>
    @endif
    @endif

    <script>
        function completeStep(stepName) {
            fetch('{{ route("student.materials.complete_step", $material) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    step: stepName
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.success) {
                    // Refresh current page to load updated locks
                    window.location.reload();
                } else {
                    alert(data.error || 'Terjadi kesalahan.');
                }
            })
            .catch(err => {
                alert('Gagal melengkapi tahapan.');
            });
        }
    </script>
</x-app-layout>
