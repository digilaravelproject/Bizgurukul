@extends('layouts.admin')
@section('title', 'Verifications Hub')

@section('content')
    <div x-data="verificationManager()" class="space-y-8 font-sans text-mainText">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-end gap-6 px-2">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-mainText uppercase">Compliance Hub</h1>
                <p class="text-mutedText mt-1 text-sm font-semibold tracking-wide">Centralized verification authority for
                    identity and finance.</p>
            </div>

            {{-- Tab Switcher --}}
            <div class="flex bg-white/60 backdrop-blur-md p-1 rounded-[24px] border border-primary/10 shadow-sm">
                <button @click="activeTab = 'kyc'"
                    :class="activeTab === 'kyc' ? 'brand-gradient text-white shadow-lg shadow-primary/20' : 'text-mutedText hover:bg-primary/5 hover:text-primary'"
                    class="px-8 py-3 rounded-[20px] text-[11px] font-black uppercase tracking-[1px] transition-all duration-300">
                    KYC Management <span
                        class="ml-2 px-2 py-0.5 bg-black/10 rounded-lg text-[9px]">{{ $pendingKycCount + $verifiedKycCount }}</span>
                </button>
                <button @click="activeTab = 'bank'"
                    :class="activeTab === 'bank' ? 'bg-secondary text-white shadow-lg shadow-secondary/20' : 'text-mutedText hover:bg-secondary/5 hover:text-secondary'"
                    class="px-8 py-3 rounded-[20px] text-[11px] font-black uppercase tracking-[1px] transition-all duration-300">
                    Payout Sync <span
                        class="ml-2 px-2 py-0.5 bg-black/10 rounded-lg text-[9px]">{{ count($pendingBankInitial) + count($pendingBankUpdates) }}</span>
                </button>
            </div>
        </div>

        {{-- 1. KYC TAB --}}
        <div x-show="activeTab === 'kyc'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" class="space-y-6">

            {{-- KYC Status Filter --}}
            <div class="flex items-center gap-3 px-2">
                <a href="{{ route('admin.verifications.index', ['kyc_status' => 'pending', 'activeTab' => 'kyc']) }}"
                    class="px-6 py-2.5 rounded-[18px] text-[10px] font-black uppercase tracking-[1px] transition-all {{ $kycStatus === 'pending' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'bg-primary/5 text-mutedText hover:bg-primary/10' }}">
                    <i class="fas fa-clock mr-2 opacity-70"></i> Pending Requests ({{ $pendingKycCount }})
                </a>
                <a href="{{ route('admin.verifications.index', ['kyc_status' => 'verified', 'activeTab' => 'kyc']) }}"
                    class="px-6 py-2.5 rounded-[18px] text-[10px] font-black uppercase tracking-[1px] transition-all {{ $kycStatus === 'verified' ? 'bg-emerald-500 text-white shadow-lg shadow-emerald-500/20' : 'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' }}">
                    <i class="fas fa-check-circle mr-2 opacity-70"></i> Verified Archive ({{ $verifiedKycCount }})
                </a>
            </div>

            <div class="bg-surface rounded-[40px] shadow-sm border border-primary/10 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead
                            class="bg-primary/5 text-[11px] uppercase font-black text-primary tracking-[2px] border-b border-primary/10">
                            <tr>
                                <th class="px-8 py-7">User Profile</th>
                                <th class="px-8 py-7">Network Sponsor</th>
                                <th class="px-8 py-7">Bank / Type</th>
                                <th class="px-8 py-7">Reg. Date</th>
                                <th class="px-8 py-7">Status</th>
                                <th class="px-8 py-7 text-right">Verification</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-primary/5">
                            @forelse($kycUsers as $user)
                                <tr class="hover:bg-primary/5 transition-all group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div
                                                class="w-12 h-12 rounded-2xl {{ $user->kyc->status === 'verified' ? 'bg-emerald-500 text-white' : 'bg-navy text-primary' }} flex items-center justify-center font-black text-lg border border-primary/10 shadow-sm group-hover:scale-110 transition-transform">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-black text-mainText text-base">{{ $user->name }} <span
                                                        class="text-[10px] text-mutedText/40 ml-1">#{{ $user->id }}</span></p>
                                                <div
                                                    class="flex flex-col text-[11px] font-bold text-mutedText/70 tracking-tight">
                                                    <span>{{ $user->email }}</span>
                                                    <span>{{ $user->mobile }}</span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        @if($user->referrer)
                                            <div class="p-3 bg-navy/5 rounded-xl border border-navy/5">
                                                <p class="font-black text-mainText text-[10px] uppercase">
                                                    {{ $user->referrer->name }}</p>
                                                <p class="text-[9px] font-bold text-mutedText truncate">{{ $user->referrer->email }}
                                                </p>
                                            </div>
                                        @else
                                            <span
                                                class="text-[10px] text-mutedText/30 font-black uppercase tracking-widest italic">Direct
                                                Entry</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span
                                                class="text-xs font-black text-mainText uppercase">{{ $user->bank->bank_name ?? 'NOT_SET' }}</span>
                                            <span
                                                class="text-[9px] font-black {{ ($user->bank->account_type ?? '') === 'Saving' ? 'text-primary' : 'text-secondary' }} uppercase tracking-widest mt-1">{{ $user->bank->account_type ?? 'N/A' }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span
                                            class="text-xs font-black text-mainText block">{{ $user->kyc->created_at->format('d M, Y') }}</span>
                                        <span
                                            class="text-[10px] text-mutedText font-bold uppercase">{{ $user->kyc->created_at->format('h:i A') }}</span>
                                    </td>
                                    <td class="px-8 py-6">
                                        @if($user->kyc->status === 'verified')
                                            <span
                                                class="bg-emerald-50 text-emerald-600 px-4 py-1.5 rounded-xl font-black text-[10px] uppercase tracking-widest border border-emerald-100 shadow-sm shadow-emerald-500/5">Verified</span>
                                        @else
                                            <span
                                                class="bg-primary/5 text-primary px-4 py-1.5 rounded-xl font-black text-[10px] uppercase tracking-widest border border-primary/10 shadow-sm shadow-primary/5">Pending
                                                Review</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <button
                                            @click="openKycModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ $user->mobile }}', '{{ $user->dob ? $user->dob->format('d M, Y') : 'N/A' }}', '{{ $user->kyc->pan_name }}', '{{ asset('storage/' . $user->kyc->document_path) }}', '{{ pathinfo($user->kyc->document_path, PATHINFO_EXTENSION) }}', '{{ $user->referrer ? addslashes($user->referrer->name) : 'No Sponsor' }}', '{{ $user->referrer ? $user->referrer->email : '' }}', '{{ $user->referrer ? $user->referrer->mobile : '' }}', '{{ $user->kyc->status }}', '{{ $user->bank->account_type ?? 'N/A' }}', '{{ $user->bank->bank_name ?? 'N/A' }}', '{{ $user->bank->account_number ?? 'N/A' }}', '{{ $user->bank->ifsc_code ?? 'N/A' }}', '{{ addslashes($user->bank->account_holder_name ?? 'N/A') }}', '{{ $user->kyc->document_back_path ? asset('storage/' . $user->kyc->document_back_path) : '' }}', '{{ $user->kyc->document_back_path ? pathinfo($user->kyc->document_back_path, PATHINFO_EXTENSION) : '' }}')"
                                            class="{{ $user->kyc->status === 'verified' ? 'bg-primary text-white' : 'brand-gradient text-white shadow-lg shadow-primary/20' }} px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-[2px] transition-all hover:scale-[1.05]">
                                            {{ $user->kyc->status === 'verified' ? 'View Archive' : 'Execute Review' }}
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div
                                                class="w-16 h-16 bg-navy/20 rounded-full flex items-center justify-center text-mutedText/30 mb-2">
                                                <i class="fas fa-id-card text-2xl"></i>
                                            </div>
                                            <p class="text-mutedText font-black uppercase tracking-widest text-xs">No records
                                                found matching critera.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
                @if($kycUsers->hasPages())
                    <div class="px-10 py-6 bg-navy/5 border-t border-navy/5">
                        {{ $kycUsers->appends(['kyc_status' => $kycStatus, 'activeTab' => 'kyc'])->links() }}
                    </div>
                @endif
            </div>
        </div>

        {{-- 2. BANK TAB --}}
        <div x-show="activeTab === 'bank'" x-transition:enter="transition ease-out duration-300"
            x-transition:enter-start="opacity-0 translate-y-4" class="space-y-10">
            {{-- Initial Setup --}}
            <section class="space-y-4">
                <h2 class="text-sm font-black uppercase tracking-[2px] text-primary px-2 flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-primary animate-pulse"></div>
                    Initial Infrastructure Setup
                </h2>
                <div class="bg-surface rounded-[40px] shadow-sm border border-primary/10 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-primary/5 text-[11px] uppercase font-black text-primary tracking-[2px]">
                                <tr>
                                    <th class="px-8 py-7">User Context</th>
                                    <th class="px-8 py-7 text-center">KYC Status</th>
                                    <th class="px-8 py-7">Settlement Pipeline</th>
                                    <th class="px-8 py-7">Evidence</th>
                                    <th class="px-8 py-7 text-right">Verification</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-primary/5">
                                @forelse($pendingBankInitial as $bank)
                                    <tr class="hover:bg-primary/5 transition-all group">
                                        <td class="px-8 py-6">
                                            <p class="font-black text-mainText">{{ $bank->user->name }} <span
                                                    class="text-[10px] text-mutedText/40 font-bold ml-1">#{{ $bank->user_id }}</span>
                                            </p>
                                            <p class="text-[10px] font-black text-mutedText/60 uppercase tracking-tighter">
                                                Sponsor: {{ $bank->user->referrer->name ?? 'N/A' }}</p>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            @php
                                                $kycStatus = $bank->user->kyc_status ?? 'not_submitted';
                                                $kycClass = [
                                                    'verified' => 'bg-green-50 text-green-600 border-green-200',
                                                    'pending' => 'bg-amber-50 text-amber-600 border-amber-200',
                                                    'rejected' => 'bg-red-50 text-red-600 border-red-200',
                                                    'not_submitted' => 'bg-navy text-mutedText border-primary/5'
                                                ][$kycStatus] ?? 'bg-navy text-mutedText border-primary/5';
                                            @endphp
                                            <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-tighter border shadow-sm {{ $kycClass }}">
                                                {{ str_replace('not_submitted', 'NONE', $kycStatus) }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="flex flex-col">
                                                <span
                                                    class="text-[10px] font-black text-primary uppercase bg-primary/5 px-2 py-0.5 rounded border border-primary/10 w-fit mb-1">{{ $bank->bank_name }}</span>
                                                <span
                                                    class="text-sm font-black text-mainText tracking-[2px]">{{ Str::mask($bank->account_number, '*', 4, -4) }}</span>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <a href="{{ asset('storage/' . $bank->document_path) }}" target="_blank"
                                                class="px-5 py-2 bg-navy rounded-xl text-mainText font-black text-[9px] uppercase tracking-[2px] hover:bg-primary hover:text-white transition-all shadow-sm">
                                                Inspect PDF
                                            </a>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <button
                                                @click="openBankInitialReview({{ json_encode($bank) }}, '{{ asset('storage/' . $bank->document_path) }}')"
                                                class="brand-gradient text-white px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-[2px] shadow-lg shadow-primary/20 hover:scale-[1.05] transition-all">
                                                Sync Setup
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5"
                                            class="px-8 py-20 text-center text-mutedText font-black uppercase tracking-widest text-[10px]">
                                            No pending settlement setups.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>

            {{-- Update Requests --}}
            <section class="space-y-4">
                <h2 class="text-sm font-black uppercase tracking-[2px] text-secondary px-2 flex items-center gap-3">
                    <div class="w-2 h-2 rounded-full bg-secondary animate-pulse"></div>
                    Migration & Sync Requests
                </h2>
                <div class="bg-surface rounded-[40px] shadow-sm border border-secondary/10 overflow-hidden">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left text-sm border-collapse">
                            <thead class="bg-secondary/5 text-[11px] uppercase font-black text-secondary tracking-[2px]">
                                <tr>
                                    <th class="px-8 py-7">User Context</th>
                                    <th class="px-8 py-7 text-center">KYC Status</th>
                                    <th class="px-8 py-7">Legacy → Incoming State</th>
                                    <th class="px-8 py-7 text-right">Migration Flow</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-secondary/5">
                                @forelse($pendingBankUpdates as $req)
                                    <tr class="hover:bg-secondary/5 transition-all group">
                                        <td class="px-8 py-6">
                                            <p class="font-black text-mainText">{{ $req->user->name }} <span
                                                    class="text-[10px] text-mutedText/40 font-bold ml-1">#{{ $req->user_id }}</span>
                                            </p>
                                            <p class="text-[10px] font-black text-mutedText/60 uppercase tracking-tighter">
                                                Sponsor: {{ $req->user->referrer->name ?? 'N/A' }}</p>
                                        </td>
                                        <td class="px-8 py-6 text-center">
                                            @php
                                                $kycStatus = $req->user->kyc_status ?? 'not_submitted';
                                                $kycClass = [
                                                    'verified' => 'bg-green-50 text-green-600 border-green-200',
                                                    'pending' => 'bg-amber-50 text-amber-600 border-amber-200',
                                                    'rejected' => 'bg-red-50 text-red-600 border-red-200',
                                                    'not_submitted' => 'bg-navy text-mutedText border-primary/5'
                                                ][$kycStatus] ?? 'bg-navy text-mutedText border-primary/5';
                                            @endphp
                                            <span class="px-2 py-1 rounded-lg text-[9px] font-black uppercase tracking-tighter border shadow-sm {{ $kycClass }}">
                                                {{ str_replace('not_submitted', 'NONE', $kycStatus) }}
                                            </span>
                                        </td>
                                        <td class="px-8 py-6">
                                            <div class="flex items-center gap-6">
                                                <div
                                                    class="text-[10px] opacity-30 grayscale blur-[0.3px] group-hover:blur-0 group-hover:opacity-60 transition-all">
                                                    <p class="font-black uppercase truncate w-32">
                                                        {{ $req->old_data['bank_name'] ?? 'STALE_AUTHORITY' }}</p>
                                                    <p class="tracking-[2px]">
                                                        ...{{ substr($req->old_data['account_number'] ?? '0000', -4) }}</p>
                                                </div>
                                                <div
                                                    class="text-secondary opacity-30 group-hover:opacity-100 transition-opacity">
                                                    <i class="fas fa-chevron-right text-lg"></i>
                                                </div>
                                                <div
                                                    class="p-4 bg-secondary/5 rounded-2xl border border-secondary/20 shadow-sm">
                                                    <p class="text-[9px] font-black text-secondary uppercase mb-1">Incoming</p>
                                                    <p class="text-xs font-black text-mainText uppercase truncate w-40">
                                                        {{ $req->bank_name }}</p>
                                                    <p class="text-[11px] font-black text-mainText tracking-[3px]">
                                                        {{ Str::mask($req->account_number, '*', 0, -4) }}</p>
                                                </div>
                                            </div>
                                        </td>
                                        <td class="px-8 py-6 text-right">
                                            <button
                                                @click="openBankUpdateReview({{ json_encode($req) }}, '{{ asset('storage/' . $req->document_path) }}')"
                                                class="bg-secondary text-white px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-[2px] shadow-lg shadow-secondary/20 hover:scale-[1.05] transition-all">
                                                Execute Sync
                                            </button>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4"
                                            class="px-8 py-20 text-center text-mutedText font-black uppercase tracking-widest text-[10px]">
                                            No infrastructure sync requested.</td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>

        {{-- ========================================== --}}
        {{-- ADVANCED MODALS --}}
        {{-- ========================================== --}}

        {{-- 1. KYC REALITY VIEW MODAL --}}
        <div x-show="kycModalOpen" x-cloak class="fixed inset-0 z-[100] overflow-hidden">
            <div class="fixed inset-0 bg-mainText/90 backdrop-blur-xl animate-fadeIn" @click="kycModalOpen = false"></div>
            <div class="flex min-h-screen items-center justify-center p-8 relative">
                <div @click.away="kycModalOpen = false"
                    class="bg-surface w-full max-w-[1440px] h-[90vh] rounded-[56px] flex flex-col md:flex-row overflow-hidden shadow-2xl border border-white/10 animate-scaleUp">

                    <button @click="kycModalOpen = false"
                        class="absolute top-12 right-12 z-[110] bg-white text-mainText hover:bg-secondary hover:text-white w-14 h-14 rounded-full flex items-center justify-center shadow-2xl transition-all hover:rotate-90">
                        <i class="fas fa-times text-2xl"></i>
                    </button>

                    {{-- Left View: Documentation --}}
                    <div class="w-full md:w-3/5 bg-black flex flex-col relative p-12">
                        <div class="absolute top-10 left-10 z-10 flex items-center gap-4">
                            <span
                                class="px-4 py-1.5 bg-primary rounded-xl text-[10px] font-black text-white uppercase tracking-[2px]">Verification
                                Payload</span>
                        </div>
                        
                        {{-- Front/Back Toggle tab switcher --}}
                        <div x-show="kycData.back_url" class="flex gap-2 p-1.5 bg-white/10 backdrop-blur rounded-2xl w-fit mb-6 self-center border border-white/10 z-10">
                            <button @click="previewSide = 'front'" 
                                    :class="previewSide === 'front' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-white/60 hover:text-white'" 
                                    class="px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                Front Side
                            </button>
                            <button @click="previewSide = 'back'" 
                                    :class="previewSide === 'back' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-white/60 hover:text-white'" 
                                    class="px-5 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                Back Side
                            </button>
                        </div>

                        <div
                            class="flex-1 w-full rounded-[40px] overflow-hidden bg-navy/20 border border-white/5 shadow-inner relative group">
                            <!-- Front Side View -->
                            <div x-show="previewSide === 'front'" class="w-full h-full">
                                <template x-if="kycData.ext === 'pdf'">
                                    <iframe :src="kycData.url" class="w-full h-full border-0"></iframe>
                                </template>
                                <template x-if="kycData.ext !== 'pdf'">
                                    <div class="w-full h-full flex items-center justify-center">
                                        <img :src="kycData.url"
                                            class="max-w-full max-h-full object-contain cursor-zoom-in group-hover:scale-105 transition-transform duration-700">
                                    </div>
                                </template>
                            </div>

                            <!-- Back Side View -->
                            <div x-show="previewSide === 'back'" class="w-full h-full">
                                <template x-if="kycData.back_ext === 'pdf'">
                                    <iframe :src="kycData.back_url" class="w-full h-full border-0"></iframe>
                                </template>
                                <template x-if="kycData.back_ext !== 'pdf'">
                                    <div class="w-full h-full flex items-center justify-center">
                                        <img :src="kycData.back_url"
                                            class="max-w-full max-h-full object-contain cursor-zoom-in group-hover:scale-105 transition-transform duration-700">
                                    </div>
                                </template>
                            </div>
                        </div>
                        <div
                            class="mt-8 flex justify-between items-center text-white/50 text-[10px] font-black uppercase tracking-[2px]">
                            <a :href="previewSide === 'front' ? kycData.url : kycData.back_url" target="_blank"
                                class="hover:text-primary transition-colors flex items-center gap-2">
                                <i class="fas fa-external-link-alt"></i> Open High-Fidelity Original
                            </a>
                            <span x-text="'System Hash: ' + kycData.id"></span>
                        </div>
                    </div>

                    {{-- Right View: Decision Engine --}}
                    <div class="w-full md:w-2/5 flex flex-col bg-surface border-l border-navy/5 overflow-hidden">
                        <div class="p-12 border-b border-navy/5 bg-navy/5">
                            <h3 class="text-3xl font-black text-mainText leading-none mb-3" x-text="kycData.name"></h3>
                            <div class="flex items-center gap-3">
                                <span
                                    class="text-[10px] font-black text-primary uppercase tracking-widest bg-primary/5 px-3 py-1 rounded border border-primary/10"
                                    x-text="'UID: #' + kycData.id"></span>
                                <span
                                    class="text-[10px] font-black text-mutedText uppercase tracking-widest border-l border-navy/10 pl-3"
                                    x-text="kycData.status === 'verified' ? 'Archive View' : 'Pending Authority'"></span>
                            </div>
                        </div>

                        <div class="p-12 flex-1 overflow-y-auto space-y-12">
                            {{-- System Matrix --}}
                            <div class="space-y-8">
                                <h4
                                    class="text-[11px] font-bold text-mutedText uppercase tracking-[4px] flex items-center gap-3">
                                    <div class="w-8 h-[1px] bg-mutedText/20"></div> Authority Matrix
                                </h4>
                                <div class="grid grid-cols-2 gap-y-8 gap-x-12">
                                    <div class="col-span-2">
                                        <label
                                            class="text-[10px] font-black text-mutedText/40 uppercase tracking-widest block mb-1">Official
                                            ID Name (Submitted)</label>
                                        <p class="text-3xl font-black text-emerald-600 tracking-tight leading-none uppercase"
                                            x-text="kycData.id_name"></p>
                                    </div>
                                    <div>
                                        <label
                                            class="text-[10px] font-black text-mutedText/40 uppercase tracking-widest block mb-1">Contact
                                            Protocol</label>
                                        <p class="text-xs font-black text-mainText" x-text="kycData.email"></p>
                                        <p class="text-xs font-black text-mainText mt-1" x-text="kycData.mobile"></p>
                                    </div>
                                    <div>
                                        <label
                                            class="text-[10px] font-black text-mutedText/40 uppercase tracking-widest block mb-1">Declared
                                            DOB</label>
                                        <p class="text-xs font-black text-mainText uppercase" x-text="kycData.dob"></p>
                                    </div>
                                    <div>
                                        <label
                                            class="text-[10px] font-black text-mutedText/40 uppercase tracking-widest block mb-1">Bank
                                            Name</label>
                                        <p class="text-[11px] font-black text-primary uppercase tracking-[2px]"
                                            x-text="kycData.bank_name || 'N/A'"></p>
                                    </div>
                                    <div>
                                        <label
                                            class="text-[10px] font-black text-mutedText/40 uppercase tracking-widest block mb-1">Bank
                                            Type (Account Type)</label>
                                        <p class="text-xs font-black text-secondary uppercase mt-0.5"
                                            x-text="kycData.account_type || 'N/A'"></p>
                                    </div>
                                    <div>
                                        <label
                                            class="text-[10px] font-black text-mutedText/40 uppercase tracking-widest block mb-1">Account Number</label>
                                        <p class="text-xs font-black text-mainText uppercase mt-0.5"
                                            x-text="kycData.account_number || 'N/A'"></p>
                                    </div>
                                    <div>
                                        <label
                                            class="text-[10px] font-black text-mutedText/40 uppercase tracking-widest block mb-1">IFSC Code</label>
                                        <p class="text-xs font-black text-mainText uppercase mt-0.5"
                                            x-text="kycData.ifsc_code || 'N/A'"></p>
                                    </div>
                                    <div class="col-span-2">
                                        <label
                                            class="text-[10px] font-black text-mutedText/40 uppercase tracking-widest block mb-1">Account Holder Name</label>
                                        <p class="text-xs font-black text-emerald-600 uppercase mt-0.5"
                                            x-text="kycData.account_holder_name || 'N/A'"></p>
                                    </div>
                                    <div
                                        class="col-span-2 p-8 bg-navy/5 border border-navy/10 rounded-[32px] group relative overflow-hidden">
                                        <div
                                            class="absolute -right-6 -top-6 w-20 h-20 bg-navy/10 rounded-full group-hover:scale-150 transition-transform duration-700">
                                        </div>
                                        <label
                                            class="text-[10px] font-black text-mutedText uppercase tracking-[2px] block mb-4">Sponsor
                                            Authority</label>
                                        <div class="flex items-center gap-5 relative">
                                            <div
                                                class="w-14 h-14 rounded-full bg-mainText text-white flex items-center justify-center font-black text-xl shadow-lg">
                                                {{ substr($user->referrer->name ?? 'D', 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-sm font-black text-mainText uppercase tracking-wide"
                                                    x-text="kycData.referrer_name"></p>
                                                <p class="text-[10px] font-bold text-mutedText"
                                                    x-text="kycData.referrer_email"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Final Action Terminal --}}
                        <div class="p-12 bg-navy/10 border-t border-navy/5">
                            <div x-show="!showRejectKyc" class="flex gap-6 animate-fadeIn">
                                <template x-if="kycData.status !== 'verified'">
                                    <button @click="processKyc('approve')"
                                        class="flex-1 brand-gradient text-white py-6 rounded-[28px] font-black text-[11px] uppercase tracking-[3px] shadow-2xl shadow-primary/30 hover:scale-[1.02] transition-all">
                                        Authorize KYC
                                    </button>
                                </template>
                                <button @click="showRejectKyc = true"
                                    class="flex-1 bg-surface border-2 border-secondary/20 text-secondary py-6 rounded-[28px] font-black text-[11px] uppercase tracking-[3px] hover:bg-secondary/5 transition-all">
                                    Reject Request
                                </button>
                            </div>

                            <div x-show="showRejectKyc" x-transition class="space-y-6 animate-scaleUp">
                                <template x-if="kycData.status === 'verified'">
                                    <div
                                        class="p-4 bg-secondary/5 border border-secondary/10 rounded-2xl text-[10px] font-black text-secondary uppercase tracking-widest leading-relaxed">
                                        ⚠️ Protocol Override: You are about to revoke a previously verified high-authority
                                        account.
                                    </div>
                                </template>
                                <textarea x-model="adminNote"
                                    class="w-full border-secondary/10 bg-white rounded-[28px] text-sm p-6 font-bold text-mainText shadow-inner focus:ring-secondary/20 focus:border-secondary transition-all"
                                    rows="4" placeholder="Execute professional rejection statement..."></textarea>
                                <div class="flex gap-4">
                                    <button @click="showRejectKyc = false"
                                        class="px-8 py-3 text-[10px] font-black text-mutedText uppercase tracking-widest">Back</button>
                                    <button @click="processKyc('reject')"
                                        class="flex-1 bg-secondary text-white py-6 rounded-[28px] font-black text-[11px] uppercase tracking-[3px] shadow-2xl shadow-secondary/40 hover:bg-secondary/90 transition-all">
                                        Confirm Rejection
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 2. BANK INITIAL REVIEW MODAL --}}
        <div x-show="bankInitialModalOpen" x-cloak class="fixed inset-0 z-[100] overflow-hidden">
            <div class="fixed inset-0 bg-mainText/90 backdrop-blur-xl animate-fadeIn" @click="bankInitialModalOpen = false">
            </div>
            <div class="flex min-h-screen items-center justify-center p-8 relative">
                <div @click.away="bankInitialModalOpen = false"
                    class="bg-surface w-full max-w-5xl rounded-[56px] flex flex-col md:flex-row shadow-2xl border border-white/20 animate-scaleUp overflow-hidden">
                    <button @click="bankInitialModalOpen = false"
                        class="absolute top-10 right-10 z-[110] bg-white text-mainText hover:bg-secondary hover:text-white w-12 h-12 rounded-full flex items-center justify-center shadow-2xl transition-all hover:rotate-90">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                    <div class="w-full md:w-1/2 bg-black flex items-center justify-center p-10">
                        <img :src="activeBankDoc"
                            class="max-w-full max-h-[70vh] object-contain rounded-[32px] shadow-2xl border border-white/10">
                    </div>
                    <div class="w-full md:w-1/2 p-14 flex flex-col bg-surface overflow-y-auto max-h-[85vh]">
                        <h3 class="text-3xl font-black text-mainText uppercase leading-none mb-8 tracking-tighter">
                            Settlement Setup</h3>
                        <div class="space-y-8 flex-1">
                            <div class="bg-navy/5 p-8 rounded-[32px] border border-primary/10 flex justify-between items-center">
                                <div>
                                    <label class="text-[9px] font-black text-mutedText uppercase tracking-[3px] block mb-4">Linked Identity</label>
                                    <p class="text-xl font-black text-mainText" x-text="activeInitialBankReq.user?.name"></p>
                                    <p class="text-xs font-bold text-mutedText mt-1" x-text="activeInitialBankReq.user?.email"></p>
                                </div>
                                <div class="text-right">
                                    <label class="text-[9px] font-black text-mutedText uppercase tracking-[3px] block mb-4">KYC Status</label>
                                    <span :class="{
                                        'bg-green-50 text-green-600 border-green-200': activeInitialBankReq.user?.kyc_status === 'verified',
                                        'bg-amber-50 text-amber-600 border-amber-200': activeInitialBankReq.user?.kyc_status === 'pending',
                                        'bg-red-50 text-red-600 border-red-200': activeInitialBankReq.user?.kyc_status === 'rejected',
                                        'bg-navy text-mutedText border-primary/5': !activeInitialBankReq.user?.kyc_status || activeInitialBankReq.user?.kyc_status === 'not_submitted'
                                    }" class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider border shadow-sm" x-text="activeInitialBankReq.user?.kyc_status === 'not_submitted' ? 'NONE' : (activeInitialBankReq.user?.kyc_status || 'NONE').toUpperCase()"></span>
                                </div>
                            </div>
                            <div class="space-y-6">
                                <div class="grid grid-cols-2 gap-6">
                                    <div
                                        class="p-6 bg-white rounded-3xl shadow-sm border border-navy/5 group hover:border-primary/20 transition-all">
                                        <p
                                            class="text-[9px] font-black uppercase text-mutedText tracking-widest mb-1 group-hover:text-primary transition-colors">
                                            Bank Name</p>
                                        <p class="text-2xl font-black text-mainText uppercase leading-none"
                                            x-text="activeInitialBankReq.bank_name"></p>
                                    </div>
                                    <div
                                        class="p-6 bg-white rounded-3xl shadow-sm border border-navy/5 group hover:border-primary/20 transition-all">
                                        <p
                                            class="text-[9px] font-black uppercase text-mutedText tracking-widest mb-1 group-hover:text-primary transition-colors">
                                            Account Type</p>
                                        <p class="text-xl font-black text-secondary uppercase leading-none"
                                            x-text="activeInitialBankReq.account_type || 'N/A'"></p>
                                    </div>
                                </div>
                                <div class="p-6 bg-white rounded-3xl shadow-sm border border-navy/5 group">
                                    <p
                                        class="text-[9px] font-black uppercase text-mutedText tracking-widest mb-1 group-hover:text-primary transition-colors">
                                        Holder Name (Match Test)</p>
                                    <p class="text-xl font-black text-primary uppercase leading-none"
                                        x-text="activeInitialBankReq.account_holder_name"></p>
                                </div>
                                <div class="grid grid-cols-2 gap-6">
                                    <div class="p-6 bg-white rounded-3xl shadow-sm border border-navy/5">
                                        <p class="text-[9px] font-black uppercase text-mutedText tracking-widest mb-1">
                                            Account Pattern</p>
                                        <p class="text-lg font-black text-mainText tracking-widest"
                                            x-text="activeInitialBankReq.account_number"></p>
                                    </div>
                                    <div class="p-6 bg-white rounded-3xl shadow-sm border border-navy/5">
                                        <p class="text-[9px] font-black uppercase text-mutedText tracking-widest mb-1">IFSC
                                            Vector</p>
                                        <p class="text-lg font-black text-mainText uppercase tracking-[2px]"
                                            x-text="activeInitialBankReq.ifsc_code"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-12 pt-8 border-t border-navy/5 space-y-4">
                            <div class="flex gap-4">
                                <button @click="processInitialBank('approve')"
                                    class="flex-1 brand-gradient text-white py-5 rounded-[24px] font-black text-[11px] uppercase tracking-[2px] shadow-2xl shadow-primary/20 hover:scale-[1.02] transition-all">Approve
                                    Setup</button>
                                <button @click="showBankInitialReject = true"
                                    class="flex-1 bg-surface border border-secondary text-secondary py-5 rounded-[24px] font-black text-[11px] uppercase tracking-[2px] hover:bg-secondary/5 transition-all">Reject</button>
                            </div>
                            <div x-show="showBankInitialReject" class="animate-scaleUp">
                                <textarea x-model="adminNote"
                                    class="w-full border-secondary/10 rounded-[24px] bg-white text-sm p-6 font-bold text-mainText shadow-inner"
                                    rows="3" placeholder="Rejection reasoning..."></textarea>
                                <button @click="processInitialBank('reject')"
                                    class="w-full mt-4 bg-secondary text-white py-5 rounded-[24px] font-black text-[11px] uppercase tracking-[2px] shadow-xl shadow-secondary/20">Confirm
                                    Formal Reject</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. BANK UPDATE REVIEW MODAL --}}
        <div x-show="bankUpdateModalOpen" x-cloak class="fixed inset-0 z-[100] overflow-hidden">
            <div class="fixed inset-0 bg-mainText/90 backdrop-blur-xl animate-fadeIn" @click="bankUpdateModalOpen = false">
            </div>
            <div class="flex min-h-screen items-center justify-center p-8 relative">
                <div @click.away="bankUpdateModalOpen = false"
                    class="bg-surface w-full max-w-[1200px] h-[85vh] rounded-[56px] flex flex-col md:flex-row shadow-2xl border border-white/20 animate-scaleUp overflow-hidden">
                    <button @click="bankUpdateModalOpen = false"
                        class="absolute top-10 right-10 z-[110] bg-white text-mainText hover:bg-secondary hover:text-white w-12 h-12 rounded-full flex items-center justify-center shadow-2xl transition-all hover:rotate-90">
                        <i class="fas fa-times text-xl"></i>
                    </button>
                    <div class="w-full md:w-1/2 bg-black flex items-center justify-center p-10">
                        <img :src="activeBankDoc"
                            class="max-w-full max-h-[65vh] object-contain rounded-[32px] shadow-2xl border border-white/10">
                    </div>
                    <div class="w-full md:w-1/2 p-14 flex flex-col bg-surface overflow-y-auto">
                        <h3
                            class="text-3xl font-black text-mainText uppercase leading-none mb-10 tracking-widest flex items-center gap-4">
                            <i class="fas fa-sync text-secondary"></i> Infrastructure Sync
                        </h3>
                        <div class="bg-navy/5 p-6 rounded-[28px] border border-secondary/10 flex justify-between items-center mb-8">
                            <div>
                                <label class="text-[9px] font-black text-mutedText uppercase tracking-[3px] block mb-1">User Entity</label>
                                <p class="text-lg font-black text-mainText" x-text="activeBankReq.user?.name"></p>
                                <p class="text-xs font-bold text-mutedText mt-0.5" x-text="activeBankReq.user?.email"></p>
                            </div>
                            <div class="text-right">
                                <label class="text-[9px] font-black text-mutedText uppercase tracking-[3px] block mb-2">KYC Status</label>
                                <span :class="{
                                    'bg-green-50 text-green-600 border-green-200': activeBankReq.user?.kyc_status === 'verified',
                                    'bg-amber-50 text-amber-600 border-amber-200': activeBankReq.user?.kyc_status === 'pending',
                                    'bg-red-50 text-red-600 border-red-200': activeBankReq.user?.kyc_status === 'rejected',
                                    'bg-navy text-mutedText border-primary/5': !activeBankReq.user?.kyc_status || activeBankReq.user?.kyc_status === 'not_submitted'
                                }" class="px-3 py-1.5 rounded-xl text-[10px] font-black uppercase tracking-wider border shadow-sm" x-text="activeBankReq.user?.kyc_status === 'not_submitted' ? 'NONE' : (activeBankReq.user?.kyc_status || 'NONE').toUpperCase()"></span>
                            </div>
                        </div>
                        <div class="space-y-10 flex-1">
                            <div class="grid grid-cols-2 gap-6 relative">
                                <div
                                    class="p-6 bg-navy/5 rounded-[28px] opacity-40 grayscale blur-[0.5px] hover:blur-0 hover:opacity-100 transition-all border border-navy/10">
                                    <p class="text-[9px] font-black uppercase text-mutedText tracking-[2px] mb-3">Legacy
                                        State</p>
                                    <p class="text-lg font-black text-mainText uppercase leading-none mb-1"
                                        x-text="activeBankReq.old_data?.bank_name"></p>
                                    <p class="text-xs font-bold text-mutedText tracking-[2px]"
                                        x-text="'...' + activeBankReq.old_data?.account_number.slice(-4)"></p>
                                    <p class="text-[10px] font-black text-secondary uppercase mt-1"
                                        x-text="activeBankReq.old_data?.account_type || 'N/A'"></p>
                                </div>
                                <div
                                    class="p-8 bg-secondary/5 rounded-[32px] border-2 border-secondary/10 shadow-2xl shadow-secondary/5">
                                    <p class="text-[10px] font-black uppercase text-secondary tracking-[3px] mb-4">Incoming
                                        Sync</p>
                                    <p class="text-2xl font-black text-secondary uppercase leading-none mb-2"
                                        x-text="activeBankReq.bank_name"></p>
                                    <p class="text-md font-black text-mainText tracking-[3px]"
                                        x-text="activeBankReq.account_number"></p>
                                    <p class="text-[10px] font-black text-secondary uppercase mt-1"
                                        x-text="activeBankReq.account_type || 'N/A'"></p>
                                </div>
                            </div>
                            <div class="space-y-6">
                                <div class="group">
                                    <p
                                        class="text-[10px] font-black uppercase text-mutedText tracking-widest mb-1 group-hover:text-secondary transition-colors">
                                        Proposed Holder Authority</p>
                                    <p class="text-xl font-black text-mainText p-5 bg-navy/5 rounded-[24px] uppercase border border-navy/5"
                                        x-text="activeBankReq.account_holder_name"></p>
                                </div>
                                <div class="grid grid-cols-1 gap-6">
                                    <div>
                                        <p class="text-[10px] font-black uppercase text-mutedText tracking-widest mb-1">
                                            Proposed IFSC Vector</p>
                                        <p class="text-xl font-black text-mainText p-5 bg-navy/5 rounded-[24px] uppercase border border-navy/5 tracking-[2px]"
                                            x-text="activeBankReq.ifsc_code"></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mt-12 pt-8 border-t border-navy/5 space-y-4">
                            <div class="flex gap-4">
                                <button @click="processBankUpdate('approve')"
                                    class="flex-1 bg-secondary text-white py-6 rounded-[28px] font-black text-[11px] uppercase tracking-[3px] shadow-2xl shadow-secondary/30 hover:scale-[1.02] transition-all">Authorize
                                    Migration</button>
                                <button @click="showBankUpdateReject = true"
                                    class="flex-1 bg-surface border-2 border-secondary/20 text-secondary py-6 rounded-[28px] font-black text-[11px] uppercase tracking-[3px] hover:bg-secondary/5 transition-all">Reject
                                    Sync</button>
                            </div>
                            <div x-show="showBankUpdateReject" class="animate-scaleUp">
                                <textarea x-model="adminNote"
                                    class="w-full border-secondary/10 bg-white rounded-[28px] text-sm p-8 font-bold text-mainText shadow-inner"
                                    rows="3" placeholder="State protocol sync rejection reason..."></textarea>
                                <button @click="processBankUpdate('reject')"
                                    class="w-full mt-4 bg-secondary text-white py-6 rounded-[28px] font-black text-[11px] uppercase tracking-[3px] shadow-2xl shadow-secondary/40">Execute
                                    Sync Rejection</button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function verificationManager() {
            return {
                activeTab: new URLSearchParams(window.location.search).get('activeTab') || 'kyc',
                kycModalOpen: false,
                bankUpdateModalOpen: false,
                bankInitialModalOpen: false,
                showRejectKyc: false,
                showBankUpdateReject: false,
                showBankInitialReject: false,

                adminNote: '',
                kycData: {},
                previewSide: 'front',
                activeBankReq: {},
                activeInitialBankReq: {},
                activeBankDoc: '',

                openKycModal(id, name, email, mobile, dob, id_name, url, ext, r_name, r_email, r_mobile, status, acc_type, b_name, acc_num, ifsc, holder, back_url, back_ext) {
                    this.kycData = { 
                        id, name, email, mobile, dob, id_name, url, ext, 
                        referrer_name: r_name, referrer_email: r_email, referrer_mobile: r_mobile, 
                        status, account_type: acc_type, bank_name: b_name,
                        account_number: acc_num, ifsc_code: ifsc, account_holder_name: holder,
                        back_url, back_ext 
                    };
                    this.adminNote = '';
                    this.showRejectKyc = false;
                    this.kycModalOpen = true;
                    this.previewSide = 'front';
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
                    if (action === 'reject' && !this.adminNote.trim()) {
                        return Swal.fire({
                            title: 'Note Missing',
                            text: 'Rejection requires a documented reason.',
                            icon: 'warning',
                            borderRadius: '24px'
                        });
                    }

                    const url = action === 'approve'
                        ? `{{ route('admin.verifications.kyc.approve', ':id') }}`.replace(':id', this.kycData.id)
                        : `{{ route('admin.verifications.kyc.reject', ':id') }}`.replace(':id', this.kycData.id);

                    Swal.fire({
                        title: `<span class="font-black text-mainText">EXECUTE KYC ${action.toUpperCase()}?</span>`,
                        text: `Are you certain you want to proceed with this identity decision?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Authorize Now',
                        confirmButtonColor: action === 'reject' ? '#e11d48' : '#F7941D',
                        borderRadius: '24px'
                    }).then(res => {
                        if (res.isConfirmed) {
                            axios.post(url, { admin_note: this.adminNote })
                                .then(() => Swal.fire({ title: 'Decision Recorded', text: 'System state updated successfully.', icon: 'success', borderRadius: '24px' }).then(() => location.reload()))
                                .catch(() => Swal.fire({ title: 'Protocol Failure', text: 'Failed to transmit decision.', icon: 'error', borderRadius: '24px' }));
                        }
                    });
                },

                processBankUpdate(action) {
                    if (action === 'reject' && !this.adminNote.trim()) {
                        return Swal.fire({ title: 'Note Missing', text: 'Sync rejection requires a formal note.', icon: 'warning', borderRadius: '24px' });
                    }

                    const url = `{{ route('admin.verifications.bank.process-update', ':id') }}`.replace(':id', this.activeBankReq.id);
                    this.executeSubmission(url, action);
                },

                processInitialBank(action) {
                    if (action === 'reject' && !this.adminNote.trim()) {
                        return Swal.fire({ title: 'Note Missing', text: 'Infrastructure rejection requires a formal note.', icon: 'warning', borderRadius: '24px' });
                    }

                    const url = `{{ route('admin.verifications.bank.verify-initial', ':id') }}`.replace(':id', this.activeInitialBankReq.id);
                    this.executeSubmission(url, action);
                },

                executeSubmission(url, action) {
                    Swal.fire({
                        title: `<span class="font-black text-mainText">EXECUTE ${action.toUpperCase()}?</span>`,
                        text: `This action will modify high-authority settlement pipelines.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: action === 'approve' ? '#F7941D' : '#e11d48',
                        borderRadius: '24px'
                    }).then(res => {
                        if (res.isConfirmed) {
                            const form = document.createElement('form');
                            form.method = 'POST';
                            form.action = url;

                            const inputs = {
                                '_token': '{{ csrf_token() }}',
                                'action': action,
                                'admin_note': this.adminNote
                            };

                            for (const [name, value] of Object.entries(inputs)) {
                                const input = document.createElement('input');
                                input.type = 'hidden';
                                input.name = name;
                                input.value = value;
                                form.appendChild(input);
                            }

                            document.body.appendChild(form);
                            form.submit();
                        }
                    });
                }
            }
        }
    </script>

    <style>
        [x-cloak] {
            display: none !important;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(10px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        @keyframes scaleUp {
            from {
                opacity: 0;
                transform: scale(0.96) translateY(30px);
            }

            to {
                opacity: 1;
                transform: scale(1) translateY(0);
            }
        }

        .animate-fadeIn {
            animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        .animate-scaleUp {
            animation: scaleUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        ::-webkit-scrollbar {
            width: 5px;
            height: 5px;
        }

        ::-webkit-scrollbar-thumb {
            background: #F7941D;
            border-radius: 10px;
        }
    </style>
@endsection