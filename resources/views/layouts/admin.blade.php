<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bizgurukul') }} - Admin</title>

    {{-- CSS Frameworks --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        .no-scrollbar::-webkit-scrollbar {
            display: none;
        }
    </style>
    @stack('styles')
</head>

<body class="font-sans antialiased bg-slate-50 text-slate-900" x-data="{ sidebarOpen: false }">
    <div class="min-h-screen flex overflow-hidden">

        {{-- 1. SIDEBAR SECTION --}}
        <aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
            class="fixed inset-y-0 left-0 z-50 w-64 bg-slate-900 text-white transform transition-transform duration-300 ease-in-out md:relative md:translate-x-0 flex flex-col shadow-2xl">

            {{-- Sidebar Logo --}}
            <div class="p-6 text-xl font-bold border-b border-slate-800 flex justify-between items-center bg-slate-900">
                <div class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-indigo-500 rounded-lg flex items-center justify-center shadow-lg">
                        <span class="text-white text-lg font-black">B</span>
                    </div>
                    <span class="tracking-tight">Bizgurukul <span class="text-indigo-400">Pro</span></span>
                </div>
                <button @click="sidebarOpen = false" class="md:hidden text-slate-400 hover:text-white">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                        </path>
                    </svg>
                </button>
            </div>

            {{-- Sidebar Nav Menu --}}
            <nav class="flex-1 p-4 space-y-2 overflow-y-auto no-scrollbar" x-data="{ lmsOpen: {{ request()->routeIs('admin.courses.*') || request()->routeIs('admin.lessons.*') ? 'true' : 'false' }} }">
                <p class="text-[10px] font-bold text-slate-500 uppercase tracking-widest px-3 mb-2">Main Menu</p>

                {{-- Dashboard --}}
                <a href="{{ route('admin.dashboard') }}"
                    class="flex items-center p-3 rounded-xl transition-all duration-200 {{ request()->routeIs('admin.dashboard') ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-900/40' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                        </path>
                    </svg>
                    <span class="font-medium">Dashboard</span>
                </a>

                {{-- LMS Management Dropdown --}}
                <div>
                    <button @click="lmsOpen = !lmsOpen"
                        class="w-full flex items-center justify-between p-3 rounded-xl transition-all duration-200 group {{ request()->routeIs('admin.courses.*') || request()->routeIs('admin.lessons.*') ? 'bg-slate-800 text-white' : 'text-slate-400 hover:bg-slate-800 hover:text-white' }}">
                        <div class="flex items-center">
                            <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.courses.*') || request()->routeIs('admin.lessons.*') ? 'text-indigo-400' : 'group-hover:text-indigo-400' }}"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                </path>
                            </svg>
                            <span class="font-medium">LMS Management</span>
                        </div>
                        <svg class="w-4 h-4 transition-transform duration-200" :class="lmsOpen ? 'rotate-180' : ''"
                            fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                            </path>
                        </svg>
                    </button>

                    <div x-show="lmsOpen" x-cloak x-transition:enter="transition ease-out duration-200"
                        class="mt-2 ml-4 pl-4 border-l border-slate-800 space-y-1">

                        <a href="{{ route('admin.courses.index') }}"
                            class="block p-2 text-sm transition-colors rounded-lg {{ request()->routeIs('admin.courses.*') ? 'bg-indigo-500/10 text-indigo-400 font-bold' : 'text-slate-400 hover:text-white' }}">
                            All Courses
                        </a>

                        <a href="{{ route('admin.lessons.all') }}"
                            class="block p-2 text-sm transition-colors rounded-lg {{ request()->routeIs('admin.lessons.*') ? 'bg-indigo-500/10 text-indigo-400 font-bold' : 'text-slate-400 hover:text-white' }}">
                            Lessons (Video HLS)
                        </a>

                        <a href="#"
                            class="block p-2 text-sm text-slate-400 hover:text-white transition-colors">Certificates</a>
                    </div>
                </div>

                {{-- Students --}}
                <a href="#"
                    class="flex items-center p-3 rounded-xl text-slate-400 hover:bg-slate-800 hover:text-white transition group">
                    <svg class="w-5 h-5 mr-3 group-hover:text-indigo-400" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                        </path>
                    </svg>
                    <span class="font-medium">Students</span>
                </a>
            </nav>

            {{-- Sidebar User Footer --}}
            <div class="p-4 border-t border-slate-800 bg-slate-900/50">
                <div class="flex items-center justify-between">
                    <div class="flex items-center overflow-hidden">
                        <div
                            class="h-9 w-9 flex-shrink-0 rounded-lg bg-indigo-500 flex items-center justify-center text-white text-xs font-bold mr-3">
                            AD</div>
                        <div class="truncate">
                            <p class="text-sm font-bold text-white truncate">Admin</p>
                            <p class="text-[10px] text-green-500 font-medium tracking-wide uppercase">● Online</p>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="p-2 text-slate-500 hover:text-red-400 hover:bg-red-400/10 rounded-lg transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                        </button>
                    </form>
                </div>
            </div>
        </aside>

        {{-- 2. MAIN CONTENT AREA --}}
        <div class="flex-1 flex flex-col min-w-0 bg-slate-50 relative">

            {{-- Navbar / Header --}}
            <header
                class="bg-white/80 backdrop-blur-md border-b border-slate-200 h-16 flex items-center px-4 md:px-8 justify-between sticky top-0 z-40">
                <div class="flex items-center flex-1">
                    <button @click="sidebarOpen = true"
                        class="md:hidden mr-4 text-slate-600 p-2 hover:bg-slate-100 rounded-lg transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                    </button>
                    <div class="w-full">
                        <div class="flex items-center justify-between w-full px-4">
                            <h2 class="text-xl font-bold text-slate-800">Admin Control Panel</h2>
                        </div>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    <button
                        class="p-2 text-slate-400 hover:text-indigo-600 rounded-full hover:bg-slate-50 transition relative">
                        <span
                            class="absolute top-1 right-1 w-2 h-2 bg-red-500 rounded-full border-2 border-white"></span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9">
                            </path>
                        </svg>
                    </button>
                    <div class="h-8 w-[1px] bg-slate-200 hidden sm:block"></div>
                    <span
                        class="text-[10px] font-bold text-slate-400 hidden sm:block uppercase tracking-widest">v1.0.2</span>
                </div>
            </header>

            {{-- Content --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-8">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>

            {{-- Footer --}}
            <footer
                class="bg-white border-t border-slate-200 py-4 px-8 text-slate-500 text-xs flex flex-col md:flex-row justify-between items-center mt-auto">
                <div>&copy; {{ date('Y') }} <span class="font-bold text-slate-700">Bizgurukul</span> Management
                    System.</div>
                <div class="flex space-x-6">
                    <a href="#" class="hover:text-indigo-600 transition">Privacy Policy</a>
                    <a href="#" class="hover:text-indigo-600 transition">Help Desk</a>
                </div>
            </footer>
        </div>
    </div>

    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak
        class="fixed inset-0 bg-slate-900/60 z-40 md:hidden backdrop-blur-sm"></div>

    @stack('scripts')
</body>

</html>
