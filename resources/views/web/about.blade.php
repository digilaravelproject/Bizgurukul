@extends('web.layouts.app')

@section('title', 'Our Story | ' . config('app.name', 'Skills Pehle'))

@push('styles')
<style>
    .glow-blob {
        position: absolute;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(var(--color-primary-rgb), 0.15) 0%, transparent 70%);
        filter: blur(60px);
        z-index: 0;
    }
</style>
@endpush

@section('content')
<div class="relative overflow-hidden bg-navy text-mainText font-sans">

    <section class="relative pt-24 pb-20 lg:pt-32 lg:pb-32 overflow-hidden">
        <div class="glow-blob -top-20 -right-20"></div>
        <div class="glow-blob bottom-0 -left-20" style="rgba(var(--color-secondary-rgb), 0.1)"></div>
        <div class="absolute inset-0 z-0 opacity-[0.03]" style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 30px 30px;"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <div class="text-center max-w-3xl mx-auto">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-secondary font-bold text-xs tracking-widest uppercase mb-8">
                    Our Identity
                </div>
                <h1 class="text-5xl md:text-7xl font-black tracking-tight text-white mb-8 leading-tight">
                    Beyond Just <br/>
                    <span class="text-transparent bg-clip-text brand-gradient">Education.</span>
                </h1>
                <p class="text-xl text-mutedText leading-relaxed font-medium">
                    Skills Pehle was born out of a simple realization: the traditional education system is moving too slow for the digital age. We are here to bridge that gap.
                </p>
            </div>
        </div>
    </section>

    <section class="py-20 bg-surface relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-2 gap-12">
                <div class="p-10 rounded-[2.5rem] bg-navy border border-white/5 hover:border-primary/30 transition-all duration-500 group">
                    <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center text-primary mb-8 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h2 class="text-3xl font-black text-white mb-4">Our Mission</h2>
                    <p class="text-mutedText text-lg leading-relaxed">
                        To empower millions of learners worldwide with high-income skills directly applicable to real-world scenarios. We focus on results, not just certificates.
                    </p>
                </div>

                <div class="p-10 rounded-[2.5rem] bg-navy border border-white/5 hover:border-secondary/30 transition-all duration-500 group">
                    <div class="w-14 h-14 bg-secondary/10 rounded-2xl flex items-center justify-center text-secondary mb-8 group-hover:scale-110 transition-transform">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                    </div>
                    <h2 class="text-3xl font-black text-white mb-4">Our Vision</h2>
                    <p class="text-mutedText text-lg leading-relaxed">
                        To be the leading global platform recognizing the needs of rapid technological shifts and preparing the workforce of tomorrow, today.
                    </p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-navy">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-16">
                <h2 class="text-4xl font-black text-white mb-4">The "Skills Pehle" Way</h2>
                <p class="text-mutedText font-medium">The principles that drive every course we build.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="text-center p-6">
                    <div class="text-5xl font-black text-primary/20 mb-4">01</div>
                    <h3 class="text-xl font-bold text-white mb-2">Practicality First</h3>
                    <p class="text-mutedText text-sm">No fluff. No unnecessary history. We teach exactly what you need to do the job.</p>
                </div>
                <div class="text-center p-6">
                    <div class="text-5xl font-black text-secondary/20 mb-4">02</div>
                    <h3 class="text-xl font-bold text-white mb-2">Mentor Driven</h3>
                    <p class="text-mutedText text-sm">Learn from people who are actually doing the work at the highest levels.</p>
                </div>
                <div class="text-center p-6">
                    <div class="text-5xl font-black text-primary/20 mb-4">03</div>
                    <h3 class="text-xl font-bold text-white mb-2">Community Led</h3>
                    <p class="text-mutedText text-sm">You're not alone. Join a network of thousands of ambitious learners.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-surface border-t border-white/5">
        <div class="max-w-4xl mx-auto px-4 text-center">
            <h2 class="text-3xl md:text-4xl font-black text-white mb-8">Want to see what we're building?</h2>
            <div class="flex flex-col sm:flex-row justify-center gap-4">
                <a href="{{ route('home') }}#courses" class="px-8 py-4 rounded-xl brand-gradient text-white font-bold hover:shadow-lg hover:shadow-primary/30 transition-all">
                    Explore Our Catalog
                </a>
                <a href="{{ route('web.contact') }}" class="px-8 py-4 rounded-xl bg-navy text-white font-bold border border-white/10 hover:bg-navy/50 transition-all">
                    Get In Touch
                </a>
            </div>
        </div>
    </section>

</div>
@endsection
