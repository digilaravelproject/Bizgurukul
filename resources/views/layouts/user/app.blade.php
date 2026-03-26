<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Skills Pehle') }} - Student</title>
    <link rel="icon" type="image/png" href="{{ Storage::url('site_images/logo.png') }}">
    {{-- CSS Frameworks --}}
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    {{-- Alpine.js --}}
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css"
        integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw=="
        crossorigin="anonymous" referrerpolicy="no-referrer" />

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

<body class="font-sans antialiased bg-navy text-mainText" x-data="{ sidebarOpen: false }">

    {{-- Impersonation Banner --}}
    @if(session('impersonator_id'))
    <div class="fixed top-0 left-0 right-0 z-[999] bg-gradient-to-r from-amber-500 to-orange-500 text-white py-2.5 px-6 flex items-center justify-between shadow-lg shadow-amber-500/30" style="font-family: inherit;">
        <div class="flex items-center gap-3">
            <svg class="w-5 h-5 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
            </svg>
            <span class="text-xs font-black uppercase tracking-widest">
                Viewing as <span class="underline underline-offset-2">{{ Auth::user()->name }}</span>
            </span>
        </div>
        <form action="{{ route('stop.impersonating') }}" method="POST">
            @csrf
            <button type="submit" class="bg-white/20 hover:bg-white/30 backdrop-blur-sm text-white px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest transition flex items-center gap-2 border border-white/20">
                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                </svg>
                Return to Admin
            </button>
        </form>
    </div>
    @endif
    <div class="h-screen flex overflow-hidden">
        {{-- 1. SIDEBAR SECTION --}}

        @include('layouts.user.sidebar')
        {{-- 2. MAIN CONTENT AREA --}}
        <div class="flex-1 flex flex-col min-w-0 bg-navy relative md:ml-64">

            {{-- Navbar / Header --}}
            @include('layouts.user.header')

            {{-- Content --}}
            <main class="flex-1 overflow-y-auto p-4 md:p-8">
                <div class="max-w-7xl mx-auto">
                    @yield('content')
                </div>
            </main>

            {{-- Footer --}}
            @include('layouts.user.footer')
        </div>
        {{-- </main> --}}
    </div>

    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak
        class="fixed inset-0 bg-mainText/60 z-40 md:hidden backdrop-blur-sm"></div>

    @include('layouts.partials.global-toast')
    @stack('scripts')
</body>

</html>
