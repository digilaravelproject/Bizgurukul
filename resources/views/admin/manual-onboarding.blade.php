@extends('layouts.admin')

@section('title', 'Secret Manual Onboarding')

@section('content')
<div class="max-w-4xl mx-auto space-y-8 font-sans text-mainText pb-20">
    {{-- Header --}}
    <div class="flex flex-col gap-2">
        <h1 class="text-3xl font-black tracking-tight text-mainText uppercase italic">Manual Onboarding & Sync</h1>
        <p class="text-mutedText text-sm font-bold uppercase tracking-widest">Secret Admin Tool for Offline Payment Synchronization</p>
    </div>

    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-600 px-6 py-4 rounded-2xl font-bold flex items-center gap-3 animate-bounce">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-600 px-6 py-4 rounded-2xl font-bold">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('admin.secret-onboarding.store') }}" method="POST" class="space-y-8">
        @csrf

        {{-- Section: User Profile --}}
        <div class="bg-surface rounded-[2rem] shadow-xl border border-primary/10 overflow-hidden">
            <div class="bg-navy p-6 border-b border-primary/5">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-primary">01. Account Information</h3>
            </div>
            
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Name --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Full Name</label>
                    <input type="text" name="name" value="{{ old('name') }}" required
                        class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                        placeholder="e.g. John Doe">
                </div>

                {{-- Email --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Email Address</label>
                    <input type="email" name="email" value="{{ old('email') }}" required
                        class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                        placeholder="john@example.com">
                </div>

                {{-- Mobile --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Mobile Number</label>
                    <input type="text" name="mobile" value="{{ old('mobile') }}" required maxlength="10"
                        class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                        placeholder="9876543210">
                </div>

                {{-- Password --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Account Password</label>
                    <input type="text" name="password" value="{{ old('password', Str::random(10)) }}" required
                        class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none">
                </div>

                {{-- Gender --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Gender</label>
                    <select name="gender" required
                        class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none appearance-none">
                        <option value="male" {{ old('gender') == 'male' ? 'selected' : '' }}>Male</option>
                        <option value="female" {{ old('gender') == 'female' ? 'selected' : '' }}>Female</option>
                        <option value="other" {{ old('gender') == 'other' ? 'selected' : '' }}>Other</option>
                    </select>
                </div>

                {{-- State --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">State / Region</label>
                    <select name="state_id" required
                        class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none appearance-none">
                        <option value="">Select State</option>
                        @foreach($states as $state)
                            <option value="{{ $state->id }}" {{ old('state_id') == $state->id ? 'selected' : '' }}>{{ $state->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Section: Product & Referral --}}
        <div class="bg-surface rounded-[2rem] shadow-xl border border-primary/10 overflow-hidden">
            <div class="bg-navy p-6 border-b border-primary/5">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-primary">02. Product & Sponsorship</h3>
            </div>
            
            <div class="p-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Bundle --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Select Bundle</label>
                    <select name="bundle_id" required
                        class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none appearance-none">
                        <option value="">Choose Bundle...</option>
                        @foreach($bundles as $bundle)
                            <option value="{{ $bundle->id }}" {{ old('bundle_id') == $bundle->id ? 'selected' : '' }}>
                                {{ $bundle->title }} (₹{{ $bundle->affiliate_price }})
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Referral Code --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Referral Code (Sponsor)</label>
                    <input type="text" name="referral_code" value="{{ old('referral_code') }}"
                        class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                        placeholder="Enter referral code">
                </div>
            </div>
        </div>

        {{-- Section: Payment Metadata --}}
        <div class="bg-surface rounded-[2rem] shadow-xl border border-primary/10 overflow-hidden">
            <div class="bg-navy p-6 border-b border-primary/5">
                <h3 class="text-xs font-black uppercase tracking-[0.2em] text-primary">03. Payment Synchronization</h3>
            </div>
            
            <div class="p-8 grid grid-cols-1 md:grid-cols-3 gap-6">
                {{-- TXN ID --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Transaction ID (RTGS/UTR)</label>
                    <input type="text" name="transaction_id" value="{{ old('transaction_id') }}" required
                        class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                        placeholder="UTR123456789">
                </div>

                {{-- Amount --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Received Amount (₹)</label>
                    <input type="number" name="amount" value="{{ old('amount') }}" required step="0.01"
                        class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none"
                        placeholder="0.00">
                </div>

                {{-- Date --}}
                <div class="space-y-2">
                    <label class="text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Payment Date</label>
                    <input type="date" name="payment_date" value="{{ old('payment_date', date('Y-m-d')) }}" required
                        class="w-full px-5 py-4 bg-navy/20 border border-primary/10 rounded-2xl text-mainText font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none">
                </div>
            </div>
        </div>

        <div class="pt-4">
            <button type="submit" 
                class="w-full bg-gradient-to-r from-primary to-secondary text-white font-black py-5 px-8 rounded-3xl shadow-2xl hover:shadow-primary/40 hover:-translate-y-1 transition-all active:scale-[0.98] uppercase tracking-widest flex items-center justify-center gap-3">
                <i class="fas fa-sync-alt animate-spin-slow"></i>
                Verify & Onboard Student
            </button>
            <p class="text-center mt-6 text-[10px] text-mutedText font-bold uppercase tracking-widest opacity-50">
                Note: This will replicate the full production flow (Invoices, Commissions, Roles, Emails)
            </p>
        </div>
    </form>
</div>

<style>
    .animate-spin-slow {
        animation: spin 3s linear infinite;
    }
    @keyframes spin {
        from { transform: rotate(0deg); }
        to { transform: rotate(360deg); }
    }
</style>
@endsection
