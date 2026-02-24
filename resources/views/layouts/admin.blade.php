<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Skills Pehle') }} - Admin</title>
    <link rel="icon" type="image/png" href="{{ asset('storage/site_images/logo.png') }}">

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <script defer src="https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css" integrity="sha512-2SwdPD6INVrV/lHTZbO2nodKhrnDdJK9/kg2XD1r9uGqPo1cUbujc+IYdlYdEErWNu69gVcYgdxlmVmzTWnetw==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <style>
        [x-cloak] { display: none !important; }
        .no-scrollbar::-webkit-scrollbar { display: none; }

        /* Smooth transitions for all elements */
        * { transition: background-color 0.2s ease, border-color 0.2s ease; }
    </style>
    @stack('styles')
</head>

<body class="antialiased bg-navy text-mainText" x-data="{ sidebarOpen: false }">

    <div class="h-screen flex overflow-hidden">

        @include('layouts.partials.sidebar')

        <div class="flex-1 flex flex-col min-w-0 bg-navy relative md:ml-64">

            {{-- Header --}}
            <header class="bg-customWhite/80 backdrop-blur-md border-b border-primary/5 h-20 flex items-center px-4 md:px-8 justify-between sticky top-0 z-40">

                <div class="flex items-center">
                    {{-- Mobile Hamburger Toggle --}}
                    <button @click="sidebarOpen = true" class="md:hidden mr-4 p-2 bg-primary/5 text-primary rounded-xl hover:bg-primary/10 transition">
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
                    {{-- Flash Messages --}}
                    @if(session('success'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)"
                             class="mb-6 p-4 rounded-2xl bg-green-500/10 border border-green-500/20 text-green-600 flex items-center justify-between animate-fade-in">
                            <div class="flex items-center gap-3 font-bold text-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                <span>{{ session('success') }}</span>
                            </div>
                            <button @click="show = false" class="text-green-600 hover:text-green-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 8000)"
                             class="mb-6 p-4 rounded-2xl bg-red-500/10 border border-red-500/20 text-red-600 flex items-center justify-between animate-fade-in">
                            <div class="flex items-center gap-3 font-bold text-sm">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <span>{{ session('error') }}</span>
                            </div>
                            <button @click="show = false" class="text-red-600 hover:text-red-700">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>
                    @endif

                    @if($errors->any())
                        <div x-data="{ show: true }" x-show="show"
                             class="mb-6 p-4 rounded-2xl bg-orange-500/10 border border-orange-500/20 text-orange-600 animate-fade-in">
                            <div class="flex items-start gap-3 font-bold text-sm">
                                <svg class="w-5 h-5 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                                <div class="flex-1">
                                    <p class="mb-2 uppercase tracking-wider text-xs">Please fix the following errors:</p>
                                    <ul class="list-disc list-inside space-y-1 font-medium">
                                        @foreach($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                                <button @click="show = false" class="text-orange-600 hover:text-orange-700">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                </button>
                            </div>
                        </div>
                    @endif

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

    {{-- Mobile Overlay Backdrop --}}
    <div x-show="sidebarOpen"
         x-transition:enter="transition opacity ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition opacity ease-in duration-300"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="sidebarOpen = false"
         x-cloak
         class="fixed inset-0 bg-mainText/60 z-40 md:hidden backdrop-blur-sm">
    </div>

    <div x-data="withdrawalNotifier()" x-init="initNotifier()">
        <template x-if="showToast">
            <div class="fixed bottom-10 right-10 z-[100] bg-emerald-500 text-white px-6 py-4 rounded-xl shadow-2xl shadow-emerald-500/30 flex justify-between items-center gap-4"
                 x-transition:enter="transition ease-out duration-300 transform"
                 x-transition:enter-start="translate-y-10 opacity-0"
                 x-transition:enter-end="translate-y-0 opacity-100"
                 x-transition:leave="transition ease-in duration-200"
                 x-transition:leave-start="opacity-100"
                 x-transition:leave-end="opacity-0">
                <div class="flex items-center gap-3">
                    <i class="fas fa-bell text-2xl animate-bounce"></i>
                    <div>
                        <h4 class="font-black text-sm uppercase tracking-widest">New Withdrawal Request</h4>
                        <p class="text-xs font-semibold opacity-90 mt-0.5">A new partner payout requires attention!</p>
                    </div>
                </div>
                <button @click="closeToast()" class="text-white hover:text-emerald-100 ml-2 transition">
                    <i class="fas fa-times"></i>
                </button>
            </div>
        </template>
    </div>

    @stack('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('withdrawalNotifier', () => ({
                lastId: {{ \App\Models\WithdrawalRequest::max('id') ?? 0 }},
                showToast: false,

                initNotifier() {
                    setInterval(() => {
                        fetch('{{ route("admin.payouts.check_new") }}')
                            .then(res => res.json())
                            .then(data => {
                                if (data.latest_id > this.lastId) {
                                    this.lastId = data.latest_id;
                                    this.showToast = true;
                                    // Auto hide
                                    setTimeout(() => this.showToast = false, 8000);

                                    // Optional: If they are on the payouts page, we could refresh it, but it's simpler to just notify.
                                }
                            })
                            .catch(err => console.error('Notification Error:', err));
                    }, 15000); // 15 seconds polling
                },

                closeToast() {
                    this.showToast = false;
                }
            }));
        });
    </script>
</body>
</html>
