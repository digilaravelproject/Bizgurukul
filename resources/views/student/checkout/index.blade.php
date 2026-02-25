@extends('layouts.user.app')

@section('content')
    <script src="https://checkout.razorpay.com/v1/checkout.js"></script>
    <script>
        function checkoutHandler() {
            return {
                basePrice: {{ $basePrice }},
                taxableAmount: {{ $taxableAmount }},
                taxAmount: {{ $taxAmount }},
                total: {{ $totalAmount }},
                taxes: @json($taxes),
                processingPayment: false,
                processingRedirect: false,

                async initiatePayment() {
                    if (this.processingPayment) return;
                    this.processingPayment = true;

                    try {
                        const response = await fetch('{{ route('razorpay.create', ['type' => $type, 'id' => $id]) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            }
                        });

                        const data = await response.json();
                        console.log('Order Creation Response:', data);

                        // Automatically handled zero-amount upgrades in the backend
                        if (data.status === 'success_free') {
                            this.processingRedirect = true;
                            window.location.href = '{{ route('student.dashboard') }}';
                            return;
                        }

                        if (data.order_id) {
                            const options = {
                                "key": data.key,
                                "amount": data.amount,
                                "currency": "INR",
                                "name": "{{ config('app.name') }}",
                                "description": data.product_name,
                                "order_id": data.order_id,
                                "handler": (response) => {
                                    this.verifyPayment(response);
                                },
                                "prefill": {
                                    "name": "{{ $user->name }}",
                                    "email": "{{ $user->email }}",
                                    "contact": "{{ $user->mobile ?? '' }}"
                                },
                                "theme": {
                                    "color": "#F7941D"
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
                            throw new Error(data.message || 'Error creating order');
                        }
                    } catch (error) {
                        this.processingPayment = false;
                        alert('Error: ' + (error.message || 'Something went wrong'));
                    }
                },

                async verifyPayment(rzpResponse) {
                    this.processingPayment = true;
                    this.processingRedirect = true;
                    try {
                        console.log('Verifying payment...', rzpResponse);
                        const response = await fetch('{{ route('razorpay.verify') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                razorpay_order_id: rzpResponse.razorpay_order_id,
                                razorpay_payment_id: rzpResponse.razorpay_payment_id,
                                razorpay_signature: rzpResponse.razorpay_signature
                            })
                        });

                        const data = await response.json();
                        console.log('Verification Response:', data);

                        if (data.status === 'success') {
                            // Redirect to dashboard
                            window.location.href = '{{ route('student.dashboard') }}';
                        } else {
                            this.processingRedirect = false;
                            this.processingPayment = false;
                            alert('Verification Failed: ' + (data.message || 'Unknown error'));
                        }
                    } catch (error) {
                        console.error('Redirection/Verification error:', error);
                        this.processingRedirect = false;
                        this.processingPayment = false;
                        alert('Payment verified but redirect failed. Redirecting manually...');
                        window.location.href = '{{ route('student.dashboard') }}';
                    }
                }
            }
        }
    </script>

    <div x-data="checkoutHandler()" class="max-w-xl mx-auto space-y-8 py-10">

        <div class="text-center space-y-1">
            <h1 class="text-3xl font-extrabold text-mainText tracking-tight">Checkout Summary</h1>
            <p class="text-sm font-medium text-mutedText">Review your details and complete your secure purchase.</p>
        </div>

        <div class="bg-surface rounded-3xl shadow-xl border border-primary/10 p-6 relative overflow-hidden">
            <div class="absolute top-0 left-0 w-full h-1 bg-gradient-to-r from-primary to-secondary"></div>

            <div class="flex gap-5 items-start border-b border-gray-100 pb-5 mb-5 mt-2">
                <div class="h-20 w-20 bg-gray-50 rounded-2xl overflow-hidden flex-shrink-0 border border-gray-200 shadow-sm">
                    @if(isset($product->thumbnail_url))
                        <img src="{{ $product->thumbnail_url }}" class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full flex items-center justify-center text-gray-400">
                            <i class="fas fa-box text-2xl"></i>
                        </div>
                    @endif
                </div>
                <div>
                    <h3 class="font-black text-mainText text-lg leading-tight">{{ $product->title }}</h3>
                    <span class="inline-block mt-2 text-[10px] font-black tracking-widest px-2.5 py-1 rounded bg-primary/10 text-primary uppercase">
                        {{ $type }}
                    </span>
                </div>
            </div>

            <div class="space-y-3 text-sm mb-6">
                <div class="flex justify-between text-mutedText font-medium text-sm">
                    <span>Base Price <span class="text-[10px] uppercase">(Subtotal)</span></span>
                    <span>₹{{ number_format($basePrice, 2) }}</span>
                </div>

                <template x-for="tax in taxes" :key="tax.id">
                    <div class="flex justify-between text-mutedText font-medium text-sm" x-show="Number(tax.calculated_amount) > 0">
                        <span x-text="tax.name + ' (' + (tax.type === 'percentage' ? tax.value + '%' : 'Fixed') + ' ' + (tax.tax_type === 'inclusive' ? 'Incl.' : 'Excl.') + ')'"></span>
                        <span x-text="'₹' + Number(tax.calculated_amount || 0).toFixed(2)"></span>
                    </div>
                </template>

                <div class="flex justify-between items-center text-xl font-black text-mainText border-t border-dashed border-gray-200 pt-4 mt-3">
                    <span>Total Amount</span>
                    <span class="text-primary">₹<span x-text="Number(total).toFixed(2)"></span></span>
                </div>
            </div>

            <div class="bg-gray-50/80 rounded-2xl border border-gray-100 p-5 mt-4">
                <h4 class="text-[10px] font-black text-mutedText uppercase tracking-widest mb-4">Billing Information</h4>
                <div class="text-sm space-y-3 font-medium text-mainText">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                            <i class="fas fa-user text-xs"></i>
                        </div>
                        <span>{{ $user->name }}</span>
                    </div>
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                            <i class="fas fa-envelope text-xs"></i>
                        </div>
                        <span>{{ $user->email }}</span>
                    </div>
                    @if($user->mobile)
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary">
                                <i class="fas fa-phone text-xs"></i>
                            </div>
                            <span>+91 {{ $user->mobile }}</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <div>
            @if($totalAmount == 0)
                <button type="button"
                    @click="initiatePayment()"
                    :disabled="processingPayment"
                    class="w-full py-4 rounded-2xl text-white font-black uppercase tracking-widest text-sm shadow-xl hover:shadow-2xl transition-all duration-300 transform active:scale-95 bg-green-500 hover:bg-green-600 flex items-center justify-center gap-2">
                    <span x-show="!processingPayment">Claim Free Upgrade</span>
                    <span x-show="processingPayment">Processing...</span>
                </button>
            @else
                <button type="button"
                    @click="initiatePayment()"
                    :disabled="processingPayment || processingRedirect"
                    class="w-full py-4 rounded-2xl text-white font-black uppercase tracking-widest text-sm shadow-xl hover:-translate-y-1 hover:shadow-2xl transition-all duration-300 transform active:scale-95 bg-primary hover:bg-secondary flex items-center justify-center gap-2 disabled:opacity-70 disabled:cursor-not-allowed">

                    <span x-show="!processingPayment && !processingRedirect">Pay Securely (₹<span x-text="Number(total).toFixed(2)"></span>)</span>

                    <span x-show="processingPayment && !processingRedirect" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Loading Gateway...
                    </span>

                    <span x-show="processingRedirect" class="flex items-center gap-2">
                        <svg class="animate-spin h-5 w-5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                        </svg>
                        Verifying & Redirecting...
                    </span>
                </button>
            @endif

            <div class="mt-4 flex flex-col items-center justify-center gap-2">
                <div class="flex items-center gap-2 text-xs font-bold text-mutedText">
                    <i class="fas fa-shield-alt text-green-500"></i>
                    100% Secure Payment via Razorpay
                </div>
            </div>
        </div>

    </div>

@endsection
