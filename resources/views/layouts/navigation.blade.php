<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Logo -->
                <div class="shrink-0 flex items-center">
                    <a href="{{ route('dashboard') }}">
                        <x-application-logo class="block h-9 w-auto fill-current text-gray-800" />
                    </a>
                </div>

                <!-- Navigation Links -->
                <div class="hidden space-x-8 sm:-my-px sm:ms-10 sm:flex">
                    <x-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                        {{ __('Dashboard') }}
                    </x-nav-link>

                    @role('admin')
                        <x-nav-link href="#">User Management</x-nav-link>
                        <x-nav-link :href="route('admin.courses.index')" :active="request()->routeIs('admin.courses.*')">Courses</x-nav-link>
                        <x-nav-link href="#">Repository</x-nav-link>
                        <x-nav-link href="#">Announcement</x-nav-link>
                        <x-nav-link :href="route('analytics.index')" :active="request()->routeIs('analytics.*')">Reports</x-nav-link>
                        <x-nav-link href="#">Settings</x-nav-link>
                    @endrole

                    @role('teacher|guru')
                        <x-nav-link :href="route('courses.index')" :active="request()->routeIs('courses.*')">My Classes</x-nav-link>
                        <x-nav-link :href="route('materials.index')" :active="request()->routeIs('materials.*')">Materials</x-nav-link>
                        <x-nav-link :href="route('assignments.index')" :active="request()->routeIs('assignments.*')">Assignments</x-nav-link>
                        <x-nav-link :href="route('quizzes.index')" :active="request()->routeIs('quizzes.*')">Quiz</x-nav-link>
                        <x-nav-link href="#">Discussions</x-nav-link>
                        <x-nav-link :href="route('analytics.index')" :active="request()->routeIs('analytics.*')">Analytics</x-nav-link>
                        <x-nav-link href="#">Grades</x-nav-link>
                    @endrole

                    @role('siswa')
                        <x-nav-link :href="route('student.courses')" :active="request()->routeIs('student.courses*')">My Classes</x-nav-link>
                        <x-nav-link :href="route('student.todos')" :active="request()->routeIs('student.todos')">To-Do List</x-nav-link>
                    @endrole
                </div>
            </div>

            <div class="hidden sm:flex sm:items-center sm:ms-6 gap-4">
                <!-- Notifications Dropdown -->
                <x-dropdown align="right" width="80">
                    <x-slot name="trigger">
                        <button class="relative p-2 text-gray-500 hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                            @if(auth()->user()->unreadNotifications->count() > 0)
                            <span class="absolute top-0 right-0 inline-flex items-center justify-center px-2 py-1 text-xs font-bold leading-none text-white transform translate-x-1/4 -translate-y-1/4 bg-red-600 rounded-full">{{ auth()->user()->unreadNotifications->count() }}</span>
                            @endif
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <div class="px-4 py-3 border-b border-gray-100 flex justify-between items-center bg-gray-50 rounded-t-md">
                            <h3 class="text-sm font-bold text-gray-800">Notifikasi</h3>
                            <form action="{{ route('notifications.markAllRead') }}" method="POST">
                                @csrf
                                <button type="submit" class="text-xs font-semibold text-indigo-600 hover:text-indigo-800">Tandai semua dibaca</button>
                            </form>
                        </div>
                        <div class="max-h-96 overflow-y-auto">
                            @forelse(auth()->user()->unreadNotifications as $notification)
                                <a href="{{ $notification->data['url'] ?? '#' }}" class="block px-4 py-3 border-b border-gray-50 hover:bg-gray-50 transition-colors">
                                    <div class="flex items-start gap-3">
                                        <div class="h-8 w-8 rounded-full bg-{{ $notification->data['color'] ?? 'indigo' }}-100 text-{{ $notification->data['color'] ?? 'indigo' }}-600 flex items-center justify-center flex-shrink-0 mt-0.5">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="{{ $notification->data['icon'] ?? 'M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z' }}"></path></svg>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-gray-800">{{ $notification->data['title'] ?? 'Notifikasi Baru' }}</p>
                                            <p class="text-xs text-gray-600 mt-0.5 line-clamp-2">{{ $notification->data['message'] ?? '' }}</p>
                                            <p class="text-xs text-gray-400 mt-1">{{ $notification->created_at->diffForHumans() }}</p>
                                        </div>
                                    </div>
                                </a>
                            @empty
                                <div class="px-4 py-6 text-center text-sm text-gray-500">
                                    Tidak ada notifikasi baru.
                                </div>
                            @endforelse
                        </div>
                    </x-slot>
                </x-dropdown>

            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
