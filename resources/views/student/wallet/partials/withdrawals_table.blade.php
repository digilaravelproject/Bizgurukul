@forelse($withdrawals as $withdrawal)
<tr class="hover:bg-primary/5 transition-colors">
    <td class="px-6 py-4">
        <span class="block text-mainText">{{ $withdrawal->created_at->format('d M Y') }}</span>
        <span class="block text-[10px] text-mutedText">{{ $withdrawal->created_at->format('h:i A') }}</span>
    </td>
    <td class="px-6 py-4 text-right text-mainText font-black">₹{{ number_format($withdrawal->payable_amount, 2) }}</td>
    <td class="px-6 py-4 text-center">
    @if($withdrawal->status === 'pending' || $withdrawal->status === 'processing')
        <span class="inline-flex px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest {{ $withdrawal->status === 'pending' ? 'bg-amber-500/10 text-amber-500' : 'bg-blue-500/10 text-blue-500' }}">
            {{ ucfirst($withdrawal->status) }}
        </span>
    @elseif($withdrawal->status === 'approved')
        <span class="inline-flex px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-emerald-500/10 text-emerald-500">Approved</span>
    @elseif($withdrawal->status === 'rejected')
        <span class="inline-flex px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-red-500/10 text-red-500">Rejected</span>
    @endif
    </td>
    <td class="px-6 py-4">
        @if($withdrawal->transaction_id)
            <span class="block text-[10px] font-bold text-mutedText uppercase">Txn ID: <span class="text-mainText">{{ $withdrawal->transaction_id }}</span></span>
        @endif
        @if($withdrawal->admin_note)
            <span class="block text-[10px] text-mutedText mt-1">{{ \Illuminate\Support\Str::limit($withdrawal->admin_note, 30) }}</span>
        @endif
        @if(!$withdrawal->transaction_id && !$withdrawal->admin_note)
            <span class="text-[10px] text-mutedText">Awaiting processing</span>
        @endif
    </td>
</tr>
@empty
<tr>
    <td colspan="4" class="px-6 py-12 text-center">
        <i class="fas fa-university text-3xl text-mutedText/30 mb-2"></i>
        <p class="text-xs font-bold uppercase tracking-widest text-mutedText">No withdrawal requests yet.</p>
    </td>
</tr>
@endforelse
