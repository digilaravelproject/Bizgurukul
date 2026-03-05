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
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 mb-8 animate-fade-in-down">
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
                            <th class="px-6 py-5 whitespace-nowrap">Date Joined</th>
                            <th class="px-6 py-5 whitespace-nowrap">Contact Details</th>
                            <th class="px-6 py-5 text-right whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary/10">
                        @forelse($referrals as $referral)
                        <tr class="table-row-hover group flex flex-col md:table-row p-4 md:p-0">

                            {{-- Name & Avatar (Top on mobile) --}}
                            <td class="block md:table-cell px-2 md:px-6 py-2 md:py-5 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 shrink-0 rounded-full bg-gradient-to-br from-green-400 to-green-600 flex items-center justify-center text-white font-bold text-sm shadow-sm ring-2 ring-white">
                                        {{ strtoupper(substr($referral->name, 0, 1)) }}
                                    </div>
                                    <span class="text-base md:text-sm font-black text-mainText truncate">{{ $referral->name }}</span>
                                </div>
                            </td>

                            {{-- Date --}}
                            <td class="block md:table-cell px-2 md:px-6 py-3 md:py-5 border-t border-primary/5 md:border-none mt-3 md:mt-0 whitespace-nowrap">
                                <div class="flex justify-between items-center md:block">
                                    <span class="md:hidden text-[10px] font-bold text-mutedText uppercase tracking-widest">Date Joined</span>
                                    <span class="text-sm font-bold text-mutedText bg-surface px-3 py-1.5 rounded-lg border border-primary/5">
                                        {{ $referral->created_at->format('d M, Y') }}
                                    </span>
                                </div>
                            </td>

                            {{-- Contact Details (Email & Phone) --}}
                            <td class="block md:table-cell px-2 md:px-6 py-3 md:py-5 border-t border-primary/5 md:border-none whitespace-normal md:whitespace-nowrap">
                                <div class="flex justify-between items-start md:block">
                                    <span class="md:hidden text-[10px] font-bold text-mutedText uppercase tracking-widest mt-1">Contact</span>
                                    <div class="flex flex-col space-y-2 items-end md:items-start">
                                        <div class="flex items-center gap-2 text-sm font-medium text-mainText flex-row-reverse md:flex-row">
                                            <div class="w-6 h-6 rounded-md bg-primary/10 flex items-center justify-center text-primary shrink-0">
                                                <i class="fas fa-envelope text-[10px]"></i>
                                            </div>
                                            <span class="truncate max-w-[150px] sm:max-w-xs">{{ $referral->email }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs font-bold text-mutedText flex-row-reverse md:flex-row">
                                            <div class="w-6 h-6 rounded-md bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                                                <i class="fas fa-phone-alt text-[10px]"></i>
                                            </div>
                                            <span>{{ $referral->phone ?? $referral->mobile ?? 'Not Provided' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="block md:table-cell px-2 md:px-6 py-3 md:py-5 text-right border-t border-primary/5 md:border-none whitespace-nowrap">
                                <div class="flex justify-between items-center md:justify-end">
                                    <span class="md:hidden text-[10px] font-bold text-mutedText uppercase tracking-widest">Status</span>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-green-50 text-green-600 border border-green-200 shadow-sm">
                                        <div class="w-1.5 h-1.5 rounded-full bg-green-500 animate-pulse"></div>
                                        <span class="text-xs font-black uppercase tracking-wider">Converted</span>
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 md:py-20 text-center">
                                 <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 md:w-20 md:h-20 bg-primary/5 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-users text-2xl md:text-3xl text-primary/40"></i>
                                    </div>
                                    <h4 class="text-base md:text-lg font-black text-mainText">No Referrals Yet</h4>
                                    <p class="text-xs md:text-sm text-mutedText mt-1 max-w-xs md:max-w-sm font-medium">Share your affiliate links to start building your network!</p>
                                </div>
                            </td>
                        </tr>
                        @endforelse
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
                            <th class="px-6 py-5 whitespace-nowrap">Date Started</th>
                            <th class="px-6 py-5 whitespace-nowrap">Contact Details</th>
                            <th class="px-6 py-5 text-right whitespace-nowrap">Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary/10">
                        @forelse($leads as $lead)
                        <tr class="table-row-hover group flex flex-col md:table-row p-4 md:p-0">

                            {{-- Name & Avatar (Top on mobile) --}}
                            <td class="block md:table-cell px-2 md:px-6 py-2 md:py-5 whitespace-nowrap">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 shrink-0 rounded-full bg-slate-200 flex items-center justify-center text-slate-600 font-bold text-sm shadow-sm ring-2 ring-white">
                                        {{ strtoupper(substr($lead->name, 0, 1)) }}
                                    </div>
                                    <span class="text-base md:text-sm font-black text-mainText truncate">{{ $lead->name }}</span>
                                </div>
                            </td>

                            {{-- Date --}}
                            <td class="block md:table-cell px-2 md:px-6 py-3 md:py-5 border-t border-primary/5 md:border-none mt-3 md:mt-0 whitespace-nowrap">
                                <div class="flex justify-between items-center md:block">
                                    <span class="md:hidden text-[10px] font-bold text-mutedText uppercase tracking-widest">Date Started</span>
                                    <span class="text-sm font-bold text-mutedText bg-surface px-3 py-1.5 rounded-lg border border-primary/5">
                                        {{ $lead->created_at->format('d M, Y') }}
                                    </span>
                                </div>
                            </td>

                            {{-- Contact Details (Email & Phone) --}}
                            <td class="block md:table-cell px-2 md:px-6 py-3 md:py-5 border-t border-primary/5 md:border-none whitespace-normal md:whitespace-nowrap">
                                <div class="flex justify-between items-start md:block">
                                    <span class="md:hidden text-[10px] font-bold text-mutedText uppercase tracking-widest mt-1">Contact</span>
                                    <div class="flex flex-col space-y-2 items-end md:items-start">
                                        <div class="flex items-center gap-2 text-sm font-medium text-mainText flex-row-reverse md:flex-row">
                                            <div class="w-6 h-6 rounded-md bg-amber-50 flex items-center justify-center text-amber-600 shrink-0">
                                                <i class="fas fa-envelope text-[10px]"></i>
                                            </div>
                                            <span class="truncate max-w-[150px] sm:max-w-xs">{{ $lead->email }}</span>
                                        </div>
                                        <div class="flex items-center gap-2 text-xs font-bold text-mutedText flex-row-reverse md:flex-row">
                                            <div class="w-6 h-6 rounded-md bg-slate-100 flex items-center justify-center text-slate-500 shrink-0">
                                                <i class="fas fa-phone-alt text-[10px]"></i>
                                            </div>
                                            <span>{{ $lead->phone ?? $lead->mobile ?? 'Not Provided' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Status --}}
                            <td class="block md:table-cell px-2 md:px-6 py-3 md:py-5 text-right border-t border-primary/5 md:border-none whitespace-nowrap">
                                <div class="flex justify-between items-center md:justify-end">
                                    <span class="md:hidden text-[10px] font-bold text-mutedText uppercase tracking-widest">Status</span>
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg bg-amber-50 text-amber-600 border border-amber-200 shadow-sm">
                                        <i class="fas fa-hourglass-half text-[10px] animate-pulse"></i>
                                        <span class="text-xs font-black uppercase tracking-wider">Pending</span>
                                    </span>
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-16 md:py-20 text-center">
                                 <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 md:w-20 md:h-20 bg-amber-50 rounded-full flex items-center justify-center mb-4">
                                        <i class="fas fa-user-clock text-2xl md:text-3xl text-amber-400"></i>
                                    </div>
                                    <h4 class="text-base md:text-lg font-black text-mainText">No Pending Leads</h4>
                                    <p class="text-xs md:text-sm text-mutedText mt-1 max-w-xs md:max-w-sm font-medium">Great job! Most of your leads have converted, or you haven't shared your link recently.</p>
                                    <a href="{{ route('student.affiliate.dashboard') }}" class="mt-5 md:mt-6 px-5 md:px-6 py-2.5 rounded-full bg-primary text-white font-bold shadow-lg hover:-translate-y-1 transition-transform text-xs md:text-sm">
                                        <i class="fas fa-link mr-2"></i> Get Referral Link
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
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
