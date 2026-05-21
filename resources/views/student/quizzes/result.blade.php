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
                            $textAnswer = $answer ? $answer->text_answer : null;
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
                                
                                <div class="flex-1 w-full overflow-hidden">
                                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold bg-indigo-50 text-indigo-700 uppercase">
                                            {{ str_replace('_', ' ', $question->question_type) }}
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-full text-xs font-bold {{ $isCorrect ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700' }}">
                                            {{ $isCorrect ? $question->points . ' / ' . $question->points : '0 / ' . $question->points }} Poin
                                        </span>
                                    </div>

                                    <p class="text-lg font-bold text-gray-800 mb-4"><span class="text-gray-400 mr-2">{{ $index + 1 }}.</span> {{ $question->question }}</p>
                                    
                                    <!-- Render MC / TF -->
                                    @if(in_array($question->question_type, ['multiple_choice', 'true_false']))
                                        <div class="space-y-2">
                                            @foreach(($question->options ?? []) as $key => $text)
                                                @php
                                                    $isSelected = $selected === $key;
                                                    $isRealCorrect = $question->correct_answer === $key;
                                                    
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
                                    
                                    <!-- Render Short Answer -->
                                    @elseif($question->question_type === 'short_answer')
                                        <div class="space-y-2 text-sm">
                                            <div class="p-3 rounded-xl border bg-white flex items-center">
                                                <span class="font-bold mr-2 text-gray-500">Jawaban Anda:</span>
                                                <span class="font-mono font-bold {{ $isCorrect ? 'text-emerald-600' : 'text-red-600' }}">{{ $textAnswer ?: '(Kosong)' }}</span>
                                            </div>
                                            <div class="p-3 rounded-xl border bg-emerald-50 border-emerald-250 flex items-center">
                                                <span class="font-bold mr-2 text-emerald-800">Jawaban Benar:</span>
                                                <span class="font-mono font-bold text-emerald-800">{{ $question->correct_answer }}</span>
                                            </div>
                                            @if(!empty($question->options['keywords']))
                                                <div class="p-3 rounded-xl border bg-indigo-50 border-indigo-200 flex items-center">
                                                    <span class="font-bold mr-2 text-indigo-800">Kata Kunci Diterima:</span>
                                                    <span class="font-mono font-bold text-indigo-800">{{ $question->options['keywords'] }}</span>
                                                </div>
                                            @endif
                                        </div>

                                    <!-- Render Fill in the blank Coding -->
                                    @elseif($question->question_type === 'fill_blank')
                                        @php
                                            $template = $question->options['code_template'] ?? '';
                                            $placeholder = $question->options['blank_placeholder'] ?? '[blank]';
                                            
                                            // Render with student's answer
                                            $studentAnswerText = $textAnswer ?: '(Kosong)';
                                            $studentHtml = '<span class="mx-1 px-3 py-0.5 rounded font-mono text-sm font-bold border ' . ($isCorrect ? 'bg-emerald-100 border-emerald-300 text-emerald-800' : 'bg-red-100 border-red-300 text-red-800') . '">' . e($studentAnswerText) . '</span>';
                                            $escapedTemplate = e($template);
                                            $renderedStudentCode = str_replace(e($placeholder), $studentHtml, $escapedTemplate);
                                            
                                            // Render with correct answer
                                            $correctHtml = '<span class="mx-1 px-3 py-0.5 rounded font-mono text-sm font-bold border bg-emerald-100 border-emerald-300 text-emerald-800">' . e($question->correct_answer) . '</span>';
                                            $renderedCorrectCode = str_replace(e($placeholder), $correctHtml, $escapedTemplate);
                                        @endphp
                                        <div class="space-y-4 text-sm">
                                            <div class="space-y-1">
                                                <p class="text-xs font-bold text-gray-400 uppercase tracking-widest">Kode Anda:</p>
                                                <div class="p-4 bg-gray-900 text-yellow-300 rounded-2xl font-mono text-sm overflow-x-auto leading-relaxed shadow-inner">
                                                    {!! nl2br($renderedStudentCode) !!}
                                                </div>
                                            </div>
                                            <div class="space-y-1">
                                                <p class="text-xs font-bold text-emerald-500 uppercase tracking-widest">Kunci Solusi Benar:</p>
                                                <div class="p-4 bg-gray-900 text-yellow-300 rounded-2xl font-mono text-sm overflow-x-auto leading-relaxed shadow-inner">
                                                    {!! nl2br($renderedCorrectCode) !!}
                                                </div>
                                            </div>
                                            
                                            @if($isCorrect && !empty($question->options['feedback_correct']))
                                                <div class="p-4 bg-emerald-50 border border-emerald-250 rounded-2xl text-emerald-800">
                                                    <strong>Feedback:</strong> {{ $question->options['feedback_correct'] }}
                                                </div>
                                            @elseif(!$isCorrect && !empty($question->options['feedback_incorrect']))
                                                <div class="p-4 bg-red-50 border border-red-200 rounded-2xl text-red-800">
                                                    <strong>Feedback:</strong> {{ $question->options['feedback_incorrect'] }}
                                                </div>
                                            @endif
                                        </div>

                                    <!-- Render Reflection -->
                                    @elseif($question->question_type === 'reflection')
                                        <div class="space-y-2 text-sm">
                                            <div class="p-4 rounded-xl border bg-white">
                                                <p class="font-bold text-gray-500 mb-2">Jawaban Refleksi Anda:</p>
                                                <p class="font-medium text-gray-800 italic leading-relaxed">"{{ $textAnswer ?: '(Kosong)' }}"</p>
                                            </div>
                                            <p class="text-xs text-emerald-600 font-bold">✓ Refleksi telah diterima dan poin penuh diberikan.</p>
                                        </div>

                                    <!-- Render Debugging -->
                                    @elseif($question->question_type === 'debugging')
                                        <div class="space-y-4 text-sm">
                                            @if(!empty($question->options['bug_description']))
                                                <div class="p-4 bg-rose-50 border border-rose-150 rounded-2xl text-rose-800 text-sm">
                                                    <strong>Deskripsi Masalah/Bug:</strong>
                                                    <p class="mt-1 font-medium">{{ $question->options['bug_description'] }}</p>
                                                </div>
                                            @endif
                                            
                                            @if(!empty($question->options['code_snippet']))
                                                <div>
                                                    <p class="text-xs font-bold text-gray-400 uppercase mb-1">Kode Bermasalah (Awal):</p>
                                                    <pre class="p-3 rounded-xl bg-gray-900 text-red-400 font-mono text-xs overflow-x-auto">{{ $question->options['code_snippet'] }}</pre>
                                                </div>
                                            @endif

                                            <div>
                                                <p class="text-xs font-bold text-gray-400 uppercase mb-1">Perbaikan Kode Anda:</p>
                                                <pre class="p-3 rounded-xl bg-gray-900 {{ $isCorrect ? 'text-green-400' : 'text-red-400' }} font-mono text-xs overflow-x-auto">{{ $textAnswer ?: '// Tidak menjawab' }}</pre>
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-emerald-500 uppercase mb-1">Kunci Solusi Benar:</p>
                                                <pre class="p-3 rounded-xl bg-gray-900 text-green-400 font-mono text-xs overflow-x-auto">{{ $question->correct_answer }}</pre>
                                            </div>
                                        </div>

                                    <!-- Render Interactive Video -->
                                    @elseif($question->question_type === 'interactive_video')
                                        @php
                                            $videoQType = $question->options['video_question_type'] ?? 'multiple_choice';
                                        @endphp
                                        <div class="space-y-3">
                                            <div class="p-3 bg-purple-50 rounded-xl text-xs text-purple-700">
                                                Pop-up Kuis Video pada detik ke-<strong>{{ $question->options['timestamp'] ?? 0 }}</strong>
                                            </div>
                                            
                                            @if($videoQType === 'multiple_choice')
                                                <div class="space-y-2">
                                                    @foreach(($question->options['options'] ?? []) as $key => $text)
                                                        @php
                                                            $isSelected = $selected === $key;
                                                            $isRealCorrect = $question->correct_answer === $key;
                                                            
                                                            if ($isRealCorrect) {
                                                                $bgClass = 'bg-emerald-100 border-emerald-300 text-emerald-800';
                                                            } elseif ($isSelected && !$isCorrect) {
                                                                $bgClass = 'bg-red-100 border-red-300 text-red-800';
                                                            } else {
                                                                $bgClass = 'bg-white border-gray-200 text-gray-500';
                                                            }
                                                        @endphp
                                                        <div class="flex items-center p-3 rounded-xl border {{ $bgClass }} text-sm">
                                                            <span class="w-6 h-6 flex flex-shrink-0 items-center justify-center rounded text-xs font-bold mr-3 @if($isRealCorrect) bg-emerald-500 text-white @elseif($isSelected) bg-red-500 text-white @else bg-gray-100 text-gray-400 @endif">{{ $key }}</span>
                                                            <span class="font-bold">{{ $text }}</span>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @elseif($videoQType === 'true_false')
                                                <div class="space-y-2">
                                                    <div class="p-3 rounded-xl border {{ $selected === 'A' ? ($isCorrect ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800') : 'bg-white text-gray-500' }} text-sm font-bold">
                                                        Jawaban Anda: {{ $selected === 'A' ? 'Benar' : 'Salah' }}
                                                    </div>
                                                    <div class="p-3 rounded-xl border bg-emerald-50 text-emerald-800 text-sm font-bold">
                                                        Jawaban Kunci: {{ $question->correct_answer === 'A' ? 'Benar' : 'Salah' }}
                                                    </div>
                                                </div>
                                            @else
                                                <div class="space-y-2 text-sm">
                                                    <div class="p-3 rounded-xl border bg-white flex items-center">
                                                        <span class="font-bold mr-2 text-gray-500">Jawaban Anda:</span>
                                                        <span class="font-mono font-bold {{ $isCorrect ? 'text-emerald-600' : 'text-red-600' }}">{{ $textAnswer ?: '(Kosong)' }}</span>
                                                    </div>
                                                    <div class="p-3 rounded-xl border bg-emerald-50 border-emerald-250 flex items-center">
                                                        <span class="font-bold mr-2 text-emerald-800">Jawaban Benar:</span>
                                                        <span class="font-mono font-bold text-emerald-800">{{ $question->correct_answer }}</span>
                                                    </div>
                                                </div>
                                            @endif
                                        </div>
                                    @endif
                                    
                                    @if($question->feedback)
                                        <div class="p-3 bg-yellow-50/50 rounded-xl border border-yellow-100 text-xs text-yellow-800 mt-4 leading-relaxed">
                                            <strong>Pembahasan:</strong> {{ $question->feedback }}
                                        </div>
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
