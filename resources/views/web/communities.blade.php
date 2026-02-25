@extends('web.layouts.app')

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
{{-- Added py-6 md:py-12 for overall vertical breathing room --}}
<div class="space-y-8 md:space-y-16 py-6 md:py-12 pb-20 font-sans text-mainText px-4 md:px-6">

    {{-- 1. HEADER: PREMIUM WELCOME --}}
    {{-- Increased padding to p-8 md:p-16 --}}
    <div class="stagger-1 rounded-[1.5rem] md:rounded-[3rem] bg-surface p-8 md:p-16 border border-primary/10 relative overflow-hidden premium-shadow">
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary/5 blur-[80px] rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-secondary/5 blur-[60px] rounded-full pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8 md:gap-12 text-center md:text-left">
            <div class="space-y-4 md:space-y-6">
                <div class="flex items-center justify-center md:justify-start gap-3">
                    <span class="bg-primary/10 text-primary px-4 py-2 rounded-full text-[10px] md:text-xs font-bold uppercase tracking-widest border border-primary/20">
                        Growing Together
                    </span>
                </div>
                <h1 class="text-3xl md:text-6xl font-black tracking-tight text-mainText leading-tight">
                    Connect with Our <br class="md:hidden"> <span class="bg-clip-text text-white brand-gradient px-2">Vibrant Ecosystem</span>
                </h1>
                <p class="text-base md:text-lg text-mutedText max-w-2xl mt-4 font-medium leading-relaxed">
                    Join our specialized groups and social channels to stay updated, network with peers, and get direct mentorship.
                </p>
            </div>
            <div class="hidden lg:flex flex-shrink-0">
                <div class="w-40 h-40 rounded-full border-4 border-primary/10 flex items-center justify-center relative">
                    <div class="absolute inset-0 rounded-full border-2 border-dashed border-primary/30 animate-[spin_20s_linear_infinite]"></div>
                    <i class="fas fa-users text-5xl text-primary animate-bounce"></i>
                </div>
            </div>
        </div>
    </div>

    {{-- Grouped Links --}}
    <div class="space-y-16 stagger-2">
        @foreach($communities as $groupName => $items)
            <div>
                {{-- More margin-bottom for the heading --}}
                <div class="flex items-center gap-4 mb-8">
                    <div class="h-1.5 w-10 brand-gradient rounded-full"></div>
                    <h2 class="text-xl md:text-3xl font-black text-mainText uppercase tracking-wider">{{ $groupName }}</h2>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                    @foreach($items as $item)
                        {{-- Increased internal padding to p-8 --}}
                        <div class="bg-surface rounded-[2rem] p-8 border border-primary/10 hover:border-primary/40 transition-all duration-300 premium-shadow flex flex-col h-full group hover-lift relative overflow-hidden">
                            <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary/5 rounded-full group-hover:scale-150 transition-transform duration-500 pointer-events-none"></div>

                            <div class="flex items-start justify-between mb-8 relative z-10">
                                <div class="w-16 h-16 bg-navy rounded-2xl flex items-center justify-center border border-primary/10 group-hover:bg-primary/5 transition-colors shadow-inner">
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
                                    <i class="{{ $icon }} text-3xl group-hover:scale-110 transition-transform duration-500"></i>
                                </div>
                            </div>

                            <h3 class="text-xl font-bold text-mainText mb-3 group-hover:text-primary transition-colors relative z-10">{{ $item->name }}</h3>
                            <p class="text-mutedText text-sm leading-relaxed mb-8 flex-grow relative z-10 font-medium">
                                {{ $item->description ?? 'Join our community for the latest updates and exclusive resources.' }}
                            </p>

                            <a href="{{ $item->link }}" target="_blank"
                                class="w-full h-14 rounded-xl bg-navy border border-primary/20 group-hover:brand-gradient flex items-center justify-center text-mainText group-hover:text-white font-bold text-xs uppercase tracking-widest transition-all duration-300 relative z-10 shadow-lg">
                                {{ $item->button_text ?? 'Join Now' }} <i class="fas fa-arrow-right ml-2 opacity-0 group-hover:opacity-100 group-hover:translate-x-1 transition-all"></i>
                            </a>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    </div>

    {{-- Support CTA --}}
    <div class="stagger-3 mt-16 bg-surface rounded-[3rem] border border-primary/10 premium-shadow overflow-hidden relative">
        <div class="absolute top-0 left-0 w-full h-1.5 brand-gradient"></div>
        <div class="p-10 md:p-20 text-center max-w-3xl mx-auto relative z-10">
            <div class="w-20 h-20 mx-auto bg-primary/10 rounded-full flex items-center justify-center text-primary text-3xl mb-8">
                <i class="fas fa-headset"></i>
            </div>
            <h3 class="text-3xl font-black text-mainText mb-6">Need Help Getting Connected?</h3>
            <p class="text-mutedText text-base mb-10 leading-relaxed">If you're facing any issues joining our communities or channels, our support team is ready to assist you 24/7.</p>
            <a href="{{ route('web.contact') }}" class="inline-flex items-center gap-4 px-10 py-5 bg-navy border border-primary/20 text-mainText font-black text-xs uppercase tracking-widest rounded-2xl hover:bg-primary/5 hover:border-primary/50 transition-all duration-300">
                Contact Support <i class="fas fa-chevron-right text-[10px]"></i>
            </a>
        </div>
    </div>

</div>

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
@endpush
@endsection
