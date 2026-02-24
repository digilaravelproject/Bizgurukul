@extends('layouts.user.app')

@section('content')
<div class="space-y-6 md:space-y-8 font-sans text-mainText" x-data="{ withdrawModal: false }">

    {{-- HEADER --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-mainText">My Wallet</h1>
            <p class="text-sm font-medium text-mutedText mt-1">Manage your affiliate earnings and payouts.</p>
        </div>
        @if($dashboardData['available_balance'] > 0)
        <button @click="withdrawModal = true" class="px-6 py-3 brand-gradient text-white rounded-xl shadow-lg hover:shadow-primary/40 transition-all font-black uppercase text-xs tracking-widest active:scale-95 flex items-center gap-2 w-full md:w-auto justify-center">
            <i class="fas fa-hand-holding-usd"></i> Request Withdrawal
        </button>
        @else
        <button disabled class="px-6 py-3 bg-navy/50 text-mutedText rounded-xl font-black uppercase text-xs tracking-widest flex items-center gap-2 cursor-not-allowed w-full md:w-auto justify-center">
            <i class="fas fa-lock"></i> No Available Balance
        </button>
        @endif
    </div>

    {{-- STATS GRID --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        {{-- Total Earnings --}}
        <div class="bg-surface rounded-2xl p-5 border border-primary/10 shadow-sm relative overflow-hidden group hover:-translate-y-1 transition duration-300">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-blue-500/10 rounded-full group-hover:scale-150 transition duration-500"></div>
            <p class="text-[10px] md:text-sm font-bold text-mutedText uppercase tracking-widest mb-1 relative z-10">Total Revenue</p>
            <h3 class="text-xl md:text-3xl font-black text-mainText tracking-tight relative z-10">₹{{ number_format($dashboardData['total_earnings'], 2) }}</h3>
        </div>

        {{-- Available Balance --}}
        <div class="bg-surface rounded-2xl p-5 border-2 border-emerald-500/30 shadow-lg relative overflow-hidden group hover:-translate-y-1 transition duration-300">
            <div class="absolute top-0 right-0 w-full h-full bg-gradient-to-bl from-emerald-500/5 to-transparent"></div>
            <p class="text-[10px] md:text-sm font-bold text-emerald-500 uppercase tracking-widest mb-1 relative z-10 flex items-center gap-2"><i class="fas fa-check-circle"></i> Available</p>
            <h3 class="text-xl md:text-3xl font-black text-mainText tracking-tight relative z-10">₹{{ number_format($dashboardData['available_balance'], 2) }}</h3>
        </div>

        {{-- On Hold Balance --}}
        <div class="bg-surface rounded-2xl p-5 border border-amber-500/30 shadow-sm relative overflow-hidden group hover:-translate-y-1 transition duration-300">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-amber-500/10 rounded-full group-hover:scale-150 transition duration-500"></div>
            <p class="text-[10px] md:text-sm font-bold text-amber-500 uppercase tracking-widest mb-1 relative z-10 flex items-center gap-2"><i class="fas fa-hourglass-half text-xs"></i> On Hold</p>
            <h3 class="text-xl md:text-3xl font-black text-mainText tracking-tight relative z-10">₹{{ number_format($dashboardData['on_hold_balance'], 2) }}</h3>
        </div>

        {{-- Total Withdrawn --}}
        <div class="bg-surface rounded-2xl p-5 border border-primary/10 shadow-sm relative overflow-hidden group hover:-translate-y-1 transition duration-300">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-primary/10 rounded-full group-hover:scale-150 transition duration-500"></div>
            <p class="text-[10px] md:text-sm font-bold text-mutedText uppercase tracking-widest mb-1 relative z-10">Total Withdrawn</p>
            <h3 class="text-xl md:text-3xl font-black text-mainText tracking-tight relative z-10">₹{{ number_format($dashboardData['total_withdrawn'], 2) }}</h3>
        </div>
    </div>


    {{-- TABS --}}
    <div x-data="{ tab: 'history' }">
        <div class="flex border-b border-primary/10 mb-6 gap-6 overflow-x-auto no-scrollbar">
            <button @click="tab = 'history'" :class="tab === 'history' ? 'border-primary text-primary' : 'border-transparent text-mutedText hover:text-mainText'" class="pb-3 border-b-2 font-black uppercase text-xs md:text-sm tracking-widest whitespace-nowrap transition-colors">
                Commission History
            </button>
            <button @click="tab = 'withdrawals'" :class="tab === 'withdrawals' ? 'border-primary text-primary' : 'border-transparent text-mutedText hover:text-mainText'" class="pb-3 border-b-2 font-black uppercase text-xs md:text-sm tracking-widest whitespace-nowrap transition-colors">
                Withdrawal Requests
            </button>
        </div>

        {{-- TAB: COMMISSION HISTORY --}}
        <div x-show="tab === 'history'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
            <div class="bg-surface border border-primary/10 rounded-3xl overflow-hidden shadow-xl shadow-primary/5">
                <div class="overflow-x-auto">
                    <form id="withdrawForm" action="{{ route('student.wallet.withdraw') }}" method="POST">
                        @csrf
                        <table class="w-full text-left">
                            <thead class="bg-navy/50 text-[10px] md:text-xs uppercase text-mutedText font-bold tracking-widest border-b border-primary/10">
                                <tr>
                                    @if($dashboardData['available_balance'] > 0)
                                    <th class="px-6 py-4 w-10 text-center">
                                        <input type="checkbox" id="selectAll" class="w-4 h-4 rounded border-primary/30 text-primary focus:ring-primary/50 bg-navy cursor-pointer">
                                    </th>
                                    @endif
                                    <th class="px-6 py-4">Date</th>
                                    <th class="px-6 py-4">Source</th>
                                    <th class="px-6 py-4 text-right">Commission</th>
                                    <th class="px-6 py-4 text-right">TDS (2%)</th>
                                    <th class="px-6 py-4 text-right">Payable</th>
                                    <th class="px-6 py-4 text-center">Status</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-primary/5 text-sm font-semibold">
                                @forelse($commissions as $comm)
                                <tr class="hover:bg-primary/5 transition-colors">
                                    @if($dashboardData['available_balance'] > 0)
                                    <td class="px-6 py-4 text-center">
                                        @if($comm->status === 'available')
                                        <input type="checkbox" name="commission_ids[]" value="{{ $comm->id }}" class="commission-checkbox w-4 h-4 rounded border-primary/30 text-primary focus:ring-primary bg-navy cursor-pointer" data-amount="{{ $comm->payable_amount }}">
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
                                    <td class="px-6 py-4 text-right text-red-500/70">-₹{{ number_format($comm->tds_amount, 2) }}</td>
                                    <td class="px-6 py-4 text-right text-mainText font-black">₹{{ number_format($comm->payable_amount, 2) }}</td>
                                    <td class="px-6 py-4 text-center">
                                        @if($comm->status === 'on_hold')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-amber-500/10 text-amber-500">
                                                <i class="fas fa-hourglass-half"></i> Hold
                                            </span>
                                            <span class="block text-[9px] text-mutedText mt-1">Available: {{ $comm->available_at->format('d M') }}</span>
                                        @elseif($comm->status === 'available')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-emerald-500/10 text-emerald-500">
                                                <i class="fas fa-check"></i> Available
                                            </span>
                                        @elseif($comm->status === 'requested')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-blue-500/10 text-blue-500">
                                                <i class="fas fa-spinner fa-spin"></i> Processing
                                            </span>
                                        @elseif($comm->status === 'paid')
                                            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-primary/10 text-primary">
                                                <i class="fas fa-wallet"></i> Paid
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="px-6 py-12 text-center">
                                        <i class="fas fa-receipt text-3xl text-mutedText/30 mb-2"></i>
                                        <p class="text-xs font-bold uppercase tracking-widest text-mutedText">No commission history found.</p>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </form>
                </div>
                <div class="px-6 py-4 border-t border-primary/10 bg-navy/30">
                    {{ $commissions->links() }}
                </div>
            </div>
        </div>

        {{-- TAB: WITHDRAWALS --}}
        <div x-show="tab === 'withdrawals'" style="display: none;" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0">
             <div class="bg-surface border border-primary/10 rounded-3xl overflow-hidden shadow-xl shadow-primary/5">
                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-navy/50 text-[10px] md:text-xs uppercase text-mutedText font-bold tracking-widest border-b border-primary/10">
                            <tr>
                                <th class="px-6 py-4">Request Date</th>
                                <th class="px-6 py-4 text-right">Amount</th>
                                <th class="px-6 py-4 text-center">Status</th>
                                <th class="px-6 py-4">Details</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-primary/5 text-sm font-semibold">
                            @forelse($withdrawals as $withdrawal)
                            <tr class="hover:bg-primary/5 transition-colors">
                                <td class="px-6 py-4">
                                    <span class="block text-mainText">{{ $withdrawal->created_at->format('d M Y') }}</span>
                                    <span class="block text-[10px] text-mutedText">{{ $withdrawal->created_at->format('h:i A') }}</span>
                                </td>
                                <td class="px-6 py-4 text-right text-mainText font-black">₹{{ number_format($withdrawal->payable_amount, 2) }}</td>
                                <td class="px-6 py-4 text-center">
                                    @if($withdrawal->status === 'pending')
                                        <span class="inline-flex px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-amber-500/10 text-amber-500">Pending</span>
                                    @elseif($withdrawal->status === 'processing')
                                        <span class="inline-flex px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-blue-500/10 text-blue-500">Processing</span>
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
                                        <span class="block text-[10px] text-mutedText mt-1">{{ Str::limit($withdrawal->admin_note, 30) }}</span>
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
                        </tbody>
                    </table>
                </div>
                <div class="px-6 py-4 border-t border-primary/10 bg-navy/30">
                    {{ $withdrawals->links() }}
                </div>
             </div>
        </div>
    </div>

    {{-- WITHDRAWAL MODAL --}}
    <div x-show="withdrawModal" class="fixed inset-0 z-50 flex items-center justify-center pointer-events-none" style="display: none;">
        <div x-show="withdrawModal" x-transition.opacity class="absolute inset-0 bg-navy/80 backdrop-blur-sm pointer-events-auto" @click="withdrawModal = false"></div>

        <div x-show="withdrawModal" x-transition.scale x-transition.opacity class="bg-surface border border-primary/20 rounded-3xl shadow-2xl p-6 md:p-8 w-full max-w-md mx-4 relative z-10 pointer-events-auto">
            <button @click="withdrawModal = false" class="absolute top-4 right-4 text-mutedText hover:text-mainText"><i class="fas fa-times"></i></button>

            <h3 class="text-xl font-black text-mainText mb-2 uppercase tracking-widest flex items-center gap-2"><i class="fas fa-hand-holding-usd text-primary"></i> Request Payout</h3>
            <p class="text-xs text-mutedText mb-6 font-semibold">Select commissions from the main table and click submit to process them together, or withdraw all available balance.</p>

            <form id="modalWithdrawForm" action="{{ route('student.wallet.withdraw') }}" method="POST">
                @csrf
                <div id="hiddenInputsContainer"></div>
                <div class="bg-navy p-4 rounded-2xl border border-primary/10 mb-6 flex justify-between items-center">
                    <span class="text-xs font-bold uppercase tracking-widest text-mutedText">Selected Total</span>
                    <span class="text-2xl font-black text-emerald-500" id="selectedTotalDisplay">₹0.00</span>
                </div>

                <div class="flex gap-4">
                    <button type="button" @click="withdrawModal = false" class="flex-1 py-3 bg-navy text-mainText font-black text-xs uppercase tracking-widest rounded-xl hover:bg-mainText hover:text-navy transition">Cancel</button>
                    <button type="button" onclick="submitWithdrawal()" class="flex-1 py-3 brand-gradient text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg hover:shadow-primary/50 transition">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const selectAll = document.getElementById('selectAll');
        const checkboxes = document.querySelectorAll('.commission-checkbox');
        const totalDisplay = document.getElementById('selectedTotalDisplay');
        const hiddenContainer = document.getElementById('hiddenInputsContainer');

        function updateTotal() {
            let total = 0;
            hiddenContainer.innerHTML = '';
            checkboxes.forEach(cb => {
                if(cb.checked) {
                    total += parseFloat(cb.dataset.amount);
                    let input = document.createElement('input');
                    input.type = 'hidden';
                    input.name = 'commission_ids[]';
                    input.value = cb.value;
                    hiddenContainer.appendChild(input);
                }
            });
            totalDisplay.innerText = '₹' + total.toFixed(2);
        }

        if(selectAll) {
            selectAll.addEventListener('change', function() {
                checkboxes.forEach(cb => cb.checked = this.checked);
                updateTotal();
            });
        }

        checkboxes.forEach(cb => {
            cb.addEventListener('change', updateTotal);
        });
    });

    function submitWithdrawal() {
        const hiddenContainer = document.getElementById('hiddenInputsContainer');
        if(hiddenContainer.children.length === 0) {
            alert('Please select at least one available commission from the history table before withdrawing.');
            return;
        }
        document.getElementById('modalWithdrawForm').submit();
    }
</script>
@endpush
@endsection
