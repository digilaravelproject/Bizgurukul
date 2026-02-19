@extends('layouts.user.app')

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
<style>
    .swal2-popup {
        border-radius: 2rem !important;
        font-family: 'Outfit', sans-serif !important;
    }
    .swal2-confirm {
        background: linear-gradient(90deg, #F7941D 0%, #D04A02 100%) !important;
        border-radius: 12px !important;
        padding: 12px 30px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
        font-size: 12px !important;
    }
    .swal2-cancel {
        border-radius: 12px !important;
        padding: 12px 30px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
        font-size: 12px !important;
    }
</style>
@endpush

@section('content')
<div class="space-y-8 pb-12 font-sans text-mainText">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-mainText tracking-tighter uppercase">Coupon Store</h1>
            <p class="text-xs text-mutedText font-medium">Purchase discount packages and boost your earnings.</p>
        </div>
        <a href="{{ route('student.coupons.index') }}"
           class="text-primary hover:text-secondary font-black text-[10px] uppercase tracking-widest transition-all flex items-center gap-2 border-b-2 border-transparent hover:border-secondary pb-1">
            <i class="fas fa-arrow-left"></i> Back to My Coupons
        </a>
    </div>

    {{-- PACKAGES GRID --}}
    @if($packages->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
            @foreach($packages as $pkg)
                <div class="bg-surface rounded-3xl p-6 border border-primary/5 shadow-sm hover:shadow-2xl hover:-translate-y-1 transition-all group flex flex-col h-full relative overflow-hidden">

                    {{-- Decorative Blur --}}
                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-primary/10 rounded-full blur-2xl group-hover:bg-primary/20 transition-all"></div>

                    <div class="mb-6 relative z-10">
                        <div class="w-14 h-14 rounded-2xl bg-secondary/10 flex items-center justify-center text-secondary text-2xl mb-4 group-hover:scale-110 transition-transform">
                            <i class="fas fa-tags"></i>
                        </div>
                        <h3 class="text-xl font-black text-mainText uppercase tracking-tight leading-tight mb-2">{{ $pkg->name }}</h3>
                        <p class="text-xs text-mutedText line-clamp-2">{{ $pkg->description }}</p>
                    </div>

                    <div class="mt-auto space-y-4 relative z-10">
                        <div class="flex justify-between items-end border-t border-dashed border-primary/10 pt-4">
                            <div>
                                <p class="text-[9px] font-black text-mutedText uppercase tracking-widest mb-1">Selling Price</p>
                                <p class="text-2xl font-black text-mainText">₹{{ number_format($pkg->selling_price) }}</p>
                            </div>
                            <div class="text-right">
                                <p class="text-[9px] font-black text-green-600 uppercase tracking-widest mb-1">User Gets</p>
                                <p class="text-lg font-black text-green-600">
                                    {{ $pkg->type === 'percentage' ? $pkg->discount_value . '%' : '₹' . number_format($pkg->discount_value) }} OFF
                                </p>
                            </div>
                        </div>

                        <button onclick="purchasePackage({{ $pkg->id }}, '{{ $pkg->name }}', {{ $pkg->selling_price }})"
                                class="brand-gradient w-full py-3.5 rounded-xl font-black text-[10px] uppercase tracking-widest text-customWhite shadow-lg hover:shadow-primary/30 active:scale-95 transition-all">
                            Purchase Now
                        </button>
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $packages->links() }}
        </div>
    @else
        <div class="flex flex-col items-center justify-center py-20 bg-surface rounded-[3rem] border-2 border-dashed border-primary/5 text-center">
            <div class="w-20 h-20 bg-secondary/10 rounded-full flex items-center justify-center text-secondary text-3xl mb-6">
                <i class="fas fa-box-open"></i>
            </div>
            <h3 class="text-xl font-black text-mainText uppercase tracking-tight mb-2">No Packages Available</h3>
            <p class="text-sm text-mutedText font-medium max-w-sm mx-auto">
                Check back later for exciting new coupon offers.
            </p>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script src="https://checkout.razorpay.com/v1/checkout.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function purchasePackage(id, name, price) {
        Swal.fire({
            title: 'Purchase Confirmation',
            text: `Are you sure you want to purchase "${name}" for ₹${price}?`,
            icon: 'question',
            showCancelButton: true,
            confirmButtonText: 'Yes, Purchase Now',
            cancelButtonText: 'Cancel',
            reverseButtons: true
        }).then((result) => {
            if (result.isConfirmed) {
                initiatePurchase(id, name);
            }
        });
    }

    function initiatePurchase(id, name) {
        Swal.fire({
            title: 'Initializing Payment',
            text: 'Please wait...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('{{ route('student.coupons.purchase.initiate') }}', {
            method: 'POST',
            body: JSON.stringify({ package_id: id }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                Swal.close();
                const order = data.data;

                var options = {
                    "key": order.key,
                    "amount": order.amount,
                    "currency": "INR",
                    "name": order.name,
                    "description": "Purchase Coupon Package: " + name,
                    "order_id": order.order_id,
                    "handler": function (response){
                        verifyPayment(response);
                    },
                    "prefill": order.prefill,
                    "theme": {
                        "color": "#F7941D" // Brand Color
                    }
                };
                var rzp1 = new Razorpay(options);
                rzp1.on('payment.failed', function (response){
                    Swal.fire({
                        title: 'Payment Failed',
                        text: response.error.description,
                        icon: 'error'
                    });
                });
                rzp1.open();

            } else {
                Swal.fire({
                    title: 'Initiation Failed',
                    text: data.message,
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: 'Something went wrong during initialization.',
                icon: 'error'
            });
        });
    }

    function verifyPayment(response) {
        Swal.fire({
            title: 'Verifying Payment',
            text: 'Processing your purchase...',
            allowOutsideClick: false,
            didOpen: () => {
                Swal.showLoading();
            }
        });

        fetch('{{ route('student.coupons.purchase.verify') }}', {
            method: 'POST',
            body: JSON.stringify({
                razorpay_order_id: response.razorpay_order_id,
                razorpay_payment_id: response.razorpay_payment_id,
                razorpay_signature: response.razorpay_signature
            }),
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(res => res.json())
        .then(data => {
            if (data.status === 'success') {
                 Swal.fire({
                    title: 'Purchase Successful!',
                    html: `Your coupon code is: <b class="text-primary font-bold text-xl select-all">${data.code}</b><br><br>It has been added to your inventory.`,
                    icon: 'success',
                    confirmButtonText: 'View My Coupons'
                 }).then(() => {
                    window.location.href = '{{ route('student.coupons.index') }}';
                 });
            } else {
                Swal.fire({
                    title: 'Verification Failed',
                    text: data.message,
                    icon: 'error'
                });
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire({
                title: 'Error',
                text: 'Payment verification failed. Please contact support.',
                icon: 'error'
            });
        });
    }
</script>
@endpush
