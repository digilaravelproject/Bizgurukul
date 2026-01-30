{{-- Save this as resources/views/admin/coupons/partials/table_rows.blade.php --}}
@forelse($coupons as $coupon)
    <tr class="hover:bg-slate-50 transition-colors border-b border-slate-50 last:border-none group">
        <td class="px-6 py-4">
            <div class="flex items-center gap-3">
                <div class="h-10 w-10 rounded-lg bg-surface flex items-center justify-center text-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M15 5v2m0 4v2m0 4v2M5 5a2 2 0 00-2 2v3a2 2 0 110 4v3a2 2 0 002 2h14a2 2 0 002-2v-3a2 2 0 110-4V7a2 2 0 00-2-2H5z" />
                    </svg>
                </div>
                <div>
                    <p class="font-bold text-navy text-sm uppercase tracking-wider">{{ $coupon->code }}</p>
                    <span
                        class="text-[10px] font-bold px-2 py-0.5 rounded {{ $coupon->is_active ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700' }}">
                        {{ $coupon->is_active ? 'ACTIVE' : 'INACTIVE' }}
                    </span>
                </div>
            </div>
        </td>
        <td class="px-6 py-4">
            @if ($coupon->coupon_type === 'general')
                <span
                    class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-50 text-blue-700">
                    Store-wide
                </span>
            @else
                <div class="flex flex-col gap-1">
                    @php
                        $cCount = is_array($coupon->selected_courses) ? count($coupon->selected_courses) : 0;
                        $bCount = is_array($coupon->selected_bundles) ? count($coupon->selected_bundles) : 0;
                    @endphp
                    @if ($cCount > 0)
                        <span class="text-xs text-mutedText font-medium">{{ $cCount }} Courses</span>
                    @endif
                    @if ($bCount > 0)
                        <span class="text-xs text-mutedText font-medium">{{ $bCount }} Bundles</span>
                    @endif
                </div>
            @endif
        </td>
        <td class="px-6 py-4">
            <p class="font-bold text-green-600">
                {{ $coupon->type == 'percentage' ? $coupon->value . '%' : '₹' . number_format($coupon->value) }} OFF
            </p>
        </td>
        <td class="px-6 py-4">
            <div class="flex items-center gap-2">
                <div class="w-16 bg-slate-200 rounded-full h-1.5">
                    <div class="bg-primary h-1.5 rounded-full"
                        style="width: {{ min(($coupon->used_count / $coupon->usage_limit) * 100, 100) }}%"></div>
                </div>
                <span
                    class="text-xs font-medium text-mutedText">{{ $coupon->used_count }}/{{ $coupon->usage_limit }}</span>
            </div>
            <p class="text-[10px] text-slate-400 mt-1">
                Exp: {{ $coupon->expiry_date ? $coupon->expiry_date->format('M d, Y') : 'Lifetime' }}
            </p>
        </td>
        <td class="px-6 py-4 text-right">
            <div class="flex items-center justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                <button @click="openModal({{ $coupon->id }})"
                    class="p-2 text-slate-400 hover:text-primary transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                    </svg>
                </button>
                <button @click="deleteItem({{ $coupon->id }})"
                    class="p-2 text-slate-400 hover:text-red-500 transition-colors">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                    </svg>
                </button>
            </div>
        </td>
    </tr>
@empty
    <tr>
        <td colspan="5" class="px-6 py-12 text-center">
            <div class="flex flex-col items-center justify-center text-mutedText">
                <svg class="w-12 h-12 mb-3 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M9 14l6-6m-5.5.5h.01m4.99 5h.01M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16l3.5-2 3.5 2 3.5-2 3.5 2z" />
                </svg>
                <span class="font-medium">No coupons found.</span>
            </div>
        </td>
    </tr>
@endforelse
