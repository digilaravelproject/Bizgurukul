@php
    if (auth()->user()->hasRole('Admin')) {
        echo "<script>window.location.href='" . url('/admin/dashboard') . "';</script>";
        exit();
    }
@endphp
@extends('layouts.user.app')

@section('content')
    <div class="space-y-6">
        {{-- 1. Dashboard Title Section --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-2">
            <div>
                <h2 class="text-2xl font-black text-slate-800 uppercase italic tracking-tighter">
                    {{ __('Student Dashboard') }}
                </h2>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1 italic">
                    Manage your affiliate network & real-time earnings
                </p>
            </div>
            <div class="bg-white px-4 py-2 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-[10px] font-black text-slate-500 uppercase italic">Live Business Status</span>
            </div>
        </div>

        {{-- 2. Error/Success Messages --}}
        @if (session('error'))
            <div class="bg-red-50 border border-red-200 text-red-700 px-6 py-4 rounded-2xl relative shadow-sm animate-bounce"
                role="alert">
                <span class="text-xs font-black uppercase italic">{{ session('error') }}</span>
            </div>
        @endif

        {{-- 3. Affiliate Link Section (Full Logic Ke Saath) --}}
        <div
            class="bg-white overflow-hidden shadow-sm rounded-[2.5rem] border border-slate-100 p-8 transition-all hover:shadow-md">
            <h3 class="text-lg font-black text-slate-800 mb-2 uppercase italic tracking-tight">Your Affiliate Business Link
            </h3>
            <p class="text-slate-500 text-xs font-medium mb-6 italic leading-relaxed">
                Share this unique link to earn
                <span class="text-indigo-600 font-black italic underline decoration-indigo-200 underline-offset-4">
                    ₹{{ number_format($commissionAmount, 2) }}
                </span> per referral enrollment.
            </p>

            <div class="flex flex-col sm:flex-row gap-4" x-data="{
                copied: false,
                shareLink: '{{ $referralLink }}',
                copyToClipboard() {
                    if (!navigator.clipboard) {
                        let textArea = document.createElement('textarea');
                        textArea.value = this.shareLink;
                        document.body.appendChild(textArea);
                        textArea.select();
                        document.execCommand('copy');
                        document.body.removeChild(textArea);
                    } else {
                        navigator.clipboard.writeText(this.shareLink);
                    }
                    this.copied = true;
                    setTimeout(() => this.copied = false, 2000);
                }
            }">
                <input type="text" readonly :value="shareLink"
                    class="w-full bg-slate-50 border-2 border-slate-100 text-slate-600 text-sm rounded-2xl focus:ring-indigo-500 focus:border-indigo-500 block p-4 font-bold select-all tracking-tight">

                <button @click="copyToClipboard()"
                    class="inline-flex items-center justify-center px-10 py-4 border border-transparent text-xs font-black rounded-2xl shadow-lg shadow-indigo-100 text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none transition-all active:scale-95 uppercase tracking-widest min-w-[160px]">
                    <span x-show="!copied">Copy Link</span>
                    <span x-show="copied" class="flex items-center" style="display: none;">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Copied!
                    </span>
                </button>
            </div>

            <div class="mt-4 text-[10px] text-slate-400 flex items-center gap-2 font-black uppercase tracking-widest">
                Referral Code:
                <span
                    class="font-mono font-black text-indigo-700 bg-indigo-50 px-3 py-1 rounded-xl border border-indigo-100 italic tracking-tighter">
                    {{ $user->referral_code ?? 'N/A' }}
                </span>
            </div>
        </div>

        {{-- 4. Stats Grid (Earnings Data) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
            {{-- Total Earnings --}}
            <div
                class="bg-gradient-to-br from-indigo-600 to-indigo-800 rounded-[2rem] p-8 text-white shadow-xl shadow-indigo-100 relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="text-indigo-100 text-[10px] font-black uppercase tracking-[0.2em] mb-2 italic">Total
                        Earnings</div>
                    <div class="text-4xl font-black italic tracking-tighter">₹{{ number_format($totalEarnings, 2) }}</div>
                </div>
            </div>

            {{-- Pending --}}
            <div
                class="bg-white rounded-[2rem] p-8 border border-slate-100 shadow-sm hover:border-indigo-100 transition-all group">
                <div
                    class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-2 group-hover:text-indigo-600 transition-colors italic">
                    Pending Payout</div>
                <div class="text-4xl font-black text-slate-800 italic tracking-tighter">
                    ₹{{ number_format($pendingEarnings, 2) }}</div>
            </div>

            {{-- Total Referrals --}}
            <div
                class="bg-white rounded-[2rem] p-8 border border-slate-100 shadow-sm hover:border-indigo-100 transition-all group">
                <div
                    class="text-slate-400 text-[10px] font-black uppercase tracking-[0.2em] mb-2 group-hover:text-indigo-600 transition-colors italic">
                    Network Size</div>
                <div class="text-4xl font-black text-slate-800 italic tracking-tighter">{{ number_format($totalReferrals) }}
                </div>
            </div>
        </div>

        {{-- 5. Recent Referrals Table --}}
        <div class="bg-white overflow-hidden shadow-sm rounded-[2.5rem] border border-slate-100">
            <div class="p-8 border-b border-slate-50 flex justify-between items-center bg-slate-50/30">
                <h3 class="text-sm font-black text-slate-800 uppercase italic tracking-widest">Recent Network Growth</h3>
                <a href="#"
                    class="text-[10px] font-black text-indigo-600 hover:text-indigo-800 uppercase tracking-[0.2em] italic underline decoration-indigo-100 underline-offset-4 transition-all">
                    View Complete History
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50/50 text-[10px] uppercase font-black text-slate-400 tracking-widest">
                        <tr>
                            <th class="px-8 py-5">Student Identity</th>
                            <th class="px-8 py-5">Date Joined</th>
                            <th class="px-8 py-5 text-right">Verification Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($recentReferrals as $ref)
                            <tr class="hover:bg-slate-50/80 transition-all group">
                                <td class="px-8 py-5">
                                    <div class="flex items-center">
                                        <div
                                            class="h-9 w-9 rounded-2xl bg-indigo-50 text-indigo-700 flex items-center justify-center font-black mr-4 text-[11px] border border-indigo-100 uppercase group-hover:scale-110 transition-transform shadow-inner">
                                            {{ substr($ref->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div
                                                class="font-black text-slate-800 uppercase text-xs italic tracking-tighter">
                                                {{ $ref->name }}</div>
                                            <p class="text-[9px] text-slate-400 font-bold uppercase italic mt-0.5">Enrolled
                                                Partner</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-5 text-xs font-bold text-slate-500 italic lowercase tracking-tight">
                                    {{ $ref->created_at->format('d M, Y') }}
                                </td>
                                <td class="px-8 py-5 text-right">
                                    @if ($ref->is_active)
                                        <span
                                            class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black uppercase italic tracking-widest bg-emerald-50 text-emerald-700 border border-emerald-100 shadow-sm">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2"></span> Active Member
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-4 py-1.5 rounded-full text-[9px] font-black uppercase italic tracking-widest bg-slate-50 text-slate-400 border border-slate-100">
                                            Verification Pending
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-8 py-20 text-center">
                                    <div class="flex flex-col items-center max-w-xs mx-auto">
                                        <div
                                            class="w-16 h-16 bg-slate-50 rounded-full flex items-center justify-center mb-4 border border-slate-100">
                                            <svg class="w-8 h-8 text-slate-200" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                                </path>
                                            </svg>
                                        </div>
                                        <p
                                            class="text-slate-400 font-black uppercase text-[10px] tracking-widest italic opacity-60">
                                            No referrals found yet</p>
                                        <p class="text-slate-300 text-[9px] mt-1 font-bold italic tracking-tight">Share your
                                            business link to build your team!</p>
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
