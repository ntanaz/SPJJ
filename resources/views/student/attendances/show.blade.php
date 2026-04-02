<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 tracking-tight flex items-center gap-3">
            <svg class="w-8 h-8 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
            Presensi & Kehadiran Kelas
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8">
            <!-- Header Banner -->
            <div class="bg-gradient-to-br from-indigo-500 to-purple-600 rounded-t-3xl p-8 text-white shadow-xl relative overflow-hidden">
                <div class="absolute -right-10 -top-10 w-48 h-48 rounded-full bg-white opacity-10 blur-xl"></div>
                <div class="relative z-10">
                    <span class="px-3 py-1 bg-white/20 rounded-full text-xs font-black uppercase tracking-widest backdrop-blur-sm mb-3 inline-block">Mata Pelajaran: {{ $attendance->course->name }}</span>
                    <h3 class="text-3xl font-black mb-2 leading-tight text-transparent bg-clip-text bg-gradient-to-r from-white to-indigo-100">{{ $attendance->title }}</h3>
                    <p class="text-indigo-100 font-medium">Batas Pengisian Pukul: <strong class="text-white">{{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }} WIB - {{ \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }} WIB</strong></p>
                </div>
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-b-3xl border border-t-0 border-gray-100">
                <div class="p-8 md:p-10">
                    
                    @if(session('success'))
                        <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-2xl flex items-center shadow-sm">
                            <svg class="w-6 h-6 mr-3 text-emerald-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="font-bold">{{ session('success') }}</span>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-2xl flex items-center shadow-sm">
                            <svg class="w-6 h-6 mr-3 text-red-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <span class="font-bold">{{ session('error') }}</span>
                        </div>
                    @endif

                    @if($record)
                        <!-- Jika Siswa Sudah Mengisi -->
                        <div class="text-center py-6">
                            <div class="inline-flex items-center justify-center w-24 h-24 bg-emerald-100 rounded-full mb-6 relative">
                                <div class="absolute inset-0 bg-emerald-400 rounded-full blur animate-pulse opacity-50"></div>
                                <svg class="w-12 h-12 text-emerald-600 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                            </div>
                            <h4 class="text-2xl font-black text-gray-800 mb-2">Terima Kasih! 🎉</h4>
                            <p class="text-gray-600 font-medium mb-6">Anda telah melakukan presensi pada tanggal {{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('d F Y') }}</p>
                            
                            <div class="bg-gray-50 border border-gray-100 rounded-2xl p-6 inline-block min-w-[250px] shadow-inner mb-6">
                                <span class="block text-xs uppercase font-bold text-gray-400 tracking-wider mb-2">Status Tercatat</span>
                                @php
                                    $statusColors = [
                                        'hadir' => 'bg-emerald-100 text-emerald-700',
                                        'izin' => 'bg-blue-100 text-blue-700',
                                        'sakit' => 'bg-amber-100 text-amber-700',
                                    ];
                                    $colorClass = $statusColors[$record->status] ?? 'bg-gray-200 text-gray-800';
                                @endphp
                                <span class="px-5 py-2 rounded-xl text-lg font-black uppercase tracking-widest {{ $colorClass }}">
                                    {{ $record->status }}
                                </span>
                            </div>
                        </div>
                    @else
                        <!-- Form Pengisian jika Belum Diisi -->
                        @if($attendance->isCurrentlyOpen())
                            <form method="POST" action="{{ route('student.attendances.submit', $attendance) }}" class="space-y-6">
                                @csrf
                                <div class="text-center mb-8">
                                    <h4 class="text-xl font-bold text-gray-800 mb-2">Konfirmasi Kehadiran</h4>
                                    <p class="text-gray-500 text-sm font-medium">Pilih salah satu status kehadiran di bawah ini untuk dicatat ke sistem akademik.</p>
                                </div>

                                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                                    <!-- Hadir -->
                                    <label class="relative flex flex-col items-center bg-white border-2 border-gray-200 rounded-2xl p-6 cursor-pointer hover:bg-gray-50 hover:border-emerald-300 transition-all shadow-sm focus-within:ring-4 focus-within:ring-emerald-100 focus-within:border-emerald-500 group">
                                        <input type="radio" name="status" value="hadir" class="sr-only peer" required>
                                        <div class="h-16 w-16 bg-gray-100 peer-checked:bg-emerald-100 rounded-full flex items-center justify-center mb-4 transition-colors">
                                            <svg class="w-8 h-8 text-gray-400 peer-checked:text-emerald-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 10h4.764a2 2 0 011.789 2.894l-3.5 7A2 2 0 0115.263 21h-4.017c-.163 0-.326-.02-.485-.06L7 20m7-10V5a2 2 0 00-2-2h-.095c-.5 0-.905.405-.905.905 0 .714-.211 1.412-.608 2.006L7 11v9m7-10h-2M7 20H5a2 2 0 01-2-2v-6a2 2 0 012-2h2.5" /></svg>
                                        </div>
                                        <span class="text-lg font-black text-gray-700 peer-checked:text-emerald-600 transition-colors">HADIR</span>
                                        <!-- Ring Outline effect when checked -->
                                        <div class="absolute inset-0 rounded-2xl border-2 border-emerald-500 opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                                    </label>

                                    <!-- Izin -->
                                    <label class="relative flex flex-col items-center bg-white border-2 border-gray-200 rounded-2xl p-6 cursor-pointer hover:bg-gray-50 hover:border-blue-300 transition-all shadow-sm focus-within:ring-4 focus-within:ring-blue-100 focus-within:border-blue-500 group">
                                        <input type="radio" name="status" value="izin" class="sr-only peer">
                                        <div class="h-16 w-16 bg-gray-100 peer-checked:bg-blue-100 rounded-full flex items-center justify-center mb-4 transition-colors">
                                            <svg class="w-8 h-8 text-gray-400 peer-checked:text-blue-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" /></svg>
                                        </div>
                                        <span class="text-lg font-black text-gray-700 peer-checked:text-blue-600 transition-colors">IZIN</span>
                                        <div class="absolute inset-0 rounded-2xl border-2 border-blue-500 opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                                    </label>

                                    <!-- Sakit -->
                                    <label class="relative flex flex-col items-center bg-white border-2 border-gray-200 rounded-2xl p-6 cursor-pointer hover:bg-gray-50 hover:border-amber-300 transition-all shadow-sm focus-within:ring-4 focus-within:ring-amber-100 focus-within:border-amber-500 group">
                                        <input type="radio" name="status" value="sakit" class="sr-only peer">
                                        <div class="h-16 w-16 bg-gray-100 peer-checked:bg-amber-100 rounded-full flex items-center justify-center mb-4 transition-colors">
                                            <svg class="w-8 h-8 text-gray-400 peer-checked:text-amber-500 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" /></svg>
                                        </div>
                                        <span class="text-lg font-black text-gray-700 peer-checked:text-amber-600 transition-colors">SAKIT</span>
                                        <div class="absolute inset-0 rounded-2xl border-2 border-amber-500 opacity-0 peer-checked:opacity-100 pointer-events-none transition-opacity"></div>
                                    </label>
                                </div>
                                <div class="text-center mt-2">
                                    @error('status')<p class="text-sm text-red-600 font-bold bg-red-50 py-2 rounded-lg">{{ $message }}</p>@enderror
                                </div>

                                <div class="pt-6 flex justify-center">
                                    <button type="submit" class="px-12 py-4 bg-indigo-600 hover:bg-indigo-700 text-white font-black text-lg rounded-2xl shadow-lg transition-transform active:scale-95 flex items-center gap-2 border border-indigo-700">
                                        Kirim Presensi Sekarang
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                    </button>
                                </div>
                            </form>
                        @else
                            <!-- Jika belum waktunya atau sudah lewat -->
                            <div class="text-center py-6">
                                <div class="inline-flex items-center justify-center w-24 h-24 bg-red-50 rounded-full mb-6 relative border-4 border-red-100">
                                    <svg class="w-12 h-12 text-red-400 relative z-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <h4 class="text-2xl font-black text-gray-800 mb-2">Sesi Presensi Ditutup</h4>
                                <p class="text-gray-500 font-medium max-w-sm mx-auto">Mohon maaf, sesi presensi untuk pertemuan ini belum dibuka atau batas waktunya telah kedaluwarsa.</p>
                                <a href="{{ route('student.courses.show', $attendance->course) }}" class="inline-block mt-8 px-8 py-3 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors">Kembali ke Kelas</a>
                            </div>
                        @endif
                    @endif
                </div>
            </div>
            
            <div class="mt-6 text-center">
                <a href="{{ route('student.courses.show', $attendance->course) }}" class="text-sm font-bold text-gray-500 hover:text-indigo-600 transition-colors inline-flex items-center gap-1">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Kembali ke Menu Kelas
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
