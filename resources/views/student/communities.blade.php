@extends('layouts.user.app')

@section('title', 'Community Hub | ' . config('app.name', 'Skills Pehle'))

@push('styles')
<style>
    /* Premium Animations */
    .stagger-1 { animation: fadeUp 0.6s ease-out 0.1s both; }
    .stagger-2 { animation: fadeUp 0.6s ease-out 0.2s both; }
    .stagger-3 { animation: fadeUp 0.6s ease-out 0.3s both; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Soft shadow for premium feel */
    .premium-shadow {
        box-shadow: 0 10px 40px -10px rgba(var(--color-primary) / 0.15);
    }

    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 40px -10px rgba(var(--color-primary) / 0.25);
    }
</style>
@endpush

@section('content')
<div class="space-y-4 md:space-y-8 pb-12 font-sans text-mainText">

    {{-- 1. HEADER: PREMIUM WELCOME --}}
    <div class="stagger-1 rounded-[1.5rem] md:rounded-[2.5rem] bg-surface p-5 md:p-10 border border-primary/10 relative overflow-hidden premium-shadow">
        {{-- Aesthetic Background Accents --}}
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary/5 blur-[80px] rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-secondary/5 blur-[60px] rounded-full pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-4 md:gap-8 text-center md:text-left">
            <div class="space-y-1.5 md:space-y-3">
                <div class="flex items-center justify-center md:justify-start gap-3">
                    <span class="bg-primary/10 text-primary px-3 py-1 md:px-4 md:py-1.5 rounded-full text-[10px] md:text-xs font-bold uppercase tracking-widest border border-primary/20">
                        Growing Together
                    </span>
                </div>
                <h1 class="text-2xl md:text-5xl font-black tracking-tight text-mainText leading-tight">
                    Connect with Our <br class="md:hidden"> <span class="bg-clip-text text-white brand-gradient">Vibrant Ecosystem</span>
                </h1>
                <p class="text-sm md:text-base text-mutedText max-w-2xl mt-4 font-medium">
                    Join our specialized groups and social channels to stay updated, network with peers, and get direct mentorship.
                </p>
            </div>
            <div class="hidden md:flex flex-shrink-0 animate-pulse">
                <div class="w-32 h-32 rounded-full border-4 border-primary/20 flex items-center justify-center relative">
                    <div class="absolute inset-0 rounded-full border border-primary/40 animate-[spin_10s_linear_infinite]"></div>
                    <i class="fas fa-users text-4xl text-primary"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Grouped Links --}}
    <div class="space-y-12 stagger-2">
        @foreach($communities as $groupName => $items)
            <div>
                <div class="flex items-center gap-4 mb-6">
                    <div class="h-1.5 w-8 brand-gradient rounded-full"></div>
                    <h2 class="text-xl md:text-2xl font-black text-mainText uppercase tracking-wider">{{ $groupName }}</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($items as $item)
                        <div class="bg-surface rounded-3xl p-6 border border-primary/10 hover:border-primary/40 transition-all duration-300 premium-shadow flex flex-col h-full group hover-lift relative overflow-hidden">
                            <div class="absolute -right-4 -top-4 w-20 h-20 bg-primary/5 rounded-full group-hover:scale-150 transition-transform duration-500 pointer-events-none"></div>

                            <div class="flex items-start justify-between mb-6 relative z-10">
                                <div class="w-14 h-14 bg-navy rounded-2xl flex items-center justify-center border border-primary/10 group-hover:bg-primary/10 transition-colors shadow-inner">
                                    @php
                                        $iconName = strtolower($item->name);
                                        $icon = match(true) {
                                            str_contains($iconName, 'whatsapp') => 'fab fa-whatsapp text-green-500',
                                            str_contains($iconName, 'telegram') => 'fab fa-telegram-plane text-blue-500',
                                            str_contains($iconName, 'instagram') => 'fab fa-instagram text-pink-500',
                                            str_contains($iconName, 'youtube') => 'fab fa-youtube text-red-600',
                                            str_contains($iconName, 'facebook') => 'fab fa-facebook-f text-blue-600',
                                            str_contains($iconName, 'twitter') || str_contains($iconName, ' x') => 'fab fa-twitter text-blue-400',
                                            str_contains($iconName, 'linkedin') => 'fab fa-linkedin-in text-blue-700',
                                            str_contains($iconName, 'discord') => 'fab fa-discord text-indigo-500',
                                            default => 'fas fa-external-link-alt text-primary'
                                        };
                                    @endphp
                                    <i class="{{ $icon }} text-2xl group-hover:scale-110 transition-transform duration-500"></i>
                                </div>
                            </div>

                            <h3 class="text-lg font-bold text-mainText mb-2 group-hover:text-primary transition-colors relative z-10">{{ $item->name }}</h3>
                            <p class="text-mutedText text-xs leading-relaxed mb-6 flex-grow relative z-10 font-medium">
                                {{ $item->description ?? 'Join our community for the latest updates and exclusive resources.' }}
                            </p>

                            <a href="{{ $item->link }}" target="_blank"
                                class="w-full h-12 rounded-xl bg-navy border border-primary/20 group-hover:brand-gradient flex items-center justify-center text-mainText group-hover:text-white font-bold text-xs uppercase tracking-widest transition-all duration-300 relative z-10">
                                {{ $item->button_text ?? 'Join Now' }} <i class="fas fa-arrow-right ml-2 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- Support CTA --}}
    <div class="stagger-3 mt-12 bg-surface rounded-[2.5rem] border border-primary/10 premium-shadow overflow-hidden relative">
        <div class="absolute top-0 left-0 w-full h-1 brand-gradient"></div>
        <div class="p-8 md:p-12 text-center max-w-2xl mx-auto relative z-10">
            <div class="w-16 h-16 mx-auto bg-primary/10 rounded-full flex items-center justify-center text-primary text-2xl mb-6">
                <i class="fas fa-headset"></i>
            </div>
            <h3 class="text-2xl font-black text-mainText mb-4">Need Help Getting Connected?</h3>
            <p class="text-mutedText text-sm mb-8">If you're facing any issues joining our communities or channels, our support team is ready to assist you.</p>
            <a href="{{ route('web.contact') }}" class="inline-flex items-center gap-3 px-8 py-4 bg-navy border border-primary/20 text-mainText font-black text-xs uppercase tracking-widest rounded-xl hover:bg-primary/5 hover:border-primary/50 transition-all duration-300">
                Contact Support
            </a>
        </div>
    </div>

</div>
@endsection
