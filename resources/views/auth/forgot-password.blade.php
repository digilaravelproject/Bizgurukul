<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-mainText tracking-tight">Forgot Password?</h1>
        <p class="text-mutedText text-sm mt-3 leading-relaxed px-4">
            {{ __('No problem. Enter your email address below and we will send you a secure link to reset your password.') }}
        </p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-6" :status="session('status')" />

    <form method="POST" action="{{ route('password.email') }}" x-data="{ loading: false }" @submit="loading = true" class="space-y-6">
        @csrf

        <!-- Email Address -->
        <div>
            <label for="email" class="block text-sm font-medium text-mainText mb-1.5">Email Address</label>
            <div class="relative">
                <input id="email" type="email" name="email" :value="old('email')" required autofocus
                    class="w-full px-4 py-3 bg-navy/30 border border-slate-200 rounded-lg text-mainText placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200"
                    placeholder="name@example.com">

                <!-- Icon -->
                <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                     <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                    </svg>
                </div>
            </div>
            <x-input-error :messages="$errors->get('email')" class="mt-1" />
        </div>

        <!-- Submit Button -->
        <button type="submit"
            :disabled="loading"
            class="w-full flex justify-center items-center py-3.5 px-4 rounded-lg text-white font-bold bg-gradient-to-r from-primary to-secondary hover:opacity-90 transition-all duration-300 shadow-lg shadow-primary/20 transform active:scale-[0.98]">

            <span x-show="!loading">Email Password Reset Link</span>

            <span x-show="loading" x-cloak class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Sending...
            </span>
        </button>

        <!-- Back to Login Link -->
        <div class="text-center pt-2">
            <a href="{{ route('login') }}" class="inline-flex items-center text-sm font-semibold text-mutedText hover:text-primary transition group">
                <svg class="w-4 h-4 mr-2 transform group-hover:-translate-x-1 transition" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                </svg>
                Back to Log In
            </a>
        </div>
    </form>
</x-guest-layout>
