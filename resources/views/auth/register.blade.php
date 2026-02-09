<x-guest-layout>
    <div class="text-center mb-10 animate-fade-in-down">
        <h1 class="text-4xl font-black text-mainText tracking-tighter uppercase">Create Account</h1>
        <p class="text-mutedText text-sm mt-2 font-medium tracking-wide">Join our community and start your journey today.</p>
    </div>

    @if($errors->has('error'))
        <div class="mb-6 p-4 rounded-2xl bg-secondary/10 border border-secondary/20 text-secondary text-xs font-bold flex items-start animate-shake">
            <svg class="h-4 w-4 mr-2 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
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
          @submit="loading = true" class="space-y-6">
        @csrf

        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">

            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-mutedText uppercase tracking-widest mb-2 ml-1">Full Name</label>
                <input type="text" name="name" value="{{ old('name') }}" required autofocus
                    class="w-full px-5 py-4 bg-navy/10 border border-primary/10 rounded-2xl text-mainText font-bold placeholder-mutedText/30 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300"
                    placeholder="e.g. Rahul Sharma">
                <x-input-error :messages="$errors->get('name')" class="mt-1 ml-1" />
            </div>

            <div>
                <label class="block text-[10px] font-black text-mutedText uppercase tracking-widest mb-2 ml-1">Email Address</label>
                <input type="email" name="email" x-model="form.email" @input="checkMatch()" required
                    class="w-full px-5 py-4 bg-navy/10 border border-primary/10 rounded-2xl text-mainText font-bold placeholder-mutedText/30 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300"
                    placeholder="you@example.com">
                <x-input-error :messages="$errors->get('email')" class="mt-1 ml-1" />
            </div>

             <div>
                <label class="block text-[10px] font-black text-mutedText uppercase tracking-widest mb-2 ml-1">Confirm Email</label>
                <div class="relative">
                    <input type="email" name="email_confirmation" x-model="form.email_confirmation" @input="checkMatch()" required
                        class="w-full px-5 py-4 bg-navy/10 border rounded-2xl text-mainText font-bold placeholder-mutedText/30 focus:outline-none focus:ring-4 transition-all duration-300"
                        :class="matchEmail ? 'border-primary/10 focus:border-primary focus:ring-primary/10' : 'border-secondary/50 focus:border-secondary focus:ring-secondary/10'"
                        placeholder="Re-enter email">

                    <div x-show="form.email_confirmation && matchEmail" class="absolute inset-y-0 right-0 flex items-center pr-4 text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>
                <p x-show="!matchEmail" class="text-[10px] font-bold text-secondary mt-1 ml-1 uppercase tracking-tighter">Mismatch: Please check email</p>
            </div>

            <div>
                <label class="block text-[10px] font-black text-mutedText uppercase tracking-widest mb-2 ml-1">Mobile Number</label>
                <input type="text" name="mobile" value="{{ old('mobile') }}"
                    class="w-full px-5 py-4 bg-navy/10 border border-primary/10 rounded-2xl text-mainText font-bold placeholder-mutedText/30 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300"
                    placeholder="+91 XXXXX XXXXX">
            </div>

            <div>
                <label class="block text-[10px] font-black text-mutedText uppercase tracking-widest mb-2 ml-1">Gender</label>
                <div class="relative">
                    <select name="gender" class="w-full px-5 py-4 bg-navy/10 border border-primary/10 rounded-2xl text-mainText font-bold appearance-none focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300">
                        <option value="">Select Identity</option>
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            <div>
                <label class="block text-[10px] font-black text-mutedText uppercase tracking-widest mb-2 ml-1">Date of Birth</label>
                <input type="date" name="dob" value="{{ old('dob') }}"
                    class="w-full px-5 py-4 bg-navy/10 border border-primary/10 rounded-2xl text-mainText font-bold focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300 [color-scheme:light]">
            </div>

            <div>
                <label class="block text-[10px] font-black text-mutedText uppercase tracking-widest mb-2 ml-1">State / Region</label>
                <div class="relative">
                    <select name="state_id" class="w-full px-5 py-4 bg-navy/10 border border-primary/10 rounded-2xl text-mainText font-bold appearance-none focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300">
                        <option value="">Choose State</option>
                        @foreach ($states as $state)
                            <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>
                                {{ $state->name }}
                            </option>
                        @endforeach
                    </select>
                    <div class="absolute inset-y-0 right-0 flex items-center pr-4 pointer-events-none text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                    </div>
                </div>
            </div>

            <div class="md:col-span-2">
                <label class="block text-[10px] font-black text-mutedText uppercase tracking-widest mb-2 ml-1">Current City</label>
                <input type="text" name="city" value="{{ old('city') }}" placeholder="Enter city name"
                    class="w-full px-5 py-4 bg-navy/10 border border-primary/10 rounded-2xl text-mainText font-bold placeholder-mutedText/30 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300">
            </div>

            <div>
                <label class="block text-[10px] font-black text-mutedText uppercase tracking-widest mb-2 ml-1">Security Password</label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password" x-model="form.password" @input="checkMatch()" required
                        class="w-full px-5 py-4 bg-navy/10 border border-primary/10 rounded-2xl text-mainText font-bold placeholder-mutedText/30 focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/10 transition-all duration-300"
                        placeholder="Min. 8 characters">
                    <button type="button" @click="showPass = !showPass" class="absolute inset-y-0 right-0 pr-4 flex items-center text-mutedText hover:text-primary transition focus:outline-none">
                        <svg x-show="!showPass" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        <svg x-show="showPass" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" /></svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1 ml-1" />
            </div>

            <div>
                <label class="block text-[10px] font-black text-mutedText uppercase tracking-widest mb-2 ml-1">Confirm Security</label>
                <div class="relative">
                    <input :type="showPass ? 'text' : 'password'" name="password_confirmation" x-model="form.password_confirmation" @input="checkMatch()" required
                        class="w-full px-5 py-4 bg-navy/10 border rounded-2xl text-mainText font-bold placeholder-mutedText/30 focus:outline-none focus:ring-4 transition-all duration-300"
                        :class="matchPassword ? 'border-primary/10 focus:border-primary focus:ring-primary/10' : 'border-secondary/50 focus:border-secondary focus:ring-secondary/10'"
                        placeholder="Re-enter password">

                    <div x-show="form.password_confirmation && matchPassword" class="absolute inset-y-0 right-0 flex items-center pr-4 text-primary">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                </div>
                <p x-show="!matchPassword" class="text-[10px] font-bold text-secondary mt-1 ml-1 uppercase tracking-tighter">Mismatch: Please check password</p>
            </div>

        </div> <div class="mt-10">
            <button type="submit"
                :disabled="loading || !matchEmail || !matchPassword"
                class="brand-gradient w-full flex justify-center items-center py-5 px-6 rounded-2xl text-white text-xs font-black uppercase tracking-[3px] shadow-xl shadow-primary/30 hover:shadow-primary/50 transform active:scale-[0.98] transition-all duration-500 disabled:opacity-50 disabled:cursor-not-allowed overflow-hidden group">

                <span x-show="!loading" class="relative z-10 flex items-center gap-2">
                    Create Premium Account
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3"><path d="M13 7l5 5m0 0l-5 5m5-5H6" /></svg>
                </span>

                <span x-show="loading" x-cloak class="relative z-10 flex items-center">
                    <svg class="animate-spin -ml-1 mr-3 h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Establishing Access...
                </span>
            </button>
        </div>

        <p class="text-center text-xs font-bold text-mutedText uppercase tracking-widest mt-8">
            Existing Learner?
            <a href="{{ route('login') }}" class="text-primary hover:text-secondary border-b-2 border-primary/20 hover:border-secondary/40 transition-all pb-0.5 ml-1">Sign In</a>
        </p>
    </form>
</x-guest-layout>
