<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>@yield('title', config('app.name', 'Skills Pehle'))</title>

    <link rel="icon" type="image/png" href="{{ asset('storage/site_images/logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

    @stack('styles')
</head>

<body class="font-sans antialiased text-mainText bg-navy flex flex-col min-h-screen">

    <nav class="fixed top-0 left-0 w-full z-50 bg-surface shadow-sm border-b border-gray-100"
        x-data="{ mobileMenuOpen: false }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-20 md:h-24">

                <div class="flex-shrink-0 flex items-center">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 group">
                        <div class="p-1.5 bg-primary/5 rounded-lg group-hover:bg-primary/10 transition-colors">
                            <img class="h-12 w-48 object-contain transform group-hover:scale-105 transition-transform duration-300"
                                src="{{ asset('storage/site_images/logo1.png') }}" alt="{{ config('app.name') }}"
                                onerror="this.src='https://ui-avatars.com/api/?name=SP&background=F7941D&color=fff'">
                        </div>
                    </a>
                </div>

                <div class="hidden md:flex md:items-center">
                    <div class="flex space-x-10 mr-10">
                        <a href="{{ route('home') }}"
                            class="text-mainText font-bold text-sm uppercase tracking-wide relative group hover:text-primary transition-colors py-2">
                            Home
                            <span
                                class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full rounded-full"></span>
                        </a>
                        <a href="{{ route('web.about') }}"
                            class="text-mainText font-bold text-sm uppercase tracking-wide relative group hover:text-primary transition-colors py-2">
                            About
                            <span
                                class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full rounded-full"></span>
                        </a>
                        <a href="{{ route('web.contact') }}"
                            class="text-mainText font-bold text-sm uppercase tracking-wide relative group hover:text-primary transition-colors py-2">
                            Contact
                            <span
                                class="absolute bottom-0 left-0 w-0 h-0.5 bg-primary transition-all duration-300 group-hover:w-full rounded-full"></span>
                        </a>
                    </div>

                    <div class="pl-8 border-l-2 border-gray-100 flex items-center gap-5">
                        @auth
                            <a href="{{ route('dashboard') }}"
                                class="px-7 py-3 rounded-xl brand-gradient text-white font-bold hover:shadow-xl hover:shadow-primary/20 hover:-translate-y-0.5 transition-all duration-300 flex items-center gap-2">
                                Dashboard
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M13 5l7 7-7 7M5 5l7 7-7 7"></path>
                                </svg>
                            </a>
                        @else
                            <a href="{{ route('login') }}"
                                class="text-mainText hover:text-primary font-bold transition-colors">Log in</a>
                            <a href="{{ route('register') }}"
                                class="px-7 py-3 rounded-xl brand-gradient text-white font-bold hover:shadow-xl hover:shadow-primary/20 hover:-translate-y-0.5 transition-all duration-300">
                                Sign Up
                            </a>
                        @endauth
                    </div>
                </div>

                <div class="flex items-center md:hidden">
                    <button @click="mobileMenuOpen = !mobileMenuOpen" type="button"
                        class="inline-flex items-center justify-center p-2.5 rounded-xl text-mainText hover:text-primary bg-gray-50 border border-gray-200 hover:border-primary/50 focus:outline-none transition-all duration-300 shadow-sm">
                        <svg class="h-6 w-6" x-show="!mobileMenuOpen" fill="none" viewBox="0 0 24 24"
                            stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg class="h-6 w-6" x-show="mobileMenuOpen" style="display: none;" fill="none"
                            viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>

        <div x-show="mobileMenuOpen" x-transition:enter="transition ease-out duration-200"
            x-transition:enter-start="opacity-0 -translate-y-4" x-transition:enter-end="opacity-100 translate-y-0"
            x-transition:leave="transition ease-in duration-150" x-transition:leave-start="opacity-100 translate-y-0"
            x-transition:leave-end="opacity-0 -translate-y-4"
            class="md:hidden absolute w-full bg-surface border-b border-gray-200 shadow-2xl" style="display: none;">
            <div class="px-6 pt-4 pb-8 space-y-3">
                <a href="{{ route('home') }}"
                    class="block px-4 py-3.5 rounded-xl text-base font-bold text-mainText hover:text-primary hover:bg-primary/5 transition-colors border border-transparent hover:border-primary/10">Home</a>
                <a href="{{ route('web.about') }}"
                    class="block px-4 py-3.5 rounded-xl text-base font-bold text-mainText hover:text-primary hover:bg-primary/5 transition-colors border border-transparent hover:border-primary/10">About</a>
                <a href="{{ route('web.contact') }}"
                    class="block px-4 py-3.5 rounded-xl text-base font-bold text-mainText hover:text-primary hover:bg-primary/5 transition-colors border border-transparent hover:border-primary/10">Contact</a>

                <div class="pt-6 mt-4 border-t border-gray-100 flex flex-col gap-4">
                    @auth
                        <a href="{{ route('dashboard') }}"
                            class="block text-center px-4 py-4 rounded-xl brand-gradient text-white font-bold shadow-lg shadow-primary/20">Go
                            to Dashboard</a>
                    @else
                        <a href="{{ route('login') }}"
                            class="block text-center px-4 py-4 rounded-xl text-mainText border-2 border-gray-100 font-bold hover:bg-gray-50 hover:border-primary/30 transition-colors">Log
                            in</a>
                        <a href="{{ route('register') }}"
                            class="block text-center px-4 py-4 rounded-xl brand-gradient text-white font-bold shadow-lg shadow-primary/20">Sign
                            Up Free</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="flex-grow pt-24 md:pt-28">
        @yield('content')
    </main>

    <footer class="bg-surface border-t border-gray-200 pt-16 pb-8 relative overflow-hidden">
        <div class="absolute -bottom-40 -right-40 w-96 h-96 bg-primary/5 rounded-full blur-3xl pointer-events-none">
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="grid grid-cols-1 md:grid-cols-12 gap-12 md:gap-8 lg:gap-12">
                <div class="md:col-span-5 lg:col-span-4">
                    <a href="{{ route('home') }}" class="flex items-center gap-3 mb-6 group w-max">
                        <img class="h-12 w-48 object-contain transform group-hover:scale-105 transition-transform duration-300"
                            src="{{ asset('storage/site_images/logo1.png') }}"
                            alt="{{ config('app.name') }}"
                            onerror="this.src='https://ui-avatars.com/api/?name=SP&background=F7941D&color=fff'">
                    </a>
                    <p class="text-mutedText leading-relaxed mb-6 font-medium">
                        Empowering the next generation with cutting-edge digital skills. Learn, grow, and succeed with
                        industry-vetted experts.
                    </p>
                    <div class="flex space-x-4">
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-navy border border-gray-200 flex items-center justify-center text-mutedText hover:bg-primary/10 hover:border-primary/30 hover:text-primary hover:-translate-y-1 transition-all duration-300">
                            <span class="sr-only">Facebook</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M22 12c0-5.523-4.477-10-10-10S2 6.477 2 12c0 4.991 3.657 9.128 8.438 9.878v-6.987h-2.54V12h2.54V9.797c0-2.506 1.492-3.89 3.777-3.89 1.094 0 2.238.195 2.238.195v2.46h-1.26c-1.243 0-1.63.771-1.63 1.562V12h2.773l-.443 2.89h-2.33v6.988C18.343 21.128 22 16.991 22 12z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-navy border border-gray-200 flex items-center justify-center text-mutedText hover:bg-secondary/10 hover:border-secondary/30 hover:text-secondary hover:-translate-y-1 transition-all duration-300">
                            <span class="sr-only">Instagram</span>
                            <svg class="h-5 w-5" fill="currentColor" viewBox="0 0 24 24">
                                <path fill-rule="evenodd"
                                    d="M12.315 2c2.43 0 2.784.013 3.808.06 1.064.049 1.791.218 2.427.465a4.902 4.902 0 011.772 1.153 4.902 4.902 0 011.153 1.772c.247.636.416 1.363.465 2.427.048 1.067.06 1.407.06 4.123v.08c0 2.643-.012 2.987-.06 4.043-.049 1.064-.218 1.791-.465 2.427a4.902 4.902 0 01-1.153 1.772 4.902 4.902 0 01-1.772 1.153c-.636.247-1.363.416-2.427.465-1.067.048-1.407.06-4.123.06h-.08c-2.643 0-2.987-.012-4.043-.06-1.064-.049-1.791-.218-2.427-.465a4.902 4.902 0 01-1.772-1.153 4.902 4.902 0 01-1.153-1.772c-.247-.636-.416-1.363-.465-2.427-.047-1.024-.06-1.379-.06-3.808v-.63c0-2.43.013-2.784.06-3.808.049-1.064.218-1.791.465-2.427a4.902 4.902 0 011.153-1.772A4.902 4.902 0 015.45 2.525c.636-.247 1.363-.416 2.427-.465C8.901 2.013 9.256 2 11.685 2h.63zm-.081 1.802h-.468c-2.456 0-2.784.011-3.807.058-.975.045-1.504.207-1.857.344-.467.182-.8.398-1.15.748-.35.35-.566.683-.748 1.15-.137.353-.3.882-.344 1.857-.047 1.023-.058 1.351-.058 3.807v.468c0 2.456.011 2.784.058 3.807.045.975.207 1.504.344 1.857.182.466.399.8.748 1.15.35.35.683.566 1.15.748.353.137.882.3 1.857.344 1.054.048 1.37.058 4.041.058h.08c2.597 0 2.917-.01 3.96-.058.976-.045 1.505-.207 1.858-.344.466-.182.8-.398 1.15-.748.35-.35.566-.683.748-1.15.137-.353.3-.882.344-1.857.048-1.055.058-1.37.058-4.041v-.08c0-2.597-.01-2.917-.058-3.96-.045-.976-.207-1.505-.344-1.858a3.097 3.097 0 00-.748-1.15 3.098 3.098 0 00-1.15-.748c-.353-.137-.882-.3-1.857-.344-1.023-.047-1.351-.058-3.807-.058zM12 6.865a5.135 5.135 0 110 10.27 5.135 5.135 0 010-10.27zm0 1.802a3.333 3.333 0 100 6.666 3.333 3.333 0 000-6.666zm5.338-3.205a1.2 1.2 0 110 2.4 1.2 1.2 0 010-2.4z"
                                    clip-rule="evenodd" />
                            </svg>
                        </a>
                        <a href="#"
                            class="w-10 h-10 rounded-full bg-navy border border-gray-200 flex items-center justify-center text-mutedText hover:bg-gray-800 hover:border-gray-800 hover:text-white hover:-translate-y-1 transition-all duration-300">
                            <span class="sr-only">X (Twitter)</span>
                            <svg class="h-4 w-4" fill="currentColor" viewBox="0 0 24 24">
                                <path
                                    d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.008 4.15H5.078z" />
                            </svg>
                        </a>
                    </div>
                </div>

                <div class="md:col-span-3 lg:col-span-2 lg:col-start-7">
                    <h3 class="text-sm font-black text-mainText tracking-widest uppercase mb-6">Explore</h3>
                    <ul class="space-y-4">
                        <li><a href="{{ route('home') }}"
                                class="text-mutedText font-medium hover:text-primary flex items-center gap-2 group transition-colors"><span
                                    class="w-1 h-1 rounded-full bg-primary opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                Home</a></li>
                        <li><a href="{{ route('home') }}#courses"
                                class="text-mutedText font-medium hover:text-primary flex items-center gap-2 group transition-colors"><span
                                    class="w-1 h-1 rounded-full bg-primary opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                Courses</a></li>
                        <li><a href="{{ route('web.about') }}"
                                class="text-mutedText font-medium hover:text-primary flex items-center gap-2 group transition-colors"><span
                                    class="w-1 h-1 rounded-full bg-primary opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                About Us</a></li>
                        <li><a href="{{ route('web.contact') }}"
                                class="text-mutedText font-medium hover:text-primary flex items-center gap-2 group transition-colors"><span
                                    class="w-1 h-1 rounded-full bg-primary opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                Contact</a></li>
                    </ul>
                </div>

                <div class="md:col-span-4 lg:col-span-3">
                    <h3 class="text-sm font-black text-mainText tracking-widest uppercase mb-6">Legal & Support</h3>
                    <ul class="space-y-4">
                        <li><a href="#"
                                class="text-mutedText font-medium hover:text-primary flex items-center gap-2 group transition-colors"><span
                                    class="w-1 h-1 rounded-full bg-primary opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                Privacy Policy</a></li>
                        <li><a href="#"
                                class="text-mutedText font-medium hover:text-primary flex items-center gap-2 group transition-colors"><span
                                    class="w-1 h-1 rounded-full bg-primary opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                Terms of Service</a></li>
                        <li><a href="#"
                                class="text-mutedText font-medium hover:text-primary flex items-center gap-2 group transition-colors"><span
                                    class="w-1 h-1 rounded-full bg-primary opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                Refund Policy</a></li>
                        <li><a href="#"
                                class="text-mutedText font-medium hover:text-primary flex items-center gap-2 group transition-colors"><span
                                    class="w-1 h-1 rounded-full bg-primary opacity-0 group-hover:opacity-100 transition-opacity"></span>
                                Help Center</a></li>
                    </ul>
                </div>
            </div>

            <div
                class="mt-16 pt-8 border-t border-gray-100 flex flex-col md:flex-row justify-between items-center gap-4">
                <p class="text-mutedText text-sm font-medium">
                    &copy; {{ date('Y') }} {{ config('app.name', 'Skills Pehle') }}. All rights reserved.
                </p>
                <div class="flex items-center gap-2 text-sm text-mutedText font-medium">
                    Developed by <span class="text-primary font-bold">Digi Emporirer</span>
                </div>
            </div>
        </div>
    </footer>

    @stack('scripts')
</body>

</html>
