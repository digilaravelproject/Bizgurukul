@forelse($referrals as $referral)
<tr class="table-row-hover group border-b border-primary/5 last:border-0">
    <td class="px-6 py-5 whitespace-nowrap">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 shrink-0 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white font-bold text-sm shadow-sm ring-2 ring-white">
                {{ strtoupper(substr($referral->name, 0, 1)) }}
            </div>
            <span class="text-sm font-black text-mainText">{{ $referral->name }}</span>
        </div>
    </td>
    <td class="px-6 py-5 whitespace-nowrap">
        <span class="text-xs font-black text-primary bg-primary/5 px-3 py-2 rounded-lg border border-primary/10">
            {{ $referral->purchased_product }}
        </span>
    </td>
    <td class="px-6 py-5 whitespace-nowrap">
        <div class="flex flex-col space-y-1">
            <div class="flex items-center gap-2 text-sm font-medium text-mainText">
                <i class="fas fa-envelope text-primary/40 text-[10px]"></i>
                <span class="truncate max-w-[200px]">{{ $referral->email }}</span>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold text-mutedText">
                <i class="fas fa-phone-alt text-primary/40 text-[10px]"></i>
                <span>{{ $referral->phone ?? $referral->mobile ?? 'Not Provided' }}</span>
            </div>
        </div>
    </td>
    <td class="px-6 py-5 text-right whitespace-nowrap">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-50 text-green-600 border border-green-200 shadow-sm">
            <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
            <span class="text-[10px] font-black uppercase tracking-wider">Converted</span>
        </span>
    </td>
</tr>
@empty
<tr>
    <td colspan="4" class="px-6 py-20 text-center">
         <div class="flex flex-col items-center justify-center">
            <div class="w-16 h-16 md:w-20 md:h-20 bg-primary/5 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-users text-2xl md:text-3xl text-primary/40"></i>
            </div>
            <h4 class="text-base md:text-lg font-black text-mainText">No Referrals Found</h4>
        </div>
    </td>
</tr>
@endforelse
