<x-guest-layout>
    <div class="text-center mb-6">
        <h1 class="text-2xl font-bold text-slate-800 tracking-tight">Create Account</h1>
        <p class="text-slate-500 mt-1 text-sm">Join the platform in seconds.</p>
    </div>

    {{-- Global Error Alert --}}
    @if($errors->has('error'))
        <div class="mb-4 p-3 rounded-xl bg-red-50 border border-red-200 text-red-600 text-sm font-medium">
            {{ $errors->first('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}" x-data="{ loading: false, showPass: false }"
        @submit="loading = true">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">

            {{-- Full Name --}}
            <div class="md:col-span-2 space-y-1">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Full Name</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
                        <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                    </div>
                    <input type="text" name="name" value="{{ old('name') }}" required autofocus
                        class="pl-10 w-full bg-white/60 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-2.5"
                        placeholder="e.g. Rahul Sharma">
                </div>
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            {{-- Email --}}
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Email</label>
                <input type="email" name="email" value="{{ old('email') }}" required
                    class="w-full bg-white/60 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-2.5"
                    placeholder="you@example.com">
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            {{-- Mobile --}}
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Mobile</label>
                <input type="text" name="mobile" value="{{ old('mobile') }}"
                    class="w-full bg-white/60 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-2.5"
                    placeholder="98765xxxxx">
            </div>

            {{-- Gender --}}
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Gender</label>
                <select name="gender"
                    class="w-full bg-white/60 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-2.5">
                    <option value="">Select</option>
                    <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                    <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                    <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                </select>
            </div>

            {{-- DOB --}}
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">DOB</label>
                <input type="date" name="dob" value="{{ old('dob') }}"
                    class="w-full bg-white/60 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-2.5">
            </div>

            {{-- State --}}
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">State</label>
                <select name="state_id"
                    class="w-full bg-white/60 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-2.5">
                    <option value="">Select State</option>
                    @foreach ($states as $state)
                        <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>
                            {{ $state->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- City --}}
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">City</label>
                <input type="text" name="city" value="{{ old('city') }}" placeholder="City"
                    class="w-full bg-white/60 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-2.5">
            </div>

            {{-- Referral Section --}}
            <div class="md:col-span-2 mt-2" x-data="{
                    showInput: {{ request()->has('ref') || \Illuminate\Support\Facades\Cookie::get('referral_code') || old('referral_code') ? 'true' : 'false' }},
                    refCode: '{{ request()->get('ref') ?? \Illuminate\Support\Facades\Cookie::get('referral_code') ?? old('referral_code') }}',
                    isReadOnly: {{ request()->has('ref') || \Illuminate\Support\Facades\Cookie::get('referral_code') ? 'true' : 'false' }},
                    message: '',
                    status: '',
                    checkCode() {
                        if(this.refCode.length > 0 && !this.isReadOnly) {
                            fetch('{{ route('check.referral') }}', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                                },
                                body: JSON.stringify({ code: this.refCode })
                            })
                            .then(res => res.json())
                            .then(data => {
                                this.status = data.status;
                                this.message = data.message;
                            });
                        }
                    }
                }" x-init="if(isReadOnly) checkCode()">

                <div x-show="!showInput" class="text-right">
                    <button type="button" @click="showInput = true"
                        class="text-sm text-indigo-600 font-semibold hover:underline">
                        Have a referral code?
                    </button>
                </div>

                <div x-show="showInput" x-transition class="space-y-1">
                    <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Referral Code
                        (Optional)</label>
                    <div class="relative">
                        <input type="text" name="referral_code" x-model="refCode" :readonly="isReadOnly"
                            @blur="checkCode()"
                            class="w-full bg-white/60 border text-slate-800 text-sm rounded-xl block p-2.5 transition-all"
                            :class="status === 'valid' ? 'border-green-500 ring-1 ring-green-500' : (status === 'invalid' ? 'border-red-500 ring-1 ring-red-500' : 'border-slate-200') "
                            placeholder="Enter Code">

                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <template x-if="status === 'valid'">
                                <svg class="h-5 w-5 text-green-500" fill="none" viewBox="0 0 24 24"
                                    stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M5 13l4 4L19 7" />
                                </svg>
                            </template>
                            <template x-if="status === 'invalid'">
                                <svg class="h-5 w-5 text-red-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </template>
                        </div>
                    </div>
                    <p x-text="message" x-show="message" class="text-[10px] font-bold mt-1"
                        :class="status === 'valid' ? 'text-green-600' : 'text-red-600'"></p>
                </div>
            </div>

            {{-- Passwords --}}
            <div class="space-y-1">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Password</label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password" required
                        class="w-full bg-white/60 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 pr-10"
                        placeholder="••••••••">
                    <button type="button" @click="showPass = !showPass"
                        class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-indigo-600">
                        <svg x-show="!showPass" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showPass" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"
                            style="display: none;">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <div class="space-y-1">
                <label class="text-xs font-bold uppercase tracking-wider text-slate-500 ml-1">Confirm</label>
                <input :type="showPass ? 'text' : 'password'" name="password_confirmation" required
                    class="w-full bg-white/60 border border-slate-200 text-slate-800 text-sm rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 block p-2.5 pr-10"
                    placeholder="••••••••">
            </div>
        </div>

        {{-- Submit Button --}}
        <div class="mt-6">
            <button type="submit"
                class="w-full py-3 px-4 rounded-xl shadow-lg shadow-green-500/30 text-white font-bold text-sm uppercase tracking-wider bg-green-600 hover:bg-green-700 transition-all duration-300 transform hover:-translate-y-0.5 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-green-500">
                <span x-show="!loading">Create Premium Account</span>
                <span x-show="loading" class="flex items-center justify-center" style="display: none;">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                        </circle>
                        <path class="opacity-75" fill="currentColor"
                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                        </path>
                    </svg>
                    Setting up...
                </span>
            </button>
        </div>

        <div class="mt-4 text-center">
            <p class="text-sm text-slate-500">
                Have an account?
                <a href="{{ route('login') }}" class="font-bold text-indigo-600 hover:text-indigo-800 transition">Sign
                    In</a>
            </p>
        </div>
    </form>
</x-guest-layout>
