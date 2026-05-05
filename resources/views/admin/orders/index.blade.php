@extends('layouts.admin')

@section('title', 'Order History')

@section('content')
<div class="space-y-6 font-sans text-mainText" 
     x-data="orderManager" 
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
            <select x-model="status" @change="updateTable(1)" 
                class="appearance-none bg-surface border border-primary/10 rounded-xl pl-4 pr-8 py-2 text-[10px] font-black uppercase text-mutedText focus:border-primary outline-none focus:ring-4 focus:ring-primary/5 transition-all shadow-sm">
                <option value="all">All Status</option>
                <option value="success">Success</option>
                <option value="pending">Pending</option>
                <option value="failed">Failed</option>
            </select>
        </x-admin.table.filter>
    </div>

    {{-- Table Section --}}
    <div class="bg-surface rounded-3xl shadow-sm border border-primary/10 overflow-hidden relative max-w-full">
        {{-- Loader --}}
        <div x-show="loading" class="absolute inset-0 bg-navy/50 backdrop-blur-[2px] z-20 flex items-center justify-center" x-transition x-cloak>
            <div class="flex flex-col items-center gap-3">
                <div class="w-10 h-10 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
                <span class="text-[10px] font-black uppercase tracking-widest text-white">Loading Orders...</span>
            </div>
        </div>
 
        {{-- Table Container with Responsive Scroll --}}
        <div class="overflow-auto min-h-[400px] max-h-[calc(100vh-300px)] custom-scrollbar w-full translate-z-0">
            <table class="w-full text-left border-collapse min-w-[1000px]">
                <thead class="sticky top-0 z-10 bg-navy/95 backdrop-blur-md text-[10px] uppercase text-primary font-black tracking-[0.2em] shadow-sm">
                    <tr>
                        <th class="px-6 py-5 border-b border-primary/5 whitespace-nowrap">Date & Time</th>
                        <th class="px-6 py-5 border-b border-primary/5 whitespace-nowrap">Invoice & ID</th>
                        <th class="px-6 py-5 border-b border-primary/5 whitespace-nowrap">User</th>
                        <th class="px-6 py-5 border-b border-primary/5 whitespace-nowrap">Sponsor</th>
                        <th class="px-6 py-5 border-b border-primary/5 whitespace-nowrap">Product</th>
                        <th class="px-6 py-5 border-b border-primary/5 text-right whitespace-nowrap">Amount</th>
                        <th class="px-6 py-5 border-b border-primary/5 text-center whitespace-nowrap">Status</th>
                        <th class="px-6 py-5 border-b border-primary/5 text-center whitespace-nowrap">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary/5" id="ordersTable">
                    @include('admin.orders.partials.history_table')
                </tbody>
            </table>
        </div>

        {{-- Standard Pagination Component --}}
        <div id="paginationLinks" class="border-t border-primary/5 bg-navy/20 p-4">
            <x-admin.table.pagination :records="$orders" />
        </div>
    </div>
</div>

@push('scripts')
<script>
    const initOrderManager = () => {
        Alpine.data('orderManager', () => ({
            loading: false,
            search: '{{ request('search') }}',
            filter: '{{ request('filter', 'all_time') }}',
            status: '{{ request('status', 'all') }}',
            startDate: '{{ request('start_date') }}',
            endDate: '{{ request('end_date') }}',
            page: 1,

            init() {
                // Initialize if needed
            },

            updateTable(page = 1) {
                this.page = page;
                this.fetchOrders();
            },

            async fetchOrders() {
                this.loading = true;
                try {
                    const params = new URLSearchParams({
                        page: this.page,
                        search: this.search,
                        filter: this.filter,
                        status: this.status,
                        start_date: this.startDate,
                        end_date: this.endDate,
                        _t: new Date().getTime()
                    });

                    const response = await fetch(`{{ route('admin.orders.index') }}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    
                    const data = await response.json();
                    if (data.status) {
                        document.getElementById('ordersTable').innerHTML = data.table;
                        document.getElementById('paginationLinks').innerHTML = data.pagination;
                        
                        // Update URL without reload
                        window.history.pushState({}, '', `?${params.toString()}`);
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                } finally {
                    this.loading = false;
                }
            },

            async postAction(url) {
                if (!confirm('Are you sure you want to proceed?')) return;
                
                this.loading = true;
                try {
                    const response = await fetch(url, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await response.json();
                    if (data.status) {
                        Toast.success(data.message);
                        this.fetchOrders();
                    } else {
                        Toast.error(data.message);
                    }
                } catch (error) {
                    console.error(error);
                    Toast.error('Something went wrong');
                } finally {
                    this.loading = false;
                }
            },

            resetFilters() {
                this.search = '';
                this.filter = 'all_time';
                this.status = 'all';
                this.startDate = '';
                this.endDate = '';
                this.updateTable(1);
            },

            exportData() {
                const params = new URLSearchParams({
                    search: this.search,
                    filter: this.filter,
                    status: this.status,
                    start_date: this.startDate,
                    end_date: this.endDate
                });
                window.location.href = `{{ route('admin.orders.export') }}?${params.toString()}`;
            }
        });
    }

    if (window.Alpine) {
        initOrderManager();
    } else {
        document.addEventListener('alpine:init', initOrderManager);
    }
</script>
@endpush
@endsection
