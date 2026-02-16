@extends('layouts.user.app')

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

<script>
    function purchasePackage(id, name, price) {
        if (!confirm(`Are you sure you want to purchase "${name}" for ₹${price}?`)) return;

        fetch('{{ route('student.coupons.purchase') }}', {
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
                alert(`Success! Your coupon code is: ${data.code}\nIt has been added to your inventory.`);
                window.location.href = '{{ route('student.coupons.index') }}';
            } else {
                alert('Purchase Failed: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong. Please try again.');
        });
    }
</script>
@endsection
