<div class="bg-white rounded-[2rem] border border-slate-200 overflow-hidden shadow-sm">
    <table class="w-full text-left">
        <thead class="bg-slate-50 border-b border-slate-200">
            <tr>
                <th class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-widest">Code Info</th>
                <th class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-widest">Scope & Items</th>
                <th class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-widest">Benefit</th>
                <th class="px-6 py-5 text-[10px] font-black text-slate-500 uppercase tracking-widest text-right">Action
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($coupons as $coupon)
                @php
                    // Safety check: Agar model casting nahi chal rahi toh manual decode karega
                    $courses = is_array($coupon->selected_courses)
                        ? $coupon->selected_courses
                        : json_decode($coupon->selected_courses, true);
                    $bundles = is_array($coupon->selected_bundles)
                        ? $coupon->selected_bundles
                        : json_decode($coupon->selected_bundles, true);

                    $courseCount = is_array($courses) ? count($courses) : 0;
                    $bundleCount = is_array($bundles) ? count($bundles) : 0;
                @endphp
                <tr class="hover:bg-slate-50/50 transition-all group">
                    <td class="px-6 py-4">
                        <p class="text-sm font-black text-[#0777be] uppercase tracking-tighter">{{ $coupon->code }}</p>
                        <p class="text-[10px] text-slate-400 font-bold uppercase italic">Usage:
                            {{ $coupon->used_count }}/{{ $coupon->usage_limit }}</p>
                    </td>

                    <td class="px-6 py-4">
                        @if ($coupon->coupon_type === 'general')
                            <span
                                class="px-2 py-1 bg-blue-50 text-[#0777be] rounded text-[9px] font-black border border-blue-100 uppercase">
                                Global Platform
                            </span>
                        @else
                            <div class="flex flex-wrap gap-1">
                                <span
                                    class="px-2 py-1 bg-slate-100 text-slate-500 rounded text-[9px] font-black border border-slate-200 uppercase">
                                    {{ $courseCount }} Courses
                                </span>
                                <span
                                    class="px-2 py-1 bg-slate-100 text-slate-500 rounded text-[9px] font-black border border-slate-200 uppercase">
                                    {{ $bundleCount }} Bundles
                                </span>
                            </div>
                        @endif
                    </td>

                    <td class="px-6 py-4">
                        <p class="text-xs font-black text-green-600">
                            {{ $coupon->type == 'percentage' ? $coupon->value . '%' : '₹' . number_format($coupon->value) }}
                            OFF
                        </p>
                        <p class="text-[9px] text-slate-400 font-bold uppercase">
                            Expires: {{ $coupon->expiry_date ? $coupon->expiry_date->format('M d, Y') : 'No Expiry' }}
                        </p>
                    </td>

                    <td class="px-6 py-4 text-right">
                        <div class="flex items-center justify-end gap-2">
                            <a href="{{ route('admin.coupons.edit', $coupon->id) }}"
                                class="p-2 text-slate-300 hover:text-[#0777be] transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <button onclick="confirmCouponDelete({{ $coupon->id }}, '{{ $coupon->code }}')"
                                class="p-2 text-slate-300 hover:text-red-500 transition-colors">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                        </div>
                        <form id="coupon-delete-form-{{ $coupon->id }}"
                            action="{{ route('admin.coupons.destroy', $coupon->id) }}" method="POST" class="hidden">
                            @csrf @method('DELETE')
                        </form>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4"
                        class="px-6 py-12 text-center text-slate-400 italic text-sm font-bold uppercase tracking-widest">
                        No coupons found.
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4">{{ $coupons->links() }}</div>
