<header
    class="bg-customWhite/80 backdrop-blur-md border-b border-primary/5 h-20 flex items-center px-4 md:px-8 justify-between sticky top-0 z-40">

    {{-- Left Side: Hamburger & Desktop Title --}}
    <div class="flex items-center flex-1">
        {{-- Mobile Hamburger Menu --}}
        <button @click="sidebarOpen = true"
            class="md:hidden mr-4 text-primary p-2 bg-primary/5 hover:bg-primary/10 rounded-xl transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>

        {{-- Desktop Text (Hidden on Mobile) --}}
        <div class="hidden md:block">
            <h2 class="text-xl font-extrabold text-mainText tracking-tight">
                Partner <span class="text-primary">Panel</span>
            </h2>
            <p class="text-[10px] text-mutedText uppercase tracking-[0.2em] font-bold">Skills Pehle Learning</p>
        </div>
    </div>

    <div class="md:hidden flex-[2] flex justify-center items-center">
        <div class="h-12 w-auto py-1">
            @if (file_exists(public_path('storage/site_images/logo1.png')))
                <img src="{{ asset('storage/site_images/logo1.png') }}" alt="Logo"
                    class="h-full w-auto object-contain" loading="lazy">
            @else
                <span class="font-bold text-base text-primary">SKILLS PEHLE</span>
            @endif
        </div>
    </div>

    {{-- Right Side: Profile Dropdown --}}
    <div class="flex items-center space-x-4 flex-1 justify-end">
        <div class="relative" x-data="{ userMenuOpen: false }">
            <button @click="userMenuOpen = !userMenuOpen" @click.away="userMenuOpen = false"
                class="flex items-center space-x-3 p-1 rounded-full hover:bg-navy transition pr-4">

                {{-- User Info (Desktop Only) --}}
                <div class="text-right hidden sm:block leading-tight">
                    <p class="text-xs font-bold text-mainText">{{ Auth::user()->name }}</p>
                    <p class="text-[10px] text-mutedText uppercase font-bold tracking-tighter">Partner</p>
                </div>

                {{-- User Avatar Icon --}}
                <div
                    class="h-9 w-9 rounded-lg brand-gradient flex items-center justify-center text-white font-bold shadow-lg shadow-primary/20 text-xs">
                    @php
                        $nameParts = explode(' ', trim(Auth::user()->name));
                        $initials = count($nameParts) === 1
                            ? strtoupper(substr($nameParts[0], 0, 2))
                            : strtoupper(substr($nameParts[0], 0, 1) . substr(end($nameParts), 0, 1));
                    @endphp
                    {{ $initials }}
                </div>
            </button>

            {{-- Dropdown Menu --}}
            <div x-show="userMenuOpen" x-cloak x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-3 w-56 origin-top-right bg-customWhite rounded-2xl shadow-xl border border-primary/10 overflow-hidden z-50">

                <div class="p-4 border-b border-navy">
                    <p class="text-[10px] font-bold text-mutedText/50 uppercase tracking-widest">Partner</p>
                    <p class="text-xs font-bold text-primary uppercase">ID: #{{ Auth::user()->id }}</p>
                </div>

                <div class="py-1">
                    <a href="{{ route('student.profile') }}"
                        class="px-4 py-2 text-xs text-mutedText hover:text-primary hover:bg-navy font-bold transition-all flex items-center gap-2">
                        <i class="fas fa-user-edit w-4"></i> Edit Profile
                    </a>
                    <a href="{{ route('student.profile', ['section' => 'kyc']) }}"
                        class="px-4 py-2 text-xs text-mutedText hover:text-primary hover:bg-navy font-bold transition-all flex items-center justify-between group">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-id-card w-4"></i> KYC Verification
                        </div>
                        <span
                            class="h-2 w-2 rounded-full {{ auth()->user()->kyc_status === 'verified' ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : (auth()->user()->kyc_status === 'pending' ? 'bg-amber-500' : 'bg-slate-300') }}"></span>
                    </a>
                    <a href="{{ route('student.profile', ['section' => 'bank']) }}"
                        class="px-4 py-2 text-xs text-mutedText hover:text-primary hover:bg-navy font-bold transition-all flex items-center justify-between group">
                        <div class="flex items-center gap-2">
                            <i class="fas fa-university w-4"></i> Bank Details
                        </div>
                        @php
                            // Fetch authenticated user once to keep code clean and avoid multiple calls
                            $authUser = auth()->user();
                            $bankStatus = $authUser->bank->status ?? 'not_submitted';
                            $hasBankPending = $authUser->bankUpdateRequests()->where('status', 'pending')->exists();
                        @endphp
                        <span
                            class="h-2 w-2 rounded-full {{ $bankStatus === 'verified' && !$hasBankPending ? 'bg-emerald-500 shadow-[0_0_8px_rgba(16,185,129,0.5)]' : ($bankStatus === 'pending' || $hasBankPending ? 'bg-amber-500' : 'bg-slate-300') }}"></span>
                    </a>
                    <a href="{{ route('student.profile', ['section' => 'password']) }}"
                        class="px-4 py-2 text-xs text-mutedText hover:text-primary hover:bg-navy font-bold transition-all flex items-center gap-2">
                        <i class="fas fa-key w-4"></i> Change Password
                    </a>

                    <a href="{{ route('student.certificates.index') }}"
                        class="px-4 py-2 text-xs text-mutedText hover:text-primary hover:bg-navy font-bold transition-all flex items-center gap-2">
                        <i class="fas fa-certificate w-4"></i> My Certificates
                    </a>

                    <div class="h-px bg-navy my-1 mx-2"></div>

                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full text-left px-4 py-2 text-xs text-secondary hover:bg-secondary/5 font-bold transition-all flex items-center gap-2">
                            <i class="fas fa-sign-out-alt w-4"></i> Logout Account
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
