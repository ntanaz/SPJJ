<x-app-layout>
    <x-slot name="header">
        {{ __('Pusat Notifikasi Guru') }}
    </x-slot>

    @if(session('success'))
        <div class="mb-4 bg-green-50 text-green-700 p-4 rounded-xl border border-green-200">
            {{ session('success') }}
        </div>
    @endif

    <div class="bg-white rounded-2xl border border-gray-100 shadow-sm overflow-hidden">
        <div class="divide-y divide-gray-100">
            @forelse($notifications as $notification)
            <div class="p-4 flex items-start space-x-4 {{ is_null($notification->read_at) ? 'bg-indigo-50/50' : 'hover:bg-gray-50' }} transition-colors">
                <div class="flex-shrink-0 mt-1">
                    <div class="w-10 h-10 rounded-full {{ is_null($notification->read_at) ? 'bg-indigo-100 text-indigo-600' : 'bg-gray-100 text-gray-500' }} flex items-center justify-center">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                    </div>
                </div>
                <div class="flex-1">
                    <h4 class="text-sm font-bold {{ is_null($notification->read_at) ? 'text-gray-900' : 'text-gray-700' }}">{{ $notification->data['title'] ?? 'Notifikasi Baru' }}</h4>
                    <p class="text-sm text-gray-600 mt-0.5">{{ $notification->data['message'] ?? 'Ada pembaruan sistem yang membutuhkan perhatian Anda.' }}</p>
                    <div class="text-xs text-gray-400 mt-1 font-medium">{{ $notification->created_at->diffForHumans() }}</div>
                </div>
                <div>
                    @if(is_null($notification->read_at))
                    <form action="{{ route('teacher.notifications.read', $notification->id) }}" method="POST">
                        @csrf
                        <button type="submit" class="text-xs font-bold text-indigo-600 hover:text-indigo-800 bg-indigo-50 px-3 py-1.5 rounded-lg transition-colors">Tandai Dibaca</button>
                    </form>
                    @endif
                </div>
            </div>
            @empty
            <div class="p-8 text-center text-gray-500">
                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-3">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/></svg>
                </div>
                Belum ada notifikasi untuk Anda saat ini.
            </div>
            @endforelse
        </div>
        @if($notifications->hasPages())
        <div class="p-4 border-t border-gray-100">
            {{ $notifications->links() }}
        </div>
        @endif
    </div>
</x-app-layout>
