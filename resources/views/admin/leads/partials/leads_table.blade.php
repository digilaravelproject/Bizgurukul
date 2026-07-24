@forelse($leads as $lead)
<tr class="hover:bg-primary/5 transition-all group border-b border-primary/5 last:border-0">
    {{-- Lead Profile --}}
    <td class="px-3.5 py-3.5 whitespace-nowrap">
        <div class="flex items-center gap-2.5 max-w-[200px]">
            <div class="w-8 h-8 rounded-lg bg-primary/10 flex items-center justify-center text-primary font-black shadow-sm shrink-0 text-xs">
                {{ strtoupper(substr($lead->name, 0, 1)) }}
            </div>
            <div class="min-w-0 flex-1">
                <p class="text-xs font-bold text-mainText group-hover:text-primary transition-colors leading-tight truncate" title="{{ $lead->name }}">
                    {{ $lead->name }}
                </p>
                <p class="text-[10px] text-mutedText/80 font-semibold mt-0.5">
                    {{ $lead->created_at->format('d M, Y') }}
                </p>
            </div>
        </div>
    </td>

    {{-- Contact Details --}}
    <td class="px-3.5 py-3.5 whitespace-nowrap">
        <div class="flex flex-col space-y-0.5 max-w-[220px]">
            <div class="flex items-center gap-1.5 text-xs font-semibold text-mainText truncate" title="{{ $lead->email }}">
                <i class="fas fa-envelope text-primary/50 text-[10px] shrink-0"></i>
                <span class="truncate">{{ $lead->email }}</span>
            </div>
            <div class="flex items-center gap-1.5 text-[11px] font-medium text-mutedText">
                <i class="fas fa-phone-alt text-primary/40 text-[9px] shrink-0"></i>
                <span>{{ $lead->mobile ?? 'N/A' }}</span>
            </div>
        </div>
    </td>

    {{-- Sponsor --}}
    <td class="px-3.5 py-3.5 whitespace-nowrap">
        @if($lead->sponsor)
            <div class="flex items-center gap-2 max-w-[180px]">
                <div class="w-6 h-6 rounded-full bg-secondary/10 flex items-center justify-center text-secondary text-[10px] font-black border border-secondary/20 shrink-0">
                    {{ strtoupper(substr($lead->sponsor->name, 0, 1)) }}
                </div>
                <span class="text-xs font-semibold text-mainText truncate" title="{{ $lead->sponsor->name }}">
                    {{ $lead->sponsor->name }}
                </span>
            </div>
        @else
            <span class="px-2 py-0.5 rounded-md bg-slate-100 text-[10px] font-black uppercase text-slate-400 tracking-wider border border-slate-200">
                Direct
            </span>
        @endif
    </td>

    {{-- Product Preference --}}
    <td class="px-3.5 py-3.5 whitespace-nowrap">
        <span class="inline-block max-w-[180px] truncate px-2.5 py-1 rounded-md bg-surface border border-primary/10 text-[10px] font-black text-primary uppercase tracking-wider shadow-sm" title="{{ $lead->product_name }}">
            {{ $lead->product_name }}
        </span>
    </td>

    {{-- Status --}}
    <td class="px-3.5 py-3.5 whitespace-nowrap text-right">
        <span class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-full bg-amber-50 text-amber-600 border border-amber-200/60 shadow-sm">
            <i class="fas fa-hourglass-half text-[9px] animate-pulse"></i>
            <span class="text-[10px] font-black uppercase tracking-wider">Pending</span>
        </span>
    </td>
</tr>
@empty
<tr>
    <td colspan="5" class="px-6 py-16 text-center">
        <div class="flex flex-col items-center justify-center">
            <div class="w-16 h-16 bg-primary/5 rounded-2xl flex items-center justify-center mb-3">
                <i class="fas fa-user-clock text-2xl text-primary/30"></i>
            </div>
            <h4 class="text-base font-black text-mainText uppercase tracking-tight">No Leads Found</h4>
            <p class="text-xs text-mutedText mt-0.5 font-medium">No pending leads match your criteria.</p>
        </div>
    </td>
</tr>
@endforelse
