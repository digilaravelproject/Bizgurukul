@extends('layouts.user.app')

@section('title', 'My Network | ' . config('app.name', 'Skills Pehle'))

@push('styles')
<style>
    /* Clean & Modern Mesh Background */
    .dashboard-bg {
        background-color: rgb(var(--color-bg-body));
        background-image:
            radial-gradient(at 0% 0%, rgba(var(--color-primary) / 0.1) 0px, transparent 50%),
            radial-gradient(at 100% 100%, rgba(var(--color-secondary) / 0.1) 0px, transparent 50%);
        background-attachment: fixed;
    }

    /* Premium Glass Cards */
    .glass-panel {
        background: rgba(var(--color-bg-card) / 0.7);
        backdrop-filter: blur(20px);
        -webkit-backdrop-filter: blur(20px);
        border: 1px solid rgba(var(--color-primary) / 0.08);
        border-radius: 1.5rem;
        box-shadow: 0 10px 30px -10px rgba(0, 0, 0, 0.05);
    }

    /* Segmented Filters (Pill style) */
    .filter-pill {
        padding: 0.6rem 1.5rem;
        border-radius: 9999px;
        font-size: 0.875rem;
        font-weight: 700;
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
        color: var(--color-mutedText);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .filter-pill:hover:not(.filter-active) {
        color: var(--color-mainText);
        background: rgba(var(--color-primary) / 0.05);
    }

    .filter-active {
        background: rgb(var(--color-primary));
        color: white !important;
        box-shadow: 0 4px 14px 0 rgba(var(--color-primary) / 0.3);
    }

    /* Table Row Hover Effects */
    .table-row-hover {
        transition: all 0.2s ease;
    }
    @media (min-width: 768px) {
        .table-row-hover:hover {
            background: rgba(var(--color-primary) / 0.03);
            transform: scale(1.002);
        }
    }

    .brand-gradient-text {
        background: linear-gradient(135deg, rgb(var(--color-primary)) 0%, rgb(var(--color-secondary)) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>
@endpush

@section('content')
<div class="dashboard-bg min-h-screen pb-20 pt-6 md:pt-8" x-data="{ activeTab: 'referrals' }">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Section --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-8 animate-fade-in-down" 
             x-data="{ 
                search: '', 
                isLoading: false,
                doSearch() {
                    this.isLoading = true;
                    fetch(`{{ route('student.affiliate.leads') }}?search=${this.search}`, {
                        headers: { 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => res.json())
                    .then(data => {
                        document.getElementById('referrals-tbody').innerHTML = data.referrals_html;
                        document.getElementById('leads-tbody').innerHTML = data.leads_html;
                        this.isLoading = false;
                    });
                }
             }">
            <div class="w-full lg:w-auto">
                <div class="flex items-center gap-3 md:gap-4 mb-2">
                    <a href="{{ route('student.affiliate.dashboard') }}" class="w-9 h-9 md:w-10 md:h-10 shrink-0 rounded-full bg-surface border border-primary/10 hover:bg-primary/5 text-mutedText flex items-center justify-center transition-colors shadow-sm">
                        <i class="fas fa-arrow-left text-sm md:text-base"></i>
                    </a>
                    <h1 class="text-2xl md:text-3xl font-black tracking-tight text-mainText truncate">
                        My <span class="brand-gradient-text">Network</span>
                    </h1>
                </div>
                <p class="text-mutedText text-xs md:text-sm font-medium ml-0 md:ml-14 mt-2 md:mt-0">Track your converted referrals and pending leads in one place.</p>
            </div>

            <div class="flex flex-col md:flex-row gap-4 w-full lg:w-auto items-center">
                {{-- Search Bar with Debounce --}}
                <div class="relative w-full md:w-64 lg:w-80 group">
                    <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                        <i class="fas fa-search text-xs text-mutedText group-focus-within:text-primary transition-colors"></i>
                    </div>
                    <input type="text" 
                           x-model="search" 
                           @input.debounce.500ms="doSearch()"
                           placeholder="Search by name, email or phone..." 
                           class="w-full bg-surface border border-primary/10 rounded-full pl-11 pr-4 py-2.5 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-4 focus:ring-primary/5 transition-all shadow-sm">
                    <div x-show="isLoading" class="absolute inset-y-0 right-0 pr-4 flex items-center">
                        <i class="fas fa-circle-notch fa-spin text-xs text-primary"></i>
                    </div>
                </div>

                {{-- Modern Tabs (Responsive width) --}}
                <div class="bg-surface p-1.5 rounded-full shadow-sm border border-primary/10 flex w-full lg:w-auto">
                    <button @click="activeTab = 'referrals'"
                            :class="activeTab === 'referrals' ? 'filter-active' : ''"
                            class="filter-pill tracking-wide outline-none flex-1 lg:flex-none justify-center">
                        <i class="fas fa-check-circle"></i> Converted
                    </button>
                    <button @click="activeTab = 'leads'"
                            :class="activeTab === 'leads' ? 'filter-active' : ''"
                            class="filter-pill tracking-wide outline-none flex-1 lg:flex-none justify-center">
                        <i class="fas fa-hourglass-half"></i> Pending
                    </button>
                </div>
            </div>
        </div>

        {{-- ========================================== --}}
        {{-- CONVERTED REFERRALS TABLE (x-show)         --}}
        {{-- ========================================== --}}
        <div x-show="activeTab === 'referrals'"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="glass-panel overflow-hidden">

            <div class="w-full">
                <table class="w-full text-left border-collapse">
                    <thead class="hidden md:table-header-group bg-primary/5 text-xs uppercase text-mutedText font-black tracking-widest border-b border-primary/10">
                        <tr>
                            <th class="px-6 py-5 whitespace-nowrap">User Profile</th>
                            <th class="px-6 py-5 whitespace-nowrap">Product</th>
                            <th class="px-6 py-5 whitespace-nowrap">Contact Details</th>
                            <th class="px-6 py-5 text-right whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody id="referrals-tbody" class="divide-y divide-primary/10">
                        @include('student.affiliate.partials.referrals_table', ['referrals' => $referrals])
                    </tbody>
                </table>
            </div>
            @if($referrals->hasPages())
            <div class="p-4 bg-surface/50 border-t border-primary/10 overflow-x-auto hide-scrollbar">
                {{ $referrals->links() }}
            </div>
            @endif
        </div>

        {{-- ========================================== --}}
        {{-- PENDING LEADS TABLE (x-show)               --}}
        {{-- ========================================== --}}
        <div x-cloak x-show="activeTab === 'leads'"
             x-transition:enter="transition ease-out duration-300 transform"
             x-transition:enter-start="opacity-0 translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             class="glass-panel overflow-hidden">

            <div class="w-full">
                <table class="w-full text-left border-collapse">
                    <thead class="hidden md:table-header-group bg-primary/5 text-xs uppercase text-mutedText font-black tracking-widest border-b border-primary/10">
                        <tr>
                            <th class="px-6 py-5 whitespace-nowrap">Lead Profile</th>
                            <th class="px-6 py-5 whitespace-nowrap">Product</th>
                            <th class="px-6 py-5 whitespace-nowrap">Contact Details</th>
                            <th class="px-6 py-5 text-right whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody id="leads-tbody" class="divide-y divide-primary/10">
                         @include('student.affiliate.partials.leads_table', ['leads' => $leads])
                    </tbody>
                </table>
            </div>
            @if($leads->hasPages())
            <div class="p-4 bg-surface/50 border-t border-primary/10 overflow-x-auto hide-scrollbar">
                {{ $leads->links() }}
            </div>
            @endif
        </div>

    </div>
</div>

@push('scripts')
{{-- Include FontAwesome if not already in your layout --}}
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@endsection
