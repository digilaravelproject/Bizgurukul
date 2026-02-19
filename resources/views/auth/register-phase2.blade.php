<x-guest-layout>
    <div class="space-y-4 text-center">
        <h1 class="text-3xl font-bold text-mainText tracking-tight">Select Your Package</h1>
        <p class="text-mutedText text-sm">Choose the best learning path for you.</p>
    </div>

    <form method="POST" action="{{ route('register.phase2.store') }}" class="mt-8 space-y-8"
          x-data="{
              referralCode: '{{ $referralCode ?? old('referral_code') }}',
              sponsorName: '{{ $maskedSponsor->name ?? '' }}',
              sponsorMobile: '{{ $maskedSponsor->mobile ?? '' }}',
              isValidSponsor: {{ $maskedSponsor ? 'true' : 'false' }},
              loadingSponsor: false,
              isLocked: {{ $maskedSponsor ? 'true' : 'false' }},
              selectedBundle: null,
              errorMessage: '',

              checkSponsor() {
                  this.errorMessage = '';
                  if (this.referralCode.length < 3) return;
                  this.loadingSponsor = true;
                  fetch('{{ route('register.check-referral') }}', {
                      method: 'POST',
                      headers: {
                          'Content-Type': 'application/json',
                          'X-CSRF-TOKEN': '{{ csrf_token() }}'
                      },
                      body: JSON.stringify({ code: this.referralCode })
                  })
                  .then(response => response.json())
                  .then(data => {
                      this.loadingSponsor = false;
                      if (data.status === 'valid') {
                          this.sponsorName = data.name;
                          this.sponsorMobile = data.mobile;
                          this.isValidSponsor = true;
                          this.errorMessage = '';
                      } else {
                          this.sponsorName = '';
                          this.sponsorMobile = '';
                          this.isValidSponsor = false;
                          this.errorMessage = 'Invalid Referral Code.';
                      }
                  })
                  .catch(() => {
                      this.loadingSponsor = false;
                      this.errorMessage = 'Error validating code.';
                  });
              },

              getFinalPrice(websitePrice, affiliatePrice) {
                  // If Valid Referral: Affiliate Price (e.g. 7000)
                  // If No Referral: Website Price - 10% (e.g. 10000 - 1000 = 9000)
                  if (this.isValidSponsor) {
                      return affiliatePrice;
                  }
                  return websitePrice * 0.9;
              },

              formatPrice(price) {
                  return '₹' + new Intl.NumberFormat('en-IN').format(price);
              }
          }">
        @csrf
        <input type="hidden" name="lead_id" value="{{ $lead->id }}">
        <input type="hidden" name="bundle_id" :value="selectedBundle">

        <!-- Sponsor Verification Section -->
        <div class="bg-navy/30 border border-slate-200 rounded-lg p-6 space-y-4">
            <h3 class="text-lg font-semibold text-mainText flex items-center">
                <svg class="h-5 w-5 mr-2 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                Referral Code
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 items-start">
                <div>
                    <label for="referral_code" class="block text-sm font-medium text-mainText mb-1.5">Enter Referral Code</label>
                    <div class="relative">
                        <input id="referral_code" type="text" name="referral_code" x-model="referralCode"
                            @input.debounce.500ms="checkSponsor()"
                            :readonly="isLocked"
                            placeholder="e.g. COMPANY"
                            class="w-full px-4 py-3 bg-navy/50 border border-slate-200 rounded-lg text-mainText placeholder-slate-400 focus:outline-none focus:border-primary focus:ring-1 focus:ring-primary transition duration-200 uppercase"
                            :class="{'opacity-75 cursor-not-allowed': isLocked}">

                        <div class="absolute inset-y-0 right-0 pr-3 flex items-center pointer-events-none">
                            <span x-show="loadingSponsor" class="animate-spin h-5 w-5 text-primary">
                                <svg class="text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </span>
                            <span x-show="!loadingSponsor && isValidSponsor" class="text-green-500">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                </svg>
                            </span>
                        </div>
                    </div>
                     <p x-show="errorMessage" x-text="errorMessage" class="text-red-500 text-xs mt-1 animate-pulse"></p>
                    <x-input-error :messages="$errors->get('referral_code')" class="mt-1" />
                </div>

                <div x-show="isValidSponsor" x-transition class="bg-primary/10 border border-primary/20 rounded-lg p-3">
                    <p class="text-xs text-primary font-semibold uppercase tracking-wider">Referring Sponsor</p>
                    <div class="flex items-center justify-between mt-1">
                        <span class="font-bold text-mainText" x-text="sponsorName"></span>
                        <span class="text-sm text-slate-500 font-mono" x-text="sponsorMobile"></span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Product Selection Section -->
        <div class="mt-8 space-y-4">
             <h3 class="text-lg font-semibold text-mainText flex items-center">
                <svg class="h-5 w-5 mr-2 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10" />
                </svg>
                Select Your Bundle
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach ($bundles as $bundle)
                <div @click="selectedBundle = {{ $bundle->id }}"
                     class="group relative border rounded-xl overflow-hidden transition-all duration-300 transform hover:-translate-y-1 hover:shadow-xl cursor-pointer bg-white dark:bg-navy/40 flex flex-col h-full"
                     :class="{'border-primary ring-2 ring-primary bg-primary/5': selectedBundle == {{ $bundle->id }}, 'border-slate-200 hover:border-primary/50': selectedBundle != {{ $bundle->id }}}">

                    <!-- Selection Indicator -->
                    <div class="absolute top-3 right-3 z-10">
                        <div class="h-6 w-6 rounded-full border-2 flex items-center justify-center transition-colors"
                             :class="{'border-primary bg-primary text-white': selectedBundle == {{ $bundle->id }}, 'border-slate-300 bg-white/80': selectedBundle != {{ $bundle->id }}}">
                            <svg x-show="selectedBundle == {{ $bundle->id }}" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7" />
                            </svg>
                        </div>
                    </div>

                    <!-- Image -->
                    <div class="aspect-video w-full bg-slate-100 relative overflow-hidden">
                        @if($bundle->thumbnail_url)
                            <img src="{{ $bundle->thumbnail_url }}" alt="{{ $bundle->title }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-400">
                                <svg class="h-12 w-12" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                        <!-- Recommended Badge -->
                        @if($loop->first)
                            <div class="absolute top-3 left-3 bg-secondary text-white text-xs font-bold px-2 py-1 rounded shadow">RECOMMENDED</div>
                        @endif
                    </div>

                    <!-- Content -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <h4 class="text-lg font-bold text-mainText group-hover:text-primary transition-colors">{{ $bundle->title }}</h4>
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-100">
                             <!-- Dynamic Pricing Display -->
                            <div class="flex flex-col">
                                <span class="text-xs text-slate-400 line-through">₹{{ number_format($bundle->website_price, 2) }}</span>
                                <div class="flex items-baseline space-x-2">
                                    <span class="text-2xl font-extrabold text-primary" x-text="formatPrice(getFinalPrice({{ $bundle->website_price }}, {{ $bundle->affiliate_price }}))">
                                        ₹{{ number_format($bundle->website_price * 0.9, 2) }}
                                    </span>
                                    <span class="text-xs font-bold px-2 py-1 rounded" :class="isValidSponsor ? 'bg-primary/10 text-primary' : 'bg-green-100 text-green-600'">
                                        <span x-text="isValidSponsor ? 'REFERRAL OFFER' : '10% DISCOUNT'">10% DISCOUNT</span>
                                    </span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
             <x-input-error :messages="$errors->get('bundle_id')" class="mt-1 text-center" />
        </div>

        <!-- Submit Button -->
        <button type="submit"
            :disabled="(!isValidSponsor && referralCode.length > 0) || !selectedBundle"
            class="w-full flex justify-center items-center py-4 px-4 rounded-lg text-white font-bold bg-gradient-to-r from-primary to-secondary hover:opacity-90 transition-all duration-300 shadow-lg shadow-primary/20 transform active:scale-[0.98] disabled:opacity-50 disabled:cursor-not-allowed">

            <span>Proceed to Checkout</span>
            <svg class="ml-2 -mr-1 w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" />
            </svg>
        </button>
    </form>
</x-guest-layout>
