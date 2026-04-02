<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
            {{ __('Edit Pengguna') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
            <h3 class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4 flex items-center gap-3">
                <img class="h-10 w-10 rounded-full border" src="https://ui-avatars.com/api/?name={{ urlencode($user->name) }}&background=random" alt="">
                Ubah Informasi Akun - <span class="text-indigo-600 ml-1">{{ $user->name }}</span>
            </h3>
            
            <form action="{{ route('users.update', $user) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <!-- Role Selection -->
                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Peran (Role)</label>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                        @foreach($roles as $role)
                        @php $isChecked = old('role', $userRole) == $role->name; @endphp
                        <label class="relative flex cursor-pointer rounded-xl border border-gray-200 p-4 shadow-sm focus:outline-none hover:border-indigo-300 transition-colors">
                            <input type="radio" name="role" value="{{ $role->name }}" class="peer sr-only" {{ $isChecked ? 'checked' : '' }}>
                            
                            <div class="flex w-full items-center justify-between">
                                <div class="flex items-center">
                                    <div class="text-sm border-2 border-transparent peer-checked:border-indigo-600 rounded-lg transition-colors p-2 bg-indigo-50 text-indigo-700 capitalize font-bold">
                                        {{ $role->name }}
                                    </div>
                                    <span class="ml-3 text-sm font-medium text-gray-900 group-hover:text-indigo-600">Hak Akses</span>
                                </div>
                                <svg class="h-5 w-5 text-indigo-600 opacity-0 peer-checked:opacity-100 transition-opacity" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd" /></svg>
                            </div>
                            <span class="pointer-events-none absolute -inset-px rounded-xl border-2 border-transparent peer-checked:border-indigo-600 transition-colors" aria-hidden="true"></span>
                        </label>
                        @endforeach
                    </div>
                    @error('role')<p class="mt-2 text-sm text-red-600">{{ $message }}</p>@enderror
                </div>

                <!-- Input Fields -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 pb-4 border-b border-gray-100">
                    <div>
                        <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" id="name" value="{{ old('name', $user->name) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" required>
                        @error('name')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-bold text-gray-700 mb-2">Alamat Email</label>
                        <input type="email" name="email" id="email" value="{{ old('email', $user->email) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200" required>
                        @error('email')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                    </div>
                </div>

                <!-- Password Update -->
                <div>
                    <h4 class="text-sm font-bold text-gray-500 uppercase tracking-wider mb-4">Ubah Kata Sandi (Opsional)</h4>
                    <p class="text-xs text-gray-500 mb-4 bg-yellow-50 border-l-4 border-yellow-400 p-2">Kosongkan kolom ini jika tidak ingin mengubah kata sandi.</p>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label for="password" class="block text-sm font-bold text-gray-700 mb-2">Sandi Baru</label>
                            <input type="password" name="password" id="password" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                            @error('password')<p class="mt-1 text-sm text-red-600">{{ $message }}</p>@enderror
                        </div>

                        <div>
                            <label for="password_confirmation" class="block text-sm font-bold text-gray-700 mb-2">Ketik Ulang Sandi Baru</label>
                            <input type="password" name="password_confirmation" id="password_confirmation" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                        </div>
                    </div>
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('users.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors hidden sm:block">
                        Batal
                    </a>
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-transform transform active:scale-95">
                        Perbarui Data
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
