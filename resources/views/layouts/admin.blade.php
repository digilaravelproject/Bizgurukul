<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Bizgurukul') }} - Admin Panel</title>

    {{-- Assets --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }

        /* Active Link Styling */
        .sidebar-link-active {
            background: linear-gradient(90deg, rgba(99, 102, 241, 0.1) 0%, rgba(99, 102, 241, 0) 100%);
            border-left: 3px solid var(--color-primary);
            color: var(--color-primary) !important;
        }

        /* Premium Texture Background */
        .bg-premium-texture {
            background-color: var(--color-bg-body);
            background-image: radial-gradient(at 0% 0%, rgba(99, 102, 241, 0.05) 0px, transparent 50%),
                              radial-gradient(at 100% 100%, rgba(251, 113, 133, 0.05) 0px, transparent 50%);
        }
    </style>
    @stack('styles')
</head>

<body class="font-sans antialiased bg-premium-texture text-mainText" x-data="{ sidebarOpen: false }">

    <div class="min-h-screen flex overflow-hidden">

        {{-- INCLUDE SIDEBAR --}}
        @include('layouts.partials.sidebar')

        {{-- MAIN CONTENT WRAPPER --}}
        <div class="flex-1 flex flex-col min-w-0 bg-navy/95 backdrop-blur-sm relative transition-all duration-300">

            {{-- Header --}}
            <header class="bg-navy/60 backdrop-blur-xl border-b border-white/5 h-20 flex items-center px-4 md:px-8 justify-between sticky top-0 z-40">

                {{-- Left: Mobile Toggle & Welcome Msg --}}
                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="md:hidden mr-4 text-white p-2 bg-white/5 rounded-xl hover:bg-white/10 transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>

                    <div class="hidden md:block">
                        <h2 class="text-xl font-bold text-white tracking-tight">
                            Welcome Back, <span class="text-primary">{{ Auth::user()->name ?? 'Admin' }}</span>
                        </h2>
                        <p class="text-[10px] text-mutedText uppercase tracking-[0.2em] font-medium">Bizgurukul Management System</p>
                    </div>
                </div>

                {{-- Right: Actions & Profile --}}
                <div class="flex items-center space-x-4 md:space-x-6">

                    {{-- Notification --}}
                    <button class="p-2.5 text-mutedText hover:text-primary rounded-xl bg-white/5 hover:bg-primary/5 transition relative group border border-transparent hover:border-primary/10">
                        <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-secondary rounded-full border-2 border-navy group-hover:animate-ping"></span>
                        <span class="absolute top-2.5 right-2.5 w-2 h-2 bg-secondary rounded-full border-2 border-navy"></span>
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </button>

                    <div class="h-8 w-[1px] bg-white/10 hidden md:block"></div>

                    {{-- Dynamic Profile Dropdown --}}
                    <div class="relative" x-data="{ profileOpen: false }">
                        <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false" class="flex items-center space-x-3 p-1 rounded-full hover:bg-white/5 transition border border-transparent hover:border-white/10 pr-4">
                            <div class="h-9 w-9 rounded-full bg-gradient-to-tr from-primary to-secondary flex items-center justify-center text-white font-bold shadow-lg shadow-primary/20 text-sm">
                                {{ substr(Auth::user()->name ?? 'AD', 0, 2) }}
                            </div>
                            <div class="hidden md:block text-left">
                                <p class="text-xs font-bold text-white">{{ Auth::user()->name ?? 'Administrator' }}</p>
                                <p class="text-[10px] text-mutedText">View Profile</p>
                            </div>
                            <svg class="w-4 h-4 text-mutedText transition-transform duration-300" :class="profileOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </button>

                        {{-- Dropdown Content --}}
                        <div x-show="profileOpen" x-cloak
                             x-transition:enter="transition ease-out duration-200"
                             x-transition:enter-start="transform opacity-0 scale-95 translate-y-2"
                             x-transition:enter-end="transform opacity-100 scale-100 translate-y-0"
                             class="absolute right-0 mt-4 w-64 origin-top-right rounded-3xl bg-[#1E293B] border border-white/10 shadow-[0_20px_60px_-10px_rgba(0,0,0,0.6)] py-2 z-50 overflow-hidden ring-1 ring-white/5">

                            <div class="px-6 py-5 bg-gradient-to-br from-white/5 to-transparent border-b border-white/5">
                                <p class="text-sm text-white font-bold">{{ Auth::user()->name ?? 'Admin' }}</p>
                                <p class="text-[11px] text-mutedText truncate">{{ Auth::user()->email ?? 'admin@bizgurukul.com' }}</p>
                                <div class="mt-2 inline-flex items-center px-2 py-0.5 rounded-md bg-primary/10 text-primary border border-primary/20 text-[10px] font-bold uppercase tracking-wider">
                                    {{ Auth::user()->role ?? 'Super Admin' }}
                                </div>
                            </div>

                            <div class="p-2 space-y-1">
                                <a href="#" class="flex items-center px-4 py-2.5 text-xs font-medium text-mutedText hover:text-white hover:bg-white/5 rounded-xl transition">
                                    <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Account Settings
                                </a>
                                <form method="POST" action="{{ route('logout') }}">
                                    @csrf
                                    <button type="submit" class="w-full flex items-center px-4 py-2.5 text-xs font-medium text-secondary hover:bg-secondary/10 rounded-xl transition">
                                        <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                                        Sign Out
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Page Content --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-8">
                <div class="max-w-7xl mx-auto animate-[fadeIn_0.5s_ease-out]">
                    @yield('content')
                </div>
            </main>

            {{-- Footer --}}
            <footer class="bg-navy/50 border-t border-white/5 py-6 px-8 text-mutedText text-xs flex flex-col md:flex-row justify-between items-center mt-auto">
                <div class="font-medium text-center md:text-left">&copy; {{ date('Y') }} <span class="text-white">Bizgurukul</span>. Crafted for Excellence.</div>
                <div class="flex space-x-6 mt-4 md:mt-0">
                    <a href="#" class="hover:text-primary transition-colors">Privacy Policy</a>
                    <a href="#" class="hover:text-primary transition-colors">Support Center</a>
                </div>
            </footer>
        </div>
    </div>

    {{-- Mobile Sidebar Overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 bg-black/80 z-40 md:hidden backdrop-blur-md transition-opacity"></div>

    @stack('scripts')
</body>
</html>
