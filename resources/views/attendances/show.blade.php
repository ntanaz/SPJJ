<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.courses.show', $attendance->course) }}" class="p-2 bg-white rounded-full text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
                Laporan Kehadiran: {{ $attendance->title }}
            </h2>
        </div>
    </x-slot>

    <div class="py-8 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <!-- Info Card -->
        <div class="bg-indigo-600 rounded-3xl p-8 mb-8 text-white shadow-xl relative overflow-hidden">
            <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
            <div class="relative z-10 flex flex-col md:flex-row justify-between items-start md:items-center">
                <div>
                    <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider mb-2">
                        📅 {{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('l, d F Y') }}
                    </span>
                    <h3 class="text-3xl font-extrabold mb-1">{{ $attendance->course->name }}</h3>
                    <p class="text-indigo-200 text-sm font-bold flex items-center gap-2">
                        <svg class="w-5 h-5 text-indigo-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Sesi Berlangsung: {{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }} WIB
                    </p>
                </div>
                
                <div class="mt-6 md:mt-0 bg-white/10 rounded-2xl p-4 backdrop-blur-sm border border-white/20">
                    <p class="text-xs uppercase font-bold tracking-widest text-indigo-200 mb-1">Total Partisipasi</p>
                    <p class="text-4xl font-black text-white">{{ $attendance->records->count() }} <span class="text-lg text-indigo-200 font-medium">Siswa</span></p>
                </div>
            </div>
        </div>

        <!-- Student List -->
        <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-8">
            <h3 class="text-xl font-bold text-gray-800 mb-6 flex items-center gap-2 border-b border-gray-100 pb-4">
                <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>
                Rekapitulasi Presensi
            </h3>
            
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50/50">
                            <th class="py-4 px-6 border-b border-gray-100 font-bold text-gray-500 uppercase text-xs tracking-wider rounded-tl-xl text-center w-16">No</th>
                            <th class="py-4 px-6 border-b border-gray-100 font-bold text-gray-500 uppercase text-xs tracking-wider">Nama Siswa</th>
                            <th class="py-4 px-6 border-b border-gray-100 font-bold text-gray-500 uppercase text-xs tracking-wider w-40 text-center">Status Kehadiran</th>
                            <th class="py-4 px-6 border-b border-gray-100 font-bold text-gray-500 uppercase text-xs tracking-wider rounded-tr-xl">Waktu Mengisi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($attendance->records as $index => $record)
                        <tr class="hover:bg-gray-50 transition-colors group">
                            <td class="py-4 px-6 border-b border-gray-100 text-sm font-semibold text-gray-500 text-center">{{ $index + 1 }}</td>
                            <td class="py-4 px-6 border-b border-gray-100 font-bold text-gray-800">
                                <div class="flex items-center gap-3">
                                    <div class="h-10 w-10 bg-indigo-50 text-indigo-600 rounded-full flex items-center justify-center border border-indigo-100 font-bold shadow-sm">
                                        {{ substr($record->user->name, 0, 1) }}
                                    </div>
                                    {{ $record->user->name }}
                                </div>
                            </td>
                            <td class="py-4 px-6 border-b border-gray-100 text-center">
                                @php
                                    $statusColors = [
                                        'hadir' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                        'izin' => 'bg-blue-100 text-blue-700 border-blue-200',
                                        'sakit' => 'bg-amber-100 text-amber-700 border-amber-200',
                                        'alpa' => 'bg-red-100 text-red-700 border-red-200',
                                    ];
                                    $colorClass = $statusColors[$record->status] ?? 'bg-gray-100 text-gray-700 border-gray-200';
                                @endphp
                                <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider border {{ $colorClass }}">
                                    {{ $record->status }}
                                </span>
                            </td>
                            <td class="py-4 px-6 border-b border-gray-100 text-sm text-gray-600 font-medium">
                                {{ $record->created_at->format('d/m/Y, H:i') }} WIB
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="py-8 text-center bg-gray-50 rounded-xl border-dashed border-2 border-gray-200">
                                <p class="text-gray-500 font-bold">Belum ada siswa yang mengisi absensi untuk sesi ini.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</x-app-layout>
