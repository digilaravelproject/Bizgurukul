{{-- 1. DESKTOP TABLE --}}
<div class="hidden md:block overflow-hidden rounded-2xl border border-primary/5 bg-surface shadow-sm relative animate-fade-in">
    <table class="w-full text-left text-sm">
        <thead class="bg-primary/5 text-[10px] uppercase font-bold text-mutedText border-b border-primary/5 tracking-widest">
            <tr>
                <th class="px-6 py-4">Coupon Details</th>
                <th class="px-6 py-4">Scope</th>
                <th class="px-6 py-4">Discount</th>
                <th class="px-6 py-4">Usage</th>
                <th class="px-6 py-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-primary/5">
            @forelse($coupons as $coupon)
                <tr class="hover:bg-primary/[0.02] transition-colors group">
                    {{-- Detail Column --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="h-9 w-9 rounded-lg bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                            </div>
                            <div>
                                <div class="font-bold text-mainText text-sm group-hover:text-primary transition-colors uppercase tracking-wider">{{ $coupon->code }}</div>
                                <div class="text-[10px] text-mutedText flex items-center gap-1">
                                    <span class="w-1.5 h-1.5 rounded-full {{ $coupon->is_active ? 'bg-green-500' : 'bg-red-500' }}"></span>
                                    {{ $coupon->is_active ? 'Active' : 'Inactive' }}
                                </div>
                            </div>
                        </div>
                    </td>

                    {{-- Scope Column --}}
                    <td class="px-6 py-4">
                        @if($coupon->coupon_type === 'general')
                            <span class="inline-flex items-center rounded-md bg-navy border border-primary/10 px-2 py-1 text-[10px] font-bold text-mutedText">STORE-WIDE</span>
                        @else
                            <div class="flex flex-col gap-1">
                                @php
                                    $cCount = is_array($coupon->selected_courses) ? count($coupon->selected_courses) : 0;
                                    $bCount = is_array($coupon->selected_bundles) ? count($coupon->selected_bundles) : 0;
                                @endphp
                                @if($cCount > 0)
                                    <span class="text-[10px] text-primary font-bold">{{ $cCount }} Courses</span>
                                @endif
                                @if($bCount > 0)
                                    <span class="text-[10px] text-secondary font-bold">{{ $bCount }} Bundles</span>
                                @endif
                            </div>
                        @endif
                    </td>

                    {{-- Discount Column --}}
                    <td class="px-6 py-4">
                        <span class="text-green-600 font-black text-sm">
                            {{ $coupon->type == 'percentage' ? $coupon->value . '%' : '₹' . number_format($coupon->value) }} OFF
                        </span>
                    </td>

                    {{-- Usage Column --}}
                    <td class="px-6 py-4">
                        <div class="w-full max-w-[100px]">
                            <div class="flex justify-between text-[9px] text-mutedText mb-1 font-bold">
                                <span>{{ $coupon->used_count }}</span>
                                <span>{{ $coupon->usage_limit }}</span>
                            </div>
                            <div class="h-1.5 w-full bg-navy rounded-full overflow-hidden">
                                <div class="h-full bg-primary rounded-full transition-all duration-500"
                                     style="width: {{ min(($coupon->used_count / $coupon->usage_limit) * 100, 100) }}%"></div>
                            </div>
                            <div class="mt-1 text-[9px] text-mutedText italic">
                                Exp: {{ $coupon->expiry_date ? $coupon->expiry_date->format('M d, Y') : 'Lifetime' }}
                            </div>
                        </div>
                    </td>

                    {{-- Actions Column --}}
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                            <button @click="openModal('edit', {{ $coupon->id }})" class="p-2 text-mutedText hover:text-primary hover:bg-primary/5 rounded-lg transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button @click="deleteCoupon({{ $coupon->id }})" class="p-2 text-mutedText hover:text-red-600 hover:bg-red-500/5 rounded-lg transition-all">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-12 text-center text-mutedText text-xs italic">
                        No coupons found. Click "Create Coupon" to add one.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- 2. MOBILE CARDS (Responsive) --}}
<div class="md:hidden space-y-4 animate-fade-in">
    @forelse($coupons as $coupon)
        <div class="bg-surface border border-primary/5 rounded-2xl p-4 shadow-sm relative overflow-hidden">
            {{-- Status Strip --}}
            <div class="absolute left-0 top-0 bottom-0 w-1 {{ $coupon->is_active ? 'bg-green-500' : 'bg-red-500' }}"></div>

            <div class="flex items-center justify-between mb-4 pl-3">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" /></svg>
                    </div>
                    <div>
                        <h3 class="text-mainText font-bold text-sm uppercase tracking-wider">{{ $coupon->code }}</h3>
                        <span class="text-[10px] font-bold {{ $coupon->is_active ? 'text-green-600' : 'text-red-500' }}">{{ $coupon->is_active ? 'ACTIVE' : 'INACTIVE' }}</span>
                    </div>
                </div>
                <div class="flex gap-1">
                    <button @click="openModal('edit', {{ $coupon->id }})" class="text-mutedText p-1.5 bg-navy rounded-lg border border-primary/10"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
                    <button @click="deleteCoupon({{ $coupon->id }})" class="text-red-500 p-1.5 bg-navy rounded-lg border border-primary/10"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                </div>
            </div>

            <div class="space-y-3 pl-3">
                <div class="bg-navy p-3 rounded-xl border border-primary/5 flex justify-between items-center">
                    <span class="text-[10px] font-bold text-mutedText uppercase tracking-wider">Discount</span>
                    <span class="text-green-500 font-black text-sm">{{ $coupon->type == 'percentage' ? $coupon->value . '%' : '₹' . number_format($coupon->value) }} OFF</span>
                </div>

                <div class="flex justify-between items-center text-[10px] text-mutedText">
                    <span>Used: <strong class="text-mainText">{{ $coupon->used_count }}</strong> / {{ $coupon->usage_limit }}</span>
                    <span>Exp: {{ $coupon->expiry_date ? $coupon->expiry_date->format('M d') : 'Lifetime' }}</span>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center text-mutedText py-10 text-xs italic">No coupons found.</div>
    @endforelse
</div>

{{-- PAGINATION --}}
@if($coupons->hasPages())
    <div class="mt-8 flex items-center justify-between px-2 pagination">
        <span class="text-[10px] text-mutedText font-bold uppercase tracking-widest">Page {{ $coupons->currentPage() }}</span>
        <div class="flex gap-2">
            @if($coupons->onFirstPage())
                <span class="px-4 py-2 bg-surface/50 border border-primary/5 rounded-xl text-[10px] font-bold text-mutedText opacity-50 cursor-not-allowed">PREVIOUS</span>
            @else
                <a href="{{ $coupons->previousPageUrl() }}" class="px-4 py-2 bg-surface border border-primary/10 rounded-xl text-[10px] font-bold text-mainText hover:bg-primary hover:text-customWhite transition shadow-sm">PREVIOUS</a>
            @endif

            @if($coupons->hasMorePages())
                <a href="{{ $coupons->nextPageUrl() }}" class="px-4 py-2 bg-surface border border-primary/10 rounded-xl text-[10px] font-bold text-mainText hover:bg-primary hover:text-customWhite transition shadow-sm">NEXT PAGE</a>
            @else
                <span class="px-4 py-2 bg-surface/50 border border-primary/5 rounded-xl text-[10px] font-bold text-mutedText opacity-50 cursor-not-allowed">NEXT PAGE</span>
            @endif
        </div>
    </div>
@endif
