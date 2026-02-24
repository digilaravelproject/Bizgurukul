@extends('layouts.user.app')
@section('title', 'Partner Dashboard')

@section('content')
<div class="space-y-8 font-sans text-mainText pb-12">

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 animate-fade-in-down">
        <div>
            <h1 class="text-3xl md:text-4xl font-black tracking-tight text-mainText">Partner Dashboard</h1>
            <p class="text-mutedText text-base font-medium mt-2">Track your referrals and earnings in real-time.</p>
        </div>
        <div class="flex gap-3">
             <a href="{{ route('student.affiliate.leads') }}" class="px-6 py-3 rounded-xl border border-primary/20 text-primary font-bold hover:bg-primary/5 transition-all">
                My Leads
            </a>
            <a href="{{ route('student.affiliate.commission_structure') }}" class="px-6 py-3 rounded-xl brand-gradient text-customWhite font-bold shadow-lg shadow-primary/30 hover:shadow-primary/50 hover:-translate-y-1 transition-all">
                Commission Structure
            </a>
        </div>
    </div>

    {{-- Stats Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6 animate-fade-in-up">
        {{-- Card 1: Total Earnings --}}
        <div class="bg-surface rounded-[2rem] p-6 border border-primary/10 shadow-lg shadow-primary/5 hover:-translate-y-1 transition-transform duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-2xl bg-indigo-500/10 flex items-center justify-center text-indigo-500 text-xl">
                    <i class="fas fa-wallet"></i>
                </div>
                <span class="bg-indigo-500/10 text-indigo-500 text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded-lg">All Time</span>
            </div>
            <h3 class="text-3xl font-black text-mainText tracking-tight">₹{{ number_format($totalEarnings) }}</h3>
            <p class="text-xs font-bold text-mutedText uppercase tracking-widest mt-1">Total Earnings</p>
        </div>

        {{-- Card 2: This Month --}}
        <div class="bg-surface rounded-[2rem] p-6 border border-primary/10 shadow-lg shadow-primary/5 hover:-translate-y-1 transition-transform duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 text-xl">
                    <i class="fas fa-calendar-check"></i>
                </div>
                <span class="bg-emerald-500/10 text-emerald-500 text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded-lg">This Month</span>
            </div>
            <h3 class="text-3xl font-black text-mainText tracking-tight">₹{{ number_format($thisMonthEarnings) }}</h3>
            <p class="text-xs font-bold text-mutedText uppercase tracking-widest mt-1">Monthly Income</p>
        </div>

        {{-- Card 3: Today's Earnings --}}
        <div class="bg-surface rounded-[2rem] p-6 border border-primary/10 shadow-lg shadow-primary/5 hover:-translate-y-1 transition-transform duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-2xl bg-amber-500/10 flex items-center justify-center text-amber-500 text-xl">
                    <i class="fas fa-sun"></i>
                </div>
                <span class="bg-amber-500/10 text-amber-500 text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded-lg">Today</span>
            </div>
            <h3 class="text-3xl font-black text-mainText tracking-tight">₹{{ number_format($todaysEarnings) }}</h3>
            <p class="text-xs font-bold text-mutedText uppercase tracking-widest mt-1">Today's Income</p>
        </div>

        {{-- Card 4: Total Referrals --}}
        <div class="bg-surface rounded-[2rem] p-6 border border-primary/10 shadow-lg shadow-primary/5 hover:-translate-y-1 transition-transform duration-300">
            <div class="flex justify-between items-start mb-4">
                <div class="w-12 h-12 rounded-2xl bg-rose-500/10 flex items-center justify-center text-rose-500 text-xl">
                    <i class="fas fa-users"></i>
                </div>
                <span class="bg-rose-500/10 text-rose-500 text-[10px] font-black uppercase tracking-widest px-2 py-1 rounded-lg">Network</span>
            </div>
            <h3 class="text-3xl font-black text-mainText tracking-tight">{{ number_format($totalReferrals) }}</h3>
            <p class="text-xs font-bold text-mutedText uppercase tracking-widest mt-1">Active Referrals</p>
        </div>
    </div>

    {{-- Recent Activity Table --}}
    <div class="bg-surface rounded-[2.5rem] border border-primary/10 shadow-xl shadow-primary/5 overflow-hidden animate-fade-in-up" style="animation-delay: 0.1s;">
        <div class="p-8 border-b border-primary/10 flex justify-between items-center">
            <h3 class="text-xl font-black text-mainText uppercase tracking-widest flex items-center gap-3">
                <i class="fas fa-history text-primary"></i> Recent Commissions
            </h3>
            <span class="text-xs font-bold text-mutedText uppercase tracking-widest">Last 5 Transactions</span>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-navy/50 text-xs uppercase text-mutedText font-bold tracking-widest">
                    <tr>
                        <th class="px-8 py-5">Date</th>
                        <th class="px-8 py-5">Referred User</th>
                        <th class="px-8 py-5">Product</th>
                        <th class="px-8 py-5">Amount</th>
                        <th class="px-8 py-5 text-right">Status</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary/5">
                    @forelse($recentCommissions as $commission)
                    <tr class="hover:bg-primary/5 transition-colors group">
                        <td class="px-8 py-5 text-sm font-semibold text-mutedText">
                            {{ $commission->created_at->format('d M Y') }}
                            <span class="block text-[10px] opacity-60">{{ $commission->created_at->format('h:i A') }}</span>
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                                    {{ substr($commission->referredUser->name ?? 'U', 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-mainText">{{ $commission->referredUser->name ?? 'Unknown' }}</p>
                                    <p class="text-[10px] text-mutedText">User ID: #{{ $commission->referred_user_id }}</p>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5">
                             @if($commission->reference_type == 'App\Models\Bundle')
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black bg-purple-500/10 text-purple-600 uppercase tracking-widest border border-purple-500/20">
                                    Bundle
                                </span>
                            @else
                                 <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black bg-blue-500/10 text-blue-600 uppercase tracking-widest border border-blue-500/20">
                                    Course
                                </span>
                            @endif
                            <span class="ml-2 text-xs font-bold text-mainText">{{ $commission->reference->title ?? 'N/A' }}</span>
                        </td>
                        <td class="px-8 py-5 text-sm font-black text-green-600 font-mono">
                            +₹{{ number_format($commission->amount) }}
                        </td>
                        <td class="px-8 py-5 text-right">
                            @if($commission->status == 'paid')
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-green-500/10 text-green-600 border border-green-500/20">
                                    <i class="fas fa-check-circle text-[10px]"></i>
                                    <span class="text-[10px] font-black uppercase tracking-wider">Paid</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-amber-500/10 text-amber-600 border border-amber-500/20">
                                    <i class="fas fa-clock text-[10px]"></i>
                                    <span class="text-[10px] font-black uppercase tracking-wider">Pending</span>
                                </span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-8 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-primary/5 rounded-full flex items-center justify-center mb-4">
                                    <i class="fas fa-search-dollar text-2xl text-primary/40"></i>
                                </div>
                                <h4 class="text-lg font-bold text-mainText">No Commissions Yet</h4>
                                <p class="text-sm text-mutedText mt-1 max-w-xs">Start sharing your affiliate links to earn commissions!</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
