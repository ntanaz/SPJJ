<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-bold text-xl text-gray-800 leading-tight">
                    {{ __('Daftar Pengumpulan Tugas') }}
                </h2>
                <p class="text-sm text-gray-500 mt-1">Tugas: {{ $assignment->title }} (Max Score: {{ $assignment->max_score }})</p>
            </div>
            <a href="{{ route('assignments.index') }}" class="px-4 py-2 bg-gray-100 text-gray-700 hover:bg-gray-200 font-bold rounded-lg transition-colors">
                Kembali ke Tugas
            </a>
        </div>
    </x-slot>

    <div class="space-y-6 max-w-7xl mx-auto">
        @if(session('success'))
            <div class="bg-green-50 text-green-700 p-4 rounded-xl border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100 text-gray-600 text-sm">
                        <th class="p-4 font-bold">Nama Siswa</th>
                        <th class="p-4 font-bold">Waktu Submit</th>
                        <th class="p-4 font-bold">File / Lampiran</th>
                        <th class="p-4 font-bold">Nilai</th>
                        <th class="p-4 font-bold text-right">Aksi Penilaian</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100 text-sm text-gray-700">
                    @forelse($submissions as $submission)
                    <tr class="hover:bg-gray-50 transition-colors">
                        <td class="p-4 font-bold text-gray-900">
                            {{ $submission->user->name }}
                        </td>
                        <td class="p-4">
                            {{ $submission->created_at->format('d M Y, H:i') }}
                            @if($submission->created_at > $assignment->deadline)
                                <span class="text-xs bg-red-100 text-red-600 px-2 py-0.5 rounded font-bold ml-2">Terlambat</span>
                            @endif
                        </td>
                        <td class="p-4">
                            @if($submission->attachments && count($submission->attachments) > 0)
                                <div class="flex flex-col gap-1">
                                @foreach($submission->attachments as $attachment)
                                    <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="text-indigo-650 hover:text-indigo-850 font-bold flex items-center space-x-1 text-xs">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                        <span class="truncate max-w-[150px]">{{ $attachment['name'] ?? 'Download' }}</span>
                                    </a>
                                @endforeach
                                </div>
                            @elseif($submission->file_path)
                                <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="text-indigo-605 hover:text-indigo-805 font-bold flex items-center space-x-1">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    <span>Download</span>
                                </a>
                            @else
                                <span class="text-gray-400 italic">Tidak ada file</span>
                            @endif
                        </td>
                        <td class="p-4">
                            @if(!is_null($submission->grade))
                                <span class="font-bold text-green-600">{{ $submission->grade }}</span> / {{ $assignment->max_score }}
                            @else
                                <span class="text-xs font-bold text-red-500">Belum dinilai</span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            <form action="{{ route('teacher.submissions.grade', $submission->id) }}" method="POST" class="flex flex-col items-end space-y-2">
                                @csrf
                                <div class="flex items-center space-x-2">
                                    <input type="number" name="grade" value="{{ $submission->grade }}" min="0" max="{{ $assignment->max_score }}" class="w-20 rounded-lg border-gray-300 text-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Skor" required>
                                    <button type="submit" class="px-3 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-lg transition-colors">
                                        Simpan
                                    </button>
                                </div>
                                <input type="text" name="feedback" value="{{ $submission->feedback }}" class="w-full max-w-xs rounded-lg border-gray-300 text-xs focus:border-indigo-500 focus:ring-indigo-500" placeholder="Komentar / Feedback (opsional)">
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500">
                            Belum ada siswa yang mengumpulkan tugas ini.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
            @if($submissions->hasPages())
                <div class="p-4 border-t border-gray-100">
                    {{ $submissions->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
