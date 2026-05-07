<x-app-layout>
    <x-slot name="header">
        {{ __('Dashboard Admin') }}
    </x-slot>

    <div class="space-y-6">
        <!-- Stats Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6">
            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center space-x-4">
                <div class="p-3 bg-blue-50 text-blue-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500 font-medium">Total Users</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['users'] }}</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center space-x-4">
                <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500 font-medium">Total Courses</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['courses'] }}</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center space-x-4">
                <div class="p-3 bg-purple-50 text-purple-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500 font-medium">Assignments</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['assignments'] }}</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center space-x-4">
                <div class="p-3 bg-green-50 text-green-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500 font-medium">Submissions</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['submissions'] }}</div>
                </div>
            </div>

            <div class="bg-white rounded-2xl p-6 border border-gray-100 shadow-sm flex items-center space-x-4">
                <div class="p-3 bg-yellow-50 text-yellow-600 rounded-xl">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                </div>
                <div>
                    <div class="text-sm text-gray-500 font-medium">Quizzes</div>
                    <div class="text-2xl font-bold text-gray-900">{{ $stats['quizzes'] }}</div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Pengguna Baru</h3>
                <div class="space-y-4">
                    @forelse($recentUsers as $user)
                    <div class="flex items-center justify-between">
                        <div class="flex items-center space-x-3">
                            <img class="w-10 h-10 rounded-full" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" alt="{{ $user->name }}">
                            <div>
                                <div class="font-medium text-gray-900">{{ $user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $user->email }}</div>
                            </div>
                        </div>
                        <span class="px-3 py-1 bg-gray-100 text-gray-600 rounded-full text-xs font-medium">{{ $user->roles->pluck('name')->first() ?? 'Siswa' }}</span>
                    </div>
                    @empty
                    <div class="text-gray-500 text-sm">Belum ada pengguna baru.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Kursus Terbaru</h3>
                <div class="space-y-4">
                    @forelse($recentCourses as $course)
                    <div class="flex items-center space-x-3">
                        <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                        </div>
                        <div>
                            <div class="font-medium text-gray-900">{{ $course->name }}</div>
                            <div class="text-sm text-gray-500">{{ $course->created_at->diffForHumans() }}</div>
                        </div>
                    </div>
                    @empty
                    <div class="text-gray-500 text-sm">Belum ada kursus baru.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
