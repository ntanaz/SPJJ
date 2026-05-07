<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
            {{ __('Laporan Aktivitas & Analitik') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
            <!-- Assignment Completion -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="h-14 w-14 rounded-full bg-indigo-100 text-indigo-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Tugas Terkumpul</p>
                    <p class="text-3xl font-black text-gray-800">{{ $analytics['assignment_completion'] }}</p>
                </div>
            </div>

            <!-- Late Submissions -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="h-14 w-14 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Tugas Terlambat</p>
                    <p class="text-3xl font-black text-gray-800">{{ $analytics['late_submissions'] }}</p>
                </div>
            </div>

            <!-- Quiz Attempts -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="h-14 w-14 rounded-full bg-amber-100 text-amber-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Sesi Kuis</p>
                    <p class="text-3xl font-black text-gray-800">{{ $analytics['quiz_attempts'] }}</p>
                </div>
            </div>

            <!-- Avg Quiz Score -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="h-14 w-14 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Rata-Rata Nilai Kuis</p>
                    <p class="text-3xl font-black text-gray-800">{{ number_format($analytics['avg_quiz_score'], 1) }}</p>
                </div>
            </div>

            <!-- Attendance Rate -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="h-14 w-14 rounded-full bg-blue-100 text-blue-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Kehadiran (Hadir)</p>
                    <p class="text-3xl font-black text-gray-800">{{ $analytics['attendance_rate'] }}</p>
                </div>
            </div>

            <!-- Material Views -->
            <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100 flex items-center gap-4">
                <div class="h-14 w-14 rounded-full bg-purple-100 text-purple-600 flex items-center justify-center">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                </div>
                <div>
                    <p class="text-sm font-bold text-gray-500 uppercase tracking-wider">Materi Dibaca</p>
                    <p class="text-3xl font-black text-gray-800">{{ $analytics['material_views'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Statistik Performa Keseluruhan</h3>
            <p class="text-gray-500">Gunakan data di atas untuk memantau kemajuan belajar siswa. Analisis ini membantu mengidentifikasi siswa yang terlambat mengumpulkan tugas atau membutuhkan bantuan tambahan di topik kuis tertentu.</p>
        </div>
    </div>
</x-app-layout>
