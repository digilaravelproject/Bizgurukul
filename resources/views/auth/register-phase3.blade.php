<x-guest-layout>
    <div class="space-y-4 text-center">
        <h1 class="text-3xl font-bold text-mainText tracking-tight">Checkout</h1>
        <p class="text-mutedText text-sm">Review your order and complete your purchase.</p>
    </div>

    <div class="mt-8 grid grid-cols-1 lg:grid-cols-3 gap-8"
         x-data="{
             couponCode: '',
             discount: 0,
             taxAmount: {{ $taxAmount }},
             total: {{ $totalAmount }},
             basePrice: {{ $basePrice }},
             couponMessage: '',
             couponStatus: '',
             loadingCoupon: false,

             applyCoupon() {
                 if (!this.couponCode) return;
                 this.loadingCoupon = true;
                 this.couponMessage = '';
                 this.couponStatus = '';

                 fetch('{{ route('register.check-coupon') }}', {
                     method: 'POST',
                     headers: {
                         'Content-Type': 'application/json',
                         'X-CSRF-TOKEN': '{{ csrf_token() }}'
                     },
                     body: JSON.stringify({
                         code: this.couponCode,
                         bundle_id: {{ $bundle->id }}
                     })
                 })
                 .then(response => response.json())
                 .then(data => {
                     this.loadingCoupon = false;
                     if (data.status === 'valid') {
                         this.discount = data.discount;
                         this.taxAmount = data.tax;
                         this.total = data.total;
                         this.couponStatus = 'success';
                         this.couponMessage = data.message;
                     } else {
                         this.couponStatus = 'error';
                         this.couponMessage = data.message;
                         // Reset values if invalid coupon was applied?
                         // Maybe keep previous valid state or reset to base.
                         // For now, let's keep it simple and just show error.
                     }
                 })
                 .catch(() => {
                     this.loadingCoupon = false;
                     this.couponStatus = 'error';
                     this.couponMessage = 'Something went wrong.';
                 });
             }
         }">

        <!-- Order Summary & Payment -->
        <div class="lg:col-span-2 space-y-6">

            <!-- User Details Review -->
            <div class="bg-navy/30 border border-slate-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-mainText mb-4">Personal Details</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                    <div>
                        <span class="block text-slate-400">Name</span>
                        <span class="font-medium text-mainText">{{ $lead->name }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400">Email</span>
                        <span class="font-medium text-mainText">{{ $maskedEmail }}</span>
                    </div>
                    <div>
                        <span class="block text-slate-400">Mobile</span>
                        <span class="font-medium text-mainText">{{ $lead->mobile }}</span>
                    </div>
                </div>
            </div>

            <!-- Payment Gateway Integration (Placeholder) -->
            <div class="bg-navy/30 border border-slate-200 rounded-lg p-6">
                <h3 class="text-lg font-semibold text-mainText mb-4">Payment Method</h3>

                <!-- Razorpay Button (We'd dynamically generate order ID here or on click) -->
                <div class="mt-4">
                    <button type="button"
                        @click="initiatePayment(total, couponCode)"
                        class="w-full py-4 px-4 rounded-lg text-white font-bold bg-primary hover:bg-primary/90 transition shadow-lg flex items-center justify-center">
                        <span>Pay Now & Activate Account</span>
                        <span x-text="'₹' + Number(total).toFixed(2)" class="ml-2"></span>
                    </button>

                    <p class="text-xs text-center text-slate-400 mt-4 flex items-center justify-center">
                        <svg class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                        </svg>
                        Secure Payment via Razorpay
                    </p>
                </div>
            </div>
        </div>

        <!-- Order Breakdown Panel -->
        <div class="space-y-6">
            <div class="bg-white dark:bg-navy/40 border border-slate-200 rounded-xl p-6 shadow-sm sticky top-6">
                <h3 class="text-lg font-bold text-mainText mb-4">Order Summary</h3>

                <!-- Product -->
                <div class="flex items-start space-x-4 mb-6 pb-6 border-b border-slate-200/50">
                    <div class="h-16 w-16 bg-slate-100 rounded-lg overflow-hidden flex-shrink-0">
                         @if($bundle->thumbnail_url)
                            <img src="{{ $bundle->thumbnail_url }}" class="w-full h-full object-cover">
                        @else
                            <div class="w-full h-full flex items-center justify-center text-slate-400">
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                                </svg>
                            </div>
                        @endif
                    </div>
                    <div>
                        <h4 class="font-semibold text-mainText">{{ $bundle->title }}</h4>
                        <span class="text-sm text-slate-500">Video Course Bundle</span>
                    </div>
                </div>

                <!-- Price Breakdown -->
                <div class="space-y-3 text-sm">
                    <div class="flex justify-between text-slate-500">
                        <span>Base Price</span>
                        <span>₹{{ number_format($basePrice, 2) }}</span>
                    </div>

                    <div class="flex justify-between text-green-500" x-show="discount > 0" x-transition>
                        <span>Coupon Discount</span>
                        <span>- ₹<span x-text="Number(discount).toFixed(2)"></span></span>
                    </div>

                    <div class="flex justify-between text-slate-500">
                        <span>Taxes (GST)</span>
                        <span>₹<span x-text="Number(taxAmount).toFixed(2)"></span></span>
                    </div>

                    <div class="border-t border-slate-200/50 pt-3 flex justify-between font-bold text-lg text-mainText">
                        <span>Total Pay</span>
                        <span>₹<span x-text="Number(total).toFixed(2)"></span></span>
                    </div>
                </div>

                <!-- Coupon Section -->
                <div class="mt-6 pt-6 border-t border-slate-200/50">
                    <label class="block text-xs font-semibold text-slate-400 uppercase tracking-wider mb-2">Have a Coupon?</label>
                    <div class="flex space-x-2">
                        <input type="text" x-model="couponCode"
                            placeholder="Enter Code"
                            class="flex-1 px-3 py-2 bg-navy/30 border border-slate-200 rounded-lg text-sm focus:outline-none focus:border-primary uppercase">
                        <button type="button" @click="applyCoupon()"
                            :disabled="!couponCode || loadingCoupon"
                            class="px-4 py-2 bg-slate-800 text-white text-sm font-medium rounded-lg hover:bg-slate-700 disabled:opacity-50 transition">
                            <span x-show="!loadingCoupon">Apply</span>
                            <span x-show="loadingCoupon" x-cloak class="animate-spin h-4 w-4 border-2 border-white rounded-full border-t-transparent"></span>
                        </button>
                    </div>

                     <!-- Coupon Feedback -->
                    <p x-show="couponMessage" x-text="couponMessage"
                       class="mt-2 text-xs font-medium"
                       :class="couponStatus === 'success' ? 'text-green-500' : 'text-red-500'"></p>
                </div>
            </div>
        </div>
    </div>

    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        function initiatePayment(total, couponCode) {
            fetch('{{ route('payment.initiate') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    lead_id: {{ $lead->id }},
                    amount: total,
                    coupon_code: couponCode
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    var options = {
                        "key": data.key,
                        "amount": data.amount,
                        "currency": "INR",
                        "name": data.name,
                        "description": data.description,
                        "order_id": data.order_id,
                        "handler": function (response){
                             verifyPayment(response);
                        },
                        "prefill": data.prefill,
                        "theme": {
                            "color": "#3399cc"
                        }
                    };
                    var rzp1 = new Razorpay(options);
                    rzp1.on('payment.failed', function (response){
                        alert('Payment Failed: ' + response.error.description);
                    });
                    rzp1.open();
                } else {
                    alert('Error initiating payment: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('An error occurred. Please try again.');
            });
        }

        function verifyPayment(response) {
            fetch('{{ route('payment.verify') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({
                    razorpay_order_id: response.razorpay_order_id,
                    razorpay_payment_id: response.razorpay_payment_id,
                    razorpay_signature: response.razorpay_signature,
                    lead_id: {{ $lead->id }}
                })
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.href = data.redirect_url;
                } else {
                    alert('Verification failed: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Verification process error.');
            });
        }
    </script>
</x-guest-layout>
