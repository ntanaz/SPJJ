<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
                {{ __('Dashboard') }}
            </h2>
            <div class="text-sm font-medium text-gray-500">
                Halo, {{ explode(' ', Auth::user()->name)[0] }}! 👋
            </div>
        </div>
    </x-slot>

    <div class="py-6">
        @if(isset($announcements) && $announcements->count() > 0)
        <!-- Announcements -->
        <div class="mb-8 space-y-4">
            @foreach($announcements as $announcement)
            <div class="bg-white rounded-2xl border border-{{ $announcement->urgency_level == 'urgent' ? 'red' : ($announcement->urgency_level == 'important' ? 'yellow' : 'blue') }}-100 shadow-sm p-4 flex items-start space-x-4">
                <div class="flex-shrink-0 mt-1">
                    @if($announcement->urgency_level == 'urgent')
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" /></svg>
                        </div>
                    @elseif($announcement->urgency_level == 'important')
                        <div class="w-10 h-10 rounded-full bg-yellow-100 flex items-center justify-center text-yellow-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        </div>
                    @else
                        <div class="w-10 h-10 rounded-full bg-blue-100 flex items-center justify-center text-blue-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5.882V19.24a1.76 1.76 0 01-3.417.592l-2.147-6.15M18 13a3 3 0 100-6M5.436 13.683A4.001 4.001 0 017 6h1.832c4.1 0 7.625-1.234 9.168-3v14c-1.543-1.766-5.067-3-9.168-3H7a3.988 3.988 0 01-1.564-.317z" /></svg>
                        </div>
                    @endif
                </div>
                <div>
                    <h4 class="text-lg font-bold text-gray-900">{{ $announcement->title }}</h4>
                    <p class="text-sm text-gray-600 mt-1">{{ $announcement->content }}</p>
                    <div class="text-xs text-gray-400 mt-2 font-medium">{{ $announcement->created_at->diffForHumans() }}</div>
                </div>
            </div>
            @endforeach
        </div>
        @endif
        @role('admin')
        <!-- Admin Dashboard -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg transform transition hover:scale-105">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-indigo-100 text-sm font-semibold mb-1 uppercase tracking-wider">Total Siswa</p>
                        <h3 class="text-4xl font-bold">128</h3>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl p-6 text-white shadow-lg transform transition hover:scale-105">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-teal-100 text-sm font-semibold mb-1 uppercase tracking-wider">Total Guru</p>
                        <h3 class="text-4xl font-bold">12</h3>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-blue-500 to-cyan-500 rounded-2xl p-6 text-white shadow-lg transform transition hover:scale-105">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-cyan-100 text-sm font-semibold mb-1 uppercase tracking-wider">Mata Pelajaran</p>
                        <h3 class="text-4xl font-bold">8</h3>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    </div>
                </div>
            </div>
        </div>
        @endrole

        @role('guru')
        <!-- Guru Dashboard -->
        <h3 class="text-xl font-bold text-gray-800 mb-4">Kelas Anda Hari Ini</h3>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
            <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex justify-between items-center">
                <div class="flex items-center space-x-4">
                    <div class="h-14 w-14 bg-indigo-50 rounded-2xl flex items-center justify-center border border-indigo-100">
                        <svg class="w-7 h-7 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
                    </div>
                    <div>
                        <h4 class="text-lg font-bold text-gray-800">10A - Koding</h4>
                        <p class="text-sm text-gray-500">Dasar-dasar Pemrograman Python</p>
                    </div>
                </div>
                <button class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-semibold rounded-lg transition-colors shadow-sm">
                    Mulai Mengajar
                </button>
            </div>
            
            <div class="bg-indigo-600 rounded-2xl p-6 shadow-md text-white flex justify-between items-center relative overflow-hidden group">
                <div class="absolute inset-0 bg-gradient-to-r from-indigo-600 to-purple-600 opacity-90"></div>
                <!-- Abstract circles -->
                <div class="absolute -right-10 -top-10 w-32 h-32 rounded-full bg-white opacity-10 group-hover:scale-110 transition-transform duration-500"></div>
                <div class="absolute right-10 -bottom-10 w-24 h-24 rounded-full bg-white opacity-10 group-hover:scale-110 transition-transform duration-500 delay-100"></div>
                
                <div class="relative z-10">
                    <h4 class="text-xl font-bold mb-1">Upload Materi Baru</h4>
                    <p class="text-indigo-100 text-sm">Bagikan ilmu dengan siswa</p>
                </div>
                <div class="relative z-10 h-12 w-12 bg-white/20 rounded-full flex items-center justify-center backdrop-blur-md cursor-pointer hover:bg-white/30 transition-colors">
                    <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                </div>
            </div>
        </div>
        @endrole

        @role('siswa')
        <!-- Siswa Dashboard -->
        
        <!-- Siswa Progress & Gamification Section -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
            <!-- Glassmorphic Welcome Banner & Overall Progress -->
            <div class="lg:col-span-2 bg-gradient-to-br from-indigo-600 via-indigo-700 to-purple-800 rounded-3xl p-6 text-white shadow-xl relative overflow-hidden flex flex-col justify-between">
                <!-- Background decorative elements -->
                <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-5 pointer-events-none"></div>
                <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-48 h-48 rounded-full bg-white opacity-5 pointer-events-none"></div>
                
                <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between">
                    <div>
                        <span class="inline-block px-3.5 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-black uppercase tracking-wider mb-4 border border-white/10 shadow-sm">
                            Dashboard Siswa
                        </span>
                        <h3 class="text-3xl font-extrabold mb-2 leading-tight">
                            Lanjutkan Belajar Anda, {{ explode(' ', Auth::user()->name)[0] }}! 👋
                        </h3>
                        <p class="text-indigo-100 text-sm font-medium mb-6">
                            Terus selesaikan aktivitas pembelajaran untuk mengumpulkan XP dan meraih peringkat tertinggi!
                        </p>
                    </div>
                    
                    <!-- Dynamic Badge Tier Display -->
                    <div class="mt-4 md:mt-0 flex flex-col items-center bg-white/10 backdrop-blur-md px-5 py-4 rounded-2xl border border-white/20 shadow-lg text-center min-w-[140px] transform hover:scale-105 transition-transform duration-300">
                        <span class="text-4xl mb-1 filter drop-shadow">{{ $badgeIcon }}</span>
                        <span class="text-xs font-bold text-indigo-200 uppercase tracking-widest">Peringkat</span>
                        <span class="text-base font-black text-white mt-0.5">{{ $badge }}</span>
                    </div>
                </div>

                <!-- Overall Progress Bar -->
                <div class="relative z-10 mt-6 pt-4 border-t border-white/10 w-full">
                    <div class="flex justify-between items-center text-xs font-bold text-indigo-100 mb-1.5">
                        <span class="flex items-center gap-1.5">
                            <svg class="w-4 h-4 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            Progres Belajar Keseluruhan
                        </span>
                        <span class="bg-white/20 px-2 py-0.5 rounded text-[11px] font-black">{{ $overallProgress }}%</span>
                    </div>
                    <div class="w-full bg-white/10 rounded-full h-3.5 p-0.5 border border-white/10 shadow-inner">
                        <div class="bg-gradient-to-r from-emerald-400 via-teal-300 to-green-400 h-2 rounded-full transition-all duration-1000 shadow-md" style="width: {{ $overallProgress }}%"></div>
                    </div>
                </div>
            </div>

            <!-- Gamification Points & Activity Card -->
            <div class="bg-white rounded-3xl p-6 shadow-md border border-gray-100 flex flex-col justify-between">
                <div>
                    <h4 class="text-gray-800 font-black text-lg mb-4 flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z" /></svg>
                        Ringkasan Poin & Progress
                    </h4>
                    
                    <div class="grid grid-cols-2 gap-4 mb-4">
                        <!-- Total XP Card -->
                        <div class="bg-yellow-50/50 rounded-2xl p-4 border border-yellow-100 flex flex-col justify-between">
                            <span class="text-xs font-bold text-yellow-700 uppercase">Total XP</span>
                            <div class="flex items-baseline gap-1 mt-2">
                                <span class="text-3xl font-black text-yellow-600">{{ $totalXp }}</span>
                                <span class="text-xs font-bold text-yellow-500">XP</span>
                            </div>
                        </div>

                        <!-- Completed Activities Card -->
                        <div class="bg-emerald-50/50 rounded-2xl p-4 border border-emerald-100 flex flex-col justify-between">
                            <span class="text-xs font-bold text-emerald-700 uppercase">Aktivitas Selesai</span>
                            <div class="flex items-baseline gap-1 mt-2">
                                <span class="text-3xl font-black text-emerald-600">{{ $completedActivitiesCount }}</span>
                                <span class="text-xs font-bold text-emerald-500">Aktivitas</span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Next Tier Info -->
                <div class="bg-gray-50 rounded-2xl p-3.5 border border-gray-100 text-xs font-medium text-gray-600 flex items-center justify-between">
                    @if($totalXp < 100)
                        <span>Kumpulkan <b>{{ 100 - $totalXp }} XP</b> lagi untuk naik ke tier 🚀 <b>Penjelajah</b>!</span>
                    @elseif($totalXp < 300)
                        <span>Kumpulkan <b>{{ 300 - $totalXp }} XP</b> lagi untuk naik ke tier 🧠 <b>Cendekiawan</b>!</span>
                    @elseif($totalXp < 600)
                        <span>Kumpulkan <b>{{ 600 - $totalXp }} XP</b> lagi untuk naik ke tier 🏆 <b>Zenith Master</b>!</span>
                    @else
                        <span>Selamat! Anda berada di tingkat tertinggi 🏆 <b>Zenith Master</b>!</span>
                    @endif
                </div>
            </div>
        </div>

        <!-- Progress Per BAB (Module) -->
        <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 mb-8">
            <h3 class="text-lg font-black text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                Progres Belajar per BAB (Modul)
            </h3>
            
            @if(isset($modulesProgress) && $modulesProgress->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($modulesProgress as $modProgress)
                        <div class="p-4 bg-gray-50/50 hover:bg-gray-50 border border-gray-100 rounded-2xl transition-all duration-300 transform hover:-translate-y-0.5 flex flex-col justify-between">
                            <div class="flex justify-between items-start gap-4 mb-2">
                                <h4 class="font-bold text-gray-800 text-sm line-clamp-1">{{ $modProgress['title'] }}</h4>
                                <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 font-extrabold text-[10px] rounded-full border border-indigo-100">
                                    {{ $modProgress['percentage'] }}% Selesai
                                </span>
                            </div>
                            <div class="w-full bg-gray-200/70 rounded-full h-2">
                                <div class="bg-gradient-to-r from-indigo-500 to-indigo-600 h-2 rounded-full transition-all duration-500" style="width: {{ $modProgress['percentage'] }}%"></div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="text-center py-6 bg-gray-50 rounded-2xl border border-gray-100 border-dashed">
                    <p class="text-gray-500 text-sm font-medium">Belum ada modul terdaftar atau progres kelas.</p>
                </div>
            @endif
        </div>

        @if(isset($upcomingAssignments) && $upcomingAssignments->count() > 0)
        <!-- Deadline Reminder -->
        <div class="mb-8 bg-gradient-to-r from-red-50 to-pink-50 rounded-3xl p-6 border border-pink-100 shadow-sm flex flex-col md:flex-row gap-6">
            <div class="flex-shrink-0 flex justify-center md:block">
                <div class="h-16 w-16 bg-pink-100 text-pink-600 rounded-2xl flex items-center justify-center animate-pulse">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                </div>
            </div>
            <div class="flex-1 w-full">
                <h4 class="text-lg font-black text-gray-800 mb-1">Pengingat! Tugas Mendekati Deadline 🔥</h4>
                <p class="text-sm font-medium text-gray-600 mb-4">Ayo kumpulkan tugasmu sebelum waktunya habis.</p>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach($upcomingAssignments as $assignment)
                        <div class="bg-white p-4 rounded-2xl border border-pink-100 shadow-sm flex justify-between items-center group hover:border-pink-300 transition-colors">
                            <div class="w-2/3">
                                <h5 class="font-bold text-gray-800 text-sm truncate group-hover:text-pink-600 transition-colors">{{ $assignment->title }}</h5>
                                <p class="text-xs font-medium text-gray-500 mb-1">{{ $assignment->course->name }}</p>
                                <p class="text-xs font-bold text-red-500 flex items-center gap-1">
                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                    {{ \Carbon\Carbon::parse($assignment->deadline)->diffForHumans() }}
                                </p>
                            </div>
                            <div class="text-right w-1/3 pl-2">
                                <a href="{{ route('student.assignments.show', $assignment) }}" class="inline-block px-4 py-2 bg-pink-100 text-pink-700 hover:bg-pink-600 hover:text-white text-xs font-bold rounded-xl transition-colors shadow-sm whitespace-nowrap">
                                    Kerjakan
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Tasks & Quizzes -->
            <div class="lg:col-span-2 space-y-8">
                
                <!-- Active Quizzes -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-6 h-6 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            Kuis Aktif
                        </h3>
                    </div>
                    @if(isset($activeQuizzes) && $activeQuizzes->count() > 0)
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            @foreach($activeQuizzes as $quiz)
                                <div class="bg-gradient-to-br from-amber-50 to-white rounded-2xl p-5 shadow-sm border border-amber-100 hover:shadow-md transition-shadow">
                                    <div class="flex justify-between items-start mb-4">
                                        <div class="h-10 w-10 bg-amber-100 text-amber-600 rounded-xl flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                        </div>
                                        @if($quiz->deadline)
                                        <span class="px-2 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded-lg">{{ \Carbon\Carbon::parse($quiz->deadline)->format('d M') }}</span>
                                        @endif
                                    </div>
                                    <h4 class="font-bold text-gray-800 mb-1">{{ $quiz->title }}</h4>
                                    <p class="text-xs text-gray-500 mb-4">{{ $quiz->course->name }} &bull; {{ $quiz->time_limit_minutes ?? '-' }} Menit</p>
                                    <a href="{{ route('student.quizzes.show', $quiz) }}" class="block w-full py-2 text-center bg-amber-500 hover:bg-amber-600 text-white font-bold rounded-xl transition-colors text-sm shadow-sm">Mulai Kuis</a>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="bg-gray-50 rounded-2xl p-6 text-center border border-gray-100 border-dashed">
                            <p class="text-gray-500 font-medium">Tidak ada kuis aktif saat ini.</p>
                        </div>
                    @endif
                </div>

                <!-- Recent Assignments -->
                <div>
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                            <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2" /></svg>
                            Tugas Terbaru
                        </h3>
                        <a href="{{ route('student.courses') }}" class="text-sm font-semibold text-emerald-600 hover:text-emerald-700">Lihat Semua</a>
                    </div>
                    <div class="space-y-3">
                        @forelse($recentAssignments ?? [] as $assignment)
                            <a href="{{ route('student.assignments.show', $assignment) }}" class="flex items-center justify-between p-4 bg-white hover:bg-emerald-50 rounded-2xl border border-gray-100 hover:border-emerald-200 shadow-sm transition-all group">
                                <div class="flex items-center gap-4">
                                    <div class="h-10 w-10 bg-emerald-100 text-emerald-600 rounded-xl flex items-center justify-center group-hover:scale-110 transition-transform">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                                    </div>
                                    <div>
                                        <h4 class="font-bold text-gray-800 group-hover:text-emerald-700 transition-colors">{{ $assignment->title }}</h4>
                                        <p class="text-xs text-gray-500">{{ $assignment->course->name }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    @if($assignment->deadline)
                                        <p class="text-xs font-bold text-gray-500 mb-1">Tenggat</p>
                                        <p class="text-sm font-bold text-emerald-600">{{ \Carbon\Carbon::parse($assignment->deadline)->format('d M Y') }}</p>
                                    @endif
                                </div>
                            </a>
                        @empty
                            <div class="bg-gray-50 rounded-2xl p-6 text-center border border-gray-100 border-dashed">
                                <p class="text-gray-500 font-medium">Belum ada tugas baru.</p>
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>

            <!-- Right Column: Schedule -->
            <div class="space-y-6">
                <h3 class="text-xl font-bold text-gray-800 flex items-center gap-2">
                    <svg class="w-6 h-6 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                    Jadwal Hari Ini
                </h3>
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    @forelse($todaySchedule ?? [] as $schedule)
                        <div class="p-4 border-b border-gray-50 hover:bg-blue-50 transition-colors flex items-start gap-4">
                            <div class="flex flex-col items-center justify-center h-12 w-14 bg-blue-100 text-blue-700 rounded-xl">
                                <span class="text-xs font-bold uppercase">{{ \Carbon\Carbon::parse($schedule->start_time)->format('H:i') }}</span>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800">{{ $schedule->title }}</h4>
                                <p class="text-xs text-gray-500 mb-1">{{ $schedule->course->name }}</p>
                                <a href="{{ route('student.attendances.show', $schedule) }}" class="text-xs font-bold text-blue-600 hover:text-blue-800">Isi Presensi &rarr;</a>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center">
                            <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400 mb-3">
                                <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <p class="text-gray-500 font-medium text-sm">Tidak ada jadwal kelas hari ini. Waktunya istirahat atau mandiri!</p>
                        </div>
                    @endforelse
                </div>
            </div>
        </div>
        @endrole
    </div>
</x-app-layout>
