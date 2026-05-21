<x-app-layout>
    <x-slot name="header">
        <div class="flex items-center gap-4">
            <a href="{{ route('student.courses') }}" class="p-2 bg-white rounded-full text-gray-500 hover:text-indigo-600 hover:bg-indigo-50 transition-colors shadow-sm">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18" /></svg>
            </a>
            <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
                {{ $course->name }}
            </h2>
        </div>
    </x-slot>

    <div class="py-6 min-h-screen" x-data="{ showActivityModal: false, showModuleModal: false, editModuleModal: false, activeModule: {}, editActivityModal: false, activeActivity: {}, showDiscussionModal: false, showVideoUploadModal: false }">
        <!-- Hero Section Course -->
        <div class="rounded-3xl p-8 mb-8 text-white shadow-xl relative overflow-hidden bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600"
             @if($course->banner_image) style="background-image: url('{{ asset('storage/' . $course->banner_image) }}'); background-size: cover; background-position: center;" @endif>
            @if($course->banner_image)
                <div class="absolute inset-0 bg-black/40 backdrop-blur-[1px]"></div>
            @else
                <div class="absolute inset-0 bg-[url('https://www.transparenttextures.com/patterns/cubes.png')] opacity-10 mix-blend-overlay"></div>
            @endif
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between">
                <div>
                    <span class="inline-block px-3 py-1 bg-white/20 backdrop-blur-md rounded-full text-xs font-bold uppercase tracking-wider mb-3">Mata Pelajaran Aktif</span>
                    <h3 class="text-3xl font-extrabold mb-2">{{ $course->name }}</h3>
                    <p class="text-indigo-100 max-w-2xl text-sm leading-relaxed">{{ $course->description }}</p>
                </div>
            </div>
        </div>

        @role('guru|teacher|admin')
        <div class="mb-6 mt-4 flex justify-end gap-3">
            <button @click="showModuleModal = true" class="px-5 py-2.5 bg-white border border-gray-200 text-gray-700 font-bold rounded-xl shadow-sm hover:bg-gray-50 hover:border-gray-300 transition-colors flex items-center gap-2">
                <svg class="w-5 h-5 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Buat Bab / Modul Baru
            </button>
            <button @click="showActivityModal = true" class="px-5 py-2.5 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-sm transition-transform active:scale-95 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                Tambahkan Aktivitas atau Sumber Daya
            </button>
        </div>
        @endrole

        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            <!-- Left col: Silabus / Materi list -->
            <div class="md:col-span-2 space-y-8">
                <!-- Presensi Section (Global at Course Level) -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                        Presensi & Kehadiran Kelas
                    </h3>
                    
                    @forelse($course->attendances as $attendance)
                        <div class="bg-gradient-to-r from-emerald-50 to-white rounded-2xl p-5 shadow-sm border border-emerald-100 flex justify-between items-center group hover:shadow-md transition-all">
                            <div class="flex items-center gap-4">
                                <div class="h-12 w-12 rounded-xl bg-emerald-100 text-emerald-500 flex items-center justify-center group-hover:scale-110 transition-transform">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                                </div>
                                <div>
                                    <h4 class="font-bold text-gray-800 text-base group-hover:text-emerald-600 transition-colors">{{ $attendance->title }}</h4>
                                    <p class="text-xs text-gray-500 font-medium">{{ \Carbon\Carbon::parse($attendance->date)->translatedFormat('d F Y') }} | {{ \Carbon\Carbon::parse($attendance->start_time)->format('H:i') }} - {{ \Carbon\Carbon::parse($attendance->end_time)->format('H:i') }} WIB</p>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                            @role('siswa')
                                @php
                                    $record = $attendance->records->where('user_id', auth()->id())->first();
                                @endphp
                                @if($record)
                                    <span class="px-3 py-1.5 bg-gray-100 text-gray-600 rounded-xl font-bold text-[10px] uppercase tracking-wider flex items-center gap-1">
                                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                        Hadir ({{ $record->status }})
                                    </span>
                                @else
                                    <a href="{{ route('student.attendances.show', $attendance) }}" class="px-4 py-1.5 {{ $attendance->isCurrentlyOpen() ? 'bg-emerald-600 hover:bg-emerald-700 text-white' : 'bg-gray-200 text-gray-500 cursor-not-allowed' }} rounded-xl font-bold transition-colors text-xs shadow-sm whitespace-nowrap">
                                        {{ $attendance->isCurrentlyOpen() ? 'Isi Kehadiran' : 'Ditutup' }}
                                    </a>
                                @endif
                            @else
                                <a href="{{ route('attendances.show', $attendance) }}" class="px-4 py-1.5 bg-emerald-100 text-emerald-700 hover:bg-emerald-600 hover:text-white rounded-xl font-bold transition-colors text-xs shadow-sm whitespace-nowrap">
                                    Laporan
                                </a>
                                <form action="{{ route('attendances.destroy', $attendance) }}" method="POST" onsubmit="return confirm('Hapus sesi absensi ini?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-1.5 text-red-400 hover:text-red-500 hover:bg-red-50 rounded-xl transition-colors">
                                        <svg class="w-4.5 h-4.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                    </button>
                                </form>
                            @endrole
                            </div>
                        </div>
                    @empty
                        <div class="py-5 text-center bg-gray-50/50 rounded-2xl border border-gray-100 border-dashed">
                            <p class="text-gray-500 text-xs font-medium">Belum ada jadwal presensi kelas hari ini.</p>
                        </div>
                    @endforelse
                </div>

                <!-- Chapters/Modules Accordion Section -->
                <div>
                    <h3 class="text-xl font-bold text-gray-800 mb-4 flex items-center gap-2">
                        <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" /></svg>
                        Materi & Aktivitas Pembelajaran
                    </h3>
                    
                    <div class="space-y-4" x-data="{ activeAccordion: {{ $course->modules->first()->id ?? 'null' }} }">
                        @forelse($course->modules as $module)
                            <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden transition-all duration-300">
                                <!-- Header -->
                                <div class="p-5 flex justify-between items-center cursor-pointer bg-gray-50/50 hover:bg-gray-50 select-none"
                                     @click="activeAccordion = (activeAccordion === {{ $module->id }} ? null : {{ $module->id }})">
                                    <div class="flex items-center gap-4">
                                        <div class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                        </div>
                                        <div>
                                            <h4 class="font-bold text-gray-800 text-lg">{{ $module->title }}</h4>
                                            @if($module->description)
                                                <p class="text-sm text-gray-500 font-medium">{{ $module->description }}</p>
                                            @endif
                                        </div>
                                    </div>
                                    <div class="flex items-center gap-3" @click.stop>
                                        @role('guru|teacher|admin')
                                            <!-- Edit & Delete buttons -->
                                            <button data-id="{{ $module->id }}"
                                                    data-title="{{ $module->title }}"
                                                    data-description="{{ $module->description }}"
                                                    data-order="{{ $module->order_number }}"
                                                    @click="activeModule = { id: $el.dataset.id, title: $el.dataset.title, description: $el.dataset.description, order_number: $el.dataset.order }; editModuleModal = true;" 
                                                    class="p-2 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                            </button>
                                            <form action="{{ route('modules.destroy', $module) }}" method="POST" onsubmit="return confirm('Hapus Bab ini? Seluruh materi, kuis, tugas, dan diskusi di dalamnya akan terhapus!');" class="inline">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="p-2 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                </button>
                                            </form>
                                        @endrole
                                        <button @click="activeAccordion = (activeAccordion === {{ $module->id }} ? null : {{ $module->id }})" class="p-2 text-gray-400 hover:text-gray-600 transition-transform duration-200" :class="{ 'rotate-180': activeAccordion === {{ $module->id }} }">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                                        </button>
                                    </div>
                                </div>

                                <!-- Collapsible Content -->
                                <div x-show="activeAccordion === {{ $module->id }}" x-collapse class="border-t border-gray-100 p-5 space-y-6">
                                    <div>
                                        <h5 class="text-xs font-bold text-gray-400 uppercase tracking-wider mb-4 flex items-center gap-1.5">
                                            <svg class="w-4 h-4 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2-2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4" /></svg>
                                            Daftar Aktivitas Pembelajaran
                                        </h5>
                                        <div class="space-y-3">
                                            @forelse($module->activities as $activity)
                                                @php
                                                    $isCompleted = $activity->progress->where('user_id', auth()->id())->first()?->is_completed ?? false;
                                                    $isUnlocked = $activity->isUnlockedFor(auth()->user());
                                                    
                                                    $typeConfigs = [
                                                        'mind_map' => ['bg' => 'bg-indigo-50 text-indigo-500', 'icon' => 'M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7'],
                                                        'material' => ['bg' => 'bg-red-50 text-red-500', 'icon' => 'M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z'],
                                                        'video' => ['bg' => 'bg-blue-50 text-blue-500', 'icon' => 'M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z'],
                                                        'coding_quiz' => ['bg' => 'bg-emerald-50 text-emerald-500', 'icon' => 'M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4'],
                                                        'reflection' => ['bg' => 'bg-purple-50 text-purple-500', 'icon' => 'M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z'],
                                                        'quiz' => ['bg' => 'bg-amber-50 text-amber-500', 'icon' => 'M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z'],
                                                        'assignment' => ['bg' => 'bg-pink-50 text-pink-500', 'icon' => 'M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2-2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4'],
                                                        'discussion' => ['bg' => 'bg-indigo-50 text-indigo-500', 'icon' => 'M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z'],
                                                    ];
                                                    $config = $typeConfigs[$activity->activity_type] ?? ['bg' => 'bg-gray-50 text-gray-500', 'icon' => 'M4 6h16M4 10h16M4 14h16M4 18h16'];
                                                @endphp
                                                
                                                <div class="bg-gray-50 rounded-xl p-4 border border-gray-100 {{ !$isUnlocked ? 'opacity-50' : 'hover:shadow-sm hover:border-indigo-100' }} transition-all flex justify-between items-center group">
                                                    <div class="flex items-center gap-3">
                                                        <div class="h-10 w-10 rounded-lg {{ $config['bg'] }} flex items-center justify-center">
                                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $config['icon'] }}" /></svg>
                                                        </div>
                                                        <div>
                                                            <div class="flex items-center gap-2">
                                                                <h6 class="font-bold text-gray-800 text-sm {{ $isUnlocked ? 'group-hover:text-indigo-650' : '' }} transition-colors">
                                                                    {{ $activity->title }}
                                                                </h6>
                                                                @if($activity->is_required)
                                                                    <span class="px-1.5 py-0.5 bg-red-50 text-red-500 rounded text-[9px] font-extrabold uppercase">Wajib</span>
                                                                @endif
                                                            </div>
                                                            <p class="text-xs text-gray-500 line-clamp-1">{{ $activity->description ?: 'Aktivitas untuk bab ini' }}</p>
                                                        </div>
                                                    </div>
                                                    
                                                    <div class="flex items-center gap-2">
                                                        <!-- Guru Actions -->
                                                        @role('guru|teacher|admin')
                                                            @if($activity->activity_type === 'video' && $activity->video_id)
                                                                <a href="{{ route('videos.manage', $activity->video_id) }}" class="p-1.5 text-blue-600 hover:text-blue-850 hover:bg-blue-50 rounded-lg transition-colors" title="Kelola Kuis Video">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                                                </a>
                                                            @endif
                                                            <button data-id="{{ $activity->id }}"
                                                                    data-title="{{ $activity->title }}"
                                                                    data-description="{{ $activity->description }}"
                                                                    data-order="{{ $activity->order_number }}"
                                                                    data-required="{{ $activity->is_required ? 'true' : 'false' }}"
                                                                    @click="activeActivity = { id: $el.dataset.id, title: $el.dataset.title, description: $el.dataset.description, order_number: $el.dataset.order, is_required: $el.dataset.required === 'true' }; editActivityModal = true;" 
                                                                    class="p-1.5 text-indigo-600 hover:text-indigo-800 hover:bg-indigo-50 rounded-lg transition-colors" title="Edit">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                                            </button>
                                                            <form action="{{ route('activities.destroy', $activity) }}" method="POST" onsubmit="return confirm('Hapus aktivitas ini dari Bab?');" class="inline">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="p-1.5 text-red-500 hover:text-red-700 hover:bg-red-50 rounded-lg transition-colors" title="Hapus">
                                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                                                                </button>
                                                            </form>
                                                        @endrole

                                                        <!-- Completion / Navigation -->
                                                        @if(!$isUnlocked)
                                                            <span class="px-2.5 py-1 bg-gray-200 text-gray-605 rounded-lg flex items-center gap-1 text-[10px] font-bold">
                                                                <svg class="h-3 w-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd" /></svg>
                                                                Terkunci
                                                            </span>
                                                        @elseif($isCompleted)
                                                            <span class="px-2.5 py-1 bg-emerald-100 text-emerald-700 rounded-lg flex items-center gap-1 text-[10px] font-bold">
                                                                <svg class="h-3.5 w-3.5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" /></svg>
                                                                Selesai
                                                            </span>
                                                        @else
                                                            <a href="{{ route('student.activities.show', $activity) }}" class="px-4 py-1.5 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg font-bold transition-all text-xs shadow-sm">
                                                                Buka
                                                            </a>
                                                        @endif
                                                    </div>
                                                </div>
                                            @empty
                                                <p class="text-xs text-gray-400 font-semibold italic py-2">Belum ada aktivitas pembelajaran untuk bab ini.</p>
                                            @endforelse
                                        </div>
                                    </div>
                                    
                                    @role('guru|teacher|admin')
                                        <div class="pt-4 border-t border-gray-100 flex flex-wrap gap-2">
                                            <a href="{{ route('materials.create', ['course_id' => $course->id, 'module_id' => $module->id]) }}" class="px-3.5 py-1.5 bg-indigo-50 hover:bg-indigo-100 text-indigo-700 rounded-xl font-bold text-xs flex items-center gap-1.5 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                                + Materi & Langkah DL
                                            </a>
                                            <button @click="activeModule = { id: {{ $module->id }} }; showVideoUploadModal = true;" class="px-3.5 py-1.5 bg-teal-50 hover:bg-teal-100 text-teal-700 rounded-xl font-bold text-xs flex items-center gap-1.5 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                                                + Video Pembelajaran
                                            </button>
                                            <a href="{{ route('quizzes.create', ['course_id' => $course->id, 'module_id' => $module->id]) }}" class="px-3.5 py-1.5 bg-amber-50 hover:bg-amber-100 text-amber-700 rounded-xl font-bold text-xs flex items-center gap-1.5 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                                + Kuis
                                            </a>
                                            <a href="{{ route('assignments.create', ['course_id' => $course->id, 'module_id' => $module->id]) }}" class="px-3.5 py-1.5 bg-pink-50 hover:bg-pink-100 text-pink-700 rounded-xl font-bold text-xs flex items-center gap-1.5 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                                + Tugas
                                            </a>
                                            <button @click="activeModule = { id: {{ $module->id }} }; showDiscussionModal = true;" class="px-3.5 py-1.5 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-xl font-bold text-xs flex items-center gap-1.5 transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                                                + Forum Diskusi
                                            </button>
                                        </div>
                                    @endrole
                                </div>
                            </div>
                        @empty
                            <div class="py-12 text-center bg-white rounded-2xl border border-gray-200 border-dashed">
                                <svg class="mx-auto h-12 w-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" /></svg>
                                <h5 class="text-sm font-bold text-gray-800 mb-1">Belum ada Bab / Modul</h5>
                                <p class="text-xs text-gray-500 font-medium max-w-xs mx-auto mb-4">Kelas ini belum memiliki modul pembelajaran atau bab terdaftar.</p>
                                @role('guru|teacher|admin')
                                    <button @click="showModuleModal = true" class="px-4 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl text-xs transition-colors">
                                        Buat Bab Pertama
                                    </button>
                                @endrole
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
            
            <!-- Right col -->
            <div class="space-y-6">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100">
                    <h3 class="text-lg font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3">Progress Belajar Anda</h3>
                    
                    <div class="w-full bg-gray-100 rounded-full h-4 mb-2">
                        <div class="bg-gradient-to-r from-emerald-400 to-teal-500 h-4 rounded-full" style="width: {{ $progressPercent ?? 0 }}%"></div>
                    </div>
                    <div class="flex justify-between items-center text-xs text-gray-500 font-bold mb-6">
                        <span>{{ $progressPercent ?? 0 }}% Selesai</span>
                        <span>{{ $completedCount ?? 0 }}/{{ $course->materials->count() }} Materi</span>
                    </div>

                    <div class="bg-indigo-50 rounded-xl p-4 border border-indigo-100 text-center text-indigo-800 text-sm">
                        Selesaikan materi ini dan dapatkan <strong>+100 XP Badge Baru!</strong> 🏆
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Activity Chooser Moodle-style -->
        <div x-show="showActivityModal" style="display: none;" class="fixed inset-0 z-[99] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showActivityModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" @click="showActivityModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showActivityModal" x-transition.scale class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-4xl sm:w-full border border-gray-100">
                    <div class="bg-gray-50 px-6 py-4 border-b border-gray-100 flex justify-between items-center">
                        <h3 class="text-xl font-black text-gray-800 flex items-center gap-2" id="modal-title">
                            <svg class="w-6 h-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                            Tambahkan Aktivitas atau Sumber Daya
                        </h3>
                        <button @click="showActivityModal = false" class="text-gray-400 hover:text-red-500 transition-colors p-2 bg-white rounded-full hover:bg-red-50 hover:shadow-sm">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </div>
                    
                    <div class="p-6 max-h-[65vh] overflow-y-auto hide-scrollbar z-50 relative bg-white">
                        <!-- 1. Activities -->
                        <h4 class="font-bold text-gray-700 uppercase tracking-widest text-xs mb-4 flex items-center gap-2"><div class="w-2 h-2 rounded-full bg-pink-500"></div> AKTIVITAS (ACTIVITIES)</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4 mb-8">
                            <a href="{{ route('assignments.create', ['course_id' => $course->id]) }}" class="flex flex-col items-center p-4 bg-white border border-gray-100 hover:border-pink-300 rounded-2xl hover:shadow-md transition-all group cursor-pointer">
                                <div class="h-14 w-14 bg-pink-50 text-pink-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-800 text-center">Assignment</span>
                            </a>
                            
                            <a href="{{ route('quizzes.create', ['course_id' => $course->id]) }}" class="flex flex-col items-center p-4 bg-white border border-gray-100 hover:border-amber-300 rounded-2xl hover:shadow-md transition-all group cursor-pointer">
                                <div class="h-14 w-14 bg-amber-50 text-amber-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-800 text-center">Quiz</span>
                            </a>

                            <button type="button" @click="showActivityModal = false; window.scrollTo({ top: document.body.scrollHeight, behavior: 'smooth' });" class="flex flex-col items-center p-4 bg-white border border-gray-100 hover:border-indigo-300 rounded-2xl hover:shadow-md transition-all group cursor-pointer">
                                <div class="h-14 w-14 bg-indigo-50 text-indigo-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"/></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-800 text-center">Forum</span>
                            </button>

                            <a href="{{ route('attendances.create', ['course_id' => $course->id]) }}" class="flex flex-col items-center p-4 bg-white border border-gray-100 hover:border-blue-300 rounded-2xl hover:shadow-md transition-all group cursor-pointer">
                                <div class="h-14 w-14 bg-blue-50 text-blue-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform"><svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg></div>
                                <span class="text-sm font-bold text-gray-800 text-center">Attendance</span>
                            </a>

                            <div onclick="alert('Module segera hadir')" class="flex flex-col items-center p-4 bg-gray-50 border border-gray-100 rounded-2xl opacity-70 group cursor-not-allowed">
                                <div class="h-14 w-14 bg-gray-200 text-gray-500 rounded-xl flex items-center justify-center mb-3"><svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg></div>
                                <span class="text-xs font-bold text-gray-800 text-center">Checklist / Wiki</span>
                            </div>
                        </div>

                        <!-- 2. Resources -->
                        <h4 class="font-bold text-gray-700 uppercase tracking-widest text-xs mb-4 flex items-center gap-2 mt-4"><div class="w-2 h-2 rounded-full bg-emerald-500"></div> SUMBER DAYA (RESOURCES)</h4>
                        <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-4">
                            <a href="{{ route('materials.create', ['course_id' => $course->id]) }}" class="flex flex-col items-center p-4 bg-white border border-gray-100 hover:border-emerald-300 rounded-2xl hover:shadow-md transition-all group cursor-pointer">
                                <div class="h-14 w-14 bg-emerald-50 text-emerald-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-800 text-center">File / Page</span>
                            </a>

                            <a href="{{ route('materials.create', ['course_id' => $course->id]) }}" class="flex flex-col items-center p-4 bg-white border border-gray-100 hover:border-sky-300 rounded-2xl hover:shadow-md transition-all group cursor-pointer">
                                <div class="h-14 w-14 bg-sky-50 text-sky-600 rounded-xl flex items-center justify-center mb-3 group-hover:scale-110 transition-transform">
                                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                                </div>
                                <span class="text-sm font-bold text-gray-800 text-center">URL</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal Tambah Bab / Modul -->
        <div x-show="showModuleModal" style="display: none;" class="fixed inset-0 z-[99] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showModuleModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" @click="showModuleModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showModuleModal" x-transition.scale class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 p-8 space-y-6">
                    <h3 class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4 flex items-center gap-3">
                        <svg class="h-6 w-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Buat Bab / Modul Baru
                    </h3>
                    <form action="{{ route('courses.modules.store', $course) }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label for="new_module_title" class="block text-sm font-bold text-gray-700 mb-2">Nama Bab / Modul</label>
                            <input type="text" name="title" id="new_module_title" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" placeholder="Cth: Bab 1 - Pengenalan AI" required>
                        </div>
                        <div>
                            <label for="new_module_desc" class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Singkat (Opsional)</label>
                            <textarea name="description" id="new_module_desc" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" placeholder="Tulis instruksi atau deskripsi singkat..."></textarea>
                        </div>
                        <div>
                            <label for="new_module_order" class="block text-sm font-bold text-gray-700 mb-2">Urutan (Order Number)</label>
                            <input type="number" name="order_number" id="new_module_order" value="{{ count($course->modules) + 1 }}" min="1" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" required>
                        </div>
                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="showModuleModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors text-sm">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-colors text-sm">
                                Simpan Bab
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit Bab / Modul -->
        <div x-show="editModuleModal" style="display: none;" class="fixed inset-0 z-[99] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="editModuleModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" @click="editModuleModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="editModuleModal" x-transition.scale class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 p-8 space-y-6">
                    <h3 class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4 flex items-center gap-3">
                        <svg class="h-6 w-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        Edit Bab / Modul
                    </h3>
                    <form :action="`/modules/${activeModule.id}`" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="edit_module_title" class="block text-sm font-bold text-gray-700 mb-2">Nama Bab / Modul</label>
                            <input type="text" name="title" id="edit_module_title" x-model="activeModule.title" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" required>
                        </div>
                        <div>
                            <label for="edit_module_desc" class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Singkat (Opsional)</label>
                            <textarea name="description" id="edit_module_desc" x-model="activeModule.description" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors"></textarea>
                        </div>
                        <div>
                            <label for="edit_module_order" class="block text-sm font-bold text-gray-700 mb-2">Urutan (Order Number)</label>
                            <input type="number" name="order_number" id="edit_module_order" x-model="activeModule.order_number" min="1" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" required>
                        </div>
                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="editModuleModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors text-sm">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-colors text-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Edit Aktivitas -->
        <div x-show="editActivityModal" style="display: none;" class="fixed inset-0 z-[99] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="editActivityModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" @click="editActivityModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="editActivityModal" x-transition.scale class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 p-8 space-y-6">
                    <h3 class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4 flex items-center gap-3">
                        <svg class="h-6 w-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                        Edit Aktivitas Pembelajaran
                    </h3>
                    <form :action="`/activities/${activeActivity.id}`" method="POST" class="space-y-4">
                        @csrf
                        @method('PUT')
                        <div>
                            <label for="edit_activity_title" class="block text-sm font-bold text-gray-700 mb-2">Nama Aktivitas</label>
                            <input type="text" name="title" id="edit_activity_title" x-model="activeActivity.title" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" required>
                        </div>
                        <div>
                            <label for="edit_activity_desc" class="block text-sm font-bold text-gray-700 mb-2">Deskripsi Singkat</label>
                            <textarea name="description" id="edit_activity_desc" x-model="activeActivity.description" rows="3" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors"></textarea>
                        </div>
                        <div>
                            <label for="edit_activity_order" class="block text-sm font-bold text-gray-700 mb-2">Nomor Urut (Order Number)</label>
                            <input type="number" name="order_number" id="edit_activity_order" x-model="activeActivity.order_number" min="1" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" required>
                        </div>
                        <div class="flex items-center gap-2">
                            <input type="checkbox" name="is_required" id="edit_activity_required" value="1" x-model="activeActivity.is_required" class="rounded border-gray-300 text-indigo-600 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200">
                            <label for="edit_activity_required" class="text-sm font-bold text-gray-700">Wajib Selesai untuk Melanjutkan</label>
                        </div>
                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-100">
                            <button type="button" @click="editActivityModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors text-sm">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-colors text-sm">
                                Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Buat Forum Diskusi -->
        <div x-show="showDiscussionModal" style="display: none;" class="fixed inset-0 z-[99] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showDiscussionModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" @click="showDiscussionModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showDiscussionModal" x-transition.scale class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 p-8 space-y-6">
                    <h3 class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4 flex items-center gap-3">
                        <svg class="h-6 w-6 text-indigo-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z" /></svg>
                        Buat Forum Diskusi Baru
                    </h3>
                    <form action="{{ route('discussions.store', $course) }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="module_id" :value="activeModule.id">
                        <div>
                            <label for="new_discussion_title" class="block text-sm font-bold text-gray-700 mb-2">Topik / Judul Diskusi</label>
                            <input type="text" name="title" id="new_discussion_title" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" placeholder="Cth: Diskusi Pengenalan AI" required>
                        </div>
                        <div>
                            <label for="new_discussion_content" class="block text-sm font-bold text-gray-700 mb-2">Deskripsi / Pertanyaan Pemantik</label>
                            <textarea name="content" id="new_discussion_content" rows="4" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" placeholder="Tulis petunjuk diskusi..." required></textarea>
                        </div>
                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-150">
                            <button type="button" @click="showDiscussionModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors text-sm">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-colors text-sm">
                                Buat Diskusi
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Upload Video Pembelajaran -->
        <div x-show="showVideoUploadModal" style="display: none;" class="fixed inset-0 z-[99] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div x-show="showVideoUploadModal" x-transition.opacity class="fixed inset-0 bg-gray-900 bg-opacity-50 backdrop-blur-sm transition-opacity" @click="showVideoUploadModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div x-show="showVideoUploadModal" x-transition.scale class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100 p-8 space-y-6">
                    <h3 class="text-xl font-bold text-gray-800 border-b border-gray-100 pb-4 flex items-center gap-3">
                        <svg class="h-6 w-6 text-teal-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" /></svg>
                        Upload Video Pembelajaran Baru
                    </h3>
                    <form :action="`/modules/${activeModule.id}/videos`" method="POST" enctype="multipart/form-data" class="space-y-4">
                        @csrf
                        <div>
                            <label for="new_video_title" class="block text-sm font-bold text-gray-700 mb-2">Judul Video</label>
                            <input type="text" name="title" id="new_video_title" class="w-full rounded-xl border-gray-300 shadow-sm focus:border-indigo-500 focus:ring focus:ring-indigo-200 transition-colors" placeholder="Cth: Penjelasan Singkat Convolutional Neural Network" required>
                        </div>
                        <div>
                            <label for="new_video_file" class="block text-sm font-bold text-gray-700 mb-2">Pilih File Video (.mp4, .mov, .webm — Maks 500MB)</label>
                            <input type="file" name="video_file" id="new_video_file" accept="video/mp4,video/mov,video/webm" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100" required>
                        </div>
                        <div class="flex items-center justify-end space-x-3 pt-4 border-t border-gray-150">
                            <button type="button" @click="showVideoUploadModal = false" class="px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-bold rounded-xl transition-colors text-sm">
                                Batal
                            </button>
                            <button type="submit" class="px-5 py-2 bg-indigo-600 hover:bg-indigo-700 text-white font-bold rounded-xl shadow-md transition-colors text-sm">
                                Upload Video
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
