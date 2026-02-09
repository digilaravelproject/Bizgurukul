@extends('layouts.user.app')

@section('content')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
<style>
    /* Custom Utility for glass effect using your variables */
    .glass-card {
        background: rgb(var(--color-bg-card) / 0.6);
        backdrop-filter: blur(10px);
        border: 1px solid rgb(var(--color-primary) / 0.2);
    }
    [x-cloak] { display: none !important; }
</style>

<div class="space-y-10 pb-20 max-w-7xl mx-auto px-4" x-data="productSelection()">

    {{-- 1. Hero & Referral Spotlight Section --}}
    <div class="relative overflow-hidden rounded-[2rem] bg-surface border border-primary/10 shadow-2xl shadow-primary/5">
        {{-- Decorative Gradient Background --}}
        <div class="absolute top-0 right-0 w-full h-full opacity-5 pointer-events-none brand-gradient"></div>

        <div class="relative z-10 p-8 md:p-12 flex flex-col lg:flex-row lg:items-center justify-between gap-10">
            {{-- Text Content --}}
            <div class="max-w-2xl">
                <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-primary/10 text-primary mb-4">
                    <span class="relative flex h-2 w-2">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                    </span>
                    <span class="text-[10px] font-bold uppercase tracking-widest">Premium Learning</span>
                </div>
                <h1 class="text-3xl md:text-5xl font-extrabold tracking-tight text-mainText mb-4">
                    Unlock Your <span class="text-primary italic">Potential</span>
                </h1>
                <p class="text-base text-mutedText font-medium leading-relaxed">
                    Welcome back, <span class="text-mainText font-bold underline decoration-primary/30">{{ Auth::user()->name }}</span>.
                    Browse our high-ticket programs or use a referral code to unlock exclusive partner benefits.
                </p>
            </div>

            {{-- HIGHLIGHTED REFERRAL BOX --}}
            <div class="w-full lg:w-96">
                <div class="p-6 rounded-2xl bg-navy border-2 border-primary/20 shadow-inner relative overflow-hidden group">
                    {{-- Shine Effect --}}
                    <div class="absolute -inset-full h-full w-1/2 z-5 block transform -skew-x-12 bg-gradient-to-r from-transparent via-customWhite/10 to-transparent group-hover:animate-[shine_3s_ease-in-out_infinite]"></div>

                    <label class="block text-[10px] font-black text-primary uppercase tracking-[0.2em] mb-4 text-center">
                        Have a Partner Code?
                    </label>

                    <div class="flex flex-col gap-3">
                        <div class="relative">
                            <input type="text" x-model="referralCode"
                                placeholder="ENTER CODE HERE"
                                class="w-full px-5 py-4 bg-surface border-0 rounded-xl text-sm font-bold tracking-widest uppercase focus:ring-2 focus:ring-primary outline-none transition-all placeholder-mutedText/40 text-mainText shadow-lg">
                        </div>

                        <button @click="applyReferral()" :disabled="loading"
                            class="w-full py-4 brand-gradient text-customWhite text-xs font-black uppercase rounded-xl transition-all active:scale-[0.98] shadow-lg shadow-primary/20 hover:shadow-primary/40 flex items-center justify-center gap-2">
                            <span x-show="!loading">Apply & Activate Benefits</span>
                            <span x-show="loading" x-cloak class="flex items-center">
                                <i class="fas fa-circle-notch animate-spin mr-2"></i> PROCESSING...
                            </span>
                        </button>
                    </div>

                    <div x-show="message" x-cloak x-transition class="mt-4 text-center">
                        <div :class="status === 'success' ? 'bg-primary/10 text-primary' : 'bg-secondary/10 text-secondary'"
                             class="py-2 px-4 rounded-lg text-[11px] font-bold uppercase tracking-wider">
                            <span x-text="message"></span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. Partner / Promo Status Bars --}}
    <div class="flex flex-wrap gap-4">
        @if($referrer && !$link)
            <div class="glass-card px-5 py-3 rounded-xl flex items-center gap-3 border-l-4 border-l-primary">
                <i class="fas fa-user-tie text-primary"></i>
                <p class="text-[11px] font-bold text-mutedText uppercase tracking-widest">
                    Active Partner: <span class="text-mainText">{{ $referrer->name }}</span>
                </p>
            </div>
        @endif

        @if($link)
            <div class="bg-secondary/10 border border-secondary/20 px-5 py-3 rounded-xl flex items-center justify-between gap-6 animate-fade-in">
                <div class="flex items-center gap-3 text-secondary">
                    <i class="fas fa-tag"></i>
                    <p class="text-[11px] font-bold uppercase tracking-tight">
                        Promo Applied: <span class="font-black">{{ $link->name }}</span>
                    </p>
                </div>
                <a href="{{ route('student.product_selection') }}" class="text-[10px] font-black uppercase text-mutedText hover:text-secondary underline">Remove</a>
            </div>
        @endif
    </div>

    {{-- 3. Content Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">

        {{-- BUNDLES --}}
        @foreach($filteredBundles as $bundle)
        <div class="group flex flex-col rounded-[2rem] bg-surface shadow-sm border border-primary/5 overflow-hidden hover:border-secondary/40 transition-all duration-500 hover:-translate-y-2">
            <div class="aspect-[4/3] w-full relative overflow-hidden bg-navy">
                @php
                    $src = $bundle->thumbnail ? (str_starts_with($bundle->thumbnail, 'http') ? $bundle->thumbnail : asset('storage/'.$bundle->thumbnail)) : 'https://via.placeholder.com/600x450';
                @endphp
                <img src="{{ $src }}" class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-navy/80 to-transparent opacity-60"></div>
                <span class="absolute top-6 left-6 bg-secondary text-customWhite text-[10px] font-black uppercase px-4 py-1.5 rounded-full tracking-widest shadow-xl">Bundle</span>
            </div>

            <div class="p-8 flex flex-col flex-1">
                <h3 class="text-xl font-bold text-mainText tracking-tight uppercase mb-3 leading-tight">{{ $bundle->title ?? $bundle->name }}</h3>
                <p class="text-mutedText font-medium text-sm line-clamp-2 mb-8 leading-relaxed">{{ strip_tags($bundle->description) }}</p>

                <div class="mt-auto pt-6 border-t border-primary/10 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-mutedText uppercase mb-1 tracking-widest">Investment</p>
                        <p class="text-3xl font-black text-secondary tracking-tighter">₹{{ number_format($bundle->price, 0) }}</p>
                    </div>
                    <button class="brand-gradient text-customWhite w-14 h-14 rounded-2xl flex items-center justify-center shadow-lg hover:shadow-secondary/30 transition-all group/btn">
                        <i class="fas fa-plus transition-transform group-hover/btn:rotate-90"></i>
                    </button>
                </div>
            </div>
        </div>
        @endforeach

        {{-- COURSES --}}
        @foreach($filteredCourses as $course)
        <div class="group flex flex-col rounded-[2rem] bg-surface shadow-sm border border-primary/5 overflow-hidden hover:border-primary/40 transition-all duration-500 hover:-translate-y-2">
            <div class="aspect-[4/3] w-full relative overflow-hidden bg-navy">
                @php
                    $src = $course->thumbnail ? (str_starts_with($course->thumbnail, 'http') ? $course->thumbnail : asset('storage/'.$course->thumbnail)) : 'https://via.placeholder.com/600x450';
                @endphp
                <img src="{{ $src }}" class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-700">
                <div class="absolute inset-0 bg-gradient-to-t from-navy/80 to-transparent opacity-60"></div>
                <span class="absolute top-6 left-6 bg-primary text-customWhite text-[10px] font-black uppercase px-4 py-1.5 rounded-full tracking-widest shadow-xl">Course</span>
            </div>

            <div class="p-8 flex flex-col flex-1">
                <h3 class="text-xl font-bold text-mainText tracking-tight uppercase mb-8 group-hover:text-primary transition-colors leading-tight">{{ $course->title }}</h3>

                <div class="mt-auto pt-6 border-t border-primary/10 flex items-center justify-between">
                    <div>
                        <p class="text-[10px] font-bold text-mutedText uppercase mb-1 tracking-widest">Investment</p>
                        <p class="text-3xl font-black text-primary tracking-tighter">₹{{ number_format($course->final_price ?? $course->price, 0) }}</p>
                    </div>
                    <a href="{{ route('student.courses.show', $course->id) }}" class="w-14 h-14 rounded-2xl bg-navy flex items-center justify-center text-customWhite shadow-lg hover:brand-gradient transition-all group/btn">
                        <i class="fas fa-arrow-right transition-transform group-hover/btn:translate-x-1"></i>
                    </a>
                </div>
            </div>
        </div>
        @endforeach
    </div>
</div>

<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('productSelection', () => ({
            referralCode: '{{ $referrer->referral_code ?? "" }}',
            message: '',
            status: '',
            loading: false,

            async applyReferral() {
                if(!this.referralCode.trim()) {
                    this.status = 'error';
                    this.message = 'Please enter a code';
                    return;
                }

                this.loading = true;
                this.message = '';

                try {
                    const response = await fetch("{{ route('student.apply_referral') }}", {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                            'Accept': 'application/json'
                        },
                        body: JSON.stringify({ referral_code: this.referralCode })
                    });

                    const data = await response.json();

                    if(response.ok && data.success) {
                        this.status = 'success';
                        this.message = `Code Applied Successfully!`;
                        setTimeout(() => window.location.reload(), 800);
                    } else {
                        this.status = 'error';
                        this.message = data.message || 'Invalid Referral Code';
                    }
                } catch (error) {
                    this.status = 'error';
                    this.message = 'Connection Error';
                } finally {
                    this.loading = false;
                }
            }
        }));
    });
</script>
@endsection
