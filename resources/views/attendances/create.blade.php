<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
            {{ __('Buat Sesi Absensi Baru') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-3xl border border-gray-100">
                <div class="p-8">
                    <form method="POST" action="{{ route('attendances.store') }}">
                        @csrf
                        <div class="space-y-6">
                            <!-- Course Selection -->
                            <div>
                                <label for="course_id" class="block text-sm font-bold text-gray-700 mb-2">Pilih Mata Pelajaran <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <select id="course_id" name="course_id" class="block w-full pl-4 pr-10 py-3 text-base border-gray-300 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm rounded-2xl shadow-sm font-medium text-gray-700 appearance-none bg-gray-50/50" required>
                                        <option value="" disabled selected>-- Pilih Kelas --</option>
                                        @foreach($courses as $course)
                                            <option value="{{ $course->id }}" {{ $selectedCourse == $course->id ? 'selected' : '' }}>
                                                {{ $course->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>
                                @error('course_id')<p class="mt-2 text-sm text-red-600 font-bold">{{ $message }}</p>@enderror
                            </div>

                            <!-- Title -->
                            <div>
                                <label for="title" class="block text-sm font-bold text-gray-700 mb-2">Judul Sesi Absensi <span class="text-red-500">*</span></label>
                                <input id="title" name="title" type="text" value="Presensi Kehadiran" class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-medium" required>
                                @error('title')<p class="mt-2 text-sm text-red-600 font-bold">{{ $message }}</p>@enderror
                            </div>

                            <!-- Date and Time Grid -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                <!-- Date -->
                                <div>
                                    <label for="date" class="block text-sm font-bold text-gray-700 mb-2">Tanggal Sesi <span class="text-red-500">*</span></label>
                                    <input id="date" name="date" type="date" value="{{ date('Y-m-d') }}" class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-medium" required>
                                    @error('date')<p class="mt-2 text-sm text-red-600 font-bold">{{ $message }}</p>@enderror
                                </div>

                                <!-- Start Time -->
                                <div>
                                    <label for="start_time" class="block text-sm font-bold text-gray-700 mb-2">Jam Dibuka <span class="text-red-500">*</span></label>
                                    <input id="start_time" name="start_time" type="time" class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-medium" required>
                                    @error('start_time')<p class="mt-2 text-sm text-red-600 font-bold">{{ $message }}</p>@enderror
                                </div>

                                <!-- End Time -->
                                <div>
                                    <label for="end_time" class="block text-sm font-bold text-gray-700 mb-2">Jam Ditutup <span class="text-red-500">*</span></label>
                                    <input id="end_time" name="end_time" type="time" class="appearance-none block w-full px-4 py-3 border border-gray-300 rounded-2xl shadow-sm placeholder-gray-400 focus:outline-none focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm font-medium" required>
                                    @error('end_time')<p class="mt-2 text-sm text-red-600 font-bold">{{ $message }}</p>@enderror
                                </div>
                            </div>
                        </div>

                        <div class="pt-8 flex items-center justify-end gap-3">
                            <a href="{{ url()->previous() }}" class="px-6 py-3 bg-white border border-gray-300 rounded-2xl text-sm font-bold text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors shadow-sm">
                                Batal
                            </a>
                            <button type="submit" class="px-6 py-3 border border-transparent rounded-2xl shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                Simpan Sesi Absensi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
