@extends('layouts.user.app')

@section('content')
<div class="space-y-6 md:space-y-8 font-sans text-mainText" x-data="walletManager()" x-init="init()">

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
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 md:gap-6">
        <div class="bg-surface rounded-2xl p-5 border border-primary/10 shadow-sm relative overflow-hidden group hover:-translate-y-1 transition duration-300">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-blue-500/10 rounded-full group-hover:scale-150 transition duration-500"></div>
            <p class="text-[10px] md:text-sm font-bold text-mutedText uppercase tracking-widest mb-1 relative z-10">Total Revenue</p>
            <h3 class="text-xl md:text-2xl font-black text-mainText tracking-tight relative z-10">₹{{ number_format($dashboardData['total_earnings'], 2) }}</h3>
        </div>

        <div class="bg-surface rounded-2xl p-5 border-2 border-emerald-500/30 shadow-lg relative overflow-hidden group hover:-translate-y-1 transition duration-300">
            <div class="absolute top-0 right-0 w-full h-full bg-gradient-to-bl from-emerald-500/5 to-transparent"></div>
            <p class="text-[10px] md:text-sm font-bold text-emerald-500 uppercase tracking-widest mb-1 relative z-10 flex items-center gap-2"><i class="fas fa-check-circle"></i> Available</p>
            <h3 class="text-xl md:text-2xl font-black text-mainText tracking-tight relative z-10">₹{{ number_format($dashboardData['available_balance'], 2) }}</h3>
            @if($dashboardData['tds_enabled'])
                <p class="text-[9px] font-bold text-mutedText/60 uppercase tracking-tighter relative z-10">Payout: ₹{{ number_format($dashboardData['available_balance_net'], 2) }}</p>
            @endif
        </div>

        <div class="bg-surface rounded-2xl p-5 border border-amber-500/30 shadow-sm relative overflow-hidden group hover:-translate-y-1 transition duration-300">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-amber-500/10 rounded-full group-hover:scale-150 transition duration-500"></div>
            <p class="text-[10px] md:text-sm font-bold text-amber-500 uppercase tracking-widest mb-1 relative z-10 flex items-center gap-2"><i class="fas fa-hourglass-half text-xs"></i> On Hold</p>
            <h3 class="text-xl md:text-2xl font-black text-mainText tracking-tight relative z-10">₹{{ number_format($dashboardData['on_hold_balance'], 2) }}</h3>
            @if($dashboardData['tds_enabled'])
                <p class="text-[9px] font-bold text-mutedText/60 uppercase tracking-tighter relative z-10">Net: ₹{{ number_format($dashboardData['on_hold_balance_net'], 2) }}</p>
            @endif
        </div>

        <div class="bg-surface rounded-2xl p-5 border border-blue-500/30 shadow-sm relative overflow-hidden group hover:-translate-y-1 transition duration-300">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-blue-500/10 rounded-full group-hover:scale-150 transition duration-500"></div>
            <p class="text-[10px] md:text-sm font-bold text-blue-500 uppercase tracking-widest mb-1 relative z-10 flex items-center gap-2"><i class="fas fa-spinner fa-spin text-xs"></i> Pending</p>
            <h3 class="text-xl md:text-2xl font-black text-mainText tracking-tight relative z-10">₹{{ number_format($dashboardData['pending_balance'], 2) }}</h3>
        </div>

        <div class="bg-surface rounded-2xl p-5 border border-primary/10 shadow-sm relative overflow-hidden group hover:-translate-y-1 transition duration-300">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-primary/10 rounded-full group-hover:scale-150 transition duration-500"></div>
            <p class="text-[10px] md:text-sm font-bold text-mutedText uppercase tracking-widest mb-1 relative z-10">Total Paid</p>
            <h3 class="text-xl md:text-2xl font-black text-mainText tracking-tight relative z-10">₹{{ number_format($dashboardData['total_withdrawn'], 2) }}</h3>
        </div>

        @if($dashboardData['tds_enabled'])
        <div class="bg-surface rounded-2xl p-5 border border-red-500/10 shadow-sm relative overflow-hidden group hover:-translate-y-1 transition duration-300">
            <div class="absolute -right-4 -top-4 w-16 h-16 bg-red-500/5 rounded-full group-hover:scale-150 transition duration-500"></div>
            <p class="text-[10px] md:text-sm font-bold text-red-500/70 uppercase tracking-widest mb-1 relative z-10">TDS Deducted</p>
            <h3 class="text-xl md:text-2xl font-black text-mainText tracking-tight relative z-10">₹{{ number_format($dashboardData['total_tds'], 2) }}</h3>
        </div>
        @endif
    </div>

    {{-- FILTERS --}}
    <x-admin.table.filter 
        placeholder="Filter by source..." 
        :showExport="false">
    </x-admin.table.filter>

    {{-- TABS --}}
    <div x-data="{ tab: 'history' }">
        <div class="flex items-center border-b border-primary/10 mb-6 gap-6 overflow-x-auto no-scrollbar">
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
                    <table class="w-full text-left">
                        <thead class="bg-navy/50 text-[10px] md:text-xs uppercase text-mutedText font-bold tracking-widest border-b border-primary/10">
                            <tr>
                                @if($dashboardData['available_balance'] > 0)
                                <th class="px-6 py-4 w-10 text-center">
                                    <input type="checkbox" @change="toggleAll($event)" :checked="isAllSelected" class="w-4 h-4 rounded border-primary/30 text-primary focus:ring-primary/50 bg-navy cursor-pointer">
                                </th>
                                @endif
                                <th class="px-6 py-4">Date</th>
                                <th class="px-6 py-4">Source</th>
                                <th class="px-6 py-4 text-right">Commission</th>
                                @if($dashboardData['tds_enabled'] || $dashboardData['total_tds'] > 0)
                                <th class="px-6 py-4 text-right">TDS</th>
                                @endif
                                <th class="px-6 py-4 text-right">Payable</th>
                                <th class="px-6 py-4 text-center">Status</th>
                            </tr>
                        </thead>
                        <tbody id="commissionsTableBody" class="divide-y divide-primary/5 text-sm font-semibold">
                            @include('student.wallet.partials.commissions_table')
                        </tbody>
                    </table>
                </div>
                <div id="commissionsPagination" class="px-6 py-4 border-t border-primary/10 bg-navy/30">
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
                        <tbody id="withdrawalsTableBody" class="divide-y divide-primary/5 text-sm font-semibold">
                            @include('student.wallet.partials.withdrawals_table')
                        </tbody>
                    </table>
                </div>
                <div id="withdrawalsPagination" class="px-6 py-4 border-t border-primary/10 bg-navy/30">
                    {{ $withdrawals->links() }}
                </div>
             </div>
        </div>
    </div>

    {{-- WITHDRAWAL MODAL --}}
    <div x-show="withdrawModal" class="fixed inset-0 z-50 flex items-center justify-center pointer-events-none" style="display: none;" x-cloak>
        <div x-show="withdrawModal" x-transition.opacity class="absolute inset-0 bg-navy/80 backdrop-blur-sm pointer-events-auto" @click="withdrawModal = false"></div>

        <div x-show="withdrawModal" x-transition.scale x-transition.opacity class="bg-surface border border-primary/20 rounded-3xl shadow-2xl p-6 md:p-8 w-full max-w-md mx-4 relative z-10 pointer-events-auto">
            <button @click="withdrawModal = false" class="absolute top-4 right-4 text-mutedText hover:text-mainText"><i class="fas fa-times"></i></button>

            <h3 class="text-xl font-black text-mainText mb-2 uppercase tracking-widest flex items-center gap-2"><i class="fas fa-hand-holding-usd text-primary"></i> Request Payout</h3>
            <p class="text-xs text-mutedText mb-6 font-semibold">Select commissions from the main table and click submit to process them together, or withdraw all available balance.</p>

            <form @submit.prevent="submitWithdrawal()" action="{{ route('student.wallet.withdraw') }}" method="POST">
                @csrf
                <template x-for="id in selectedCommissions" :key="id">
                    <input type="hidden" name="commission_ids[]" :value="id">
                </template>

                <div class="bg-navy p-4 rounded-2xl border border-primary/10 mb-6 flex justify-between items-center">
                    <span class="text-xs font-bold uppercase tracking-widest text-mutedText">Selected Total</span>
                    <span class="text-2xl font-black text-emerald-500" x-text="formatCurrency(selectedTotal)">₹0.00</span>
                </div>

                <div class="flex gap-4">
                    <button type="button" @click="withdrawModal = false" class="flex-1 py-3 bg-navy text-mainText font-black text-xs uppercase tracking-widest rounded-xl hover:bg-mainText hover:text-navy transition">Cancel</button>
                    <button type="submit" :disabled="selectedCommissions.length === 0" class="flex-1 py-3 brand-gradient text-white font-black text-xs uppercase tracking-widest rounded-xl shadow-lg hover:shadow-primary/50 transition disabled:opacity-50 disabled:cursor-not-allowed">Confirm</button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
    function walletManager() {
        return {
            withdrawModal: false,
            isLoading: false,
            search: '',
            perPage: 20,
            startDate: '',
            endDate: '',
            selectedCommissions: [],
            lastUrl: "{{ route('student.wallet.index') }}",
            controller: null,

            get selectedTotal() {
                let total = 0;
                document.querySelectorAll('.commission-checkbox').forEach(cb => {
                    if (this.selectedCommissions.includes(parseInt(cb.value))) {
                        total += parseFloat(cb.dataset.amount);
                    }
                });
                return total;
            },

            get isAllSelected() {
                const checkboxes = document.querySelectorAll('.commission-checkbox');
                if (checkboxes.length === 0) return false;
                return Array.from(checkboxes).every(cb => this.selectedCommissions.includes(parseInt(cb.value)));
            },

            init() {
                this.$watch('search', () => this.updateTable());
                
                // Handle pagination clicks
                document.addEventListener('click', (e) => {
                    const link = e.target.closest('#commissionsPagination a, #withdrawalsPagination a');
                    if (link) {
                        e.preventDefault();
                        this.fetchData(link.href);
                    }
                });
            },

            toggleAll(e) {
                const checkboxes = document.querySelectorAll('.commission-checkbox');
                checkboxes.forEach(cb => {
                    const id = parseInt(cb.value);
                    if (e.target.checked) {
                        if (!this.selectedCommissions.includes(id)) {
                            this.selectedCommissions.push(id);
                        }
                    } else {
                        this.selectedCommissions = this.selectedCommissions.filter(item => item !== id);
                    }
                });
            },

            updateTable() {
                this.fetchData("{{ route('student.wallet.index') }}");
            },

            resetFilters() {
                this.search = '';
                this.startDate = '';
                this.endDate = '';
                this.perPage = 20;
                this.updateTable();
            },

            async fetchData(url = null) {
                let targetUrlRaw = url || this.lastUrl;
                this.lastUrl = targetUrlRaw;

                if (this.controller) this.controller.abort();
                this.controller = new AbortController();

                this.isLoading = true;
                try {
                    let targetUrl = new URL(targetUrlRaw.includes('http') ? targetUrlRaw : window.location.origin + targetUrlRaw);
                    
                    targetUrl.searchParams.set('search', this.search || '');
                    targetUrl.searchParams.set('per_page', this.perPage || 20);
                    targetUrl.searchParams.set('start_date', this.startDate || '');
                    targetUrl.searchParams.set('end_date', this.endDate || '');
                    targetUrl.searchParams.set('_t', new Date().getTime());

                    let response = await fetch(targetUrl, {
                        headers: {
                            "X-Requested-With": "XMLHttpRequest",
                            "Accept": "application/json"
                        },
                        signal: this.controller.signal
                    });

                    let result = await response.json();
                    if (result.status) {
                        document.getElementById('commissionsTableBody').innerHTML = result.commissions_table;
                        document.getElementById('commissionsPagination').innerHTML = result.commissions_pagination;
                        document.getElementById('withdrawalsTableBody').innerHTML = result.withdrawals_table;
                        document.getElementById('withdrawalsPagination').innerHTML = result.withdrawals_pagination;
                    }
                } catch (error) {
                    if (error.name !== 'AbortError') console.error('Fetch error:', error);
                } finally {
                    this.isLoading = false;
                }
            },

            formatCurrency(amount) {
                return '₹' + parseFloat(amount).toLocaleString('en-IN', {
                    minimumFractionDigits: 2,
                    maximumFractionDigits: 2
                });
            },

            async submitWithdrawal() {
                if (this.selectedCommissions.length === 0) {
                    Swal.fire({
                        icon: 'warning',
                        title: 'Selection Required',
                        text: 'Please select at least one commission to withdraw.',
                        background: '#1A1D21',
                        color: '#FFFFFF'
                    });
                    return;
                }

                const result = await Swal.fire({
                    title: 'Request Payout?',
                    text: `Are you sure you want to request payout for ${this.selectedCommissions.length} selected commission(s)?`,
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: 'Yes, Request',
                    cancelButtonText: 'Cancel',
                    background: '#1A1D21',
                    color: '#FFFFFF'
                });

                if (result.isConfirmed) {
                    this.isLoading = true;
                    try {
                        let response = await fetch("{{ route('student.wallet.withdraw') }}", {
                            method: 'POST',
                            headers: {
                                "X-Requested-With": "XMLHttpRequest",
                                "Accept": "application/json",
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({
                                commission_ids: this.selectedCommissions
                            })
                        });

                        let res = await response.json();
                        if (res.status) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Success',
                                text: res.message,
                                background: '#1A1D21',
                                color: '#FFFFFF'
                            });
                            this.selectedCommissions = [];
                            this.fetchData();
                        } else {
                            throw new Error(res.message);
                        }
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Error',
                            text: error.message || 'Something went wrong',
                            background: '#1A1D21',
                            color: '#FFFFFF'
                        });
                    } finally {
                        this.isLoading = false;
                    }
                }
            }
        }
    }
</script>
@endpush
@endsection
