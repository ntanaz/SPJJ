<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.courses') }}" class="p-2 bg-white rounded-full text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
                {{ $course->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 min-h-screen" x-data="{ showActivityModal: false }">
        <!-- Hero Section Course -->
        <div class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 rounded-3xl p-8 mb-8 text-white shadow-xl relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between">
                <div>
                    <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider mb-3">Mata Pelajaran Aktif</span>
                    <h3 class="text-3xl font-extrabold mb-2">{{ $course->name }}</h3>
                    <p class="text-indigo-100 max-w-2xl text-sm leading-relaxed">{{ $course->description }}</p>
                </div>
            </div>
        </div>

        @role('guru|teacher|admin')
        <div class="mb-6 mt-4 flex justify-end">
            <button @click="showActivityModal = true" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm transition-transform active:scale-95 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambahkan Aktivitas atau Sumber Daya
            </button>
        </div>
        @endrole

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Left col: Silabus / Materi list -->
            <div class="md:col-span-2 space-y-4">
                <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    Modul Pembelajaran
                </h3>
                
                @forelse($course->materials as $material)
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 {{ $material->is_locked && $loop->iteration > 1 ? 'opacity-60 bg-gray-50' : 'hover:shadow-md hover:border-indigo-100 cursor-pointer' }} transition-all flex justify-between items-center group">
                        <div class="flex items-center gap-4">
                            <!-- Icon Type -->
                            @php
                                $typeConfigs = [
                                    'pdf' => ['bg' => 'bg-red-50 text-red-500', 'icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
                                    'video' => ['bg' => 'bg-blue-50 text-blue-500', 'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                                    'meeting_link' => ['bg' => 'bg-purple-50 text-purple-500', 'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                                    'slide' => ['bg' => 'bg-yellow-50 text-yellow-500', 'icon' => 'M7 12l3-3 3 3 4-4M8 21l4-4 4 4M3 4h18M4 4h16v12a1 1 0 01-1 1H5a1 1 0 01-1-1V4z'],
                                ];
                                $config = $typeConfigs[$material->type] ?? ['bg' => 'bg-gray-50 text-gray-500', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'];
                            @endphp
                            
                            <div class="h-12 w-12 rounded-xl {{ $config['bg'] }} flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}" /></svg>
                            </div>
                            
                            <div>
                                <h4 class="font-bold text-gray-800 text-lg group-hover:text-indigo-600 transition-colors">Bab {{ $material->order }}: {{ $material->title }}</h4>
                                <p class="text-sm text-gray-500 line-clamp-1">{{ $material->description ?: 'Format: ' . strtoupper($material->type) }}</p>
                            </div>
                        </div>
                        
                        <div>
                            @if($material->is_locked && $loop->iteration > 1)
                                <div class="px-3 py-1 bg-gray-200 text-gray-600 rounded-lg flex items-center gap-1 text-xs font-bold">
                                    <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                                    Terkunci
                                </div>
                            @else
                                <a href="#" class="px-5 py-2 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-xl font-bold transition-colors text-sm">
                                    Buka Materi
                                </a>
                            @endif
                        </div>
                    </div>
                @empty
                    <div class="py-8 text-center bg-white rounded-2xl border border-gray-100 border-dashed">
                        <p class="text-gray-500 font-medium">Belum ada materi pembelajaran yang ditambahkan guru.</p>
                    </div>
                @endforelse

                <!-- Presensi Section -->
                <h3 class="text-xl font-bold text-gray-800 mb-4 mt-8 flex items-center gap-2">
                    <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                    Presensi & Kehadiran Kelas
                </h3>
                
                @forelse($course->attendances as $attendance)
                    <div class="bg-gradient-to-r from-emerald-50 to-white rounded-2xl p-5 shadow-sm border border-emerald-100 flex justify-between items-center group hover:shadow-md transition-all">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-xl bg-emerald-100 text-emerald-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-lg group-hover:text-emerald-600 transition-colors">{{ $attendance->title }}</h4>
                                <p class="text-sm text-gray-500 font-medium">{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('d F Y') }} | {{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }} WIB</p>
                            </div>
                        </div>

                        <div class="flex items-center gap-2">
                        @role('siswa')
                            @php
                                $record = $attendance->records->where('user_id', auth()->id())->first();
                            @endphp
                            @if($record)
                                <span class="px-4 py-2 bg-gray-100 text-gray-600 rounded-xl font-bold text-xs uppercase tracking-wider flex items-center gap-1">
                                    <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                    Terkonfirmasi ({{ $record->status }})
                                </span>
                            @else
                                <a href="{{ route('student.attendances.show', $attendance) }}" class="px-5 py-2 {{ $attendance->isCurrentlyOpen() ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-gray-200 text-gray-500 cursor-not-allowed' }} rounded-xl font-bold transition-colors text-sm shadow-sm whitespace-nowrap">
                                    {{ $attendance->isCurrentlyOpen() ? 'Isi Kehadiran' : 'Ditutup' }}
                                </a>
                            @endif
                        @else
                            <a href="{{ route('attendances.show', $attendance) }}" class="px-5 py-2 bg-emerald-100 text-emerald-700 hover:bg-emerald-600 hover:text-white rounded-xl font-bold transition-colors text-sm shadow-sm whitespace-nowrap">
                                Lihat Laporan
                            </a>
                            <form action="{{ route('attendances.destroy', $attendance) }}" method="POST" onsubmit="return confirm('Hapus sesi absensi ini?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-2 text-red-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                </button>
                            </form>
                        @endrole
                        </div>
                    </div>
                @empty
                    <div class="py-6 text-center bg-gray-50/50 rounded-2xl border border-gray-100 border-dashed">
                        <p class="text-gray-500 text-sm font-medium">Belum ada jadwal presensi / absensi.</p>
                    </div>
                @endforelse

                <!-- Penugasan Section -->
                <h3 class="text-xl font-bold text-gray-800 mb-4 mt-8 flex items-center gap-2">
                    <svg class="w-6 h-6 text-pink-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    Daftar Penugasan
                </h3>

                @forelse($course->assignments as $assignment)
                    <div class="bg-gradient-to-r from-pink-50 to-white rounded-2xl p-5 shadow-sm border border-pink-100 flex justify-between items-center group hover:shadow-md transition-all">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-xl bg-pink-100 text-pink-500 flex items-center justify-center group-hover:rotate-12 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-lg group-hover:text-pink-600 transition-colors">{{ $assignment->title }}</h4>
                                <p class="text-sm font-bold text-red-500">Tenggat: {{ \Carbon\Carbon::parse($assignment->deadline)->translatedFormat('d M Y, H:i') }}</p>
                            </div>
                        </div>
                        <a href="{{ route('student.assignments.show', $assignment) }}" class="px-5 py-2 bg-pink-600 text-white rounded-xl font-bold hover:bg-pink-700 transition-colors text-sm shadow-sm">
                            Kumpulkan Tugas
                        </a>
                    </div>
                @empty
                    <div class="py-6 text-center bg-gray-50/50 rounded-2xl border border-gray-100 border-dashed">
                        <p class="text-gray-500 text-sm font-medium">Bagus! Tidak ada tugas yang menunggu saat ini.</p>
                    </div>
                @endforelse

                <!-- Kuis & Ujian Section -->
                <h3 class="text-xl font-bold text-gray-800 mb-4 mt-8 flex items-center gap-2">
                    <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                    Kuis & Ujian Online
                </h3>

                @forelse($course->quizzes as $quiz)
                    <div class="bg-gradient-to-r from-amber-50 to-white rounded-2xl p-5 shadow-sm border border-amber-100 flex justify-between items-center group hover:shadow-md transition-all">
                        <div class="flex items-center gap-4">
                            <div class="h-12 w-12 rounded-xl bg-amber-100 text-amber-500 flex items-center justify-center group-hover:rotate-12 transition-transform">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-lg group-hover:text-amber-600 transition-colors">{{ $quiz->title }}</h4>
                                <p class="text-sm text-gray-500">{{ Str::limit($quiz->description, 50) }}</p>
                            </div>
                        </div>
                        @role('siswa')
                            <a href="{{ route('student.quizzes.show', $quiz) }}" class="px-5 py-2 bg-amber-500 text-white rounded-xl font-bold hover:bg-amber-600 transition-colors text-sm shadow-sm">
                                Mulai Kuis / Ujian
                            </a>
                        @else
                            <a href="{{ route('quizzes.show', $quiz) }}" class="px-5 py-2 bg-amber-100 text-amber-700 hover:bg-amber-600 hover:text-white rounded-xl font-bold transition-colors text-sm shadow-sm whitespace-nowrap">
                                Kelola Soal
                            </a>
                        @endrole
                    </div>
                @empty
                    <div class="py-6 text-center bg-gray-50/50 rounded-2xl border border-gray-100 border-dashed">
                        <p class="text-gray-500 text-sm font-medium">Belum ada Kuis/Ujian terjadwal.</p>
                    </div>
                @endforelse

                <!-- Forum Diskusi Section -->
                <h3 class="text-xl font-bold text-gray-800 mb-4 mt-8 flex items-center gap-2">
                    <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                    Ruang Diskusi Kelas
                </h3>

                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    @role('guru|teacher|admin')
                    <!-- Form Tambah Diskusi -->
                    <form action="{{ route('discussions.store', $course) }}" method="POST" class="mb-8">
                        @csrf
                        <div class="flex items-start gap-4">
                            <div class="h-10 w-10 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold flex-shrink-0">
                                {{ substr(auth()->user()->name, 0, 1) }}
                            </div>
                            <div class="flex-1 space-y-3">
                                <input type="text" name="title" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors text-sm font-bold" placeholder="Judul / Topik Diskusi" required>
                                @error('title')<p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p>@enderror
                                <textarea name="content" rows="3" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors text-sm" placeholder="Jelaskan pertanyaan / pemantik diskusi untuk siswa..." required></textarea>
                                @error('content')<p class="mt-1 text-xs text-red-600 font-bold">{{ $message }}</p>@enderror
                                <div class="mt-3 flex justify-end">
                                    <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold rounded-xl shadow-sm transition-transform active:scale-95 flex items-center gap-2">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                                        Buka Sesi Diskusi
                                    </button>
                                </div>
                            </div>
                        </div>
                    </form>
                    @endrole

                    <!-- Daftar Topik Diskusi -->
                    <div class="space-y-4">
                        @forelse($course->discussions as $discussion)
                            <div class="bg-gray-50 hover:bg-white rounded-2xl p-5 border border-gray-100 hover:shadow-md hover:border-indigo-100 transition-all group flex justify-between items-center relative">
                                <div class="flex items-start gap-4">
                                    <div class="h-12 w-12 rounded-xl {{ $discussion->user->hasRole(['guru', 'teacher', 'admin']) ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-200 text-gray-600' }} flex items-center justify-center flex-shrink-0">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" /></svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800 text-lg group-hover:text-indigo-600 transition-colors">{{ $discussion->title }}</h4>
                                        <p class="text-sm text-gray-500 mb-2">Oleh <span class="font-semibold">{{ $discussion->user->name }}</span> &bull; {{ $discussion->created_at->diffForHumans() }}</p>
                                        
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-indigo-100 text-indigo-800">
                                            {{ $discussion->replies ? $discussion->replies->count() : 0 }} Balasan
                                        </span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('discussions.show', $discussion) }}" class="px-5 py-2 bg-white text-indigo-600 border border-indigo-200 hover:bg-indigo-50 hover:border-indigo-300 rounded-xl font-bold transition-colors text-sm shadow-sm whitespace-nowrap">
                                        Masuk Forum
                                    </a>
                                    
                                    @role('guru|teacher|admin')
                                        <form action="{{ route('discussions.destroy', $discussion) }}" method="POST" onsubmit="return confirm('Hapus topik ini beserta semua balasan siswa?');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-2 text-red-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                            </button>
                                        </form>
                                    @endrole
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-8">
                                <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-indigo-50 text-indigo-300 mb-3">
                                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                                </div>
                                <p class="text-gray-500 font-medium text-sm">Belum ada diskusi di kelas ini.<br>Jadilah yang pertama untuk bertanya!</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Right col -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Progress Belajar Anda</h3>
                    
                    <div class="w-full bg-gray-100 rounded-full h-4 mb-2">
                        <div class="bg-gradient-to-r from-emerald-400 to-teal-500 h-4 rounded-full" style="width: 10%"></div>
                    </div>
                    <div class="flex justify-between items-center text-xs text-gray-500 font-bold mb-6">
                        <span>10% Selesai</span>
                        <span>0/{{ $course->materials->count() }} Bab</span>
                    </div>

                    <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100 text-center text-indigo-800 text-sm">
                        Selesaikan materi ini dan dapatkan <strong>+100 XP Badge Baru!</strong> 🏆
                    </div>
                </div>
            </div>
        </div>
        <!-- Modal Activity Chooser Moodle-style -->
        <div x-show="showActivityModal" style="display: none;" class="fixed inset-0 z-[99] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showActivityModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" @click="showActivityModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showActivityModal" x-transition.scale class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-100">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-xl font-black text-gray-800 flex items-center gap-2" id="modal-title">
                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Tambahkan Aktivitas atau Sumber Daya
                        </h3>
                        <button @click="showActivityModal = false" class="text-gray-400 hover:text-red-500 transition-colors p-2 bg-white rounded-full hover:bg-red-50 hover:shadow-sm">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    
                    <div class="p-6 max-h-[65vh] overflow-y-auto hide-scrollbar z-50 relative bg-white">
                        <!-- 1. Activities -->
                        <h4 class="font-bold text-gray-700 uppercase tracking-widest text-xs mb-4 flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-pink-500"></div> AKTIVITAS (ACTIVITIES)</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
                            <a href="{{ route('assignments.create') }}" class="flex flex-col items-center p-4 bg-white border border-gray-100 hover:border-pink-300 rounded-2xl hover:shadow-md transition-all group cursor-pointer">
                                <div class="h-14 w-14 bg-pink-50 text-pink-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-800 text-center">Assignment</span>
                            </a>
                            
                            <a href="{{ route('quizzes.create') }}" class="flex flex-col items-center p-4 bg-white border border-gray-100 hover:border-amber-300 rounded-2xl hover:shadow-md transition-all group cursor-pointer">
                                <div class="h-14 w-14 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-800 text-center">Quiz</span>
                            </a>

                            <button type="button" @click="showActivityModal = false; window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });" class="flex flex-col items-center p-4 bg-white border border-gray-100 hover:border-indigo-300 rounded-2xl hover:shadow-md transition-all group cursor-pointer">
                                <div class="h-14 w-14 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-800 text-center">Forum</span>
                            </button>

                            <!-- Add dummy elements for visuals as requested -->
                            <a href="{{ route('attendances.create', ['course_id' => $course->id]) }}" class="flex flex-col items-center p-4 bg-white border border-gray-100 hover:border-blue-300 rounded-2xl hover:shadow-md transition-all group cursor-pointer">
                                <div class="h-14 w-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform"><svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                                <span class="text-sm font-bold text-gray-800 text-center">Attendance</span>
                            </a>

                            <div onclick="alert('Module segera hadir')" class="flex flex-col items-center p-4 bg-gray-50 border border-gray-100 rounded-2xl opacity-70 group cursor-not-allowed">
                                <div class="h-14 w-14 bg-gray-200 text-gray-500 rounded-xl flex items-center justify-center mb-3"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg></div>
                                <span class="text-xs font-bold text-gray-800 text-center">Checklist / Wiki</span>
                            </div>
                        </div>

                        <!-- 2. Resources -->
                        <h4 class="font-bold text-gray-700 uppercase tracking-widest text-xs mb-4 flex items-center gap-2 mt-4"><div class="w-2 h-2 rounded-full bg-emerald-500"></div> SUMBER DAYA (RESOURCES)</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                            <a href="{{ route('materials.create') }}" class="flex flex-col items-center p-4 bg-white border border-gray-100 hover:border-emerald-300 rounded-2xl hover:shadow-md transition-all group cursor-pointer">
                                <div class="h-14 w-14 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-800 text-center">File / Page</span>
                            </a>

                            <a href="{{ route('materials.create') }}" class="flex flex-col items-center p-4 bg-white border border-gray-100 hover:border-sky-300 rounded-2xl hover:shadow-md transition-all group cursor-pointer">
                                <div class="h-14 w-14 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-800 text-center">URL</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
