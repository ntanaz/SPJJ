<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.courses.show', $discussion->course) }}" class="p-2 bg-white rounded-full text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
                {{ __('Forum Topik Utama') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto">
        <!-- Notifikasi -->
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center shadow-sm">
                <svg class="w-6 h-6 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span class="font-bold">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl flex items-center shadow-sm">
                <svg class="w-6 h-6 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="font-bold">{{ session('error') }}</span>
            </div>
        @endif

        <!-- Card Topik Diskusi -->
        <div class="bg-indigo-600 rounded-3xl p-8 text-white shadow-xl relative overflow-hidden mb-8 {{ $discussion->is_pinned ? 'border-4 border-yellow-400' : '' }}">
            @if($discussion->is_pinned)
                <div class="absolute top-0 right-0 bg-yellow-400 text-yellow-900 font-bold px-4 py-1 rounded-bl-xl shadow text-sm flex items-center z-20">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    Disematkan
                </div>
            @endif
            @if($discussion->is_locked)
                <div class="absolute top-0 right-32 bg-red-500 text-white font-bold px-4 py-1 rounded-b-xl shadow text-sm flex items-center z-20">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                    Dikunci
                </div>
            @endif

            <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-white opacity-10"></div>
            <div class="absolute -bottom-10 right-20 w-32 h-32 rounded-full bg-white opacity-5"></div>
            
            <div class="relative z-10 flex flex-col md:flex-row gap-6">
                <!-- Avatar Guru -->
                <div class="flex-shrink-0">
                    <div class="h-16 w-16 bg-white text-indigo-600 rounded-2xl flex items-center justify-center font-black text-2xl shadow-md border-4 border-indigo-500">
                        {{ substr($discussion->user->name, 0, 1) }}
                    </div>
                </div>
                
                <div class="flex-1">
                    <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-4 gap-2">
                        <div>
                            <span class="px-3 py-1 bg-indigo-500 rounded-full text-xs font-bold uppercase tracking-widest text-indigo-100 flex items-center inline-flex gap-1 mb-2 shadow-inner">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Dibuat oleh {{ $discussion->user->name }}
                            </span>
                            <h3 class="text-3xl font-black mb-1 text-white leading-tight">{{ $discussion->title }}</h3>
                            <p class="text-indigo-200 text-sm font-medium">{{ $discussion->course->name }} &bull; {{ $discussion->created_at->diffForHumans() }}</p>
                        </div>
                        
                        @role('guru|teacher|admin')
                        <div class="flex items-center gap-2 mt-2 sm:mt-0">
                            <form action="{{ route('discussions.pin', $discussion) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1 bg-white/20 hover:bg-white/40 rounded-lg text-sm font-bold transition-colors">
                                    {{ $discussion->is_pinned ? 'Unpin' : 'Pin Topik' }}
                                </button>
                            </form>
                            <form action="{{ route('discussions.lock', $discussion) }}" method="POST">
                                @csrf
                                <button type="submit" class="px-3 py-1 bg-red-500/80 hover:bg-red-500 rounded-lg text-sm font-bold transition-colors">
                                    {{ $discussion->is_locked ? 'Buka Kunci' : 'Kunci Topik' }}
                                </button>
                            </form>
                        </div>
                        @endrole
                    </div>
                    
                    <div class="prose prose-indigo prose-invert max-w-none font-medium leading-relaxed bg-black/10 p-5 rounded-2xl shadow-inner border border-white/5 mt-4">
                        {!! nl2br(e($discussion->content)) !!}
                    </div>
                </div>
            </div>
        </div>

        <!-- Section Balasan -->
        <div class="mb-4 flex items-center justify-between">
            <h4 class="text-2xl font-black text-gray-800 flex items-center gap-2">
                <span class="text-indigo-600">{{ $discussion->replies->count() }}</span> Balasan Peserta
            </h4>
        </div>

        <div class="space-y-6 mb-8">
            @forelse($discussion->replies as $reply)
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 {{ $reply->user->id === auth()->id() ? 'border-l-4 border-l-indigo-500' : '' }} flex flex-col sm:flex-row gap-6">
                    <div class="flex-shrink-0 flex sm:flex-col items-center gap-3">
                        <div class="h-12 w-12 rounded-xl {{ $reply->user->hasRole(['guru', 'teacher', 'admin']) ? 'bg-amber-100 text-amber-600' : 'bg-gray-100 text-gray-600' }} flex items-center justify-center font-bold text-lg">
                            {{ substr($reply->user->name, 0, 1) }}
                        </div>
                        
                        @if($reply->grade !== null)
                            <div class="text-center mt-2" title="Dinilai Guru">
                                <span class="block text-[10px] text-emerald-600 font-bold uppercase tracking-wider mb-0.5">Nilai</span>
                                <span class="px-3 py-1 bg-emerald-50 text-emerald-700 font-black rounded-lg border border-emerald-200 shadow-sm">{{ $reply->grade }}</span>
                            </div>
                        @endif
                    </div>
                    
                    <div class="flex-1 w-full space-y-4">
                        <div class="flex justify-between items-start">
                            <div>
                                <h5 class="font-bold text-gray-800 text-lg">{{ $reply->user->name }}</h5>
                                <p class="text-xs text-gray-500 font-medium">{{ $reply->created_at->translatedFormat('d F Y, H:i') }} ({{ $reply->created_at->diffForHumans() }})</p>
                            </div>
                            
                            @if(auth()->id() === $reply->user_id || auth()->user()->hasRole(['guru', 'teacher', 'admin']))
                                <form action="{{ route('discussion_replies.destroy', $reply) }}" method="POST" onsubmit="return confirm('Hapus balasan diskusi ini?');" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2 text-gray-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-colors">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            @endif
                        </div>
                        
                        <div class="text-gray-700 leading-relaxed font-medium whitespace-pre-line">{{ $reply->content }}</div>
                        
                        <!-- Area Penilaian (Khusus Guru/Admin & Bukan Milik Guru tsb) -->
                        @role('guru|teacher|admin')
                            @if(!$reply->user->hasRole(['guru', 'teacher', 'admin']))
                            <div class="pt-4 mt-2 border-t border-gray-100">
                                <form action="{{ route('discussion_replies.grade', $reply) }}" method="POST" class="flex flex-col sm:flex-row items-center gap-3">
                                    @csrf
                                    <label class="text-sm font-bold text-gray-700 hidden sm:block">Beri Nilai Siswa:</label>
                                    <div class="flex items-center gap-2 w-full sm:w-auto">
                                        <input type="number" name="grade" min="0" max="100" class="w-24 rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 font-bold" value="{{ $reply->grade }}" placeholder="0-100" required>
                                        <button type="submit" class="px-4 py-2 bg-emerald-50 text-emerald-600 hover:bg-emerald-600 hover:text-white font-bold rounded-xl border border-emerald-200 hover:border-emerald-600 transition-colors shadow-sm">Simpan Nilai</button>
                                    </div>
                                    @error('grade')<span class="text-xs text-red-500 font-bold ml-2">{{ $message }}</span>@enderror
                                </form>
                            </div>
                            @endif
                        @endrole
                    </div>
                </div>
            @empty
                <div class="text-center py-10 bg-white rounded-3xl border border-gray-100 shadow-sm border-dashed">
                    <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-gray-50 text-gray-400 mb-3 border-2 border-dashed border-gray-200">
                        <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/></svg>
                    </div>
                    <p class="text-gray-500 font-medium text-lg">Belum ada balasan diskusi.</p>
                    <p class="text-gray-400 text-sm mt-1">Ayo jadi yang pertama bergabung dalam percakapan ini!</p>
                </div>
            @endforelse
        </div>

        <!-- Form Tulis Balasan -->
        @if(!$discussion->is_locked)
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
            <h4 class="font-bold text-gray-800 mb-4 flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h10a8 8 0 018 8v2M3 10l6 6m-6-6l6-6"/></svg>
                Tulis Balasan / Tanggapan Anda
            </h4>
            
            <form action="{{ route('discussion_replies.store', $discussion) }}" method="POST">
                @csrf
                <textarea name="content" rows="4" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors p-4 resize-y text-gray-700 font-medium" placeholder="Tulis analisis, jawaban, atau pendapat Anda mengenai diskusi ini..." required></textarea>
                @error('content')<p class="mt-2 text-sm text-red-600 font-bold">{{ $message }}</p>@enderror
                
                <div class="mt-4 flex justify-end">
                    <button type="submit" class="px-8 py-3 bg-indigo-600 text-white font-black rounded-xl hover:bg-indigo-700 focus:outline-none focus:ring-4 focus:ring-indigo-200 transition-all shadow-md active:scale-95 flex items-center gap-2">
                        Kirim Balasan
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"/></svg>
                    </button>
                </div>
            </form>
        </div>
        @else
        <div class="bg-red-50 rounded-3xl p-6 text-center shadow-sm border border-red-100">
            <div class="inline-flex h-12 w-12 items-center justify-center rounded-full bg-red-100 text-red-500 mb-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
            </div>
            <h4 class="font-bold text-red-800 text-lg">Topik Ini Telah Dikunci</h4>
            <p class="text-red-600 mt-1">Anda tidak dapat lagi mengirimkan balasan pada diskusi ini.</p>
        </div>
        @endif
    </div>
</x-app-layout>
