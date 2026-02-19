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
        font-size: 11px !important;
    }
    .swal2-cancel {
        border-radius: 12px !important;
        padding: 12px 30px !important;
        font-weight: 800 !important;
        text-transform: uppercase !important;
        letter-spacing: 1px !important;
        font-size: 11px !important;
    }
    /* Alpine Transition Fix */
    [x-cloak] { display: none !important; }
</style>
@endpush

@section('content')
<div class="space-y-8 pb-12 font-sans text-mainText" x-data="couponManager()">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-mainText tracking-tighter uppercase">My Coupons</h1>
            <p class="text-xs text-mutedText font-medium">Manage and share your purchased coupons.</p>
        </div>
        <a href="{{ route('student.coupons.store') }}"
           class="bg-primary text-customWhite px-6 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg hover:bg-secondary hover:shadow-xl hover:-translate-y-1 transition-all active:scale-95 flex items-center gap-2">
            <i class="fas fa-shopping-cart"></i> Buy New Coupons
        </a>
    </div>

    {{-- FILTERS --}}
    <div class="flex items-center gap-2 overflow-x-auto pb-2 no-scrollbar">
        @foreach(['active' => 'Active', 'used' => 'Redeemed', 'expired' => 'Expired'] as $key => $label)
            <a href="{{ route('student.coupons.index', ['status' => $key]) }}"
               class="px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all border
               {{ $status === $key
                  ? 'bg-primary text-customWhite border-primary shadow-lg shadow-primary/20'
                  : 'bg-surface text-mutedText border-transparent hover:bg-primary/5 hover:text-primary' }}">
               {{ $label }}
            </a>
        @endforeach
    </div>

    {{-- COUPON GRID --}}
    @if($coupons->count() > 0)
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($coupons as $coupon)
                <div class="bg-surface rounded-[2.5rem] p-8 border border-primary/5 shadow-sm hover:shadow-2xl transition-all duration-300 group relative overflow-hidden">

                    {{-- Decorative Background --}}
                    <div class="absolute -top-12 -right-12 w-32 h-32 bg-primary/5 rounded-full blur-3xl group-hover:bg-primary/10 transition-all"></div>

                    {{-- Status Badge --}}
                    <div class="absolute top-6 right-6">
                        <span class="px-4 py-1.5 rounded-xl text-[9px] font-black uppercase tracking-widest
                            {{ $coupon->status === 'active' ? 'bg-green-500/10 text-green-600' :
                               ($coupon->status === 'used' ? 'bg-blue-500/10 text-blue-600' : 'bg-red-500/10 text-red-600') }}">
                            {{ ucfirst($coupon->status) }}
                        </span>
                    </div>

                    <div class="flex items-center gap-5 mb-8">
                        <div class="w-14 h-14 rounded-2xl bg-gradient-to-br from-primary/20 to-secondary/10 flex items-center justify-center text-primary text-2xl shadow-inner">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-mainText uppercase tracking-tight leading-tight">{{ $coupon->package->name ?? 'General Coupon' }}</h3>
                            <p class="text-[10px] text-mutedText font-black uppercase tracking-widest mt-1 opacity-70">
                                Expires: {{ $coupon->expiry_date ? $coupon->expiry_date->format('D, d M Y') : 'Never' }}
                            </p>
                        </div>
                    </div>

                    {{-- Coupon Code Box --}}
                    <div class="bg-gray-50/80 rounded-2xl p-6 border-2 border-dashed border-primary/10 mb-8 relative group/code hover:border-primary/30 transition-colors">
                        <p class="text-[9px] font-black text-mutedText uppercase tracking-widest mb-2 text-center opacity-60">Your Exclusive Code</p>
                        <div class="flex items-center justify-center gap-3">
                            <span class="text-2xl font-black text-primary tracking-[0.2em] font-mono select-all">{{ $coupon->code }}</span>
                            <button @click="copyToClipboard('{{ $coupon->code }}')"
                                    class="w-10 h-10 rounded-xl bg-white shadow-sm border border-gray-100 text-mutedText hover:text-primary hover:border-primary hover:scale-110 active:scale-95 transition-all flex items-center justify-center" title="Copy Code">
                                <i class="far fa-copy text-sm"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between border-t border-gray-100 pt-6">
                        <div class="flex flex-col">
                            <span class="text-[9px] font-black text-mutedText uppercase tracking-widest mb-1 opacity-60">Discount Value</span>
                            <span class="text-lg font-black text-mainText">
                                @if($coupon->type === 'percentage')
                                    {{ (int)$coupon->value }}% <small class="text-[10px] uppercase opacity-60 ml-0.5">OFF</small>
                                @else
                                    ₹{{ number_format($coupon->value) }} <small class="text-[10px] uppercase opacity-60 ml-0.5">OFF</small>
                                @endif
                            </span>
                        </div>

                        @if($coupon->status === 'active')
                            <button @click="openTransferModal({{ $coupon->id }}, '{{ $coupon->code }}')"
                                    class="brand-gradient px-5 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest text-white shadow-lg shadow-primary/20 hover:shadow-primary/40 hover:-translate-y-0.5 active:scale-95 transition-all flex items-center gap-2">
                                <i class="fas fa-paper-plane"></i> Transfer
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-12">
            {{ $coupons->appends(['status' => $status])->links() }}
        </div>

    @else
        <div class="flex flex-col items-center justify-center py-24 bg-surface rounded-[4rem] border-2 border-dashed border-primary/10 text-center shadow-inner">
            <div class="w-24 h-24 bg-primary/5 rounded-full flex items-center justify-center text-primary text-4xl mb-8">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <h3 class="text-2xl font-black text-mainText uppercase tracking-tight mb-3">No {{ $status }} Coupons</h3>
            <p class="text-sm text-mutedText font-medium max-w-sm mx-auto mb-10 leading-relaxed">
                It looks like your inventory is empty. Browse our exclusive packages in the store to find the perfect discount.
            </p>
            <a href="{{ route('student.coupons.store') }}" class="brand-gradient text-customWhite px-10 py-4 rounded-xl font-black text-[11px] uppercase tracking-widest shadow-xl hover:shadow-primary/40 transition-all hover:-translate-y-1 active:scale-95">
                Browse Coupon Store
            </a>
        </div>
    @endif

    {{-- Transfer Modal (Alpine Controlled) --}}
    <template x-if="showModal">
        <div class="fixed inset-0 z-[60] flex items-center justify-center p-4">
            <div class="fixed inset-0 bg-navy/90 backdrop-blur-md transition-opacity" @click="showModal = false" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0" x-transition:enter-end="opacity-100"></div>

            <div class="relative transform overflow-hidden rounded-[3rem] bg-white text-left shadow-2xl transition-all sm:w-full sm:max-w-lg border border-primary/10"
                 x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 sm:translate-y-0 sm:scale-95" x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                <div class="bg-white px-6 pb-6 pt-8 sm:p-10 sm:pb-8">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-[1.5rem] bg-primary/10 sm:mx-0">
                            <i class="fas fa-paper-plane text-2xl text-primary"></i>
                        </div>
                        <div class="mt-4 text-center sm:ml-8 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-2xl font-black leading-6 text-mainText uppercase tracking-tight">Transfer Coupon</h3>
                            <div class="mt-4 text-xs text-mutedText font-medium leading-relaxed">
                                <p>Transfering code: <span class="bg-gray-100 px-3 py-1 rounded-lg text-primary font-black ml-1 select-all" x-text="transferData.code"></span></p>
                                <p class="mt-2">The recipient will become the new owner. You will <span class="text-secondary font-black underline">lose access</span> to this coupon.</p>
                            </div>

                            <div class="mt-8">
                                <label class="block text-[10px] font-black uppercase text-secondary tracking-widest mb-3 ml-1">Recipient Email Address</label>
                                <div class="relative group">
                                    <div class="absolute inset-y-0 left-0 pl-5 flex items-center pointer-events-none text-mutedText group-focus-within:text-primary transition-colors">
                                        <i class="fas fa-envelope"></i>
                                    </div>
                                    <input type="email" x-model="transferData.email" required
                                           class="w-full rounded-2xl border-gray-100 shadow-inner focus:border-primary focus:ring-4 focus:ring-primary/10 font-bold text-sm pl-12 pr-6 py-4.5 bg-gray-50/50 transition-all placeholder:text-gray-300"
                                           placeholder="e.g. user@example.com">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50/80 px-8 py-8 sm:flex sm:flex-row-reverse sm:px-10 gap-4 border-t border-gray-100">
                    <button type="button" @click="submitTransfer()"
                            class="brand-gradient inline-flex w-full justify-center rounded-2xl px-10 py-4.5 text-[11px] font-black uppercase text-white shadow-xl hover:shadow-primary/40 sm:ml-3 sm:w-auto tracking-[0.1em] transition-all active:scale-95 disabled:opacity-70"
                            :disabled="processing">
                        <span x-show="!processing" class="flex items-center gap-2">Send Coupon <i class="fas fa-arrow-right"></i></span>
                        <span x-show="processing" class="flex items-center gap-2">
                            <i class="fas fa-spinner fa-spin"></i> Transferring...
                        </span>
                    </button>
                    <button type="button" @click="showModal = false"
                            class="inline-flex w-full justify-center rounded-2xl bg-white px-10 py-4.5 text-[11px] font-black uppercase text-mainText shadow-sm ring-1 ring-inset ring-gray-200 hover:bg-gray-50 sm:mt-0 sm:w-auto tracking-[0.1em] transition-all active:scale-95">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </template>
