<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center justify-between w-full">
            <span>{{ __('Pusat Sumber Belajar Digital (PSBD)') }}</span>
            <a href="{{ route('admin.learning-resources.create') }}" class="px-4 py-2 bg-indigo-600 text-white rounded-lg text-sm font-medium hover:bg-indigo-700 transition-colors">
                + Upload Resource
            </a>
        </div>
    </x-slot>

    @if(session('success'))
        <div class="mb-4 bg-green-50 text-green-700 p-4 rounded-xl border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden mb-6 p-4">
        <form action="{{ route('admin.learning-resources.index') }}" method="GET" class="flex flex-col md:flex-row gap-4">
            <div class="flex-1">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari materi atau bank soal..." class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
            </div>
            <div class="w-full md:w-48">
                <select name="category" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                    <option value="">Semua Kategori</option>
                    <option value="bank_soal" {{ request('category') == 'bank_soal' ? 'selected' : '' }}>Bank Soal</option>
                    <option value="materi" {{ request('category') == 'materi' ? 'selected' : '' }}>Materi Pembelajaran</option>
                </select>
            </div>
            <button type="submit" class="px-5 py-2.5 bg-gray-900 text-white rounded-xl font-medium hover:bg-gray-800 transition-colors">Filter</button>
        </form>
    </div>

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50 border-b border-gray-100">
                        <th class="p-4 font-semibold text-gray-600 text-sm">Judul & Tipe</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm">Kategori</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm">Uploader</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm">Tanggal Upload</th>
                        <th class="p-4 font-semibold text-gray-600 text-sm w-32">Aksi</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($resources as $resource)
                    <tr class="hover:bg-gray-50/50 transition-colors">
                        <td class="p-4">
                            <div class="font-medium text-gray-900 flex items-center gap-2">
                                <span class="uppercase text-[10px] font-bold bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded">{{ $resource->type }}</span>
                                {{ $resource->title }}
                            </div>
                            <div class="text-sm text-gray-500 mt-1 line-clamp-1">{{ $resource->description }}</div>
                        </td>
                        <td class="p-4">
                            @if($resource->category == 'bank_soal')
                                <span class="px-2 py-1 bg-yellow-100 text-yellow-700 rounded-lg text-xs font-bold">Bank Soal</span>
                            @else
                                <span class="px-2 py-1 bg-blue-100 text-blue-700 rounded-lg text-xs font-bold">Materi</span>
                            @endif
                        </td>
                        <td class="p-4 text-gray-600 text-sm">{{ $resource->user->name }}</td>
                        <td class="p-4 text-gray-500 text-sm">{{ $resource->created_at->format('d M Y') }}</td>
                        <td class="p-4 flex gap-2">
                            <a href="{{ asset('storage/' . $resource->file_path) }}" target="_blank" class="text-blue-600 hover:text-blue-800 font-medium text-sm">Download</a>
                            <form action="{{ route('admin.learning-resources.destroy', $resource) }}" method="POST" class="inline-block" onsubmit="return confirm('Yakin ingin menghapus resource ini?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-red-600 hover:text-red-800 font-medium text-sm">Hapus</button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-gray-500">Belum ada resource / dokumen.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($resources->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $resources->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
