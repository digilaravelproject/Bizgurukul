@extends('layouts.admin')

@section('title', 'Secret Manual Onboarding')

@section('content')
<div x-data="manualOnboarding()" class="max-w-5xl mx-auto space-y-10 font-sans text-mainText pb-24 pt-4 px-4 md:px-0">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div class="flex items-center gap-5">
            <div class="w-14 h-14 bg-gradient-to-br from-primary/20 to-secondary/20 rounded-2xl flex items-center justify-center text-primary text-2xl shadow-inner">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <h1 class="text-3xl md:text-4xl font-black tracking-tight text-mainText uppercase italic">Manual Onboarding</h1>
                <p class="text-mutedText text-[10px] md:text-xs font-bold uppercase tracking-[0.3em]">Secret Admin Sync Tool • Level 4 Access</p>
            </div>
        </div>
        <div class="flex items-center gap-3 bg-surface p-2 rounded-2xl border border-primary/10 shadow-sm">
            <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
            <span class="text-[10px] font-black uppercase tracking-widest text-mutedText">Live Sync Active</span>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-600 px-6 py-5 rounded-[1.5rem] font-bold flex items-center gap-4 animate-fade-in shadow-lg">
            <div class="w-10 h-10 bg-green-500 rounded-full flex items-center justify-center text-white shrink-0">
                <i class="fas fa-check text-lg"></i>
            </div>
            <span class="text-sm md:text-base">{{ session('success') }}</span>
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/20 text-red-600 px-6 py-5 rounded-[1.5rem] font-bold flex items-center gap-4 shadow-lg">
            <div class="w-10 h-10 bg-red-500 rounded-full flex items-center justify-center text-white shrink-0">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <span class="text-sm md:text-base">{{ session('error') }}</span>
        </div>
    @endif

    <form action="{{ route('admin.secret-onboarding.store') }}" method="POST" class="space-y-10">
        @csrf
        <input type="hidden" name="mode" x-model="mode">

        {{-- Section: Onboarding Mode --}}
        <div class="bg-surface rounded-[2.5rem] shadow-2xl border border-primary/5 transition-all overflow-visible">
            <div class="bg-navy p-6 md:p-8 border-b border-primary/5 flex flex-col md:flex-row justify-between items-center gap-4 rounded-t-[2.5rem]">
                <div class="flex flex-col">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.25em] text-primary">Step 01</h3>
                    <h2 class="text-lg font-black text-white uppercase italic">Onboarding Mode</h2>
                </div>
                <div class="flex bg-navy/60 p-1.5 rounded-2xl border border-primary/10 w-full md:w-auto">
                    <button type="button" @click="mode = 'lead'" :class="mode === 'lead' ? 'bg-primary text-white shadow-[0_8px_20px_-5px_rgba(var(--primary-rgb),0.5)]' : 'text-mutedText hover:text-mainText'" class="flex-1 md:flex-none px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                        <i class="fas fa-database mr-2"></i> Select Lead
                    </button>
                    <button type="button" @click="mode = 'manual'" :class="mode === 'manual' ? 'bg-primary text-white shadow-[0_8px_20px_-5px_rgba(var(--primary-rgb),0.5)]' : 'text-mutedText hover:text-mainText'" class="flex-1 md:flex-none px-6 py-3 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                        <i class="fas fa-plus mr-2"></i> New User
                    </button>
                </div>
            </div>
            
            <div class="p-8 md:p-10">
                <template x-if="mode === 'lead'">
                    <div class="space-y-6">
                        <div class="flex items-center justify-between">
                            <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Search Database Leads</label>
                            <span x-show="isLoading" class="text-[10px] font-bold text-primary animate-pulse italic">Scanning Database...</span>
                        </div>
                        <div class="relative">
                            <div class="absolute inset-y-0 left-5 flex items-center text-mutedText/40">
                                <i class="fas fa-search"></i>
                            </div>
                            <input type="text" x-model="searchQuery" @input.debounce.300ms="fetchLeads()" placeholder="Name, Email, or Phone..."
                                class="w-full pl-12 pr-5 py-5 bg-navy/20 border border-primary/10 rounded-[1.5rem] text-mainText font-bold focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none placeholder:text-mutedText/30 placeholder:font-medium">
                            
                            {{-- Lead Dropdown --}}
                            <div x-show="leads.length > 0" @click.away="leads = []" class="absolute z-[200] w-full mt-3 bg-surface border border-primary/10 rounded-[1.5rem] shadow-[0_30px_60px_-12px_rgba(0,0,0,0.5)] max-h-72 overflow-y-auto animate-fade-in divide-y divide-primary/5">
                                <template x-for="lead in leads" :key="lead.id">
                                    <button type="button" @click="selectLead(lead)" class="w-full text-left px-6 py-5 hover:bg-primary/10 transition-all flex items-center justify-between group">
                                        <div class="flex flex-col gap-1">
                                            <span class="text-sm font-black text-mainText group-hover:text-primary transition-colors" x-text="lead.name"></span>
                                            <div class="flex items-center gap-3">
                                                <span class="text-[10px] font-bold text-mutedText uppercase tracking-widest" x-text="lead.email"></span>
                                                <span class="w-1 h-1 bg-primary/20 rounded-full"></span>
                                                <span class="text-[10px] font-bold text-mutedText uppercase tracking-widest" x-text="lead.mobile"></span>
                                            </div>
                                        </div>
                                        <i class="fas fa-chevron-right text-mutedText group-hover:text-primary group-hover:translate-x-1 transition-all text-xs"></i>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <input type="hidden" name="lead_id" x-model="selectedLeadId">
                        
                        <div x-show="selectedLeadId" class="bg-gradient-to-r from-primary/10 to-transparent border border-primary/20 p-5 rounded-[1.5rem] flex items-center justify-between animate-fade-in shadow-inner">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 bg-primary rounded-2xl flex items-center justify-center text-white shadow-lg">
                                    <i class="fas fa-check-double text-lg"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-primary/70">Lead Locked</p>
                                    <p class="text-base font-black text-mainText" x-text="selectedLeadName"></p>
                                </div>
                            </div>
                            <button type="button" @click="clearLead()" class="bg-white/5 hover:bg-red-500/10 text-red-500 px-5 py-2.5 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all">
                                <i class="fas fa-times mr-2"></i> Deselect
                            </button>
                        </div>
                    </div>
                </template>

                <template x-if="mode === 'manual'">
                    <div class="p-10 bg-navy/20 rounded-[2rem] border-2 border-dashed border-primary/20 text-center flex flex-col items-center gap-4">
                        <div class="w-12 h-12 bg-primary/10 rounded-full flex items-center justify-center text-primary">
                            <i class="fas fa-keyboard text-xl"></i>
                        </div>
                        <p class="text-xs font-black text-mutedText uppercase tracking-[0.2em]">Manual Entry Mode: System will generate a fresh Lead entry</p>
                    </div>
                </template>
            </div>
        </div>

        {{-- Section: Account Information --}}
        <div class="bg-surface rounded-[2.5rem] shadow-2xl border border-primary/5 transition-all overflow-hidden">
            <div class="bg-navy p-6 md:p-8 border-b border-primary/5 rounded-t-[2.5rem]">
                <h3 class="text-[10px] font-black uppercase tracking-[0.25em] text-primary">Step 02</h3>
                <h2 class="text-lg font-black text-white uppercase italic">Account Information</h2>
            </div>
            
            <div class="p-8 md:p-10 grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Name --}}
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1 flex items-center gap-2">
                        <i class="fas fa-user-circle text-primary/50"></i> Full Name
                    </label>
                    <input type="text" name="name" x-model="formData.name" required
                        class="w-full px-6 py-5 bg-navy/20 border border-primary/10 rounded-[1.5rem] text-mainText font-bold focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none"
                        placeholder="e.g. John Doe">
                </div>

                {{-- Email --}}
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1 flex items-center gap-2">
                        <i class="fas fa-envelope text-primary/50"></i> Email Address
                    </label>
                    <input type="email" name="email" x-model="formData.email" required
                        class="w-full px-6 py-5 bg-navy/20 border border-primary/10 rounded-[1.5rem] text-mainText font-bold focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none"
                        placeholder="john@example.com">
                </div>

                {{-- Mobile --}}
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1 flex items-center gap-2">
                        <i class="fas fa-phone-alt text-primary/50"></i> Mobile Number
                    </label>
                    <input type="text" name="mobile" x-model="formData.mobile" required maxlength="10"
                        class="w-full px-6 py-5 bg-navy/20 border border-primary/10 rounded-[1.5rem] text-mainText font-bold focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none"
                        placeholder="9876543210">
                </div>

                {{-- Password --}}
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1 flex items-center gap-2">
                        <i class="fas fa-lock text-primary/50"></i> Account Password
                    </label>
                    <input type="text" name="password" x-model="formData.password" required
                        class="w-full px-6 py-5 bg-navy/20 border border-primary/10 rounded-[1.5rem] text-mainText font-bold focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none">
                </div>

                {{-- Gender --}}
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1 flex items-center gap-2">
                        <i class="fas fa-venus-mars text-primary/50"></i> Gender
                    </label>
                    <div class="relative">
                        <select name="gender" x-model="formData.gender" required
                            class="w-full px-6 py-5 bg-navy/20 border border-primary/10 rounded-[1.5rem] text-mainText font-bold focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none appearance-none">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-mutedText/40">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                {{-- State --}}
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-primary/50"></i> State / Region
                    </label>
                    <div class="relative">
                        <select name="state_id" x-model="formData.state_id" required
                            class="w-full px-6 py-5 bg-navy/20 border border-primary/10 rounded-[1.5rem] text-mainText font-bold focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none appearance-none">
                            <option value="">Select State</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-mutedText/40">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section: Product & Referral --}}
        <div class="bg-surface rounded-[2.5rem] shadow-2xl border border-primary/5 transition-all overflow-hidden">
            <div class="bg-navy p-6 md:p-8 border-b border-primary/5 rounded-t-[2.5rem]">
                <h3 class="text-[10px] font-black uppercase tracking-[0.25em] text-primary">Step 03</h3>
                <h2 class="text-lg font-black text-white uppercase italic">Product & Sponsorship</h2>
            </div>
            
            <div class="p-8 md:p-10 grid grid-cols-1 md:grid-cols-2 gap-8">
                {{-- Bundle --}}
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1 flex items-center gap-2">
                        <i class="fas fa-box-open text-primary/50"></i> Select Bundle
                    </label>
                    <div class="relative">
                        <select name="bundle_id" x-model="formData.bundle_id" required
                            class="w-full px-6 py-5 bg-navy/20 border border-primary/10 rounded-[1.5rem] text-mainText font-bold focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none appearance-none">
                            <option value="">Choose Bundle...</option>
                            @foreach($bundles as $bundle)
                                <option value="{{ $bundle->id }}">
                                    {{ $bundle->title }} (₹{{ $bundle->affiliate_price }})
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-mutedText/40">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                {{-- Referral Code --}}
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1 flex items-center gap-2">
                        <i class="fas fa-user-friends text-primary/50"></i> Referral Code (Sponsor)
                    </label>
                    <div class="relative">
                        <input type="text" name="referral_code" x-model="formData.referral_code"
                            class="w-full px-6 py-5 bg-navy/20 border border-primary/10 rounded-[1.5rem] text-mainText font-bold focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none"
                            placeholder="Enter referral code">
                        <div x-show="sponsorName" class="mt-3 bg-primary/10 p-3 rounded-xl border border-primary/20 text-[10px] font-black uppercase text-primary tracking-widest flex items-center gap-2 animate-fade-in">
                            <i class="fas fa-check-circle text-xs"></i> Sponsor Found: <span x-text="sponsorName" class="text-mainText"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section: Payment Metadata --}}
        <div class="bg-surface rounded-[2.5rem] shadow-2xl border border-primary/5 transition-all overflow-hidden">
            <div class="bg-navy p-6 md:p-8 border-b border-primary/5 rounded-t-[2.5rem]">
                <h3 class="text-[10px] font-black uppercase tracking-[0.25em] text-primary">Step 04</h3>
                <h2 class="text-lg font-black text-white uppercase italic">Payment Synchronization</h2>
            </div>
            
            <div class="p-8 md:p-10 space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Payment Method --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1 flex items-center gap-2">
                            <i class="fas fa-credit-card text-primary/50"></i> Payment Gateway
                        </label>
                        <div class="relative">
                            <select name="payment_method" x-model="formData.payment_method" required
                                class="w-full px-6 py-5 bg-navy/20 border border-primary/10 rounded-[1.5rem] text-mainText font-bold focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none appearance-none">
                                <option value="razorpay">Razorpay (Production Parity)</option>
                                <option value="cashfree">Cashfree (Production Parity)</option>
                                <option value="manual_admin">Offline / RTGS / Manual</option>
                            </select>
                            <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-mutedText/40">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Amount --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1 flex items-center gap-2">
                            <i class="fas fa-rupee-sign text-primary/50"></i> Received Amount
                        </label>
                        <div class="relative">
                            <input type="number" name="amount" x-model="formData.amount" required step="0.01"
                                class="w-full px-6 py-5 bg-navy/20 border border-primary/10 rounded-[1.5rem] text-mainText font-bold focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none"
                                placeholder="0.00">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    {{-- Payment ID --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1 flex items-center gap-2">
                            <i class="fas fa-fingerprint text-primary/50"></i> Gateway Payment ID
                        </label>
                        <input type="text" name="gateway_payment_id" x-model="formData.gateway_payment_id" required
                            class="w-full px-6 py-5 bg-navy/20 border border-primary/10 rounded-[1.5rem] text-mainText font-bold focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none"
                            placeholder="pay_123abc or UTR...">
                    </div>

                    {{-- Order ID --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1 flex items-center gap-2">
                            <i class="fas fa-hashtag text-primary/50"></i> Gateway Order ID
                        </label>
                        <input type="text" name="gateway_order_id" x-model="formData.gateway_order_id"
                            class="w-full px-6 py-5 bg-navy/20 border border-primary/10 rounded-[1.5rem] text-mainText font-bold focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none"
                            placeholder="order_XYZ123">
                    </div>

                    {{-- UTR --}}
                    <div class="space-y-3">
                        <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1 flex items-center gap-2">
                            <i class="fas fa-barcode text-primary/50"></i> UTR Number
                        </label>
                        <input type="text" name="utr_number" x-model="formData.utr_number"
                            class="w-full px-6 py-5 bg-navy/20 border border-primary/10 rounded-[1.5rem] text-mainText font-bold focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none"
                            placeholder="UTR-XXXXX">
                    </div>
                </div>

                {{-- Date --}}
                <div class="space-y-3">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-primary/50"></i> Transaction Date
                    </label>
                    <input type="date" name="payment_date" x-model="formData.payment_date" required
                        class="w-full px-6 py-5 bg-navy/20 border border-primary/10 rounded-[1.5rem] text-mainText font-bold focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none">
                </div>
            </div>
        </div>

        <div class="pt-10">
            <button type="submit" 
                class="w-full bg-gradient-to-r from-primary via-secondary to-primary bg-[length:200%_auto] animate-shimmer text-white font-black py-6 px-10 rounded-[2rem] shadow-[0_20px_50px_-15px_rgba(var(--primary-rgb),0.5)] hover:shadow-[0_25px_60px_-10px_rgba(var(--primary-rgb),0.6)] hover:-translate-y-1.5 transition-all active:scale-[0.97] uppercase tracking-[0.2em] flex items-center justify-center gap-4 text-sm md:text-base">
                <i class="fas fa-sync-alt text-xl"></i>
                Finalize Onboarding & Sync
            </button>
            <div class="mt-8 flex items-center justify-center gap-6 opacity-40">
                <div class="h-[1px] flex-1 bg-gradient-to-r from-transparent to-mutedText"></div>
                <p class="text-[10px] font-black uppercase tracking-[0.3em] whitespace-nowrap">Immutable Production Parity</p>
                <div class="h-[1px] flex-1 bg-gradient-to-l from-transparent to-mutedText"></div>
            </div>
        </div>
    </form>
</div>

<script>
function manualOnboarding() {
    return {
        mode: 'lead',
        searchQuery: '',
        leads: [],
        selectedLeadId: null,
        selectedLeadName: '',
        sponsorName: '',
        isLoading: false,
        formData: {
            name: '',
            email: '',
            mobile: '',
            password: '{{ Str::random(12) }}',
            gender: 'male',
            state_id: '',
            referral_code: '',
            bundle_id: '',
            payment_method: 'manual_admin',
            gateway_payment_id: '',
            gateway_order_id: '',
            utr_number: '',
            amount: '',
            payment_date: '{{ date("Y-m-d") }}'
        },

        fetchLeads() {
            if (this.searchQuery.length < 3) {
                this.leads = [];
                return;
            }
            this.isLoading = true;
            fetch(`{{ route('admin.api.leads') }}?q=${this.searchQuery}`)
                .then(res => res.json())
                .then(data => {
                    this.leads = data;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        selectLead(lead) {
            this.selectedLeadId = lead.id;
            this.selectedLeadName = lead.name;
            this.leads = [];
            this.searchQuery = '';
            this.isLoading = true;
            
            fetch(`{{ url('admin/api/leads') }}/${lead.id}`)
                .then(res => res.json())
                .then(data => {
                    const leadData = data.lead;
                    this.formData.name = leadData.name;
                    this.formData.email = leadData.email;
                    this.formData.mobile = leadData.mobile;
                    this.formData.gender = leadData.gender || 'male';
                    this.formData.state_id = leadData.state_id;
                    this.formData.referral_code = leadData.referral_code;
                    this.formData.bundle_id = data.bundle_id;
                    this.sponsorName = data.sponsor_name;
                })
                .finally(() => {
                    this.isLoading = false;
                });
        },

        clearLead() {
            this.selectedLeadId = null;
            this.selectedLeadName = '';
            this.sponsorName = '';
            this.formData.name = '';
            this.formData.email = '';
            this.formData.mobile = '';
            this.formData.referral_code = '';
            this.formData.bundle_id = '';
        }
    }
}
</script>

<style>
    .animate-spin-slow {
        animation: spin 3s linear infinite;
    }
    .animate-shimmer {
        animation: shimmer 5s linear infinite;
    }
    @keyframes shimmer {
        0% { background-position: 0% 50%; }
        100% { background-position: 200% 50%; }
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .animate-fade-in {
        animation: fadeIn 0.4s cubic-bezier(0.16, 1, 0.3, 1);
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(15px); }
        to { opacity: 1; transform: translateY(0); }
    }
    [x-cloak] { display: none !important; }
</style>
@endsection
