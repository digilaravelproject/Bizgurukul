<x-guest-layout>
    <div class="space-y-3 text-center px-4">
        <h1 class="text-3xl md:text-5xl font-black text-mainText tracking-tight">Create Account</h1>
        <p class="text-mutedText text-sm md:text-base font-medium">Join us today! Enter your details to get started.</p>
    </div>

    <form method="POST" action="{{ route('register.phase1.store') }}" class="mt-10 space-y-6 px-2 md:px-0"
        x-data="registerForm({
            email: {{ Js::from($email ?? old('email')) }},
            isLocked: {{ $email ? 'true' : 'false' }},
            oldName: {{ Js::from(old('name')) }},
            oldMobile: {{ Js::from(old('mobile')) }},
            intent: {{ Js::from($intent ?? old('intent')) }},
            target_bundle_id: {{ Js::from($target_bundle_id ?? old('target_bundle_id')) }}
        })" @submit="loading = true">
        @csrf

        <input type="hidden" name="intent" :value="intent">
        <input type="hidden" name="target_bundle_id" :value="target_bundle_id">

        <div class="space-y-6">
            {{-- Full Name --}}
            <div>
                <label for="name" class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Full Name <span class="text-red-500">*</span></label>
                <input id="name" type="text" name="name" x-model="name" required autofocus
                    placeholder="Enter your full name"
                    class="w-full px-4 py-3.5 bg-navy/20 border border-primary/10 rounded-xl text-mainText placeholder-slate-500 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-bold text-sm">
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            {{-- Row 1: Email & Confirm Email --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Email Address <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input type="hidden" name="email" :value="email">
                        <input id="email_real" type="email"
                            x-show="!showMasked"
                            x-model="email"
                            @blur="if(email.length > 0) showMasked = true"
                            @input.debounce.500ms="validateEmail(email); checkEmailMatch()"
                            :readonly="isLocked"
                            placeholder="name@example.com"
                            class="w-full px-4 py-3.5 bg-navy/20 border border-primary/10 rounded-xl text-mainText placeholder-slate-500 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-bold text-sm"
                            :class="{'opacity-75 cursor-not-allowed': isLocked}">

                        <input id="email_display" type="text"
                            x-show="showMasked"
                            :value="maskedEmailDisplay"
                            @focus="if(!isLocked) showMasked = false"
                            :readonly="true"
                            placeholder="name@example.com"
                            class="w-full px-4 py-3.5 bg-navy/20 border border-primary/10 rounded-xl text-mainText placeholder-slate-500 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-bold text-sm cursor-pointer"
                            :class="{'opacity-75 cursor-not-allowed': isLocked}">

                        <div class="absolute inset-y-0 right-3 flex items-center" x-show="isLocked">
                            <button type="button" @click="isLocked = false; showMasked = false; $nextTick(() => document.getElementById('email_real').focus())"
                                class="text-[10px] uppercase font-black bg-primary/10 text-primary px-2.5 py-1.5 rounded-lg hover:bg-primary/20 transition-all">
                                Change
                            </button>
                        </div>
                    </div>
                    <template x-if="emailError">
                        <p class="text-[10px] text-red-500 font-bold mt-1.5" x-text="emailError"></p>
                    </template>
                    <x-input-error :messages="$errors->get('email')" class="mt-1" />
                </div>

                <div>
                    <label for="email_confirmation" class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Confirm Email <span class="text-red-500">*</span></label>
                    <input id="email_confirmation" type="email" name="email_confirmation" required
                        x-model="confirmEmail"
                        @input.debounce.500ms="checkEmailMatch()"
                        placeholder="Re-enter your email"
                        class="w-full px-4 py-3.5 bg-navy/20 border border-primary/10 rounded-xl text-mainText placeholder-slate-500 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-bold text-sm"
                        :class="{'ring-1 ring-red-500 border-red-500': emailMatchError}">

                    <template x-if="emailMatchError">
                        <p class="text-[10px] text-red-500 font-bold mt-1.5" x-text="emailMatchError"></p>
                    </template>
                    <template x-if="email && confirmEmail && !emailMatchError && !emailError">
                        <p class="text-[10px] text-green-500 font-bold mt-1.5 flex items-center bg-green-500/5 py-1 px-2 rounded-lg w-fit">
                            <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                            Emails match!
                        </p>
                    </template>
                </div>
            </div>

            {{-- Row 2: Mobile & Gender --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="mobile" class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Mobile Number <span class="text-red-500">*</span></label>
                    <div class="flex group">
                        <span class="inline-flex items-center px-4 rounded-l-xl border border-r-0 border-primary/10 bg-navy/40 text-primary text-sm font-black transition-colors group-focus-within:border-primary group-focus-within:bg-primary/10">
                            +91
                        </span>
                        <input id="mobile" type="text" name="mobile" x-model="mobile" required
                            placeholder="9876543210" maxlength="10"
                            class="flex-1 min-w-0 block w-full px-4 py-3.5 bg-navy/20 border border-primary/10 rounded-r-xl text-mainText placeholder-slate-500 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-bold text-sm">
                    </div>
                    <x-input-error :messages="$errors->get('mobile')" class="mt-1" />
                </div>

                <div>
                    <label for="gender" class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Gender <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <select id="gender" name="gender" required 
                            class="w-full px-4 py-3.5 bg-navy/20 border border-primary/10 rounded-xl text-mainText focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-bold text-sm appearance-none cursor-pointer hover:bg-navy/30 hover:border-primary/30">
                            <option value="" class="bg-navy">Select Gender</option>
                            <option value="male" class="bg-navy" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" class="bg-navy" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" class="bg-navy" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-mutedText group-hover:text-primary transition-colors">
                            <svg class="w-4 h-4 transition-transform duration-300 group-focus-within:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('gender')" class="mt-1" />
                </div>
            </div>

            {{-- Row 3: State & Password --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5" x-data="{ showPass: false }">
                <div>
                    <label for="state_id" class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">State/Region <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <select id="state_id" name="state_id" required 
                            class="w-full px-4 py-3.5 bg-navy/20 border border-primary/10 rounded-xl text-mainText focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-bold text-sm appearance-none cursor-pointer hover:bg-navy/30 hover:border-primary/30">
                            <option value="" class="bg-navy">Select State</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}" class="bg-navy" {{ old('state_id') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute right-4 top-1/2 -translate-y-1/2 pointer-events-none text-mutedText group-hover:text-primary transition-colors">
                            <svg class="w-4 h-4 transition-transform duration-300 group-focus-within:rotate-180" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                            </svg>
                        </div>
                    </div>
                    <x-input-error :messages="$errors->get('state_id')" class="mt-1" />
                </div>

                <div>
                    <label for="password" class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Create Password <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <input id="password" :type="showPass ? 'text' : 'password'" name="password" required
                            placeholder="Min 8 characters"
                            x-model="password"
                            @input.debounce.500ms="checkPasswordStrength()"
                            class="w-full px-4 py-3.5 bg-navy/20 border border-primary/10 rounded-xl text-mainText placeholder-slate-500 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-bold text-sm"
                            :class="{'ring-1 ring-red-500 border-red-500': passwordError}">
                        <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-primary transition-all">
                             <svg x-show="!showPass" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                             <svg x-show="showPass" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                        </button>
                    </div>
                    <template x-if="passwordError">
                         <p class="text-[10px] text-red-500 font-bold mt-1.5" x-text="passwordError"></p>
                    </template>
                    <template x-if="passwordStrength && !passwordError && password.length > 0">
                        <p class="text-[10px] mt-1.5 font-black uppercase tracking-wider" :class="{
                            'text-red-500': passwordStrength === 'Weak',
                            'text-yellow-500': passwordStrength === 'Medium',
                            'text-green-500': passwordStrength === 'Strong'
                        }">Strength: <span x-text="passwordStrength"></span></p>
                    </template>
                </div>
            </div>

        </div>

        <button type="submit"
            :disabled="loading || isFormInvalid()"
            class="w-full mt-8 flex justify-center items-center py-4 px-4 rounded-2xl text-white text-lg font-black bg-gradient-to-r from-primary to-secondary hover:opacity-90 transition-all duration-300 shadow-xl shadow-primary/20 transform active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed uppercase tracking-wider">
            <span x-show="!loading">Next: Select Product</span>
            <span x-show="loading" x-cloak class="flex items-center">
                <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
            </span>
        </button>
    </form>

    <!-- Footer Links & Copyright -->
    <div class="mt-8 space-y-4 text-center">
        <p class="text-sm text-mutedText font-medium">
            Already have an account?
            <a href="{{ route('login') }}" class="font-black text-primary hover:text-secondary transition-colors underline decoration-primary/20 underline-offset-4">Log In</a>
        </p>
    </div>

    @push('scripts')
    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('registerForm', (config) => ({
                loading: false,
                name: config.oldName || '',

                // Email fields
                email: config.email || '',
                confirmEmail: '',
                isLocked: config.isLocked || false,
                showMasked: config.isLocked || (config.email ? true : false),
                emailError: '',
                emailMatchError: '',

                // Intent fields
                intent: config.intent || '',
                target_bundle_id: config.target_bundle_id || '',

                // Other details
                mobile: config.oldMobile || '',

                // Password fields
                password: '',
                passwordStrength: '',
                passwordError: '',

                init() {
                    // Initial validation if email exists
                    if (this.email) {
                        this.validateEmail(this.email);
                        this.showMasked = true;
                    }
                },

                // Dynamic Masking Logic: aXXXe@gXXXl.com
                get maskedEmailDisplay() {
                    if (!this.email || !this.email.includes('@')) return this.email;

                    const [localPart, domainPart] = this.email.split('@');

                    // Mask Local Part (Before @)
                    let maskedLocal = localPart;
                    if (localPart.length > 2) {
                        maskedLocal = localPart[0] + 'XXX' + localPart[localPart.length - 1];
                    } else if (localPart.length === 2) {
                        maskedLocal = localPart[0] + 'X';
                    } else if (localPart.length === 1) {
                        maskedLocal = 'X';
                    }

                    // Mask Domain Part (After @)
                    let maskedDomain = domainPart;
                    const domainSplit = domainPart.split('.');
                    if (domainSplit.length >= 2) {
                        const extension = domainSplit.pop();
                        const domainName = domainSplit.join('.');

                        if (domainName.length > 2) {
                            maskedDomain = domainName[0] + 'XXX' + domainName[domainName.length - 1] + '.' + extension;
                        } else {
                            maskedDomain = domainName + '.' + extension;
                        }
                    }

                    return `${maskedLocal}@${maskedDomain}`;
                },

                validateEmail(emailVal) {
                    this.emailError = '';
                    if (!emailVal) return;

                    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
                    if (!regex.test(emailVal)) {
                        this.emailError = 'Please enter a valid email address.';
                    }
                },

                checkEmailMatch() {
                    this.emailMatchError = '';
                    if (!this.confirmEmail || this.confirmEmail.length === 0) return;

                    if (this.email !== this.confirmEmail) {
                        this.emailMatchError = 'Emails do not match.';
                    }
                },

                checkPasswordStrength() {
                    this.passwordError = '';
                    this.passwordStrength = '';

                    if (!this.password || this.password.length === 0) {
                        this.checkPasswordMatch();
                        return;
                    }

                    if (this.password.length < 8) {
                        this.passwordError = 'Password must be at least 8 characters.';
                        this.passwordStrength = 'Weak';
                        this.checkPasswordMatch();
                        return;
                    }

                    let strength = 0;
                    if (/[A-Z]/.test(this.password)) strength++;
                    if (/[a-z]/.test(this.password)) strength++;
                    if (/[0-9]/.test(this.password)) strength++;
                    if (/[^A-Za-z0-9]/.test(this.password)) strength++;

                    if (strength <= 2) {
                        this.passwordStrength = 'Weak';
                    } else if (strength === 3) {
                        this.passwordStrength = 'Medium';
                    } else {
                        this.passwordStrength = 'Strong';
                    }

                    this.checkPasswordMatch();
                },


                isFormInvalid() {
                    return this.emailError !== '' ||
                           this.emailMatchError !== '' ||
                           this.passwordError !== '' ||
                           (this.email !== '' && this.confirmEmail !== '' && this.email !== this.confirmEmail) ||
                           this.name === '' ||
                           this.email === '';
                }
            }));
        });
    </script>
    @endpush
</x-guest-layout>
