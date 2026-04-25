@extends('layouts.admin')

@section('title', 'Secret Manual Onboarding')

@section('content')
    <div x-data="manualOnboarding()" class="max-w-5xl mx-auto space-y-6 font-sans text-slate-800 pb-12 pt-4 px-4 md:px-0">
        {{-- Header --}}
        <div
            class="bg-slate-900 rounded-xl shadow-md p-6 flex flex-col md:flex-row md:items-center justify-between gap-4 relative overflow-hidden">
            <div class="flex items-center gap-4 relative z-10">
                <div
                    class="w-12 h-12 bg-primary/20 rounded-lg flex items-center justify-center text-primary text-xl border border-primary/20">
                    <i class="fas fa-user-shield"></i>
                </div>
                <div>
                    <h1 class="text-xl md:text-2xl font-bold text-white">Manual Onboarding</h1>
                    <p class="text-slate-400 text-xs font-semibold mt-1 flex items-center gap-2">
                        <span class="w-2 h-2 bg-green-500 rounded-full animate-pulse"></span>
                        Secret Admin Sync Tool • Level 4 Access
                    </p>
                </div>
            </div>
            <div class="flex items-center gap-2 bg-white/10 px-3 py-1.5 rounded-md border border-white/10 relative z-10">
                <div class="relative flex h-2.5 w-2.5 items-center justify-center">
                    <span
                        class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                    <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                </div>
                <span class="text-xs font-bold uppercase tracking-wider text-slate-200">Live Node</span>
            </div>
        </div>

        {{-- Alerts --}}
        @if(session('success'))
            <div
                class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg font-medium flex items-center gap-3 shadow-sm animate-fade-in">
                <i class="fas fa-check-circle text-green-500 text-lg"></i>
                <span class="text-sm">{{ session('success') }}</span>
            </div>
        @endif

        @if(session('error'))
            <div
                class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg font-medium flex items-center gap-3 shadow-sm">
                <i class="fas fa-exclamation-circle text-red-500 text-lg"></i>
                <span class="text-sm">{{ session('error') }}</span>
            </div>
        @endif

        <form action="{{ route('admin.secret-onboarding.store') }}" method="POST" class="space-y-6">
            @csrf
            <input type="hidden" name="mode" :value="mode">

            {{-- Section 01: Onboarding Mode --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div
                    class="bg-slate-50 border-b border-slate-200 px-6 py-4 flex flex-col md:flex-row justify-between items-center gap-4">
                    <div>
                        <h3 class="text-[10px] font-bold uppercase tracking-wider text-primary mb-0.5">Module Protocol</h3>
                        <h2 class="text-lg font-bold text-slate-800">01. Onboarding Mode</h2>
                    </div>

                    {{-- Mode Switcher --}}
                    <div class="flex bg-slate-200 p-1 rounded-lg w-full md:w-auto">
                        <button type="button" @click="mode = 'lead'"
                            :class="mode === 'lead' ? 'bg-white text-primary shadow-sm font-bold' : 'text-slate-600 hover:text-slate-800 font-medium'"
                            class="flex-1 md:flex-none px-4 py-2 rounded-md text-sm transition-all duration-200">
                            <i class="fas fa-database mr-1.5"></i> Sync Existing Lead
                        </button>
                        <button type="button" @click="mode = 'manual'"
                            :class="mode === 'manual' ? 'bg-white text-primary shadow-sm font-bold' : 'text-slate-600 hover:text-slate-800 font-medium'"
                            class="flex-1 md:flex-none px-4 py-2 rounded-md text-sm transition-all duration-200">
                            <i class="fas fa-plus mr-1.5"></i> Direct Manual Entry
                        </button>
                    </div>
                </div>

                <div class="p-6">
                    <template x-if="mode === 'lead'">
                        <div class="space-y-4">
                            <div class="flex items-center justify-between">
                                <label class="text-xs font-semibold text-slate-600">Search Database Leads</label>
                                <span x-show="isLoading" class="text-xs font-medium text-primary animate-pulse">Scanning
                                    Database...</span>
                            </div>

                            <div class="relative">
                                <div class="absolute inset-y-0 left-3 flex items-center text-slate-400">
                                    <i class="fas fa-search text-sm"></i>
                                </div>
                                <input type="text" x-model="searchQuery" @input.debounce.300ms="fetchLeads()"
                                    placeholder="Search Name, Email, or Phone..."
                                    class="w-full pl-9 pr-10 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none">
                                <div class="absolute right-3 top-1/2 -translate-y-1/2 flex items-center gap-1"
                                    x-show="isLoading">
                                    <div class="w-1 h-1 bg-primary rounded-full animate-bounce"></div>
                                    <div class="w-1 h-1 bg-primary rounded-full animate-bounce delay-75"></div>
                                    <div class="w-1 h-1 bg-primary rounded-full animate-bounce delay-150"></div>
                                </div>

                                {{-- Lead Dropdown --}}
                                <div x-show="leads.length > 0" @click.away="leads = []"
                                    class="absolute z-50 w-full mt-1 bg-white border border-slate-200 rounded-lg shadow-lg max-h-60 overflow-y-auto">
                                    <template x-for="lead in leads" :key="lead.id">
                                        <button type="button" @click="selectLead(lead)"
                                            class="w-full text-left px-4 py-3 hover:bg-slate-50 border-b border-slate-100 last:border-0 transition-colors flex items-center justify-between group">
                                            <div class="flex flex-col">
                                                <span class="text-sm font-semibold text-slate-800"
                                                    x-text="lead.name"></span>
                                                <div class="flex items-center gap-2 mt-0.5">
                                                    <span class="text-xs text-slate-500" x-text="lead.email"></span>
                                                    <span class="w-1 h-1 bg-slate-300 rounded-full"></span>
                                                    <span class="text-xs text-slate-500" x-text="lead.mobile"></span>
                                                </div>
                                            </div>
                                            <i
                                                class="fas fa-chevron-right text-slate-400 group-hover:text-primary transition-colors text-xs"></i>
                                        </button>
                                    </template>
                                </div>
                            </div>

                            <input type="hidden" name="lead_id" :value="selectedLeadId">

                            <div x-show="selectedLeadId"
                                class="bg-blue-50 border border-blue-100 p-4 rounded-lg flex items-center justify-between animate-fade-in mt-4">
                                <div class="flex items-center gap-3">
                                    <div
                                        class="w-10 h-10 bg-primary/10 text-primary rounded-lg flex items-center justify-center">
                                        <i class="fas fa-user-check"></i>
                                    </div>
                                    <div>
                                        <p class="text-[10px] font-bold uppercase tracking-wider text-primary">Lead Locked
                                        </p>
                                        <p class="text-sm font-bold text-slate-800" x-text="selectedLeadName"></p>
                                    </div>
                                </div>
                                <button type="button" @click="clearLead()"
                                    class="text-red-500 hover:bg-red-50 px-3 py-1.5 rounded-md text-xs font-semibold transition-colors flex items-center gap-1.5">
                                    <i class="fas fa-times"></i> Deselect
                                </button>
                            </div>
                        </div>
                    </template>

                    <template x-if="mode === 'manual'">
                        <div
                            class="p-8 bg-slate-50 rounded-lg border border-dashed border-slate-300 text-center flex flex-col items-center gap-3 animate-fade-in">
                            <div
                                class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-primary text-xl shadow-sm border border-slate-200">
                                <i class="fas fa-keyboard"></i>
                            </div>
                            <div>
                                <p class="text-base font-bold text-slate-800">Manual Entry Mode Active</p>
                                <p class="text-xs text-slate-500 mt-1">System will bypass lead lookup and generate a fresh
                                    master entry.</p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Section 02: Account Information --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
                    <h3 class="text-[10px] font-bold uppercase tracking-wider text-primary mb-0.5">Entity Credentials</h3>
                    <h2 class="text-lg font-bold text-slate-800">02. Account Information</h2>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                            <i class="fas fa-user-circle text-slate-400"></i> Full Name
                        </label>
                        <input type="text" name="name" x-model="formData.name" required placeholder="e.g. John Doe"
                            class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                            <i class="fas fa-envelope text-slate-400"></i> Email Address
                        </label>
                        <input type="email" name="email" x-model="formData.email" required placeholder="john@example.com"
                            class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                            <i class="fas fa-phone-alt text-slate-400"></i> Mobile Number
                        </label>
                        <input type="text" name="mobile" x-model="formData.mobile" required maxlength="10"
                            placeholder="9876543210"
                            class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                            <i class="fas fa-lock text-slate-400"></i> Account Password
                        </label>
                        <input type="text" name="password" x-model="formData.password" required
                            class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none">
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                            <i class="fas fa-venus-mars text-slate-400"></i> Gender
                        </label>
                        <select name="gender" x-model="formData.gender" required
                            class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                            <i class="fas fa-map-marker-alt text-slate-400"></i> State / Region
                        </label>
                        <select name="state_id" x-model="formData.state_id" required
                            class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none">
                            <option value="">Select State</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Section 03: Product & Sponsorship --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
                    <h3 class="text-[10px] font-bold uppercase tracking-wider text-primary mb-0.5">Commercial Flow</h3>
                    <h2 class="text-lg font-bold text-slate-800">03. Product & Sponsorship</h2>
                </div>

                <div class="p-6 grid grid-cols-1 md:grid-cols-2 gap-5">
                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                            <i class="fas fa-box-open text-slate-400"></i> Select Bundle
                        </label>
                        <select name="bundle_id" x-model="formData.bundle_id" required
                            class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none">
                            <option value="">Choose Bundle...</option>
                            @foreach($bundles as $bundle)
                                <option value="{{ $bundle->id }}">
                                    {{ $bundle->title }} (₹{{ $bundle->affiliate_price }})
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                            <i class="fas fa-user-friends text-slate-400"></i> Referral Code (Sponsor)
                        </label>
                        <input type="text" name="referral_code" x-model="formData.referral_code"
                            placeholder="Enter referral code"
                            class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none">

                        <div x-show="sponsorName"
                            class="mt-2 bg-primary/5 px-3 py-2 rounded border border-primary/20 text-xs font-semibold text-primary flex items-center gap-2 animate-fade-in">
                            <i class="fas fa-check-circle"></i>
                            <span>Sponsor: <span x-text="sponsorName" class="text-slate-700"></span></span>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Section 04: Payment Synchronization --}}
            <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="bg-slate-50 border-b border-slate-200 px-6 py-4">
                    <h3 class="text-[10px] font-bold uppercase tracking-wider text-primary mb-0.5">Financial Reconciliation
                    </h3>
                    <h2 class="text-lg font-bold text-slate-800">04. Payment Synchronization</h2>
                </div>

                <div class="p-6 space-y-5">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                                <i class="fas fa-credit-card text-slate-400"></i> Payment Gateway
                            </label>
                            <select name="payment_method" x-model="formData.payment_method" required
                                class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none">
                                <option value="razorpay">Razorpay (Production Parity)</option>
                                <option value="cashfree">Cashfree (Production Parity)</option>
                                <option value="manual_admin">Offline / RTGS / Manual</option>
                            </select>
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                                <i class="fas fa-rupee-sign text-slate-400"></i> Received Amount
                            </label>
                            <input type="number" name="amount" x-model="formData.amount" required step="0.01"
                                placeholder="0.00"
                                class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-5">
                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                                <i class="fas fa-fingerprint text-slate-400"></i> Gateway Payment ID
                            </label>
                            <input type="text" name="gateway_payment_id" x-model="formData.gateway_payment_id" required
                                placeholder="pay_123abc or UTR..."
                                class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                                <i class="fas fa-hashtag text-slate-400"></i> Gateway Order ID
                            </label>
                            <input type="text" name="gateway_order_id" x-model="formData.gateway_order_id"
                                placeholder="order_XYZ123"
                                class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none">
                        </div>

                        <div class="space-y-1.5">
                            <label class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                                <i class="fas fa-barcode text-slate-400"></i> UTR Number
                            </label>
                            <input type="text" name="utr_number" x-model="formData.utr_number" placeholder="UTR-XXXXX"
                                class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none">
                        </div>
                    </div>

                    <div class="space-y-1.5">
                        <label class="text-xs font-semibold text-slate-600 flex items-center gap-1.5">
                            <i class="fas fa-calendar-alt text-slate-400"></i> Transaction Date
                        </label>
                        <input type="date" name="payment_date" x-model="formData.payment_date" required
                            class="w-full px-4 py-2.5 bg-white border border-slate-300 rounded-lg text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all outline-none">
                    </div>
                </div>
            </div>

            {{-- Submit Action --}}
            <div class="pt-4">
                <button type="submit"
                    class="w-full bg-slate-900 text-white font-bold py-3.5 px-6 rounded-xl shadow-md hover:bg-slate-800 hover:shadow-lg transition-all duration-200 flex items-center justify-center gap-2">
                    <i class="fas fa-cloud-upload-alt text-primary"></i>
                    Execute Master Sync
                </button>
                <p class="text-center text-xs text-slate-500 mt-3 font-medium flex items-center justify-center gap-2">
                    <i class="fas fa-lock text-slate-400"></i> Secure Admin Action
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
        :root {
            --primary-rgb: 247, 148, 29;
            /* Keep your primary color intact */
        }

        .animate-fade-in {
            animation: fadeIn 0.3s ease-in-out;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(5px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Custom Scrollbar for Dropdown to keep it neat */
        .overflow-y-auto::-webkit-scrollbar {
            width: 6px;
        }

        .overflow-y-auto::-webkit-scrollbar-track {
            background: transparent;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb {
            background: #cbd5e1;
            border-radius: 10px;
        }

        .overflow-y-auto::-webkit-scrollbar-thumb:hover {
            background: #94a3b8;
        }

        [x-cloak] {
            display: none !important;
        }
    </style>
@endsection
