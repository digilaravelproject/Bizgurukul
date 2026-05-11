@forelse($leads as $lead)
<tr class="table-row-hover group border-b border-primary/5 last:border-0">
    <td class="px-6 py-5 whitespace-nowrap">
        <div class="flex flex-col">
            <span class="text-sm font-black text-mainText">{{ $lead->created_at->format('d M Y') }}</span>
            <span class="text-[10px] text-mutedText uppercase font-bold">{{ $lead->created_at->format('h:i A') }}</span>
        </div>
    </td>
    <td class="px-6 py-5 whitespace-nowrap">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 shrink-0 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-sm shadow-sm ring-2 ring-white">
                {{ strtoupper(substr($lead->name, 0, 1)) }}
            </div>
            <span class="text-sm font-black text-mainText">{{ $lead->name }}</span>
        </div>
    </td>
    <td class="px-6 py-5 whitespace-nowrap">
        <span class="text-xs font-black text-amber-600 bg-amber-50 px-3 py-2 rounded-lg border border-amber-100">
            {{ $lead->product_name }}
        </span>
    </td>
    <td class="px-6 py-5 whitespace-nowrap">
        <div class="flex flex-col space-y-1">
            <div class="flex items-center gap-2 text-sm font-medium text-mainText">
                <i class="fas fa-envelope text-amber-600/40 text-[10px]"></i>
                <span class="truncate max-w-[200px]">{{ $lead->email }}</span>
            </div>
            <div class="flex items-center gap-2 text-xs font-bold text-mutedText">
                <i class="fas fa-phone-alt text-slate-400 text-[10px]"></i>
                <span>{{ $lead->phone ?? $lead->mobile ?? 'Not Provided' }}</span>
            </div>
        </div>
    </td>
    <td class="px-6 py-5 text-right whitespace-nowrap">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 text-amber-600 border border-amber-200 shadow-sm">
            <i class="fas fa-hourglass-half text-[10px] animate-pulse"></i>
            <span class="text-[10px] font-black uppercase tracking-wider">Pending</span>
        </span>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="px-6 py-20 text-center">
         <div class="flex flex-col items-center justify-center">
            <div class="w-16 h-16 md:w-20 md:h-20 bg-amber-50 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-user-clock text-2xl md:text-3xl text-amber-400"></i>
            </div>
            <h4 class="text-base md:text-lg font-black text-mainText">No Pending Leads Found</h4>
        </div>
    </td>
</tr>
@endforelse
