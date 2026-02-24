@extends('layouts.admin')
@section('title', 'Verifications Hub')

@section('content')
    <div x-data="verificationManager()" class="space-y-8 font-sans text-mainText">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-end gap-4 px-2">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-mainText">Verifications Hub</h1>
                <p class="text-mutedText mt-1 text-sm font-medium">Review and verify user identity and bank documents.</p>
            </div>

            {{-- Tab Switcher --}}
            <div class="flex bg-navy/5 p-1 rounded-2xl border border-primary/10">
                <button @click="activeTab = 'kyc'"
                    :class="activeTab === 'kyc' ? 'bg-customWhite shadow-sm text-primary' : 'text-mutedText hover:bg-navy/5'"
                    class="px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                    KYC Requests ({{ $pendingKyc->count() }})
                </button>
                <button @click="activeTab = 'bank'"
                    :class="activeTab === 'bank' ? 'bg-customWhite shadow-sm text-primary' : 'text-mutedText hover:bg-navy/5'"
                    class="px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                    Bank Requests ({{ count($pendingBankInitial) + count($pendingBankUpdates) }})
                </button>
            </div>
        </div>

        {{-- 1. KYC TAB --}}
        <div x-show="activeTab === 'kyc'" x-transition.opacity class="space-y-6">
            <div class="bg-surface rounded-3xl shadow-sm border border-primary/10 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm">
                        <thead class="bg-primary/5 text-[11px] uppercase font-black text-primary tracking-widest border-b border-primary/10">
                            <tr>
                                <th class="px-6 py-5">User Details</th>
                                <th class="px-6 py-5">Sponsor Details</th>
                                <th class="px-6 py-5">Date</th>
                                <th class="px-6 py-5 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-primary/5">
                            @forelse($pendingKyc as $user)
                                <tr class="hover:bg-primary/5 transition-colors group">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-xl bg-navy/30 flex items-center justify-center text-primary font-black border border-primary/10">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-mainText">{{ $user->name }} <span class="text-[10px] text-mutedText/50">#{{ $user->id }}</span></p>
                                                <div class="flex flex-col text-[10px] font-bold text-mutedText/80">
                                                    <span><i class="fas fa-envelope mr-1"></i>{{ $user->email }}</span>
                                                    <span><i class="fas fa-phone mr-1"></i>{{ $user->mobile }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($user->referrer)
                                            <div>
                                                <p class="font-bold text-mutedText text-xs">{{ $user->referrer->name }}</p>
                                                <p class="text-[10px] text-mutedText/60 italic">{{ $user->referrer->email }}</p>
                                            </div>
                                        @else
                                            <span class="text-[10px] text-mutedText/40 italic font-bold">No Sponsor</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 font-bold text-mutedText text-xs">
                                        {{ $user->kyc->created_at->format('d M, Y') }}
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <button @click="openKycModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ $user->mobile }}', '{{ $user->dob ? $user->dob->format('d M, Y') : 'N/A' }}', '{{ $user->kyc->pan_name }}', '{{ asset('storage/' . $user->kyc->document_path) }}', '{{ pathinfo($user->kyc->document_path, PATHINFO_EXTENSION) }}', '{{ $user->referrer ? addslashes($user->referrer->name) : 'No Sponsor' }}', '{{ $user->referrer ? $user->referrer->email : '' }}', '{{ $user->referrer ? $user->referrer->mobile : '' }}')"
                                            class="brand-gradient text-white px-5 py-2 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-sm hover:scale-[1.02] transition">
                                            Review KYC
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-10 text-center text-mutedText font-bold italic">No pending KYC requests found.</td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($pendingKyc->hasPages())
                    <div class="p-4 bg-primary/5 border-t border-primary/5">
                        {{ $pendingKyc->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- 2. BANK TAB --}}
        <div x-show="activeTab === 'bank'" x-transition.opacity class="space-y-8">
            {{-- Initial Setup --}}
            <section>
                <h2 class="text-sm font-black uppercase tracking-[2px] text-primary mb-4 px-2">Initial Setup Requests</h2>
                <div class="bg-surface rounded-3xl shadow-sm border border-primary/10 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-primary/5 text-[11px] uppercase font-black text-primary tracking-widest">
                                <tr>
                                    <th class="px-6 py-5">User & Sponsor</th>
                                    <th class="px-6 py-5">Bank Details</th>
                                    <th class="px-6 py-5">Proof</th>
                                    <th class="px-6 py-5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-primary/5">
                                @forelse($pendingBankInitial as $bank)
                                    <tr class="hover:bg-primary/5 transition-colors">
                                        <td class="px-6 py-4">
                                            <p class="font-black text-mainText">{{ $bank->user->name }} <span class="text-xs text-mutedText font-bold">#{{ $bank->user_id }}</span></p>
                                            <p class="text-[10px] font-bold text-mutedText italic">Sponsor: {{ $bank->user->referrer->name ?? 'N/A' }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="text-[10px] font-bold space-y-0.5">
                                                <p class="text-mainText uppercase bg-navy/5 px-2 py-0.5 rounded-md inline-block">{{ $bank->bank_name }}</p>
                                                <p class="text-mutedText">A/C: {{ $bank->account_number }}</p>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4">
                                            <a href="{{ asset('storage/' . $bank->document_path) }}" target="_blank" class="text-primary font-black text-[10px] uppercase hover:underline">
                                                <i class="fas fa-file-pdf mr-1"></i> View Proof
                                            </a>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button @click="openBankInitialReview({{ json_encode($bank) }}, '{{ asset('storage/' . $bank->document_path) }}')"
                                                class="brand-gradient text-white px-5 py-2 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-primary/10 transition hover:scale-[1.02]">
                                                Review Details
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="4" class="px-6 py-12 text-center text-mutedText font-bold italic">No pending initial bank setups.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            {{-- Update Requests --}}
            <section>
                <h2 class="text-sm font-black uppercase tracking-[2px] text-secondary mb-4 px-2">Bank Change Requests</h2>
                <div class="bg-surface rounded-3xl shadow-sm border border-secondary/10 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm">
                            <thead class="bg-secondary/5 text-[11px] uppercase font-black text-secondary tracking-widest">
                                <tr>
                                    <th class="px-6 py-5">User & Sponsor</th>
                                    <th class="px-6 py-5">Current → New Details</th>
                                    <th class="px-6 py-5 text-right">Action</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-secondary/5">
                                @forelse($pendingBankUpdates as $req)
                                    <tr class="hover:bg-secondary/5 transition-colors group">
                                        <td class="px-6 py-4">
                                            <p class="font-black text-mainText">{{ $req->user->name }} <span class="text-xs text-mutedText font-bold">#{{ $req->user_id }}</span></p>
                                            <p class="text-[10px] font-bold text-mutedText italic">Sponsor: {{ $req->user->referrer->name ?? 'N/A' }}</p>
                                        </td>
                                        <td class="px-6 py-4">
                                            <div class="flex items-center gap-4">
                                                {{-- Old --}}
                                                <div class="text-[9px] opacity-40 italic font-bold">
                                                    <p>{{ $req->old_data['bank_name'] ?? 'N/A' }}</p>
                                                    <p>...{{ substr($req->old_data['account_number'] ?? '0000', -4) }}</p>
                                                </div>
                                                <i class="fas fa-long-arrow-alt-right text-secondary animate-pulse"></i>
                                                {{-- New --}}
                                                <div class="text-[10px] font-bold">
                                                    <p class="text-emerald-600 bg-emerald-50 px-2 rounded">{{ $req->bank_name }}</p>
                                                    <p class="text-mainText">{{ $req->account_number }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-6 py-4 text-right">
                                            <button @click="openBankUpdateReview({{ json_encode($req) }}, '{{ asset('storage/' . $req->document_path) }}')"
                                                class="bg-secondary text-white px-4 py-2 rounded-xl font-black text-[10px] uppercase tracking-widest shadow-lg shadow-secondary/10">
                                                Review Change
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="3" class="px-6 py-12 text-center text-mutedText font-bold italic">No pending bank update requests.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        {{-- ========================================== --}}
        {{-- MODALS --}}
        {{-- ========================================== --}}

        {{-- 1. KYC REVIEW MODAL --}}
        <div x-show="kycModalOpen" x-cloak class="fixed inset-0 z-[60] overflow-y-auto">
            <div class="fixed inset-0 bg-navy/80 backdrop-blur-md"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div @click.away="kycModalOpen = false"
                    class="bg-surface w-full max-w-6xl h-[85vh] rounded-[40px] flex flex-col md:flex-row overflow-hidden shadow-2xl relative border border-primary/10">

                    <button @click="kycModalOpen = false" class="absolute top-6 right-6 z-[70] bg-white/10 hover:bg-secondary text-white p-2 rounded-full transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    {{-- Left: Document View --}}
                    <div class="w-full md:w-3/5 bg-black flex flex-col items-center justify-center relative p-10">
                        <div class="w-full h-full rounded-2xl overflow-hidden bg-navy/20 border border-white/5 shadow-inner">
                            <template x-if="kycData.ext === 'pdf'">
                                <iframe :src="kycData.url" class="w-full h-full border-0"></iframe>
                            </template>
                            <template x-if="kycData.ext !== 'pdf'">
                                <img :src="kycData.url" class="w-full h-full object-contain">
                            </template>
                        </div>
                        <a :href="kycData.url" target="_blank" class="mt-6 brand-gradient text-white px-8 py-3 rounded-2xl text-[10px] font-black uppercase tracking-[2px] shadow-lg shadow-primary/20">
                            View Full Document
                        </a>
                    </div>

                    {{-- Right: Content View --}}
                    <div class="w-full md:w-2/5 flex flex-col bg-surface border-l border-primary/5">
                        <div class="p-10 border-b border-primary/5">
                            <h3 class="text-2xl font-black text-mainText">KYC Verification</h3>
                            <p class="text-[10px] text-mutedText uppercase font-bold tracking-widest mt-1">Cross-Check Document with System Data</p>
                        </div>

                        <div class="p-10 flex-1 overflow-y-auto space-y-8">
                            {{-- User Base Info --}}
                            <div class="space-y-4">
                                <h4 class="text-[10px] font-black text-primary uppercase tracking-[2px] bg-primary/5 inline-block px-2 py-0.5 rounded">User Profile</h4>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <p class="text-[9px] text-mutedText uppercase font-black tracking-widest">System Name</p>
                                        <p class="font-bold text-mainText" x-text="kycData.name"></p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] text-mutedText uppercase font-black tracking-widest">User ID</p>
                                        <p class="font-bold text-primary" x-text="'#' + kycData.id"></p>
                                    </div>
                                    <div class="col-span-2">
                                        <p class="text-[9px] text-mutedText uppercase font-black tracking-widest">Contact Info</p>
                                        <p class="text-xs font-bold text-mainText" x-text="kycData.email"></p>
                                        <p class="text-xs font-bold text-mainText" x-text="kycData.mobile"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Sponsor Info --}}
                            <div class="bg-navy/5 p-6 rounded-3xl border border-primary/5 space-y-3">
                                <h4 class="text-[10px] font-black text-mutedText uppercase tracking-[2px]">Sponsor Data</h4>
                                <div>
                                    <p class="text-[10px] font-black text-mainText uppercase" x-text="kycData.referrer_name"></p>
                                    <p class="text-[10px] font-bold text-mutedText" x-text="kycData.referrer_email"></p>
                                    <p class="text-[10px] font-bold text-mutedText" x-text="kycData.referrer_mobile"></p>
                                </div>
                            </div>

                            {{-- Document Info --}}
                            <div class="space-y-4">
                                <h4 class="text-[10px] font-black text-emerald-600 uppercase tracking-[2px] bg-emerald-50 inline-block px-2 py-0.5 rounded">Submitted Proof</h4>
                                <div>
                                    <p class="text-[9px] text-mutedText uppercase font-black tracking-widest">Name on ID</p>
                                    <p class="text-xl font-black text-emerald-600" x-text="kycData.id_name"></p>
                                </div>
                            </div>
                        </div>

                        {{-- Action Area --}}
                        <div class="p-10 bg-navy/5 border-t border-primary/5">
                            <div class="flex gap-4">
                                <button @click="processKyc('approve')" class="flex-1 brand-gradient text-white py-4 rounded-2xl font-black text-[10px] uppercase shadow-lg shadow-primary/20">Approve</button>
                                <button @click="showRejectKyc = true" class="flex-1 bg-surface border border-secondary text-secondary py-4 rounded-2xl font-black text-[10px] uppercase">Reject</button>
                            </div>

                            <div x-show="showRejectKyc" class="mt-4 animate-fadeIn">
                                <textarea x-model="adminNote" class="w-full border-secondary/20 rounded-xl bg-white text-xs p-4 font-bold" rows="3" placeholder="Explain the reason for rejection..."></textarea>
                                <div class="flex justify-end gap-3 mt-3">
                                    <button @click="showRejectKyc = false" class="text-[10px] font-black text-mutedText p-2 uppercase">Cancel</button>
                                    <button @click="processKyc('reject')" class="bg-secondary text-white px-6 py-2 rounded-xl text-[10px] font-black uppercase shadow-lg shadow-secondary/20">Confirm Reject</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. BANK INITIAL REVIEW MODAL --}}
        <div x-show="bankInitialModalOpen" x-cloak class="fixed inset-0 z-[60] overflow-y-auto">
            <div class="fixed inset-0 bg-navy/80 backdrop-blur-md"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div @click.away="bankInitialModalOpen = false"
                    class="bg-surface w-full max-w-5xl rounded-[40px] flex flex-col md:flex-row overflow-hidden shadow-2xl relative border border-primary/10">

                    <button @click="bankInitialModalOpen = false" class="absolute top-6 right-6 z-[70] bg-white/10 hover:bg-secondary text-white p-2 rounded-full transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    <div class="w-full md:w-3/5 bg-black flex items-center justify-center p-6">
                         <img :src="activeBankDoc" class="max-w-full max-h-[75vh] object-contain rounded-xl">
                    </div>

                    <div class="w-full md:w-2/5 p-10 flex flex-col bg-surface overflow-y-auto max-h-[85vh]">
                        <h3 class="text-xl font-black text-mainText uppercase mb-6 tracking-widest border-b border-navy pb-4">Bank Detail Setup</h3>

                        <div class="space-y-6 flex-1">
                            {{-- User Context --}}
                            <div class="bg-navy/5 p-5 rounded-2xl border border-primary/5">
                                <p class="text-[9px] font-black uppercase text-mutedText tracking-widest mb-3">System Context</p>
                                <div class="space-y-2">
                                    <p class="text-sm font-bold text-mainText" x-text="activeInitialBankReq.user?.name + ' (#' + activeInitialBankReq.user_id + ')'"></p>
                                    <p class="text-[10px] font-bold text-mutedText italic" x-text="'Sponsor: ' + (activeInitialBankReq.user?.referrer?.name || 'N/A')"></p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <h4 class="text-[10px] font-black text-primary uppercase tracking-[2px]">Bank Information</h4>
                                <div class="grid grid-cols-1 gap-4">
                                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-navy/5">
                                        <p class="text-[9px] font-black uppercase text-mutedText mb-1">Bank Name</p>
                                        <p class="text-sm font-black text-mainText" x-text="activeInitialBankReq.bank_name"></p>
                                    </div>
                                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-navy/5">
                                        <p class="text-[9px] font-black uppercase text-mutedText mb-1">Account Holder Name</p>
                                        <p class="text-sm font-black text-primary uppercase" x-text="activeInitialBankReq.account_holder_name"></p>
                                    </div>
                                    <div class="grid grid-cols-2 gap-3">
                                        <div class="bg-white p-4 rounded-2xl shadow-sm border border-navy/5">
                                            <p class="text-[9px] font-black uppercase text-mutedText mb-1">Account Number</p>
                                            <p class="text-sm font-black text-mainText" x-text="activeInitialBankReq.account_number"></p>
                                        </div>
                                        <div class="bg-white p-4 rounded-2xl shadow-sm border border-navy/5">
                                            <p class="text-[9px] font-black uppercase text-mutedText mb-1">IFSC Code</p>
                                            <p class="text-sm font-black text-mainText uppercase" x-text="activeInitialBankReq.ifsc_code"></p>
                                        </div>
                                    </div>
                                    <div class="bg-white p-4 rounded-2xl shadow-sm border border-navy/5">
                                        <p class="text-[9px] font-black uppercase text-mutedText mb-1">UPI ID</p>
                                        <p class="text-sm font-black text-emerald-600" x-text="activeInitialBankReq.upi_id || 'Not Provided'"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-navy space-y-4">
                            <div class="flex gap-3">
                                <button @click="processInitialBank('approve')" class="flex-1 brand-gradient text-white py-4 rounded-2xl font-black text-[10px] uppercase shadow-lg shadow-primary/20 transition hover:scale-[1.02]">Approve Setup</button>
                                <button @click="showBankInitialReject = true" class="flex-1 bg-surface border border-secondary text-secondary py-4 rounded-2xl font-black text-[10px] uppercase transition hover:bg-secondary/5">Reject</button>
                            </div>

                            <div x-show="showBankInitialReject" class="animate-fadeIn">
                                <textarea x-model="adminNote" class="w-full border-secondary/20 rounded-xl bg-white text-xs p-4 font-bold" rows="2" placeholder="Rejection reason..."></textarea>
                                <button @click="processInitialBank('reject')" class="w-full mt-2 bg-secondary text-white py-2 rounded-xl text-[10px] font-black uppercase">Confirm Reject</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. BANK UPDATE REVIEW MODAL --}}
        <div x-show="bankUpdateModalOpen" x-cloak class="fixed inset-0 z-[60] overflow-y-auto">
            <div class="fixed inset-0 bg-navy/80 backdrop-blur-md"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div @click.away="bankUpdateModalOpen = false"
                    class="bg-surface w-full max-w-4xl rounded-[40px] flex flex-col md:flex-row overflow-hidden shadow-2xl relative border border-secondary/10">

                    <button @click="bankUpdateModalOpen = false" class="absolute top-6 right-6 z-[70] bg-white/10 hover:bg-secondary text-white p-2 rounded-full transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    <div class="w-full md:w-1/2 bg-black flex items-center justify-center p-6">
                         <img :src="activeBankDoc" class="max-w-full max-h-[70vh] object-contain rounded-xl">
                    </div>

                    <div class="w-full md:w-1/2 p-10 flex flex-col max-h-[85vh] overflow-y-auto">
                        <h3 class="text-xl font-black text-mainText uppercase mb-6 tracking-widest border-b border-navy pb-4">Bank Detail Update</h3>

                        <div class="space-y-6 flex-1">
                            <div class="grid grid-cols-2 gap-4">
                                <div class="bg-navy/5 p-4 rounded-2xl opacity-50">
                                    <p class="text-[9px] font-black uppercase text-mutedText mb-2">Old Account</p>
                                    <p class="text-xs font-bold text-mainText" x-text="activeBankReq.old_data?.account_number"></p>
                                    <p class="text-[10px] text-mutedText" x-text="activeBankReq.old_data?.bank_name"></p>
                                </div>
                                <div class="bg-emerald-50 p-4 rounded-2xl border border-emerald-100">
                                    <p class="text-[9px] font-black uppercase text-emerald-600 mb-2">New Account</p>
                                    <p class="text-xs font-black text-emerald-700" x-text="activeBankReq.account_number"></p>
                                    <p class="text-[10px] text-emerald-600 font-bold" x-text="activeBankReq.bank_name"></p>
                                </div>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <p class="text-[10px] font-black uppercase text-mutedText tracking-widest mb-1">New Holder Name</p>
                                    <p class="text-sm font-bold text-mainText p-3 bg-navy/5 rounded-xl uppercase" x-text="activeBankReq.account_holder_name"></p>
                                </div>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <p class="text-[10px] font-black uppercase text-mutedText tracking-widest mb-1">New IFSC Code</p>
                                        <p class="text-sm font-bold text-mainText p-3 bg-navy/5 rounded-xl uppercase" x-text="activeBankReq.ifsc_code"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-black uppercase text-mutedText tracking-widest mb-1">New UPI ID</p>
                                        <p class="text-sm font-bold text-emerald-600 p-3 bg-emerald-50 rounded-xl" x-text="activeBankReq.upi_id || 'N/A'"></p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 pt-6 border-t border-navy space-y-4">
                            <div class="flex gap-3">
                                <button @click="processBankUpdate('approve')" class="flex-1 bg-emerald-500 text-white py-3 rounded-2xl font-black text-[10px] uppercase shadow-lg shadow-emerald-500/20 transition hover:scale-[1.02]">Approve Update</button>
                                <button @click="showBankUpdateReject = true" class="flex-1 bg-secondary text-white py-3 rounded-2xl font-black text-[10px] uppercase shadow-lg shadow-secondary/20 transition hover:bg-secondary/90">Reject Update</button>
                            </div>

                            <div x-show="showBankUpdateReject" class="animate-fadeIn">
                                <textarea x-model="adminNote" class="w-full border-secondary/20 rounded-xl bg-white text-xs p-4 font-bold" rows="2" placeholder="Rejection reason..."></textarea>
                                <button @click="processBankUpdate('reject')" class="w-full mt-2 bg-secondary text-white py-2 rounded-xl text-[10px] font-black uppercase">Confirm Reject</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. BANK REJECT MODAL (For simple table actions) --}}
        <template x-if="bankRejectModalOpen">
            <div class="fixed inset-0 z-[100] flex items-center justify-center p-4">
                <div class="fixed inset-0 bg-navy/90 backdrop-blur-sm" @click="bankRejectModalOpen = false"></div>
                <div class="bg-surface w-full max-w-md p-8 rounded-[32px] relative shadow-2xl border border-secondary/20">
                    <h3 class="text-xl font-black text-secondary uppercase tracking-widest mb-2">Reject Request</h3>
                    <p class="text-xs text-mutedText font-bold mb-6">Explain why this bank detail setup/update is being rejected.</p>

                    <textarea x-model="adminNote" class="w-full border-primary/10 rounded-2xl bg-navy/5 p-4 text-xs font-bold min-h-[120px]" placeholder="e.g. Invalid IFSC code, account mismatch..."></textarea>

                    <div class="flex gap-4 mt-8">
                        <button @click="bankRejectModalOpen = false" class="flex-1 py-3 text-xs font-black text-mutedText uppercase">Cancel</button>
                        <button @click="confirmBankRejection()" class="flex-1 bg-secondary text-white py-3 rounded-xl font-black text-xs uppercase shadow-lg shadow-secondary/20">Submit Rejection</button>
                    </div>
                </div>
            </div>
        </template>

    </div>

    <script>
        function verificationManager() {
            return {
                activeTab: 'kyc',
                kycModalOpen: false,
                bankUpdateModalOpen: false,
                bankInitialModalOpen: false,
                showRejectKyc: false,
                showBankUpdateReject: false,
                showBankInitialReject: false,
                bankRejectModalOpen: false,

                adminNote: '',
                kycData: {},
                activeBankReq: {},
                activeInitialBankReq: {},
                activeBankDoc: '',
                activeBankId: null,
                activeBankType: 'initial',

                openKycModal(id, name, email, mobile, dob, id_name, url, ext, r_name, r_email, r_mobile) {
                    this.kycData = { id, name, email, mobile, dob, id_name, url, ext, referrer_name: r_name, referrer_email: r_email, referrer_mobile: r_mobile };
                    this.adminNote = '';
                    this.showRejectKyc = false;
                    this.kycModalOpen = true;
                },

                openBankUpdateReview(req, docUrl) {
                    this.activeBankReq = req;
                    this.activeBankDoc = docUrl;
                    this.adminNote = '';
                    this.showBankUpdateReject = false;
                    this.bankUpdateModalOpen = true;
                },

                openBankInitialReview(req, docUrl) {
                    this.activeInitialBankReq = req;
                    this.activeBankDoc = docUrl;
                    this.adminNote = '';
                    this.showBankInitialReject = false;
                    this.bankInitialModalOpen = true;
                },

                processKyc(action) {
                    if (action === 'reject' && !this.adminNote) return Swal.fire('Error', 'Provide rejection reason', 'error');

                    const url = action === 'approve'
                        ? `{{ route('admin.verifications.kyc.approve', ':id') }}`.replace(':id', this.kycData.id)
                        : `{{ route('admin.verifications.kyc.reject', ':id') }}`.replace(':id', this.kycData.id);

                    Swal.fire({
                        title: 'Are you sure?',
                        text: `You want to ${action} this KYC.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Proceed!',
                        confirmButtonColor: '#F7941D'
                    }).then(res => {
                        if (res.isConfirmed) {
                            axios.post(url, { admin_note: this.adminNote })
                                .then(() => Swal.fire('Success', 'Process complete', 'success').then(() => location.reload()))
                                .catch(() => Swal.fire('Error', 'Action failed', 'error'));
                        }
                    });
                },

                processBankUpdate(action) {
                    if (action === 'reject' && !this.adminNote) return Swal.fire('Error', 'Provide note', 'error');

                    const url = `{{ route('admin.verifications.bank.process-update', ':id') }}`.replace(':id', this.activeBankReq.id);

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;

                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';

                    const actInput = document.createElement('input');
                    actInput.type = 'hidden';
                    actInput.name = 'action';
                    actInput.value = action;

                    const note = document.createElement('input');
                    note.type = 'hidden';
                    note.name = 'admin_note';
                    note.value = this.adminNote;

                    form.appendChild(csrf);
                    form.appendChild(actInput);
                    form.appendChild(note);
                    document.body.appendChild(form);
                    form.submit();
                },

                processInitialBank(action) {
                    if (action === 'reject' && !this.adminNote) return Swal.fire('Error', 'Provide note', 'error');

                    const url = `{{ route('admin.verifications.bank.verify-initial', ':id') }}`.replace(':id', this.activeInitialBankReq.id);

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;

                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';

                    const actInput = document.createElement('input');
                    actInput.type = 'hidden';
                    actInput.name = 'action';
                    actInput.value = action;

                    const note = document.createElement('input');
                    note.type = 'hidden';
                    note.name = 'admin_note';
                    note.value = this.adminNote;

                    form.appendChild(csrf);
                    form.appendChild(actInput);
                    form.appendChild(note);
                    document.body.appendChild(form);
                    form.submit();
                },

                openBankRejectModal(type, id) {
                    this.activeBankType = type;
                    this.activeBankId = id;
                    this.adminNote = '';
                    this.bankRejectModalOpen = true;
                },

                confirmBankRejection() {
                    if(!this.adminNote) return Swal.fire('Error', 'Provide note', 'error');

                    const url = this.activeBankType === 'initial'
                        ? `{{ route('admin.verifications.bank.verify-initial', ':id') }}`.replace(':id', this.activeBankId)
                        : `{{ route('admin.verifications.bank.process-update', ':id') }}`.replace(':id', this.activeBankId);

                    const form = document.createElement('form');
                    form.method = 'POST';
                    form.action = url;

                    const csrf = document.createElement('input');
                    csrf.type = 'hidden';
                    csrf.name = '_token';
                    csrf.value = '{{ csrf_token() }}';

                    const action = document.createElement('input');
                    action.type = 'hidden';
                    action.name = 'action';
                    action.value = 'reject';

                    const note = document.createElement('input');
                    note.type = 'hidden';
                    note.name = 'admin_note';
                    note.value = this.adminNote;

                    form.appendChild(csrf);
                    form.appendChild(action);
                    form.appendChild(note);
                    document.body.appendChild(form);
                    form.submit();
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(5px); } to { opacity: 1; transform: translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.3s ease-out forwards; }
    </style>
@endsection
