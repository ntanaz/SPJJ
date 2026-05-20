<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <h2 class="font-bold text-2xl text-gray-800 tracking-tight flex items-center gap-3">
                <svg class="w-8 h-8 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                Ujian Berlangsung: {{ $quiz->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <!-- Progress Bar Visual & Timer -->
        <div class="mb-8" x-data="{ total: {{ $quiz->questions->count() }}, answered: 0 }" x-init="
            const inputs = document.querySelectorAll('input[type=radio], input[type=text], textarea');
            const updateCount = () => { 
                const radios = document.querySelectorAll('input[type=radio]:checked').length;
                const texts = Array.from(document.querySelectorAll('input[type=text], textarea')).filter(el => el.value.trim() !== '').length;
                answered = radios + texts; 
            };
            inputs.forEach(el => el.addEventListener('change', updateCount));
            inputs.forEach(el => el.addEventListener('input', updateCount));
            updateCount();
        ">
            <div class="flex justify-between items-end mb-2">
                <div class="text-xs font-bold text-gray-500 uppercase tracking-widest">
                    <span>Progress Pengerjaan</span>
                    <span x-text="answered + ' / ' + total + ' Soal'" class="ml-2"></span>
                </div>
                
                @if($quiz->time_limit_minutes)
                @php
                    $endTime = \Carbon\Carbon::parse($attempt->started_at)->addMinutes($quiz->time_limit_minutes);
                @endphp
                <div class="bg-red-50 text-red-600 px-4 py-2 rounded-xl border border-red-100 font-bold flex items-center gap-2 shadow-sm" id="timer-container" data-endtime="{{ $endTime->timestamp * 1000 }}">
                    <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span id="countdown-timer">--:--:--</span>
                </div>
                
                <script>
                    document.addEventListener('DOMContentLoaded', function() {
                        const container = document.getElementById('timer-container');
                        const timerDisplay = document.getElementById('countdown-timer');
                        const endTime = parseInt(container.dataset.endtime);
                        const form = document.getElementById('quiz-form');
                        
                        const updateTimer = setInterval(function() {
                            const now = new Date().getTime();
                            const distance = endTime - now;
                            
                            if (distance <= 0) {
                                clearInterval(updateTimer);
                                timerDisplay.innerHTML = "WAKTU HABIS";
                                container.classList.replace('bg-red-50', 'bg-red-600');
                                container.classList.replace('text-red-600', 'text-white');
                                
                                // Auto submit
                                alert('Waktu pengerjaan telah habis. Jawaban akan dikumpulkan secara otomatis.');
                                form.submit();
                                return;
                            }
                            
                            const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                            const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                            const seconds = Math.floor((distance % (1000 * 60)) / 1000);
                            
                            timerDisplay.innerHTML = 
                                (hours > 0 ? (hours < 10 ? "0" + hours : hours) + ":" : "") + 
                                (minutes < 10 ? "0" + minutes : minutes) + ":" + 
                                (seconds < 10 ? "0" + seconds : seconds);
                                
                            // Warning colors if less than 5 minutes
                            if (distance < 5 * 60 * 1000) {
                                container.classList.add('animate-bounce');
                            }
                        }, 1000);
                    });
                </script>
                @endif
            </div>
            
            <div class="w-full bg-gray-200 rounded-full h-3">
                <div class="bg-gradient-to-r from-amber-400 to-orange-500 h-3 rounded-full transition-all duration-500" :style="'width: ' + ((answered/total)*100) + '%'"></div>
            </div>
        </div>

        <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
            <form action="{{ route('student.quizzes.submit', $attempt) }}" method="POST" id="quiz-form">
                @csrf
                <div class="p-8 sm:p-12">
                    <div class="space-y-12">
                        @foreach($quiz->questions as $index => $question)
                            <div class="relative pl-0 sm:pl-16">
                                <!-- Number Indicator -->
                                <div class="hidden sm:flex absolute left-0 top-0 w-12 h-12 bg-amber-100 text-amber-600 rounded-full font-black text-xl items-center justify-center border-2 border-white shadow-sm ring-4 ring-amber-50">
                                    {{ $index + 1 }}
                                </div>
                                <div class="sm:hidden w-10 h-10 mb-4 bg-amber-100 text-amber-600 rounded-full font-black flex items-center justify-center text-lg">
                                    {{ $index + 1 }}
                                </div>
                                
                                <!-- Question Text -->
                                <h4 class="text-xl font-bold text-gray-800 leading-relaxed mb-6">{{ $question->question }}</h4>
                                
                                <!-- Options / Inputs depending on type -->
                                
                                <!-- 1. Multiple Choice -->
                                @if($question->question_type === 'multiple_choice')
                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                        @foreach($question->options as $key => $option)
                                            <label class="group relative flex cursor-pointer rounded-2xl border-2 border-gray-200 bg-white p-5 hover:border-amber-300 hover:bg-amber-50 focus-within:ring-4 focus-within:ring-amber-100 transition-all">
                                                <div class="flex w-full items-center justify-between">
                                                    <div class="flex items-center gap-4">
                                                        <div class="relative flex h-6 w-6 items-center justify-center">
                                                            <input type="radio" name="answers[{{ $question->id }}]" value="{{ $key }}" class="peer h-6 w-6 border-2 border-gray-300 text-amber-600 focus:ring-amber-500 rounded-full bg-white checked:border-amber-500 checked:bg-amber-500 transition-colors">
                                                        </div>
                                                        <div class="text-sm font-bold text-gray-800">
                                                            <span class="mr-2 inline-block px-2 py-1 bg-gray-100 text-gray-500 rounded-lg text-xs">{{ $key }}</span>
                                                            {{ $option }}
                                                        </div>
                                                    </div>
                                                </div>
                                            </label>
                                        @endforeach
                                    </div>
                                
                                <!-- 2. True / False -->
                                @elseif($question->question_type === 'true_false')
                                    <div class="flex gap-6">
                                        <label class="group relative flex cursor-pointer rounded-2xl border-2 border-gray-200 bg-white px-8 py-5 hover:border-amber-300 hover:bg-amber-50 focus-within:ring-4 focus-within:ring-amber-100 transition-all">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="A" class="peer h-6 w-6 mr-3 border-2 border-gray-300 text-amber-600 focus:ring-amber-500 rounded-full bg-white checked:border-amber-500 checked:bg-amber-500 transition-colors">
                                            <span class="text-sm font-bold text-gray-800">Benar</span>
                                        </label>
                                        <label class="group relative flex cursor-pointer rounded-2xl border-2 border-gray-200 bg-white px-8 py-5 hover:border-amber-300 hover:bg-amber-50 focus-within:ring-4 focus-within:ring-amber-100 transition-all">
                                            <input type="radio" name="answers[{{ $question->id }}]" value="B" class="peer h-6 w-6 mr-3 border-2 border-gray-300 text-amber-600 focus:ring-amber-500 rounded-full bg-white checked:border-amber-500 checked:bg-amber-500 transition-colors">
                                            <span class="text-sm font-bold text-gray-800">Salah</span>
                                        </label>
                                    </div>
                                
                                <!-- 3. Short Answer -->
                                @elseif($question->question_type === 'short_answer')
                                    <div class="w-full">
                                        <input type="text" name="answers[{{ $question->id }}]" placeholder="Ketik jawaban singkat Anda di sini..." class="w-full rounded-2xl border-2 border-gray-200 px-5 py-4 focus:border-amber-500 focus:ring focus:ring-amber-200 text-sm font-medium">
                                    </div>
                                
                                <!-- 4. Fill in the Blank -->
                                @elseif($question->question_type === 'fill_blank')
                                    <div class="w-full">
                                        <input type="text" name="answers[{{ $question->id }}]" placeholder="Masukkan kata kunci/kode untuk melengkapi..." class="w-full rounded-2xl border-2 border-gray-200 px-5 py-4 focus:border-amber-500 focus:ring focus:ring-amber-200 text-sm font-mono">
                                    </div>

                                <!-- 5. Reflection -->
                                @elseif($question->question_type === 'reflection')
                                    <div class="w-full">
                                        <textarea name="answers[{{ $question->id }}]" rows="4" placeholder="Ketik refleksi atau pendapat bebas Anda di sini..." class="w-full rounded-2xl border-2 border-gray-200 px-5 py-4 focus:border-amber-500 focus:ring focus:ring-amber-200 text-sm font-medium"></textarea>
                                    </div>

                                <!-- 6. Debugging -->
                                @elseif($question->question_type === 'debugging')
                                    <div class="w-full space-y-4">
                                        <textarea name="answers[{{ $question->id }}]" rows="6" placeholder="Tulis perbaikan kode pemrograman Anda di sini..." class="w-full rounded-2xl border-2 border-gray-200 bg-gray-900 text-green-400 font-mono text-sm px-5 py-4 focus:border-amber-500 focus:ring focus:ring-amber-200"></textarea>
                                    </div>

                                <!-- 7. Interactive Video -->
                                @elseif($question->question_type === 'interactive_video')
                                    <div class="space-y-4" x-data="{ paused: false, answered: false, currentTime: 0 }">
                                        <div class="relative rounded-2xl overflow-hidden shadow-md bg-black">
                                            <video id="video-{{ $question->id }}" class="w-full max-h-[400px]" controls
                                                   @timeupdate="currentTime = $el.currentTime; if (currentTime >= {{ $question->options['timestamp'] ?? 0 }} && !answered) { $el.pause(); paused = true; }">
                                                <source src="{{ $question->options['video_url'] ?? '' }}" type="video/mp4">
                                                Your browser does not support the video tag.
                                            </video>
                                        </div>
                                        
                                        <div x-show="paused" class="p-5 bg-amber-50 border border-amber-200 rounded-2xl shadow-sm transition-all duration-300">
                                            <p class="font-bold text-amber-900 mb-3 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-amber-600 animate-bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 5.25v13.5m-7.5-13.5v13.5" /></svg>
                                                Video Dijeda Otomatis: Jawab kuis video di bawah ini
                                            </p>
                                            
                                            <div class="bg-white p-4 rounded-xl border border-amber-100">
                                                <!-- MC sub-type inside video -->
                                                @if(($question->options['video_question_type'] ?? '') === 'multiple_choice')
                                                    <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                                        @foreach(($question->options['options'] ?? []) as $key => $option)
                                                            <label class="flex items-center gap-3 p-3 rounded-xl border border-gray-200 bg-white hover:bg-amber-50 cursor-pointer transition-colors">
                                                                <input type="radio" name="answers[{{ $question->id }}]" value="{{ $key }}" @change="answered = true; paused = false; document.getElementById('video-{{ $question->id }}').play()" class="text-amber-600 focus:ring-amber-500">
                                                                <span class="font-bold text-sm text-gray-700">{{ $key }}. {{ $option }}</span>
                                                            </label>
                                                        @endforeach
                                                    </div>
                                                
                                                <!-- TF sub-type inside video -->
                                                @elseif(($question->options['video_question_type'] ?? '') === 'true_false')
                                                    <div class="flex gap-4">
                                                        <label class="flex items-center gap-2 cursor-pointer p-3 border border-gray-200 bg-white rounded-xl hover:bg-amber-50 transition-colors">
                                                            <input type="radio" name="answers[{{ $question->id }}]" value="A" @change="answered = true; paused = false; document.getElementById('video-{{ $question->id }}').play()" class="text-amber-600 focus:ring-amber-500">
                                                            <span class="font-bold text-sm text-gray-700">Benar</span>
                                                        </label>
                                                        <label class="flex items-center gap-2 cursor-pointer p-3 border border-gray-200 bg-white rounded-xl hover:bg-amber-50 transition-colors">
                                                            <input type="radio" name="answers[{{ $question->id }}]" value="B" @change="answered = true; paused = false; document.getElementById('video-{{ $question->id }}').play()" class="text-amber-600 focus:ring-amber-500">
                                                            <span class="font-bold text-sm text-gray-700">Salah</span>
                                                        </label>
                                                    </div>
                                                
                                                <!-- SA sub-type inside video -->
                                                @else
                                                    <div class="space-y-3">
                                                        <input type="text" id="video-sa-{{ $question->id }}" name="answers[{{ $question->id }}]" placeholder="Ketik jawaban Anda..." class="w-full rounded-xl border-gray-300 shadow-sm focus:border-amber-500 focus:ring focus:ring-amber-200">
                                                        <button type="button" @click="if (document.getElementById('video-sa-{{ $question->id }}').value.trim() !== '') { answered = true; paused = false; document.getElementById('video-{{ $question->id }}').play() }" class="px-5 py-2 bg-amber-600 hover:bg-amber-700 text-white rounded-xl text-sm font-bold transition-colors">
                                                            Kirim Jawaban & Lanjutkan
                                                        </button>
                                                    </div>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                @endif
                            </div>
                            
                            @if(!$loop->last)
                                <hr class="border-gray-100 border-2 border-dashed my-8">
                            @endif
                        @endforeach
                    </div>
                </div>

                <div class="bg-gray-50 px-8 py-6 border-t border-gray-100 flex items-center justify-between sm:px-12 rounded-b-3xl">
                    <p class="text-sm text-gray-500 font-medium hidden sm:block">Periksa kembali jawaban Anda sebelum mengumpulkan.</p>
                    <button type="submit" onclick="return confirm('Apakah Anda yakin ingin mengumpulkan Kuis ini? Semua jawaban yang terkirim tidak dapat diubah kembali.')" class="w-full sm:w-auto px-10 py-4 bg-amber-600 hover:bg-amber-700 active:scale-95 transition-transform text-white text-lg font-black rounded-xl shadow-lg shadow-amber-600/30 flex items-center justify-center gap-3">
                        <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                        Selesai & Kumpulkan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
