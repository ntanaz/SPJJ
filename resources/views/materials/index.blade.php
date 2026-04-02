<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
            {{ __('Materi Pembelajaran') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mb-6 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-800">Daftar Modul & Materi</h3>
            <a href="{{ route('materials.create') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Unggah Materi Baru
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
                            <th class="px-6 py-4">Judul Materi</th>
                            <th class="px-6 py-4">Tipe Media</th>
                            <th class="px-6 py-4 text-center">Urutan Bab</th>
                            <th class="px-6 py-4 text-center">Gembok (Lock)</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($materials as $material)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <span class="text-sm font-bold text-gray-800">{{ $material->course->name ?? 'N/A' }}</span>
                            </td>
                            <td class="px-6 py-4">
                                <div class="text-sm font-bold text-indigo-600 group-hover:text-indigo-800 transition-colors">{{ $material->title }}</div>
                                <div class="text-xs text-gray-500 truncate max-w-[200px] mt-1">{{ $material->description }}</div>
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $typeColors = [
                                        'pdf' => 'bg-red-100 text-red-700',
                                        'video' => 'bg-blue-100 text-blue-700',
                                        'slide' => 'bg-yellow-100 text-yellow-700',
                                        'file' => 'bg-gray-100 text-gray-700',
                                        'meeting_link' => 'bg-purple-100 text-purple-700',
                                    ];
                                    $color = $typeColors[$material->type] ?? 'bg-indigo-100 text-indigo-700';
                                @endphp
                                <span class="px-3 py-1 text-xs font-bold rounded-full {{ $color }} uppercase tracking-wider">{{ $material->type }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                <span class="text-sm font-bold bg-gray-100 px-3 py-1 rounded-lg text-gray-700 border border-gray-200">Bab {{ $material->order }}</span>
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($material->is_locked)
                                    <svg class="w-5 h-5 text-amber-500 mx-auto" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                                @else
                                    <svg class="w-5 h-5 text-emerald-500 mx-auto" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 11V7a4 4 0 118 0m-4 8v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2z" /></svg>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm">
                                <form action="{{ route('materials.destroy', $material) }}" method="POST" class="inline-block" onsubmit="return confirm('Hapus materi ini? Semua progres siswa yang terkait materi ini akan diinisialisasi ulang.');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 font-medium hover:bg-red-600 hover:text-white rounded-lg transition-colors shadow-sm">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-8 text-center bg-gray-50/50">
                                <svg class="h-12 w-12 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                <p class="text-gray-500 font-medium">Belum ada materi pembelajaran yang diunggah.</p>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($materials->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $materials->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
