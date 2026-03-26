@forelse($referrals as $referral)
<tr class="table-row-hover group flex flex-col md:table-row p-4 md:p-0">
    <td class="block md:table-cell px-2 md:px-6 py-2 md:py-5 whitespace-nowrap">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 shrink-0 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white font-bold text-sm shadow-sm ring-2 ring-white">
                {{ strtoupper(substr($referral->name, 0, 1)) }}
            </div>
            <span class="text-base md:text-sm font-black text-mainText truncate">{{ $referral->name }}</span>
        </div>
    </td>
    <td class="block md:table-cell px-2 md:px-6 py-3 md:py-5 border-t border-primary/5 md:border-none mt-3 md:mt-0 whitespace-nowrap">
        <div class="flex justify-between items-center md:block">
            <span class="md:hidden text-[10px] font-bold text-mutedText uppercase tracking-widest">Product</span>
            <span class="text-sm font-black text-primary bg-primary/5 px-3 py-1.5 rounded-lg border border-primary/10">
                {{ $referral->purchased_product }}
            </span>
        </div>
    </td>
    <td class="block md:table-cell px-2 md:px-6 py-3 md:py-5 border-t border-primary/5 md:border-none whitespace-normal md:whitespace-nowrap">
        <div class="flex justify-between items-start md:block">
            <span class="md:hidden text-[10px] font-bold text-mutedText uppercase tracking-widest mt-1">Contact</span>
            <div class="flex flex-col space-y-2 items-end md:items-start">
                <div class="flex items-center gap-2 text-sm font-medium text-mainText flex-row-reverse md:flex-row">
                    <div class="w-6 h-6 rounded-md bg-primary/10 flex items-center justify-center text-primary shrink-0">
                        <i class="fas fa-envelope text-[10px]"></i>
                    </div>
                    <span class="truncate max-w-[150px] sm:max-w-xs">{{ $referral->email }}</span>
                </div>
                <div class="flex items-center gap-2 text-xs font-bold text-mutedText flex-row-reverse md:flex-row">
                    <div class="w-6 h-6 rounded-md bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                        <i class="fas fa-phone-alt text-[10px]"></i>
                    </div>
                    <span>{{ $referral->phone ?? $referral->mobile ?? 'Not Provided' }}</span>
                </div>
            </div>
        </div>
    </td>
    <td class="block md:table-cell px-2 md:px-6 py-3 md:py-5 text-right border-t border-primary/5 md:border-none whitespace-nowrap">
        <div class="flex justify-between items-center md:justify-end">
            <span class="md:hidden text-[10px] font-bold text-mutedText uppercase tracking-widest">Status</span>
            <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-50 text-green-600 border border-green-200 shadow-sm">
                <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
                <span class="text-xs font-black uppercase tracking-wider">Converted</span>
            </span>
        </div>
    </td>
</tr>
@empty
<tr>
    <td colspan="4" class="px-6 py-16 md:py-20 text-center">
         <div class="flex flex-col items-center justify-center">
            <div class="w-16 h-16 md:w-20 md:h-20 bg-primary/5 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-users text-2xl md:text-3xl text-primary/40"></i>
            </div>
            <h4 class="text-base md:text-lg font-black text-mainText">No Referrals Found</h4>
        </div>
    </td>
</tr>
@endforelse
