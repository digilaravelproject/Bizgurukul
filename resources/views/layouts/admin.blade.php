<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Skills Pehle') }} - Admin</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }

        /* Smooth transitions for all elements */
        * { transition: background-color 0.2s ease, border-color 0.2s ease; }
    </style>
    @stack('styles')
</head>

<body class="antialiased bg-navy text-mainText">

    <div class="min-h-screen flex overflow-hidden">

        @include('layouts.partials.sidebar')

        <div class="flex-1 flex flex-col min-w-0 bg-navy relative">

            {{-- Header --}}
            <header class="bg-customWhite/80 backdrop-blur-md border-b border-primary/5 h-20 flex items-center px-4 md:px-8 justify-between sticky top-0 z-40">

                <div class="flex items-center">
                    <button @click="sidebarOpen = true" class="md:hidden mr-4 p-2 bg-primary/5 text-primary rounded-xl">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                    </button>

                    <div class="hidden md:block">
                        <h2 class="text-xl font-extrabold text-mainText tracking-tight">
                            Welcome, <span class="text-primary">{{ Auth::user()->name ?? 'Admin' }}</span>
                        </h2>
                        <p class="text-[10px] text-mutedText uppercase tracking-[0.2em] font-bold">Skills Pehle Management</p>
                    </div>
                </div>

                <div class="flex items-center space-x-4">
                    {{-- Notification --}}
                    <button class="p-2.5 text-mutedText hover:text-primary rounded-xl bg-navy border border-transparent hover:border-primary/10 transition relative">
                        <span class="absolute top-2 right-2 w-2 h-2 bg-secondary rounded-full border-2 border-customWhite"></span>
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"></path></svg>
                    </button>

                    <div class="h-8 w-[1px] bg-primary/10 hidden md:block"></div>

                    {{-- Profile Dropdown --}}
                    <div class="relative" x-data="{ profileOpen: false }">
                        <button @click="profileOpen = !profileOpen" @click.away="profileOpen = false" class="flex items-center space-x-3 p-1 rounded-full hover:bg-navy transition pr-4">
                            <div class="h-9 w-9 rounded-lg brand-gradient flex items-center justify-center text-white font-bold shadow-lg shadow-primary/20 text-xs">
                                {{ substr(Auth::user()->name ?? 'AD', 0, 2) }}
                            </div>
                            <div class="hidden md:block text-left leading-tight">
                                <p class="text-xs font-bold text-mainText">{{ Auth::user()->name ?? 'Administrator' }}</p>
                                <p class="text-[10px] text-mutedText uppercase font-bold tracking-tighter">Profile</p>
                            </div>
                        </button>

                        <div x-show="profileOpen" x-cloak x-transition class="absolute right-0 mt-3 w-56 bg-customWhite border border-primary/10 shadow-xl rounded-2xl py-2 z-50">
                            <div class="px-4 py-3 border-b border-navy">
                                <p class="text-xs font-bold text-mainText">{{ Auth::user()->email ?? 'admin@skillspehle.com' }}</p>
                            </div>
                            <a href="#" class="block px-4 py-2 text-xs text-mutedText hover:text-primary hover:bg-navy font-bold">Settings</a>
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit" class="w-full text-left px-4 py-2 text-xs text-secondary hover:bg-secondary/5 font-bold">Sign Out</button>
                            </form>
                        </div>
                    </div>
                </div>
            </header>

            {{-- Main Content --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-8 bg-navy">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>

            {{-- Footer --}}
            <footer class="bg-customWhite border-t border-primary/5 py-4 px-8 text-mutedText text-[10px] font-bold uppercase tracking-widest flex flex-col md:flex-row justify-between items-center">
                <div>&copy; {{ date('Y') }} Skills Pehle</div>
                <div class="flex space-x-6 mt-2 md:mt-0">
                    <a href="#" class="hover:text-primary transition-colors">Support</a>
                    <a href="#" class="hover:text-primary transition-colors">Privacy</a>
                </div>
            </footer>
        </div>
    </div>

    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak class="fixed inset-0 bg-mainText/40 z-40 md:hidden backdrop-blur-sm transition-opacity"></div>

    @stack('scripts')
</body>
</html>
