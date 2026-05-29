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

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-8" x-data="{ 
        questionType: '{{ session('error_edit_question_id') ? 'multiple_choice' : (old('question_type') ?: 'multiple_choice') }}', 
        videoQType: '{{ session('error_edit_question_id') ? 'multiple_choice' : (old('video_question_type') ?: 'multiple_choice') }}', 
        showEditModal: {{ session('error_edit_question_id') ? 'true' : 'false' }}, 
        showPreviewModal: false,
        editQuestion: { 
            id: {{ session('error_edit_question_id') ? (int) session('error_edit_question_id') : 'null' }}, 
            question_type: {{ json_encode(session('error_edit_question_id') ? old('question_type', 'multiple_choice') : 'multiple_choice') }}, 
            question: {{ json_encode(session('error_edit_question_id') ? old('question', '') : '') }}, 
            points: {{ (int) (session('error_edit_question_id') ? (old('points') ?: 10) : 10) }}, 
            feedback: {{ json_encode(session('error_edit_question_id') ? old('feedback', '') : '') }}, 
            correct_answer: {{ json_encode(session('error_edit_question_id') ? old('correct_answer', '') : '') }}, 
            options: { 
                A: {{ json_encode((session('error_edit_question_id') && old('question_type') === 'multiple_choice') ? old('option_a', '') : '') }}, 
                B: {{ json_encode((session('error_edit_question_id') && old('question_type') === 'multiple_choice') ? old('option_b', '') : '') }}, 
                C: {{ json_encode((session('error_edit_question_id') && old('question_type') === 'multiple_choice') ? old('option_c', '') : '') }}, 
                D: {{ json_encode((session('error_edit_question_id') && old('question_type') === 'multiple_choice') ? old('option_d', '') : '') }}, 
                E: {{ json_encode((session('error_edit_question_id') && old('question_type') === 'multiple_choice') ? old('option_e', '') : '') }} 
            }, 
            video_url: {{ json_encode((session('error_edit_question_id') && old('question_type') === 'interactive_video') ? old('video_url', '') : '') }}, 
            timestamp: {{ json_encode((session('error_edit_question_id') && old('question_type') === 'interactive_video') ? old('timestamp', '') : '') }}, 
            video_question_type: {{ json_encode((session('error_edit_question_id') && old('question_type') === 'interactive_video') ? old('video_question_type', 'multiple_choice') : 'multiple_choice') }}, 
            video_options: { 
                A: {{ json_encode((session('error_edit_question_id') && old('question_type') === 'interactive_video' && old('video_question_type') === 'multiple_choice') ? old('option_a', '') : '') }}, 
                B: {{ json_encode((session('error_edit_question_id') && old('question_type') === 'interactive_video' && old('video_question_type') === 'multiple_choice') ? old('option_b', '') : '') }}, 
                C: {{ json_encode((session('error_edit_question_id') && old('question_type') === 'interactive_video' && old('video_question_type') === 'multiple_choice') ? old('option_c', '') : '') }}, 
                D: {{ json_encode((session('error_edit_question_id') && old('question_type') === 'interactive_video' && old('video_question_type') === 'multiple_choice') ? old('option_d', '') : '') }} 
            },
            keywords: {{ json_encode((session('error_edit_question_id') && old('question_type') === 'short_answer') ? old('keywords', '') : '') }},
            code_template: {{ json_encode((session('error_edit_question_id') && old('question_type') === 'fill_blank') ? old('code_template', '') : '') }},
            blank_placeholder: {{ json_encode((session('error_edit_question_id') && old('question_type') === 'fill_blank') ? old('blank_placeholder', '[blank]') : '[blank]') }},
            feedback_correct: {{ json_encode((session('error_edit_question_id') && old('question_type') === 'fill_blank') ? old('feedback_correct', '') : '') }},
            feedback_incorrect: {{ json_encode((session('error_edit_question_id') && old('question_type') === 'fill_blank') ? old('feedback_incorrect', '') : '') }},
            max_attempts: {{ (int) ((session('error_edit_question_id') && old('question_type') === 'fill_blank') ? (old('max_attempts') ?: 3) : 3) }},
            code_snippet: {{ json_encode((session('error_edit_question_id') && old('question_type') === 'debugging') ? old('code_snippet', '') : '') }},
            bug_description: {{ json_encode((session('error_edit_question_id') && old('question_type') === 'debugging') ? old('bug_description', '') : '') }}
        } 
    }">
        <!-- Error / Validasi -->
        @if ($errors->any())
            <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl flex flex-col shadow-sm gap-2">
                <div class="flex items-center">
                    <svg class="w-6 h-6 mr-3 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    <span class="font-bold">Gagal menyimpan data kuis. Silakan periksa kolom berikut:</span>
                </div>
                <ul class="list-disc list-inside text-sm pl-9 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

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

                    <form action="{{ route('quizzes.questions.store', $quiz) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
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
                            <textarea name="question" rows="3" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm font-medium" required placeholder="Ketik soal atau instruksi disini...">{{ session('error_edit_question_id') ? '' : old('question') }}</textarea>
                        </div>

                        <!-- 1. MULTIPLE CHOICE -->
                        <div x-show="questionType === 'multiple_choice'" class="space-y-3 pt-2 border-t border-gray-100">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi A</label>
                                <input type="text" name="option_a" :disabled="questionType !== 'multiple_choice'" x-bind:required="questionType === 'multiple_choice'" value="{{ session('error_edit_question_id') ? '' : old('option_a') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi B</label>
                                <input type="text" name="option_b" :disabled="questionType !== 'multiple_choice'" x-bind:required="questionType === 'multiple_choice'" value="{{ session('error_edit_question_id') ? '' : old('option_b') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi C</label>
                                <input type="text" name="option_c" :disabled="questionType !== 'multiple_choice'" x-bind:required="questionType === 'multiple_choice'" value="{{ session('error_edit_question_id') ? '' : old('option_c') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi D</label>
                                <input type="text" name="option_d" :disabled="questionType !== 'multiple_choice'" x-bind:required="questionType === 'multiple_choice'" value="{{ session('error_edit_question_id') ? '' : old('option_d') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi E (Opsional)</label>
                                <input type="text" name="option_e" :disabled="questionType !== 'multiple_choice'" value="{{ session('error_edit_question_id') ? '' : old('option_e') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">
                            </div>

                            <div class="pt-2">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Jawaban Benar</label>
                                <div class="flex gap-4 flex-wrap">
                                    @foreach(['A', 'B', 'C', 'D', 'E'] as $opt)
                                        <label class="flex items-center gap-2 cursor-pointer p-2 border border-gray-200 rounded-lg hover:bg-indigo-50 hover:border-indigo-200 transition-colors">
                                            <input type="radio" name="correct_answer" :disabled="questionType !== 'multiple_choice'" value="{{ $opt }}" x-bind:required="questionType === 'multiple_choice'" {{ (!session('error_edit_question_id') && old('correct_answer') === $opt) ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
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
                                    <input type="radio" name="correct_answer" :disabled="questionType !== 'true_false'" value="A" x-bind:required="questionType === 'true_false'" {{ (!session('error_edit_question_id') && old('correct_answer') === 'A') ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                                    <span class="font-bold text-sm text-gray-700">Benar</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 border border-gray-200 rounded-xl hover:bg-indigo-50 hover:border-indigo-200 transition-colors">
                                    <input type="radio" name="correct_answer" :disabled="questionType !== 'true_false'" value="B" x-bind:required="questionType === 'true_false'" {{ (!session('error_edit_question_id') && old('correct_answer') === 'B') ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                                    <span class="font-bold text-sm text-gray-700">Salah</span>
                                </label>
                            </div>
                        </div>

                        <!-- 3. SHORT ANSWER -->
                        <div x-show="questionType === 'short_answer'" class="space-y-3 pt-2 border-t border-gray-100">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Kunci Jawaban Singkat</label>
                                <input type="text" name="correct_answer" :disabled="questionType !== 'short_answer'" placeholder="Masukkan jawaban yang benar..." x-bind:required="questionType === 'short_answer'" value="{{ session('error_edit_question_id') ? '' : old('correct_answer') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">
                                <p class="text-xs text-gray-500 mt-1">Jawaban siswa akan dicocokkan secara case-insensitive.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Keyword Jawaban (Opsional, pisahkan dengan koma)</label>
                                <input type="text" name="keywords" :disabled="questionType !== 'short_answer'" placeholder="Cth: py, python3, scripting" value="{{ session('error_edit_question_id') ? '' : old('keywords') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">
                                <p class="text-xs text-gray-500 mt-1">Jika diisi, jawaban siswa yang mengandung salah satu kata kunci di atas juga akan dianggap benar.</p>
                            </div>
                        </div>

                        <!-- 4. FILL IN THE BLANK (CODING) -->
                        <div x-show="questionType === 'fill_blank'" class="space-y-3 pt-2 border-t border-gray-100">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Code Template</label>
                                <textarea name="code_template" :disabled="questionType !== 'fill_blank'" x-bind:required="questionType === 'fill_blank'" rows="4" placeholder="def greet(name):&#10;    [blank]('Hello ' + name)" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm font-mono">{{ session('error_edit_question_id') ? '' : old('code_template') }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Tulis template kode di mana siswa harus mengisi bagian yang rumpang.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Placeholder Blank</label>
                                <input type="text" name="blank_placeholder" :disabled="questionType !== 'fill_blank'" placeholder="[blank]" value="{{ session('error_edit_question_id') ? '' : old('blank_placeholder', '[blank]') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm font-mono">
                                <p class="text-xs text-gray-500 mt-1">String penanda kekosongan pada template kode di atas (default: [blank]).</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Kunci Jawaban Rumpang (Isian Benar)</label>
                                <input type="text" name="correct_answer" :disabled="questionType !== 'fill_blank'" placeholder="Cth: print" x-bind:required="questionType === 'fill_blank'" value="{{ session('error_edit_question_id') ? '' : old('correct_answer') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">
                                <p class="text-xs text-gray-500 mt-1">Gunakan kata yang tepat untuk mengisi kekosongan kode di atas.</p>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Feedback Jawaban Benar (Opsional)</label>
                                <textarea name="feedback_correct" :disabled="questionType !== 'fill_blank'" rows="2" placeholder="Luar biasa! Penggunaan print() sudah benar." class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">{{ session('error_edit_question_id') ? '' : old('feedback_correct') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Feedback Jawaban Salah (Opsional)</label>
                                <textarea name="feedback_incorrect" :disabled="questionType !== 'fill_blank'" rows="2" placeholder="Kurang tepat. Ingat fungsi bawaan Python untuk menampilkan output." class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">{{ session('error_edit_question_id') ? '' : old('feedback_incorrect') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Batas Percobaan (Max Attempt)</label>
                                <input type="number" name="max_attempts" :disabled="questionType !== 'fill_blank'" min="1" value="{{ session('error_edit_question_id') ? 3 : old('max_attempts', 3) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm font-bold">
                            </div>
                        </div>

                        <!-- 5. REFLECTION (No correct answer required) -->
                        <div x-show="questionType === 'reflection'" class="pt-2 border-t border-gray-100">
                            <p class="text-xs text-gray-500">Pertanyaan reflektif tidak membutuhkan kunci jawaban benar. Siswa akan mendapatkan poin penuh jika mengisi form refleksi.</p>
                        </div>

                        <!-- 6. DEBUGGING -->
                        <div x-show="questionType === 'debugging'" class="space-y-3 pt-2 border-t border-gray-100">
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Kode Snippet Bermasalah (Buggy Code)</label>
                                <textarea name="code_snippet" :disabled="questionType !== 'debugging'" x-bind:required="questionType === 'debugging'" rows="4" placeholder="def add(a, b)&#10;return a + b" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm font-mono">{{ session('error_edit_question_id') ? '' : old('code_snippet') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Bug / Masalah</label>
                                <textarea name="bug_description" :disabled="questionType !== 'debugging'" x-bind:required="questionType === 'debugging'" rows="2" placeholder="Fungsi add() memiliki kesalahan sintaksis pada tanda titik dua dan indentasi." class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">{{ session('error_edit_question_id') ? '' : old('bug_description') }}</textarea>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Kunci Solusi Kode Benar</label>
                                <textarea name="correct_answer" :disabled="questionType !== 'debugging'" rows="4" placeholder="def add(a, b):&#10;    return a + b" x-bind:required="questionType === 'debugging'" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm font-mono">{{ session('error_edit_question_id') ? '' : old('correct_answer') }}</textarea>
                                <p class="text-xs text-gray-500 mt-1">Jawaban kode siswa akan dicocokkan dengan mengabaikan whitespace.</p>
                            </div>
                        </div>

                        <!-- 7. INTERACTIVE VIDEO -->
                        <div x-show="questionType === 'interactive_video'" class="space-y-4 pt-2 border-t border-gray-100">
                            <div class="p-3 bg-indigo-50/55 border border-indigo-100 rounded-xl space-y-2">
                                <label class="block text-sm font-bold text-gray-700">Pilih / Unggah Video</label>
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-gray-500 uppercase">Pilih Video Terunggah</label>
                                    <select name="video_url_select" :disabled="questionType !== 'interactive_video'" class="w-full rounded-xl border-gray-300 shadow-sm text-sm">
                                        <option value="">-- Pilih Video Pembelajaran --</option>
                                        @foreach($uploadedVideos as $v)
                                            <option value="{{ $v->video_path }}" {{ old('video_url_select') === $v->video_path ? 'selected' : '' }}>{{ $v->title }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div class="relative flex py-2 items-center">
                                    <div class="flex-grow border-t border-gray-300"></div>
                                    <span class="flex-shrink mx-4 text-gray-400 text-xs font-bold uppercase">Atau</span>
                                    <div class="flex-grow border-t border-gray-300"></div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-gray-500 uppercase">Unggah File Video Baru</label>
                                    <input type="file" name="video_file" :disabled="questionType !== 'interactive_video'" class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200">
                                </div>
                                <div class="relative flex py-2 items-center">
                                    <div class="flex-grow border-t border-gray-300"></div>
                                    <span class="flex-shrink mx-4 text-gray-400 text-xs font-bold uppercase">Atau</span>
                                    <div class="flex-grow border-t border-gray-300"></div>
                                </div>
                                <div class="space-y-2">
                                    <label class="block text-xs font-bold text-gray-500 uppercase">Masukkan URL Video Manual</label>
                                    <input type="text" name="video_url" :disabled="questionType !== 'interactive_video'" placeholder="Cth: /videos/html_intro.mp4 atau link Youtube" value="{{ session('error_edit_question_id') ? '' : old('video_url') }}" class="w-full rounded-xl border-gray-300 shadow-sm text-sm">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-1">Timestamp Muncul (detik)</label>
                                <input type="number" name="timestamp" :disabled="questionType !== 'interactive_video'" min="0" placeholder="Cth: 45" x-bind:required="questionType === 'interactive_video'" value="{{ session('error_edit_question_id') ? '' : old('timestamp') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Tipe Soal Video</label>
                                <select name="video_question_type" :disabled="questionType !== 'interactive_video'" x-model="videoQType" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm">
                                    <option value="multiple_choice">Pilihan Ganda (MC)</option>
                                    <option value="true_false">Benar / Salah (TF)</option>
                                    <option value="short_answer">Jawaban Teks Singkat</option>
                                </select>
                            </div>

                            <!-- Opsi Sub-Tipe Video MC -->
                            <div x-show="videoQType === 'multiple_choice'" class="space-y-3 pl-4 border-l-2 border-indigo-100">
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi A</label>
                                    <input type="text" name="option_a" :disabled="!(questionType === 'interactive_video' && videoQType === 'multiple_choice')" x-bind:required="questionType === 'interactive_video' && videoQType === 'multiple_choice'" value="{{ session('error_edit_question_id') ? '' : old('option_a') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi B</label>
                                    <input type="text" name="option_b" :disabled="!(questionType === 'interactive_video' && videoQType === 'multiple_choice')" x-bind:required="questionType === 'interactive_video' && videoQType === 'multiple_choice'" value="{{ session('error_edit_question_id') ? '' : old('option_b') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi C</label>
                                    <input type="text" name="option_c" :disabled="!(questionType === 'interactive_video' && videoQType === 'multiple_choice')" x-bind:required="questionType === 'interactive_video' && videoQType === 'multiple_choice'" value="{{ session('error_edit_question_id') ? '' : old('option_c') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi D</label>
                                    <input type="text" name="option_d" :disabled="!(questionType === 'interactive_video' && videoQType === 'multiple_choice')" x-bind:required="questionType === 'interactive_video' && videoQType === 'multiple_choice'" value="{{ session('error_edit_question_id') ? '' : old('option_d') }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-gray-700 mb-2">Jawaban Benar</label>
                                    <div class="flex gap-4">
                                        @foreach(['A', 'B', 'C', 'D'] as $opt)
                                            <label class="flex items-center gap-2 cursor-pointer p-2 border border-gray-200 rounded-lg">
                                                <input type="radio" name="correct_answer" :disabled="!(questionType === 'interactive_video' && videoQType === 'multiple_choice')" value="{{ $opt }}" x-bind:required="questionType === 'interactive_video' && videoQType === 'multiple_choice'" {{ (!session('error_edit_question_id') && old('correct_answer') === $opt) ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
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
                                        <input type="radio" name="correct_answer" :disabled="!(questionType === 'interactive_video' && videoQType === 'true_false')" value="A" x-bind:required="questionType === 'interactive_video' && videoQType === 'true_false'" {{ (!session('error_edit_question_id') && old('correct_answer') === 'A') ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                                        <span class="font-bold text-sm text-gray-700">Benar</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer p-3 border border-gray-200 rounded-xl">
                                        <input type="radio" name="correct_answer" :disabled="!(questionType === 'interactive_video' && videoQType === 'true_false')" value="B" x-bind:required="questionType === 'interactive_video' && videoQType === 'true_false'" {{ (!session('error_edit_question_id') && old('correct_answer') === 'B') ? 'checked' : '' }} class="text-indigo-600 focus:ring-indigo-500">
                                        <span class="font-bold text-sm text-gray-700">Salah</span>
                                    </label>
                                </div>
                            </div>

                            <!-- Opsi Sub-Tipe Video Short Answer -->
                            <div x-show="videoQType === 'short_answer'" class="space-y-3 pl-4 border-l-2 border-indigo-100">
                                <label class="block text-sm font-bold text-gray-700 mb-2">Kunci Jawaban Singkat Video</label>
                                <input type="text" name="correct_answer" :disabled="!(questionType === 'interactive_video' && videoQType === 'short_answer')" placeholder="Masukkan jawaban video..." x-bind:required="questionType === 'interactive_video' && videoQType === 'short_answer'" value="{{ session('error_edit_question_id') ? '' : old('correct_answer') }}" class="w-full rounded-xl border-gray-300 shadow-sm text-sm">
                            </div>
                        </div>

                        <!-- Feedback Pembahasan -->
                        <div class="pt-2 border-t border-gray-100">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Pembahasan / Feedback Jawaban (Opsional)</label>
                            <textarea name="feedback" rows="2" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm" placeholder="Tulis penjelasan jawaban di sini...">{{ session('error_edit_question_id') ? '' : old('feedback') }}</textarea>
                        </div>

                        <!-- Poin Soal -->
                        <div class="pt-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Beban Nilai / Poin</label>
                            <input type="number" name="points" min="1" value="{{ session('error_edit_question_id') ? 10 : old('points', 10) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm font-bold">
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
                    <div class="relative z-10 text-right flex flex-col sm:flex-row items-center gap-4">
                        <div class="text-right">
                            <p class="text-sm text-indigo-100 font-medium">Beban Skor Maksimal:</p>
                            <p class="text-2xl font-black">{{ $quiz->questions->sum('points') }} Poin</p>
                        </div>
                        @if($quiz->questions->count() > 0)
                            <button type="button" @click="showPreviewModal = true" class="inline-flex items-center gap-2 px-5 py-2.5 bg-white text-indigo-700 hover:bg-indigo-50 rounded-2xl text-sm font-bold shadow-md hover:shadow-lg transition-all focus:outline-none focus:ring-2 focus:ring-indigo-300 transform hover:-translate-y-0.5 active:scale-95 duration-200">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                Pratinjau Kuis (Siswa)
                            </button>
                        @endif
                    </div>
                </div>

                <div class="space-y-4">
                    @forelse($quiz->questions as $index => $question)
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 relative group">
                            <div class="absolute top-6 right-6 opacity-0 group-hover:opacity-100 transition-opacity flex items-center gap-1">
                                <button type="button" 
                                    @click="
                                        let q = {{ json_encode($question) }};
                                        editQuestion = {
                                            id: q.id,
                                            question_type: q.question_type,
                                            question: q.question,
                                            points: q.points,
                                            feedback: q.feedback,
                                            correct_answer: q.correct_answer,
                                            options: Object.assign({ A: '', B: '', C: '', D: '', E: '' }, q.options || {}),
                                            video_url: (q.options && q.options.video_url) ? q.options.video_url : '',
                                            timestamp: (q.options && q.options.timestamp) ? q.options.timestamp : '',
                                            video_question_type: (q.options && q.options.video_question_type) ? q.options.video_question_type : 'multiple_choice',
                                            video_options: Object.assign({ A: '', B: '', C: '', D: '' }, (q.options && q.options.options) || {}),
                                            keywords: (q.options && q.options.keywords) ? q.options.keywords : '',
                                            code_template: (q.options && q.options.code_template) ? q.options.code_template : '',
                                            blank_placeholder: (q.options && q.options.blank_placeholder) ? q.options.blank_placeholder : '[blank]',
                                            feedback_correct: (q.options && q.options.feedback_correct) ? q.options.feedback_correct : '',
                                            feedback_incorrect: (q.options && q.options.feedback_incorrect) ? q.options.feedback_incorrect : '',
                                            max_attempts: (q.options && q.options.max_attempts) ? q.options.max_attempts : 3,
                                            code_snippet: (q.options && q.options.code_snippet) ? q.options.code_snippet : '',
                                            bug_description: (q.options && q.options.bug_description) ? q.options.bug_description : ''
                                        };
                                        showEditModal = true;
                                    "
                                    class="p-2 text-amber-500 hover:bg-amber-50 rounded-lg transition-colors" 
                                    title="Edit Soal">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <form action="{{ route('quizzes.questions.destroy', $question) }}" method="POST" onsubmit="return confirm('Hapus soal ini?');" class="inline">
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

                                    <!-- Render Short Answer -->
                                    @if($question->question_type === 'short_answer')
                                        <div class="space-y-2 mb-4">
                                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-150 text-sm font-semibold text-gray-700">
                                                Kunci Jawaban Benar: <span class="text-indigo-600 font-mono">{{ $question->correct_answer }}</span>
                                            </div>
                                            @if(!empty($question->options['keywords']))
                                                <div class="p-3 bg-gray-50 rounded-xl border border-gray-150 text-sm text-gray-600">
                                                    Keywords: <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 rounded font-mono text-xs">{{ $question->options['keywords'] }}</span>
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Render Fill Blank -->
                                    @if($question->question_type === 'fill_blank')
                                        <div class="space-y-2 mb-4">
                                            <div class="p-3 bg-gray-50 rounded-xl border border-gray-150 text-sm font-semibold text-gray-700">
                                                Kunci Jawaban Rumpang: <span class="text-indigo-600 font-mono">{{ $question->correct_answer }}</span>
                                            </div>
                                            @if(!empty($question->options['code_template']))
                                                <div class="space-y-1">
                                                    <p class="text-xs font-bold text-gray-400 uppercase">Template Kode:</p>
                                                    <pre class="p-3 bg-gray-900 text-yellow-300 rounded-xl font-mono text-xs overflow-x-auto">{{ $question->options['code_template'] }}</pre>
                                                </div>
                                            @endif
                                            <div class="grid grid-cols-2 gap-2 text-xs text-gray-500">
                                                <div>Placeholder: <span class="font-mono bg-gray-100 px-1.5 py-0.5 rounded text-gray-700">{{ $question->options['blank_placeholder'] ?? '[blank]' }}</span></div>
                                                <div>Batas Percobaan: <span class="font-bold text-gray-700">{{ $question->options['max_attempts'] ?? 3 }} kali</span></div>
                                            </div>
                                            @if(!empty($question->options['feedback_correct']))
                                                <div class="p-2.5 bg-emerald-50 border border-emerald-100 text-emerald-800 text-xs rounded-xl">
                                                    <strong>Feedback Benar:</strong> {{ $question->options['feedback_correct'] }}
                                                </div>
                                            @endif
                                            @if(!empty($question->options['feedback_incorrect']))
                                                <div class="p-2.5 bg-red-50 border border-red-100 text-red-800 text-xs rounded-xl">
                                                    <strong>Feedback Salah:</strong> {{ $question->options['feedback_incorrect'] }}
                                                </div>
                                            @endif
                                        </div>
                                    @endif

                                    <!-- Render Debugging solutions -->
                                    @if($question->question_type === 'debugging')
                                        <div class="space-y-3 mb-4">
                                            @if(!empty($question->options['bug_description']))
                                                <div class="p-3 bg-rose-50 border border-rose-100 rounded-xl text-rose-800 text-sm">
                                                    <strong>Deskripsi Bug:</strong> {{ $question->options['bug_description'] }}
                                                </div>
                                            @endif
                                            @if(!empty($question->options['code_snippet']))
                                                <div class="space-y-1">
                                                    <p class="text-xs font-bold text-gray-400 uppercase">Kode Bermasalah (Buggy):</p>
                                                    <pre class="p-3 bg-gray-900 text-red-400 rounded-xl font-mono text-xs overflow-x-auto">{{ $question->options['code_snippet'] }}</pre>
                                                </div>
                                            @endif
                                            <div class="space-y-1">
                                                <p class="text-xs font-bold text-gray-400 uppercase">Kunci Solusi Benar:</p>
                                                <pre class="p-3 bg-gray-900 text-green-400 rounded-xl font-mono text-xs overflow-x-auto">{{ $question->correct_answer }}</pre>
                                            </div>
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
                                        @if(in_array($question->options['video_question_type'] ?? '', ['multiple_choice', 'true_false']))
                                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 mb-4">
                                                @foreach(($question->options['options'] ?? []) as $key => $text)
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

    <!-- Modal Edit Soal -->
    <div x-show="showEditModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showEditModal = false"></div>

        <!-- Modal Content -->
        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-xl transition-all sm:my-8 sm:w-full sm:max-w-lg p-6 space-y-4"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <div class="flex items-center justify-between border-b border-gray-100 pb-3">
                    <h3 class="text-lg font-bold text-gray-900">Edit Soal</h3>
                    <button type="button" @click="showEditModal = false" class="text-gray-400 hover:text-gray-500">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <form :action="'{{ route('quizzes.questions.update', '__ID__') }}'.replace('__ID__', editQuestion.id)" method="POST" enctype="multipart/form-data" class="space-y-4 text-left">
                    @csrf
                    @method('PUT')

                    <input type="hidden" name="question_type" :value="editQuestion.question_type">

                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Tipe Soal</label>
                        <span class="px-3 py-1.5 rounded-lg bg-gray-100 text-gray-700 text-sm font-bold block uppercase" x-text="editQuestion.question_type.replace('_', ' ')"></span>
                    </div>

                    <!-- Pertanyaan -->
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Pertanyaan / Instruksi Soal</label>
                        <textarea name="question" rows="3" x-model="editQuestion.question" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm font-medium" required></textarea>
                    </div>

                    <!-- 1. MULTIPLE CHOICE -->
                    <div x-show="editQuestion.question_type === 'multiple_choice'" class="space-y-3 pt-2 border-t border-gray-100">
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi A</label>
                            <input type="text" name="option_a" :disabled="editQuestion.question_type !== 'multiple_choice'" x-model="editQuestion.options.A" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi B</label>
                            <input type="text" name="option_b" :disabled="editQuestion.question_type !== 'multiple_choice'" x-model="editQuestion.options.B" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi C</label>
                            <input type="text" name="option_c" :disabled="editQuestion.question_type !== 'multiple_choice'" x-model="editQuestion.options.C" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi D</label>
                            <input type="text" name="option_d" :disabled="editQuestion.question_type !== 'multiple_choice'" x-model="editQuestion.options.D" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi E (Opsional)</label>
                            <input type="text" name="option_e" :disabled="editQuestion.question_type !== 'multiple_choice'" x-model="editQuestion.options.E" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                        </div>

                        <div class="pt-2">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Jawaban Benar</label>
                            <div class="flex gap-4 flex-wrap">
                                <template x-for="opt in ['A', 'B', 'C', 'D', 'E']">
                                    <label class="flex items-center gap-2 cursor-pointer p-2 border border-gray-200 rounded-lg">
                                        <input type="radio" name="correct_answer" :disabled="editQuestion.question_type !== 'multiple_choice'" :value="opt" x-model="editQuestion.correct_answer" class="text-indigo-600 focus:ring-indigo-500">
                                        <span class="font-bold text-sm text-gray-700" x-text="opt"></span>
                                    </label>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- 2. TRUE OR FALSE -->
                    <div x-show="editQuestion.question_type === 'true_false'" class="pt-2 border-t border-gray-100">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Pernyataan Benar atau Salah?</label>
                        <div class="flex gap-4">
                            <label class="flex items-center gap-2 cursor-pointer p-3 border border-gray-200 rounded-xl">
                                <input type="radio" name="correct_answer" :disabled="editQuestion.question_type !== 'true_false'" value="A" x-model="editQuestion.correct_answer" class="text-indigo-600 focus:ring-indigo-500">
                                <span class="font-bold text-sm text-gray-700">Benar</span>
                            </label>
                            <label class="flex items-center gap-2 cursor-pointer p-3 border border-gray-200 rounded-xl">
                                <input type="radio" name="correct_answer" :disabled="editQuestion.question_type !== 'true_false'" value="B" x-model="editQuestion.correct_answer" class="text-indigo-600 focus:ring-indigo-500">
                                <span class="font-bold text-sm text-gray-700">Salah</span>
                            </label>
                        </div>
                    </div>

                    <!-- 3. SHORT ANSWER -->
                    <div x-show="editQuestion.question_type === 'short_answer'" class="space-y-3 pt-2 border-t border-gray-100">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kunci Jawaban Singkat</label>
                            <input type="text" name="correct_answer" :disabled="editQuestion.question_type !== 'short_answer'" placeholder="Masukkan jawaban yang benar..." x-model="editQuestion.correct_answer" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                            <p class="text-xs text-gray-500 mt-1">Jawaban siswa akan dicocokkan secara case-insensitive.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Keyword Jawaban (Opsional, pisahkan dengan koma)</label>
                            <input type="text" name="keywords" :disabled="editQuestion.question_type !== 'short_answer'" placeholder="Cth: py, python3, scripting" x-model="editQuestion.keywords" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                            <p class="text-xs text-gray-500 mt-1">Jika diisi, jawaban siswa yang mengandung salah satu kata kunci di atas juga akan dianggap benar.</p>
                        </div>
                    </div>

                    <!-- 4. FILL IN THE BLANK (CODING) -->
                    <div x-show="editQuestion.question_type === 'fill_blank'" class="space-y-3 pt-2 border-t border-gray-100">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Code Template</label>
                            <textarea name="code_template" :disabled="editQuestion.question_type !== 'fill_blank'" x-model="editQuestion.code_template" rows="4" placeholder="def greet(name):&#10;    [blank]('Hello ' + name)" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm font-mono"></textarea>
                            <p class="text-xs text-gray-500 mt-1">Tulis template kode di mana siswa harus mengisi bagian yang rumpang.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Placeholder Blank</label>
                            <input type="text" name="blank_placeholder" :disabled="editQuestion.question_type !== 'fill_blank'" placeholder="[blank]" x-model="editQuestion.blank_placeholder" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm font-mono">
                            <p class="text-xs text-gray-500 mt-1">String penanda kekosongan pada template kode di atas (default: [blank]).</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kunci Jawaban Rumpang (Isian Benar)</label>
                            <input type="text" name="correct_answer" :disabled="editQuestion.question_type !== 'fill_blank'" placeholder="Cth: print" x-model="editQuestion.correct_answer" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm">
                            <p class="text-xs text-gray-500 mt-1">Gunakan kata yang tepat untuk mengisi kekosongan kode di atas.</p>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Feedback Jawaban Benar (Opsional)</label>
                            <textarea name="feedback_correct" :disabled="editQuestion.question_type !== 'fill_blank'" rows="2" placeholder="Luar biasa! Penggunaan print() sudah benar." x-model="editQuestion.feedback_correct" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Feedback Jawaban Salah (Opsional)</label>
                            <textarea name="feedback_incorrect" :disabled="editQuestion.question_type !== 'fill_blank'" rows="2" placeholder="Kurang tepat. Ingat fungsi bawaan Python untuk menampilkan output." x-model="editQuestion.feedback_incorrect" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Batas Percobaan (Max Attempt)</label>
                            <input type="number" name="max_attempts" :disabled="editQuestion.question_type !== 'fill_blank'" min="1" x-model="editQuestion.max_attempts" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm font-bold">
                        </div>
                    </div>

                    <!-- 6. DEBUGGING -->
                    <div x-show="editQuestion.question_type === 'debugging'" class="space-y-3 pt-2 border-t border-gray-100">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kode Snippet Bermasalah (Buggy Code)</label>
                            <textarea name="code_snippet" :disabled="editQuestion.question_type !== 'debugging'" x-model="editQuestion.code_snippet" rows="4" placeholder="def add(a, b)&#10;return a + b" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm font-mono"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Bug / Masalah</label>
                            <textarea name="bug_description" :disabled="editQuestion.question_type !== 'debugging'" x-model="editQuestion.bug_description" rows="2" placeholder="Fungsi add() memiliki kesalahan sintaksis pada tanda titik dua dan indentasi." class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm"></textarea>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kunci Solusi Kode Benar</label>
                            <textarea name="correct_answer" :disabled="editQuestion.question_type !== 'debugging'" rows="4" x-model="editQuestion.correct_answer" placeholder="def add(a, b):&#10;    return a + b" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm font-mono"></textarea>
                            <p class="text-xs text-gray-500 mt-1">Jawaban kode siswa akan dicocokkan dengan mengabaikan whitespace.</p>
                        </div>
                    </div>

                    <!-- 7. INTERACTIVE VIDEO -->
                    <div x-show="editQuestion.question_type === 'interactive_video'" class="space-y-4 pt-2 border-t border-gray-100">
                        <div class="p-3 bg-indigo-50/55 border border-indigo-100 rounded-xl space-y-2">
                            <label class="block text-sm font-bold text-gray-700">Pilih / Unggah Video</label>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase">Pilih Video Terunggah</label>
                                <select name="video_url_select" :disabled="editQuestion.question_type !== 'interactive_video'" class="w-full rounded-xl border-gray-300 shadow-sm text-sm">
                                    <option value="">-- Pilih Video Pembelajaran --</option>
                                    @foreach($uploadedVideos as $v)
                                        <option value="{{ $v->video_path }}">{{ $v->title }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="relative flex py-2 items-center">
                                <div class="flex-grow border-t border-gray-300"></div>
                                <span class="flex-shrink mx-4 text-gray-400 text-xs font-bold uppercase">Atau</span>
                                <div class="flex-grow border-t border-gray-300"></div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase">Unggah File Video Baru</label>
                                <input type="file" name="video_file" :disabled="editQuestion.question_type !== 'interactive_video'" class="w-full text-xs text-gray-500 file:mr-2 file:py-1.5 file:px-3 file:rounded-xl file:border-0 file:text-xs file:font-bold file:bg-indigo-100 file:text-indigo-700 hover:file:bg-indigo-200">
                            </div>
                            <div class="relative flex py-2 items-center">
                                <div class="flex-grow border-t border-gray-300"></div>
                                <span class="flex-shrink mx-4 text-gray-400 text-xs font-bold uppercase">Atau</span>
                                <div class="flex-grow border-t border-gray-300"></div>
                            </div>
                            <div class="space-y-2">
                                <label class="block text-xs font-bold text-gray-500 uppercase">Masukkan URL Video Manual</label>
                                <input type="text" name="video_url" :disabled="editQuestion.question_type !== 'interactive_video'" x-model="editQuestion.video_url" placeholder="Cth: /videos/html_intro.mp4 atau link Youtube" class="w-full rounded-xl border-gray-300 shadow-sm text-sm">
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Timestamp Muncul (detik)</label>
                            <input type="number" name="timestamp" :disabled="editQuestion.question_type !== 'interactive_video'" x-model="editQuestion.timestamp" min="0" placeholder="Cth: 45" class="w-full rounded-xl border-gray-300 shadow-sm text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tipe Soal Video</label>
                            <select name="video_question_type" :disabled="editQuestion.question_type !== 'interactive_video'" x-model="editQuestion.video_question_type" class="w-full rounded-xl border-gray-300 shadow-sm text-sm">
                                <option value="multiple_choice">Pilihan Ganda (MC)</option>
                                <option value="true_false">Benar / Salah (TF)</option>
                                <option value="short_answer">Jawaban Teks Singkat</option>
                            </select>
                        </div>

                        <!-- Opsi Sub-Tipe Video MC -->
                        <div x-show="editQuestion.video_question_type === 'multiple_choice'" class="space-y-3 pl-4 border-l-2 border-indigo-100">
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi A</label>
                                <input type="text" name="option_a" :disabled="!(editQuestion.question_type === 'interactive_video' && editQuestion.video_question_type === 'multiple_choice')" x-model="editQuestion.video_options.A" class="w-full rounded-xl border-gray-300 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi B</label>
                                <input type="text" name="option_b" :disabled="!(editQuestion.question_type === 'interactive_video' && editQuestion.video_question_type === 'multiple_choice')" x-model="editQuestion.video_options.B" class="w-full rounded-xl border-gray-300 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi C</label>
                                <input type="text" name="option_c" :disabled="!(editQuestion.question_type === 'interactive_video' && editQuestion.video_question_type === 'multiple_choice')" x-model="editQuestion.video_options.C" class="w-full rounded-xl border-gray-300 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-gray-500 uppercase tracking-widest mb-1">Opsi D</label>
                                <input type="text" name="option_d" :disabled="!(editQuestion.question_type === 'interactive_video' && editQuestion.video_question_type === 'multiple_choice')" x-model="editQuestion.video_options.D" class="w-full rounded-xl border-gray-300 shadow-sm text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Jawaban Benar</label>
                                <div class="flex gap-4">
                                    <template x-for="opt in ['A', 'B', 'C', 'D']">
                                        <label class="flex items-center gap-2 cursor-pointer p-2 border border-gray-200 rounded-lg">
                                            <input type="radio" name="correct_answer" :disabled="!(editQuestion.question_type === 'interactive_video' && editQuestion.video_question_type === 'multiple_choice')" :value="opt" x-model="editQuestion.correct_answer" class="text-indigo-600 focus:ring-indigo-500">
                                            <span class="font-bold text-sm text-gray-700" x-text="opt"></span>
                                        </label>
                                    </template>
                                </div>
                            </div>
                        </div>

                        <!-- Opsi Sub-Tipe Video TF -->
                        <div x-show="editQuestion.video_question_type === 'true_false'" class="space-y-3 pl-4 border-l-2 border-indigo-100">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Pernyataan Benar/Salah?</label>
                            <div class="flex gap-4">
                                <label class="flex items-center gap-2 cursor-pointer p-3 border border-gray-200 rounded-xl">
                                    <input type="radio" name="correct_answer" :disabled="!(editQuestion.question_type === 'interactive_video' && editQuestion.video_question_type === 'true_false')" value="A" x-model="editQuestion.correct_answer" class="text-indigo-600 focus:ring-indigo-500">
                                    <span class="font-bold text-sm text-gray-700">Benar</span>
                                </label>
                                <label class="flex items-center gap-2 cursor-pointer p-3 border border-gray-200 rounded-xl">
                                    <input type="radio" name="correct_answer" :disabled="!(editQuestion.question_type === 'interactive_video' && editQuestion.video_question_type === 'true_false')" value="B" x-model="editQuestion.correct_answer" class="text-indigo-600 focus:ring-indigo-500">
                                    <span class="font-bold text-sm text-gray-700">Salah</span>
                                </label>
                            </div>
                        </div>

                        <!-- Opsi Sub-Tipe Video Short Answer -->
                        <div x-show="editQuestion.video_question_type === 'short_answer'" class="space-y-3 pl-4 border-l-2 border-indigo-100">
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kunci Jawaban Singkat Video</label>
                            <input type="text" name="correct_answer" :disabled="!(editQuestion.question_type === 'interactive_video' && editQuestion.video_question_type === 'short_answer')" x-model="editQuestion.correct_answer" placeholder="Masukkan jawaban video..." class="w-full rounded-xl border-gray-300 shadow-sm text-sm">
                        </div>
                    </div>

                    <!-- Feedback Pembahasan -->
                    <div class="pt-2 border-t border-gray-100">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Pembahasan / Feedback Jawaban (Opsional)</label>
                        <textarea name="feedback" rows="2" x-model="editQuestion.feedback" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm" placeholder="Tulis penjelasan jawaban di sini..."></textarea>
                    </div>

                    <!-- Poin Soal -->
                    <div class="pt-2">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Beban Nilai / Poin</label>
                        <input type="number" name="points" min="1" x-model="editQuestion.points" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 text-sm font-bold">
                    </div>

                    <div class="pt-4 flex justify-end gap-2 border-t border-gray-100">
                        <button type="button" @click="showEditModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-xl">Batal</button>
                        <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-sm transition-transform active:scale-95">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal Pratinjau Kuis -->
    <div x-show="showPreviewModal" 
         class="fixed inset-0 z-50 overflow-y-auto" 
         style="display: none;" 
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         x-data="{ 
             previewAnswers: {}, 
             previewChecked: {}, 
             showFeedback: {},
             previewSubmitted: false,
             currentQuestionIndex: 0,
             totalQuestions: {{ $quiz->questions->count() }},
             questionsList: {!! json_encode($quiz->questions->map(fn($q) => [
                 'id' => $q->id,
                 'question_type' => $q->question_type,
                 'question' => $q->question,
                 'points' => $q->points,
                 'correct' => $q->correct_answer,
                 'options' => $q->options,
                 'feedback' => $q->feedback
             ])) !!},
             calculateScore() {
                 let earnedPoints = 0;
                 let totalPoints = 0;
                 this.questionsList.forEach(q => {
                     totalPoints += q.points;
                     let ans = this.previewAnswers[q.id];
                     if (ans === undefined || ans === null) return;
                     
                     let isCorrect = false;
                     let strAns = String(ans).trim();
                     
                     if (q.question_type === 'multiple_choice' || q.question_type === 'true_false') {
                         isCorrect = strAns.toLowerCase() === q.correct.toLowerCase();
                     } else if (q.question_type === 'short_answer') {
                         isCorrect = strAns.toLowerCase() === q.correct.toLowerCase();
                         if (!isCorrect && q.options && q.options.keywords) {
                             let kwList = q.options.keywords.split(',').map(k => k.trim().toLowerCase());
                             isCorrect = kwList.some(k => k && strAns.toLowerCase().includes(k));
                         }
                     } else if (q.question_type === 'fill_blank') {
                         isCorrect = strAns.toLowerCase() === q.correct.toLowerCase();
                     } else if (q.question_type === 'debugging') {
                         let cleanAns = strAns.replace(/\s+/g, '').toLowerCase();
                         let cleanCorrect = q.correct.replace(/\s+/g, '').toLowerCase();
                         isCorrect = cleanAns === cleanCorrect;
                     } else if (q.question_type === 'reflection') {
                         isCorrect = strAns !== '';
                     } else if (q.question_type === 'interactive_video') {
                         isCorrect = strAns.toLowerCase() === q.correct.toLowerCase();
                     }
                     
                     if (isCorrect) {
                         earnedPoints += q.points;
                     }
                 });
                 return totalPoints > 0 ? Math.round((earnedPoints / totalPoints) * 100) : 0;
             },
             checkAnswer(qId, type, correct, keywords = '') {
                 this.previewChecked[qId] = true;
                 let ans = this.previewAnswers[qId];
                 if (ans === undefined || ans === null || String(ans).trim() === '') {
                     this.showFeedback[qId] = { isCorrect: false, text: 'Harap isi atau pilih jawaban Anda terlebih dahulu!' };
                     return;
                 }
                 
                 let strAns = String(ans);
                 if (type === 'multiple_choice' || type === 'true_false') {
                     if (strAns.toLowerCase().trim() === correct.toLowerCase().trim()) {
                         this.showFeedback[qId] = { isCorrect: true, text: 'Luar biasa! Pilihan Anda tepat.' };
                     } else {
                         this.showFeedback[qId] = { isCorrect: false, text: 'Jawaban kurang tepat. Kunci jawaban: ' + correct };
                     }
                 } else if (type === 'short_answer') {
                     let cleanAns = strAns.toLowerCase().trim();
                     let cleanCorrect = correct.toLowerCase().trim();
                     let match = cleanAns === cleanCorrect;
                     if (!match && keywords) {
                         let kwList = keywords.split(',').map(k => k.trim().toLowerCase());
                         match = kwList.some(k => k && cleanAns.includes(k));
                     }
                     if (match) {
                         this.showFeedback[qId] = { isCorrect: true, text: 'Hebat! Jawaban Anda sesuai.' };
                     } else {
                         this.showFeedback[qId] = { isCorrect: false, text: 'Kurang tepat. Kunci jawaban: ' + correct };
                     }
                 } else if (type === 'fill_blank') {
                     if (strAns.toLowerCase().trim() === correct.toLowerCase().trim()) {
                         this.showFeedback[qId] = { isCorrect: true, text: 'Luar biasa! Kode Anda bekerja dengan sempurna.' };
                     } else {
                         this.showFeedback[qId] = { isCorrect: false, text: 'Kurang tepat. Isian yang benar: ' + correct };
                     }
                 } else if (type === 'debugging') {
                     let cleanAns = strAns.replace(/\s+/g, '').toLowerCase();
                     let cleanCorrect = correct.replace(/\s+/g, '').toLowerCase();
                     if (cleanAns === cleanCorrect) {
                         this.showFeedback[qId] = { isCorrect: true, text: 'Selamat! Anda berhasil menemukan dan memperbaiki bug.' };
                     } else {
                         this.showFeedback[qId] = { isCorrect: false, text: 'Perbaikan kurang tepat. Periksa kembali logika kode Anda!' };
                     }
                 } else if (type === 'reflection') {
                     this.showFeedback[qId] = { isCorrect: true, text: '✓ Refleksi Diterima! Pendapat/refleksi Anda telah disimpan secara interaktif.' };
                 }
             }
         }">
        
        <!-- Backdrop -->
        <div class="fixed inset-0 bg-gray-900 bg-opacity-80 backdrop-blur-sm transition-opacity" @click="showPreviewModal = false"></div>

        <!-- Modal Content -->
        <div class="flex min-h-screen items-center justify-center p-4 text-center sm:p-6">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-4xl p-6 sm:p-8 space-y-6 flex flex-col max-h-[90vh]"
                 x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                 x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100 translate-y-0 sm:scale-100"
                 x-transition:leave-end="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95">
                
                <!-- Modal Header -->
                <div class="flex items-center justify-between border-b border-gray-150 pb-4 flex-shrink-0">
                    <div>
                        <h3 class="text-2xl font-black text-gray-900 tracking-tight flex items-center gap-3">
                            <svg class="w-7 h-7 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                            Pratinjau Kuis (Simulasi Tampilan Siswa)
                        </h3>
                        <p class="text-sm text-gray-500 font-medium mt-1">Uji interaktif kuis secara real-time. Mode simulasi ini tidak menyimpan attempt.</p>
                    </div>
                    <button type="button" @click="showPreviewModal = false; previewSubmitted = false" class="text-gray-400 hover:text-gray-650 hover:bg-gray-100 p-2 rounded-xl transition-all">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Modal Body (Preview Simulator Layout) -->
                <div class="flex-grow overflow-y-auto pr-2 py-4">
                    
                    <!-- 1. SIMULASI SELESAI / HASIL SKOR -->
                    <div x-show="previewSubmitted" class="space-y-6">
                        <div class="p-8 bg-gradient-to-r from-emerald-500 to-teal-600 text-white text-center rounded-3xl relative overflow-hidden shadow-lg mb-6">
                            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
                            <div class="relative z-10">
                                <span class="text-xs font-bold uppercase tracking-widest text-emerald-100 mb-2 inline-block">Hasil Skor Simulasi Anda</span>
                                <h3 class="text-5xl font-black mb-1" x-text="calculateScore() + ' / 100'"></h3>
                                <p class="text-sm font-medium text-emerald-50 mt-2">Ini adalah mode simulasi. Jawaban dan skor Anda tidak akan disimpan ke database.</p>
                                <button type="button" @click="previewSubmitted = false; previewAnswers = {}; previewChecked = {}; showFeedback = {}; currentQuestionIndex = 0" class="mt-4 px-5 py-2.5 bg-white text-emerald-800 rounded-xl text-xs font-bold shadow hover:bg-emerald-50 transition-colors">Ulangi Simulasi</button>
                            </div>
                        </div>
                        
                        <div class="space-y-4">
                            <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-2">Evaluasi Hasil Per Soal:</h4>
                            @foreach($quiz->questions as $index => $question)
                                <div class="p-6 rounded-2xl border-2 bg-gray-50/50 border-gray-150">
                                    <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-750 uppercase">Soal {{ $index + 1 }}</span>
                                    <h4 class="text-lg font-bold text-gray-800 mt-2 mb-4">{{ $question->question }}</h4>
                                    
                                    <div class="p-4 bg-white rounded-xl border border-gray-150 text-sm space-y-1">
                                        <div>
                                            <span class="font-bold text-gray-500">Jawaban Anda:</span>
                                            <span class="font-mono font-bold text-indigo-650" x-text="previewAnswers[{{ $question->id }}] || '(Tidak Menjawab)'"></span>
                                        </div>
                                        @if($question->question_type !== 'reflection')
                                            <div>
                                                <span class="font-bold text-emerald-600">Kunci Jawaban:</span>
                                                <span class="font-mono font-bold text-emerald-600">{{ $question->correct_answer }}</span>
                                            </div>
                                        @endif
                                    </div>
                                    @if($question->feedback)
                                        <div class="p-3 bg-yellow-50/55 border border-yellow-100 rounded-xl text-xs text-yellow-800 mt-3 leading-normal">
                                            <strong>Pembahasan:</strong> {{ $question->feedback }}
                                        </div>
                                    @endif
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- 2. SIMULASI SEDANG BERLANGSUNG -->
                    <div x-show="!previewSubmitted" class="grid grid-cols-1 md:grid-cols-4 gap-6">
                        <!-- Sidebar: Nomer-nomer Soal -->
                        <div class="md:col-span-1 border-r border-gray-100 pr-4 space-y-4">
                            <h4 class="text-xs font-bold text-gray-400 uppercase tracking-widest mb-2">Navigasi Soal</h4>
                            <div class="grid grid-cols-5 md:grid-cols-3 gap-2">
                                @foreach($quiz->questions as $index => $question)
                                    <button type="button" 
                                            @click="currentQuestionIndex = {{ $index }}"
                                            class="w-10 h-10 rounded-xl font-bold text-sm flex items-center justify-center transition-all border"
                                            :class="currentQuestionIndex === {{ $index }} ? 'bg-indigo-600 border-indigo-600 text-white shadow-md' : 
                                                   (previewAnswers[{{ $question->id }}] ? 'bg-indigo-50 border-indigo-200 text-indigo-700 font-bold' : 'bg-white border-gray-200 text-gray-650 hover:border-indigo-100')">
                                        <span>{{ $index + 1 }}</span>
                                    </button>
                                @endforeach
                            </div>
                            
                            <div class="pt-4 border-t border-gray-100">
                                <button type="button" 
                                        @click="if (confirm('Kumpulkan jawaban simulasi pratinjau Anda?')) { previewSubmitted = true }" 
                                        class="px-4 py-2.5 bg-gradient-to-r from-indigo-650 to-purple-600 text-white text-xs font-black rounded-xl shadow-md w-full active:scale-95 duration-200">
                                    Kumpulkan Simulasi
                                </button>
                            </div>
                        </div>

                        <!-- Area Utama Soal Aktif -->
                        <div class="md:col-span-3 space-y-6">
                            @foreach($quiz->questions as $index => $question)
                                <div x-show="currentQuestionIndex === {{ $index }}" class="space-y-6 animate-fade-in" x-data="{ localVideoPaused: false, localVideoAnswered: false, localVideoTime: 0 }">
                                    <div class="flex items-center gap-2 mb-2 flex-wrap">
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-indigo-50 text-indigo-700 uppercase tracking-wider">
                                            {{ str_replace('_', ' ', $question->question_type) }}
                                        </span>
                                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-700 tracking-wider">
                                            {{ $question->points }} Poin
                                        </span>
                                    </div>

                                    <h4 class="text-xl font-extrabold text-gray-800 leading-relaxed">{{ $question->question }}</h4>

                                    <!-- 1. Multiple Choice -->
                                    @if($question->question_type === 'multiple_choice')
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach($question->options as $key => $option)
                                                <label class="group relative flex cursor-pointer rounded-2xl border-2 p-5 hover:border-indigo-300 hover:bg-indigo-50/50 transition-all"
                                                       :class="previewAnswers[{{ $question->id }}] === '{{ $key }}' ? 'border-indigo-600 bg-indigo-50/50' : 'border-gray-200 bg-white'">
                                                    <div class="flex w-full items-center justify-between">
                                                        <div class="flex items-center gap-3">
                                                            <input type="radio" x-model="previewAnswers[{{ $question->id }}]" value="{{ $key }}" class="h-5 w-5 border-2 border-gray-300 text-indigo-600 focus:ring-indigo-500 rounded-full">
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
                                        <div class="flex gap-4">
                                            <label class="group relative flex cursor-pointer rounded-xl border-2 px-6 py-4 hover:border-indigo-300 hover:bg-indigo-50/50 transition-all"
                                                   :class="previewAnswers[{{ $question->id }}] === 'A' ? 'border-indigo-600 bg-indigo-50/50' : 'border-gray-200 bg-white'">
                                                <input type="radio" x-model="previewAnswers[{{ $question->id }}]" value="A" class="h-5 w-5 mr-3 border-2 border-gray-300 text-indigo-600 focus:ring-indigo-500 rounded-full">
                                                <span class="text-sm font-bold text-gray-800">Benar</span>
                                            </label>
                                            <label class="group relative flex cursor-pointer rounded-xl border-2 px-6 py-4 hover:border-indigo-300 hover:bg-indigo-50/50 transition-all"
                                                   :class="previewAnswers[{{ $question->id }}] === 'B' ? 'border-indigo-600 bg-indigo-50/50' : 'border-gray-200 bg-white'">
                                                <input type="radio" x-model="previewAnswers[{{ $question->id }}]" value="B" class="h-5 w-5 mr-3 border-2 border-gray-300 text-indigo-600 focus:ring-indigo-500 rounded-full">
                                                <span class="text-sm font-bold text-gray-800">Salah</span>
                                            </label>
                                        </div>

                                    <!-- 3. Short Answer -->
                                    @elseif($question->question_type === 'short_answer')
                                        <div class="w-full">
                                            <input type="text" x-model="previewAnswers[{{ $question->id }}]" placeholder="Ketik jawaban singkat di sini..." class="w-full rounded-2xl border-2 border-gray-200 px-5 py-4 focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm font-medium">
                                        </div>

                                    <!-- 4. Fill in the Blank -->
                                    @elseif($question->question_type === 'fill_blank')
                                        @php
                                            $template = $question->options['code_template'] ?? '';
                                            $placeholder = $question->options['blank_placeholder'] ?? '[blank]';
                                            $inputHtml = '<input type="text" x-model="previewAnswers['.$question->id.']" placeholder="..." class="mx-1 px-3 py-1 bg-white border-2 border-indigo-300 rounded-lg text-indigo-900 font-mono text-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 outline-none w-40 inline-block">';
                                            $escapedTemplate = e($template);
                                            $renderedCode = str_replace(e($placeholder), $inputHtml, $escapedTemplate);
                                        @endphp
                                        <div class="w-full space-y-2">
                                            <div class="p-4 bg-gray-900 text-yellow-300 rounded-xl font-mono text-sm overflow-x-auto leading-relaxed shadow-inner">
                                                {!! nl2br($renderedCode) !!}
                                            </div>
                                            <div class="text-[10px] text-gray-400">
                                                *Lengkapi bagian kosong pada potongan kode di atas
                                            </div>
                                        </div>

                                    <!-- 5. Reflection -->
                                    @elseif($question->question_type === 'reflection')
                                        <div class="w-full">
                                            <textarea x-model="previewAnswers[{{ $question->id }}]" rows="4" placeholder="Ketik refleksi atau pendapat bebas di sini..." class="w-full rounded-2xl border-2 border-gray-200 px-5 py-4 focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm font-medium"></textarea>
                                        </div>

                                    <!-- 6. Debugging -->
                                    @elseif($question->question_type === 'debugging')
                                        <div class="w-full space-y-3">
                                            @if(!empty($question->options['bug_description']))
                                                <div class="p-3.5 bg-rose-50 border border-rose-100 rounded-xl text-rose-800 text-xs">
                                                    <strong>Deskripsi Masalah/Bug:</strong>
                                                    <p class="mt-0.5 font-medium">{{ $question->options['bug_description'] }}</p>
                                                </div>
                                            @endif
                                            
                                            @if(!empty($question->options['code_snippet']))
                                                <div class="space-y-1">
                                                    <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Kode Bermasalah:</p>
                                                    <pre class="p-3 bg-gray-900 text-red-400 rounded-xl font-mono text-xs overflow-x-auto shadow-inner">{{ $question->options['code_snippet'] }}</pre>
                                                </div>
                                            @endif
                                            
                                            <div class="space-y-1.5">
                                                <label class="block text-xs font-bold text-gray-700">Tulis Perbaikan Kode:</label>
                                                <textarea x-model="previewAnswers[{{ $question->id }}]" rows="5" placeholder="Tulis kode pemrograman yang benar di sini..." class="w-full rounded-xl border-2 border-gray-200 bg-gray-900 text-green-400 font-mono text-xs px-4 py-3 focus:border-indigo-500 focus:ring focus:ring-indigo-200 shadow-inner"></textarea>
                                            </div>
                                        </div>

                                    <!-- 7. Interactive Video -->
                                    @elseif($question->question_type === 'interactive_video')
                                        @php
                                            $videoUrl = $question->options['video_url'] ?? '';
                                            $timestamp = $question->options['timestamp'] ?? 0;
                                            $videoQType = $question->options['video_question_type'] ?? 'multiple_choice';
                                        @endphp
                                        <div class="space-y-4">
                                            <div class="relative rounded-xl overflow-hidden shadow-md bg-black max-w-xl mx-auto">
                                                <video id="preview-video-{{ $question->id }}" class="w-full max-h-[300px]" controls
                                                       @timeupdate="localVideoTime = $el.currentTime; if (localVideoTime >= {{ $timestamp }} && !localVideoAnswered) { $el.pause(); localVideoPaused = true; }">
                                                    <source src="{{ $videoUrl }}" type="video/mp4">
                                                    Browser Anda tidak mendukung video tag.
                                                </video>
                                            </div>
                                            
                                            <div x-show="localVideoPaused" class="p-4 bg-purple-50 border border-purple-100 rounded-xl transition-all duration-300 max-w-xl mx-auto">
                                                <p class="font-bold text-purple-900 text-xs mb-2.5 flex items-center gap-1.5">
                                                    <svg class="w-4 h-4 text-purple-600 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 5.25v13.5m-7.5-13.5v13.5" /></svg>
                                                    Video Berhenti Otomatis (Detik {{ $timestamp }}):
                                                </p>
                                                
                                                <div class="bg-white p-3 rounded-lg border border-purple-100">
                                                    @if($videoQType === 'multiple_choice')
                                                        <div class="grid grid-cols-1 gap-2">
                                                            @foreach(($question->options['options'] ?? []) as $key => $option)
                                                                <label class="flex items-center gap-2 p-2 rounded-lg border border-gray-200 bg-white hover:bg-purple-50 cursor-pointer transition-colors text-xs font-semibold">
                                                                    <input type="radio" x-model="previewAnswers[{{ $question->id }}]" value="{{ $key }}" @change="localVideoAnswered = true; localVideoPaused = false; document.getElementById('preview-video-{{ $question->id }}').play()" class="text-indigo-600 focus:ring-indigo-500">
                                                                    <span>{{ $key }}. {{ $option }}</span>
                                                                </label>
                                                            @endforeach
                                                        </div>
                                                    @elseif($videoQType === 'true_false')
                                                        <div class="flex gap-3">
                                                            <label class="flex items-center gap-2 cursor-pointer p-2 border border-gray-200 bg-white rounded-lg hover:bg-purple-50 transition-colors text-xs font-semibold">
                                                                <input type="radio" x-model="previewAnswers[{{ $question->id }}]" value="A" @change="localVideoAnswered = true; localVideoPaused = false; document.getElementById('preview-video-{{ $question->id }}').play()" class="text-indigo-600 focus:ring-indigo-500">
                                                                <span>Benar</span>
                                                            </label>
                                                            <label class="flex items-center gap-2 cursor-pointer p-2 border border-gray-250 bg-white rounded-lg hover:bg-purple-50 transition-colors text-xs font-semibold">
                                                                <input type="radio" x-model="previewAnswers[{{ $question->id }}]" value="B" @change="localVideoAnswered = true; localVideoPaused = false; document.getElementById('preview-video-{{ $question->id }}').play()" class="text-indigo-600 focus:ring-indigo-500">
                                                                <span>Salah</span>
                                                            </label>
                                                        </div>
                                                    @else
                                                        <div class="space-y-2">
                                                            <input type="text" id="preview-video-sa-{{ $question->id }}" x-model="previewAnswers[{{ $question->id }}]" placeholder="Ketik jawaban video..." class="w-full rounded-lg border-gray-300 text-xs">
                                                            <button type="button" @click="if (previewAnswers[{{ $question->id }}]) { localVideoAnswered = true; localVideoPaused = false; document.getElementById('preview-video-{{ $question->id }}').play() }" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-xs font-bold transition-colors">
                                                                Kirim Jawaban & Lanjutkan
                                                            </button>
                                                        </div>
                                                    @endif
                                                </div>
                                            </div>
                                            
                                            <div class="text-[10px] text-gray-500 flex justify-between">
                                                <span>*Video akan terjeda pada detik ke-{{ $timestamp }}</span>
                                                <button type="button" @click="localVideoAnswered = false; localVideoPaused = false; document.getElementById('preview-video-{{ $question->id }}').currentTime = 0; document.getElementById('preview-video-{{ $question->id }}').play()" class="text-indigo-600 hover:underline">Reset Simulasi Video</button>
                                            </div>
                                        </div>
                                    @endif

                                    <!-- Interactive Single Check Button -->
                                    <div class="mt-6 pt-4 border-t border-gray-150 flex flex-col gap-3">
                                        <div class="flex items-center justify-between">
                                            <span class="text-xs text-gray-400 font-medium">Uji kebenaran jawaban Soal ini secara langsung:</span>
                                            <button type="button" 
                                                    @click="checkAnswer({{ $question->id }}, '{{ $question->question_type }}', '{{ addslashes($question->correct_answer) }}', '{{ addslashes($question->options['keywords'] ?? '') }}')"
                                                    class="inline-flex items-center gap-1.5 px-4 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 border border-indigo-200 rounded-xl text-xs font-bold shadow-sm transition-all active:scale-95">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                                Uji Jawaban Soal Ini
                                            </button>
                                        </div>

                                        <template x-if="previewChecked[{{ $question->id }}]">
                                            <div class="p-4 rounded-xl border text-xs font-bold leading-relaxed transition-all duration-300"
                                                 :class="showFeedback[{{ $question->id }}].isCorrect ? 'bg-emerald-50 border-emerald-250 text-emerald-800' : 'bg-rose-50 border-rose-250 text-rose-800'">
                                                <div class="flex items-start gap-2">
                                                    <span class="text-base" x-text="showFeedback[{{ $question->id }}].isCorrect ? '🎉' : '❌'"></span>
                                                    <div class="flex-1">
                                                        <p x-text="showFeedback[{{ $question->id }}].text"></p>
                                                        @if($question->feedback)
                                                            <p class="mt-2 font-semibold text-gray-650 bg-white/70 p-2 rounded-lg border border-black/5 leading-normal">
                                                                <strong>Pembahasan/Feedback Soal:</strong> {{ $question->feedback }}
                                                            </p>
                                                        @endif
                                                    </div>
                                                </div>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            @endforeach

                            <!-- Next / Prev Paging Buttons -->
                            <div class="flex items-center justify-between pt-6 border-t border-gray-100 mt-6">
                                <button type="button" 
                                        @click="currentQuestionIndex--" 
                                        :disabled="currentQuestionIndex === 0" 
                                        class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 disabled:opacity-50 disabled:cursor-not-allowed text-xs font-bold rounded-xl transition-all">
                                    &larr; Sebelumnya
                                </button>
                                <span class="text-xs text-gray-500 font-bold" x-text="'Soal ' + (currentQuestionIndex + 1) + ' dari ' + totalQuestions"></span>
                                <button type="button" 
                                        @click="currentQuestionIndex++" 
                                        :disabled="currentQuestionIndex === totalQuestions - 1" 
                                        class="px-4 py-2 bg-indigo-650 hover:bg-indigo-700 text-white disabled:opacity-50 disabled:cursor-not-allowed text-xs font-bold rounded-xl transition-all">
                                    Berikutnya &rarr;
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Modal Footer -->
                <div class="flex justify-end border-t border-gray-150 pt-4 flex-shrink-0">
                    <button type="button" @click="showPreviewModal = false; previewSubmitted = false" class="px-5 py-2.5 bg-gray-100 hover:bg-gray-200 text-gray-700 text-sm font-bold rounded-2xl transition-all">
                        Tutup Pratinjau
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>
</x-app-layout>
