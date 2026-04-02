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
        
        <!-- Gamification Banner -->
        <div class="bg-gradient-to-r from-indigo-600 via-purple-600 to-fuchsia-500 rounded-3xl p-8 mb-8 text-white shadow-xl relative overflow-hidden">
            <div class="absolute top-0 right-0 -mr-16 -mt-16 w-64 h-64 rounded-full bg-white opacity-5"></div>
            <div class="absolute bottom-0 left-0 -ml-16 -mb-16 w-48 h-48 rounded-full bg-white opacity-5"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between">
                <div>
                    <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider mb-3">Target Hari Ini</span>
                    <h3 class="text-3xl font-extrabold mb-2 text-transparent bg-clip-text bg-gradient-to-r from-white to-blue-100">Selesaikan Bab 1: AI</h3>
                    <p class="text-indigo-100 max-w-lg text-sm leading-relaxed">Dapatkan +50 XP dengan menyelesaikan kuis hari ini. Kumpulkan badge untuk masuk ke Top 3 Student!</p>
                </div>
                <div class="mt-6 md:mt-0 flex flex-col items-center">
                    <div class="w-24 h-24 bg-gradient-to-br from-yellow-300 to-yellow-500 rounded-full flex items-center justify-center border-4 border-white/20 shadow-inner">
                        <svg class="w-12 h-12 text-yellow-50" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 2a1 1 0 011 1v1h1a1 1 0 010 2H6v1a1 1 0 01-2 0V6H3a1 1 0 010-2h1V3a1 1 0 011-1zm0 10a1 1 0 011 1v1h1a1 1 0 110 2H6v1a1 1 0 11-2 0v-1H3a1 1 0 110-2h1v-1a1 1 0 011-1zM12 2a1 1 0 01.967.744L14.146 7.2 17.5 9.134a1 1 0 010 1.732l-3.354 1.935-1.18 4.455a1 1 0 01-1.933 0L9.854 12.8 6.5 10.866a1 1 0 010-1.732l3.354-1.935 1.18-4.455A1 1 0 0112 2z" clip-rule="evenodd" /></svg>
                    </div>
                </div>
            </div>
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
                <h4 class="text-lg font-black text-gray-800 mb-1">Pengingat! Deadline Minggu Ini 🔥</h4>
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
                                    Lihat Tugas
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left Column: Courses -->
            <div class="lg:col-span-2 space-y-6">
                <div class="flex items-center justify-between">
                    <h3 class="text-xl font-bold text-gray-800">Mata Pelajaran Saya</h3>
                    <a href="#" class="text-sm font-semibold text-indigo-600 hover:text-indigo-700">Lihat Semua</a>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Course Card: AI -->
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow group cursor-pointer">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-12 h-12 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center border border-blue-100 group-hover:bg-blue-600 group-hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.75 17L9 20l-1 1h8l-1-1-.75-3M3 13h18M5 17h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" /></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-lg">Kecerdasan Artifisial</h4>
                                <p class="text-xs font-semibold text-indigo-500">Guru Budi</p>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5 mb-2 mt-6">
                            <div class="bg-gradient-to-r from-blue-500 to-indigo-500 h-2.5 rounded-full" style="width: 45%"></div>
                        </div>
                        <div class="flex justify-between items-center text-xs text-gray-500 font-medium font-sans mt-2">
                            <span>Progress: 45%</span>
                            <span>Bab 2 dari 5</span>
                        </div>
                    </div>

                    <!-- Course Card: Coding -->
                    <div class="bg-white rounded-2xl p-5 shadow-sm border border-gray-100 hover:shadow-md transition-shadow group cursor-pointer">
                        <div class="flex items-center space-x-4 mb-4">
                            <div class="w-12 h-12 bg-teal-50 text-teal-600 rounded-xl flex items-center justify-center border border-teal-100 group-hover:bg-teal-600 group-hover:text-white transition-colors">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4" /></svg>
                            </div>
                            <div>
                                <h4 class="font-bold text-gray-800 text-lg">Koding (Python)</h4>
                                <p class="text-xs font-semibold text-teal-500">Guru Budi</p>
                            </div>
                        </div>
                        <div class="w-full bg-gray-100 rounded-full h-2.5 mb-2 mt-6">
                            <div class="bg-gradient-to-r from-teal-400 to-emerald-500 h-2.5 rounded-full" style="width: 15%"></div>
                        </div>
                        <div class="flex justify-between items-center text-xs text-gray-500 font-medium font-sans mt-2">
                            <span>Progress: 15%</span>
                            <span>Bab 1 dari 10</span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Column: Top Students -->
            <div class="space-y-6">
                <h3 class="text-xl font-bold text-gray-800">Top 3 Siswa Bulan Ini</h3>
                
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="p-5 flex items-center justify-between border-b border-gray-50 bg-gradient-to-r from-yellow-50 to-amber-50">
                        <div class="flex items-center space-x-3">
                            <div class="text-2xl font-black text-amber-500">1</div>
                            <img class="h-10 w-10 rounded-full border-2 border-white shadow-sm" src="https://ui-avatars.com/api/?name=Siswa Andi" alt="">
                            <div>
                                <div class="text-sm font-bold text-gray-800">Siswa Andi (Kamu)</div>
                                <div class="text-xs text-gray-500">10A</div>
                            </div>
                        </div>
                        <div class="text-right">
                            <div class="text-sm font-black text-amber-600">850 XP</div>
                        </div>
                    </div>
                    
                    <div class="p-5 flex items-center justify-between border-b border-gray-50 hover:bg-gray-50 transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="text-lg font-bold text-gray-400 w-4 text-center">2</div>
                            <img class="h-10 w-10 rounded-full border-2 border-gray-200" src="https://ui-avatars.com/api/?name=Budi" alt="">
                            <div>
                                <div class="text-sm font-semibold text-gray-700">Budi Santoso</div>
                                <div class="text-xs text-gray-500">10B</div>
                            </div>
                        </div>
                        <div class="text-right text-sm font-bold text-gray-600">720 XP</div>
                    </div>

                    <div class="p-5 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div class="flex items-center space-x-3">
                            <div class="text-lg font-bold text-gray-400 w-4 text-center">3</div>
                            <img class="h-10 w-10 rounded-full border-2 border-gray-200" src="https://ui-avatars.com/api/?name=Citra" alt="">
                            <div>
                                <div class="text-sm font-semibold text-gray-700">Citra Lestari</div>
                                <div class="text-xs text-gray-500">10A</div>
                            </div>
                        </div>
                        <div class="text-right text-sm font-bold text-gray-600">650 XP</div>
                    </div>
                </div>
            </div>
        </div>
        @endrole
    </div>
</x-app-layout>
