<header
    class="bg-white/80 backdrop-blur-md border-b border-slate-200 h-16 flex items-center px-4 md:px-8 justify-between sticky top-0 z-40">
    <div class="flex items-center flex-1">
        {{-- Mobile Hamburger Menu --}}
        <button @click="sidebarOpen = true"
            class="md:hidden mr-4 text-slate-600 p-2 hover:bg-slate-100 rounded-lg transition">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
            </svg>
        </button>
        <h2 class="text-sm font-black text-slate-800 uppercase italic tracking-tighter">Student Panel</h2>
    </div>

    <div class="flex items-center space-x-4">
        {{-- Profile Dropdown Section --}}
        <div class="relative" x-data="{ userMenuOpen: false }">
            <button @click="userMenuOpen = !userMenuOpen" @click.away="userMenuOpen = false"
                class="flex items-center space-x-3 focus:outline-none group">

                {{-- User Info (Desktop Only) --}}
                <div class="text-right hidden sm:block leading-none">
                    <p class="text-[10px] font-black text-slate-400 uppercase italic mb-0.5">Welcome back,</p>
                    <p
                        class="text-[11px] font-black text-slate-800 uppercase italic tracking-tighter group-hover:text-indigo-600 transition-colors">
                        {{ Auth::user()->name }}
                    </p>
                </div>

                {{-- User Avatar Icon --}}
                <div
                    class="h-10 w-10 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center font-black border border-indigo-100 shadow-sm group-hover:shadow-md transition-all">
                    {{ substr(Auth::user()->name, 0, 1) }}
                </div>
            </button>

            {{-- Dropdown Menu Logic --}}
            <div x-show="userMenuOpen" x-cloak x-transition:enter="transition ease-out duration-100"
                x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100"
                x-transition:leave="transition ease-in duration-75" x-transition:leave-start="opacity-100 scale-100"
                x-transition:leave-end="opacity-0 scale-95"
                class="absolute right-0 mt-3 w-56 origin-top-right bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden ring-1 ring-black ring-opacity-5">

                <div class="p-4 border-b border-slate-50 bg-slate-50/50">
                    <p class="text-[10px] font-black text-slate-400 uppercase italic tracking-widest">Student Identity
                    </p>
                    <p class="text-xs font-black text-indigo-600 uppercase italic">ID: #{{ Auth::user()->id }}</p>
                </div>

                <div class="p-2 space-y-1">
                    {{-- My Profile Link --}}
                    <a href="#"
                        class="flex items-center px-4 py-3 text-xs font-black text-slate-600 uppercase italic rounded-xl hover:bg-slate-50 hover:text-indigo-600 transition-all">
                        <svg class="w-4 h-4 mr-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                        </svg>
                        Edit Profile
                    </a>

                    {{-- Settings Link --}}
                    <a href="#"
                        class="flex items-center px-4 py-3 text-xs font-black text-slate-600 uppercase italic rounded-xl hover:bg-slate-50 hover:text-indigo-600 transition-all">
                        <svg class="w-4 h-4 mr-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z">
                            </path>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                        </svg>
                        Security
                    </a>

                    <div class="h-px bg-slate-50 my-1 mx-2"></div>

                    {{-- Logout Form --}}
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit"
                            class="w-full flex items-center px-4 py-3 text-xs font-black text-red-500 uppercase italic rounded-xl hover:bg-red-50 transition-all">
                            <svg class="w-4 h-4 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1">
                                </path>
                            </svg>
                            Logout Account
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
