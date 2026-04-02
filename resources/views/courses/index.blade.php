<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
            {{ __('Mata Pelajaran') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mb-6 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-800">Daftar Mata Pelajaran</h3>
            <a href="{{ route('courses.create') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Tambah Mata Pelajaran
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl flex items-center">
                <svg class="w-6 h-6 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @forelse($courses as $course)
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden group hover:shadow-md transition-shadow relative">
                    <div class="h-32 bg-gradient-to-r from-indigo-500 to-purple-600 relative">
                        <div class="absolute inset-0 opacity-20 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] mix-blend-overlay"></div>
                    </div>
                    <div class="p-6">
                        <div class="flex justify-between items-start mb-4">
                            <h4 class="text-xl font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">{{ $course->name }}</h4>
                        </div>
                        <p class="text-gray-600 text-sm line-clamp-3 mb-6">{{ $course->description }}</p>
                        
                        <div class="flex gap-2 border-t border-gray-100 pt-4">
                            <a href="{{ route('courses.edit', $course) }}" class="flex-1 text-center py-2 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-600 hover:text-white transition-colors font-medium text-sm">Edit</a>
                            
                            <form action="{{ route('courses.destroy', $course) }}" method="POST" class="flex-1" onsubmit="return confirm('Yakin ingin menghapus?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-full text-center py-2 bg-red-50 text-red-600 rounded-lg hover:bg-red-600 hover:text-white transition-colors font-medium text-sm">Hapus</button>
                            </form>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-1 md:col-span-2 lg:col-span-3 py-12 text-center bg-white rounded-2xl border border-gray-100 border-dashed">
                    <svg class="w-16 h-16 text-gray-300 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                    <h3 class="text-lg font-bold text-gray-800">Belum Ada Mata Pelajaran</h3>
                    <p class="text-gray-500 mt-1">Tambahkan mata pelajaran pertama Anda sekarang.</p>
                </div>
            @endforelse
        </div>
        
        @if($courses->hasPages())
            <div class="mt-8">
                {{ $courses->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
