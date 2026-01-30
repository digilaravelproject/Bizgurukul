<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-customWhite border-r border-primary/10 transform transition-transform duration-300 ease-in-out md:relative md:translate-x-0 flex flex-col overflow-hidden shadow-sm">

    <div class="relative z-10 flex flex-col h-full">
        {{-- Logo Section --}}
        <div class="p-6 flex justify-between items-center h-20 border-b border-navy">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 brand-gradient rounded-xl flex items-center justify-center shadow-lg shadow-primary/20 group">
                    <span class="text-white text-xl font-black italic group-hover:scale-110 transition-transform">S</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-lg font-extrabold tracking-tight text-mainText leading-none">Skills<span class="text-primary">Pehle</span></span>
                    <span class="text-[9px] text-mutedText tracking-[0.2em] font-bold uppercase mt-1">Admin Pro</span>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="md:hidden text-mutedText hover:text-primary transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Nav Menu --}}
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto no-scrollbar" x-data="{ lmsOpen: {{ request()->routeIs('admin.courses.*') ? 'true' : 'false' }} }">

            <p class="text-[10px] font-bold text-mutedText/50 uppercase tracking-[0.2em] px-4 mb-4 mt-2">Main Dashboard</p>

            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 group {{ request()->routeIs('admin.dashboard') ? 'bg-primary/10 text-primary shadow-sm' : 'text-mutedText hover:bg-navy hover:text-primary' }}">
                <svg class="w-5 h-5 mr-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span class="font-bold text-sm">Dashboard</span>
            </a>

            <div class="pt-4">
                <p class="text-[10px] font-bold text-mutedText/50 uppercase tracking-[0.2em] px-4 mb-4">LMS Engine</p>

                <button @click="lmsOpen = !lmsOpen"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-2xl transition-all group {{ request()->routeIs('admin.courses.*') ? 'bg-navy text-primary' : 'text-mutedText hover:bg-navy hover:text-primary' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span class="font-bold text-sm">Courses</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-300" :class="lmsOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="lmsOpen" x-cloak x-transition:enter="transition ease-out duration-200" class="ml-4 pl-4 mt-2 space-y-1 border-l-2 border-primary/20">
                    <a href="{{ route('admin.courses.index') }}" class="block py-2 px-4 text-xs rounded-lg transition-all {{ request()->routeIs('admin.courses.index') ? 'text-primary font-bold bg-primary/5' : 'text-mutedText hover:text-primary' }}">
                        All Courses
                    </a>
                    <a href="#" class="block py-2 px-4 text-xs text-mutedText hover:text-primary transition-colors">Course Bundles</a>
                </div>
            </div>

            <div class="pt-4">
                <p class="text-[10px] font-bold text-mutedText/50 uppercase tracking-[0.2em] px-4 mb-4">User Management</p>
                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 group {{ request()->routeIs('admin.users.*') ? 'bg-primary/10 text-primary shadow-sm' : 'text-mutedText hover:bg-navy hover:text-primary' }}">
                    <svg class="w-5 h-5 mr-3 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span class="font-bold text-sm">Students List</span>
                </a>
            </div>
        </nav>

        {{-- Minimal Footer --}}
        <div class="p-4 mt-auto border-t border-navy bg-navy/30">
            <div class="flex items-center p-2 rounded-xl border border-primary/5">
                <div class="h-9 w-9 rounded-lg brand-gradient flex items-center justify-center text-white font-bold text-xs shadow-md">
                    {{ substr(Auth::user()->name ?? 'AD', 0, 2) }}
                </div>
                <div class="ml-3 flex-1 overflow-hidden">
                    <p class="text-xs font-bold text-mainText truncate">{{ Auth::user()->name ?? 'Administrator' }}</p>
                    <p class="text-[9px] text-primary font-bold uppercase tracking-wider">Super Admin</p>
                </div>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit" class="p-1.5 text-mutedText hover:text-secondary transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
