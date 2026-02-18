<x-guest-layout>
    <!-- Header -->
    <div class="text-center space-y-2">
        <h1 class="text-3xl font-bold text-mainText tracking-tight">Welcome Back</h1>
        <p class="text-mutedText text-sm">Please enter your details to sign in.</p>
    </div>

    <x-auth-session-status class="mb-4" :status="session('status')" />

    <!-- Form -->
    <form method="POST" action="{{ route('login') }}" class="mt-8 space-y-6"
          x-data="{
              loading: false,
              showPassword: false,
              emailChecked: false,
              email: '{{ old('email') }}',
              maskedEmail: '',
              checkEmail() {
                  if (!this.email) return;
                  this.loading = true;
                  fetch('{{ route('login.check-email') }}', {
                      method: 'POST',
                      headers: {
                          'Content-Type': 'application/json',
                          'X-CSRF-TOKEN': '{{ csrf_token() }}'
                      },
                      body: JSON.stringify({ email: this.email })
                  })
                  .then(response => response.json())
                  .then(data => {
                      this.loading = false;
                      if (data.status === 'user' || data.status === 'lead') {
                          // Mask Email
                          let parts = this.email.split('@');
                          if (parts.length === 2) {
                              let name = parts[0];
                              let domainFull = parts[1];
                              let maskedName = name.length > 1 ? name[0] + '********' + name[name.length - 1] : name + '********';
                              let dParts = domainFull.split('.');
                              let dName = dParts[0];
                              let tld = dParts.slice(1).join('.');
                              let maskedD = dName.length > 1 ? dName[0] + '****' + dName[dName.length - 1] : dName + '****';
                              this.maskedEmail = maskedName + '@' + maskedD + (tld ? '.' + tld : '');
                          } else {
                              this.maskedEmail = this.email;
                          }

                          if (data.status === 'user') {
                              this.emailChecked = true;
                              this.$nextTick(() => {
                                  if(document.getElementById('password')) document.getElementById('password').focus();
                              });
                          } else {
                              // It's a lead, redirect to Phase 2
                              window.location.href = data.redirect_url;
                          }
                      } else if (data.redirect_url) {
                          window.location.href = data.redirect_url;
                      }
                  })
                  .catch(error => {
                      this.loading = false;
                      console.error('Error:', error);
                  });
              }
          }"
          @submit.prevent="if(!emailChecked) checkEmail(); else $el.submit()">
        @csrf

        <div class="space-y-5">
            <!-- Email -->
            <div>
                <label for="email_display" class="block text-sm font-medium text-mainText mb-1.5">Email Address</label>
                <div class="relative">
                    <!-- Real Email for submission -->
                    <input type="hidden" name="email" :value="email">

                    <input id="email_display" type="email"
                        :value="emailChecked ? maskedEmail : email"
                        @input="email = $event.target.value"
                        :required="!emailChecked" autofocus autocomplete="username"
                        placeholder="name@example.com"
                        :readonly="emailChecked"
                        class="w-full px-4 py-3 bg-navy/30 border border-slate-200 rounded-lg text-mainText placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200"
                        :class="{'opacity-75 cursor-not-allowed': emailChecked}">
                    <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                        <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 12a4 4 0 10-8 0 4 4 0 008 0zm0 0v1.5a2.5 2.5 0 005 0V12a9 9 0 10-9 9m4.5-1.206a8.959 8.959 0 01-4.5 1.207" />
                        </svg>
                    </div>
                </div>
                <!-- Reset Email Link -->
                <div x-show="emailChecked" x-cloak class="mt-1 text-right">
                    <button type="button" @click="emailChecked = false; $nextTick(() => document.getElementById('email').focus())" class="text-xs text-primary hover:underline">Change Email</button>
                </div>
                <x-input-error :messages="$errors->get('email')" class="mt-1" />
            </div>

            <!-- Password -->
            <div x-show="emailChecked" x-transition:enter="transition ease-out duration-300"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-100 transform scale-100">
                <div class="flex items-center justify-between mb-1.5">
                    <label for="password" class="block text-sm font-medium text-mainText">Password</label>
                    @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-sm font-semibold text-primary hover:text-secondary transition">
                            Forgot password?
                        </a>
                    @endif
                </div>
                <div class="relative">
                    <input id="password" :type="showPassword ? 'text' : 'password'" name="password" :required="emailChecked" autocomplete="current-password"
                        placeholder="Enter your password"
                        class="w-full px-4 py-3 bg-navy/30 border border-slate-200 rounded-lg text-mainText placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200">

                    <button type="button" @click="showPassword = !showPassword" class="absolute inset-y-0 right-0 pr-3 flex items-center text-slate-400 hover:text-primary transition focus:outline-none">
                        <!-- Eye Icon -->
                        <svg x-show="!showPassword" class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Eye Off Icon -->
                        <svg x-show="showPassword" x-cloak class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <x-input-error :messages="$errors->get('password')" class="mt-1" />
            </div>
        </div>

        <!-- Remember Me -->
        <div class="flex items-center" x-show="emailChecked" x-transition>
            <input id="remember_me" type="checkbox" name="remember" class="h-4 w-4 text-primary focus:ring-primary border-gray-300 rounded">
            <label for="remember_me" class="ml-2 block text-sm text-mutedText">Remember me for 30 days</label>
        </div>

        <!-- Submit Button -->
        <button type="submit"
            :disabled="loading"
            class="w-full flex justify-center items-center py-3.5 px-4 rounded-lg text-white font-bold bg-gradient-to-r from-primary to-secondary hover:opacity-90 transition-all duration-300 shadow-lg shadow-primary/20 transform active:scale-[0.98]">

            <span x-show="!loading" x-text="emailChecked ? 'Log In' : 'Next'"></span>

            <span x-show="loading" x-cloak class="flex items-center">
                <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
                Processing...
            </span>
        </button>

        <!-- Footer -->
        <p class="text-center text-sm text-mutedText">
            Don't have an account?
            <a href="{{ route('register.phase1') }}" class="font-bold text-primary hover:text-secondary transition">Create an account</a>
        </p>
    </form>
</x-guest-layout>
