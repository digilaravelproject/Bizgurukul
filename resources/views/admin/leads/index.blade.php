@extends('layouts.admin')

@section('content')
<div x-data="leadManager()" x-init="init()">
    {{-- Header Section --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-mainText tracking-tight">Lead <span class="text-primary">Management</span></h2>
            <p class="text-sm text-mutedText font-medium mt-1">Monitor and track potential customers who haven't completed their registration.</p>
        </div>
    </div>

    {{-- Filter Bar Component --}}
    <x-admin.table.filter 
        placeholder="Search leads by name, email, mobile..." 
        exportAction="exportLeads"
    />

    {{-- Main Table Section --}}
    <div class="bg-surface rounded-3xl border border-primary/10 shadow-sm overflow-hidden relative">
        {{-- Loading Overlay --}}
        <div x-show="loading" 
             class="absolute inset-0 bg-surface/60 backdrop-blur-[2px] z-20 flex items-center justify-center transition-all"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0"
             x-cloak>
            <div class="flex flex-col items-center gap-3">
                <div class="w-12 h-12 border-4 border-primary/10 border-t-primary rounded-full animate-spin"></div>
                <span class="text-[10px] font-black uppercase tracking-[0.2em] text-primary animate-pulse">Refreshing Data</span>
            </div>
        </div>

        <div class="overflow-x-auto overflow-y-hidden">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-primary/5 text-[10px] font-black text-mutedText uppercase tracking-[0.2em] border-b border-primary/10">
                        <th class="px-6 py-5">Lead Profile</th>
                        <th class="px-6 py-5">Contact Details</th>
                        <th class="px-6 py-5">Sponsor (Affiliate)</th>
                        <th class="px-6 py-5">Product Preference</th>
                        <th class="px-6 py-5 text-right">Status</th>
                    </tr>
                </thead>
                <tbody id="leads-tbody" class="divide-y divide-primary/5">
                    @include('admin.leads.partials.leads_table', ['leads' => $leads])
                </tbody>
            </table>
        </div>

        {{-- Pagination Component --}}
        <div id="pagination-wrapper">
            <x-admin.table.pagination :records="$leads" />
        </div>
    </div>
</div>

@push('scripts')
<script>
    function leadManager() {
        return {
            loading: false,
            search: '',
            startDate: '',
            endDate: '',
            perPage: 20,
            currentPage: 1,

            init() {
                // Listen for changes from filter components
                window.addEventListener('search-changed', (e) => {
                    this.search = e.detail.search;
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
                this.loading = true;
                this.currentPage = page;
                
                try {
                    const params = new URLSearchParams({
                        search: this.search,
                        start_date: this.startDate,
                        end_date: this.endDate,
                        per_page: this.perPage,
                        page: this.currentPage
                    });

                    const response = await fetch(`{{ route('admin.leads.index') }}?${params.toString()}`, {
                        headers: {
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    });

                    if (!response.ok) throw new Error('Network response was not ok');

                    const data = await response.json();
                    
                    document.getElementById('leads-tbody').innerHTML = data.table;
                    document.getElementById('pagination-wrapper').innerHTML = data.pagination;
                } catch (error) {
                    console.error('Error fetching leads:', error);
                } finally {
                    this.loading = false;
                }
            },

            goToPage(url) {
                if (!url) return;
                const page = new URL(url).searchParams.get('page');
                this.updateTable(page);
            },

            resetFilters() {
                this.search = '';
                this.startDate = '';
                this.endDate = '';
                this.updateTable(1);
            },

            exportLeads() {
                const params = new URLSearchParams({
                    search: this.search,
                    start_date: this.startDate,
                    end_date: this.endDate
                });
                
                window.location.href = `{{ route('admin.leads.export') }}?${params.toString()}`;
            }
        }
    }
</script>
@endpush
@endsection
