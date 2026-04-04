@extends('layouts.admin')

@section('title', 'Order History')

@section('content')
<div class="space-y-6 font-sans text-mainText" 
     x-data="orderManager()" 
     x-init="init()">

    {{-- Header & Filters --}}
    <div class="flex flex-col gap-6">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-2xl font-black tracking-tight text-mainText uppercase italic">Order History</h1>
                <p class="text-mutedText mt-1 text-xs font-bold uppercase tracking-widest">Track user orders and view invoices.</p>
            </div>

            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 px-3 py-1.5 bg-orange-500/10 border border-orange-500/20 rounded-lg">
                    <i class="fas fa-info-circle text-orange-500 text-[10px]"></i>
                    <span class="text-[10px] text-orange-600 font-black uppercase">Pending status: Started but not completed</span>
                </div>
            </div>
        </div>

        {{-- Standard Filter Component --}}
        <x-admin.table.filter 
            :export-route="route('admin.orders.export')"
            placeholder="Search orders, transactions..."
        >
            <x-slot name="extraFilters">
                <select x-model="status" @change="updateTable(1)" 
                    class="appearance-none bg-surface border border-primary/10 rounded-xl pl-4 pr-8 py-2 text-[10px] font-black uppercase text-mutedText focus:border-primary outline-none focus:ring-4 focus:ring-primary/5 transition-all shadow-sm">
                    <option value="all">All Status</option>
                    <option value="success">Success</option>
                    <option value="pending">Pending</option>
                    <option value="failed">Failed</option>
                </select>
            </x-slot>
        </x-admin.table.filter>
    </div>

    {{-- Table Section --}}
    <div class="bg-surface rounded-3xl shadow-sm border border-primary/10 overflow-hidden relative">
        {{-- Loader --}}
        <div x-show="loading" class="absolute inset-0 bg-navy/50 backdrop-blur-[2px] z-20 flex items-center justify-center" x-transition x-cloak>
            <div class="flex flex-col items-center gap-3">
                <div class="w-10 h-10 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                <span class="text-[10px] font-black uppercase tracking-widest text-white">Loading Orders...</span>
            </div>
        </div>

        <div class="overflow-x-auto min-h-[400px]">
            <table class="w-full text-left border-collapse">
                <thead class="bg-navy/50 text-[10px] uppercase text-primary font-black tracking-[0.2em]">
                    <tr>
                        <th class="px-6 py-5 border-b border-primary/5">Date & Time</th>
                        <th class="px-6 py-5 border-b border-primary/5">Invoice & ID</th>
                        <th class="px-6 py-5 border-b border-primary/5">User</th>
                        <th class="px-6 py-5 border-b border-primary/5">Sponsor</th>
                        <th class="px-6 py-5 border-b border-primary/5">Product</th>
                        <th class="px-6 py-5 border-b border-primary/5 text-right">Amount</th>
                        <th class="px-6 py-5 border-b border-primary/5 text-center">Status</th>
                        <th class="px-6 py-5 border-b border-primary/5 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary/5" id="orders-tbody">
                    @include('admin.orders.partials.history_table')
                </tbody>
            </table>
        </div>

        {{-- Standard Pagination Component --}}
        <div id="pagination-wrapper" class="border-t border-primary/5 bg-navy/20 p-4">
            <x-admin.table.pagination :records="$orders" />
        </div>
    </div>
</div>

@push('scripts')
<script>
    function orderManager() {
        return {
            loading: false,
            search: '',
            filter: 'all_time',
            status: 'all',
            startDate: '',
            endDate: '',
            perPage: 20,
            page: 1,

            init() {
                // Listen for changes from filter components
                window.addEventListener('search-changed', (e) => {
                    this.search = e.detail.search || '';
                    this.updateTable(1);
                });

                window.addEventListener('date-changed', (e) => {
                    this.startDate = e.detail.start_date;
                    this.endDate = e.detail.end_date;
                    this.updateTable(1);
                });

                window.addEventListener('per-page-changed', (e) => {
                    this.perPage = e.detail.per_page;
                    this.updateTable(1);
                });

                window.addEventListener('page-changed', (e) => {
                    this.goToPage(e.detail.url);
                });
            },

            async updateTable(page = 1) {
                this.page = page;
                this.loading = true;

                try {
                    const params = new URLSearchParams({
                        page: this.page,
                        search: this.search,
                        filter: this.filter,
                        status: this.status,
                        per_page: this.perPage,
                        start_date: this.startDate,
                        end_date: this.endDate
                    });

                    const response = await fetch(`{{ route('admin.orders.index') }}?${params.toString()}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    if (!response.ok) throw new Error('Network error');

                    const data = await response.json();
                    document.getElementById('orders-tbody').innerHTML = data.table;
                    document.getElementById('pagination-wrapper').innerHTML = data.pagination;
                } catch (error) {
                    console.error('Error:', error);
                } finally {
                    this.loading = false;
                }
            },

            goToPage(url) {
                if (!url) return;
                const page = new URL(url).searchParams.get('page');
                this.updateTable(page);
            }
        }
    }
</script>
@endpush
@endsection
