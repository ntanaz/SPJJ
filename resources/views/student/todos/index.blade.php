<x-app-layout>
    <x-slot name="header">
        <h2 class="font-bold text-2xl text-gray-800 tracking-tight">
            {{ __('To-Do List Saya') }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto">
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100 mb-8">
            <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 mb-8 border-b border-gray-100 pb-6">
                <div>
                    <h3 class="text-xl font-bold text-gray-800">Tugas & Kuis</h3>
                    <p class="text-sm text-gray-500 mt-1">Pantau semua aktivitas pembelajaran yang perlu Anda selesaikan.</p>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('student.todos', ['filter' => 'pending']) }}" class="px-4 py-2 text-sm font-bold rounded-xl {{ $filter === 'pending' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }} transition-colors">
                        Pending
                    </a>
                    <a href="{{ route('student.todos', ['filter' => 'completed']) }}" class="px-4 py-2 text-sm font-bold rounded-xl {{ $filter === 'completed' ? 'bg-emerald-600 text-white shadow-sm' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }} transition-colors">
                        Selesai
                    </a>
                    <a href="{{ route('student.todos', ['filter' => 'overdue']) }}" class="px-4 py-2 text-sm font-bold rounded-xl {{ $filter === 'overdue' ? 'bg-red-600 text-white shadow-sm' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }} transition-colors">
                        Terlewat
                    </a>
                    <a href="{{ route('student.todos', ['filter' => 'all']) }}" class="px-4 py-2 text-sm font-bold rounded-xl {{ $filter === 'all' ? 'bg-gray-800 text-white shadow-sm' : 'bg-gray-50 text-gray-600 hover:bg-gray-100' }} transition-colors">
                        Semua
                    </a>
                </div>
            </div>

            <div class="space-y-4">
                @forelse($todos as $todo)
                    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center p-5 rounded-2xl border transition-all hover:shadow-md
                        {{ $todo['status'] === 'completed' ? 'bg-gray-50 border-gray-200 opacity-75 hover:opacity-100' : 'bg-white border-gray-100' }}">
                        <div class="flex items-center gap-4 mb-4 sm:mb-0 w-full sm:w-auto">
                            <!-- Checkbox / Status Icon -->
                            <div class="flex-shrink-0">
                                @if($todo['status'] === 'completed')
                                    <div class="h-8 w-8 rounded-full bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                    </div>
                                @elseif($todo['status'] === 'overdue')
                                    <div class="h-8 w-8 rounded-full bg-red-100 text-red-600 flex items-center justify-center">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </div>
                                @else
                                    <div class="h-8 w-8 rounded-full border-2 border-gray-300 flex items-center justify-center"></div>
                                @endif
                            </div>
                            
                            <!-- Detail -->
                            <div>
                                <h4 class="font-bold text-gray-800 text-lg {{ $todo['status'] === 'completed' ? 'line-through text-gray-500' : '' }}">
                                    {{ $todo['title'] }}
                                </h4>
                                <div class="flex items-center gap-3 text-sm mt-1">
                                    <span class="inline-flex items-center gap-1 font-bold text-{{ $todo['color'] }}-600 bg-{{ $todo['color'] }}-50 px-2 py-0.5 rounded-lg">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $todo['icon'] }}"></path></svg>
                                        {{ ucfirst($todo['type']) }}
                                    </span>
                                    <span class="text-gray-500 font-medium">{{ $todo['course'] }}</span>
                                    
                                    @if($todo['deadline'])
                                        <span class="text-gray-400">&bull;</span>
                                        <span class="font-bold {{ $todo['status'] === 'overdue' ? 'text-red-500' : 'text-gray-500' }}">
                                            Tenggat: {{ \Carbon\Carbon::parse($todo['deadline'])->format('d M Y, H:i') }}
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="w-full sm:w-auto text-right">
                            <a href="{{ $todo['url'] }}" class="inline-block w-full sm:w-auto px-5 py-2 text-sm font-bold rounded-xl shadow-sm transition-colors
                                {{ $todo['status'] === 'completed' ? 'bg-white border border-gray-200 text-gray-600 hover:bg-gray-50' : 'bg-indigo-600 text-white hover:bg-indigo-700' }}">
                                {{ $todo['status'] === 'completed' ? 'Lihat Detail' : 'Kerjakan Sekarang' }}
                            </a>
                        </div>
                    </div>
                @empty
                    <div class="text-center py-12 bg-gray-50 rounded-2xl border border-gray-100 border-dashed">
                        <div class="inline-flex h-16 w-16 items-center justify-center rounded-full bg-emerald-100 text-emerald-500 mb-4">
                            <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h4 class="text-lg font-bold text-gray-800 mb-1">Bagus Sekali!</h4>
                        <p class="text-gray-500 font-medium">Tidak ada tugas dalam kategori ini.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</x-app-layout>
