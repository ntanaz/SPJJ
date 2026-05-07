<x-app-layout>
    <x-slot name="header">
        {{ __('Manajemen Hak Akses & Role') }}
    </x-slot>

    @if(session('success'))
        <div class="mb-4 bg-green-50 text-green-700 p-4 rounded-xl border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($roles as $role)
        <div class="bg-white rounded-2xl border border-gray-100 shadow-sm p-6">
            <div class="flex items-center justify-between mb-4">
                <h3 class="text-lg font-bold text-gray-900 capitalize">{{ $role->name }}</h3>
                <span class="px-2 py-1 bg-indigo-50 text-indigo-700 rounded-lg text-xs font-bold">{{ $role->permissions->count() }} Permissions</span>
            </div>

            <form action="{{ route('admin.roles.update', $role) }}" method="POST">
                @csrf @method('PUT')
                
                <div class="space-y-2 mb-6 max-h-60 overflow-y-auto pr-2">
                    @foreach($permissions as $permission)
                    <label class="flex items-center space-x-3 cursor-pointer p-2 hover:bg-gray-50 rounded-lg transition-colors">
                        <input type="checkbox" name="permissions[]" value="{{ $permission->name }}" 
                               {{ $role->hasPermissionTo($permission->name) ? 'checked' : '' }} 
                               class="rounded text-indigo-600 focus:ring-indigo-500 h-4 w-4 border-gray-300">
                        <span class="text-sm font-medium text-gray-700 capitalize">{{ $permission->name }}</span>
                    </label>
                    @endforeach
                </div>

                <div class="pt-4 border-t border-gray-100">
                    <button type="submit" class="w-full py-2 bg-indigo-600 text-white rounded-xl text-sm font-medium hover:bg-indigo-700 transition-colors">
                        Simpan Hak Akses
                    </button>
                </div>
            </form>
        </div>
        @endforeach
    </div>
</x-app-layout>
