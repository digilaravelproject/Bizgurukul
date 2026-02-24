@extends('layouts.admin')

@section('content')
<div class="space-y-6">



    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-mainText uppercase tracking-widest"><i class="fas fa-hand-holding-usd text-primary"></i> Payout Management</h2>
            <p class="text-xs text-mutedText font-semibold mt-1">Review and process partner withdrawal requests securely.</p>
        </div>
    </div>

    <div class="bg-surface rounded-3xl border border-primary/10 shadow-xl shadow-primary/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-navy/50 text-[10px] uppercase text-mutedText font-black tracking-widest border-b border-primary/10">
                    <tr>
                        <th class="px-6 py-4 whitespace-nowrap">ID / Date</th>
                        <th class="px-6 py-4 whitespace-nowrap">Partner Details</th>
                        <th class="px-6 py-4 whitespace-nowrap text-right">Amount / TDS</th>
                        <th class="px-6 py-4 whitespace-nowrap text-right">Payable</th>
                        <th class="px-6 py-4 whitespace-nowrap text-center">Status</th>
                        <th class="px-6 py-4 whitespace-nowrap text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary/5 text-sm font-semibold text-mainText">
                    @forelse($withdrawals as $withdrawal)
                    <tr class="hover:bg-primary/5 transition-colors">
                        <td class="px-6 py-5 whitespace-nowrap">
                            <span class="block font-black text-primary">#{{ str_pad($withdrawal->id, 5, '0', STR_PAD_LEFT) }}</span>
                            <span class="block text-[10px] text-mutedText uppercase tracking-widest mt-1">{{ $withdrawal->created_at->format('d M y - h:i A') }}</span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl brand-gradient flex items-center justify-center text-white font-black text-sm shadow-md">
                                    {{ substr($withdrawal->user->name, 0, 1) }}
                                </div>
                                <div>
                                    <span class="block font-bold">{{ $withdrawal->user->name }}</span>
                                    <span class="block text-[10px] text-mutedText mt-0.5">{{ $withdrawal->user->email }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-right">
                             <span class="block font-bold">₹{{ number_format($withdrawal->amount, 2) }}</span>
                             <span class="block text-[10px] text-red-500/80 mt-1 uppercase tracking-widest font-black">- TDS: ₹{{ number_format($withdrawal->tds_deducted, 2) }}</span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-right">
                            <span class="block text-xl font-black text-emerald-500 tracking-tight">₹{{ number_format($withdrawal->payable_amount, 2) }}</span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-center">
                            @if($withdrawal->status === 'pending')
                                <span class="bg-amber-500/10 text-amber-500 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest"><i class="fas fa-clock"></i> Pending</span>
                            @elseif($withdrawal->status === 'processing')
                                <span class="bg-blue-500/10 text-blue-500 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest"><i class="fas fa-spinner fa-spin"></i> Processing</span>
                            @elseif($withdrawal->status === 'approved')
                                <span class="bg-emerald-500/10 text-emerald-500 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest"><i class="fas fa-check-circle"></i> Paid</span>
                            @elseif($withdrawal->status === 'rejected')
                                <span class="bg-red-500/10 text-red-500 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest"><i class="fas fa-times-circle"></i> Rejected</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-right">
                            @if(in_array($withdrawal->status, ['pending', 'processing']))
                            <div class="flex items-center justify-end gap-2" x-data="{ open: false, mode: 'approve' }">
                                <button @click="open = true; mode = 'approve'" class="w-8 h-8 rounded-full bg-emerald-500/10 text-emerald-500 hover:bg-emerald-500 hover:text-white transition flex items-center justify-center shadow-sm" title="Approve Payout">
                                    <i class="fas fa-check text-xs"></i>
                                </button>
                                <button @click="open = true; mode = 'reject'" class="w-8 h-8 rounded-full bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition flex items-center justify-center shadow-sm" title="Reject Payout">
                                    <i class="fas fa-times text-xs"></i>
                                </button>

                                {{-- MODAL --}}
                                <div x-show="open" class="fixed inset-0 z-[99]" style="display: none;">
                                    <div x-show="open" x-transition.opacity class="absolute inset-0 bg-navy/80 backdrop-blur-sm" @click="open = false"></div>
                                    <div class="fixed inset-0 flex items-center justify-center pointer-events-none p-4">
                                        <div x-show="open" x-transition.scale x-transition.opacity @click.stop class="bg-surface rounded-3xl border border-primary/20 shadow-2xl p-6 md:p-8 w-full max-w-lg pointer-events-auto relative text-left">
                                            <button @click="open = false" class="absolute top-6 right-6 text-mutedText hover:text-mainText transition"><i class="fas fa-times"></i></button>

                                            {{-- Approve Form --}}
                                            <template x-if="mode === 'approve'">
                                                <form action="{{ route('admin.payouts.approve', $withdrawal->id) }}" method="POST" class="space-y-6">
                                                    @csrf
                                                    <h3 class="text-xl font-black text-emerald-500 uppercase tracking-widest flex items-center gap-3"><i class="fas fa-check-circle text-2xl"></i> Process Payout</h3>
                                                    <p class="text-xs text-mutedText font-semibold">Verify the partner's banking details and process the payment manually, then enter the transaction reference below to complete.</p>

                                                    <div class="bg-navy p-4 rounded-2xl border-2 border-primary/10 space-y-3 font-mono text-[11px] text-mutedText">
                                                        <div class="flex justify-between border-b border-primary/5 pb-2">
                                                            <span class="uppercase tracking-widest">Payable Amount:</span> <span class="text-emerald-500 text-sm font-black">₹{{ number_format($withdrawal->payable_amount, 2) }}</span>
                                                        </div>
                                                        <div class="flex justify-between"><span>Bank Name:</span> <span class="text-mainText">{{ $withdrawal->user->bank->bank_name ?? 'N/A' }}</span></div>
                                                        <div class="flex justify-between"><span>Account No:</span> <span class="text-mainText">{{ $withdrawal->user->bank->account_number ?? 'N/A' }}</span></div>
                                                        <div class="flex justify-between"><span>IFSC:</span> <span class="text-mainText">{{ $withdrawal->user->bank->ifsc_code ?? 'N/A' }}</span></div>
                                                        <div class="flex justify-between border-t border-primary/5 pt-2"><span>UPI ID:</span> <span class="text-primary">{{ $withdrawal->user->bank->upi_id ?? 'N/A' }}</span></div>
                                                    </div>

                                                    <div class="grid grid-cols-2 gap-4">
                                                        <div>
                                                            <label class="block text-[10px] font-bold text-mutedText uppercase tracking-widest mb-2">Payment Method</label>
                                                            <select name="payment_method" required class="w-full bg-navy border border-primary/20 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary focus:ring-1 focus:ring-primary transition-all outline-none">
                                                                <option value="NEFT">NEFT</option>
                                                                <option value="IMPS">IMPS</option>
                                                                <option value="UPI">UPI</option>
                                                                <option value="RTGS">RTGS</option>
                                                                <option value="Manual / Crypto">Manual / Crypto</option>
                                                            </select>
                                                        </div>
                                                        <div>
                                                            <label class="block text-[10px] font-bold text-mutedText uppercase tracking-widest mb-2">Transaction / UTR #</label>
                                                            <input type="text" name="transaction_id" required placeholder="e.g. UTR1234..." class="w-full bg-navy border border-primary/20 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary focus:ring-1 focus:ring-primary transition-all outline-none uppercase font-mono tracking-widest">
                                                        </div>
                                                        <div class="col-span-2">
                                                            <label class="block text-[10px] font-bold text-mutedText uppercase tracking-widest mb-2">Admin Note (Optional)</label>
                                                            <input type="text" name="admin_note" placeholder="Any internal notes" class="w-full bg-navy border border-primary/20 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none">
                                                        </div>
                                                    </div>

                                                    <button type="submit" class="w-full py-4 bg-emerald-500 hover:bg-emerald-400 text-white rounded-xl font-black uppercase tracking-widest shadow-lg shadow-emerald-500/20 active:scale-95 transition-all text-xs">Verify & Confirm Paid</button>
                                                </form>
                                            </template>

                                            {{-- Reject Form --}}
                                            <template x-if="mode === 'reject'">
                                                <form action="{{ route('admin.payouts.reject', $withdrawal->id) }}" method="POST" class="space-y-6">
                                                    @csrf
                                                    <h3 class="text-xl font-black text-red-500 uppercase tracking-widest flex items-center gap-3"><i class="fas fa-times-circle text-2xl"></i> Reject Payout</h3>
                                                    <p class="text-xs text-mutedText font-semibold">The commissions in this request will be marked back as "Available" in the partner's wallet so they can re-request after fixing any issues.</p>

                                                    <div>
                                                        <label class="block text-[10px] font-bold text-mutedText uppercase tracking-widest mb-2">Rejection Reason</label>
                                                        <textarea name="admin_note" required rows="3" placeholder="e.g. Invalid KYC, Bank details mismatched..." class="w-full bg-navy border border-primary/20 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-red-500 focus:ring-1 focus:ring-red-500 transition-all outline-none resize-none"></textarea>
                                                    </div>

                                                    <button type="submit" class="w-full py-4 bg-red-500/10 text-red-500 border border-red-500/50 hover:bg-red-500 hover:text-white rounded-xl font-black uppercase tracking-widest active:scale-95 transition-all text-xs">Confirm Rejection</button>
                                                </form>
                                            </template>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            @else
                                <button class="w-8 h-8 rounded-full bg-navy/50 text-mutedText/30 cursor-not-allowed flex items-center justify-center flex-shrink-0 mx-auto border border-primary/5">
                                    <i class="fas fa-lock text-[10px]"></i>
                                </button>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-16 text-center">
                            <i class="fas fa-university text-4xl text-mutedText/30 mb-4"></i>
                            <h3 class="text-xs font-black text-mutedText uppercase tracking-widest">No Withdrawal Requests Found.</h3>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="px-6 py-4 border-t border-primary/10">
            {{ $withdrawals->links() }}
        </div>
    </div>
</div>
@endsection
