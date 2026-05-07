<x-app-layout>
    <x-slot name="header">
        {{ __('Teacher Dashboard') }}
    </x-slot>

    <div class="space-y-6">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-2xl p-6 text-white shadow-lg transform transition hover:scale-105">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-indigo-100 text-sm font-semibold mb-1 uppercase tracking-wider">Total Siswa</p>
                        <h3 class="text-4xl font-bold">{{ $stats['total_students'] }}</h3>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                    </div>
                </div>
            </div>
            
            <div class="bg-gradient-to-br from-emerald-400 to-teal-500 rounded-2xl p-6 text-white shadow-lg transform transition hover:scale-105">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-teal-100 text-sm font-semibold mb-1 uppercase tracking-wider">Kelas Aktif</p>
                        <h3 class="text-4xl font-bold">{{ $stats['total_classes'] }}</h3>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                    </div>
                </div>
            </div>

            <div class="bg-gradient-to-br from-red-500 to-pink-500 rounded-2xl p-6 text-white shadow-lg transform transition hover:scale-105">
                <div class="flex justify-between items-center">
                    <div>
                        <p class="text-pink-100 text-sm font-semibold mb-1 uppercase tracking-wider">Tugas Menunggu Nilai</p>
                        <h3 class="text-4xl font-bold">{{ $stats['pending_assignments'] }}</h3>
                    </div>
                    <div class="p-3 bg-white/20 rounded-xl backdrop-blur-sm">
                        <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" /></svg>
                    </div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Recent Submissions -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-800">Recent Submissions (Tugas Siswa)</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentSubmissions as $submission)
                    <div class="p-4 flex items-center justify-between hover:bg-gray-50 transition-colors">
                        <div class="flex items-center space-x-3">
                            <img src="{{ $submission->user->avatar ? asset('storage/' . $submission->user->avatar) : 'https://ui-avatars.com/api/?name='.urlencode($submission->user->name) }}" class="w-10 h-10 rounded-full" alt="avatar">
                            <div>
                                <h4 class="text-sm font-bold text-gray-800">{{ $submission->user->name }}</h4>
                                <p class="text-xs text-gray-500">{{ $submission->assignment->title }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span class="text-xs text-gray-400 block">{{ $submission->created_at->diffForHumans() }}</span>
                            @if(is_null($submission->grade))
                                <span class="text-[10px] uppercase font-bold text-red-600 bg-red-50 px-2 py-0.5 rounded">Belum Dinilai</span>
                            @else
                                <span class="text-[10px] uppercase font-bold text-green-600 bg-green-50 px-2 py-0.5 rounded">Nilai: {{ $submission->grade }}</span>
                            @endif
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-gray-500 text-sm">Belum ada tugas yang dikumpulkan.</div>
                    @endforelse
                </div>
            </div>

            <!-- Recent Discussions -->
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
                <div class="p-4 border-b border-gray-100 flex justify-between items-center bg-gray-50">
                    <h3 class="font-bold text-gray-800">Diskusi Terbaru</h3>
                </div>
                <div class="divide-y divide-gray-100">
                    @forelse($recentDiscussions as $discussion)
                    <div class="p-4 hover:bg-gray-50 transition-colors">
                        <div class="flex items-start space-x-3">
                            <div class="w-8 h-8 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center font-bold text-xs">
                                {{ substr($discussion->user->name, 0, 1) }}
                            </div>
                            <div class="flex-1">
                                <h4 class="text-sm font-bold text-gray-800">{{ $discussion->title }}</h4>
                                <p class="text-xs text-gray-600 line-clamp-1">{{ $discussion->content }}</p>
                                <div class="text-xs text-gray-400 mt-1 flex justify-between">
                                    <span>{{ $discussion->user->name }} • {{ $discussion->course->name }}</span>
                                    <span>{{ $discussion->created_at->diffForHumans() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="p-6 text-center text-gray-500 text-sm">Belum ada diskusi terbaru.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
