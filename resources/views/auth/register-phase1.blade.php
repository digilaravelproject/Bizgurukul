<x-guest-layout>
    <div class="space-y-2 text-center">
        <h1 class="text-3xl font-bold text-mainText tracking-tight">Create Account</h1>
        <p class="text-mutedText text-sm">Join us today! Enter your details to get started.</p>
    </div>

    <form method="POST" action="{{ route('register.phase1.store') }}" class="mt-8 space-y-6" x-data="{ loading: false }" @submit="loading = true">
        @csrf

        <div class="space-y-5">
            <!-- Full Name -->
            <div>
                <label for="name" class="block text-sm font-medium text-mainText mb-1.5">Full Name</label>
                <input id="name" type="text" name="name" :value="old('name')" required autofocus
                    placeholder="John Doe"
                    class="w-full px-4 py-3 bg-navy/30 border border-slate-200 rounded-lg text-mainText placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200">
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <!-- Email & Confirm Email -->
            <div x-data="{
                email: '{{ $email ?? old('email') }}',
                maskedEmail: '',
                isLocked: '{{ $email ? 'true' : 'false' }}' === 'true',
                init() {
                    // if (this.isLocked) this.maskEmail();
                    // No complex masking needed if using type=password for first field as requested to be 'encrypted format'
                }
            }">
                 <!-- Hidden Real Email -->
                 <input type="hidden" name="email" :value="email">

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Email Field (Masked/Encrypted) -->
                    <div>
                        <label class="block text-sm font-medium text-mainText mb-1.5">Email Address</label>
                        <div class="relative">
                            <!-- Helper text or icon to indicate it's masked could be added, but relying on type="password" for 'encrypted format' -->
                            <input id="email_display" type="password"
                                :value="email"
                                @input="email = $event.target.value"
                                :readonly="isLocked"
                                required
                                placeholder="name@example.com"
                                class="w-full px-4 py-3 bg-navy/30 border border-slate-200 rounded-lg text-mainText placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200"
                                :class="{'opacity-75 cursor-not-allowed text-slate-400': isLocked}">

                            <div class="absolute inset-y-0 right-0 pr-3 flex items-center" x-show="isLocked">
                                <button type="button" @click="isLocked = false; $nextTick(() => $el.closest('.relative').querySelector('input').focus())" class="text-xs text-primary hover:underline">Change</button>
                            </div>
                        </div>
                        <x-input-error :messages="$errors->get('email')" class="mt-1" />
                    </div>

                    <!-- Confirm Email Field (Visible) -->
                    <div>
                        <label for="email_confirmation" class="block text-sm font-medium text-mainText mb-1.5">Confirm Email Address</label>
                        <input id="email_confirmation" type="email" name="email_confirmation" required
                            placeholder="Re-enter your email to confirm"
                            class="w-full px-4 py-3 bg-navy/30 border border-slate-200 rounded-lg text-mainText placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200">
                    </div>
                </div>
            </div>

            <!-- Mobile Number -->
            <div>
                <label for="mobile" class="block text-sm font-medium text-mainText mb-1.5">Mobile Number</label>
                <div class="flex">
                    <span class="inline-flex items-center px-4 rounded-l-md border border-r-0 border-slate-200 bg-navy/30 text-slate-400 sm:text-sm">
                        +91
                    </span>
                    <input id="mobile" type="text" name="mobile" :value="old('mobile')" required
                        placeholder="9876543210"
                        class="flex-1 min-w-0 block w-full px-4 py-3 bg-navy/30 border border-slate-200 rounded-lg text-mainText placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200">
                </div>
                <x-input-error :messages="$errors->get('mobile')" class="mt-1" />
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                 <!-- Gender -->
                <div>
                    <label for="gender" class="block text-sm font-medium text-mainText mb-1.5">Gender</label>
                    <select id="gender" name="gender" class="w-full px-4 py-3 bg-navy/30 border border-slate-200 rounded-lg text-mainText focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200">
                        <option value="">Select</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                <!-- DOB -->
                <div>
                    <label for="dob" class="block text-sm font-medium text-mainText mb-1.5">Date of Birth</label>
                    <input id="dob" type="date" name="dob" :value="old('dob')"
                        class="w-full px-4 py-3 bg-navy/30 border border-slate-200 rounded-lg text-mainText placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200">
                </div>
            </div>

            <!-- State -->
            <div>
                <label for="state" class="block text-sm font-medium text-mainText mb-1.5">State/Region</label>
                <select id="state" name="state" class="w-full px-4 py-3 bg-navy/30 border border-slate-200 rounded-lg text-mainText focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200">
                    <option value="">Select State</option>
                    @foreach(['Andhra Pradesh','Arunachal Pradesh','Assam','Bihar','Chhattisgarh','Goa','Gujarat','Haryana','Himachal Pradesh','Jharkhand','Karnataka','Kerala','Madhya Pradesh','Maharashtra','Manipur','Meghalaya','Mizoram','Nagaland','Odisha','Punjab','Rajasthan','Sikkim','Tamil Nadu','Telangana','Tripura','Uttar Pradesh','Uttarakhand','West Bengal','Andaman and Nicobar Islands','Chandigarh','Dadra and Nagar Haveli and Daman and Diu','Lakshadweep','Delhi','Puducherry','Ladakh','Jammu and Kashmir'] as $state)
                        <option value="{{ $state }}" {{ old('state') == $state ? 'selected' : '' }}>{{ $state }}</option>
                    @endforeach
                </select>
            </div>

            <!-- Password Fields with Visibility Toggle -->
            <div x-data="{ showPass: false, showConfirmPass: false }" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- Password -->
                    <div>
                        <label for="password" class="block text-sm font-medium text-mainText mb-1.5">Create Password</label>
                        <div class="relative">
                            <input id="password" :type="showPass ? 'text' : 'password'" name="password" required
                                placeholder="Create a strong password"
                                class="w-full px-4 py-3 bg-navy/30 border border-slate-200 rounded-lg text-mainText placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200">
                            <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-primary transition focus:outline-none">
                                 <svg x-show="!showPass" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                 <svg x-show="showPass" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                    </div>

                    <!-- Confirm Password -->
                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-mainText mb-1.5">Confirm Password</label>
                        <div class="relative">
                            <input id="password_confirmation" :type="showConfirmPass ? 'text' : 'password'" name="password_confirmation" required
                                placeholder="Confirm your password"
                                class="w-full px-4 py-3 bg-navy/30 border border-slate-200 rounded-lg text-mainText placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200">
                            <button type="button" @click="showConfirmPass = !showConfirmPass" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-primary transition focus:outline-none">
                                 <svg x-show="!showConfirmPass" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                                 <svg x-show="showConfirmPass" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                            </button>
                        </div>
                    </div>
                </div>
                 <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>
        </div>

        <button type="submit"
            :disabled="loading"
            class="w-full flex justify-center items-center py-3.5 px-4 rounded-lg text-white font-bold bg-gradient-to-r from-primary to-secondary hover:opacity-90 transition-all duration-300 shadow-lg shadow-primary/20 transform active:scale-[0.98]">
            <span x-show="!loading">Next: Select Product</span>
            <span x-show="loading" x-cloak class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
            </span>
        </button>
    </form>
</x-guest-layout>
