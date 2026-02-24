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
            oldDob: {{ Js::from(old('dob')) }},
            oldPincode: {{ Js::from(old('pincode')) }}
        })" @submit="loading = true">
        @csrf

        <div class="space-y-6">
            {{-- Full Name --}}
            <div>
                <label for="name" class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Full Name <span class="text-red-500">*</span></label>
                <input id="name" type="text" name="name" x-model="name" required autofocus
                    placeholder="Enter your full name"
                    class="w-full px-4 py-3.5 bg-navy/20 border border-primary/10 rounded-xl text-mainText placeholder-slate-500 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-bold text-sm">
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            {{-- Email Group --}}
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
                            Emails match perfectly!
                        </p>
                    </template>
                </div>
            </div>

            {{-- Mobile & Gender --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="mobile" class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Mobile Number <span class="text-red-500">*</span></label>
                    <div class="flex">
                        <span class="inline-flex items-center px-4 rounded-l-xl border border-r-0 border-primary/10 bg-navy/40 text-primary text-sm font-black">
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
                    <div class="relative">
                        <select id="gender" name="gender" required class="w-full px-4 py-3.5 bg-navy/20 border border-primary/10 rounded-xl text-mainText focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-bold text-sm appearance-none cursor-pointer">
                            <option value="">Select Gender</option>
                            <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                            <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                            <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-mutedText text-xs pointer-events-none"></i>
                    </div>
                </div>
            </div>

            {{-- DOB & State --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                <div>
                    <label for="dob" class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Date of Birth <span class="text-red-500">*</span></label>
                    <input id="dob" type="date" name="dob" x-model="dob" required
                        class="w-full px-4 py-3.5 bg-navy/20 border border-primary/10 rounded-xl text-mainText focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-bold text-sm">
                </div>

                <div>
                    <label for="state" class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">State/Region <span class="text-red-500">*</span></label>
                    <div class="relative">
                        <select id="state" name="state" required class="w-full px-4 py-3.5 bg-navy/20 border border-primary/10 rounded-xl text-mainText focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-bold text-sm appearance-none cursor-pointer">
                            <option value="">Select State</option>
                            @foreach(['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal','Andaman and Nicobar Islands','Chandigarh','Dadra and Nagar Haveli and Daman and Diu','Lakshadweep','Delhi','Puducherry','Ladakh','Jammu and Kashmir'] as $state)
                                <option value="{{ $state }}" {{ old('state') == $state ? 'selected' : '' }}>{{ $state }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-mutedText text-xs pointer-events-none"></i>
                    </div>
                </div>
            </div>

            {{-- Passwords --}}
            <div x-data="{ showPass: false, showConfirmPass: false }" class="space-y-5">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
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

                    <div>
                        <label for="password_confirmation" class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Confirm Password <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <input id="password_confirmation" :type="showConfirmPass ? 'text' : 'password'" name="password_confirmation" required
                                placeholder="Confirm password"
                                x-model="confirmPassword"
                                @input.debounce.500ms="checkPasswordMatch()"
                                class="w-full px-4 py-3.5 bg-navy/20 border border-primary/10 rounded-xl text-mainText placeholder-slate-500 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all font-bold text-sm"
                                :class="{'ring-1 ring-red-500 border-red-500': matchError}">
                            <button type="button" @click="showConfirmPass = !showConfirmPass" class="absolute inset-y-0 right-3 flex items-center text-slate-400 hover:text-primary transition-all">
                                 <svg x-show="!showConfirmPass" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                 <svg x-show="showConfirmPass" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                        <template x-if="matchError">
                             <p class="text-[10px] text-red-500 font-bold mt-1.5" x-text="matchError"></p>
                        </template>
                        <template x-if="password && confirmPassword && !matchError && !passwordError">
                             <p class="text-[10px] text-green-500 font-bold mt-1.5 flex items-center bg-green-500/5 py-1 px-2 rounded-lg w-fit">
                                 <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                                 Passwords match!
                             </p>
                        </template>
                    </div>
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

                // Other details
                mobile: config.oldMobile || '',
                dob: config.oldDob || '',
                pincode: config.oldPincode || '',

                // Password fields
                password: '',
                confirmPassword: '',
                passwordStrength: '',
                passwordError: '',
                matchError: '',

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

                checkPasswordMatch() {
                    this.matchError = '';
                    if (!this.confirmPassword || this.confirmPassword.length === 0) return;

                    if (this.password !== this.confirmPassword) {
                        this.matchError = 'Passwords do not match.';
                    }
                },

                isFormInvalid() {
                    return this.emailError !== '' ||
                           this.emailMatchError !== '' ||
                           this.passwordError !== '' ||
                           this.matchError !== '' ||
                           (this.password !== '' && this.password !== this.confirmPassword) ||
                           (this.email !== '' && this.confirmEmail !== '' && this.email !== this.confirmEmail) ||
                           this.name === '' ||
                           this.email === '';
                }
            }));
        });
    </script>
    @endpush
</x-guest-layout>
