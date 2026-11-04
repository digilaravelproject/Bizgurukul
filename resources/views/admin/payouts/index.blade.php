@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="payoutManager" x-init="init()">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-mainText uppercase tracking-widest"><i class="fas fa-hand-holding-usd text-primary"></i> Payout Management</h2>
            <p class="text-xs text-mutedText font-semibold mt-1">Review and process partner withdrawal requests securely.</p>
        </div>

        {{-- Entries Per Page Selector --}}
        <div class="flex items-center gap-3 bg-surface px-4 py-2 rounded-2xl border border-primary/10 shadow-sm">
            <span class="text-[10px] font-black uppercase tracking-widest text-mutedText">Show</span>
            <select x-model="perPage" @change="fetchPayouts()" class="bg-transparent text-sm font-black text-primary outline-none cursor-pointer">
                <option value="10">10</option>
                <option value="20">20</option>
                <option value="50">50</option>
                <option value="100">100</option>
                <option value="200">200</option>
            </select>
            <span class="text-[10px] font-black uppercase tracking-widest text-mutedText">Entries</span>
        </div>
    </div>

    {{-- Filter Toolbar (Search, Status, Date Range, Reset, Export) --}}
    <div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-surface p-6 rounded-2xl border border-primary/10 shadow-sm">
        {{-- Search Input --}}
        <div class="relative w-full md:w-80 group">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                <i class="fas fa-search text-xs text-mutedText group-focus-within:text-primary transition-colors"></i>
            </div>
            <input type="text" 
                   x-model="search" 
                   @input.debounce.400ms="fetchPayouts()" 
                   placeholder="Search partner, email, UTR..." 
                   class="w-full bg-surface border border-primary/10 rounded-xl pl-11 pr-4 py-2.5 text-sm font-medium text-mainText focus:border-primary outline-none focus:ring-4 focus:ring-primary/5 transition-all shadow-sm">
        </div>

        {{-- Filters & Export --}}
        <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
            {{-- Status Filter --}}
            <div class="relative">
                <select x-model="status" 
                        @change="fetchPayouts()" 
                        class="appearance-none bg-surface border border-primary/10 rounded-xl pl-4 pr-8 py-2 text-[10px] font-black uppercase text-mutedText focus:border-primary outline-none focus:ring-4 focus:ring-primary/5 transition-all shadow-sm cursor-pointer">
                    <option value="all">All Status</option>
                    <option value="approved">Approved / Paid</option>
                    <option value="pending">Pending</option>
                    <option value="processing">Processing</option>
                    <option value="rejected">Rejected</option>
                </select>
                <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-mutedText/40">
                    <i class="fas fa-chevron-down text-[8px]"></i>
                </div>
            </div>

            {{-- Date Range (From - To) --}}
            <div class="flex items-center gap-2 bg-primary/5 p-1 rounded-xl border border-primary/10">
                <input type="date" 
                       x-model="startDate" 
                       @change="fetchPayouts()"
                       title="Start Date"
                       class="bg-transparent border-none text-xs font-bold text-mainText focus:ring-0 px-2 py-1 placeholder-mutedText">
                <span class="text-mutedText text-[10px] font-black uppercase tracking-widest">to</span>
                <input type="date" 
                       x-model="endDate" 
                       @change="fetchPayouts()"
                       title="End Date"
                       class="bg-transparent border-none text-xs font-bold text-mainText focus:ring-0 px-2 py-1 placeholder-mutedText">
            </div>

            {{-- Reset Button --}}
            <button @click="resetFilters()" 
                    class="p-2.5 bg-primary/5 hover:bg-primary/10 text-mutedText hover:text-primary rounded-xl transition-all border border-primary/10" 
                    title="Reset Filters">
                <i class="fas fa-undo-alt text-xs"></i>
            </button>

            {{-- Export Excel Button --}}
            <button @click="exportData()" 
                    class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-lg shadow-green-600/20 active:scale-95 group">
                <svg class="w-4 h-4 group-hover:bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Export Excel</span>
            </button>
        </div>
    </div>

    <div class="bg-surface rounded-3xl border border-primary/10 shadow-xl shadow-primary/5 overflow-hidden relative">
        {{-- Loading Overlay --}}
        <div x-show="isLoading" x-transition.opacity class="absolute inset-0 bg-surface/60 backdrop-blur-[2px] z-10 flex items-center justify-center" style="display: none;">
            <div class="flex flex-col items-center gap-3">
                <i class="fas fa-circle-notch fa-spin text-3xl text-primary"></i>
                <span class="text-[10px] font-black uppercase tracking-widest text-primary animate-pulse">Updating...</span>
            </div>
        </div>

        <div id="payouts-container">
            @include('admin.payouts.partials.payout_table')
        </div>
    </div>
</div>

@push('scripts')
<script>
    const initPayoutManager = () => {
        Alpine.data('payoutManager', () => ({
            perPage: 20,
            search: '',
            status: 'all',
            startDate: '',
            endDate: '',
            isLoading: false,

            init() {
                // Listen for pagination clicks
                document.addEventListener('click', (e) => {
                    const link = e.target.closest('.ajax-pagination a');
                    if (link) {
                        e.preventDefault();
                        this.fetchPayouts(link.href);
                    }
                });
            },

            exportData() {
                const params = new URLSearchParams({
                    search: this.search || '',
                    status: this.status || '',
                    start_date: this.startDate || '',
                    end_date: this.endDate || ''
                });
                window.location.href = `{{ route('admin.payouts.export') }}?${params.toString()}`;
            },

            resetFilters() {
                this.search = '';
                this.status = 'all';
                this.startDate = '';
                this.endDate = '';
                this.perPage = 20;
                this.fetchPayouts("{{ route('admin.payouts.index') }}");
            },

            async fetchPayouts(url = null) {
                this.isLoading = true;
                
                try {
                    let fetchUrl = new URL(url || "{{ route('admin.payouts.index') }}", window.location.origin);
                    fetchUrl.searchParams.set('per_page', this.perPage);
                    fetchUrl.searchParams.set('search', this.search || '');
                    fetchUrl.searchParams.set('status', this.status || 'all');
                    fetchUrl.searchParams.set('start_date', this.startDate || '');
                    fetchUrl.searchParams.set('end_date', this.endDate || '');
                    fetchUrl.searchParams.set('_t', new Date().getTime());
                    
                    const response = await fetch(fetchUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Network response was not ok');
                    
                    const html = await response.text();
                    document.getElementById('payouts-container').innerHTML = html;
                    
                    // Update URL in browser for bookmarking/refresh without reload
                    window.history.pushState({}, '', fetchUrl);

                } catch (error) {
                    console.error('Error fetching payouts:', error);
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong while loading the data!',
                        background: '#FFFFFF',
                        color: '#2D2D2D'
                    });
                } finally {
                    this.isLoading = false;
                }
            }
        }));
    }

    if (window.Alpine) {
        initPayoutManager();
    } else {
        document.addEventListener('alpine:init', initPayoutManager);
    }
</script>
@endpush
@endsection
