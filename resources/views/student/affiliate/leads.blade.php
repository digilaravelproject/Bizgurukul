@extends('layouts.user.app')
@section('title', 'My Leads')

@section('content')
<div class="space-y-8 font-sans text-mainText pb-12" x-data="{ activeTab: 'referrals' }">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 animate-fade-in-down">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('student.affiliate.dashboard') }}" class="p-2 rounded-xl bg-surface border border-primary/10 hover:bg-primary/5 text-mutedText transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-3xl font-black tracking-tight text-mainText">My Network</h1>
            </div>
            <p class="text-mutedText text-base font-medium ml-12">Track your converted referrals and pending leads.</p>
        </div>

        {{-- Tabs --}}
        <div class="bg-surface p-1 rounded-2xl border border-primary/10 shadow-sm flex space-x-1">
            <button @click="activeTab = 'referrals'"
                    :class="activeTab === 'referrals' ? 'bg-primary/10 text-primary font-bold shadow-sm' : 'text-mutedText hover:text-mainText'"
                    class="px-6 py-2.5 rounded-xl text-sm transition-all uppercase tracking-widest outline-none">
                <i class="fas fa-check-circle mr-2"></i> Converted
            </button>
            <button @click="activeTab = 'leads'"
                    :class="activeTab === 'leads' ? 'bg-primary/10 text-primary font-bold shadow-sm' : 'text-mutedText hover:text-mainText'"
                    class="px-6 py-2.5 rounded-xl text-sm transition-all uppercase tracking-widest outline-none">
                <i class="fas fa-hourglass-start mr-2"></i> Pending
            </button>
        </div>
    </div>

    {{-- Converted Referrals Table (x-show) --}}
    <div x-show="activeTab === 'referrals'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-surface rounded-[2.5rem] border border-primary/10 shadow-xl shadow-primary/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-navy/50 text-xs uppercase text-mutedText font-bold tracking-widest">
                    <tr>
                        <th class="px-8 py-5">Date Joined</th>
                        <th class="px-8 py-5">Name</th>
                        <th class="px-8 py-5 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary/5">
                    @forelse($referrals as $referral)
                    <tr class="hover:bg-primary/5 transition-colors group">
                        <td class="px-8 py-5 text-sm font-semibold text-mutedText">
                            {{ $referral->created_at->format('d M Y') }}
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full brand-gradient flex items-center justify-center text-customWhite font-bold text-sm">
                                    {{ substr($referral->name, 0, 1) }}
                                </div>
                                <span class="text-sm font-bold text-mainText">{{ $referral->name }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right">
                             <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-green-500/10 text-green-600 border border-green-500/20">
                                <i class="fas fa-check text-[10px]"></i>
                                <span class="text-[10px] font-black uppercase tracking-wider">Converted</span>
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-8 py-24 text-center">
                             <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-primary/5 rounded-full flex items-center justify-center mb-6">
                                    <i class="fas fa-users text-3xl text-primary/40"></i>
                                </div>
                                <h4 class="text-xl font-bold text-mainText">No Referrals Yet</h4>
                                <p class="text-sm text-mutedText mt-2 max-w-sm">Share your affiliate links to start building your network!</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($referrals->hasPages())
        <div class="p-6 border-t border-primary/10">
            {{ $referrals->links() }}
        </div>
        @endif
    </div>

    {{-- Pending Leads Table (x-show) --}}
    <div x-cloak x-show="activeTab === 'leads'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4" x-transition:enter-end="opacity-100 translate-y-0" class="bg-surface rounded-[2.5rem] border border-primary/10 shadow-xl shadow-primary/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-navy/50 text-xs uppercase text-mutedText font-bold tracking-widest">
                    <tr>
                        <th class="px-8 py-5">Date Started</th>
                        <th class="px-8 py-5">Name</th>
                        <th class="px-8 py-5 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary/5">
                    @forelse($leads as $lead)
                    <tr class="hover:bg-primary/5 transition-colors group">
                        <td class="px-8 py-5 text-sm font-semibold text-mutedText">
                            {{ $lead->created_at->format('d M Y') }}
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-full bg-secondary/10 flex items-center justify-center text-secondary font-bold text-sm">
                                    {{ substr($lead->name, 0, 1) }}
                                </div>
                                <span class="text-sm font-bold text-mainText">{{ $lead->name }}</span>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-right">
                             <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-gray-500/10 text-gray-600 border border-gray-500/20">
                                <i class="fas fa-hourglass-start text-[10px]"></i>
                                <span class="text-[10px] font-black uppercase tracking-wider">Pending</span>
                            </span>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-8 py-24 text-center">
                             <div class="flex flex-col items-center justify-center">
                                <div class="w-20 h-20 bg-primary/5 rounded-full flex items-center justify-center mb-6 animate-pulse">
                                    <i class="fas fa-user-clock text-3xl text-primary/40"></i>
                                </div>
                                <h4 class="text-xl font-bold text-mainText">No Pending Leads</h4>
                                <p class="text-sm text-mutedText mt-2 max-w-sm">Great job! Most of your leads have converted, or you haven't shared your link recently.</p>
                                <a href="{{ route('student.affiliate.dashboard') }}" class="mt-6 px-8 py-3 rounded-xl brand-gradient text-customWhite font-bold shadow-lg hover:-translate-y-1 transition-all">
                                    Generate New Link
                                </a>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        {{-- Leads doesn't use pagination in controller anymore, it uses get(), but leaving empty just in case --}}
    </div>
</div>

@endsection
