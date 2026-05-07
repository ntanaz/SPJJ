<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <span>{{ __('Pengumuman Sistem') }}</span>
            <a href="{{ route('admin.announcements.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                + Tambah Pengumuman
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 bg-green-50 text-green-700 p-4 rounded-xl border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="p-4 font-semibold text-gray-600 text-sm">Judul</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm">Target</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm">Urgensi</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm">Dibuat Pada</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($announcements as $announcement)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-4 text-gray-900 font-medium">{{ $announcement->title }}</td>
                        <td class="p-4 text-gray-600 capitalize">{{ $announcement->target_audience }}</td>
                        <td class="p-4">
                            @if($announcement->urgency_level == 'urgent')
                                <span class="px-2 py-1 bg-red-100 text-red-700 rounded-lg text-xs font-bold uppercase tracking-wider">Urgent</span>
                            @elseif($announcement->urgency_level == 'important')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs font-bold uppercase tracking-wider">Penting</span>
                            @else
                                <span class="px-2 py-1 bg-gray-100 text-gray-700 rounded-lg text-xs font-bold uppercase tracking-wider">Normal</span>
                            @endif
                        </td>
                        <td class="p-4 text-gray-500 text-sm">{{ $announcement->created_at->format('d M Y') }}</td>
                        <td class="p-4 flex gap-2">
                            <a href="{{ route('admin.announcements.edit', $announcement) }}" class="text-indigo-600 hover:text-indigo-800 font-medium text-sm">Edit</a>
                            <form action="{{ route('admin.announcements.destroy', $announcement) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500">Belum ada pengumuman.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($announcements->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $announcements->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
