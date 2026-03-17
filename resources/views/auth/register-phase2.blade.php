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
              showModal: false,
              modalBundle: null,

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
                  if (this.isValidSponsor) {
                      return affiliatePrice;
                  }
                  return websitePrice;
              },

              formatPrice(price) {
                  return '₹' + new Intl.NumberFormat('en-IN').format(price);
              },

              openModal(bundle) {
                  this.modalBundle = bundle;
                  this.showModal = true;
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
                    </div>

                    <!-- Content -->
                    <div class="p-5 flex-1 flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-start mb-2">
                                <h4 class="text-lg font-bold text-mainText group-hover:text-primary transition-colors">{{ $bundle->title }}</h4>
                            </div>
                            <!-- View Courses Button -->
                            <button type="button" 
                                    @click.stop="openModal({{ json_encode(['title' => $bundle->title, 'courses' => $bundle->courses->map(fn($c) => ['title' => $c->title])]) }})"
                                    class="text-xs font-bold text-primary hover:underline flex items-center bg-primary/5 px-2 py-1 rounded">
                                <svg class="w-3.5 h-3.5 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                </svg>
                                View Details
                            </button>
                        </div>

                        <div class="mt-4 pt-4 border-t border-slate-100">
                             <!-- Dynamic Pricing Display -->
                            <div class="flex flex-col">
                                <span class="text-xs text-slate-400 line-through" x-show="isValidSponsor && {{ $bundle->website_price }} > {{ $bundle->affiliate_price }}" x-cloak>₹{{ number_format($bundle->website_price, 2) }}</span>
                                <div class="flex items-baseline space-x-2 flex-wrap gap-y-1 mt-1">
                                    <span class="text-2xl font-extrabold text-primary" x-text="formatPrice(getFinalPrice({{ $bundle->website_price }}, {{ $bundle->affiliate_price }}))">
                                        ₹{{ number_format($bundle->website_price, 2) }}
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

        <!-- Course Details Modal -->
        <div x-show="showModal" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             class="fixed inset-0 z-[100] overflow-y-auto" 
             x-cloak>
            
            <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showModal = false">
                    <div class="absolute inset-0 bg-slate-900/80 backdrop-blur-sm"></div>
                </div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-slate-100"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">
                    
                    <div class="bg-white px-6 py-4 border-b border-slate-100 flex justify-between items-center">
                        <h3 class="text-xl font-black text-mainText" x-text="modalBundle ? modalBundle.title : ''"></h3>
                        <button type="button" @click="showModal = false" class="text-slate-400 hover:text-slate-600 transition-colors">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 py-6">
                        <p class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-4">Included Courses:</p>
                        <div class="space-y-3 max-h-[300px] overflow-y-auto pr-2 custom-scrollbar">
                            <template x-for="(course, index) in (modalBundle ? modalBundle.courses : [])" :key="index">
                                <div class="flex items-start bg-navy/5 p-3 rounded-xl border border-navy/10 group hover:border-primary/30 transition-colors">
                                    <div class="flex-shrink-0 h-6 w-6 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xs font-bold mr-3 mt-0.5">
                                        <span x-text="index + 1"></span>
                                    </div>
                                    <span class="text-mainText font-semibold text-sm leading-tight group-hover:text-primary transition-colors" x-text="course.title"></span>
                                </div>
                            </template>
                            <div x-show="modalBundle && modalBundle.courses.length === 0" class="text-center py-4 text-slate-400 italic">
                                No specific courses listed for this bundle.
                            </div>
                        </div>
                    </div>

                    <div class="bg-slate-50 px-6 py-4 flex flex-col sm:flex-row-reverse gap-3">
                        <button type="button" @click="showModal = false" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-bold text-white hover:bg-primary/90 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:ml-3 sm:w-auto sm:text-sm transition-all duration-300">
                            Close
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </form>
</x-guest-layout>
