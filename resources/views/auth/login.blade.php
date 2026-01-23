<x-guest-layout>
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <div class="text-center mb-8">
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Welcome Back!</h1>
        <p class="text-slate-500 mt-1 text-sm">Please sign in to continue.</p>
    </div>

    <form method="POST" action="{{ route('login') }}" class="space-y-5" x-data="{ loading: false, showPassword: false }"
        @submit="loading = true">
        @csrf

        <div class="space-y-1">
            <label for="email" class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Email</label>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </div>
                <input id="email"
                    class="pl-10 w-full bg-white/60 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-3 transition"
                    type="email" name="email" :value="old('email')" required autofocus autocomplete="username"
                    placeholder="name@example.com">
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <div class="space-y-1">
            <div class="flex justify-between items-center ml-1">
                <label for="password" class="text-xs font-bold uppercase tracking-wider text-slate-500">Password</label>
                @if (Route::has('password.request'))
                    <a class="text-xs font-semibold text-indigo-600 hover:text-indigo-800 transition"
                        href="{{ route('password.request') }}">
                        Forgot password?
                    </a>
                @endif
            </div>
            <div class="relative">
                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                    </svg>
                </div>

                <input id="password" :type="showPassword ? 'text' : 'password'"
                    class="pl-10 pr-10 w-full bg-white/60 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-3 transition"
                    name="password" required autocomplete="current-password" placeholder="••••••••">

                <button type="button" @click="showPassword = !showPassword"
                    class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-indigo-600 cursor-pointer focus:outline-none">
                    <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                    </svg>
                    <svg x-show="showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                        style="display: none;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                    </svg>
                </button>
            </div>
            <x-input-error :messages="$errors->get('password')" class="mt-1" />
        </div>

        <div class="flex items-center">
            <input id="remember_me" type="checkbox"
                class="w-4 h-4 text-indigo-600 border-slate-300 rounded focus:ring-indigo-500" name="remember">
            <label for="remember_me" class="ml-2 block text-sm text-slate-600">Remember me</label>
        </div>

        <button type="submit"
            class="w-full py-3 px-4 rounded-xl shadow-lg shadow-indigo-500/30 text-white font-bold text-sm uppercase tracking-wider bg-indigo-600 hover:bg-indigo-700 transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
            <span x-show="!loading">Log In</span>
            <span x-show="loading" class="flex items-center justify-center" style="display: none;">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                    </path>
                </svg>
                Processing...
            </span>
        </button>

        <div class="mt-4 text-center">
            <p class="text-sm text-slate-500">
                New here?
                <a href="{{ route('register') }}"
                    class="font-bold text-indigo-600 hover:text-indigo-800 transition">Create Account</a>
            </p>
        </div>
    </form>
</x-guest-layout>
