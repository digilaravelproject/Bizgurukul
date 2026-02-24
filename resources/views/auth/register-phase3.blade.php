<x-guest-layout>
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        function checkoutHandler() {
            return {
                couponCode: '',
                discount: 0,
                basePrice: {{ $basePrice }},
                taxableAmount: {{ $taxableAmount }},
                taxAmount: {{ $taxAmount }},
                total: {{ $totalAmount }},
                taxes: @json($taxes),
                couponMessage: '',
                couponStatus: '',
                loadingCoupon: false,
                processingPayment: false,

                async applyCoupon() {
                    if (!this.couponCode) return;
                    this.loadingCoupon = true;
                    this.couponMessage = '';
                    this.couponStatus = '';

                    try {
                        const response = await fetch('{{ route('register.check-coupon') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                code: this.couponCode,
                                bundle_id: {{ $bundle->id }}
                            })
                        });

                        const data = await response.json();

                        if (data.status === 'valid') {
                            this.discount = data.discount;
                            this.taxableAmount = data.taxable_amount;
                            this.taxAmount = data.tax;
                            this.total = data.total;
                            // Re-assign updated taxes from backend
                            if (data.taxes) {
                                this.taxes = data.taxes;
                            }
                            this.couponStatus = 'success';
                            this.couponMessage = data.message;
                        } else {
                            this.couponStatus = 'error';
                            this.couponMessage = data.message;
                        }
                    } catch (error) {
                        console.error(error);
                        this.couponStatus = 'error';
                        this.couponMessage = 'Failed to check coupon.';
                    } finally {
                        this.loadingCoupon = false;
                    }
                },

                async initiatePayment() {
                    if (this.processingPayment) return;
                    this.processingPayment = true;

                    try {
                        const response = await fetch('{{ route('payment.initiate') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                lead_id: {{ $lead->id }},
                                amount: this.total,
                                coupon_code: this.couponCode
                            })
                        });

                        const data = await response.json();

                        if (data.status === 'success') {
                            const options = {
                                "key": data.key,
                                "amount": data.amount,
                                "currency": "INR",
                                "name": "{{ config('app.name') }}",
                                "description": "Course Bundle Purchase",
                                "order_id": data.order_id,
                                "handler": (response) => {
                                    this.verifyPayment(response);
                                },
                                "prefill": {
                                    "name": "{{ $lead->name }}",
                                    "email": "{{ $lead->email }}",
                                    "contact": "{{ $lead->mobile }}"
                                },
                                "theme": {
                                    "color": "#F7941D" // Razorpay Theme Color Matched
                                },
                                "modal": {
                                    "ondismiss": () => {
                                        this.processingPayment = false;
                                    }
                                }
                            };
                            const rzp1 = new Razorpay(options);
                            rzp1.on('payment.failed', (response) => {
                                this.processingPayment = false;
                                alert('Payment Failed: ' + response.error.description);
                            });
                            rzp1.open();
                        } else {
                            throw new Error(data.message);
                        }
                    } catch (error) {
                        this.processingPayment = false;
                        alert('Error: ' + (error.message || 'Something went wrong'));
                    }
                },

                async verifyPayment(rzpResponse) {
                    try {
                        const response = await fetch('{{ route('payment.verify') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                razorpay_order_id: rzpResponse.razorpay_order_id,
                                razorpay_payment_id: rzpResponse.razorpay_payment_id,
                                razorpay_signature: rzpResponse.razorpay_signature,
                                lead_id: {{ $lead->id }},
                                coupon_code: this.couponCode
                            })
                        });

                        const data = await response.json();

                        if (data.status === 'success') {
                            window.location.href = data.redirect_url;
                        } else {
                            alert('Verification Failed: ' + data.message);
                            this.processingPayment = false;
                        }
                    } catch (error) {
                        console.error(error);
                        alert('Payment verified but redirect failed. Contact support.');
                        this.processingPayment = false;
                    }
                }
            }
        }
    </script>
    <div x-data="checkoutHandler()" class="w-full space-y-6">

        <div class="text-center space-y-1">
            <h1 class="text-2xl font-bold text-[rgb(var(--color-text-main))]">Checkout</h1>
            <p class="text-sm text-[rgb(var(--color-text-muted))]">Complete your purchase securely.</p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-[rgb(var(--color-primary)/0.1)] p-5 relative overflow-hidden">
            <div class="flex gap-4 items-start border-b border-gray-100 pb-4 mb-4">
                <div class="h-16 w-16 bg-gray-50 rounded-lg overflow-hidden flex-shrink-0 border border-gray-200">
                    @if($bundle->thumbnail_url)
                        <img src="{{ $bundle->thumbnail_url }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z" />
                            </svg>
                        </div>
                    @endif
                </div>
                <div>
                    <h3 class="font-bold text-[rgb(var(--color-text-main))] text-sm leading-tight">{{ $bundle->title }}</h3>
                    <span class="inline-block mt-1 text-[10px] font-bold px-2 py-0.5 rounded bg-[rgb(var(--color-primary)/0.1)] text-[rgb(var(--color-primary))]">
                        BUNDLE
                    </span>
                </div>
            </div>

            <div class="space-y-2 text-sm mb-5">
                <div class="flex justify-between text-[rgb(var(--color-text-muted))] text-xs">
                    <span>Base Price</span>
                    <span>₹{{ number_format($basePrice, 2) }}</span>
                </div>

                <div class="flex justify-between text-green-600 text-xs font-medium" x-show="discount > 0" x-transition>
                    <span>Coupon Discount</span>
                    <span>- ₹<span x-text="Number(discount).toFixed(2)"></span></span>
                </div>

                <template x-for="tax in taxes" :key="tax.id">
                    <div class="flex justify-between text-[rgb(var(--color-text-muted))] text-xs">
                        <span x-text="tax.name + ' (' + (tax.type === 'percentage' ? tax.value + '%' : 'Fixed') + ' ' + (tax.tax_type === 'inclusive' ? 'Incl.' : 'Excl.') + ')'"></span>
                        <span x-text="'₹' + Number(tax.calculated_amount || 0).toFixed(2)"></span>
                    </div>
                </template>

                <div class="flex justify-between items-center text-lg font-bold text-[rgb(var(--color-text-main))] border-t border-dashed border-gray-200 pt-3 mt-2">
                    <span>Total Pay</span>
                    <span>₹<span x-text="Number(total).toFixed(2)"></span></span>
                </div>
            </div>

            <div class="relative">
                <input type="text"
                    x-model="couponCode"
                    @keydown.enter.prevent="applyCoupon()"
                    placeholder="Have a coupon code?"
                    class="w-full pl-3 pr-20 py-2.5 bg-gray-50 border border-gray-200 rounded-lg text-sm focus:ring-1 focus:ring-[rgb(var(--color-primary))] focus:border-[rgb(var(--color-primary))] uppercase transition-colors">

                <button type="button"
                    @click="applyCoupon()"
                    :disabled="!couponCode || loadingCoupon"
                    class="absolute right-1 top-1 bottom-1 px-3 bg-[rgb(var(--color-text-main))] text-white text-xs font-bold rounded hover:bg-black transition disabled:opacity-50">
                    <span x-show="!loadingCoupon">APPLY</span>
                    <span x-show="loadingCoupon" x-cloak class="animate-spin h-3 w-3 border-2 border-white rounded-full border-t-transparent inline-block"></span>
                </button>
            </div>

            <p x-show="couponMessage" x-text="couponMessage"
               class="mt-2 text-xs font-medium text-center"
               :class="couponStatus === 'success' ? 'text-green-600' : 'text-red-500'"></p>
        </div>

        <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4">
            <h4 class="text-xs font-bold text-[rgb(var(--color-text-muted))] uppercase tracking-wider mb-3">Your Details</h4>
            <div class="text-sm space-y-2">
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-[rgb(var(--color-primary))]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <span class="font-medium text-[rgb(var(--color-text-main))]">{{ $lead->name }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-[rgb(var(--color-primary))]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                    <span class="text-[rgb(var(--color-text-muted))]">{{ $maskedEmail }}</span>
                </div>
                <div class="flex items-center gap-3">
                    <svg class="w-4 h-4 text-[rgb(var(--color-primary))]" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                    <span class="text-[rgb(var(--color-text-muted))]">+91 {{ $lead->mobile }}</span>
                </div>
            </div>
        </div>

        <div>
            <button type="button"
                @click="initiatePayment()"
                :disabled="processingPayment"
                class="w-full py-4 rounded-xl text-white font-bold text-base shadow-lg hover:shadow-xl transition-all duration-300 transform active:scale-95 brand-gradient flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">

                <span x-show="!processingPayment">Pay ₹<span x-text="Number(total).toFixed(2)"></span> & Join</span>

                <span x-show="processingPayment" class="flex items-center gap-2">
                    <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    Processing...
                </span>
            </button>
            <div class="mt-3 flex items-center justify-center gap-1.5 text-[10px] text-[rgb(var(--color-text-muted))]">
                <svg class="w-3 h-3 text-green-500" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                Secure Payment via Razorpay
            </div>
        </div>

    </div>

</x-guest-layout>
