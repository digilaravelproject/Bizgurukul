@forelse($leads as $lead)
<tr class="hover:bg-primary/5 transition-colors group">
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary/5 flex items-center justify-center text-primary font-bold shadow-sm">
                {{ strtoupper(substr($lead->name, 0, 1)) }}
            </div>
            <div>
                <p class="text-sm font-black text-mainText">{{ $lead->name }}</p>
                <p class="text-[10px] text-mutedText font-bold uppercase tracking-wider">{{ $lead->created_at->format('d M, Y') }}</p>
            </div>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <div class="flex flex-col">
            <div class="flex items-center gap-2 text-xs font-bold text-mainText">
                <i class="fas fa-envelope text-primary/40"></i>
                <span>{{ $lead->email }}</span>
            </div>
            <div class="flex items-center gap-2 text-[10px] font-bold text-mutedText mt-1">
                <i class="fas fa-phone-alt text-primary/40"></i>
                <span>{{ $lead->mobile ?? 'N/A' }}</span>
            </div>
        </div>
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        @if($lead->sponsor)
            <div class="flex items-center gap-2">
                <div class="w-6 h-6 rounded-full bg-secondary/10 flex items-center justify-center text-secondary text-[10px] font-bold">
                    {{ strtoupper(substr($lead->sponsor->name, 0, 1)) }}
                </div>
                <span class="text-xs font-bold text-mainText">{{ $lead->sponsor->name }}</span>
            </div>
        @else
            <span class="text-[10px] font-black uppercase text-mutedText/40 italic">Direct Lead</span>
        @endif
    </td>
    <td class="px-6 py-4 whitespace-nowrap">
        <span class="px-3 py-1.5 rounded-lg bg-surface border border-primary/10 text-xs font-black text-primary truncate max-w-[150px] inline-block">
            {{ $lead->product_name }}
        </span>
    </td>
    <td class="px-6 py-4 whitespace-nowrap text-right text-xs font-black">
        <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 text-amber-600 border border-amber-200">
            <i class="fas fa-hourglass-half animate-pulse"></i>
            <span class="uppercase tracking-wider">Pending</span>
        </span>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="px-6 py-20 text-center">
        <div class="flex flex-col items-center justify-center">
            <div class="w-20 h-20 bg-primary/5 rounded-2xl flex items-center justify-center mb-4">
                <i class="fas fa-user-clock text-3xl text-primary/20"></i>
            </div>
            <h4 class="text-lg font-black text-mainText">No Leads Found</h4>
            <p class="text-sm text-mutedText mt-1 font-medium">No pending leads match your criteria.</p>
        </div>
    </td>
</tr>
@endforelse
