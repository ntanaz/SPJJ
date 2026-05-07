<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.courses.show', $assignment->course) }}" class="p-2 bg-white rounded-full text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
                {{ __('Pekerjaan Tugas Siswa') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto">
        @if(session('success'))
            <div class="mb-6 p-4 bg-emerald-50 border border-emerald-200 text-emerald-700 rounded-xl relative flex items-center shadow-sm">
                <svg class="w-6 h-6 mr-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="font-bold text-lg">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div class="mb-6 p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl relative flex items-center shadow-sm">
                <svg class="w-6 h-6 mr-3 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                <span class="font-bold text-lg">{{ session('error') }}</span>
            </div>
        @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            <!-- Left col: Task Instructions -->
            <div class="lg:col-span-2 space-y-6">
                <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
                    <div class="flex items-center gap-3 mb-6">
                        <div class="h-14 w-14 rounded-2xl bg-pink-100 text-pink-500 flex items-center justify-center">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                        </div>
                        <div>
                            <h3 class="text-2xl font-black text-gray-800">{{ $assignment->title }}</h3>
                            <p class="text-gray-500 font-medium">{{ $assignment->course->name }}</p>
                        </div>
                    </div>

                    <div class="prose max-w-none text-gray-700 whitespace-pre-line border-t border-gray-100 pt-6">
                        {{ $assignment->description }}
                    </div>
                </div>
            </div>

            <!-- Right col: Submission/Deadline Box -->
            <div class="space-y-6">
                <!-- Status Box -->
                <div class="bg-white rounded-3xl p-6 shadow-sm border border-gray-100">
                    <h4 class="font-bold text-lg text-gray-800 mb-4 border-b border-gray-100 pb-3">Status Pengerjaan</h4>
                    
                    <div class="space-y-4 text-sm">
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Status Tugas</span>
                            @if($submission)
                                @if($submission->is_late)
                                    <span class="px-3 py-1 bg-amber-100 text-amber-700 rounded-full font-bold">Diserahkan Terlambat</span>
                                @else
                                    <span class="px-3 py-1 bg-emerald-100 text-emerald-700 rounded-full font-bold">Diserahkan</span>
                                @endif
                            @else
                                <span class="px-3 py-1 bg-gray-100 text-gray-700 rounded-full font-bold">Belum diserahkan</span>
                            @endif
                        </div>
                        @if($submission)
                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Waktu Diserahkan</span>
                            <span class="font-bold text-gray-800 text-right">{{ $submission->updated_at->translatedFormat('d M Y, H:i') }}</span>
                        </div>
                        @endif

                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Batas Waktu</span>
                            <span class="font-bold text-gray-800 text-right">{{ \Carbon\Carbon::parse($assignment->deadline)->translatedFormat('d M Y, H:i') }}</span>
                        </div>

                        <div class="flex justify-between items-center py-2 border-b border-gray-50">
                            <span class="text-gray-500 font-medium">Sisa Waktu</span>
                            @if($isPastDeadline)
                                <span class="font-bold text-red-600 bg-red-50 px-2 py-1 rounded-md">{{ $timeLeft }}</span>
                            @else
                                <span class="font-bold text-amber-600 bg-amber-50 px-2 py-1 rounded-md">{{ $timeLeft }}</span>
                            @endif
                        </div>

                        @if($submission && $submission->grade !== null)
                        <div class="flex justify-between items-center py-2 pt-4">
                            <span class="text-gray-500 font-bold">Nilai Anda</span>
                            <span class="text-2xl font-black text-indigo-600">{{ $submission->grade }}/100</span>
                        </div>
                        @if($submission->feedback)
                        <div class="mt-4 p-4 bg-indigo-50 border border-indigo-100 rounded-xl">
                            <p class="text-xs font-bold text-indigo-400 uppercase tracking-widest mb-1">Catatan Guru:</p>
                            <p class="text-sm text-indigo-800">{{ $submission->feedback }}</p>
                        </div>
                        @endif
                        @endif
                    </div>
                </div>

                <!-- Submission Form Area -->
                <div class="bg-gradient-to-br from-indigo-50 to-blue-50 rounded-3xl p-6 shadow-sm border border-indigo-100">
                    <h4 class="font-bold text-lg text-indigo-900 mb-4 border-b border-indigo-200 pb-3">Area Penyerahan</h4>
                    
                        @if($submission && $isPastDeadline)
                            <!-- Already submitted but past deadline (can't resubmit, wait, maybe we allow late resubmission? let's allow it but warn them) -->
                            <div class="text-center py-4 bg-amber-50/50 rounded-xl border border-amber-200 mb-4">
                                <p class="text-amber-600 font-bold mb-1">Batas Waktu Telah Lewat</p>
                                <p class="text-xs text-amber-500 italic">Pengumpulan baru akan ditandai terlambat.</p>
                            </div>
                        @endif
                        <!-- Within deadline (Can submit/resubmit) -->
                        <form action="{{ route('student.assignments.submit', $assignment) }}" method="POST" enctype="multipart/form-data" class="space-y-4">
                            @csrf
                            
                            @if($submission)
                                <div class="py-3 px-4 bg-indigo-100/50 rounded-xl mb-4 border border-indigo-200 text-sm">
                                    <p class="text-indigo-600 font-bold mb-2">Berkas/Jawaban Tersimpan:</p>
                                    @if($submission->text_content)
                                        <div class="bg-white p-3 rounded border border-indigo-100 text-gray-700 mb-3 whitespace-pre-line text-xs">
                                            {{ $submission->text_content }}
                                        </div>
                                    @endif
                                    
                                    @if($submission->attachments && count($submission->attachments) > 0)
                                        <ul class="space-y-1 mb-2">
                                        @foreach($submission->attachments as $attachment)
                                            <li>
                                                <a href="{{ asset('storage/' . $attachment['path']) }}" target="_blank" class="font-bold text-indigo-800 hover:underline flex items-center gap-1 text-xs">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                                    {{ $attachment['name'] ?? 'File Terlampir' }}
                                                </a>
                                            </li>
                                        @endforeach
                                        </ul>
                                    @elseif($submission->file_path)
                                        <a href="{{ asset('storage/' . $submission->file_path) }}" target="_blank" class="font-bold text-indigo-800 hover:underline flex items-center gap-1 text-xs mb-2">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.172 7l-6.586 6.586a2 2 0 102.828 2.828l6.414-6.586a4 4 0 00-5.656-5.656l-6.415 6.585a6 6 0 108.486 8.486L20.5 13"></path></svg>
                                            File Laporan Anda
                                        </a>
                                    @endif
                                    
                                    <p class="text-xs text-indigo-500 mt-2">Mengisi form di bawah akan menambahkan/menimpa jawaban Anda.</p>
                                </div>
                            @endif

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Teks Jawaban (Opsional)</label>
                                <textarea name="text_content" rows="4" class="w-full rounded-2xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 text-sm transition-colors" placeholder="Ketik jawaban Anda di sini jika diminta..."></textarea>
                                @error('text_content')<p class="mt-2 text-sm text-red-600 font-bold">{{ $message }}</p>@enderror
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-gray-700 mb-2">Upload File Laporan/Tugas</label>
                                <div class="mt-1 flex justify-center px-6 pt-5 pb-6 border-2 border-indigo-200 border-dashed rounded-2xl bg-white hover:bg-gray-50 transition-colors">
                                    <div class="space-y-1 text-center">
                                        <svg class="mx-auto h-10 w-10 text-indigo-400" stroke="currentColor" fill="none" viewBox="0 0 48 48" aria-hidden="true"><path d="M28 8H12a4 4 0 00-4 4v20m32-12v8m0 0v8a4 4 0 01-4 4H12a4 4 0 01-4-4v-4m32-4l-3.172-3.172a4 4 0 00-5.656 0L28 28M8 32l9.172-9.172a4 4 0 015.656 0L28 28m0 0l4 4m4-24h8m-4-4v8m-12 4h.02" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" /></svg>
                                        <div class="flex text-sm text-gray-600 justify-center">
                                            <label for="files" class="relative cursor-pointer bg-white rounded-md font-medium text-indigo-600 hover:text-indigo-500 focus-within:outline-none focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-indigo-500">
                                                <span>Pilih file (Bisa lebih dari 1)</span>
                                                <input id="files" name="files[]" type="file" multiple class="sr-only">
                                            </label>
                                        </div>
                                        <p class="text-xs text-gray-500">PDF, DOCX, ZIP, JPG, PNG s/d 10MB</p>
                                    </div>
                                </div>
                                @error('files.*')<p class="mt-2 text-sm text-red-600 font-bold">{{ $message }}</p>@enderror
                                @error('files')<p class="mt-2 text-sm text-red-600 font-bold">{{ $message }}</p>@enderror
                            </div>
                            
                            <button type="submit" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-xl shadow-sm text-sm font-bold text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 transition-colors">
                                {{ $submission ? 'Perbarui Penyerahan' : 'Serahkan Tugas Sekarang' }}
                            </button>
                        </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
