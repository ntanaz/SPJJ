<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
            {{ $user->hasRole(['guru', 'teacher', 'admin']) ? __('Pemantauan Tugas & Kuis') : __('To-Do List Saya') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Header Info -->
        <div class="bg-gradient-to-r from-indigo-600 to-purple-600 rounded-3xl p-8 mb-8 text-white shadow-xl relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
            <div class="relative z-10">
                <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider mb-3">
                    {{ $user->hasRole(['guru', 'teacher', 'admin']) ? 'Portal Pendidik' : 'Progres Belajar' }}
                </span>
                <h3 class="text-3xl font-extrabold mb-2">
                    {{ $user->hasRole(['guru', 'teacher', 'admin']) ? 'Pemantauan Aktivitas Kelas' : 'Daftar Tugas & Kuis Kelas' }}
                </h3>
                <p class="text-indigo-100 max-w-2xl text-sm leading-relaxed">
                    {{ $user->hasRole(['guru', 'teacher', 'admin']) ? 'Pantau pengumpulan tugas siswa, lakukan penilaian cepat, dan kelola kuis interaktif dari seluruh kelas Anda.' : 'Pantau dan selesaikan semua penugasan serta kuis tepat waktu untuk menjaga konsistensi belajar Anda.' }}
                </p>
            </div>
        </div>

        @if(!$user->hasRole(['guru', 'teacher', 'admin']))
            <!-- Student Filters -->
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8">
                <div>
                    <h4 class="text-lg font-bold text-gray-800">Status Tugas</h4>
                    <p class="text-xs text-gray-500">Gunakan filter untuk menampilkan tugas tertentu.</p>
                </div>
                <div class="flex flex-wrap gap-2">
                    <a href="{{ route('student.todos', ['filter' => 'pending']) }}" class="px-4 py-2 text-sm font-bold rounded-xl shadow-sm transition-all {{ $filter === 'pending' ? 'bg-indigo-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-100' }}">
                        Menunggu (Pending)
                    </a>
                    <a href="{{ route('student.todos', ['filter' => 'completed']) }}" class="px-4 py-2 text-sm font-bold rounded-xl shadow-sm transition-all {{ $filter === 'completed' ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-100' }}">
                        Selesai (Completed)
                    </a>
                    <a href="{{ route('student.todos', ['filter' => 'overdue']) }}" class="px-4 py-2 text-sm font-bold rounded-xl shadow-sm transition-all {{ $filter === 'overdue' ? 'bg-red-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-100' }}">
                        Terlewat (Overdue)
                    </a>
                    <a href="{{ route('student.todos', ['filter' => 'all']) }}" class="px-4 py-2 text-sm font-bold rounded-xl shadow-sm transition-all {{ $filter === 'all' ? 'bg-gray-800 text-white' : 'bg-white text-gray-600 hover:bg-gray-50 border border-gray-100' }}">
                        Semua (All)
                    </a>
                </div>
            </div>
        @endif

        <!-- List Groups -->
        <div class="space-y-10">
            @forelse($groupedData as $group)
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 hover:shadow-md transition-all">
                    <!-- Class Header Banner -->
                    <div class="flex items-center justify-between mb-6 pb-4 border-b border-gray-100">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                            </div>
                            <div>
                                <h3 class="font-extrabold text-xl text-gray-800">{{ $group['class_name'] }}</h3>
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wider">{{ $group['course_name'] }}</p>
                            </div>
                        </div>
                    </div>

                    @if($user->hasRole(['guru', 'teacher', 'admin']))
                        <!-- Teacher View Inside Group -->
                        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
                            <!-- Section Assignments -->
                            <div>
                                <h4 class="font-black text-gray-700 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-pink-500"></span>
                                    Daftar Penugasan
                                </h4>
                                <div class="space-y-3">
                                    @forelse($group['assignments'] as $assignment)
                                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 hover:border-pink-200 transition-all flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                            <div class="flex-1 min-w-0">
                                                <h5 class="font-bold text-gray-800 text-base truncate">{{ $assignment['title'] }}</h5>
                                                <p class="text-xs text-red-500 font-bold mb-2">Tenggat: {{ $assignment['deadline'] ? \Carbon\Carbon::parse($assignment['deadline'])->translatedFormat('d M Y, H:i') : 'Tanpa Tenggat' }} WIB</p>
                                                
                                                <!-- Progress Stats -->
                                                <div class="flex items-center gap-4 text-xs font-semibold text-gray-500 mt-2">
                                                    <span>Pengumpulan: {{ $assignment['total_submissions'] }} / {{ $assignment['total_students'] }} Siswa</span>
                                                    @if($assignment['pending_grading'] > 0)
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-amber-50 text-amber-700 border border-amber-200 font-bold">
                                                            {{ $assignment['pending_grading'] }} Perlu Dinilai
                                                        </span>
                                                    @else
                                                        <span class="inline-flex items-center px-2 py-0.5 rounded-md bg-emerald-50 text-emerald-700 border border-emerald-200 font-bold">
                                                            Selesai Dinilai
                                                        </span>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="w-full sm:w-auto flex-shrink-0">
                                                <a href="{{ $assignment['url'] }}" class="inline-block w-full text-center px-4 py-2 bg-pink-50 text-pink-700 hover:bg-pink-600 hover:text-white rounded-xl font-bold transition-all text-xs shadow-sm">
                                                    Nilai & Kelola
                                                </a>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-gray-500 text-xs font-medium py-3 text-center bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">Belum ada penugasan.</p>
                                    @endforelse
                                </div>
                            </div>

                            <!-- Section Quizzes -->
                            <div>
                                <h4 class="font-black text-gray-700 text-sm uppercase tracking-wider mb-4 flex items-center gap-2">
                                    <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span>
                                    Kuis & Ujian
                                </h4>
                                <div class="space-y-3">
                                    @forelse($group['quizzes'] as $quiz)
                                        <div class="bg-gray-50 rounded-2xl p-4 border border-gray-100 hover:border-amber-200 transition-all flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
                                            <div class="flex-1 min-w-0">
                                                <h5 class="font-bold text-gray-800 text-base truncate">{{ $quiz['title'] }}</h5>
                                                <p class="text-xs text-gray-500 font-semibold mb-2">Tenggat: {{ $quiz['deadline'] ? \Carbon\Carbon::parse($quiz['deadline'])->translatedFormat('d M Y, H:i') : 'Tanpa Tenggat' }} WIB</p>
                                                
                                                <div class="flex items-center gap-4 text-xs font-semibold text-gray-500 mt-2">
                                                    <span>Mengerjakan: {{ $quiz['total_attempts'] }} / {{ $quiz['total_students'] }} Siswa</span>
                                                </div>
                                            </div>
                                            <div class="w-full sm:w-auto flex-shrink-0">
                                                <a href="{{ $quiz['url'] }}" class="inline-block w-full text-center px-4 py-2 bg-amber-50 text-amber-700 hover:bg-amber-500 hover:text-white rounded-xl font-bold transition-all text-xs shadow-sm">
                                                    Kelola Soal
                                                </a>
                                            </div>
                                        </div>
                                    @empty
                                        <p class="text-gray-500 text-xs font-medium py-3 text-center bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">Belum ada kuis.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    @else
                        <!-- Student View Inside Group -->
                        <div class="space-y-3">
                            @forelse($group['todos'] as $todo)
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-4 rounded-2xl border transition-all hover:shadow-sm
                                    {{ $todo['status'] === 'completed' ? 'bg-gray-50/70 border-gray-200/60 opacity-80' : 'bg-white border-gray-100 hover:border-indigo-100' }}">
                                    <div class="flex items-start sm:items-center gap-4 mb-4 sm:mb-0 w-full sm:w-auto min-w-0">
                                        <!-- Status indicator icon -->
                                        <div class="flex-shrink-0 mt-1 sm:mt-0">
                                            @if($todo['status'] === 'completed')
                                                <div class="h-7 w-7 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M5 13l4 4L19 7"></path></svg>
                                                </div>
                                            @elseif($todo['status'] === 'overdue')
                                                <div class="h-7 w-7 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                                                    <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                                                </div>
                                            @else
                                                <div class="h-7 w-7 rounded-full border-2 border-gray-300 flex items-center justify-center">
                                                    <span class="w-2 h-2 rounded-full bg-gray-400"></span>
                                                </div>
                                            @endif
                                        </div>

                                        <div class="min-w-0">
                                            <h4 class="font-extrabold text-gray-800 text-base truncate {{ $todo['status'] === 'completed' ? 'line-through text-gray-400' : '' }}">
                                                {{ $todo['title'] }}
                                            </h4>
                                            <div class="flex flex-wrap items-center gap-x-3 gap-y-1 text-xs mt-1">
                                                <span class="inline-flex items-center gap-1 font-extrabold text-{{ $todo['color'] }}-600 bg-{{ $todo['color'] }}-50 px-2 py-0.5 rounded-md text-[10px] uppercase tracking-wide">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="{{ $todo['icon'] }}"></path></svg>
                                                    {{ $todo['type'] }}
                                                </span>
                                                
                                                @if($todo['deadline'])
                                                    <span class="text-gray-400">&bull;</span>
                                                    <span class="font-bold flex items-center gap-1 {{ $todo['status'] === 'overdue' ? 'text-red-500' : 'text-gray-500' }}">
                                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                        Tenggat: {{ \Carbon\Carbon::parse($todo['deadline'])->translatedFormat('d M Y, H:i') }} WIB
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>

                                    <div class="w-full sm:w-auto flex-shrink-0 text-right">
                                        <a href="{{ $todo['url'] }}" class="inline-block w-full sm:w-auto px-5 py-2 text-xs font-bold rounded-xl shadow-sm transition-all
                                            {{ $todo['status'] === 'completed' ? 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' : 'bg-indigo-600 text-white hover:bg-indigo-700 active:scale-95' }}">
                                            {{ $todo['status'] === 'completed' ? 'Lihat Detail' : 'Kerjakan Sekarang' }}
                                        </a>
                                    </div>
                                </div>
                            @empty
                                <p class="text-gray-500 text-xs font-medium py-3 text-center bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                                    Tidak ada tugas dalam kategori ini.
                                </p>
                            @endforelse
                        </div>
                    @endif
                </div>
            @empty
                <div class="text-center py-16 bg-white rounded-3xl border border-gray-100 shadow-sm">
                    <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-emerald-50 text-emerald-500 mb-4 animate-bounce">
                        <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h4 class="text-xl font-extrabold text-gray-800 mb-1">Semua Bersih!</h4>
                    <p class="text-gray-500 font-medium max-w-sm mx-auto text-sm">Tidak ada tugas atau kuis yang ditemukan dalam kategori ini.</p>
                </div>
            @endforelse
        </div>
    </div>
</x-app-layout>
