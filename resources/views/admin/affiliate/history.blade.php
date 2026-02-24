@extends('layouts.admin')

@section('title', 'Referral History')

@section('content')
<div class="space-y-8 font-sans text-mainText">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-mainText">Referral & Commission History</h1>
            <p class="text-mutedText mt-1 text-sm">Track affiliate performance and manage commission payouts.</p>
        </div>
        <form action="{{ route('admin.affiliate.history') }}" method="GET" class="flex gap-3">
             <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search affiliates..." class="pl-10 pr-4 py-2 bg-surface border border-primary/10 rounded-xl text-sm focus:ring-primary focus:border-primary w-64 shadow-sm text-mainText">
                <svg class="w-4 h-4 text-mutedText absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <button type="submit" class="bg-primary hover:bg-secondary text-white px-4 py-2 rounded-xl text-sm font-bold shadow-md transition-colors">Search</button>
        </form>
    </div>

    {{-- Main Content Card --}}
    <div class="bg-surface rounded-2xl shadow-sm border border-primary/10 overflow-hidden">

        <div class="p-6 border-b border-primary/5 flex justify-between items-center bg-navy/30">
            <h3 class="text-lg font-bold text-mainText flex items-center gap-2">
                <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                Recent Conversions
            </h3>
            <span class="text-xs font-medium text-mutedText bg-white px-3 py-1 rounded-full border border-primary/5 shadow-sm">
                Total Records: {{ $commissions->total() }}
            </span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-primary/5 text-xs uppercase text-primary font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">Date & Time</th>
                        <th class="px-6 py-4">Affiliate</th>
                        <th class="px-6 py-4">Referred User</th>
                        <th class="px-6 py-4">Product / Course</th>
                        <th class="px-6 py-4 text-right">Commission</th>
                        <th class="px-6 py-4 text-center">Status & Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary/5">
                    @forelse ($commissions as $commission)
                        <tr class="hover:bg-navy transition-colors group">

                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-mainText">{{ $commission->created_at->format('d M Y') }}</span>
                                    <span class="text-xs text-mutedText">{{ $commission->created_at->format('h:i A') }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-full bg-orange-100 flex items-center justify-center text-primary font-bold text-sm border border-primary/10 overflow-hidden shrink-0">
                                        @if($commission->affiliate && $commission->affiliate->profile_picture)
                                            <img src="{{ asset('storage/'.$commission->affiliate->profile_picture) }}" class="w-full h-full object-cover">
                                        @else
                                            {{ substr($commission->affiliate->name ?? 'U', 0, 1) }}
                                        @endif
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-mainText">{{ $commission->affiliate->name ?? 'Unknown' }}</p>
                                        <span class="inline-flex items-center px-1.5 py-0.5 rounded text-[10px] font-medium bg-surface text-mutedText border border-primary/10">
                                            {{ $commission->affiliate->referral_code ?? 'N/A' }}
                                        </span>
                                    </div>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                <div class="flex items-center gap-2">
                                    <div class="w-8 h-8 rounded-full bg-navy flex items-center justify-center text-mutedText text-xs font-bold border border-white shrink-0">
                                        {{ substr($commission->referredUser->name ?? '?', 0, 1) }}
                                    </div>
                                    <span class="text-sm text-mainText">{{ $commission->referredUser->name ?? 'Unknown' }}</span>
                                </div>
                            </td>

                            <td class="px-6 py-4">
                                @if ($commission->reference)
                                    <div class="flex items-center gap-2">
                                        <div class="p-1.5 rounded bg-blue-50 text-blue-500">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                        </div>
                                        <span class="text-sm font-medium text-mutedText line-clamp-1 max-w-[150px]" title="{{ $commission->reference->title }}">
                                            {{ $commission->reference->title }}
                                        </span>
                                    </div>
                                @else
                                    <span class="text-xs text-mutedText italic">Product ID: {{ $commission->reference_id }}</span>
                                @endif
                            </td>

                            <td class="px-6 py-4 text-right">
                                <span class="text-base font-black text-primary">
                                    ₹{{ number_format($commission->amount, 2) }}
                                </span>
                            </td>

                            <td class="px-6 py-4 text-center">
                                @if($commission->status == 'on_hold')
                                    <div class="flex flex-col items-center gap-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-amber-500/10 text-amber-500">
                                            <i class="fas fa-hourglass-half mr-1"></i> On Hold
                                        </span>
                                        <form action="{{ route('admin.payouts.commission.early_approve', $commission->id) }}" method="POST" onsubmit="return confirm('Manually approve this commission early?');">
                                            @csrf
                                            <button type="submit" class="text-[10px] uppercase tracking-widest bg-emerald-500 hover:bg-emerald-400 text-white px-3 py-1.5 rounded-lg font-black transition-all shadow-md flex items-center gap-1">
                                                <i class="fas fa-check"></i> Early Approve
                                            </button>
                                        </form>
                                    </div>
                                @elseif($commission->status == 'available')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-emerald-500/10 text-emerald-500">
                                        <i class="fas fa-check-circle mr-1"></i> Available
                                    </span>
                                @elseif($commission->status == 'requested')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-blue-500/10 text-blue-500">
                                        <i class="fas fa-spinner fa-spin mr-1"></i> Processing Withdrawal
                                    </span>
                                @elseif($commission->status == 'pending')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-orange-100 text-orange-700">
                                        Pending
                                    </span>
                                @elseif($commission->status == 'paid')
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-green-100 text-green-700 border border-green-200">
                                        <i class="fas fa-wallet mr-1"></i> Paid
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-widest bg-gray-100 text-gray-600">
                                        {{ ucfirst($commission->status) }}
                                    </span>
                                @endif
                            </td>

                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center">
                                <div class="flex flex-col items-center justify-center">
                                    <div class="w-16 h-16 bg-navy rounded-full flex items-center justify-center mb-4">
                                        <svg class="w-8 h-8 text-primary/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    </div>
                                    <h3 class="text-lg font-bold text-mainText">No History Found</h3>
                                    <p class="text-sm text-mutedText">There are no referral commissions recorded yet.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        @if($commissions->hasPages())
            <div class="p-4 border-t border-primary/5 bg-navy/30">
                {{ $commissions->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
