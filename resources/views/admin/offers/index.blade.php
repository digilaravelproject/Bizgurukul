@extends('layouts.admin')

@section('content')
<div x-data="offerManager()" x-init="init()">
    {{-- Header Section --}}
    <div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h2 class="text-3xl font-black text-mainText tracking-tight">Offer <span class="text-primary">Management</span></h2>
            <p class="text-sm text-mutedText font-medium mt-1">Manage time-sensitive promotional offers and conditional reward overrides.</p>
        </div>
        <a href="{{ route('admin.offers.create') }}" class="px-5 py-2.5 rounded-xl bg-primary text-white font-bold text-xs hover:bg-primary/90 transition-all shadow-md flex items-center gap-2 self-start md:self-auto">
            <i class="fas fa-plus"></i> Create New Offer
        </a>
    </div>

    {{-- Rules Explanatory Banner --}}
    <div class="mb-6 p-4 rounded-2xl bg-primary/5 border border-primary/10 flex flex-col md:flex-row items-start md:items-center justify-between gap-4 shadow-sm">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary shrink-0">
                <i class="fas fa-clock text-lg"></i>
            </div>
            <div>
                <h4 class="text-xs font-black uppercase tracking-wider text-mainText">Conditional Reward Calculation Engine</h4>
                <p class="text-xs text-mutedText font-medium">Active phase offers are <strong>excluded</strong> from standard rewards. Once an offer expires, it is <strong>automatically included</strong> into reward calculations.</p>
            </div>
        </div>
        <div class="flex items-center gap-2 shrink-0">
            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-emerald-50 text-emerald-600 border border-emerald-200">🟢 Active = Excluded</span>
            <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-50 text-rose-600 border border-rose-200">🔴 Expired = Included</span>
        </div>
    </div>

    {{-- Filter Bar --}}
    <div class="mb-4">
        <x-admin.table.filter placeholder="Search offers...">
            <select x-model="status" @change="updateTable(1)" class="appearance-none bg-surface border border-primary/10 rounded-xl pl-4 pr-8 py-2 text-[10px] font-black uppercase text-mutedText focus:border-primary outline-none focus:ring-4 focus:ring-primary/5 transition-all shadow-sm">
                <option value="">All Statuses</option>
                <option value="active">Active Phase (Excluded)</option>
                <option value="expired">Expired Phase (Included)</option>
                <option value="disabled">Disabled</option>
            </select>
        </x-admin.table.filter>
    </div>

    {{-- Main Table Section --}}
    <div class="bg-surface rounded-3xl border border-primary/10 shadow-sm overflow-hidden relative">
        {{-- Loading Overlay --}}
        <div x-show="loading" class="absolute inset-0 bg-surface/60 backdrop-blur-[2px] z-20 flex items-center justify-center transition-all" x-cloak>
            <div class="flex flex-col items-center gap-3">
                <div class="w-10 h-10 border-4 border-primary/10 border-t-primary rounded-full animate-spin"></div>
                <span class="text-[10px] font-black uppercase tracking-wider text-primary">Loading Offers...</span>
            </div>
        </div>

        <div class="overflow-x-auto hide-scrollbar">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-primary/5 text-[10px] font-black text-mutedText uppercase tracking-wider border-b border-primary/10">
                        <th class="px-3.5 py-3.5 whitespace-nowrap">Offer & Banner</th>
                        <th class="px-3.5 py-3.5 whitespace-nowrap">Reward Value</th>
                        <th class="px-3.5 py-3.5 whitespace-nowrap">Target Amount</th>
                        <th class="px-3.5 py-3.5 whitespace-nowrap">Date Window</th>
                        <th class="px-3.5 py-3.5 text-center whitespace-nowrap">Status & Engine Rule</th>
                        <th class="px-3.5 py-3.5 text-right whitespace-nowrap">Actions</th>
                    </tr>
                </thead>
                <tbody id="offers-tbody" class="divide-y divide-primary/5">
                    @include('admin.offers.partials.offers_table', ['offers' => $offers])
                </tbody>
            </table>
        </div>

        <div id="pagination-wrapper" class="overflow-x-auto hide-scrollbar border-t border-primary/5 bg-navy/20 p-4">
            <x-admin.table.pagination :records="$offers" />
        </div>
    </div>
</div>

@push('scripts')
<script>
    function offerManager() {
        return {
            loading: false,
            search: '',
            status: '',
            page: 1,

            init() {
                window.addEventListener('search-changed', (e) => {
                    this.search = e.detail.search;
                    this.updateTable(1);
                });
                window.addEventListener('page-changed', (e) => {
                    this.goToPage(e.detail.url);
                });
            },

            async updateTable(page = 1) {
                this.loading = true;
                this.page = page;
                try {
                    const params = new URLSearchParams({
                        search: this.search,
                        status: this.status,
                        page: this.page
                    });

                    const response = await fetch(`{{ route('admin.offers.index') }}?${params.toString()}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    });

                    const data = await response.json();
                    document.getElementById('offers-tbody').innerHTML = data.table;
                    document.getElementById('pagination-wrapper').innerHTML = data.pagination;
                } catch (error) {
                    console.error('Error fetching offers:', error);
                } finally {
                    this.loading = false;
                }
            },

            goToPage(url) {
                if (!url) return;
                const page = new URL(url).searchParams.get('page') || 1;
                this.updateTable(page);
            }
        }
    }
</script>
@endpush
@endsection
