<nav class="navbar navbar-expand-lg bg-white shadow-sm h-[60px] px-4 sticky top-0 z-50">
    <div class="d-flex align-items-center w-full">

        <button class="btn border-0 text-slate-600 hover:text-indigo-600 transition-colors"
            @click="sidebarOpen = !sidebarOpen">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h7" />
            </svg>
        </button>

        <div class="hidden md:flex ms-4 items-center bg-slate-100 rounded-md px-3 py-1.5 w-64">
            <span class="text-slate-400">🔍</span>
            <input type="text" placeholder="Search..."
                class="bg-transparent border-none outline-none text-sm ms-2 w-full text-slate-600">
        </div>

        <div class="ms-auto flex items-center gap-3">

            <button class="relative p-2 text-slate-500 hover:text-indigo-600 transition">
                🔔
                <span class="absolute top-1 right-1 h-2 w-2 rounded-full bg-red-500"></span>
            </button>

            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button
                    class="px-4 py-1.5 bg-red-50 text-red-600 rounded-md hover:bg-red-600 hover:text-white transition-all text-sm font-medium border border-red-200">
                    Logout
                </button>
            </form>
        </div>
    </div>
</nav>