</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function couponManager() {
        return {
            showModal: false,
            processing: false,
            transferData: {
                id: null,
                code: '',
                email: ''
            },

            copyToClipboard(code) {
                try {
                    navigator.clipboard.writeText(code).then(() => {
                        this.showSuccessToast('Code copied to clipboard!');
                    });
                } catch (err) {
                    // Fallback for non-https or older browsers
                    alert('Could not copy automatically. Please select the code and Ctrl+C.');
                }
            },

            showSuccessToast(msg) {
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: msg,
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    background: '#ffffff',
                    color: '#2D2D2D',
                    iconColor: '#F7941D',
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                });
            },

            openTransferModal(id, code) {
                this.transferData.id = id;
                this.transferData.code = code;
                this.transferData.email = '';
                this.showModal = true;
            },

            submitTransfer() {
                if (!this.transferData.email) {
                    Swal.fire({
                        title: 'Wait!',
                        text: 'We need the recipient\'s email to continue.',
                        icon: 'warning',
                        confirmButtonText: 'Got it'
                    });
                    return;
                }

                // Email validation simple
                if (!this.transferData.email.match(/^[^\s@]+@[^\s@]+\.[^\s@]+$/)) {
                    Swal.fire({
                        title: 'Invalid Email',
                        text: 'Please enter a valid email address.',
                        icon: 'error'
                    });
                    return;
                }

                this.processing = true;

                fetch('{{ route('student.coupons.transfer') }}', {
                    method: 'POST',
                    body: JSON.stringify({
                        coupon_id: this.transferData.id,
                        recipient_email: this.transferData.email
                    }),
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    this.processing = false;
                    if (data.status === 'success') {
                        this.showModal = false;
                        Swal.fire({
                            title: 'Transfer Complete!',
                            text: data.message,
                            icon: 'success'
                        }).then(() => {
                            location.reload();
                        });
                    } else {
                        Swal.fire({
                            title: 'Transfer Failed',
                            text: data.message,
                            icon: 'error'
                        });
                    }
                })
                .catch(error => {
                    this.processing = false;
                    console.error('Error:', error);
                    Swal.fire({
                        title: 'System Error',
                        text: 'An unexpected error occurred. Please contact support.',
                        icon: 'error'
                    });
                });
            }
        }
    }
</script>
@endpush
