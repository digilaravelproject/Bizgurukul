@extends('layouts.admin')

@section('title', 'Referral History')

@section('content')
<div class="space-y-8 font-sans text-mainText" x-data="historyManager()" x-init="init()">

    <x-admin.table.filter 
        placeholder="Search affiliates..." 
        :show-date-filter="true"
        id="historyFilter"
    />

    {{-- Main Content Card --}}
    <div class="bg-surface rounded-2xl shadow-sm border border-primary/10 overflow-hidden relative">
        <div class="p-6 border-b border-primary/5 flex justify-between items-center bg-navy/30">
            <h3 class="text-lg font-bold text-mainText flex items-center gap-2">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Recent Conversions
            </h3>
        </div>

        <div class="overflow-x-auto relative min-h-[300px]">
            <div x-show="loading" class="absolute inset-0 bg-surface/80 backdrop-blur-sm z-10 flex items-center justify-center" x-cloak>
                <div class="flex flex-col items-center gap-3">
                    <svg class="w-10 h-10 animate-spin text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                    </svg>
                    <span class="text-xs font-bold text-primary animate-pulse uppercase tracking-widest">Updating...</span>
                </div>
            </div>
            <table class="w-full text-left">
                <thead class="bg-primary/5 text-xs uppercase text-primary font-bold tracking-wider border-b border-primary/10">
                    <tr>
                        <th class="px-6 py-4">Date & Time</th>
                        <th class="px-6 py-4">Affiliate</th>
                        <th class="px-6 py-4">Referred User</th>
                        <th class="px-6 py-4">Product / Course</th>
                        <th class="px-6 py-4 text-right">Commission</th>
                        <th class="px-6 py-4 text-center">Status & Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary/5" id="historyTableBody">
                    @include('admin.affiliate.partials.history_table')
                </tbody>
            </table>
        </div>

        <div id="historyPagination" class="p-6 border-t border-primary/5 bg-navy/20">
            <x-admin.table.pagination :records="$commissions" />
        </div>
    </div>
</div>

@push('scripts')
<script>
    function historyManager() {
        return {
            loading: false,
            search: '',
            perPage: 20,
            startDate: '',
            endDate: '',
            lastUrl: "{{ route('admin.affiliate.history') }}",

            init() {
                // No special init needed, filters bind to search/perPage
            },

            updateTable() {
                this.fetchHistory("{{ route('admin.affiliate.history') }}");
            },

            fetchHistory(url = null) {
                let finalUrl = url || this.lastUrl;
                this.lastUrl = finalUrl;
                this.goToPage(finalUrl);
            },

            async goToPage(url) {
                if (!url) return;
                this.loading = true;
                this.lastUrl = url;
                try {
                    let targetUrl = new URL(url.includes('http') ? url : window.location.origin + url);
                    
                    targetUrl.searchParams.set('search', this.search || '');
                    targetUrl.searchParams.set('per_page', this.perPage || 20);
                    targetUrl.searchParams.set('start_date', this.startDate || '');
                    targetUrl.searchParams.set('end_date', this.endDate || '');
                    targetUrl.searchParams.set('_t', new Date().getTime());

                    const response = await fetch(targetUrl, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'Accept': 'application/json'
                        }
                    });
                    const data = await response.json();
                    if (data.status) {
                        document.getElementById('historyTableBody').innerHTML = data.table;
                        document.getElementById('historyPagination').innerHTML = data.pagination;
                    }
                } catch (error) {
                    console.error('Fetch error:', error);
                } finally {
                    this.loading = false;
                }
            },

            async approveEarly(id) {
                if (!confirm('Manually approve this commission early?')) return;

                this.loading = true;
                try {
                    const response = await fetch(`{{ url('admin/payouts/early-approve') }}/${id}`, {
                        method: 'POST',
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        }
                    });
                    const data = await response.json();
                    if (data.status) {
                        Toast.success(data.message);
                        this.fetchHistory(); // Stay on same page
                    } else {
                        Toast.error(data.message);
                    }
                } catch (error) {
                    console.error(error);
                    Toast.error('Something went wrong');
                } finally {
                    this.loading = false;
                }
            }
        }
    }
</script>
@endpush
@endsection
