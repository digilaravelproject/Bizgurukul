<aside :class="sidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    class="fixed inset-y-0 left-0 z-50 w-64 bg-[#0F172A] border-r border-white/5 transform transition-transform duration-300 ease-in-out md:relative md:translate-x-0 flex flex-col overflow-hidden shadow-2xl">

    {{-- 1. Ambient Background Glow (Not just flat blue) --}}
    <div class="absolute inset-0 pointer-events-none z-0 overflow-hidden">
        {{-- Top Left Glow --}}
        <div class="absolute -top-[10%] -left-[10%] w-40 h-40 bg-primary rounded-full mix-blend-screen filter blur-[80px] opacity-20 animate-pulse"></div>
        {{-- Bottom Right Glow --}}
        <div class="absolute bottom-[10%] -right-[10%] w-40 h-40 bg-secondary rounded-full mix-blend-screen filter blur-[80px] opacity-10"></div>
    </div>

    {{-- 2. Sidebar Content (Z-index high to sit above glow) --}}
    <div class="relative z-10 flex flex-col h-full">

        {{-- Logo Section --}}
        <div class="p-6 flex justify-between items-center h-20 border-b border-white/5 bg-white/[0.02] backdrop-blur-sm">
            <div class="flex items-center space-x-3">
                <div class="w-9 h-9 bg-gradient-to-br from-primary to-indigo-800 rounded-xl flex items-center justify-center shadow-lg shadow-primary/30 border border-white/10 group">
                    <span class="text-white text-xl font-black italic group-hover:scale-110 transition-transform">B</span>
                </div>
                <div class="flex flex-col">
                    <span class="text-lg font-extrabold tracking-tight text-white leading-none">Biz<span class="text-primary">Gurukul</span></span>
                    <span class="text-[9px] text-mutedText tracking-[0.3em] font-medium uppercase mt-0.5">Admin Pro</span>
                </div>
            </div>
            <button @click="sidebarOpen = false" class="md:hidden text-mutedText hover:text-white transition">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
            </button>
        </div>

        {{-- Nav Menu --}}
        <nav class="flex-1 p-4 space-y-1 overflow-y-auto no-scrollbar" x-data="{ lmsOpen: {{ request()->routeIs('admin.courses.*') ? 'true' : 'false' }} }">

            <p class="text-[10px] font-bold text-mutedText/40 uppercase tracking-[0.2em] px-4 mb-4 mt-2">Overview</p>

            <a href="{{ route('admin.dashboard') }}"
                class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 group mb-1 {{ request()->routeIs('admin.dashboard') ? 'sidebar-link-active shadow-lg shadow-primary/10' : 'text-mutedText hover:bg-white/5 hover:text-white' }}">
                <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.dashboard') ? 'text-primary' : 'group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2V6zM14 6a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2V6zM4 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2H6a2 2 0 01-2-2v-2zM14 16a2 2 0 012-2h2a2 2 0 012 2v2a2 2 0 01-2 2h-2a2 2 0 01-2-2v-2z"></path></svg>
                <span class="font-semibold text-sm">Dashboard</span>
            </a>

            <div class="pt-4">
                <p class="text-[10px] font-bold text-mutedText/40 uppercase tracking-[0.2em] px-4 mb-4">LMS Engine</p>

                {{-- Course Dropdown --}}
                <button @click="lmsOpen = !lmsOpen"
                    class="w-full flex items-center justify-between px-4 py-3 rounded-2xl transition-all group {{ request()->routeIs('admin.courses.*') ? 'bg-white/5 text-white' : 'text-mutedText hover:bg-white/5 hover:text-white' }}">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3 {{ request()->routeIs('admin.courses.*') ? 'text-secondary' : 'text-mutedText group-hover:text-secondary' }} transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                        <span class="font-semibold text-sm">Courses</span>
                    </div>
                    <svg class="w-4 h-4 transition-transform duration-300" :class="lmsOpen ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </button>

                <div x-show="lmsOpen" x-cloak x-transition:enter="transition ease-out duration-200" x-transition:enter-start="opacity-0 -translate-y-2" x-transition:enter-end="opacity-100 translate-y-0" class="ml-4 pl-4 mt-2 space-y-1 border-l border-white/10">
                    <a href="{{ route('admin.courses.index') }}" class="block py-2 px-4 text-xs rounded-r-xl transition-all {{ request()->routeIs('admin.courses.*') ? 'text-white bg-white/5 font-bold border-l-2 border-primary' : 'text-mutedText hover:text-white' }}">
                        All Courses
                    </a>
                    <a href="#" class="block py-2 px-4 text-xs text-mutedText hover:text-white transition-colors">Course Bundles</a>
                </div>
            </div>

            <div class="pt-4">
                <p class="text-[10px] font-bold text-mutedText/40 uppercase tracking-[0.2em] px-4 mb-4">People</p>
                <a href="{{ route('admin.users.index') }}"
                    class="flex items-center px-4 py-3 rounded-2xl transition-all duration-300 group {{ request()->routeIs('admin.users.*') ? 'sidebar-link-active shadow-lg shadow-primary/10' : 'text-mutedText hover:bg-white/5 hover:text-white' }}">
                    <svg class="w-5 h-5 mr-3 transition-colors {{ request()->routeIs('admin.users.*') ? 'text-primary' : 'group-hover:text-white' }}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    <span class="font-semibold text-sm">Students List</span>
                </a>
            </div>
        </nav>

        {{-- Dynamic User Footer --}}
        <div class="p-4 mt-auto border-t border-white/5 bg-white/[0.02]">
            <div class="flex items-center bg-[#0F172A]/80 backdrop-blur-md p-3 rounded-2xl border border-white/10 shadow-lg relative overflow-hidden group hover:border-primary/30 transition-colors cursor-pointer">

                {{-- Hover Effect --}}
                <div class="absolute inset-0 bg-gradient-to-r from-primary/10 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>

                {{-- User Avatar --}}
                <div class="relative h-10 w-10 flex-shrink-0">
                    <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-gray-700 to-gray-900 border border-white/10 flex items-center justify-center text-white font-bold shadow-inner">
                        {{ substr(Auth::user()->name ?? 'AD', 0, 2) }}
                    </div>
                    <span class="absolute -top-1 -right-1 flex h-3 w-3">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-3 w-3 bg-green-500 border-2 border-[#0F172A]"></span>
                    </span>
                </div>

                {{-- User Info --}}
                <div class="ml-3 min-w-0 flex-1 relative z-10">
                    <p class="text-xs font-bold text-white truncate group-hover:text-primary transition-colors">
                        {{ Auth::user()->name ?? 'Administrator' }}
                    </p>
                    <div class="flex items-center mt-0.5">
                        <p class="text-[9px] text-mutedText truncate max-w-[90px]">
                            {{ Auth::user()->role ?? 'Super Admin' }}
                        </p>
                        <span class="mx-1.5 text-white/20">|</span>
                        <p class="text-[9px] text-green-400 font-bold tracking-wider">LIVE</p>
                    </div>
                </div>

                {{-- Logout Button (Hidden by default, shown on hover optional, but kept simple here) --}}
                <form method="POST" action="{{ route('logout') }}" class="ml-2">
                    @csrf
                    <button type="submit" class="text-mutedText hover:text-secondary transition-colors p-1" title="Logout">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path></svg>
                    </button>
                </form>
            </div>
        </div>
    </div>
</aside>
