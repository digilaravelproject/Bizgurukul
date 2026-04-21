@extends('layouts.admin')
@section('title', 'User Details - ' . $user->name)

@section('content')
<div class="animate-fade-in pb-20">
    {{-- Top Action Bar --}}
    <div class="flex items-center justify-between mb-8 group">
        <div class="flex items-center gap-4">
            <a href="{{ route('admin.users.index') }}" 
               class="bg-white/10 hover:bg-white text-mainText hover:text-navy p-3 rounded-2xl transition-all duration-300 shadow-sm">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7"></path>
                </svg>
            </a>
            <div>
                <h2 class="text-2xl font-black tracking-tight text-mainText">{{ $user->name }}</h2>
                <div class="flex items-center gap-2 mt-1">
                    <span class="text-[10px] bg-primary/10 text-primary px-2.5 py-1 rounded-lg font-black uppercase tracking-widest">{{ $userData['role'] }}</span>
                    <span class="text-[10px] bg-navy-light text-mutedText px-2.5 py-1 rounded-lg font-bold">ID: #{{ $user->id }}</span>
                </div>
            </div>
        </div>
        
        <div class="flex items-center gap-3">
             <form method="POST" action="{{ route('admin.users.impersonate', $user->id) }}">
                @csrf
                <button type="submit" 
                        class="bg-white/5 hover:bg-white border border-primary/10 text-mainText hover:text-navy px-6 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-sm flex items-center gap-2">
                    <i class="fas fa-user-secret text-primary"></i>
                    Impersonate
                </button>
            </form>
            <button class="brand-gradient text-white px-8 py-3 rounded-2xl text-xs font-black uppercase tracking-widest transition-all shadow-lg shadow-primary/25 hover:-translate-y-0.5 active:scale-95">
                Quick Action
            </button>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- LEFT COLUMN: Profile & Stats --}}
        <div class="lg:col-span-4 space-y-8">
            {{-- Profile Card --}}
            <div class="bg-white border border-primary/10 rounded-[2.5rem] overflow-hidden shadow-xl shadow-primary/5 relative">
                <div class="brand-gradient h-32 absolute top-0 left-0 w-full opacity-10"></div>
                
                <div class="pt-12 px-8 pb-10 flex flex-col items-center relative z-10 text-center">
                    <div class="relative group">
                        <div class="absolute -inset-1 bg-gradient-to-tr from-primary to-secondary rounded-[2rem] blur opacity-25 group-hover:opacity-50 transition duration-1000 group-hover:duration-200"></div>
                        @if($userData['profile_photo_url'])
                            <img src="{{ $userData['profile_photo_url'] }}" 
                                 class="h-32 w-32 rounded-[2rem] border-4 border-white shadow-2xl object-cover relative">
                        @else
                            <div class="h-32 w-32 rounded-[2rem] border-4 border-white shadow-2xl brand-gradient flex items-center justify-center text-white text-4xl font-black relative">
                                {{ $userData['initials'] }}
                            </div>
                        @endif
                        
                        {{-- Status Indicator --}}
                        <div class="absolute -bottom-1 -right-1 h-8 w-8 rounded-full border-4 border-white flex items-center justify-center shadow-lg transition-transform duration-300 group-hover:scale-110 {{ $user->is_banned ? 'bg-secondary' : 'bg-green-500' }}">
                            <i class="fas {{ $user->is_banned ? 'fa-ban' : 'fa-check' }} text-white text-[10px]"></i>
                        </div>
                    </div>

                    <h3 class="mt-8 text-xl font-black text-mainText">{{ $user->name }}</h3>
                    <p class="text-sm text-mutedText font-medium mt-1">{{ $user->email }}</p>
                    
                    <div class="flex gap-2 mt-6">
                        <div class="px-4 py-2 bg-navy-light rounded-xl border border-primary/5 flex flex-col items-center">
                            <span class="text-[9px] font-black uppercase text-mutedText/60 tracking-tighter">KYC</span>
                            <span class="text-[10px] font-black uppercase {{ $user->kyc_status === 'verified' ? 'text-green-600' : 'text-secondary' }}">
                                {{ $user->kyc_status }}
                            </span>
                        </div>
                         <div class="px-4 py-2 bg-navy-light rounded-xl border border-primary/5 flex flex-col items-center">
                            <span class="text-[9px] font-black uppercase text-mutedText/60 tracking-tighter">Bank</span>
                            <span class="text-[10px] font-black uppercase {{ ($userData['bank']['status'] ?? '') === 'verified' ? 'text-green-600' : 'text-secondary' }}">
                                {{ $userData['bank']['status'] ?? 'N/A' }}
                            </span>
                        </div>
                        <div class="px-4 py-2 bg-navy-light rounded-xl border border-primary/5 flex flex-col items-center">
                            <span class="text-[9px] font-black uppercase text-mutedText/60 tracking-tighter">Joined</span>
                            <span class="text-[10px] font-black uppercase text-mainText">{{ $userData['joined_at'] }}</span>
                        </div>
                    </div>
                </div>

                {{-- Quick Metrics --}}
                <div class="px-4 pb-4">
                    <div class="bg-navy p-6 rounded-[2.5rem] grid grid-cols-2 gap-4">
                        <div class="brand-gradient p-5 rounded-[1.8rem] shadow-lg shadow-primary/20 text-white group cursor-help transition-all duration-300 hover:scale-[1.02]">
                            <p class="text-[10px] font-black uppercase tracking-widest opacity-80 mb-1">Earnings</p>
                            <h4 class="text-xl font-black tracking-tighter">₹{{ number_format($userData['total_earnings'], 0) }}</h4>
                        </div>
                        <div class="bg-white/10 p-5 rounded-[1.8rem] text-mainText group cursor-help transition-all duration-300 hover:scale-[1.02] border border-white/5">
                            <p class="text-[10px] font-black uppercase tracking-widest text-mutedText/80 mb-1">Wallet</p>
                            <h4 class="text-xl font-black tracking-tighter">₹{{ number_format($userData['wallet_balance'], 0) }}</h4>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Sponsor Card --}}
            <div class="bg-white border border-primary/10 rounded-[2rem] p-6 shadow-xl shadow-primary/5 flex items-center gap-5 group hover:border-primary/30 transition-all duration-500">
                <div class="h-16 w-16 rounded-2xl brand-gradient flex items-center justify-center text-white text-xl font-black shadow-lg shadow-primary/20 transition-transform duration-500 group-hover:rotate-6">
                    {{ substr($userData['sponsor_name'], 0, 1) }}
                </div>
                <div>
                    <h4 class="text-[10px] font-black text-mutedText uppercase tracking-[0.2em] mb-1">Sponsor Details</h4>
                    <p class="text-base font-black text-mainText group-hover:text-primary transition-colors">{{ $userData['sponsor_name'] }}</p>
                    <p class="text-[10px] text-mutedText font-bold">Referrer Partner</p>
                </div>
                <div class="ml-auto opacity-0 group-hover:opacity-100 transition-all scale-75 group-hover:scale-100">
                    <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M17 8l4 4m0 0l-4 4m4-4H3" /></svg>
                </div>
            </div>
        </div>

        {{-- RIGHT COLUMN: Detailed Info --}}
        <div class="lg:col-span-8 space-y-8">
            {{-- Information Grid --}}
            <div class="bg-white border border-primary/10 rounded-[2.5rem] p-4 shadow-xl shadow-primary/5">
                <div class="bg-navy rounded-[2rem] p-8">
                    <div class="flex items-center gap-3 mb-10">
                        <div class="h-12 w-12 rounded-2xl brand-gradient flex items-center justify-center text-white shadow-lg rotate-3 group-hover:rotate-0 transition-transform">
                            <i class="fas fa-id-card text-xl"></i>
                        </div>
                        <div>
                            <h3 class="text-xl font-black text-mainText tracking-tight">Identity & Profile</h3>
                            <p class="text-[10px] text-mutedText font-bold uppercase tracking-widest mt-0.5">Comprehensive User Data</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                        <div class="bg-white/5 p-6 rounded-3xl border border-white/5 group hover:bg-white/10 transition-all hover:-translate-y-1">
                            <p class="text-[10px] font-black text-mutedText uppercase tracking-widest mb-2 opacity-60">Mobile Number</p>
                            <p class="text-sm font-bold text-mainText flex items-center gap-2">
                                <span class="h-6 w-6 rounded-lg bg-primary/10 flex items-center justify-center text-primary text-[10px]">
                                    <i class="fas fa-phone"></i>
                                </span>
                                {{ $userData['mobile'] ?: 'Not Provided' }}
                            </p>
                        </div>
                        <div class="bg-white/5 p-6 rounded-3xl border border-white/5 group hover:bg-white/10 transition-all hover:-translate-y-1">
                            <p class="text-[10px] font-black text-mutedText uppercase tracking-widest mb-2 opacity-60">Gender</p>
                            <p class="text-sm font-bold text-mainText capitalize flex items-center gap-2">
                                <span class="h-6 w-6 rounded-lg bg-pink-500/10 flex items-center justify-center text-pink-500 text-[10px]">
                                    <i class="fas fa-venus-mars"></i>
                                </span>
                                {{ $userData['gender'] ?: 'N/A' }}
                            </p>
                        </div>
                        <div class="bg-white/5 p-6 rounded-3xl border border-white/5 group hover:bg-white/10 transition-all hover:-translate-y-1">
                            <p class="text-[10px] font-black text-mutedText uppercase tracking-widest mb-2 opacity-60">Date of Birth</p>
                            <p class="text-sm font-bold text-mainText flex items-center gap-2">
                                <span class="h-6 w-6 rounded-lg bg-amber-500/10 flex items-center justify-center text-amber-500 text-[10px]">
                                    <i class="fas fa-calendar-day"></i>
                                </span>
                                {{ $userData['dob'] ?: 'N/A' }}
                            </p>
                        </div>
                        <div class="bg-white/5 p-6 rounded-3xl border border-white/5 group hover:bg-white/10 transition-all hover:-translate-y-1">
                            <p class="text-[10px] font-black text-mutedText uppercase tracking-widest mb-2 opacity-60">State / Region</p>
                            <p class="text-sm font-bold text-mainText flex items-center gap-2">
                                <span class="h-6 w-6 rounded-lg bg-indigo-500/10 flex items-center justify-center text-indigo-500 text-[10px]">
                                    <i class="fas fa-map-marker-alt"></i>
                                </span>
                                {{ $userData['state_name'] }}
                            </p>
                        </div>
                        <div class="bg-white/5 p-6 rounded-3xl border border-white/5 group hover:bg-white/10 transition-all hover:-translate-y-1">
                            <p class="text-[10px] font-black text-mutedText uppercase tracking-widest mb-2 opacity-60">City</p>
                            <p class="text-sm font-bold text-mainText flex items-center gap-2">
                                <span class="h-6 w-6 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500 text-[10px]">
                                    <i class="fas fa-city"></i>
                                </span>
                                {{ $userData['city'] ?: 'N/A' }}
                            </p>
                        </div>
                        <div class="bg-white/5 p-6 rounded-3xl border border-white/5 group hover:bg-white/10 transition-all hover:-translate-y-1">
                            <p class="text-[10px] font-black text-mutedText uppercase tracking-widest mb-2 opacity-60">Referral Code</p>
                            <p class="text-sm font-bold text-primary flex items-center gap-2">
                                <span class="h-6 w-6 rounded-lg bg-primary/10 flex items-center justify-center text-primary text-[10px]">
                                    <i class="fas fa-hashtag"></i>
                                </span>
                                {{ $user->referral_code }}
                                <button onclick="navigator.clipboard.writeText('{{ $user->referral_code }}'); alert('Copied!')" class="hover:scale-125 transition-transform"><i class="fas fa-copy text-[10px] text-mutedText"></i></button>
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Bank Card --}}
            <div class="bg-white border border-primary/10 rounded-[2.5rem] p-4 shadow-xl shadow-primary/5">
                <div class="bg-navy rounded-[2rem] p-8 relative overflow-hidden">
                     {{-- Decorative Background Icon --}}
                    <div class="absolute -bottom-12 -right-12 opacity-[0.05] pointer-events-none">
                        <i class="fas fa-university text-[15rem] -rotate-12"></i>
                    </div>

                    <div class="flex items-center justify-between mb-10 relative z-10">
                        <div class="flex items-center gap-3">
                            <div class="h-12 w-12 rounded-2xl bg-green-500/10 text-green-500 flex items-center justify-center shadow-lg shadow-green-500/10">
                                <i class="fas fa-university text-xl"></i>
                            </div>
                            <div>
                                <h3 class="text-xl font-black text-mainText tracking-tight">Financial Records</h3>
                                <p class="text-[10px] text-mutedText font-bold uppercase tracking-widest">Payout & Bank Profile</p>
                            </div>
                        </div>
                        <div class="px-5 py-2.5 rounded-2xl {{ ($userData['bank']['status'] ?? '') === 'verified' ? 'bg-green-500/10 text-green-500' : 'bg-secondary/10 text-secondary' }} text-[10px] font-black uppercase tracking-widest border border-current/20 backdrop-blur-md">
                            {{ $userData['bank']['status'] ?? 'Not Linked' }}
                        </div>
                    </div>

                    @if($userData['bank'])
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 relative z-10">
                            <div class="space-y-6">
                                <div class="bg-white/5 p-8 rounded-[2.5rem] border border-white/5 hover:bg-white/10 transition-colors">
                                    <p class="text-[9px] font-black text-mutedText uppercase tracking-widest mb-2 opacity-60">Financial Institution</p>
                                    <p class="text-lg font-black text-mainText">{{ $userData['bank']['name'] }}</p>
                                </div>
                                <div class="bg-white/5 p-8 rounded-[2.5rem] border border-white/5 hover:bg-white/10 transition-colors">
                                    <p class="text-[9px] font-black text-mutedText uppercase tracking-widest mb-2 opacity-60">Verified Account Holder</p>
                                    <p class="text-lg font-black text-mainText">{{ $userData['bank']['holder'] }}</p>
                                </div>
                            </div>
                            <div class="space-y-6">
                                <div class="bg-white/5 p-8 rounded-[2.5rem] border border-white/5 hover:bg-white/10 transition-colors">
                                    <p class="text-[9px] font-black text-mutedText uppercase tracking-widest mb-2 opacity-60">Account Identifier</p>
                                    <p class="text-lg font-black text-primary tracking-[0.2em]">
                                        {{ str_repeat('*', max(0, strlen($userData['bank']['account']) - 4)) . substr($userData['bank']['account'], -4) }}
                                    </p>
                                </div>
                                <div class="bg-white/5 p-8 rounded-[2.5rem] border border-white/5 hover:bg-white/10 transition-colors">
                                    @if($userData['bank']['upi'])
                                        <p class="text-[9px] font-black text-mutedText uppercase tracking-widest mb-2 opacity-60">UPI / Payment Handle</p>
                                        <p class="text-lg font-black text-secondary">{{ $userData['bank']['upi'] }}</p>
                                    @else
                                        <p class="text-[9px] font-black text-mutedText uppercase tracking-widest mb-2 opacity-60">Standard IFSC Code</p>
                                        <p class="text-lg font-black text-mainText uppercase">{{ $userData['bank']['ifsc'] }}</p>
                                    @endif
                                </div>
                            </div>
                        </div>
                    @else
                        <div class="bg-white/5 rounded-[3rem] p-16 text-center border-2 border-dashed border-white/10 group cursor-pointer hover:border-primary/30 transition-all duration-500 relative z-10">
                            <div class="h-24 w-24 bg-navy mx-auto rounded-[2.5rem] flex items-center justify-center text-mutedText text-4xl mb-8 shadow-2xl group-hover:scale-110 transition-transform">
                                <i class="fas fa-credit-card opacity-20"></i>
                            </div>
                            <h4 class="text-xl font-black text-mainText mb-3">Gateway Not Configured</h4>
                            <p class="text-sm text-mutedText font-medium max-w-sm mx-auto leading-relaxed">No payout methods have been established for this account yet. Settlement records will appear here once verified.</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .bg-navy-light { background-color: rgba(16, 26, 45, 0.4); }
    .animate-fade-in { animation: fadeIn 0.8s cubic-bezier(0.16, 1, 0.3, 1); }
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px) scale(0.98); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
</style>
@endsection
