@extends('layouts.admin')

@section('title', 'Secret Manual Onboarding')

@section('content')
<div x-data="manualOnboarding()" class="max-w-4xl mx-auto space-y-8 font-sans text-mainText pb-20">
    {{-- Header --}}
    <div class="flex flex-col gap-2">
        <div class="flex items-center gap-4">
            <div class="w-12 h-12 bg-primary/10 rounded-2xl flex items-center justify-center text-primary text-xl">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <h1 class="text-3xl font-black tracking-tight text-mainText uppercase italic">Manual Onboarding & Sync</h1>
                <p class="text-mutedText text-sm font-bold uppercase tracking-widest">Secret Admin Tool for Offline Payment Synchronization</p>
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-600 px-6 py-4 rounded-2xl font-bold flex items-center gap-3 animate-fade-in">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="bg-red-500/10 border border-red-500/20 text-red-600 px-6 py-4 rounded-2xl font-bold">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-600 px-6 py-4 rounded-2xl font-bold">
            <ul class="list-disc list-inside text-sm">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.secret-onboarding.store') }}" method="POST" class="space-y-8">
        @csrf
        <input type="hidden" name="mode" x-model="mode">

        {{-- Section: Onboarding Mode --}}
        <div class="bg-surface rounded-[2rem] shadow-xl border border-primary/10 transition-all">
            <div class="bg-navy p-6 border-b border-primary/5 flex justify-between items-center rounded-t-[2rem]">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-primary">01. Onboarding Mode</h3>
                <div class="flex bg-navy/40 p-1 rounded-xl border border-primary/10">
                    <button type="button" @click="mode = 'lead'" :class="mode === 'lead' ? 'bg-primary text-white shadow-lg' : 'text-mutedText hover:text-mainText'" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">Select Lead</button>
                    <button type="button" @click="mode = 'manual'" :class="mode === 'manual' ? 'bg-primary text-white shadow-lg' : 'text-mutedText hover:text-mainText'" class="px-4 py-2 rounded-lg text-[10px] font-black uppercase tracking-widest transition-all">New User</button>
                </div>
            </div>
            
            <div class="p-8">
                <template x-if="mode === 'lead'">
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Search Existing Lead</label>
                        <div class="relative">
                            <input type="text" x-model="searchQuery" @input.debounce.300ms="fetchLeads()" placeholder="Search by Name, Email, or Mobile..."
                                class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none">
                            
                            {{-- Fixed Dropdown: Added z-index and removed parent overflow-hidden --}}
                            <div x-show="leads.length > 0" @click.away="leads = []" class="absolute z-[100] w-full mt-2 bg-surface border border-primary/10 rounded-2xl shadow-2xl max-h-60 overflow-y-auto">
                                <template x-for="lead in leads" :key="lead.id">
                                    <button type="button" @click="selectLead(lead)" class="w-full text-left px-5 py-4 hover:bg-primary/10 border-b border-primary/5 last:border-0 transition-colors flex flex-col">
                                        <span class="text-sm font-black text-mainText" x-text="lead.name"></span>
                                        <span class="text-[10px] font-bold text-mutedText uppercase tracking-widest" x-text="lead.email + ' • ' + lead.mobile"></span>
                                    </button>
                                </template>
                            </div>
                        </div>
                        <input type="hidden" name="lead_id" x-model="selectedLeadId">
                        
                        <div x-show="selectedLeadId" class="bg-primary/5 border border-primary/10 p-4 rounded-2xl flex items-center justify-between animate-fade-in">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 bg-primary/20 rounded-full flex items-center justify-center text-primary">
                                    <i class="fas fa-check-circle"></i>
                                </div>
                                <div>
                                    <p class="text-[10px] font-black uppercase tracking-widest text-primary">Selected Lead</p>
                                    <p class="text-sm font-bold text-mainText" x-text="selectedLeadName"></p>
                                </div>
                            </div>
                            <button type="button" @click="clearLead()" class="text-[10px] font-black uppercase text-red-500 hover:text-red-600 tracking-widest">Remove</button>
                        </div>
                    </div>
                </template>

                <template x-if="mode === 'manual'">
                    <div class="p-4 bg-navy/20 rounded-2xl border border-dashed border-primary/20 text-center">
                        <p class="text-xs font-bold text-mutedText uppercase tracking-widest">Manual Entry Mode: Fill all details below</p>
                    </div>
                </template>
            </div>
        </div>

        {{-- Section: Account Information --}}
        <div class="bg-surface rounded-[2rem] shadow-xl border border-primary/10">
            <div class="bg-navy p-6 border-b border-primary/5 rounded-t-[2rem]">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-primary">02. Account Information</h3>
            </div>
            
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Name --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Full Name</label>
                    <input type="text" name="name" x-model="formData.name" required
                        class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                        placeholder="e.g. John Doe">
                </div>

                {{-- Email --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Email Address</label>
                    <input type="email" name="email" x-model="formData.email" required
                        class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                        placeholder="john@example.com">
                </div>

                {{-- Mobile --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Mobile Number</label>
                    <input type="text" name="mobile" x-model="formData.mobile" required maxlength="10"
                        class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                        placeholder="9876543210">
                </div>

                {{-- Password --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Account Password</label>
                    <input type="text" name="password" x-model="formData.password" required
                        class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none">
                </div>

                {{-- Gender --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Gender</label>
                    <select name="gender" x-model="formData.gender" required
                        class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none appearance-none">
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="other">Other</option>
                    </select>
                </div>

                {{-- State --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">State / Region</label>
                    <select name="state_id" x-model="formData.state_id" required
                        class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none appearance-none">
                        <option value="">Select State</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}">{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Section: Product & Referral --}}
        <div class="bg-surface rounded-[2rem] shadow-xl border border-primary/10">
            <div class="bg-navy p-6 border-b border-primary/5 rounded-t-[2rem]">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-primary">03. Product & Sponsorship</h3>
            </div>
            
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Bundle --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Select Bundle</label>
                    <select name="bundle_id" x-model="formData.bundle_id" required
                        class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none appearance-none">
                        <option value="">Choose Bundle...</option>
                        @foreach($bundles as $bundle)
                            <option value="{{ $bundle->id }}">
                                {{ $bundle->title }} (₹{{ $bundle->affiliate_price }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Referral Code --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Referral Code (Sponsor)</label>
                    <div class="relative">
                        <input type="text" name="referral_code" x-model="formData.referral_code"
                            class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                            placeholder="Enter referral code">
                        <div x-show="sponsorName" class="mt-2 text-[10px] font-black uppercase text-primary tracking-widest flex items-center gap-1">
                            <i class="fas fa-user-check"></i> Sponsor: <span x-text="sponsorName"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section: Payment Metadata --}}
        <div class="bg-surface rounded-[2rem] shadow-xl border border-primary/10">
            <div class="bg-navy p-6 border-b border-primary/5 rounded-t-[2rem]">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-primary">04. Payment Synchronization</h3>
            </div>
            
            <div class="p-8 space-y-6">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Payment Method --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Payment Gateway</label>
                        <select name="payment_method" x-model="formData.payment_method" required
                            class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none appearance-none">
                            <option value="razorpay">Razorpay (Production Parity)</option>
                            <option value="cashfree">Cashfree (Production Parity)</option>
                            <option value="manual_admin">Offline / RTGS / Manual</option>
                        </select>
                    </div>

                    {{-- Amount --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Received Amount (₹)</label>
                        <input type="number" name="amount" x-model="formData.amount" required step="0.01"
                            class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                            placeholder="0.00">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    {{-- Payment ID --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Gateway Payment ID</label>
                        <input type="text" name="gateway_payment_id" x-model="formData.gateway_payment_id" required
                            class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                            placeholder="pay_123abc or UTR...">
                    </div>

                    {{-- Order ID --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Gateway Order ID (Optional)</label>
                        <input type="text" name="gateway_order_id" x-model="formData.gateway_order_id"
                            class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                            placeholder="order_XYZ123">
                    </div>

                    {{-- UTR --}}
                    <div class="space-y-2">
                        <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">UTR Number (Optional)</label>
                        <input type="text" name="utr_number" x-model="formData.utr_number"
                            class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                            placeholder="UTR-XXXXX">
                    </div>
                </div>

                {{-- Date --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Payment Date</label>
                    <input type="date" name="payment_date" x-model="formData.payment_date" required
                        class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none">
                </div>
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" 
                class="w-full bg-gradient-to-r from-primary to-secondary text-white font-black py-5 px-8 rounded-3xl shadow-2xl hover:shadow-primary/40 hover:-translate-y-1 transition-all active:scale-[0.98] uppercase tracking-widest flex items-center justify-center gap-3">
                <i class="fas fa-sync-alt animate-spin-slow"></i>
                Synchronize & Process Onboarding
            </button>
            <p class="text-center mt-6 text-[10px] text-mutedText font-bold uppercase tracking-widest opacity-50">
                Note: This will replicate the full production flow (Invoices, Commissions, Roles, Emails)
            </p>
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
        formData: {
            name: '',
            email: '',
            mobile: '',
            password: '{{ Str::random(10) }}',
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
            fetch(`{{ route('admin.api.leads') }}?q=${this.searchQuery}`)
                .then(res => res.json())
                .then(data => {
                    this.leads = data;
                });
        },

        selectLead(lead) {
            this.selectedLeadId = lead.id;
            this.selectedLeadName = lead.name;
            this.leads = [];
            this.searchQuery = '';
            
            // Fixed nested data structure access
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
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
    .animate-fade-in {
        animation: fadeIn 0.4s ease-out;
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
</style>
@endsection
