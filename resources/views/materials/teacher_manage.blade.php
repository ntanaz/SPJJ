<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <a href="{{ route('student.courses.show', $material->course) }}" class="p-2 bg-white rounded-full text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors shadow-sm">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
                </a>
                <div>
                    <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
                        Kelola: {{ $material->title }}
                    </h2>
                    <p class="text-sm text-gray-500">Mata Pelajaran: {{ $material->course->name }} &bull; Panel Kontrol Guru</p>
                </div>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto px-4" x-data="{ activePanel: 'progress' }">
        
        <!-- Flash Alert -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl flex items-center shadow-sm">
                <svg class="w-6 h-6 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span class="font-bold text-sm">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl flex items-center shadow-sm">
                <svg class="w-6 h-6 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                <span class="font-bold text-sm">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-4 gap-8">
            
            <!-- Left Sidebar Navigation -->
            <div class="space-y-3">
                <button @click="activePanel = 'progress'" :class="activePanel === 'progress' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white hover:bg-indigo-50/50 text-gray-700 hover:text-indigo-600'" class="w-full text-left px-5 py-3.5 rounded-2xl font-bold transition-all flex items-center gap-3 border border-gray-150/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 002 2h2a2 2 0 002-2"/></svg>
                    Progres &amp; Penilaian Siswa
                </button>
                <button @click="activePanel = 'mind_map'" :class="activePanel === 'mind_map' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white hover:bg-indigo-50/50 text-gray-700 hover:text-indigo-600'" class="w-full text-left px-5 py-3.5 rounded-2xl font-bold transition-all flex items-center gap-3 border border-gray-150/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7"/></svg>
                    Peta Pikiran (Step 1)
                </button>
                <button @click="activePanel = 'upload_video'" :class="activePanel === 'upload_video' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white hover:bg-indigo-50/50 text-gray-700 hover:text-indigo-600'" class="w-full text-left px-5 py-3.5 rounded-2xl font-bold transition-all flex items-center gap-3 border border-gray-150/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                    Upload Video (Step 3)
                </button>
                <button @click="activePanel = 'video'" :class="activePanel === 'video' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white hover:bg-indigo-50/50 text-gray-700 hover:text-indigo-600'" class="w-full text-left px-5 py-3.5 rounded-2xl font-bold transition-all flex items-center gap-3 border border-gray-150/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                    Kuis Video Interaktif (Step 3)
                </button>
                <button @click="activePanel = 'coding'" :class="activePanel === 'coding' ? 'bg-indigo-600 text-white shadow-md' : 'bg-white hover:bg-indigo-50/50 text-gray-700 hover:text-indigo-600'" class="w-full text-left px-5 py-3.5 rounded-2xl font-bold transition-all flex items-center gap-3 border border-gray-150/50">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"/></svg>
                    Kuis Koding (Step 4)
                </button>
            </div>

            <!-- Right Content Panels -->
            <div class="md:col-span-3 space-y-6">

                <!-- PANEL 1: PROGRESS & STUDENT GRADING -->
                <div x-show="activePanel === 'progress'" class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 space-y-8">
                    <div>
                        <h3 class="text-2xl font-black text-gray-800">Progres Belajar & Penilaian</h3>
                        <p class="text-gray-500 text-sm mt-1">Pantau kemajuan tahapan Discovery Learning siswa dan beri nilai hasil refleksi mereka.</p>
                    </div>

                    <!-- Step Tracker Table -->
                    <div class="border border-gray-150/50 rounded-2xl overflow-hidden">
                        <table class="w-full text-left whitespace-nowrap text-sm">
                            <thead class="bg-gray-50 border-b border-gray-100 font-bold text-gray-700">
                                <tr>
                                    <th class="px-6 py-4">Nama Siswa</th>
                                    <th class="px-6 py-4 text-center">Mind Map</th>
                                    <th class="px-6 py-4 text-center">Modul</th>
                                    <th class="px-6 py-4 text-center">Video</th>
                                    <th class="px-6 py-4 text-center">Kuis Koding</th>
                                    <th class="px-6 py-4 text-center">Refleksi</th>
                                    <th class="px-6 py-4 text-center">Aktv. Video</th>
                                    <th class="px-6 py-4 text-right">Status Akhir</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-100">
                                @forelse($studentProgress as $prog)
                                    <tr class="hover:bg-gray-50/50 transition-colors">
                                        <td class="px-6 py-4 font-bold text-gray-800">{{ $prog['student']->name }}</td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-block h-3.5 w-3.5 rounded-full {{ !empty($prog['steps']['mind_map']) ? 'bg-emerald-500' : 'bg-gray-200' }}"></span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-block h-3.5 w-3.5 rounded-full {{ !empty($prog['steps']['modul']) ? 'bg-emerald-500' : 'bg-gray-200' }}"></span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-block h-3.5 w-3.5 rounded-full {{ !empty($prog['steps']['video']) ? 'bg-emerald-500' : 'bg-gray-200' }}"></span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-block h-3.5 w-3.5 rounded-full {{ !empty($prog['steps']['coding']) ? 'bg-emerald-500' : 'bg-gray-200' }}"></span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            <span class="inline-block h-3.5 w-3.5 rounded-full {{ !empty($prog['steps']['reflection']) ? 'bg-emerald-500' : 'bg-gray-200' }}"></span>
                                        </td>
                                        <td class="px-6 py-4 text-center">
                                            @php
                                                $totalQuestions = $material->interactiveVideoQuestions->count();
                                                $answeredCount  = \App\Models\VideoParticipationTracking::where('material_id', $material->id)->where('user_id', $prog['student']->id)->count();
                                            @endphp
                                            <span class="text-xs font-bold {{ $answeredCount >= $totalQuestions && $totalQuestions > 0 ? 'text-emerald-600' : 'text-gray-500' }}">
                                                {{ $answeredCount }}/{{ $totalQuestions }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            @if($prog['is_material_completed'])
                                                <span class="px-3 py-1 bg-emerald-100 text-emerald-800 text-xs font-bold rounded-lg uppercase">Selesai</span>
                                            @else
                                                <span class="px-3 py-1 bg-amber-100 text-amber-800 text-xs font-bold rounded-lg uppercase">Dalam Proses</span>
                                            @endif
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="px-6 py-8 text-center text-gray-500">Belum ada siswa terdaftar pada kelas mata pelajaran ini.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Attempts & Reflections Grading Section -->
                    <div class="space-y-4 pt-6 border-t border-gray-100">
                        <h4 class="font-black text-gray-800 text-lg">Daftar Pengumpulan Kuis Koding & Refleksi</h4>
                        
                        <div class="space-y-6">
                            @forelse($attempts as $att)
                                <div class="bg-gray-50/50 p-6 rounded-3xl border border-gray-150/50 space-y-4 relative">
                                    <div class="flex items-start justify-between flex-wrap gap-2">
                                        <div>
                                            <h5 class="font-bold text-gray-800 text-base">{{ $att->user->name }}</h5>
                                            <p class="text-xs text-gray-500">Percobaan Ke-{{ $att->percobaan_ke }} &bull; Dikirim pada {{ $att->waktu_submit->translatedFormat('d M Y, H:i') }} WIB</p>
                                            @if($att->feedback)
                                                <p class="text-xs text-indigo-600 font-semibold mt-1">Feedback Sistem: <span class="italic">"{{ $att->feedback }}"</span></p>
                                            @endif
                                        </div>
                                        <div class="flex gap-2">
                                            <span class="px-3 py-1 rounded-lg text-xs font-bold uppercase tracking-wider {{ $att->hasil_validasi ? 'bg-emerald-150 text-emerald-800' : 'bg-red-150 text-red-800' }}">
                                                {{ $att->hasil_validasi ? 'Lolos Otomatis' : 'Gagal' }}
                                            </span>
                                            @if($att->graded_at)
                                                <span class="px-3 py-1 bg-indigo-100 text-indigo-800 rounded-lg text-xs font-bold uppercase tracking-wider">Sudah Dinilai ({{ $att->final_grade }})</span>
                                            @else
                                                <span class="px-3 py-1 bg-amber-100 text-amber-800 rounded-lg text-xs font-bold uppercase tracking-wider">Butuh Penilaian</span>
                                            @endif
                                        </div>
                                    </div>

                                    <!-- Answers Filled -->
                                    <div class="p-4 bg-white border border-gray-150 rounded-2xl space-y-2">
                                        <div class="text-xs text-gray-400 font-bold uppercase">Jawaban Siswa (Rumpang):</div>
                                        <div class="font-mono text-sm text-gray-700 bg-gray-50 p-3 rounded-lg overflow-x-auto">
                                            {{ implode('  |  ', $att->jawaban) }}
                                        </div>
                                    </div>

                                    <!-- Student Reflection -->
                                    @if($att->reflection)
                                        <div class="p-4 bg-white border border-gray-150 rounded-2xl space-y-2">
                                            <div class="text-xs text-indigo-400 font-bold uppercase">Refleksi Koding & Analisis:</div>
                                            <p class="text-sm text-gray-700 leading-relaxed whitespace-pre-wrap">{!! e($att->reflection) !!}</p>
                                        </div>
                                    @else
                                        <div class="p-4 bg-amber-50/50 border border-amber-100 text-amber-850 rounded-2xl text-xs font-medium">
                                            Siswa belum mengunggah refleksi/analisis kode untuk percobaan ini.
                                        </div>
                                    @endif

                                    <!-- Grading Form -->
                                    <form action="{{ route('materials.grade_attempt', $att) }}" method="POST" class="bg-white p-6 border border-gray-150 rounded-2xl space-y-4 shadow-sm" x-data="{ cGrad: {{ $att->correctness_grade ?? 80 }}, rGrad: {{ $att->reflection_grade ?? 80 }}, fGrad: {{ $att->final_grade ?? 80 }} }">
                                        @csrf
                                        <strong class="text-sm font-bold text-gray-800 block">Form Penilaian Guru:</strong>
                                        
                                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 mb-1">Nilai Correctness (0-100)</label>
                                                <input type="number" name="correctness_grade" x-model.number="cGrad" @input="fGrad = Math.round((cGrad + rGrad)/2)" min="0" max="100" class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500" required>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 mb-1">Nilai Refleksi (0-100)</label>
                                                <input type="number" name="reflection_grade" x-model.number="rGrad" @input="fGrad = Math.round((cGrad + rGrad)/2)" min="0" max="100" class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500" required>
                                            </div>
                                            <div>
                                                <label class="block text-xs font-bold text-gray-500 mb-1">Nilai Akhir Rata-rata</label>
                                                <input type="number" name="final_grade" x-model.number="fGrad" min="0" max="100" class="w-full rounded-xl border-gray-300 text-sm bg-gray-50 font-bold focus:border-indigo-500" required>
                                            </div>
                                        </div>
                                        <div class="flex justify-end">
                                            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold text-xs rounded-xl shadow-sm transition-all active:scale-95">
                                                Simpan Nilai
                                            </button>
                                        </div>
                                    </form>
                                </div>
                            @empty
                                <div class="py-12 text-center bg-gray-50/50 border border-gray-150/50 rounded-3xl border-dashed">
                                    <p class="text-gray-500 font-bold">Belum ada percobaan pengiriman kuis koding dari siswa.</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- PANEL 2: CONFIG MIND MAP -->
                <div x-show="activePanel === 'mind_map'" class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 space-y-6">
                    <div>
                        <h3 class="text-2xl font-black text-gray-800">Konfigurasi Mind Map (Peta Pikiran)</h3>
                        <p class="text-gray-500 text-sm mt-1">Unggah berkas gambar peta konsep/peta pikiran yang digunakan untuk merangsang kognisi siswa.</p>
                    </div>

                    @if($material->mind_map_path)
                        <div class="border border-gray-200 p-4 bg-gray-50 rounded-2xl">
                            <label class="block text-xs font-bold text-gray-400 uppercase mb-2">Gambar Saat Ini:</label>
                            <img src="{{ asset('storage/' . $material->mind_map_path) }}" alt="Mind Map" class="max-h-[300px] object-contain rounded-xl">
                        </div>
                    @endif

                    <form action="{{ route('materials.upload_mind_map', $material) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label for="mind_map_image" class="block text-sm font-bold text-gray-700 mb-2">Pilih File Gambar (PNG, JPG, JPEG):</label>
                            <input type="file" name="mind_map_image" id="mind_map_image" accept="image/*" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                            @error('mind_map_image')<p class="mt-1 text-xs text-red-650 font-bold">{{ $message }}</p>@enderror
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-100">
                            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-all active:scale-95">
                                Unggah Mind Map
                            </button>
                        </div>
                    </form>
                </div>

                <!-- PANEL 3: UPLOAD VIDEO (PRIMARY SOURCE) -->
                <div x-show="activePanel === 'upload_video'" class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 space-y-6">
                    <div>
                        <h3 class="text-2xl font-black text-gray-800">🎬 Upload Video Pembelajaran</h3>
                        <p class="text-gray-500 text-sm mt-1">Upload video MP4 sebagai sumber utama pembelajaran interaktif. Video lokal mendukung popup kuis otomatis.</p>
                    </div>

                    @if($material->file_path && in_array($material->format ?? $material->type, ['video']))
                        <div class="bg-gray-50 border border-gray-200 rounded-2xl p-4 space-y-3">
                            <p class="text-xs font-bold text-gray-400 uppercase">Video Saat Ini:</p>
                            <video controls class="w-full rounded-xl max-h-64 bg-black">
                                <source src="{{ asset('storage/' . $material->file_path) }}" type="video/mp4">
                            </video>
                            <p class="text-xs text-gray-400 font-mono">{{ basename($material->file_path) }}</p>
                        </div>
                    @else
                        <div class="py-8 text-center bg-gray-50/50 rounded-2xl border-2 border-dashed border-gray-200">
                            <svg class="w-12 h-12 text-gray-300 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg>
                            <p class="text-gray-400 text-sm font-medium">Belum ada video yang diunggah.</p>
                        </div>
                    @endif

                    <form action="{{ route('materials.upload_video', $material) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label for="video_file" class="block text-sm font-bold text-gray-700 mb-2">Upload File Video Baru (MP4, MOV, WebM — Maks. 500MB):</label>
                            <input type="file" name="video_file" id="video_file" accept="video/mp4,video/mov,video/avi,video/webm"
                                class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100"
                                required>
                            @error('video_file')<p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p>@enderror
                        </div>
                        <div class="bg-amber-50 border border-amber-100 p-4 rounded-2xl text-amber-800 text-xs font-medium">
                            ⚠️ Mengunggah video baru akan <strong>menggantikan</strong> video lama secara permanen.
                        </div>
                        <div class="flex justify-end pt-4 border-t border-gray-100">
                            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-all active:scale-95 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"/></svg>
                                Upload Video
                            </button>
                        </div>
                    </form>
                </div>

                <!-- PANEL 4: CONFIG VIDEO POPUP QUESTIONS -->
                <div x-show="activePanel === 'video'" class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 space-y-6" x-data="videoQuestionManager()">
                    <div>
                        <h3 class="text-2xl font-black text-gray-800">Pertanyaan Video Interaktif</h3>
                        <p class="text-gray-500 text-sm mt-1">Sisipkan pertanyaan kuis pada timestamp (detik) tertentu. Mendukung pilihan ganda, benar/salah, dan jawaban singkat.</p>
                    </div>

                    <form action="{{ route('materials.save_video_questions', $material) }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="space-y-4">
                            <strong class="text-sm font-bold text-gray-800 block">Daftar Pertanyaan Pop Up:</strong>

                            <template x-for="(q, idx) in questions" :key="idx">
                                <div class="p-6 bg-gray-50 rounded-2xl border border-gray-150 space-y-4 relative">
                                    <button type="button" @click="removeQuestion(idx)" class="absolute top-4 right-4 text-red-400 hover:text-red-600 p-2 bg-white rounded-xl shadow-sm">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>

                                    <div class="grid grid-cols-1 sm:grid-cols-4 gap-4">
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Muncul di Detik Ke-</label>
                                            <input type="number" :name="'questions['+idx+'][timestamp]'" x-model.number="q.timestamp" min="0" placeholder="45" class="w-full rounded-xl border-gray-300 text-sm" required>
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Tipe Pertanyaan</label>
                                            <select :name="'questions['+idx+'][question_type]'" x-model="q.question_type" class="w-full rounded-xl border-gray-300 text-sm" required>
                                                <option value="multiple_choice">Pilihan Ganda</option>
                                                <option value="true_false">Benar / Salah</option>
                                                <option value="short_answer">Jawaban Singkat</option>
                                            </select>
                                        </div>
                                        <div class="sm:col-span-2">
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Pertanyaan</label>
                                            <input type="text" :name="'questions['+idx+'][question]'" x-model="q.question" placeholder="Apa tujuan utama..." class="w-full rounded-xl border-gray-300 text-sm" required>
                                        </div>
                                    </div>

                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                        <div x-show="q.question_type === 'multiple_choice'">
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Pilihan Jawaban <span class="text-gray-400">(pisahkan koma)</span></label>
                                            <input type="text" :name="'questions['+idx+'][options]'" x-model="q.options" placeholder="Pilihan A, Pilihan B, Pilihan C" class="w-full rounded-xl border-gray-300 text-sm">
                                        </div>
                                        <div x-show="q.question_type === 'true_false'">
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Pilihan (otomatis)</label>
                                            <input type="text" :name="'questions['+idx+'][options]'" value="Benar, Salah" readonly class="w-full rounded-xl border-gray-200 bg-gray-100 text-gray-400 text-sm cursor-not-allowed">
                                        </div>
                                        <div x-show="q.question_type === 'short_answer'">
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Tidak ada pilihan <span class="text-gray-400">(jawaban teks)</span></label>
                                            <input type="text" :name="'questions['+idx+'][options]'" value="" readonly class="w-full rounded-xl border-gray-200 bg-gray-100 text-gray-400 text-sm cursor-not-allowed" placeholder="-">
                                        </div>
                                        <div>
                                            <label class="block text-xs font-bold text-gray-500 mb-1">Kunci Jawaban</label>
                                            <input type="text" :name="'questions['+idx+'][correct_answer]'" x-model="q.correct_answer" placeholder="Jawaban benar..." class="w-full rounded-xl border-gray-300 text-sm" required>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <button type="button" @click="addQuestion()" class="w-full py-3 bg-indigo-50 hover:bg-indigo-100 border-2 border-dashed border-indigo-200 text-indigo-700 font-bold rounded-2xl transition-all flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                Sisipkan Pertanyaan Baru
                            </button>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-100">
                            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-all active:scale-95">
                                Simpan Semua Pertanyaan
                            </button>
                        </div>
                    </form>
                </div>

                <!-- PANEL 5: CONFIG CODING QUIZ -->
                <div x-show="activePanel === 'coding'" class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 space-y-6" x-data="{ quizType: '{{ $material->codingQuiz?->quiz_type ?? 'fill_blank' }}' }">
                    <div>
                        <h3 class="text-2xl font-black text-gray-800">Pengaturan Kuis Koding</h3>
                        <p class="text-gray-500 text-sm mt-1">Buat tantangan koding. Pilih tipe: <em>Fill in the Blank</em>, <em>Debugging</em>, atau <em>Short Answer</em>.</p>
                    </div>

                    <form action="{{ route('materials.save_coding_quiz', $material) }}" method="POST" class="space-y-4">
                        @csrf

                        <!-- Quiz Type Selector -->
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Tipe Kuis Koding:</label>
                            <div class="grid grid-cols-3 gap-3">
                                <label :class="quizType === 'fill_blank' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-600 hover:border-indigo-200'" class="flex flex-col items-center p-4 rounded-2xl border-2 cursor-pointer transition-all">
                                    <input type="radio" name="quiz_type" value="fill_blank" x-model="quizType" class="sr-only">
                                    <span class="text-2xl mb-1">💻</span>
                                    <span class="text-xs font-bold text-center">Fill in the Blank</span>
                                </label>
                                <label :class="quizType === 'debugging' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-600 hover:border-indigo-200'" class="flex flex-col items-center p-4 rounded-2xl border-2 cursor-pointer transition-all">
                                    <input type="radio" name="quiz_type" value="debugging" x-model="quizType" class="sr-only">
                                    <span class="text-2xl mb-1">🔍</span>
                                    <span class="text-xs font-bold text-center">Debugging Quiz</span>
                                </label>
                                <label :class="quizType === 'short_answer' ? 'border-indigo-500 bg-indigo-50 text-indigo-700' : 'border-gray-200 bg-white text-gray-600 hover:border-indigo-200'" class="flex flex-col items-center p-4 rounded-2xl border-2 cursor-pointer transition-all">
                                    <input type="radio" name="quiz_type" value="short_answer" x-model="quizType" class="sr-only">
                                    <span class="text-2xl mb-1">✏️</span>
                                    <span class="text-xs font-bold text-center">Short Answer</span>
                                </label>
                            </div>
                        </div>

                        <div>
                            <label for="instruction" class="block text-sm font-bold text-gray-700 mb-2">
                                <span x-text="quizType === 'debugging' ? '🔍 Instruksi Debugging / Deskripsi Kesalahan:' : (quizType === 'short_answer' ? '✏️ Pertanyaan Short Answer:' : '💻 Instruksi Deskripsi Soal:')"></span>
                            </label>
                            <textarea name="instruction" id="instruction" rows="4" class="w-full rounded-2xl border-gray-300 text-sm focus:border-indigo-500"
                                :placeholder="quizType === 'debugging' ? 'Cth: Temukan dan perbaiki minimal 2 kesalahan sintaks pada kode Python di bawah ini...' : (quizType === 'short_answer' ? 'Cth: Apa fungsi utama dari perulangan for dalam Python?' : 'Cth: Lengkapi blok fungsi perulangan python berikut...')"
                                required>{{ $material->codingQuiz?->instruction }}</textarea>
                            @error('instruction')<p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p>@enderror
                        </div>

                        <!-- Code Template field (fill_blank and debugging only) -->
                        <div x-show="quizType !== 'short_answer'">
                            <label for="code_template" class="block text-sm font-bold text-gray-700 mb-2 font-mono">
                                <span x-text="quizType === 'debugging' ? '⚠️ Kode Bermasalah (yang ditampilkan ke siswa):' : '📄 Template Kode Sumber (gunakan [blank] untuk bagian kosong):'"></span>
                            </label>
                            <textarea name="code_template" id="code_template" rows="8" class="w-full rounded-2xl border-gray-300 font-mono text-sm bg-gray-900 text-gray-100 p-4 focus:ring-indigo-500"
                                :placeholder="quizType === 'debugging' ? 'def hitung_rata(data):\n    total = 0\n    for x in data  # missing colon\n        total =+ x  # wrong operator\n    return total / len(datas)  # wrong variable' : 'def cetak_genap():\n    for i in range(1, 11):\n        if i [blank] 2 == 0:\n            [blank](i)'">{{ $material->codingQuiz?->code_template }}</textarea>
                            @error('code_template')<p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="correct_answers" class="block text-sm font-bold text-gray-700 mb-2">
                                <span x-text="quizType === 'fill_blank' ? 'Kunci Jawaban Blanks (pisahkan dengan koma, urut):' : 'Kunci Jawaban (satu atau beberapa alternatif, pisahkan koma):'"></span>
                            </label>
                            <input type="text" name="correct_answers" id="correct_answers"
                                value="{{ $material->codingQuiz ? implode(', ', $material->codingQuiz->correct_answers) : '' }}"
                                class="w-full rounded-xl border-gray-300 text-sm focus:border-indigo-500"
                                :placeholder="quizType === 'fill_blank' ? '%, print' : (quizType === 'debugging' ? 'def hitung_rata(data):\n    total = 0\n    for x in data:\n        total += x\n    return total / len(data)' : 'Kecerdasan Buatan, AI, Artificial Intelligence')"
                                required>
                            @error('correct_answers')<p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p>@enderror
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            <div>
                                <label for="feedback_correct" class="block text-xs font-bold text-gray-500 mb-1">Feedback Jika Benar (Opsional)</label>
                                <textarea name="feedback_correct" id="feedback_correct" rows="2" class="w-full rounded-xl border-gray-300 text-xs" placeholder="Selamat! Jawaban Anda benar.">{{ $material->codingQuiz?->feedback_correct }}</textarea>
                            </div>
                            <div>
                                <label for="feedback_incorrect" class="block text-xs font-bold text-gray-500 mb-1">Feedback Jika Salah (Opsional)</label>
                                <textarea name="feedback_incorrect" id="feedback_incorrect" rows="2" class="w-full rounded-xl border-gray-300 text-xs" placeholder="Jawaban kurang tepat. Coba lagi.">{{ $material->codingQuiz?->feedback_incorrect }}</textarea>
                            </div>
                        </div>

                        <div class="flex justify-end pt-4 border-t border-gray-100">
                            <button type="submit" class="px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-all active:scale-95">
                                Simpan Kuis Koding
                            </button>
                        </div>
                    </form>
                </div>

            </div>

            <!-- Forum Diskusi Materi -->
            <div class="mt-8 space-y-6">
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                    Forum Diskusi Materi
                </h3>

                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <!-- Form Tambah Diskusi Materi -->
                    <form action="{{ route('discussions.store_material', $material) }}" method="POST" class="mb-8">
                        @csrf
                        <div class="flex items-start gap-4">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold flex-shrink-0">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="flex-1 space-y-3 text-left">
                                <input type="text" name="title" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors text-sm font-bold" placeholder="Judul / Topik Diskusi Materi Baru" required>
                                @error('title')<p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p>@enderror
                                <textarea name="content" rows="3" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors text-sm" placeholder="Tulis instruksi diskusi atau pertanyaan pemantik materi..." required></textarea>
                                @error('content')<p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p>@enderror
                                <div class="mt-3 flex justify-end">
                                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-sm transition-transform active:scale-95 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                        Kirim Diskusi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>

                    <!-- Daftar Topik Diskusi Materi -->
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
                                    <h4 class="font-extrabold text-gray-900 text-base mb-1 hover:text-indigo-600">
                                        <a href="{{ route('discussions.show', $disc) }}">{{ $disc->title }}</a>
                                    </h4>
                                    <p class="text-sm text-gray-600 line-clamp-2 leading-relaxed mb-3">{{ $disc->content }}</p>
                                    
                                    <div class="flex items-center gap-4">
                                        <a href="{{ route('discussions.show', $disc) }}" class="inline-flex items-center gap-1.5 text-xs font-bold text-indigo-600 hover:text-indigo-800">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                                            {{ $disc->replies->count() }} Komentar
                                        </a>
                                    </div>
                                </div>
                                
                                <div class="absolute top-5 right-5 opacity-0 group-hover:opacity-100 transition-opacity">
                                    <form action="{{ route('discussions.destroy', $disc) }}" method="POST" onsubmit="return confirm('Hapus diskusi ini?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="p-1.5 text-red-500 hover:bg-red-50 rounded-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                                        </button>
                                    </form>
                                </div>
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
    </div>

    <!-- Interactive Video Questions Manager JS Script -->
    <script>
        function videoQuestionManager() {
            return {
                questions: {!! json_encode($material->interactiveVideoQuestions->map(fn($q) => [
                    'timestamp'     => $q->timestamp,
                    'question_type' => $q->question_type ?? 'multiple_choice',
                    'question'      => $q->question,
                    'options'       => is_array($q->options) ? implode(', ', $q->options) : $q->options,
                    'correct_answer' => $q->correct_answer
                ])) !!},

                addQuestion() {
                    this.questions.push({
                        timestamp:      0,
                        question_type:  'multiple_choice',
                        question:       '',
                        options:        '',
                        correct_answer: ''
                    });
                },

                removeQuestion(index) {
                    this.questions.splice(index, 1);
                }
            }
        }
    </script>
</x-app-layout>
