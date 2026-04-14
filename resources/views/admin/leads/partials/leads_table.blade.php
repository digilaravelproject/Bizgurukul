@forelse($leads as $lead)
<tr class="hover:bg-primary/5 transition-all group border-b border-primary/5 last:border-0">
    <td class="px-6 py-5 whitespace-nowrap">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary/5 flex items-center justify-center text-primary font-black shadow-sm ring-1 ring-primary/10 group-hover:bg-primary/10 transition-colors">
                {{ strtoupper(substr($lead->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-sm font-black text-mainText group-hover:text-primary transition-colors leading-tight">{{ $lead->name }}</p>
                <p class="text-[10px] text-mutedText font-bold uppercase tracking-widest mt-0.5">{{ $lead->created_at->format('d M, Y') }}</p>
            </div>
        </div>
    </td>
    <td class="px-6 py-5 whitespace-nowrap">
        <div class="flex flex-col space-y-1">
            <div class="flex items-center gap-2 text-xs font-bold text-mainText">
                <i class="fas fa-envelope text-primary/40 text-[10px]"></i>
                <span class="hover:text-primary cursor-pointer">{{ $lead->email }}</span>
            </div>
            <div class="flex items-center gap-2 text-[10px] font-bold text-mutedText">
                <i class="fas fa-phone-alt text-primary/40 text-[10px]"></i>
                <span>{{ $lead->mobile ?? 'N/A' }}</span>
            </div>
        </div>
    </td>
    <td class="px-6 py-5 whitespace-nowrap">
        @if($lead->sponsor)
            <div class="flex items-center gap-2">
                <div class="w-7 h-7 rounded-full bg-secondary/10 flex items-center justify-center text-secondary text-[10px] font-black border border-secondary/20">
                    {{ strtoupper(substr($lead->sponsor->name, 0, 1)) }}
                </div>
                <span class="text-xs font-bold text-mainText">{{ $lead->sponsor->name }}</span>
            </div>
        @else
            <span class="px-2 py-1 rounded-md bg-slate-100 text-[10px] font-black uppercase text-slate-400 tracking-widest border border-slate-200">
                Direct
            </span>
        @endif
    </td>
    <td class="px-6 py-5 whitespace-nowrap">
        <span class="px-3 py-1.5 rounded-lg bg-surface border border-primary/10 text-[10px] font-black text-primary uppercase tracking-wider shadow-sm">
            {{ $lead->product_name }}
        </span>
    </td>
    <td class="px-6 py-5 whitespace-nowrap text-right">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 text-amber-600 border border-amber-200 shadow-sm">
            <i class="fas fa-hourglass-half text-[10px] animate-pulse"></i>
            <span class="text-[10px] font-black uppercase tracking-wider">Pending</span>
        </span>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="px-6 py-24 text-center">
        <div class="flex flex-col items-center justify-center">
            <div class="w-20 h-20 bg-primary/5 rounded-3xl flex items-center justify-center mb-6 shadow-inner">
                <i class="fas fa-user-clock text-3xl text-primary/20"></i>
            </div>
            <h4 class="text-lg font-black text-mainText uppercase tracking-tighter">No Leads Found</h4>
            <p class="text-sm text-mutedText mt-1 font-medium">No pending leads match your criteria.</p>
        </div>
    </td>
</tr>
@endforelse
