<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <style>
            body { font-family: 'Outfit', sans-serif; }
            .glassmorphism {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(10px);
                border: 1px solid rgba(255, 255, 255, 0.3);
            }
        </style>
    </head>
    <body x-data="{ sidebarOpen: false, profileOpen: false }" class="bg-[#f8fafc] text-gray-800 overflow-hidden h-screen flex relative">
        
        <!-- Mobile Sidebar Overlay overlay -->
        <div x-show="sidebarOpen" 
             style="display: none;"
             x-transition.opacity duration.300ms
             @click="sidebarOpen = false" 
             class="fixed inset-0 z-20 bg-gray-900/50 backdrop-blur-sm lg:hidden">
        </div>

        <!-- Sidebar Navigation -->
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'" 
               class="fixed inset-y-0 left-0 z-30 w-64 bg-white shadow-xl h-full flex flex-col transition-transform duration-300 lg:relative lg:translate-x-0 flex-shrink-0">
            <div class="h-16 flex items-center justify-between px-6 border-b border-gray-100">
                <h1 class="text-2xl font-black bg-clip-text text-transparent bg-gradient-to-r from-blue-600 to-indigo-600">LMS Edu</h1>
                <button @click="sidebarOpen = false" class="lg:hidden text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                </button>
            </div>
            
            <nav class="flex-1 overflow-y-auto py-4 space-y-1 scrollbar-hide">
                <div class="px-6 py-2 text-xs font-bold text-gray-400 uppercase tracking-wider mb-2">Menu Utama</div>
                
                <a href="{{ route('dashboard') }}" class="flex items-center space-x-3 px-6 py-3 {{ request()->routeIs('dashboard') ? 'text-indigo-600 bg-indigo-50 border-r-4 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }} transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg>
                    <span class="font-medium">Dashboard</span>
                </a>
                
                @role('admin')
                <a href="{{ route('users.index') }}" class="flex items-center space-x-3 px-6 py-3 {{ request()->routeIs('users.*') ? 'text-indigo-600 bg-indigo-50 border-r-4 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }} transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                    <span class="font-medium">User Management</span>
                </a>
                <a href="{{ route('courses.index') }}" class="flex items-center space-x-3 px-6 py-3 {{ request()->routeIs('courses.*') ? 'text-indigo-600 bg-indigo-50 border-r-4 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }} transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                    <span class="font-medium">Course Management</span>
                </a>
                @endrole

                @role('guru')
                <a href="{{ route('materials.index') }}" class="flex items-center space-x-3 px-6 py-3 {{ request()->routeIs('materials.*') ? 'text-indigo-600 bg-indigo-50 border-r-4 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }} transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span class="font-medium">My Classes & Materials</span>
                </a>
                <a href="{{ route('assignments.index') }}" class="flex items-center space-x-3 px-6 py-3 {{ request()->routeIs('assignments.*') ? 'text-indigo-600 bg-indigo-50 border-r-4 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }} transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span class="font-medium">Assignments</span>
                </a>
                <a href="{{ route('quizzes.index') }}" class="flex items-center space-x-3 px-6 py-3 {{ request()->routeIs('quizzes.*') ? 'text-indigo-600 bg-indigo-50 border-r-4 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }} transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span class="font-medium">Exams & Quizzes</span>
                </a>
                @endrole

                @role('siswa')
                <a href="{{ route('student.courses') }}" class="flex items-center space-x-3 px-6 py-3 {{ request()->routeIs('student.courses*') ? 'text-indigo-600 bg-indigo-50 border-r-4 border-indigo-600' : 'text-gray-600 hover:bg-gray-50 hover:text-indigo-600' }} transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/></svg>
                    <span class="font-medium">Mata Pelajaran (AI & Coding)</span>
                </a>
                <a href="{{ route('student.courses') }}" class="flex items-center space-x-3 px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-indigo-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"/></svg>
                    <span class="font-medium">Tugas & Kuis (Dalam Modul)</span>
                </a>
                <a href="#" class="flex items-center space-x-3 px-6 py-3 text-gray-600 hover:bg-gray-50 hover:text-indigo-600 transition-colors">
                    <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                    <span class="font-medium">Forum Diskusi</span>
                </a>
                @endrole
            </nav>
            
            <div class="p-4 border-t border-gray-100 hidden lg:block">
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="flex items-center w-full space-x-3 px-4 py-2 text-red-500 hover:bg-red-50 rounded-lg transition-colors font-medium">
                        <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                        <span>Keluar Sistem</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Main Content Area -->
        <div class="flex-1 flex flex-col h-screen overflow-hidden relative w-full lg:w-[calc(100%-16rem)] max-w-full">
            
            <!-- Navbar -->
            <header class="h-16 glassmorphism border-b border-gray-200 flex items-center justify-between px-4 sm:px-6 z-10 sticky top-0 w-full">
                <div class="flex items-center gap-3">
                    <button @click="sidebarOpen = true" class="lg:hidden text-gray-500 hover:text-indigo-600 focus:outline-none p-1 bg-white rounded-md shadow-sm border border-gray-200">
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>

                    @if (isset($header))
                        <div class="text-xl font-bold tracking-tight text-gray-800 line-clamp-1">
                            {{ $header }}
                        </div>
                    @endif
                </div>
                
                <div class="flex items-center space-x-3 sm:space-x-5">
                    <!-- Gamification Points (Siswa only) -->
                    @role('siswa')
                    <div class="hidden sm:flex items-center space-x-1.5 px-3 py-1.5 bg-yellow-50 border border-yellow-200 rounded-full text-yellow-600 font-bold text-sm shadow-sm cursor-pointer hover:bg-yellow-100 transition-colors">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 2a8 8 0 100 16 8 8 0 000-16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" /></svg>
                        <span>{{ Auth::user()->points ?? 0 }} XP</span>
                    </div>
                    @endrole

                    <!-- Notifications -->
                    <button class="text-gray-400 hover:text-indigo-600 relative transition-transform hover:scale-105 focus:outline-none">
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"/></svg>
                        <span class="absolute top-0 right-1 h-2 w-2 rounded-full bg-red-500 border-2 border-white"></span>
                    </button>
                    
                    <!-- Profile Interactive Dropdown -->
                    <div class="relative pl-3 sm:pl-5 border-l border-gray-200">
                        <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false" class="flex items-center space-x-2 focus:outline-none focus:ring-2 focus:ring-indigo-500 rounded-full p-1 transition-colors hover:bg-gray-100 group">
                            <div class="text-right hidden sm:block mr-1">
                                <div class="text-sm font-bold text-gray-800">{{ Auth::user()->name }}</div>
                                <div class="text-xs text-indigo-500 font-bold capitalize">{{ Auth::user()->roles->pluck('name')->first() ?? 'Siswa' }}</div>
                            </div>
                            <img class="h-9 w-9 rounded-full border-2 border-indigo-200 p-0.5 object-cover shadow-sm group-hover:border-indigo-400 transition-colors" src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->name) }}&background=4f46e5&color=fff" alt="Avatar">
                            <svg class="w-4 h-4 text-gray-400 group-hover:text-indigo-500 transition-colors hidden sm:block" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
                        </button>

                        <!-- Dropdown Menu -->
                        <div x-show="profileOpen" 
                             style="display: none;"
                             x-transition:enter="transition ease-out duration-100"
                             x-transition:enter-start="transform opacity-0 scale-95"
                             x-transition:enter-end="transform opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-75"
                             x-transition:leave-start="transform opacity-100 scale-100"
                             x-transition:leave-end="transform opacity-0 scale-95"
                             class="absolute right-0 mt-2 w-48 bg-white rounded-xl shadow-lg border border-gray-100 py-1 z-50 overflow-hidden">
                            <div class="px-4 py-3 border-b border-gray-100 sm:hidden">
                                <p class="text-sm font-bold text-gray-900 truncate">{{ Auth::user()->name }}</p>
                                <p class="text-xs font-semibold text-indigo-600 truncate capitalize">{{ Auth::user()->roles->pluck('name')->first() ?? 'Siswa' }}</p>
                            </div>
                            <!-- Mobile gamification -->
                            @role('siswa')
                            <div class="px-4 py-2 border-b border-gray-100 sm:hidden">
                                <span class="text-xs font-bold text-yellow-600 bg-yellow-50 px-2 py-1 rounded-full border border-yellow-200">{{ Auth::user()->points ?? 0 }} XP Tersimpan</span>
                            </div>
                            @endrole
                            <a href="{{ route('profile.edit') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition-colors font-medium">Profil Saya</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="block w-full text-left px-4 py-2 text-sm text-red-600 hover:bg-red-50 hover:text-red-700 transition-colors font-medium">
                                    Keluar Sistem
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <main class="flex-1 overflow-x-hidden overflow-y-auto w-full">
                <div class="p-4 sm:p-6 lg:p-8 w-full max-w-full lg:max-w-7xl mx-auto min-h-full">
                    {{ $slot }}
                </div>
            </main>
        </div>
    </body>
</html>
