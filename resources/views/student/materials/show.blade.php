<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.courses.show', $material->course) }}" class="p-2 bg-white rounded-full text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
                {{ __('Materi Pembelajaran') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto">
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
            <div class="flex items-center gap-4 mb-6 pb-6 border-b border-gray-100">
                @php
                    $icon = 'M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253';
                    $color = 'indigo';
                    
                    if($material->format === 'video' || str_contains($material->type, 'video')) {
                        $icon = 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z';
                        $color = 'red';
                    } elseif($material->format === 'link' || str_contains($material->type, 'link')) {
                        $icon = 'M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1';
                        $color = 'blue';
                    } elseif($material->format === 'document' || in_array($material->type, ['pdf', 'slide'])) {
                        $icon = 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z';
                        $color = 'emerald';
                    }
                @endphp
                <div class="h-16 w-16 rounded-2xl bg-{{ $color }}-100 text-{{ $color }}-600 flex items-center justify-center flex-shrink-0">
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $icon }}"/></svg>
                </div>
                <div>
                    <h3 class="text-2xl font-black text-gray-800">{{ $material->title }}</h3>
                    <p class="text-gray-500 font-medium">{{ $material->course->name }} &bull; Diunggah {{ $material->created_at->translatedFormat('d M Y') }}</p>
                </div>
            </div>

            @if($material->description)
            <div class="prose max-w-none text-gray-700 whitespace-pre-line mb-8 border-l-4 border-indigo-100 pl-4">
                {{ $material->description }}
            </div>
            @endif

            <div class="bg-gray-50 rounded-2xl p-6 border border-gray-100">
                @if($material->format === 'video' || str_contains($material->type, 'video'))
                    <!-- Video Player/Embed -->
                    <div class="aspect-w-16 aspect-h-9 rounded-xl overflow-hidden bg-black mb-4">
                        @if($material->youtube_url)
                            @php
                                // Extract video ID from youtube URL
                                preg_match('%(?:youtube(?:-nocookie)?\.com/(?:[^/]+/.+/|(?:v|e(?:mbed)?)/|.*[?&]v=)|youtu\.be/)([^"&?/\s]{11})%i', $material->youtube_url, $match);
                                $youtube_id = $match[1] ?? null;
                            @endphp
                            @if($youtube_id)
                                <iframe width="100%" height="450" src="https://www.youtube.com/embed/{{ $youtube_id }}" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture" allowfullscreen></iframe>
                            @else
                                <a href="{{ $material->youtube_url }}" target="_blank" class="flex flex-col items-center justify-center h-full h-64 text-white hover:text-red-400">
                                    <svg class="w-16 h-16 mb-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"></path></svg>
                                    <span>Tonton di YouTube</span>
                                </a>
                            @endif
                        @elseif($material->file_path)
                            <video controls class="w-full h-full">
                                <source src="{{ asset('storage/' . $material->file_path) }}" type="video/mp4">
                                Browser Anda tidak mendukung tag video.
                            </video>
                        @endif
                    </div>
                @elseif($material->format === 'document' || in_array($material->type, ['pdf', 'slide']))
                    <div class="text-center py-8">
                        <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-emerald-100 text-emerald-500 mb-4">
                            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800 mb-2">Dokumen Materi</h4>
                        <p class="text-gray-500 mb-6">Unduh atau lihat dokumen materi pembelajaran ini.</p>
                        @if($material->file_path)
                            <div class="flex justify-center gap-4">
                                <a href="{{ asset('storage/' . $material->file_path) }}" target="_blank" class="px-6 py-3 bg-emerald-600 hover:bg-emerald-700 text-white font-bold rounded-xl shadow-sm transition-colors flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    Buka Dokumen
                                </a>
                                <a href="{{ asset('storage/' . $material->file_path) }}" download class="px-6 py-3 bg-white border-2 border-emerald-600 text-emerald-700 hover:bg-emerald-50 font-bold rounded-xl shadow-sm transition-colors flex items-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg>
                                    Unduh File
                                </a>
                            </div>
                        @else
                            <p class="text-red-500 font-bold">File dokumen tidak tersedia.</p>
                        @endif
                    </div>
                @elseif($material->format === 'link' || str_contains($material->type, 'link'))
                    <div class="text-center py-8">
                        <div class="inline-flex h-20 w-20 items-center justify-center rounded-full bg-blue-100 text-blue-500 mb-4">
                            <svg class="h-10 w-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-800 mb-2">Tautan Eksternal</h4>
                        <p class="text-gray-500 mb-6">Materi ini merujuk ke halaman web atau sumber daya eksternal.</p>
                        @if($material->text_content || $material->youtube_url || $material->file_path)
                            @php
                                $url = $material->youtube_url ?? $material->text_content ?? $material->file_path;
                                // basic check if it is a URL
                                if (!preg_match("~^(?:f|ht)tps?://~i", $url)) {
                                    $url = "http://" . $url;
                                }
                            @endphp
                            <a href="{{ $url }}" target="_blank" class="inline-flex px-6 py-3 bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl shadow-sm transition-colors items-center gap-2">
                                Buka Tautan
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                            </a>
                        @else
                            <p class="text-red-500 font-bold">Tautan tidak tersedia.</p>
                        @endif
                    </div>
                @else
                    <!-- Text/Default -->
                    <div class="prose max-w-none text-gray-700">
                        @if($material->text_content)
                            <div class="whitespace-pre-wrap">{!! nl2br(e($material->text_content)) !!}</div>
                        @else
                            <p class="text-gray-500 italic">Konten teks tidak tersedia.</p>
                        @endif
                    </div>
                @endif
            </div>

            <div class="mt-8 flex justify-end">
                @php
                    $isCompleted = \App\Models\MaterialProgress::where('material_id', $material->id)->where('user_id', auth()->id())->where('is_completed', true)->exists();
                @endphp
                @if($isCompleted)
                    <div class="inline-flex items-center gap-2 px-6 py-3 bg-emerald-50 text-emerald-700 font-bold rounded-xl border border-emerald-200">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Materi Telah Diselesaikan
                    </div>
                @else
                    <form action="{{ route('student.materials.complete', $material) }}" method="POST">
                        @csrf
                        <button type="submit" class="px-6 py-3 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm transition-colors flex items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Tandai Telah Dibaca / Selesai
                        </button>
                    </form>
                @endif
            </div>

        </div>
    </div>
</x-app-layout>
