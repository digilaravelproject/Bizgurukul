<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-6">
         <h1 class="text-3xl font-bold text-mainText tracking-tight">Verify Your Email</h1>
    </div>

    <!-- Text Content -->
    <div class="mb-8 text-sm text-mutedText leading-relaxed text-center px-4">
        {{ __('Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn\'t receive the email, we will gladly send you another.') }}
    </div>

    @if (session('status') == 'verification-link-sent')
        <div class="mb-6 p-4 rounded-lg bg-green-50 border-l-4 border-green-500 text-green-700 text-sm flex items-start">
            <svg class="h-5 w-5 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ __('A new verification link has been sent to the email address you provided during registration.') }}
        </div>
    @endif

    <div class="space-y-4" x-data="{ loading: false }">
        <form method="POST" action="{{ route('verification.send') }}" @submit="loading = true">
            @csrf

            <button type="submit"
                :disabled="loading"
                class="w-full flex justify-center items-center py-3.5 px-4 rounded-lg text-white font-bold bg-gradient-to-r from-primary to-secondary hover:opacity-90 transition-all duration-300 shadow-lg shadow-primary/20 transform active:scale-[0.98]">

                <span x-show="!loading">{{ __('Resend Verification Email') }}</span>

                <span x-show="loading" x-cloak class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Sending...
                </span>
            </button>
        </form>

        <form method="POST" action="{{ route('logout') }}" class="text-center">
            @csrf

            <button type="submit" class="text-sm font-semibold text-mutedText hover:text-primary transition underline decoration-transparent hover:decoration-primary">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>
</x-guest-layout>
