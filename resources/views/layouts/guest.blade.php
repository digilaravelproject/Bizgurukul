<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Skills Pehle') }}</title>

    <!-- Alpine.js -->
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    <!-- Scripts & Styles -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])

    <style>
        [x-cloak] {
            display: none !important;
        }
    </style>
</head>

<body class="font-sans text-mainText antialiased bg-navy selection:bg-primary selection:text-white">

    <div class="min-h-screen w-full flex">

        <!-- Left Side: Branding / Image (Hidden on Mobile, Visible on Desktop) -->
        <div class="hidden lg:flex w-1/2 bg-navy relative overflow-hidden items-center justify-center p-12">
            <!-- Abstract Background Decorations using Brand Colors -->
            <div class="absolute top-0 left-0 w-full h-full opacity-10">
                <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-primary rounded-full blur-3xl"></div>
                <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-secondary rounded-full blur-3xl"></div>
            </div>

            <div class="relative z-10 text-center">
                <!-- CSS-Only Logo Representation (Matches your image) -->
                {{-- <div class="inline-flex items-center bg-white border-2 border-primary p-1 shadow-2xl transform hover:scale-105 transition duration-500">
                    <div class="bg-gradient-to-r from-primary to-secondary text-white px-6 py-3 font-extrabold text-4xl tracking-tight">
                        SKILLS
                    </div>
                    <div class="px-6 py-3 bg-white text-secondary font-extrabold text-4xl tracking-tight flex items-center relative">
                        P<span class="text-primary">₹</span>HLE
                        <!-- Arrow Graphic -->
                        <div class="absolute bottom-2 left-6 right-4 h-0.5 bg-secondary"></div>
                        <svg class="absolute bottom-[5px] right-4 w-3 h-3 text-secondary fill-current" viewBox="0 0 24 24"><path d="M24 12l-12-8v16l12-8z"/></svg>
                    </div>
                </div> --}}
                @if (file_exists(public_path('storage/site_images/logo1.png')))
                    {{-- 1. Image agar file system mein exist karti hai --}}
                    <img src="{{ asset('storage/site_images/logo1.png') }}" alt="Logo"
                        class="h-[100px] w-auto transform hover:scale-105 transition duration-500 object-contain">
                @else
                    {{-- 2. Agar image nahi hai toh ye CSS wala logo dikhega --}}
                    <div
                        class="inline-flex items-center bg-white border-2 border-primary p-1 shadow-2xl transform hover:scale-105 transition duration-500">
                        <div
                            class="bg-gradient-to-r from-primary to-secondary text-white px-6 py-3 font-extrabold text-4xl tracking-tight">
                            SKILLS
                        </div>
                        <div
                            class="px-6 py-3 bg-white text-secondary font-extrabold text-4xl tracking-tight flex items-center relative">
                            P<span class="text-primary">₹</span>HLE
                            <div class="absolute bottom-2 left-6 right-4 h-0.5 bg-secondary"></div>
                            <svg class="absolute bottom-[5px] right-4 w-3 h-3 text-secondary fill-current"
                                viewBox="0 0 24 24">
                                <path d="M24 12l-12-8v16l12-8z" />
                            </svg>
                        </div>
                    </div>
                @endif

                <div class="mt-12 space-y-4">
                    <h2 class="text-3xl font-bold text-mainText">Master New Skills</h2>
                    <p class="text-mutedText text-lg max-w-md mx-auto">Join the community of learners and earners.
                        Upgrade your future today.</p>
                </div>
            </div>
        </div>

        <!-- Right Side: Form Area -->
        <div
            class="w-full lg:w-1/2 bg-surface flex flex-col justify-center items-center p-6 sm:p-12 lg:p-8 shadow-2xl lg:shadow-none border-l border-slate-100">
            <div class="w-full max-w-md space-y-8">
                <!-- Mobile Logo (Visible only on small screens) -->
                {{-- <div class="lg:hidden flex justify-center mb-8">
                    <div class="inline-flex items-center border border-primary p-0.5 shadow-md scale-75 origin-center">
                        <div class="bg-gradient-to-r from-primary to-secondary text-white px-3 py-1 font-bold text-xl">
                            SKILLS
                        </div>
                        <div class="px-3 py-1 bg-white text-secondary font-bold text-xl">
                            P<span class="text-primary">₹</span>HLE
                        </div>
                    </div>
                </div> --}}
                @if (file_exists(public_path('storage/site_images/logo1.png')))
                    <div class="lg:hidden flex justify-center mb-8">
                        <img src="{{ asset('storage/site_images/logo1.png') }}" alt="Logo"
                            class="h-[100px] w-auto transform hover:scale-105 transition duration-500 object-contain">
                    </div>
                @else
                    <div class="lg:hidden flex justify-center mb-8">
                        <div
                            class="inline-flex items-center border border-primary p-0.5 shadow-md scale-75 origin-center">
                            <div
                                class="bg-gradient-to-r from-primary to-secondary text-white px-3 py-1 font-bold text-xl">
                                SKILLS
                            </div>
                            <div class="px-3 py-1 bg-white text-secondary font-bold text-xl">
                                P<span class="text-primary">₹</span>HLE
                            </div>
                        </div>
                    </div>
                @endif

                <!-- Slot Content -->
                {{ $slot }}

            </div>

            <div class="mt-8 text-center">
                <p class="text-xs text-mutedText">
                    &copy; {{ date('Y') }} Skills Pehle. All rights reserved.
                </p>
            </div>
        </div>

    </div>
</body>

</html>
