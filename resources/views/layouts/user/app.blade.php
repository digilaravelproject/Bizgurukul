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

<body class="font-sans antialiased bg-navy text-mainText h-screen flex flex-col overflow-hidden" x-data="{ sidebarOpen: false }">

    {{-- Impersonation Banner --}}
    @if(session('impersonator_id'))
    <div class="relative z-[999] bg-gradient-to-r from-amber-600 via-orange-500 to-amber-600 text-white py-1.5 px-4 md:px-6 flex items-center justify-between shadow-lg shadow-amber-900/20 shrink-0 border-b border-white/10" style="font-family: inherit;">
        <div class="flex items-center gap-2 md:gap-3">
            <div class="flex items-center justify-center w-6 h-6 rounded-full bg-white/20 animate-pulse">
                <i class="fas fa-user-secret text-[10px]"></i>
            </div>
            <span class="text-[9px] md:text-[10px] font-black uppercase tracking-[0.15em] leading-none">
                <span class="hidden sm:inline opacity-80">System:</span> Viewing as <span class="text-white border-b border-white/30">{{ Auth::user()->name }}</span>
            </span>
        </div>
        <form action="{{ route('stop.impersonating') }}" method="POST">
            @csrf
            <button type="submit" class="bg-white/10 hover:bg-white/25 backdrop-blur-md text-white px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest transition-all flex items-center gap-2 border border-white/20 hover:scale-105 active:scale-95 shadow-sm">
                <i class="fas fa-sign-out-alt text-[10px]"></i>
                <span class="hidden xs:inline">Exit Session</span>
                <span class="xs:hidden">Exit</span>
            </button>
        </form>
    </div>
    @endif

    <div class="flex-1 flex overflow-hidden">
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
    </div>

    {{-- Mobile Overlay --}}
    <div x-show="sidebarOpen" @click="sidebarOpen = false" x-cloak
        class="fixed inset-0 bg-mainText/60 z-40 md:hidden backdrop-blur-sm"></div>

    @include('layouts.partials.global-toast')
    @stack('scripts')
</body>

</html>
