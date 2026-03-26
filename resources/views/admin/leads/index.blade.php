@extends('layouts.admin')

@section('content')
<div class="mb-8 flex flex-col md:flex-row md:items-center justify-between gap-4" 
     x-data="{ 
        search: '', 
        isLoading: false,
        doSearch() {
            this.isLoading = true;
            fetch(`{{ route('admin.leads.index') }}?search=${this.search}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            })
            .then(res => res.text())
            .then(html => {
                document.getElementById('leads-tbody').innerHTML = html;
                this.isLoading = false;
            });
        }
     }">
    <div>
        <h2 class="text-3xl font-black text-mainText tracking-tight">Lead <span class="text-primary">Management</span></h2>
        <p class="text-sm text-mutedText font-medium mt-1">Monitor and track potential customers who haven't completed their registration.</p>
    </div>

    <div class="relative w-full md:w-80 group">
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <i class="fas fa-search text-xs text-mutedText group-focus-within:text-primary transition-colors"></i>
        </div>
        <input type="text" 
               x-model="search" 
               @input.debounce.500ms="doSearch()"
               placeholder="Search leads by name, email..." 
               class="w-full bg-surface border border-primary/10 rounded-2xl pl-11 pr-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-4 focus:ring-primary/5 transition-all shadow-sm">
        <div x-show="isLoading" class="absolute inset-y-0 right-0 pr-4 flex items-center">
            <i class="fas fa-circle-notch fa-spin text-xs text-primary"></i>
        </div>
    </div>
</div>

<div class="bg-surface rounded-3xl border border-primary/10 shadow-sm overflow-hidden">
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

    @if($leads->hasPages())
    <div class="px-6 py-5 bg-primary/5 border-t border-primary/10">
        {{ $leads->links() }}
    </div>
    @endif
</div>
@endsection
