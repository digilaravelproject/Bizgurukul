@extends('layouts.admin')

@section('title', 'Secret Manual Onboarding')

@section('content')
<div x-data="manualOnboarding()" class="max-w-5xl mx-auto space-y-10 font-sans text-mainText pb-24 pt-4 px-4 md:px-0">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 p-8 bg-slate-950 rounded-[2.5rem] shadow-2xl relative overflow-hidden group">
        {{-- Background Decoration --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary/10 rounded-full blur-3xl -mr-20 -mt-20 group-hover:bg-primary/20 transition-all duration-700"></div>
        <div class="absolute bottom-0 left-0 w-48 h-48 bg-secondary/10 rounded-full blur-3xl -ml-20 -mb-20 group-hover:bg-secondary/20 transition-all duration-700"></div>
        
        <div class="flex items-center gap-5 relative z-10">
            <div class="w-16 h-16 bg-gradient-to-br from-primary via-secondary to-primary bg-[length:200%_auto] animate-shimmer rounded-2xl flex items-center justify-center text-white text-3xl shadow-[0_10px_30px_-5px_rgba(var(--primary-rgb),0.4)]">
                <i class="fas fa-user-shield"></i>
            </div>
            <div>
                <h1 class="text-3xl md:text-5xl font-black tracking-tighter text-white uppercase italic leading-none">Manual <span class="text-primary">Onboarding</span></h1>
                <p class="text-slate-400 text-[10px] md:text-xs font-bold uppercase tracking-[0.4em] mt-2 flex items-center gap-2">
                    <span class="w-2 h-2 bg-primary rounded-full animate-pulse"></span>
                    Secret Admin Sync Tool • Level 4 Access
                </p>
            </div>
        </div>
        <div class="flex items-center gap-4 bg-white/5 backdrop-blur-xl p-3 px-6 rounded-2xl border border-white/10 shadow-2xl relative z-10 group/status">
            <div class="w-3 h-3 bg-green-500 rounded-full animate-ping"></div>
            <div class="w-3 h-3 bg-green-500 rounded-full absolute ml-0"></div>
            <span class="text-[10px] font-black uppercase tracking-[0.2em] text-slate-300">Live Database Node: <span class="text-green-400">ACTIVE</span></span>
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
        <div class="bg-white rounded-[2.5rem] shadow-[0_20px_70px_-15px_rgba(0,0,0,0.1)] border border-slate-100 transition-all overflow-visible group/section hover:shadow-[0_30px_90px_-20px_rgba(0,0,0,0.15)]">
            <div class="bg-slate-950 p-8 md:p-10 border-b border-white/5 flex flex-col md:flex-row justify-between items-center gap-6 rounded-t-[2.5rem] relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary/10 blur-2xl rounded-full -mr-10 -mt-10"></div>
                <div class="flex flex-col relative z-10">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-primary mb-1">Module Protocol</h3>
                    <h2 class="text-xl md:text-2xl font-black text-white uppercase italic">01. Onboarding Mode</h2>
                </div>
                <div class="flex bg-white/5 backdrop-blur-xl p-2 rounded-[1.5rem] border border-white/10 w-full md:w-auto relative z-10 shadow-inner">
                    <button type="button" @click="mode = 'lead'" :class="mode === 'lead' ? 'bg-primary text-white shadow-[0_10px_25px_-5px_rgba(var(--primary-rgb),0.5)]' : 'text-slate-400 hover:text-white'" class="flex-1 md:flex-none px-8 py-4 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 transform active:scale-95">
                        <i class="fas fa-database mr-2"></i> Sync Existing Lead
                    </button>
                    <button type="button" @click="mode = 'manual'" :class="mode === 'manual' ? 'bg-primary text-white shadow-[0_10px_25px_-5px_rgba(var(--primary-rgb),0.5)]' : 'text-slate-400 hover:text-white'" class="flex-1 md:flex-none px-8 py-4 rounded-xl text-[10px] font-black uppercase tracking-widest transition-all duration-300 transform active:scale-95">
                        <i class="fas fa-plus mr-2"></i> Direct Manual Entry
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
                        <div class="relative group/search">
                            <div class="absolute inset-y-0 left-6 flex items-center text-primary group-focus-within/search:scale-110 transition-transform">
                                <i class="fas fa-search text-lg"></i>
                            </div>
                            <input type="text" x-model="searchQuery" @input.debounce.300ms="fetchLeads()" placeholder="Search Name, Email, or Phone..."
                                class="w-full pl-16 pr-8 py-6 bg-slate-50 border border-slate-200 rounded-[2rem] text-mainText font-bold focus:bg-white focus:border-primary focus:ring-[12px] focus:ring-primary/5 transition-all outline-none placeholder:text-slate-400 placeholder:font-medium text-lg shadow-inner">
                            <div class="absolute right-6 top-1/2 -translate-y-1/2 flex items-center gap-2" x-show="isLoading">
                                <div class="w-1.5 h-1.5 bg-primary rounded-full animate-bounce [animation-delay:-0.3s]"></div>
                                <div class="w-1.5 h-1.5 bg-primary rounded-full animate-bounce [animation-delay:-0.15s]"></div>
                                <div class="w-1.5 h-1.5 bg-primary rounded-full animate-bounce"></div>
                            </div>
                        </div>
                            
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
                    <div class="p-12 bg-slate-50 rounded-[2.5rem] border-2 border-dashed border-slate-200 text-center flex flex-col items-center gap-6 animate-fade-in group/manual shadow-inner">
                        <div class="w-20 h-20 bg-white rounded-3xl flex items-center justify-center text-primary text-3xl shadow-xl group-hover/manual:scale-110 transition-transform duration-500 border border-slate-100">
                            <i class="fas fa-keyboard"></i>
                        </div>
                        <div class="space-y-2">
                            <p class="text-xs font-black text-slate-400 uppercase tracking-[0.3em]">Protocol Overridden</p>
                            <p class="text-lg font-black text-mainText">Manual Entry Mode Active</p>
                            <p class="text-xs font-medium text-mutedText max-w-xs mx-auto">System will bypass lead lookup and generate a fresh master entry for this user.</p>
                        </div>
                        <div class="flex items-center gap-3 bg-white px-5 py-2.5 rounded-xl border border-slate-200 shadow-sm">
                            <div class="w-2 h-2 bg-primary rounded-full animate-pulse"></div>
                            <span class="text-[10px] font-black uppercase tracking-widest text-primary">Direct Injection Ready</span>
                        </div>
                    </div>
                </template>
            </div>
        </div>

        {{-- Section: Account Information --}}
        <div class="bg-white rounded-[2.5rem] shadow-[0_20px_70px_-15px_rgba(0,0,0,0.1)] border border-slate-100 transition-all overflow-hidden group/section hover:shadow-[0_30px_90px_-20px_rgba(0,0,0,0.15)]">
            <div class="bg-slate-950 p-8 md:p-10 border-b border-white/5 rounded-t-[2.5rem] relative overflow-hidden">
                <div class="absolute bottom-0 left-0 w-32 h-32 bg-secondary/10 blur-2xl rounded-full -ml-10 -mb-10"></div>
                <div class="relative z-10">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-primary mb-1">Entity Credentials</h3>
                    <h2 class="text-xl md:text-2xl font-black text-white uppercase italic">02. Account Information</h2>
                </div>
            </div>
            
            <div class="p-8 md:p-10 grid grid-cols-1 md:grid-cols-2 gap-10">
                {{-- Name --}}
                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1 flex items-center gap-2">
                        <i class="fas fa-user-circle text-primary"></i> Full Name
                    </label>
                    <input type="text" name="name" x-model="formData.name" required
                        class="w-full px-8 py-5 bg-slate-50 border border-slate-200 rounded-[1.5rem] text-mainText font-bold focus:bg-white focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none shadow-inner"
                        placeholder="e.g. John Doe">
                </div>

                {{-- Email --}}
                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1 flex items-center gap-2">
                        <i class="fas fa-envelope text-primary"></i> Email Address
                    </label>
                    <input type="email" name="email" x-model="formData.email" required
                        class="w-full px-8 py-5 bg-slate-50 border border-slate-200 rounded-[1.5rem] text-mainText font-bold focus:bg-white focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none shadow-inner"
                        placeholder="john@example.com">
                </div>

                {{-- Mobile --}}
                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1 flex items-center gap-2">
                        <i class="fas fa-phone-alt text-primary"></i> Mobile Number
                    </label>
                    <input type="text" name="mobile" x-model="formData.mobile" required maxlength="10"
                        class="w-full px-8 py-5 bg-slate-50 border border-slate-200 rounded-[1.5rem] text-mainText font-bold focus:bg-white focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none shadow-inner"
                        placeholder="9876543210">
                </div>

                {{-- Password --}}
                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1 flex items-center gap-2">
                        <i class="fas fa-lock text-primary"></i> Account Password
                    </label>
                    <input type="text" name="password" x-model="formData.password" required
                        class="w-full px-8 py-5 bg-slate-50 border border-slate-200 rounded-[1.5rem] text-mainText font-bold focus:bg-white focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none shadow-inner">
                </div>

                {{-- Gender --}}
                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1 flex items-center gap-2">
                        <i class="fas fa-venus-mars text-primary"></i> Gender
                    </label>
                    <div class="relative">
                        <select name="gender" x-model="formData.gender" required
                            class="w-full px-8 py-5 bg-slate-50 border border-slate-200 rounded-[1.5rem] text-mainText font-bold focus:bg-white focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none appearance-none shadow-inner">
                            <option value="male">Male</option>
                            <option value="female">Female</option>
                            <option value="other">Other</option>
                        </select>
                        <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-primary/40">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                {{-- State --}}
                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1 flex items-center gap-2">
                        <i class="fas fa-map-marker-alt text-primary"></i> State / Region
                    </label>
                    <div class="relative">
                        <select name="state_id" x-model="formData.state_id" required
                            class="w-full px-8 py-5 bg-slate-50 border border-slate-200 rounded-[1.5rem] text-mainText font-bold focus:bg-white focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none appearance-none shadow-inner">
                            <option value="">Select State</option>
                            @foreach($states as $state)
                                <option value="{{ $state->id }}">{{ $state->name }}</option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-primary/40">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section: Product & Referral --}}
        <div class="bg-white rounded-[2.5rem] shadow-[0_20px_70px_-15px_rgba(0,0,0,0.1)] border border-slate-100 transition-all overflow-hidden group/section hover:shadow-[0_30px_90px_-20px_rgba(0,0,0,0.15)]">
            <div class="bg-slate-950 p-8 md:p-10 border-b border-white/5 rounded-t-[2.5rem] relative overflow-hidden">
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary/10 blur-2xl rounded-full -mr-10 -mt-10"></div>
                <div class="relative z-10">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-primary mb-1">Commercial Flow</h3>
                    <h2 class="text-xl md:text-2xl font-black text-white uppercase italic">03. Product & Sponsorship</h2>
                </div>
            </div>
            
            <div class="p-8 md:p-10 grid grid-cols-1 md:grid-cols-2 gap-10">
                {{-- Bundle --}}
                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1 flex items-center gap-2">
                        <i class="fas fa-box-open text-primary"></i> Select Bundle
                    </label>
                    <div class="relative">
                        <select name="bundle_id" x-model="formData.bundle_id" required
                            class="w-full px-8 py-5 bg-slate-50 border border-slate-200 rounded-[1.5rem] text-mainText font-bold focus:bg-white focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none appearance-none shadow-inner">
                            <option value="">Choose Bundle...</option>
                            @foreach($bundles as $bundle)
                                <option value="{{ $bundle->id }}">
                                    {{ $bundle->title }} (₹{{ $bundle->affiliate_price }})
                                </option>
                            @endforeach
                        </select>
                        <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-primary/40">
                            <i class="fas fa-chevron-down text-xs"></i>
                        </div>
                    </div>
                </div>

                {{-- Referral Code --}}
                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1 flex items-center gap-2">
                        <i class="fas fa-user-friends text-primary"></i> Referral Code (Sponsor)
                    </label>
                    <div class="relative">
                        <input type="text" name="referral_code" x-model="formData.referral_code"
                            class="w-full px-8 py-5 bg-slate-50 border border-slate-200 rounded-[1.5rem] text-mainText font-bold focus:bg-white focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none shadow-inner"
                            placeholder="Enter referral code">
                        <div x-show="sponsorName" class="mt-4 bg-primary/5 backdrop-blur-md p-4 rounded-2xl border border-primary/20 text-[10px] font-black uppercase text-primary tracking-[0.2em] flex items-center gap-3 animate-fade-in shadow-sm">
                            <div class="w-6 h-6 bg-primary rounded-full flex items-center justify-center text-white text-[8px]">
                                <i class="fas fa-check"></i>
                            </div>
                            <span>Sponsor Verified: <span x-text="sponsorName" class="text-mainText"></span></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Section: Payment Metadata --}}
        <div class="bg-white rounded-[2.5rem] shadow-[0_20px_70px_-15px_rgba(0,0,0,0.1)] border border-slate-100 transition-all overflow-hidden group/section hover:shadow-[0_30px_90px_-20px_rgba(0,0,0,0.15)]">
            <div class="bg-slate-950 p-8 md:p-10 border-b border-white/5 rounded-t-[2.5rem] relative overflow-hidden">
                <div class="absolute bottom-0 right-0 w-32 h-32 bg-secondary/10 blur-2xl rounded-full -mr-10 -mb-10"></div>
                <div class="relative z-10">
                    <h3 class="text-[10px] font-black uppercase tracking-[0.3em] text-primary mb-1">Financial Reconciliation</h3>
                    <h2 class="text-xl md:text-2xl font-black text-white uppercase italic">04. Payment Synchronization</h2>
                </div>
            </div>
            
            <div class="p-8 md:p-10 space-y-10">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Payment Method --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1 flex items-center gap-2">
                            <i class="fas fa-credit-card text-primary"></i> Payment Gateway
                        </label>
                        <div class="relative">
                            <select name="payment_method" x-model="formData.payment_method" required
                                class="w-full px-8 py-5 bg-slate-50 border border-slate-200 rounded-[1.5rem] text-mainText font-bold focus:bg-white focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none appearance-none shadow-inner">
                                <option value="razorpay">Razorpay (Production Parity)</option>
                                <option value="cashfree">Cashfree (Production Parity)</option>
                                <option value="manual_admin">Offline / RTGS / Manual</option>
                            </select>
                            <div class="absolute inset-y-0 right-6 flex items-center pointer-events-none text-primary/40">
                                <i class="fas fa-chevron-down text-xs"></i>
                            </div>
                        </div>
                    </div>

                    {{-- Amount --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1 flex items-center gap-2">
                            <i class="fas fa-rupee-sign text-primary"></i> Received Amount
                        </label>
                        <div class="relative">
                            <input type="number" name="amount" x-model="formData.amount" required step="0.01"
                                class="w-full px-8 py-5 bg-slate-50 border border-slate-200 rounded-[1.5rem] text-mainText font-bold focus:bg-white focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none shadow-inner"
                                placeholder="0.00">
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-10">
                    {{-- Payment ID --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1 flex items-center gap-2">
                            <i class="fas fa-fingerprint text-primary"></i> Gateway Payment ID
                        </label>
                        <input type="text" name="gateway_payment_id" x-model="formData.gateway_payment_id" required
                            class="w-full px-8 py-5 bg-slate-50 border border-slate-200 rounded-[1.5rem] text-mainText font-bold focus:bg-white focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none shadow-inner"
                            placeholder="pay_123abc or UTR...">
                    </div>

                    {{-- Order ID --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1 flex items-center gap-2">
                            <i class="fas fa-hashtag text-primary"></i> Gateway Order ID
                        </label>
                        <input type="text" name="gateway_order_id" x-model="formData.gateway_order_id"
                            class="w-full px-8 py-5 bg-slate-50 border border-slate-200 rounded-[1.5rem] text-mainText font-bold focus:bg-white focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none shadow-inner"
                            placeholder="order_XYZ123">
                    </div>

                    {{-- UTR --}}
                    <div class="space-y-4">
                        <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1 flex items-center gap-2">
                            <i class="fas fa-barcode text-primary"></i> UTR Number
                        </label>
                        <input type="text" name="utr_number" x-model="formData.utr_number"
                            class="w-full px-8 py-5 bg-slate-50 border border-slate-200 rounded-[1.5rem] text-mainText font-bold focus:bg-white focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none shadow-inner"
                            placeholder="UTR-XXXXX">
                    </div>
                </div>

                {{-- Date --}}
                <div class="space-y-4">
                    <label class="text-[10px] font-black uppercase tracking-widest text-slate-400 ml-1 flex items-center gap-2">
                        <i class="fas fa-calendar-alt text-primary"></i> Transaction Date
                    </label>
                    <input type="date" name="payment_date" x-model="formData.payment_date" required
                        class="w-full px-8 py-5 bg-slate-50 border border-slate-200 rounded-[1.5rem] text-mainText font-bold focus:bg-white focus:border-primary focus:ring-8 focus:ring-primary/5 transition-all outline-none shadow-inner">
                </div>
            </div>
        </div>

        <div class="pt-12">
            <button type="submit" 
                class="w-full bg-slate-950 text-white font-black py-8 px-10 rounded-[2.5rem] shadow-[0_30px_70px_-15px_rgba(0,0,0,0.4)] hover:shadow-[0_40px_80px_-20px_rgba(0,0,0,0.5)] hover:-translate-y-2 transition-all duration-500 active:scale-[0.98] uppercase tracking-[0.4em] flex flex-col items-center justify-center gap-3 text-sm relative overflow-hidden group/submit">
                {{-- Button Shimmer effect --}}
                <div class="absolute inset-0 bg-gradient-to-r from-transparent via-white/10 to-transparent -translate-x-full group-hover/submit:animate-shimmer-fast"></div>
                
                <div class="flex items-center gap-4 relative z-10">
                    <i class="fas fa-bolt text-primary text-xl animate-pulse"></i>
                    <span class="text-xl">Execute Master Sync</span>
                    <i class="fas fa-bolt text-primary text-xl animate-pulse"></i>
                </div>
                <span class="text-[9px] font-bold text-slate-500 tracking-[0.5em] opacity-80 group-hover/submit:opacity-100 transition-opacity">Commit Data to Production Cluster</span>
            </button>
            
            <div class="mt-12 flex flex-col items-center gap-6">
                <div class="flex items-center gap-8 w-full opacity-20">
                    <div class="h-[1px] flex-1 bg-gradient-to-r from-transparent to-slate-900"></div>
                    <div class="flex items-center gap-4">
                        <i class="fas fa-fingerprint text-xs"></i>
                        <i class="fas fa-shield-alt text-xs"></i>
                        <i class="fas fa-microchip text-xs"></i>
                    </div>
                    <div class="h-[1px] flex-1 bg-gradient-to-l from-transparent to-slate-900"></div>
                </div>
                <p class="text-[9px] font-black uppercase tracking-[0.6em] text-slate-400 text-center leading-relaxed">
                    Security Protocol Alpha-6 • Encrypted Backend Tunnel • Authorized Admin Personnel Only
                </p>
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
    :root {
        --primary-rgb: 247, 148, 29;
    }
    .animate-spin-slow {
        animation: spin 3s linear infinite;
    }
    .animate-shimmer {
        animation: shimmer 5s linear infinite;
    }
    .animate-shimmer-fast {
        animation: shimmer 1.5s infinite;
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
        animation: fadeIn 0.6s cubic-bezier(0.23, 1, 0.32, 1);
    }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    
    /* Custom Scrollbar for Dropdown */
    .overflow-y-auto::-webkit-scrollbar {
        width: 6px;
    }
    .overflow-y-auto::-webkit-scrollbar-track {
        background: transparent;
    }
    .overflow-y-auto::-webkit-scrollbar-thumb {
        background: rgba(var(--primary-rgb), 0.2);
        border-radius: 10px;
    }
    .overflow-y-auto::-webkit-scrollbar-thumb:hover {
        background: rgba(var(--primary-rgb), 0.5);
    }

    [x-cloak] { display: none !important; }
</style>
@endsection
