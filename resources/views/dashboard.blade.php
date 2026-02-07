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
        <div class="flex flex-col md:flex-row md::items-center justify-between gap-4 mb-2">
            <div>
                <h2 class="text-2xl font-bold text-mainText">
                    {{ __('Student Dashboard') }}
                </h2>
                <p class="text-xs text-mutedText font-medium mt-1">
                    Manage your affiliate network & real-time earnings
                </p>
            </div>
            <div class="bg-customWhite px-4 py-2 rounded-2xl border border-primary/10 shadow-sm flex items-center gap-2">
                <span class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></span>
                <span class="text-xs font-bold text-mutedText">Live Business Status</span>
            </div>
        </div>

        {{-- 2. Error/Success Messages --}}
        @if (session('error'))
            <div class="bg-red-500/10 border border-red-500/20 text-red-500 px-6 py-4 rounded-2xl relative shadow-sm"
                role="alert">
                <span class="text-sm font-medium">{{ session('error') }}</span>
            </div>
        @endif

        {{-- 3. Affiliate Link Section (Full Logic Ke Saath) --}}
        <div
            class="bg-customWhite overflow-hidden shadow-sm rounded-2xl border border-primary/5 p-8 transition-all hover:shadow-md">
            <h3 class="text-lg font-bold text-mainText mb-2">Your Affiliate Business Link
            </h3>
            <p class="text-mutedText text-sm font-medium mb-6 leading-relaxed">
                Share this unique link to earn
                <span class="text-primary font-bold">
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
                    class="w-full bg-navy border border-primary/10 text-mainText text-sm rounded-xl focus:ring-primary focus:border-primary block p-3 font-medium select-all placeholder-mutedText">

                <button @click="copyToClipboard()"
                    class="inline-flex items-center justify-center px-8 py-3 border border-transparent text-sm font-bold rounded-xl shadow-lg shadow-primary/20 text-white brand-gradient hover:opacity-90 focus:outline-none transition-all active:scale-95 min-w-[140px]">
                    <span x-show="!copied">Copy Link</span>
                    <span x-show="copied" class="flex items-center" style="display: none;">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        Copied!
                    </span>
                </button>
            </div>

            <div class="mt-4 text-xs text-mutedText flex items-center gap-2 font-bold uppercase tracking-wider">
                Referral Code:
                <span
                    class="font-mono font-bold text-primary bg-primary/5 px-2 py-1 rounded-lg border border-primary/10">
                    {{ $user->referral_code ?? 'N/A' }}
                </span>
            </div>
        </div>

        {{-- 3.5 Specific Link Generator --}}
        <div class="bg-customWhite overflow-hidden shadow-sm rounded-2xl border border-primary/5 p-8 transition-all hover:shadow-md"
             x-data="{
                type: 'course',
                selectedId: '',
                generatedLink: '',
                baseUrl: '{{ url('/') }}',
                refCode: '{{ $user->referral_code }}',
                generate() {
                    if(!this.selectedId) return;
                    let path = this.type === 'course' ? '/course/' : '/coursesp/';
                    this.generatedLink = this.baseUrl + path + this.selectedId + '?ref=' + this.refCode;
                },
                copySpecific() {
                    navigator.clipboard.writeText(this.generatedLink);
                    alert('Link Copied!');
                }
             }">
            <h3 class="text-lg font-bold text-mainText mb-2">Generate Product Link</h3>
            <p class="text-mutedText text-sm font-medium mb-6 leading-relaxed">
                Promote a specific Course or Bundle directly.
            </p>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-bold uppercase tracking-wider text-mutedText mb-1">Type</label>
                    <select x-model="type" @change="selectedId = ''; generatedLink = ''"
                        class="w-full bg-navy border border-primary/10 text-mainText text-sm rounded-xl focus:ring-primary focus:border-primary block p-2.5 font-medium">
                        <option value="course">Course</option>
                        <option value="bundle">Bundle</option>
                    </select>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold uppercase tracking-wider text-mutedText mb-1">Select Product</label>
                    <select x-model="selectedId" @change="generate()"
                        class="w-full bg-navy border border-primary/10 text-mainText text-sm rounded-xl focus:ring-primary focus:border-primary block p-2.5 font-medium">
                        <option value="">-- Select --</option>
                        <template x-if="type === 'course'">
                            <optgroup label="Courses">
                                @foreach($courses as $course)
                                    <option value="{{ $course->id }}">{{ $course->title }}</option>
                                @endforeach
                            </optgroup>
                        </template>
                        <template x-if="type === 'bundle'">
                            <optgroup label="Bundles">
                                @foreach($bundles as $bundle)
                                    <option value="{{ $bundle->id }}">{{ $bundle->title }}</option>
                                @endforeach
                            </optgroup>
                        </template>
                    </select>
                </div>
            </div>

            <div x-show="generatedLink" class="relative" style="display: none;">
                <label class="block text-xs font-bold uppercase tracking-wider text-mutedText mb-1">Your Unique Link</label>
                <div class="flex gap-2">
                    <input type="text" readonly :value="generatedLink"
                        class="w-full bg-primary/5 border border-primary/20 text-primary text-sm rounded-xl focus:ring-primary focus:border-primary block p-3 font-medium select-all">
                    <button @click="copySpecific()"
                        class="px-6 py-3 bg-primary text-white rounded-xl font-bold text-xs uppercase tracking-wider hover:bg-primary/90 transition-colors shadow-lg shadow-primary/20">
                        Copy
                    </button>
                </div>
            </div>
        </div>

        {{-- 4. Stats Grid (Earnings Data) --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Total Earnings --}}
            <div
                class="brand-gradient rounded-2xl p-6 text-white shadow-xl shadow-primary/20 relative overflow-hidden group">
                <div class="absolute -right-4 -bottom-4 opacity-10 group-hover:scale-110 transition-transform">
                    <svg class="w-24 h-24" fill="currentColor" viewBox="0 0 24 24">
                        <path
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                </div>
                <div class="relative z-10">
                    <div class="text-white/80 text-xs font-bold uppercase tracking-wider mb-2">Total Earnings</div>
                    <div class="text-3xl font-extrabold">₹{{ number_format($totalEarnings, 2) }}</div>
                </div>
            </div>

            {{-- Pending --}}
            <div
                class="bg-customWhite rounded-2xl p-6 border border-primary/10 shadow-sm hover:border-primary/20 transition-all group">
                <div
                    class="text-mutedText text-xs font-bold uppercase tracking-wider mb-2 group-hover:text-primary transition-colors">
                    Pending Payout</div>
                <div class="text-3xl font-extrabold text-mainText">
                    ₹{{ number_format($pendingEarnings, 2) }}</div>
            </div>

            {{-- Total Referrals --}}
            <div
                class="bg-customWhite rounded-2xl p-6 border border-primary/10 shadow-sm hover:border-primary/20 transition-all group">
                <div
                    class="text-mutedText text-xs font-bold uppercase tracking-wider mb-2 group-hover:text-primary transition-colors">
                    Network Size</div>
                <div class="text-3xl font-extrabold text-mainText">{{ number_format($totalReferrals) }}
                </div>
            </div>
        </div>

        {{-- 5. Recent Referrals Table --}}
        <div class="bg-customWhite overflow-hidden shadow-sm rounded-2xl border border-primary/10">
            <div class="p-6 border-b border-navy/5 flex justify-between items-center bg-navy/5">
                <h3 class="text-sm font-bold text-mainText uppercase tracking-wider">Recent Network Growth</h3>
                <a href="#"
                    class="text-xs font-bold text-primary hover:text-primary/80 uppercase tracking-wider">
                    View Complete History
                </a>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-mutedText">
                    <thead class="bg-navy/5 text-xs uppercase font-bold text-mutedText/70 tracking-wider">
                        <tr>
                            <th class="px-6 py-4">Student Identity</th>
                            <th class="px-6 py-4">Date Joined</th>
                            <th class="px-6 py-4 text-right">Verification Status</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-navy/5">
                        @forelse($recentReferrals as $ref)
                            <tr class="hover:bg-navy/5 transition-all group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center">
                                        <div
                                            class="h-8 w-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center font-bold mr-3 border border-primary/10 uppercase group-hover:scale-110 transition-transform shadow-inner text-xs">
                                            {{ substr($ref->name, 0, 2) }}
                                        </div>
                                        <div>
                                            <div
                                                class="font-bold text-mainText text-sm">
                                                {{ $ref->name }}</div>
                                            <p class="text-xs text-mutedText mt-0.5">Enrolled Partner</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 text-sm font-medium text-mutedText">
                                    {{ $ref->created_at->format('d M, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    @if ($ref->is_active)
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide bg-emerald-500/10 text-emerald-500 border border-emerald-500/20">
                                            <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 mr-2"></span> Active
                                        </span>
                                    @else
                                        <span
                                            class="inline-flex items-center px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wide bg-navy/5 text-mutedText border border-primary/10">
                                            Pending
                                        </span>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center max-w-xs mx-auto">
                                        <div
                                            class="w-12 h-12 bg-navy/5 rounded-full flex items-center justify-center mb-3 border border-primary/10">
                                            <svg class="w-6 h-6 text-mutedText/50" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                                </path>
                                            </svg>
                                        </div>
                                        <p
                                            class="text-mutedText font-bold text-sm opacity-80">
                                            No referrals found yet</p>
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
