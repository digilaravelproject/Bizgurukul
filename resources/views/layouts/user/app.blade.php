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

<body class="font-sans antialiased bg-navy text-mainText h-screen flex flex-col overflow-hidden"
    x-data="{ sidebarOpen: false }">

    {{-- Impersonation Banner --}}
    @if(session('impersonator_id'))
        <div class="relative z-[999] text-white py-2 px-4 md:px-6 flex items-center justify-between shadow-[0_4px_12px_rgba(0,0,0,0.15)] shrink-0 border-b border-white/5"
            style="background-color: #b45309 !important; font-family: inherit;">
            <div class="flex items-center gap-2 md:gap-3">
                <div class="flex items-center justify-center w-7 h-7 rounded-full bg-white/15">
                    <i class="fas fa-user-shield text-[11px] text-white"></i>
                </div>
                <span class="text-[10px] md:text-[11px] font-black uppercase tracking-[0.12em] leading-none text-white">
                    <span class="hidden sm:inline opacity-90">Security Console:</span> Viewing as <span
                        class="bg-white/10 px-2 py-0.5 rounded ml-1">{{ Auth::user()->name }}</span>
                </span>
            </div>
            <form action="{{ route('stop.impersonating') }}" method="POST">
                @csrf
                <button type="submit"
                    class="bg-white px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-widest transition-all flex items-center gap-2 shadow-sm transform hover:scale-[1.03] active:scale-95 border border-white/20"
                    style="color: #b45309 !important;">
                    <i class="fas fa-power-off text-[10px]"></i>
                    <span class="hidden xs:inline">Stop Session</span>
                    <span class="xs:hidden">Stop</span>
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
