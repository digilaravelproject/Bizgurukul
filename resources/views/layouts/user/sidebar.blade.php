<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-navy border-r border-primary/10 transform transition-transform duration-300 ease-in-out md:fixed md:translate-x-0 flex flex-col h-screen shadow-xl md:shadow-none">

    <div class="relative z-10 flex flex-col h-full bg-customWhite">
        {{-- Sidebar Logo --}}
        <div class="p-6 flex justify-between items-center h-20 border-b border-navy">
            <div class="flex items-center space-x-3">
                <div class="w-auto h-9 flex items-center justify-center">
                    @if (file_exists(public_path('storage/site_images/logo1.png')))
                        <img src="{{ asset('storage/site_images/logo1.png') }}" alt="Logo"
                            class="h-full w-auto object-contain group-hover:scale-110 transition-transform" loading="lazy">
                    @else
                        <div class="h-full flex items-center justify-center font-bold text-lg text-primary">
                            SKILLS PEHLE
                        </div>
                    @endif
                </div>
            </div>
            <button @click="sidebarOpen = false" class="md:hidden text-mutedText hover:text-primary transition p-2">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                    </path>
                </svg>
            </button>
        </div>

        {{-- Sidebar Nav Menu --}}
        <nav class="flex-1 p-4 space-y-1 overflow-hidden">
            <p class="text-[10px] font-bold text-mutedText/50 uppercase tracking-[0.2em] px-4 mb-2">Learning Hub</p>

            {{-- Dashboard --}}
            <a href="{{ route('student.dashboard') }}"
                class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 group {{ request()->routeIs('student.dashboard') ? 'bg-primary/10 text-primary shadow-sm' : 'text-mutedText hover:bg-navy hover:text-primary' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6">
                    </path>
                </svg>
                <span class="text-sm font-bold">Dashboard</span>
            </a>

            {{-- My Courses Link --}}
            <a href="{{ route('student.courses.index') }}"
                class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 group {{ request()->routeIs('student.courses.*') ? 'bg-primary/10 text-primary shadow-sm' : 'text-mutedText hover:bg-navy hover:text-primary' }}">

                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                    </path>
                </svg>

                <span class="text-sm font-bold">All Courses</span>
            </a>

            {{-- My Courses --}}
            <a href="{{ route('student.my-courses') }}"
                class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 group {{ request()->routeIs('student.my-courses') ? 'bg-primary/10 text-primary shadow-sm' : 'text-mutedText hover:bg-navy hover:text-primary' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                    </path>
                </svg>
                <span class="text-sm font-bold">My Courses</span>
            </a>

            {{-- Beginner Guide --}}
            <a href="{{ route('student.beginner-guide') }}"
                class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 group {{ request()->routeIs('student.beginner-guide') ? 'bg-primary/10 text-primary shadow-sm' : 'text-mutedText hover:bg-navy hover:text-primary' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-sm font-bold">Beginner's Guide</span>
            </a>

            <p class="text-[10px] font-bold text-mutedText/50 uppercase tracking-[0.2em] px-4 mt-6 mb-2">Affiliate
                Section</p>

            {{-- Affiliate Dashboard --}}
            <a href="{{ route('student.affiliate.dashboard') }}"
                class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 group {{ request()->routeIs('student.affiliate.dashboard') ? 'bg-primary/10 text-primary shadow-sm' : 'text-mutedText hover:bg-navy hover:text-primary' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                    </path>
                </svg>
                <span class="text-sm font-bold">Partner Dashboard</span>
            </a>

            {{-- My Leads --}}
            <a href="{{ route('student.affiliate.leads') }}"
                class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 group {{ request()->routeIs('student.affiliate.leads') ? 'bg-primary/10 text-primary shadow-sm' : 'text-mutedText hover:bg-navy hover:text-primary' }}">
                <i class="fas fa-users w-5 h-5 mr-3 text-lg flex items-center justify-center text-center"></i>
                <span class="text-sm font-bold">My Leads</span>
            </a>

            {{-- Commission Structure --}}
            <a href="{{ route('student.affiliate.commission_structure') }}"
                class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 group {{ request()->routeIs('student.affiliate.commission_structure') ? 'bg-primary/10 text-primary shadow-sm' : 'text-mutedText hover:bg-navy hover:text-primary' }}">
                <i class="fas fa-sitemap w-5 h-5 mr-3 text-lg flex items-center justify-center text-center"></i>
                <span class="text-sm font-bold">Commission Structure</span>
            </a>

            {{-- Affiliate Links --}}
            <a href="{{ route('student.affiliate-links.index') }}"
                class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 group {{ request()->routeIs('student.affiliate-links.*') ? 'bg-primary/10 text-primary shadow-sm' : 'text-mutedText hover:bg-navy hover:text-primary' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                    </path>
                </svg>
                <span class="text-sm font-bold">Marketing Links</span>
            </a>

            <p class="text-[10px] font-bold text-mutedText/50 uppercase tracking-[0.2em] px-4 mt-6 mb-2">Growth Lab</p>

            {{-- My Coupons --}}
            <a href="{{ route('student.coupons.index') }}"
                class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 group {{ request()->routeIs('student.coupons.index') ? 'bg-primary/10 text-primary shadow-sm' : 'text-mutedText hover:bg-navy hover:text-primary' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z"></path>
                </svg>
                <span class="text-sm font-bold">My Coupon Bank</span>
            </a>

            {{-- Coupon Store --}}
            <a href="{{ route('student.coupons.store') }}"
                class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 group {{ request()->routeIs('student.coupons.store') ? 'bg-primary/10 text-primary shadow-sm' : 'text-mutedText hover:bg-navy hover:text-primary' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                </svg>
                <span class="text-sm font-bold">Coupon Store</span>
            </a>

            <p class="text-[10px] font-bold text-mutedText/50 uppercase tracking-[0.2em] px-4 mt-6 mb-2">Account & Billing</p>

            {{-- My Invoices --}}
            <a href="{{ route('student.invoices.index') }}"
                class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 group {{ request()->routeIs('student.invoices.*') ? 'bg-primary/10 text-primary shadow-sm' : 'text-mutedText hover:bg-navy hover:text-primary' }}">
                <i class="fas fa-file-invoice-dollar w-5 h-5 mr-3 text-lg flex items-center justify-center text-center"></i>
                <span class="text-sm font-bold">My Invoices</span>
            </a>
        </nav>

        {{-- Sidebar User Footer --}}
        <div class="p-4 mt-auto border-t border-navy bg-navy/30">
            <div class="flex items-center p-2 rounded-xl border border-primary/5 bg-customWhite">
                <div
                    class="h-9 w-9 rounded-lg brand-gradient flex items-center justify-center text-white font-bold text-xs shadow-md">
                    {{ substr(Auth::user()->name ?? 'ST', 0, 2) }}
                </div>
                <div class="ml-3 flex-1 overflow-hidden">
                    <p class="text-xs font-bold text-mainText truncate">{{ Auth::user()->name }}</p>
                    <p class="text-[9px] text-primary font-bold uppercase tracking-wider">Verified Student</p>
                </div>

            </div>
        </div>
    </div>
</aside>
