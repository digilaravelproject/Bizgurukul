@extends('layouts.admin')

@section('title', 'Order History')

@section('content')
<div class="space-y-8 font-sans text-mainText" x-data="orderFilter()">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-mainText">Order History</h1>
            <p class="text-mutedText mt-1 text-sm">Track user orders and view invoices.</p>
        </div>

        <div class="flex flex-wrap items-center gap-3">
            <div class="relative">
                <input type="text" x-model="search" @input.debounce.500ms="fetchOrders()" placeholder="Search orders..." class="pl-10 pr-4 py-2 bg-surface text-mainText border border-primary/10 rounded-xl text-sm focus:ring-primary focus:border-primary w-64 shadow-sm">
                <svg class="w-4 h-4 text-mutedText absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>

            <select x-model="filter" @change="fetchOrders()" class="bg-surface border border-primary/10 rounded-xl text-sm font-medium focus:ring-primary text-mainText px-4 py-2">
                <option value="all_time">All Time</option>
                <option value="today">Today</option>
                <option value="7_days">7 Days</option>
                <option value="30_days">30 Days</option>
                <option value="custom">Custom Date</option>
            </select>

            <div x-show="filter === 'custom'" class="flex gap-2 items-center" x-transition x-cloak>
                <input type="date" x-model="startDate" class="bg-surface border border-primary/10 rounded-xl text-sm font-medium focus:ring-primary text-mainText px-3 py-2">
                <span class="text-mutedText">to</span>
                <input type="date" x-model="endDate" class="bg-surface border border-primary/10 rounded-xl text-sm font-medium focus:ring-primary text-mainText px-3 py-2">
                <button @click="fetchOrders()" class="bg-primary hover:bg-primary-dark text-white px-3 py-2 rounded-xl text-sm font-bold transition-colors">
                    Apply
                </button>
            </div>
        </div>
    </div>

    {{-- Main Content Card --}}
    <div class="bg-surface rounded-2xl shadow-sm border border-primary/10 overflow-hidden">

        <div class="p-6 border-b border-primary/5 flex justify-between items-center bg-navy/30">
            <h3 class="text-lg font-bold text-mainText flex items-center gap-2">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                Recent Orders
            </h3>
            <span class="text-xs font-medium text-mutedText bg-white px-3 py-1 rounded-full border border-primary/5 shadow-sm">
                Total Records: {{ $orders->total() }}
            </span>
        </div>

        <div class="overflow-x-auto relative min-h-[300px]">
            <div x-show="loading" class="absolute inset-0 bg-surface/80 backdrop-blur-sm z-10 flex items-center justify-center" x-cloak>
                <svg class="w-8 h-8 animate-spin text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </div>
            <table class="w-full text-left">
                <thead class="bg-primary/5 text-xs uppercase text-primary font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Date & Time</th>
                        <th class="px-6 py-4">Order ID</th>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Product</th>
                        <th class="px-6 py-4 text-right">Amount</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary/5" id="history-table-body">
                    @include('admin.orders.partials.history_table')
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('orderFilter', () => ({
            loading: false,
            search: '{{ request('search') }}',
            filter: 'all_time',
            startDate: '',
            endDate: '',

            async fetchOrders() {
                if (this.filter === 'custom' && (!this.startDate || !this.endDate)) return;

                this.loading = true;
                try {
                    let url = `{{ route('admin.orders.index') }}?filter=${this.filter}&search=${encodeURIComponent(this.search)}`;
                    if (this.filter === 'custom') {
                        url += `&start_date=${this.startDate}&end_date=${this.endDate}`;
                    }

                    const response = await fetch(url, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Network error');

                    const html = await response.text();
                    document.getElementById('history-table-body').innerHTML = html;
                } catch (error) {
                    console.error('Error fetching orders:', error);
                } finally {
                    this.loading = false;
                }
            }
        }));
    });

    // Support pagination links click (AJAX)
    document.addEventListener('click', async function(e) {
        let link = e.target.closest('a.page-link');
        if(!link && e.target.tagName === 'A') {
            const href = e.target.getAttribute('href');
            if (href && href.includes('page=')) {
                link = e.target;
            }
        }

        if (link && link.closest('#history-table-body')) {
            e.preventDefault();
            const component = Alpine.$data(document.querySelector('[x-data="orderFilter()"]'));
            component.loading = true;
            try {
                const response = await fetch(link.href, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                });
                if(!response.ok) throw new Error('Network Error');
                const html = await response.text();
                document.getElementById('history-table-body').innerHTML = html;
            } catch (err) {
                console.error(err);
            } finally {
                component.loading = false;
            }
        }
    });
</script>
@endpush
@endsection
