<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
            {{ __('Kuis & Ujian Online') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mb-6 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-800">Daftar Evaluasi (Kuis)</h3>
            <a href="{{ route('quizzes.create') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Buat Kuis Baru
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center">
                <svg class="w-6 h-6 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-left text-xs font-bold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Mata Pelajaran</th>
                            <th class="px-6 py-4">Judul Kuis/Ujian</th>
                            <th class="px-6 py-4 text-center">Jumlah Soal</th>
                            <th class="px-6 py-4 text-center">Pendaftar/Dikerjakan</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($quizzes as $quiz)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-gray-800">{{ $quiz->course->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-indigo-600 group-hover:text-indigo-800 transition-colors">{{ $quiz->title }}</div>
                                <div class="text-xs text-gray-500 truncate max-w-[200px] mt-1">{{ Str::limit($quiz->description, 50) }}</div>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="px-3 py-1 text-xs font-bold rounded-lg border bg-blue-50 text-blue-600 border-blue-200">
                                    {{ $quiz->questions->count() }} Soal
                                </span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold bg-gray-100 px-3 py-1 rounded-lg text-gray-700 border border-gray-200">{{ $quiz->attempts()->count() }} Siswa</span>
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <div class="flex justify-end gap-2">
                                    <a href="{{ route('quizzes.show', $quiz) }}" class="px-3 py-1.5 bg-indigo-50 text-indigo-600 font-medium hover:bg-indigo-600 hover:text-white rounded-lg transition-colors shadow-sm">
                                        Kelola Soal
                                    </a>
                                    <form action="{{ route('quizzes.destroy', $quiz) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus kuis ini? Semua data ujian siswa terkait kuis ini akan hilang.');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 font-medium hover:bg-red-600 hover:text-white rounded-lg transition-colors shadow-sm">Hapus</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center bg-gray-50/50">
                                <p class="text-gray-500 font-medium">Belum ada Kuis/Ujian yang dibuat.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($quizzes->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $quizzes->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
