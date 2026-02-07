<x-guest-layout>
    <!-- Header -->
    <div class="text-center mb-8">
        <h1 class="text-3xl font-bold text-mainText tracking-tight">Create Account</h1>
        <p class="text-mutedText text-sm mt-1">Join the platform to start learning.</p>
    </div>

    <!-- Global Error -->
    @if($errors->has('error'))
        <div class="mb-6 p-4 rounded-lg bg-red-50 border-l-4 border-red-500 text-red-700 text-sm flex items-start">
            <svg class="h-5 w-5 mr-2 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            {{ $errors->first('error') }}
        </div>
    @endif

    <form method="POST" action="{{ route('register') }}"
          x-data="{
              loading: false,
              showPass: false,
              form: { email: '{{ old('email') }}', email_confirmation: '', password: '', password_confirmation: '' },
              matchEmail: true,
              matchPassword: true,
              checkMatch() {
                  this.matchEmail = !this.form.email_confirmation || (this.form.email === this.form.email_confirmation);
                  this.matchPassword = !this.form.password_confirmation || (this.form.password === this.form.password_confirmation);
              }
          }"
          @submit="loading = true" class="space-y-5">
        @csrf

        <!-- Grid Layout for Inputs -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

            <!-- Full Name (Full Width) -->
            <div class="md:col-span-2">
                <label class="block text-sm font-medium text-mainText mb-1.5">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="w-full px-4 py-3 bg-navy/30 border border-slate-200 rounded-lg text-mainText placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200"
                    placeholder="e.g. Rahul Sharma">
                <x-input-error :messages="$errors->get('name')" class="mt-1" />
            </div>

            <!-- Email -->
            <div>
                <label class="block text-sm font-medium text-mainText mb-1.5">Email Address</label>
                <input type="email" name="email" x-model="form.email" @input="checkMatch()" required
                    class="w-full px-4 py-3 bg-navy/30 border border-slate-200 rounded-lg text-mainText placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200"
                    placeholder="you@example.com">
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

             <!-- Confirm Email -->
             <div>
                <label class="block text-sm font-medium text-mainText mb-1.5">Confirm Email</label>
                <div class="relative">
                    <input type="email" name="email_confirmation" x-model="form.email_confirmation" @input="checkMatch()" required
                        class="w-full px-4 py-3 bg-navy/30 border rounded-lg text-mainText placeholder-slate-400 focus:outline-none focus:ring-1 transition duration-200"
                        :class="matchEmail ? 'border-slate-200 focus:border-primary focus:ring-primary' : 'border-red-500 focus:border-red-500 focus:ring-red-500'"
                        placeholder="Re-enter email">

                    <div x-show="form.email_confirmation && matchEmail" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-green-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div x-show="!matchEmail" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-red-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </div>
                </div>
                <p x-show="!matchEmail" class="text-xs text-red-500 mt-1">Emails do not match.</p>
            </div>

            <!-- Mobile -->
            <div>
                <label class="block text-sm font-medium text-mainText mb-1.5">Mobile Number</label>
                <input type="text" name="mobile" value="{{ old('mobile') }}"
                    class="w-full px-4 py-3 bg-navy/30 border border-slate-200 rounded-lg text-mainText placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200"
                    placeholder="98765xxxxx">
            </div>

            <!-- Gender -->
            <div>
                <label class="block text-sm font-medium text-mainText mb-1.5">Gender</label>
                <div class="relative">
                    <select name="gender" class="w-full px-4 py-3 bg-navy/30 border border-slate-200 rounded-lg text-mainText appearance-none focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200">
                        <option value="">Select</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            <!-- DOB -->
            <div>
                <label class="block text-sm font-medium text-mainText mb-1.5">Date of Birth</label>
                <input type="date" name="dob" value="{{ old('dob') }}"
                    class="w-full px-4 py-3 bg-navy/30 border border-slate-200 rounded-lg text-mainText placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200">
            </div>

            <!-- State -->
            <div>
                <label class="block text-sm font-medium text-mainText mb-1.5">State</label>
                <div class="relative">
                    <select name="state_id" class="w-full px-4 py-3 bg-navy/30 border border-slate-200 rounded-lg text-mainText appearance-none focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200">
                        <option value="">Select State</option>
                        @foreach ($states as $state)
                            <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>
                                {{ $state->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-slate-500">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            <!-- City -->
            <div>
                <label class="block text-sm font-medium text-mainText mb-1.5">City</label>
                <input type="text" name="city" value="{{ old('city') }}" placeholder="City Name"
                    class="w-full px-4 py-3 bg-navy/30 border border-slate-200 rounded-lg text-mainText placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200">
            </div>



            <!-- Password -->
            <div>
                <label class="block text-sm font-medium text-mainText mb-1.5">Password</label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password" x-model="form.password" @input="checkMatch()" required
                        class="w-full px-4 py-3 bg-navy/30 border border-slate-200 rounded-lg text-mainText placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200"
                        placeholder="••••••••">
                    <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-primary transition focus:outline-none">
                        <svg x-show="!showPass" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <svg x-show="showPass" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>

            <!-- Confirm Password -->
            <div>
                <label class="block text-sm font-medium text-mainText mb-1.5">Confirm Password</label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password_confirmation" x-model="form.password_confirmation" @input="checkMatch()" required
                        class="w-full px-4 py-3 bg-navy/30 border rounded-lg text-mainText placeholder-slate-400 focus:outline-none focus:ring-1 transition duration-200"
                        :class="matchPassword ? 'border-slate-200 focus:border-primary focus:ring-primary' : 'border-red-500 focus:border-red-500 focus:ring-red-500'"
                        placeholder="••••••••">

                    <div x-show="form.password_confirmation && matchPassword" class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-green-500">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>
                <p x-show="!matchPassword" class="text-xs text-red-500 mt-1">Passwords do not match.</p>
            </div>

        </div> <!-- End Grid -->

        <!-- Submit Button -->
        <div class="mt-6">
            <button type="submit"
                :disabled="loading || !matchEmail || !matchPassword"
                class="w-full flex justify-center items-center py-3.5 px-4 rounded-lg text-white font-bold bg-gradient-to-r from-primary to-secondary hover:opacity-90 transition-all duration-300 shadow-lg shadow-primary/20 transform active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">

                <span x-show="!loading">Create Premium Account</span>

                <span x-show="loading" x-cloak class="flex items-center">
                    <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Setting up...
                </span>
            </button>
        </div>

        <!-- Footer -->
        <p class="text-center text-sm text-mutedText">
            Already have an account?
            <a href="{{ route('login') }}" class="font-bold text-primary hover:text-secondary transition">Sign In</a>
        </p>
    </form>
</x-guest-layout>
