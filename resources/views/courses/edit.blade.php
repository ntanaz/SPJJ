<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
            {{ __('Ubah Mata Pelajaran') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 space-y-6">
            <h3 class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4">
                Edit - <span class="text-indigo-600">{{ $course->name }}</span>
            </h3>
            
            <form action="{{ route('courses.update', $course) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')
                
                <div>
                    <label for="name" class="block text-sm font-bold text-gray-700 mb-2">Nama Mata Pelajaran</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $course->name) }}" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" required>
                    @error('name')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                </div>

                <div>
                    <label for="description" class="block text-sm font-bold text-gray-700 mb-2">Deskripsi / Penjelasan Singkat</label>
                    <textarea name="description" id="description" rows="4" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" required>{{ old('description', $course->description) }}</textarea>
                    @error('description')<p class="mt-1 text-sm text-red-600 font-medium">{{ $message }}</p>@enderror
                </div>

                <div class="flex items-center justify-end space-x-4 pt-4 border-t border-gray-100">
                    <a href="{{ route('courses.index') }}" class="px-5 py-2.5 bg-gray-100 text-gray-700 font-semibold rounded-xl hover:bg-gray-200 transition-colors hidden sm:block">
                        Batal
                    </a>
                    <button type="submit" class="w-full sm:w-auto px-6 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-transform transform active:scale-95">
                        Perbarui Mata Pelajaran
                    </button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
