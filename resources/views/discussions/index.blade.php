<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
            {{ __('Forum Diskusi Kelas & Materi') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto px-4">
        <!-- Notifikasi -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center shadow-sm">
                <svg class="w-6 h-6 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        <div class="mb-6">
            <h3 class="text-xl font-bold text-gray-800">Ruang Diskusi Anda</h3>
            <p class="text-gray-500 text-sm mt-1">Ikuti diskusi kelas bersama Guru dan rekan belajar lainnya.</p>
        </div>

        @forelse($courses as $course)
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 mb-8">
                <!-- Course Header -->
                <div class="flex items-center gap-3 border-b border-gray-100 pb-4 mb-6">
                    <div class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    </div>
                    <div>
                        <h4 class="font-bold text-gray-800 text-lg">{{ $course->name }}</h4>
                        <p class="text-xs text-gray-400">Total Sesi Diskusi: {{ $course->discussions->count() }}</p>
                    </div>
                </div>

                <!-- Discussions List under Course -->
                <div class="space-y-4">
                    @forelse($course->discussions as $discussion)
                        @php
                            $latestReply = $discussion->replies->sortByDesc('created_at')->first();
                            $lastActivity = $latestReply ? $latestReply->created_at : $discussion->created_at;
                        @endphp
                        <div class="bg-gray-50 hover:bg-white rounded-2xl p-5 border border-gray-100 hover:shadow-md hover:border-indigo-100 transition-all group flex flex-col sm:flex-row justify-between sm:items-center gap-4 relative">
                            <div class="flex items-start gap-4">
                                <div class="h-12 w-12 rounded-xl {{ $discussion->user->hasRole(['guru', 'teacher', 'admin']) ? 'bg-indigo-150 text-indigo-600' : 'bg-gray-200 text-gray-600' }} flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" /></svg>
                                </div>
                                <div>
                                    <div class="flex items-center gap-2 flex-wrap">
                                        <h5 class="font-bold text-gray-800 text-lg group-hover:text-indigo-600 transition-colors">{{ $discussion->title }}</h5>
                                        @if($discussion->is_pinned)
                                            <span class="px-2 py-0.5 bg-yellow-100 text-yellow-800 text-[10px] font-black rounded uppercase">Pinned</span>
                                        @endif
                                        @if($discussion->is_locked)
                                            <span class="px-2 py-0.5 bg-red-100 text-red-800 text-[10px] font-black rounded uppercase">Locked</span>
                                        @endif
                                        @if($discussion->material)
                                            <span class="px-2 py-0.5 bg-indigo-50 text-indigo-700 text-[10px] font-black rounded uppercase">Materi: {{ $discussion->material->title }}</span>
                                        @endif
                                    </div>
                                    <p class="text-sm text-gray-500 mb-2 mt-1">
                                        Pembuat: <span class="font-semibold">{{ $discussion->user->name }}</span> 
                                        @if($discussion->user->hasRole(['guru', 'teacher', 'admin']))
                                            <span class="text-xs text-indigo-600 bg-indigo-50 px-1.5 py-0.5 rounded font-bold">Guru</span>
                                        @endif
                                    </p>
                                    <div class="flex items-center gap-4 text-xs text-gray-400 font-medium">
                                        <span class="flex items-center gap-1">
                                            <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z" /></svg>
                                            {{ $discussion->replies_count ?? $discussion->replies->count() }} Balasan
                                        </span>
                                        <span>&bull;</span>
                                        <span>Aktivitas Terakhir: {{ $lastActivity->diffForHumans() }}</span>
                                    </div>
                                </div>
                            </div>
                            <div class="flex items-center gap-3 self-end sm:self-center">
                                <a href="{{ route('discussions.show', $discussion) }}" class="px-5 py-2 bg-white text-indigo-600 border border-indigo-200 hover:bg-indigo-50 hover:border-indigo-300 rounded-xl font-bold transition-colors text-sm shadow-sm whitespace-nowrap">
                                    Buka Diskusi
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
                        <div class="text-center py-6 bg-gray-50/50 rounded-2xl border border-gray-100 border-dashed">
                            <p class="text-gray-500 font-medium">Belum ada topik diskusi untuk mata pelajaran ini.</p>
                            @role('guru|teacher|admin')
                                <a href="{{ route('student.courses.show', $course) }}" class="inline-block mt-3 text-sm font-bold text-indigo-600 hover:text-indigo-700">
                                    + Buka Diskusi di Halaman Kelas
                                </a>
                            @endrole
                        </div>
                    @endforelse
                </div>
            </div>
        @empty
            <div class="text-center py-12 bg-white rounded-3xl border border-gray-100 border-dashed shadow-sm">
                <svg class="w-16 h-16 text-indigo-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                <h3 class="text-lg font-bold text-gray-800">Tidak ada Kelas Tersedia</h3>
                <p class="text-gray-500 mt-1">Anda belum tergabung dalam kelas pembelajaran apa pun.</p>
            </div>
        @endforelse
    </div>
</x-app-layout>
