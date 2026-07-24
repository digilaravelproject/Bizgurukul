@forelse($offers as $offer)
<tr class="hover:bg-primary/5 transition-all group border-b border-primary/5 last:border-0">
    {{-- Offer Details & Banner --}}
    <td class="px-3.5 py-3.5 whitespace-nowrap">
        <div class="flex items-center gap-3 max-w-[240px]">
            @if($offer->image)
                <img src="{{ $offer->image_url }}" alt="{{ $offer->title }}" class="w-10 h-10 rounded-xl object-cover border border-primary/10 shadow-sm shrink-0">
            @else
                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-black shadow-sm shrink-0 border border-primary/20 text-xs">
                    <i class="fas fa-tags text-sm"></i>
                </div>
            @endif
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-mainText group-hover:text-primary transition-colors leading-tight truncate" title="{{ $offer->title }}">
                    {{ $offer->title }}
                </p>
                @if($offer->description)
                    <p class="text-[11px] text-mutedText/80 font-medium truncate mt-0.5" title="{{ $offer->description }}">
                        {{ $offer->description }}
                    </p>
                @endif
            </div>
        </div>
    </td>

    {{-- Reward Value & Type --}}
    <td class="px-3.5 py-3.5 whitespace-nowrap">
        <div class="flex flex-col">
            <span class="text-xs font-black text-primary">
                ₹{{ number_format($offer->reward_value, 2) }}
            </span>
            <span class="text-[10px] font-bold text-mutedText uppercase tracking-wider">
                Type: {{ ucfirst($offer->reward_type) }}
            </span>
        </div>
    </td>

    {{-- Target Amount --}}
    <td class="px-3.5 py-3.5 whitespace-nowrap">
        @if($offer->target_amount > 0)
            <span class="text-xs font-bold text-mainText">
                ₹{{ number_format($offer->target_amount, 2) }}
            </span>
        @else
            <span class="text-[10px] uppercase font-bold text-slate-400">No Target</span>
        @endif
    </td>

    {{-- Date Range --}}
    <td class="px-3.5 py-3.5 whitespace-nowrap">
        <div class="flex flex-col text-[11px] text-mutedText font-medium">
            <span>Start: {{ $offer->start_date ? $offer->start_date->format('d M Y, h:i A') : 'Immediate' }}</span>
            <span>End: {{ $offer->end_date ? $offer->end_date->format('d M Y, h:i A') : 'No Expiry' }}</span>
        </div>
    </td>

    {{-- Real-Time Status & Engine Rule Indicator --}}
    <td class="px-3.5 py-3.5 whitespace-nowrap text-center">
        @if($offer->status === 'Active')
            <div class="flex flex-col items-center gap-0.5">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-200">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    Active
                </span>
                <span class="text-[9px] font-bold text-amber-600 uppercase tracking-widest" title="Active offers are strictly EXCLUDED from standard calculations until expired">
                    <i class="fas fa-eye-slash mr-0.5"></i> Excluded
                </span>
            </div>
        @elseif($offer->status === 'Expired')
            <div class="flex flex-col items-center gap-0.5">
                <span class="inline-flex items-center gap-1.5 px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-600 border border-rose-200">
                    <i class="fas fa-clock mr-0.5"></i> Expired
                </span>
                <span class="text-[9px] font-bold text-emerald-600 uppercase tracking-widest" title="Expired offer rewards are automatically INCLUDED into calculations">
                    <i class="fas fa-check-double mr-0.5"></i> Included
                </span>
            </div>
        @elseif($offer->status === 'Upcoming')
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-amber-50 text-amber-600 border border-amber-200">
                Upcoming
            </span>
        @else
            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-gray-100 text-gray-500">
                Disabled
            </span>
        @endif
    </td>

    {{-- Actions --}}
    <td class="px-3.5 py-3.5 whitespace-nowrap text-right">
        <div class="flex items-center justify-end gap-2">
            <a href="{{ route('admin.offers.edit', $offer->id) }}" class="px-2.5 py-1 rounded-lg bg-navy hover:bg-primary/10 text-primary font-bold text-xs transition-all flex items-center gap-1">
                <i class="fas fa-edit text-[10px]"></i> Edit
            </a>
            <form action="{{ route('admin.offers.destroy', $offer->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this offer?');" class="inline">
                @csrf
                @method('DELETE')
                <button type="submit" class="px-2.5 py-1 rounded-lg bg-rose-50 hover:bg-rose-100 text-rose-600 font-bold text-xs transition-all flex items-center gap-1">
                    <i class="fas fa-trash text-[10px]"></i> Delete
                </button>
            </form>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="6" class="px-6 py-16 text-center">
        <div class="flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-primary/5 rounded-2xl flex items-center justify-center mb-3">
                <i class="fas fa-tags text-2xl text-primary/30"></i>
            </div>
            <h4 class="text-base font-black text-mainText uppercase tracking-tight">No Time-Sensitive Offers Found</h4>
            <p class="text-xs text-mutedText mt-0.5 font-medium">Create your first time-sensitive offer to get started.</p>
        </div>
    </td>
</tr>
@endforelse
