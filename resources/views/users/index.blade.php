<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
            {{ __('Manajemen Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="mb-6 flex justify-between items-center">
            <h3 class="text-xl font-bold text-gray-800">Daftar Pengguna</h3>
            <a href="{{ route('users.create') }}" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-semibold rounded-xl shadow-sm hover:shadow-md transition-all flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Tambah Pengguna
            </a>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl relative flex items-center">
                <svg class="w-6 h-6 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="font-medium">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl relative flex items-center">
                <svg class="w-6 h-6 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="font-medium">{{ session('error') }}</span>
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full whitespace-nowrap">
                    <thead>
                        <tr class="bg-gray-50 border-b border-gray-100 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">
                            <th class="px-6 py-4">Nama</th>
                            <th class="px-6 py-4">Email</th>
                            <th class="px-6 py-4">Peran (Role)</th>
                            <th class="px-6 py-4 text-center">XP</th>
                            <th class="px-6 py-4 text-right">Aksi</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($users as $user)
                        <tr class="hover:bg-gray-50/50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center">
                                    <img class="h-10 w-10 rounded-full object-cover mr-3 border border-gray-200" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=e0e7ff&color=4338ca" alt="">
                                    <div class="font-bold text-gray-800 group-hover:text-indigo-600 transition-colors">{{ $user->name }}</div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-gray-600 text-sm">
                                {{ $user->email }}
                            </td>
                            <td class="px-6 py-4">
                                @foreach($user->roles as $role)
                                    @php
                                        $color = match($role->name) {
                                            'admin' => 'bg-purple-100 text-purple-700 border-purple-200',
                                            'guru' => 'bg-emerald-100 text-emerald-700 border-emerald-200',
                                            'siswa' => 'bg-blue-100 text-blue-700 border-blue-200',
                                            default => 'bg-gray-100 text-gray-700 border-gray-200'
                                        };
                                    @endphp
                                    <span class="px-3 py-1 text-xs font-bold rounded-full border {{ $color }} capitalize relative top-[-1px]">{{ $role->name }}</span>
                                @endforeach
                            </td>
                            <td class="px-6 py-4 text-center">
                                @if($user->hasRole('siswa'))
                                    <span class="font-black text-amber-500">{{ $user->points }} XP</span>
                                @else
                                    <span class="text-gray-400">-</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-right text-sm font-medium">
                                <a href="{{ route('users.edit', $user) }}" class="inline-block px-3 py-1.5 bg-indigo-50 text-indigo-600 hover:bg-indigo-600 hover:text-white rounded-lg transition-colors mr-2">Edit</a>
                                <form action="{{ route('users.destroy', $user) }}" method="POST" class="inline-block" onsubmit="return confirm('Apakah Anda yakin ingin menghapus pengguna ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="px-3 py-1.5 bg-red-50 text-red-600 hover:bg-red-600 hover:text-white rounded-lg transition-colors">Hapus</button>
                                </form>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-8 text-center text-gray-500">
                                <div class="flex flex-col items-center justify-center">
                                    <svg class="h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                                    <p class="font-medium text-lg text-gray-600">Belum ada pengguna</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($users->hasPages())
                <div class="px-6 py-4 border-t border-gray-100">
                    {{ $users->links() }}
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
