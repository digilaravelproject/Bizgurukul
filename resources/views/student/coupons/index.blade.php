@extends('layouts.user.app')

@section('content')
<div class="space-y-8 pb-12 font-sans text-mainText">

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
                <div class="bg-surface rounded-3xl p-6 border border-primary/5 shadow-sm hover:shadow-xl transition-all group relative overflow-hidden">

                    {{-- Status Badge --}}
                    <div class="absolute top-4 right-4">
                        <span class="px-3 py-1 rounded-full text-[9px] font-black uppercase tracking-widest
                            {{ $coupon->status === 'active' ? 'bg-green-500/10 text-green-600' :
                               ($coupon->status === 'used' ? 'bg-blue-500/10 text-blue-600' : 'bg-red-500/10 text-red-600') }}">
                            {{ ucfirst($coupon->status) }}
                        </span>
                    </div>

                    <div class="flex items-center gap-4 mb-6">
                        <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary text-xl">
                            <i class="fas fa-ticket-alt"></i>
                        </div>
                        <div>
                            <h3 class="text-lg font-black text-mainText uppercase tracking-tight">{{ $coupon->package->name ?? 'General Coupon' }}</h3>
                            <p class="text-[10px] text-mutedText font-bold uppercase tracking-widest">
                                Validity: {{ $coupon->expiry_date->format('d M, Y') }}
                            </p>
                        </div>
                    </div>

                    {{-- Coupon Code Box --}}
                    <div class="bg-navy/5 rounded-2xl p-4 border border-dashed border-primary/20 mb-6 relative group/code">
                        <p class="text-[9px] font-black text-mutedText uppercase tracking-widest mb-1 text-center">Coupon Code</p>
                        <div class="flex items-center justify-center gap-2">
                            <span class="text-xl font-black text-primary tracking-widest font-mono select-all">{{ $coupon->code }}</span>
                            <button onclick="navigator.clipboard.writeText('{{ $coupon->code }}'); alert('Copied!');"
                                    class="text-mutedText hover:text-primary transition-colors p-1" title="Copy Code">
                                <i class="far fa-copy"></i>
                            </button>
                        </div>
                    </div>

                    <div class="flex items-center justify-between text-[10px] font-black uppercase tracking-widest text-mutedText border-t border-primary/5 pt-4">
                        <span>Value: ₹{{ number_format($coupon->value) }}</span>

                        @if($coupon->status === 'active')
                            <button onclick="openTransferModal({{ $coupon->id }}, '{{ $coupon->code }}')"
                                    class="text-secondary hover:text-primary transition-colors flex items-center gap-1">
                                <i class="fas fa-paper-plane"></i> Transfer
                            </button>
                        @endif
                    </div>
                </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $coupons->appends(['status' => $status])->links() }}
        </div>

    @else
        <div class="flex flex-col items-center justify-center py-20 bg-surface rounded-[3rem] border-2 border-dashed border-primary/5 text-center">
            <div class="w-20 h-20 bg-primary/5 rounded-full flex items-center justify-center text-primary text-3xl mb-6">
                <i class="fas fa-ticket-alt"></i>
            </div>
            <h3 class="text-xl font-black text-mainText uppercase tracking-tight mb-2">No Coupons Found</h3>
            <p class="text-sm text-mutedText font-medium max-w-sm mx-auto mb-8">
                You don't have any {{ $status }} coupons yet. Purchase packages to generate coupons.
            </p>
            <a href="{{ route('student.coupons.store') }}" class="brand-gradient text-customWhite px-8 py-3 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg hover:shadow-primary/30 transition-all hover:-translate-y-1">
                Browse Store
            </a>
        </div>
    @endif
</div>

{{-- Transfer Modal --}}
<div id="transferModal" class="fixed inset-0 z-50 hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
    <div class="fixed inset-0 bg-navy/80 backdrop-blur-sm transition-opacity" onclick="closeTransferModal()"></div>

    <div class="fixed inset-0 z-10 overflow-y-auto">
        <div class="flex min-h-full items-end justify-center p-4 text-center sm:items-center sm:p-0">
            <div class="relative transform overflow-hidden rounded-3xl bg-white text-left shadow-2xl transition-all sm:my-8 sm:w-full sm:max-w-lg border border-primary/10">
                <div class="bg-white px-4 pb-4 pt-5 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex h-12 w-12 flex-shrink-0 items-center justify-center rounded-full bg-primary/10 sm:mx-0 sm:h-10 sm:w-10">
                            <i class="fas fa-paper-plane text-primary"></i>
                        </div>
                        <div class="mt-3 text-center sm:ml-4 sm:mt-0 sm:text-left w-full">
                            <h3 class="text-lg font-black leading-6 text-mainText uppercase tracking-tight" id="modal-title">Transfer Coupon</h3>
                            <div class="mt-2 text-sm text-mutedText font-medium">
                                <p>You are transferring coupon <strong id="transferCouponCode" class="text-primary">CODE</strong>. This action cannot be undone.</p>
                            </div>

                            <form id="transferForm" class="mt-6 space-y-4">
                                @csrf
                                <input type="hidden" name="coupon_id" id="transferCouponId">
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-secondary tracking-widest mb-2">Recipient Email</label>
                                    <input type="email" name="recipient_email" required
                                           class="w-full rounded-xl border-gray-300 shadow-sm focus:border-primary focus:ring-primary font-bold text-sm px-4 py-3 bg-gray-50"
                                           placeholder="user@example.com">
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:flex sm:flex-row-reverse sm:px-6 gap-2">
                    <button type="button" onclick="submitTransfer()"
                            class="inline-flex w-full justify-center rounded-xl bg-primary px-5 py-3 text-[10px] font-black uppercase text-white shadow-sm hover:bg-secondary sm:ml-3 sm:w-auto tracking-widest transition-all">
                        Transfer Now
                    </button>
                    <button type="button" onclick="closeTransferModal()"
                            class="mt-3 inline-flex w-full justify-center rounded-xl bg-white px-5 py-3 text-[10px] font-black uppercase text-mainText shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50 sm:mt-0 sm:w-auto tracking-widest transition-all">
                        Cancel
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function openTransferModal(id, code) {
        document.getElementById('transferCouponId').value = id;
        document.getElementById('transferCouponCode').innerText = code;
        document.getElementById('transferModal').classList.remove('hidden');
    }

    function closeTransferModal() {
        document.getElementById('transferModal').classList.add('hidden');
    }

    function submitTransfer() {
        const form = document.getElementById('transferForm');
        const formData = new FormData(form);

        fetch('{{ route('student.coupons.transfer') }}', {
            method: 'POST',
            body: formData,
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.status === 'success') {
                alert(data.message);
                location.reload();
            } else {
                alert('Error: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong.');
        });
    }
</script>
@endsection
