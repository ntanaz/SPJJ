<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.courses.show', $quiz->course) }}" class="p-2 bg-white rounded-full text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
                Hasil Lengkap: {{ $quiz->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-8">
        <!-- Notifikasi -->
        @if(session('success'))
            <div class="p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl flex items-center shadow-sm">
                <svg class="w-6 h-6 mr-3 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-3xl overflow-hidden shadow-xl border border-gray-100">
            <!-- Score Overview -->
            <div class="bg-gradient-to-r {{ $attempt->score >= 75 ? 'from-emerald-500 to-teal-600' : 'from-amber-500 to-orange-500' }} p-12 text-center text-white relative">
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
                <div class="relative z-10">
                    <span class="text-sm font-bold uppercase tracking-widest text-white/80 mb-4 inline-block">Nilai Akhir Ujian</span>
                    <h3 class="text-7xl font-black mb-2">{{ $attempt->score }} <span class="text-3xl text-white/50">/ 100</span></h3>
                    <p class="text-xl font-bold mt-4">
                        {{ $attempt->score >= 75 ? '🎉 Luar Biasa! Anda telah menguasai materi ini.' : '💪 Wah, butuh sedikit perjuangan lagi. Tetap semangat!' }}
                    </p>
                </div>
            </div>

            <!-- Detail Jawaban -->
            <div class="p-8 sm:p-12 pb-0">
                <h3 class="text-2xl font-black text-gray-800 mb-8 flex items-center gap-3">
                    <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    Review Jawaban
                </h3>

                <div class="space-y-6">
                    @foreach($quiz->questions as $index => $question)
                        @php
                            $answer = $attempt->answers->where('quiz_question_id', $question->id)->first();
                            $selected = $answer ? $answer->selected_option : null;
                            $isCorrect = $answer ? $answer->is_correct : false;
                        @endphp
                        <div class="p-6 rounded-2xl border-2 {{ $isCorrect ? 'border-emerald-100 bg-emerald-50/30' : 'border-red-100 bg-red-50/30' }}">
                            <div class="flex gap-4">
                                <!-- Status Icon -->
                                <div class="w-10 h-10 flex flex-shrink-0 items-center justify-center rounded-full font-black text-xl shadow-sm text-white {{ $isCorrect ? 'bg-emerald-500 shadow-emerald-500/30' : 'bg-red-500 shadow-red-500/30' }}">
                                    @if($isCorrect)
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    @else
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                    @endif
                                </div>
                                
                                <div class="flex-1">
                                    <p class="text-lg font-bold text-gray-800 mb-4"><span class="text-gray-400 mr-2">{{ $index + 1 }}.</span> {{ $question->question }}</p>
                                    
                                    <div class="space-y-2">
                                        @foreach($question->options as $key => $text)
                                            @php
                                                $isSelected = $selected === $key;
                                                $isRealCorrect = $question->correct_answer === $key;
                                                
                                                // Determine styling
                                                if ($isRealCorrect) {
                                                    $bgClass = 'bg-emerald-100 border-emerald-300 text-emerald-800';
                                                    $icon = '<svg class="w-5 h-5 text-emerald-600 ml-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>';
                                                } elseif ($isSelected && !$isCorrect) {
                                                    $bgClass = 'bg-red-100 border-red-300 text-red-800';
                                                    $icon = '<svg class="w-5 h-5 text-red-600 ml-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd" /></svg>';
                                                } else {
                                                    $bgClass = 'bg-white border-gray-200 text-gray-500';
                                                    $icon = '';
                                                }
                                            @endphp
                                            <div class="flex items-center p-3 rounded-xl border {{ $bgClass }} transition-colors">
                                                <div class="w-6 h-6 flex flex-shrink-0 items-center justify-center rounded text-xs font-bold mr-3 @if($isRealCorrect) bg-emerald-500 text-white @elseif($isSelected) bg-red-500 text-white @else bg-gray-100 text-gray-400 @endif">
                                                    {{ $key }}
                                                </div>
                                                <span class="text-sm font-bold">{{ $text }}</span>
                                                {!! $icon !!}
                                            </div>
                                        @endforeach
                                    </div>
                                    
                                    @if(!$isCorrect && !$selected)
                                        <p class="text-red-500 text-sm font-bold mt-3">⚠️ Anda tidak menjawab pertanyaan ini.</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
            
            <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 text-center rounded-b-3xl mt-12">
                <a href="{{ route('student.courses.show', $quiz->course) }}" class="inline-flex items-center justify-center gap-2 px-8 py-3 bg-white text-gray-700 hover:text-indigo-600 border border-gray-300 hover:border-indigo-300 rounded-xl font-bold shadow-sm transition-all focus:outline-none focus:ring-4 focus:ring-indigo-100 active:scale-95">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Tutup dan Kembali ke Kelas
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
