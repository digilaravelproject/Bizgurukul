<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-slate-800 leading-tight">
            {{ __('Student Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- 1. Error/Success Messages --}}
            @if(session('error'))
                <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative" role="alert">
                    <span class="block sm:inline">{{ session('error') }}</span>
                </div>
            @endif

            {{-- 2. Referral Link Section --}}
            <div
                class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100 p-6 transition-all hover:shadow-md">
                <h3 class="text-lg font-bold text-slate-800 mb-2">Your Affiliate Link</h3>
                <p class="text-slate-500 text-sm mb-4">Share this link to earn <span
                        class="text-indigo-600 font-bold">₹{{ $commissionAmount }}</span>
                    per referral.</p>

                <div class="flex flex-col sm:flex-row gap-3" x-data="{
                    copied: false,
                    shareLink: '{{ $referralLink }}',
                    copyToClipboard() {
                        if (!navigator.clipboard) {
                            // Fallback for older browsers or non-https
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
                        class="w-full bg-slate-50 border border-slate-200 text-slate-600 text-sm rounded-xl focus:ring-indigo-500 focus:border-indigo-500 block p-3 font-medium select-all">

                    <button @click="copyToClipboard()"
                        class="inline-flex items-center justify-center px-6 py-3 border border-transparent text-sm font-bold rounded-xl shadow-sm text-white bg-indigo-600 hover:bg-indigo-700 focus:outline-none transition-all active:scale-95">
                        <span x-show="!copied">Copy Link</span>
                        <span x-show="copied" class="flex items-center" style="display: none;">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M5 13l4 4L19 7"></path>
                            </svg>
                            Copied!
                        </span>
                    </button>
                </div>
                <div class="mt-2 text-sm text-slate-500 flex items-center gap-2">
                    Your Referral Code:
                    <span
                        class="font-mono font-bold text-indigo-700 bg-indigo-50 px-2 py-0.5 rounded border border-indigo-100 uppercase tracking-tighter">
                        {{ $user->referral_code ?? 'N/A' }}
                    </span>
                </div>
            </div>

            {{-- 3. Stats Grid --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- Total Earnings --}}
                <div
                    class="bg-gradient-to-br from-indigo-600 to-indigo-700 rounded-2xl p-6 text-white shadow-lg shadow-indigo-200">
                    <div class="flex justify-between items-start">
                        <div class="text-indigo-100 text-sm font-medium uppercase tracking-wider">Total Earnings</div>
                        <svg class="w-6 h-6 text-indigo-200 opacity-50" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                            </path>
                        </svg>
                    </div>
                    <div class="text-3xl font-extrabold mt-1">₹{{ number_format($totalEarnings, 2) }}</div>
                </div>

                {{-- Pending --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <div class="text-slate-500 text-sm font-medium uppercase tracking-wider">Pending Payout</div>
                    <div class="text-3xl font-extrabold text-slate-800 mt-1">₹{{ number_format($pendingEarnings, 2) }}
                    </div>
                </div>

                {{-- Total Referrals --}}
                <div class="bg-white rounded-2xl p-6 border border-slate-100 shadow-sm">
                    <div class="text-slate-500 text-sm font-medium uppercase tracking-wider">Total Referrals</div>
                    <div class="text-3xl font-extrabold text-slate-800 mt-1">{{ number_format($totalReferrals) }}</div>
                </div>
            </div>

            {{-- 4. Recent Referrals Table --}}
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-100">
                <div class="p-6 border-b border-slate-100 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800">Recent Referrals</h3>
                    <a href="#"
                        class="text-xs font-bold text-indigo-600 hover:text-indigo-800 uppercase tracking-widest">View
                        All</a>
                </div>
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500">
                            <tr>
                                <th class="px-6 py-4">Name</th>
                                <th class="px-6 py-4">Date Joined</th>
                                <th class="px-6 py-4">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            @forelse($recentReferrals as $ref)
                                <tr class="hover:bg-slate-50/80 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            <div
                                                class="h-8 w-8 rounded-full bg-indigo-100 text-indigo-700 flex items-center justify-center font-bold mr-3 text-xs uppercase">
                                                {{ substr($ref->name, 0, 2) }}
                                            </div>
                                            <div class="font-medium text-slate-800">{{ $ref->name }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 text-slate-500">{{ $ref->created_at->format('d M, Y') }}</td>
                                    <td class="px-6 py-4">
                                        @if($ref->is_active)
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-green-100 text-green-800">
                                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Active
                                            </span>
                                        @else
                                            <span
                                                class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-600">
                                                Inactive
                                            </span>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-slate-200 mb-3" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z">
                                                </path>
                                            </svg>
                                            <p class="text-slate-400 font-medium">No referrals yet. Share your link to start
                                                earning!</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
