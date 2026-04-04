@forelse($commissions as $comm)
<tr class="hover:bg-primary/5 transition-colors">
    @if($dashboardData['available_balance'] > 0)
    <td class="px-6 py-4 text-center">
        @if($comm->status === 'available')
        <input type="checkbox" 
               x-model="selectedCommissions" 
               :value="{{ $comm->id }}" 
               class="commission-checkbox w-4 h-4 rounded border-primary/30 text-primary focus:ring-primary bg-navy cursor-pointer" 
               data-amount="{{ $comm->payable_amount }}">
        @else
        <i class="fas fa-lock text-mutedText/30 text-xs text-center block"></i>
        @endif
    </td>
    @endif
    <td class="px-6 py-4">
        <span class="block text-mainText">{{ $comm->created_at->format('d M Y') }}</span>
        <span class="block text-[10px] text-mutedText">{{ $comm->created_at->format('h:i A') }}</span>
    </td>
    <td class="px-6 py-4">
        <span class="block text-mainText">{{ $comm->referredUser->name ?? 'Unknown' }}</span>
        <span class="block text-[10px] uppercase text-primary mt-0.5 tracking-wider">{{ $comm->reference->title ?? 'Product' }}</span>
    </td>
    <td class="px-6 py-4 text-right text-mutedText">₹{{ number_format($comm->amount, 2) }}</td>
    @if($dashboardData['tds_enabled'] || $dashboardData['total_tds'] > 0)
    <td class="px-6 py-4 text-right text-red-500/70">-₹{{ number_format($comm->tds_amount, 2) }}</td>
    @endif
    <td class="px-6 py-4 text-right text-mainText font-black">₹{{ number_format($comm->payable_amount, 2) }}</td>
    <td class="px-6 py-4 text-center">
        @if($comm->status === 'on_hold')
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-amber-500/10 text-amber-500">
                <i class="fas fa-hourglass-half"></i> Hold
            </span>
            <span class="block text-[10px] text-mutedText mt-1">Available: {{ $comm->available_at->format('d M, Y') }}</span>
        @elseif($comm->status === 'available')
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-emerald-500/10 text-emerald-500">
                <i class="fas fa-check"></i> Available
            </span>
        @elseif($comm->status === 'requested')
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-blue-500/10 text-blue-500">
                <i class="fas fa-spinner fa-spin"></i> Processing
            </span>
        @elseif($comm->status === 'paid')
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-emerald-500/10 text-emerald-500">
                <i class="fas fa-wallet"></i> Paid
            </span>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="{{ ($dashboardData['available_balance'] > 0 ? 1 : 0) + ($dashboardData['tds_enabled'] || $dashboardData['total_tds'] > 0 ? 6 : 5) }}" class="px-6 py-12 text-center">
        <i class="fas fa-receipt text-3xl text-mutedText/30 mb-2"></i>
        <p class="text-xs font-bold uppercase tracking-widest text-mutedText">No commission history found.</p>
    </td>
</tr>
@endforelse
