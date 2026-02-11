<aside {{-- Sidebar opens with 0 translation, hides with -full translation on mobile --}} :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-navy border-r border-primary/10 transform transition-transform duration-300 ease-in-out md:relative md:translate-x-0 flex flex-col overflow-hidden shadow-xl md:shadow-none ">

    <div class="relative z-10 flex flex-col h-full bg-customWhite">
        {{-- Logo Section --}}
        <div class="p-6 flex justify-between items-center h-20 border-b border-navy">
            <div class="flex items-center space-x-3">
                <div class="w-auto h-9 flex items-center justify-center">
                    <img src="{{ asset('storage/site_images/logo1.png') }}" alt="Logo"
                        class="h-full w-auto object-contain group-hover:scale-110 transition-transform" loading="lazy">
                </div>
                {{-- <div class="flex flex-col">
                    <span class="text-lg font-extrabold tracking-tight text-mainText leading-none">Skills<span class="text-primary">Pehle</span></span>
                    <span class="text-[9px] text-mutedText tracking-[0.2em] font-bold uppercase mt-1">Admin Pro</span>
                </div> --}}
            </div>

            {{-- Mobile Close Button --}}
            <button @click="sidebarOpen = false" class="md:hidden text-mutedText hover:text-primary transition p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        {{-- Nav Menu --}}
        {{-- Nav Menu --}}
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto no-scrollbar" x-data="{
            lmsOpen: {{ request()->routeIs('admin.courses.*', 'admin.categories.*', 'admin.lessons.*') ? 'true' : 'false' }},
            userOpen: {{ request()->routeIs('admin.users.*', 'admin.kyc.*') ? 'true' : 'false' }},
            promoOpen: {{ request()->routeIs('admin.coupons.*', 'admin.bundles.*') ? 'true' : 'false' }}
        }">

            {{-- 1. ANALYTICS & DASHBOARD --}}
            <p class="text-[10px] font-bold text-mutedText/50 uppercase tracking-[0.2em] px-4 mb-2">Main Menu</p>
            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 group {{ request()->routeIs('admin.dashboard') ? 'bg-primary/10 text-primary shadow-sm' : 'text-mutedText hover:bg-navy hover:text-primary' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z">
                    </path>
                </svg>
                <span class="font-bold text-sm">Dashboard</span>
            </a>

            {{-- 2. LMS ENGINE (Dropdown) --}}
            <div class="pt-4">
                <p class="text-[10px] font-bold text-mutedText/50 uppercase tracking-[0.2em] px-4 mb-2">Academic Content
                </p>
                <button @click="lmsOpen = !lmsOpen"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-2xl transition-all group {{ request()->routeIs('admin.courses.*', 'admin.categories.*') ? 'bg-navy text-primary' : 'text-mutedText hover:bg-navy hover:text-primary' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                        <span class="font-bold text-sm">Course Manager</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-300" :class="lmsOpen ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div x-show="lmsOpen" x-cloak x-transition
                    class="ml-4 pl-4 mt-2 space-y-1 border-l-2 border-primary/20">
                    <a href="{{ route('admin.courses.index') }}"
                        class="block py-2 px-4 text-xs rounded-lg transition-all {{ request()->routeIs('admin.courses.index') ? 'text-primary font-bold bg-primary/5' : 'text-mutedText hover:text-primary' }}">
                        All Courses
                    </a>
                    <a href="{{ route('admin.categories.index') }}"
                        class="block py-2 px-4 text-xs rounded-lg transition-all {{ request()->routeIs('admin.categories.index') ? 'text-primary font-bold bg-primary/5' : 'text-mutedText hover:text-primary' }}">
                        Categories
                    </a>
                    {{-- <a href="{{ route('admin.lessons.all') }}" class="block py-2 px-4 text-xs rounded-lg transition-all {{ request()->routeIs('admin.lessons.all') ? 'text-primary font-bold bg-primary/5' : 'text-mutedText hover:text-primary' }}">
                Manage Lessons
            </a> --}}
                </div>
            </div>

            {{-- 3. MARKETING & SALES (Dropdown) --}}
            <div class="pt-4">
                <p class="text-[10px] font-bold text-mutedText/50 uppercase tracking-[0.2em] px-4 mb-2">Sales & Growth
                </p>
                <button @click="promoOpen = !promoOpen"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-2xl transition-all group {{ request()->routeIs('admin.coupons.*', 'admin.bundles.*') ? 'bg-navy text-primary' : 'text-mutedText hover:bg-navy hover:text-primary' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                        <span class="font-bold text-sm">Promotions</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-300" :class="promoOpen ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div x-show="promoOpen" x-cloak x-transition
                    class="ml-4 pl-4 mt-2 space-y-1 border-l-2 border-primary/20">
                    <a href="{{ route('admin.coupons.index') }}"
                        class="block py-2 px-4 text-xs rounded-lg transition-all {{ request()->routeIs('admin.coupons.index') ? 'text-primary font-bold bg-primary/5' : 'text-mutedText hover:text-primary' }}">
                        Coupons
                    </a>
                    <a href="{{ route('admin.coupon-packages.index') }}"
                        class="block py-2 px-4 text-xs rounded-lg transition-all {{ request()->routeIs('admin.coupon-packages.*') ? 'text-primary font-bold bg-primary/5' : 'text-mutedText hover:text-primary' }}">
                        <div class="flex items-center gap-2">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4" />
                            </svg>
                            Coupon Packages
                        </div>
                    </a>
                    <a href="{{ route('admin.bundles.index') }}"
                        class="block py-2 px-4 text-xs rounded-lg transition-all {{ request()->routeIs('admin.bundles.*') ? 'text-primary font-bold bg-primary/5' : 'text-mutedText hover:text-primary' }}">
                        Course Bundles
                    </a>
                    <a href="#" class="block py-2 px-4 text-xs text-mutedText/50 cursor-not-allowed">Orders
                        (Coming Soon)</a>
                </div>
            </div>

            {{-- 4. USER MANAGEMENT (Dropdown) --}}
            <div class="pt-4">
                <p class="text-[10px] font-bold text-mutedText/50 uppercase tracking-[0.2em] px-4 mb-2">Identity</p>
                <button @click="userOpen = !userOpen"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-2xl transition-all group {{ request()->routeIs('admin.users.*', 'admin.kyc.*') ? 'bg-navy text-primary' : 'text-mutedText hover:bg-navy hover:text-primary' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <span class="font-bold text-sm">User Control</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-300" :class="userOpen ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                    </svg>
                </button>

                <div x-show="userOpen" x-cloak x-transition
                    class="ml-4 pl-4 mt-2 space-y-1 border-l-2 border-primary/20">
                    <a href="{{ route('admin.users.index') }}"
                        class="block py-2 px-4 text-xs rounded-lg transition-all {{ request()->routeIs('admin.users.index') ? 'text-primary font-bold bg-primary/5' : 'text-mutedText hover:text-primary' }}">
                        Students List
                    </a>
                    <a href="{{ route('admin.kyc.index') }}"
                        class="block py-2 px-4 text-xs rounded-lg transition-all {{ request()->routeIs('admin.kyc.*') ? 'text-primary font-bold bg-primary/5' : 'text-mutedText hover:text-primary' }}">
                        KYC Requests
                    </a>
                </div>
            </div>

            {{-- 5. AFFILIATE SYSTEM (Dropdown) --}}
            <div class="pt-4" x-data="{ affiliateOpen: {{ request()->routeIs('admin.affiliate.*') ? 'true' : 'false' }} }">
                <p class="text-[10px] font-bold text-mutedText/50 uppercase tracking-[0.2em] px-4 mb-2">Partners</p>
                <button @click="affiliateOpen = !affiliateOpen"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-2xl transition-all group {{ request()->routeIs('admin.affiliate.*') ? 'bg-navy text-primary' : 'text-mutedText hover:bg-navy hover:text-primary' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z">
                            </path>
                        </svg>
                        <span class="font-bold text-sm">Affiliate System</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-300" :class="affiliateOpen ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7">
                        </path>
                    </svg>
                </button>

                <div x-show="affiliateOpen" x-cloak x-transition
                    class="ml-4 pl-4 mt-2 space-y-1 border-l-2 border-primary/20">
                    <a href="{{ route('admin.affiliate.rules.index') }}"
                        class="block py-2 px-4 text-xs rounded-lg transition-all {{ request()->routeIs('admin.affiliate.rules.*') ? 'text-primary font-bold bg-primary/5' : 'text-mutedText hover:text-primary' }}">
                        Commission Rules
                    </a>
                    <a href="{{ route('admin.affiliate.history') }}"
                        class="block py-2 px-4 text-xs rounded-lg transition-all {{ request()->routeIs('admin.affiliate.history') ? 'text-primary font-bold bg-primary/5' : 'text-mutedText hover:text-primary' }}">
                        Payout History
                    </a>
                </div>
            </div>

            {{-- 5. SETTINGS --}}
            {{-- <div class="pt-4">
        <p class="text-[10px] font-bold text-mutedText/50 uppercase tracking-[0.2em] px-4 mb-2">Config</p>
        <a href="#"
            class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 group text-mutedText hover:bg-navy hover:text-primary">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><circle cx="12" cy="12" r="3"></circle></svg>
            <span class="font-bold text-sm">Platform Settings</span>
        </a>
    </div> --}}

        </nav>

        {{-- Minimal Footer --}}
        <div class="p-4 mt-auto border-t border-navy bg-navy/30">
            <div class="flex items-center p-2 rounded-xl border border-primary/5 bg-customWhite">
                <div
                    class="h-9 w-9 rounded-lg brand-gradient flex items-center justify-center text-white font-bold text-xs shadow-md">
                    {{ substr(Auth::user()->name ?? 'AD', 0, 2) }}
                </div>
                <div class="ml-3 flex-1 overflow-hidden">
                    <p class="text-xs font-bold text-mainText truncate">{{ Auth::user()->name ?? 'Administrator' }}
                    </p>
                    <p class="text-[9px] text-primary font-bold uppercase tracking-wider">Super Admin</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-1.5 text-mutedText hover:text-secondary transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                            </path>
                        </svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
