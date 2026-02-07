<x-guest-layout>
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-mainText tracking-tight">One Last Step!</h1>
        <p class="text-mutedText text-sm mt-1">Do you have a referral code?</p>
    </div>

    <form method="POST" action="{{ route('onboarding.referral.store') }}" x-data="{
        code: '{{ $referralCode ?? '' }}',
        status: '',
        message: '',
        loading: false,
        checkCode() {
            if(this.code.length > 0) {
                fetch('{{ route('check.referral') }}', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': '{{ csrf_token() }}' },
                    body: JSON.stringify({ code: this.code })
                })
                .then(res => res.json())
                .then(data => {
                    this.status = data.status;
                    this.message = data.message;
                });
            } else {
                this.status = '';
                this.message = '';
            }
        }
    }" x-init="if(code) checkCode()" class="space-y-6">
        @csrf

        <div class="bg-navy/50 p-6 rounded-2xl border border-dashed border-primary/20">

            <label class="block text-xs font-bold uppercase tracking-wider text-mutedText mb-2">Referral Code</label>

            <div class="relative">
                <input type="text" name="referral_code" x-model="code"
                    @input.debounce.500ms="checkCode()"
                    class="w-full px-5 py-4 bg-white border-2 rounded-xl text-lg font-bold text-center tracking-widest text-mainText placeholder-slate-300 focus:outline-none transition-all duration-200"
                    :class="status === 'valid' ? 'border-green-500 text-green-700' : (status === 'invalid' ? 'border-red-500 text-red-700' : 'border-slate-200 focus:border-primary') "
                    placeholder="ENTER CODE">

                <!-- Status Indicator -->
                <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                    <template x-if="status === 'valid'">
                        <div class="w-8 h-8 rounded-full bg-green-100 flex items-center justify-center text-green-600">
                             <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </div>
                    </template>
                    <template x-if="status === 'invalid'">
                         <div class="w-8 h-8 rounded-full bg-red-100 flex items-center justify-center text-red-600">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </div>
                    </template>
                </div>
            </div>

            <p x-text="message" x-show="message" class="text-center text-sm font-bold mt-3"
                :class="status === 'valid' ? 'text-green-600' : 'text-red-500'"></p>

            <div x-show="code && status === 'valid'" class="mt-4 p-3 bg-green-50 rounded-lg text-center text-xs text-green-700 font-medium border border-green-100 animate-fade-in">
                Referral discount/benefits will be applied to your account.
            </div>
        </div>

        <div class="flex flex-col gap-3">
            <button type="submit"
                :disabled="code && status !== 'valid'"
                class="w-full flex justify-center py-4 px-4 rounded-xl text-white font-bold text-lg bg-gradient-to-r from-primary to-secondary hover:opacity-90 transition-all shadow-lg shadow-primary/20 disabled:opacity-50 disabled:cursor-not-allowed">
                Continue
            </button>

            <a href="{{ route('onboarding.skip') }}" class="w-full py-4 text-center text-sm font-bold text-slate-400 hover:text-slate-600 transition tracking-wide uppercase">
                I don't have a code, Skip
            </a>
        </div>
    </form>
</x-guest-layout>
