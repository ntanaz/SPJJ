<x-app-layout>
    <x-slot name="header">
        {{ __('Pengaturan Profil Guru') }}
    </x-slot>

    <div class="max-w-4xl mx-auto space-y-6">
        @if(session('success'))
            <div class="bg-green-50 text-green-700 p-4 rounded-xl border border-green-200">
                {{ session('success') }}
            </div>
        @endif

        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
            <div class="p-6 border-b border-gray-100">
                <h3 class="text-lg font-bold text-gray-900">Informasi Pribadi & Keamanan</h3>
                <p class="text-sm text-gray-500 mt-1">Perbarui nama, biodata, foto profil, dan kata sandi akun Anda.</p>
            </div>
            
            <form action="{{ route('teacher.profile.update') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-6">
                @csrf
                @method('PUT')
                
                <div class="flex items-center space-x-6">
                    <div class="flex-shrink-0">
                        <img src="{{ auth()->user()->avatar ? asset('storage/' . auth()->user()->avatar) : 'https://ui-avatars.com/api/?name='.urlencode(auth()->user()->name) }}" class="w-24 h-24 rounded-full object-cover border-4 border-white shadow-lg" alt="Profile Photo">
                    </div>
                    <div class="flex-1">
                        <label class="block text-sm font-bold text-gray-700 mb-2">Foto Profil (Opsional)</label>
                        <input type="file" name="avatar" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                        <p class="text-xs text-gray-500 mt-1">JPG, JPEG, PNG maks 2MB.</p>
                        @error('avatar') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Nama Lengkap</label>
                        <input type="text" name="name" value="{{ old('name', auth()->user()->name) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required>
                        @error('name') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                    </div>
                    
                    <div>
                        <label class="block text-sm font-bold text-gray-700 mb-2">Alamat Email</label>
                        <input type="email" value="{{ auth()->user()->email }}" class="w-full rounded-xl border-gray-300 shadow-sm bg-gray-50 text-gray-500" disabled>
                        <p class="text-xs text-gray-400 mt-1">Email tidak dapat diubah. Hubungi admin untuk bantuan.</p>
                    </div>
                </div>

                <div>
                    <label class="block text-sm font-bold text-gray-700 mb-2">Bio / Deskripsi Singkat</label>
                    <textarea name="bio" rows="4" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ceritakan sedikit tentang Anda, keahlian Anda, atau pesan untuk siswa...">{{ old('bio', auth()->user()->bio) }}</textarea>
                    @error('bio') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                </div>

                <hr class="border-gray-100 my-6">

                <div>
                    <h4 class="text-sm font-bold text-gray-900 mb-4">Ubah Kata Sandi (Kosongkan jika tidak ingin diubah)</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Kata Sandi Baru</label>
                            <input type="password" name="password" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Minimal 8 karakter">
                            @error('password') <p class="text-red-500 text-xs mt-1">{{ $message }}</p> @enderror
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-2">Konfirmasi Kata Sandi Baru</label>
                            <input type="password" name="password_confirmation" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" placeholder="Ulangi kata sandi baru">
                        </div>
                    </div>
                </div>

                <div class="flex justify-end pt-4">
                    <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-colors">
                        Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
