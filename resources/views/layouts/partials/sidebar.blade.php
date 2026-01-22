<div class="bg-slate-900 border-end sidebar flex flex-col justify-between"
    :class="sidebarOpen ? 'w-[260px]' : 'w-0 overflow-hidden'"
    style="min-height: 100vh; transition: all 0.3s ease-in-out;">

    <div class="h-[60px] flex items-center justify-center bg-indigo-600 shadow-md">
        <h3 class="text-white font-bold text-xl tracking-wide uppercase m-0">Admin Panel</h3>
    </div>

    <div class="flex-1 py-6 overflow-y-auto">
        <nav class="space-y-2 px-3">
            <a href="{{ route('dashboard') }}"
                class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('dashboard') ? 'bg-indigo-600 text-white shadow-lg translate-x-1' : 'text-slate-400 hover:bg-slate-800 hover:text-white hover:translate-x-1' }}">
                <span class="mr-3 text-lg">📊</span>
                <span class="font-medium">Dashboard</span>
            </a>

            <p class="px-4 mt-6 mb-2 text-xs font-semibold text-slate-500 uppercase tracking-wider">Management</p>

            @can('role-list')
                <a href="{{ route('roles.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('roles.*') ? 'bg-indigo-600 text-white shadow-lg translate-x-1' : 'text-slate-400 hover:bg-slate-800 hover:text-white hover:translate-x-1' }}">
                    <span class="mr-3 text-lg">🛡️</span>
                    <span class="font-medium">Manage Roles</span>
                </a>
            @endcan

            @can('user-list')
                <a href="{{ route('users.index') }}"
                    class="flex items-center px-4 py-3 rounded-lg transition-all duration-200 group {{ request()->routeIs('users.*') ? 'bg-indigo-600 text-white shadow-lg translate-x-1' : 'text-slate-400 hover:bg-slate-800 hover:text-white hover:translate-x-1' }}">
                    <span class="mr-3 text-lg">👥</span>
                    <span class="font-medium">Manage Users</span>
                </a>
            @endcan
        </nav>
    </div>

    <div class="p-4 bg-slate-800 border-t border-slate-700">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-full bg-indigo-500 flex items-center justify-center text-white font-bold">
                {{ substr(Auth::user()->name, 0, 1) }}
            </div>
            <div>
                <p class="text-sm font-medium text-white m-0">{{ Auth::user()->name }}</p>
                <p class="text-xs text-slate-400 m-0">Administrator</p>
            </div>
        </div>
    </div>

</div>
