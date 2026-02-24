@extends('web.layouts.app')

@section('title', config('app.name', 'Skills Pehle') . ' | The Future of Digital Learning')

@push('styles')
<style>
    /* Custom utility animations */
    .hero-float-1 { animation: float 6s ease-in-out infinite; }
    .hero-float-2 { animation: float 8s ease-in-out infinite reverse; }
    .hero-float-3 { animation: float 7s ease-in-out infinite 1s; }

    @keyframes float {
        0%, 100% { transform: translateY(0px) rotate(0deg); }
        50% { transform: translateY(-15px) rotate(1deg); }
    }
</style>
@endpush

@section('content')
<div class="relative overflow-hidden bg-navy text-mainText font-sans" x-data="homepageConfig()">

    <section class="relative pt-24 pb-20 lg:pt-32 lg:pb-32 overflow-hidden bg-navy">
        <div class="absolute inset-0 z-0 opacity-[0.04]" style="background-image: radial-gradient(rgb(var(--color-primary)) 1px, transparent 1px); background-size: 40px 40px;"></div>
        <div class="absolute inset-0 overflow-hidden pointer-events-none z-0">
            <div class="absolute -top-[10%] -right-[5%] w-[40%] h-[50%] bg-primary/20 rounded-full blur-[120px]"></div>
            <div class="absolute top-[30%] -left-[10%] w-[30%] h-[40%] bg-secondary/15 rounded-full blur-[100px]"></div>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 flex flex-col lg:flex-row items-center gap-16">
            <div class="w-full lg:w-1/2 text-left animate-fade-in-down z-20">
                <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-secondary font-bold text-xs tracking-widest uppercase mb-8 shadow-sm">
                    <span class="relative flex h-2.5 w-2.5">
                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                        <span class="relative inline-flex rounded-full h-2.5 w-2.5 bg-primary"></span>
                    </span>
                    Premium Ed-Tech Platform
                </div>

                <h1 class="text-5xl md:text-6xl lg:text-7xl font-black tracking-tight text-mainText mb-6 leading-[1.15]">
                    Unlock Your True <br/>
                    <span class="text-white bg-clip-text brand-gradient">Potential Today.</span>
                </h1>

                <p class="text-lg text-mutedText mb-10 max-w-lg leading-relaxed font-medium">
                    Master high-income skills with industry-vetted courses, dedicated mentors, and a community driven to succeed.
                </p>

                <div class="flex flex-col sm:flex-row gap-4 mb-12">
                    <a href="#courses" class="px-8 py-4 rounded-xl brand-gradient text-white font-bold hover:shadow-lg hover:shadow-primary/30 hover:-translate-y-0.5 transition-all duration-300 text-center flex items-center justify-center gap-2">
                        Explore Catalog
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </a>
                    <a href="#how-it-works" class="px-8 py-4 rounded-xl bg-surface text-mainText font-bold border border-gray-200 hover:border-primary/50 hover:bg-gray-50 transition-all duration-300 flex items-center justify-center gap-2 shadow-sm">
                        <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" /></svg>
                        How it Works
                    </a>
                </div>

                <div class="flex items-center gap-5">
                    <div class="flex -space-x-3">
                        <img class="w-10 h-10 rounded-full border-2 border-surface object-cover" src="https://images.unsplash.com/photo-1534528741775-53994a69daeb?q=80&w=150&auto=format&fit=crop" alt="Student">
                        <img class="w-10 h-10 rounded-full border-2 border-surface object-cover" src="https://images.unsplash.com/photo-1506794778202-cad84cf45f1d?q=80&w=150&auto=format&fit=crop" alt="Student">
                        <img class="w-10 h-10 rounded-full border-2 border-surface object-cover" src="https://images.unsplash.com/photo-1494790108377-be9c29b29330?q=80&w=150&auto=format&fit=crop" alt="Student">
                        <div class="w-10 h-10 rounded-full border-2 border-surface bg-primary/10 flex items-center justify-center text-xs font-bold text-primary shrink-0">
                            12k+
                        </div>
                    </div>
                    <div class="text-sm">
                        <div class="flex gap-1 text-yellow-500 mb-0.5">
                            @for($i=0; $i<5; $i++) <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118z"/></svg> @endfor
                        </div>
                        <span class="font-bold text-mainText">4.9/5</span> <span class="text-mutedText">from reviews</span>
                    </div>
                </div>
            </div>

            <div class="w-full lg:w-1/2 relative h-[500px] hidden md:block">
                <div class="absolute inset-0 rounded-[2rem] overflow-hidden bg-surface shadow-2xl shadow-primary/10 z-10 p-2 transform rotate-2 hover:rotate-0 transition-all duration-700 border border-white">
                    <img src="https://images.unsplash.com/photo-1522202176988-66273c2fd55f?q=80&w=1000&auto=format&fit=crop" alt="Platform Overview" class="w-full h-full object-cover rounded-[1.5rem]">
                    <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent rounded-[1.5rem]"></div>
                </div>

                <div class="absolute top-8 -left-8 z-20 hero-float-1 bg-surface p-4 rounded-2xl shadow-xl shadow-primary/5 border border-gray-100 flex items-center gap-4">
                    <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center text-green-600">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    </div>
                    <div>
                        <div class="text-[10px] text-mutedText font-bold uppercase tracking-wider">Course Completed</div>
                        <div class="text-sm font-bold text-mainText">Advanced UI Design</div>
                    </div>
                </div>

                <div class="absolute bottom-16 -right-6 z-20 hero-float-2 bg-surface p-4 rounded-2xl shadow-xl shadow-primary/5 border border-gray-100 flex items-center gap-4">
                    <div class="w-10 h-10 brand-gradient rounded-full flex items-center justify-center text-white font-bold text-lg">
                        A+
                    </div>
                    <div>
                        <div class="text-[10px] text-mutedText font-bold uppercase tracking-wider">Average Grade</div>
                        <div class="text-sm font-bold text-mainText">Top 10% Tier</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    {{-- <section class="py-10 bg-surface border-y border-gray-100 overflow-hidden relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <p class="text-xs font-bold text-mutedText uppercase tracking-widest mb-6">Trusted by students working at top tech firms</p>
            <div class="flex flex-wrap justify-center items-center gap-10 md:gap-20 opacity-50 grayscale hover:grayscale-0 transition-all duration-500">
                <svg class="h-6 md:h-8 hover:text-primary transition-colors" viewBox="0 0 100 30" fill="currentColor"><text x="0" y="22" font-family="sans-serif" font-weight="900" font-size="22">TechCorp</text></svg>
                <svg class="h-6 md:h-8 hover:text-primary transition-colors" viewBox="0 0 100 30" fill="currentColor"><text x="0" y="22" font-family="sans-serif" font-weight="900" font-size="22">Innovate.io</text></svg>
                <svg class="h-6 md:h-8 hover:text-primary transition-colors" viewBox="0 0 100 30" fill="currentColor"><text x="0" y="22" font-family="sans-serif" font-weight="900" font-size="22">GlobalBrands</text></svg>
                <svg class="h-6 md:h-8 hover:text-primary transition-colors" viewBox="0 0 100 30" fill="currentColor"><text x="0" y="22" font-family="sans-serif" font-weight="900" font-size="22">FutureNet</text></svg>
                <svg class="h-6 md:h-8 hidden sm:block hover:text-primary transition-colors" viewBox="0 0 100 30" fill="currentColor"><text x="0" y="22" font-family="sans-serif" font-weight="900" font-size="22">ApexSys</text></svg>
            </div>
        </div>
    </section> --}}

    <section class="py-16 bg-surface relative" x-data="statsCounter()" x-intersect.once="startCounters()">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-2 md:grid-cols-4 gap-8 divide-x divide-gray-100 bg-navy rounded-3xl p-8 border border-primary/10">
                <div class="text-center px-4">
                    <div class="text-4xl md:text-5xl font-black text-white bg-clip-text brand-gradient mb-1" x-text="stats.students + '+'"></div>
                    <div class="text-xs font-bold text-mutedText uppercase tracking-wider">Enrolled Students</div>
                </div>
                <div class="text-center px-4">
                    <div class="text-4xl md:text-5xl font-black text-white bg-clip-text brand-gradient mb-1" x-text="stats.courses + '+'"></div>
                    <div class="text-xs font-bold text-mutedText uppercase tracking-wider">Premium Courses</div>
                </div>
                <div class="text-center px-4">
                    <div class="text-4xl md:text-5xl font-black text-white bg-clip-text brand-gradient mb-1" x-text="stats.reviews + 'k'"></div>
                    <div class="text-xs font-bold text-mutedText uppercase tracking-wider">5-Star Reviews</div>
                </div>
                <div class="text-center px-4">
                    <div class="text-4xl md:text-5xl font-black text-white bg-clip-text brand-gradient mb-1" x-text="stats.years + '+'"></div>
                    <div class="text-xs font-bold text-mutedText uppercase tracking-wider">Years Experience</div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-navy relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-black text-mainText mb-4">Why Top Performers Choose Us</h2>
                <p class="text-lg text-mutedText">We've engineered an ecosystem focused entirely on your practical, real-world success.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                <div class="bg-surface border border-gray-100 rounded-2xl p-8 hover:-translate-y-2 transition-all duration-300 hover:shadow-xl hover:shadow-primary/10 group">
                    <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-6 text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-mainText mb-2 group-hover:text-primary transition-colors">Lifetime Access</h3>
                    <p class="text-mutedText text-sm leading-relaxed">Learn at your own pace. Return to the material anytime, anywhere, with all future updates included.</p>
                </div>

                <div class="bg-surface border border-gray-100 rounded-2xl p-8 hover:-translate-y-2 transition-all duration-300 hover:shadow-xl hover:shadow-primary/10 group">
                    <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-6 text-primary group-hover:bg-secondary group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4L19 7"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-mainText mb-2 group-hover:text-secondary transition-colors">Industry Certified</h3>
                    <p class="text-mutedText text-sm leading-relaxed">Earn verifiable, blockchain-backed certificates that instantly boost your resume and LinkedIn profile.</p>
                </div>

                <div class="bg-surface border border-gray-100 rounded-2xl p-8 hover:-translate-y-2 transition-all duration-300 hover:shadow-xl hover:shadow-primary/10 group">
                    <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-6 text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-mainText mb-2 group-hover:text-primary transition-colors">Expert Instructors</h3>
                    <p class="text-mutedText text-sm leading-relaxed">No pure theorists. You're learning strictly from practitioners working at top 1% tech companies.</p>
                </div>

                <div class="bg-surface border border-gray-100 rounded-2xl p-8 hover:-translate-y-2 transition-all duration-300 hover:shadow-xl hover:shadow-primary/10 group">
                    <div class="w-12 h-12 bg-primary/10 rounded-xl flex items-center justify-center mb-6 text-primary group-hover:bg-secondary group-hover:text-white transition-colors duration-300">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                    </div>
                    <h3 class="text-lg font-bold text-mainText mb-2 group-hover:text-secondary transition-colors">24/7 Support</h3>
                    <p class="text-mutedText text-sm leading-relaxed">Join elite private groups to network, get immediate problem-solving help, and collaborate.</p>
                </div>
            </div>
        </div>
    </section>

    <section id="how-it-works" class="py-20 bg-surface overflow-hidden relative border-y border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-3xl mx-auto mb-16">
                <h2 class="text-3xl md:text-4xl font-black text-mainText mb-4">Your Path to Mastery</h2>
                <p class="text-lg text-mutedText">Four simple steps separating you from your potential future.</p>
            </div>

            <div class="relative mt-10">
                <div class="hidden md:block absolute top-10 left-[10%] right-[10%] h-0.5 brand-gradient opacity-30"></div>

                <div class="grid grid-cols-1 md:grid-cols-4 gap-10 relative z-10">
                    <div class="text-center relative group">
                        <div class="w-20 h-20 mx-auto bg-surface rounded-full flex items-center justify-center border-4 border-navy group-hover:border-primary shadow-lg mb-4 relative z-10 font-black text-2xl text-primary transition-all duration-300 group-hover:scale-110">1</div>
                        <h3 class="text-lg font-bold text-mainText mb-1">Sign Up</h3>
                        <p class="text-mutedText text-sm">Create your free account instantly.</p>
                    </div>
                    <div class="text-center relative group md:translate-y-6">
                        <div class="w-20 h-20 mx-auto bg-surface rounded-full flex items-center justify-center border-4 border-navy group-hover:border-secondary shadow-lg mb-4 relative z-10 font-black text-2xl text-secondary transition-all duration-300 group-hover:scale-110">2</div>
                        <h3 class="text-lg font-bold text-mainText mb-1">Choose Course</h3>
                        <p class="text-mutedText text-sm">Select the bundle that fits your goals.</p>
                    </div>
                    <div class="text-center relative group">
                        <div class="w-20 h-20 mx-auto brand-gradient rounded-full flex items-center justify-center border-4 border-navy shadow-lg shadow-primary/30 mb-4 relative z-10 font-black text-2xl text-white transition-all duration-300 group-hover:scale-110">3</div>
                        <h3 class="text-lg font-bold text-mainText mb-1">Learn & Practice</h3>
                        <p class="text-mutedText text-sm">Watch videos and master the skill.</p>
                    </div>
                    <div class="text-center relative group md:translate-y-6">
                        <div class="w-20 h-20 mx-auto bg-surface rounded-full flex items-center justify-center border-4 border-navy group-hover:border-primary shadow-lg mb-4 relative z-10 font-black text-2xl text-primary transition-all duration-300 group-hover:scale-110">4</div>
                        <h3 class="text-lg font-bold text-mainText mb-1">Get Certified</h3>
                        <p class="text-mutedText text-sm">Pass the exam and start earning.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-navy relative">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-end mb-12 gap-6">
                <div>
                    <h2 class="text-3xl md:text-4xl font-black text-mainText mb-4">Ultimate Mastery Bundles</h2>
                    <p class="text-lg text-mutedText max-w-2xl">Packaged for maximum value. Get the complete roadmap instead of just building blocks.</p>
                </div>
                <div class="hidden md:block">
                    <span class="px-4 py-2 bg-red-100 text-red-600 font-bold rounded-lg text-sm animate-pulse flex items-center gap-2">
                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.395 2.553a1 1 0 00-1.45-.385c-.345.23-.614.558-.822.88-.214.33-.403.713-.57 1.116-.334.804-.614 1.768-.84 2.734a31.365 31.365 0 00-.613 3.58 2.64 2.64 0 01-.945-1.067c-.328-.68-.398-1.534-.398-2.654A1 1 0 005.05 6.05 6.981 6.981 0 003 11a7 7 0 1011.95-4.95c-.592-.591-.98-.985-1.348-1.467-.363-.476-.724-1.063-1.207-2.03zM12.12 15.12A3 3 0 017 13s.879.5 2.5.5c0-1 .5-4 1.25-4.5.5 1 .786 1.293 1.371 1.879A2.99 2.99 0 0113 13a2.99 2.99 0 01-.879 2.121z" clip-rule="evenodd"/></svg>
                        Save up to 40%
                    </span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 items-center">
                @forelse($bundles as $index => $bundle)
                    <div class="rounded-3xl border {{ $index === 1 ? 'border-primary shadow-2xl shadow-primary/20 bg-surface lg:-translate-y-4 ring-4 ring-primary/10' : 'border-gray-200 hover:border-primary/50 group bg-surface hover:shadow-xl' }} p-8 transition-all duration-300 flex flex-col relative overflow-hidden">
                        @if($index === 1)
                        <div class="absolute top-0 right-0 brand-gradient text-white text-[10px] font-bold px-4 py-1.5 rounded-bl-xl uppercase tracking-widest z-10 shadow-sm">
                            Best Value
                        </div>
                        <div class="absolute -top-10 -right-10 w-32 h-32 bg-primary/10 rounded-full blur-2xl z-0 pointer-events-none"></div>
                        @endif

                        <div class="mb-6 relative z-10">
                            <span class="inline-block px-3 py-1 {{ $index === 1 ? 'bg-primary/10 text-primary' : 'bg-gray-100 text-mutedText group-hover:text-primary group-hover:bg-primary/10 transition-colors' }} font-bold text-xs uppercase tracking-wider rounded-md mb-4">{{ $index === 1 ? 'Pro Mastery' : 'Starter Pack' }}</span>
                            <h3 class="text-2xl font-black text-mainText mb-2">{{ $bundle->title }}</h3>
                            <p class="text-mutedText text-sm line-clamp-2 leading-relaxed">{{ Str::limit(strip_tags($bundle->description), 80) }}</p>
                        </div>

                        <div class="mb-8 flex items-end gap-2 relative z-10">
                            <span class="text-4xl font-black text-mainText">₹{{ number_format($bundle->final_price) }}</span>
                            @if($bundle->website_price > $bundle->final_price)
                                <span class="text-lg text-gray-400 line-through mb-1">₹{{ number_format($bundle->website_price) }}</span>
                            @endif
                        </div>

                        <ul class="space-y-4 mb-8 flex-grow relative z-10">
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 {{ $index === 1 ? 'text-primary' : 'text-green-500 group-hover:text-primary transition-colors' }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span class="text-mainText text-sm font-medium">Access to all bundle features</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 {{ $index === 1 ? 'text-primary' : 'text-green-500 group-hover:text-primary transition-colors' }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span class="text-mainText text-sm font-medium">Comprehensive Curriculum</span>
                            </li>
                            <li class="flex items-center gap-3">
                                <svg class="w-5 h-5 {{ $index === 1 ? 'text-primary' : 'text-green-500 group-hover:text-primary transition-colors' }}" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                <span class="text-mainText text-sm font-medium">Premium Mentorship</span>
                            </li>
                        </ul>

                        <a href="{{ route('bundles.show', $bundle->slug ?? $bundle->id) }}" class="w-full block py-4 text-center rounded-xl font-bold transition-all duration-300 mt-auto relative z-10 {{ $index === 1 ? 'brand-gradient text-white hover:shadow-lg hover:shadow-primary/30 hover:-translate-y-1' : 'bg-gray-50 text-mainText border border-gray-200 group-hover:bg-primary group-hover:text-white group-hover:border-primary group-hover:-translate-y-1' }}">
                            Get Started
                        </a>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 bg-surface rounded-2xl border border-gray-100">
                        <p class="text-mutedText text-lg">New bundles are currently being crafted. Check back soon!</p>
                    </div>
                @endforelse
            </div>
        </div>
    </section>

    <section class="py-24 bg-surface border-y border-gray-100" id="courses" x-data="{ activeTab: 'all' }">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-12">
                <h2 class="text-3xl md:text-4xl font-black text-mainText mb-4">Explore Our Catalog</h2>
                <p class="text-lg text-mutedText max-w-2xl mx-auto">Specific skills to solve specific problems. Choose your path below.</p>
            </div>

            @php
                $uniqueCategories = $courses->pluck('category.name')->filter()->unique()->map(function($item) { return strtolower($item); });
            @endphp
            <div class="flex flex-wrap justify-center gap-3 mb-12">
                <button @click="activeTab = 'all'" :class="{ 'bg-primary text-white shadow-md ring-2 ring-primary/20': activeTab === 'all', 'bg-white text-mainText border border-gray-200 hover:border-primary/50': activeTab !== 'all' }" class="px-5 py-2.5 rounded-full font-bold transition-all text-sm tracking-wide">All Courses</button>
                @foreach($uniqueCategories as $cat)
                <button @click="activeTab = '{{ $cat }}'" :class="{ 'bg-primary text-white shadow-md ring-2 ring-primary/20': activeTab === '{{ $cat }}', 'bg-white text-mainText border border-gray-200 hover:border-primary/50': activeTab !== '{{ $cat }}' }" class="px-5 py-2.5 rounded-full font-bold transition-all text-sm tracking-wide capitalize">{{ $cat }}</button>
                @endforeach
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                @forelse($courses as $course)
                    @php
                        $catName = strtolower($course->category->name ?? 'uncategorized');
                        // Fallback unsplash matching tags based on category
                        $imageTag = match(true) {
                            str_contains($catName, 'design') => 'photo-1561070791-2526d30994b5',
                            str_contains($catName, 'marketing') => 'photo-1460925895917-afdab827c52f',
                            str_contains($catName, 'code') || str_contains($catName, 'dev') => 'photo-1498050108023-c5249f4df085',
                            default => 'photo-1516321318423-f06f85e504b3'
                        };
                    @endphp
                    <div x-show="activeTab === 'all' || activeTab === '{{ $catName }}'" x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100" class="bg-white rounded-2xl overflow-hidden shadow-sm hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 border border-gray-100 flex flex-col group hover:-translate-y-1.5">
                        <div class="relative h-48 overflow-hidden bg-gray-100">
                            <img src="{{ $course->thumbnail_url ?? 'https://images.unsplash.com/'.$imageTag.'?q=80&w=600&auto=format&fit=crop' }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                            <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-0 group-hover:opacity-100 transition-opacity duration-300 flex items-end justify-center pb-6">
                                <span class="bg-primary text-white text-xs font-bold px-4 py-2 rounded-full transform translate-y-4 group-hover:translate-y-0 transition-all duration-300 shadow-lg">Preview Course</span>
                            </div>
                            <div class="absolute top-3 right-3 bg-white/95 backdrop-blur-md px-2.5 py-1 rounded-md text-[10px] font-black text-secondary shadow-sm uppercase tracking-wider">{{ $course->subCategory->name ?? 'All Levels' }}</div>
                        </div>
                        <div class="p-5 flex flex-col flex-grow relative">
                            <div class="flex items-center gap-1.5 mb-3 text-xs text-mutedText font-bold bg-navy w-max px-2 py-1 rounded-md border border-gray-100">
                                <span class="flex items-center gap-1 text-yellow-500"><svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>4.9</span>
                            </div>
                            <h3 class="text-lg font-bold text-mainText mb-2 group-hover:text-primary transition-colors line-clamp-2 leading-tight">{{ $course->title }}</h3>
                            <p class="text-sm text-mutedText mb-6 line-clamp-2 leading-relaxed">{{ Str::limit(strip_tags($course->description), 80) }}</p>
                            <div class="mt-auto pt-4 border-t border-gray-100 flex items-center justify-between">
                                <div class="flex flex-col">
                                    <span class="text-[10px] text-mutedText uppercase tracking-wider font-bold mb-0.5">Enrollment</span>
                                    <span class="text-xl font-black text-mainText">₹{{ number_format($course->final_price) }}</span>
                                </div>
                                <a href="{{ route('course.show', $course->slug ?? $course->id) }}" class="w-10 h-10 rounded-full bg-primary/10 text-primary flex items-center justify-center group-hover:bg-primary group-hover:text-white transition-all duration-300">
                                    <svg class="w-4 h-4 transform group-hover:-rotate-45 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12 bg-white rounded-2xl border border-gray-100">
                        <p class="text-mutedText text-lg">No courses available at the moment.</p>
                    </div>
                @endforelse
            </div>

            <div class="mt-14 text-center">
                <a href="#" class="inline-flex items-center justify-center px-8 py-3 rounded-xl border-2 border-gray-200 text-mainText font-bold hover:border-primary hover:text-primary transition-all duration-300 gap-2 group">
                    View All Courses
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>
        </div>
    </section>

    <section class="py-20 bg-navy">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-end mb-14 gap-6">
                <div class="max-w-xl">
                    <h2 class="text-3xl md:text-4xl font-black text-mainText mb-4">Meet The Experts</h2>
                    <p class="text-lg text-mutedText mb-0">Learn exclusively from practitioners who have generated millions in revenue and managed top tier products.</p>
                </div>
                <a href="#" class="text-primary font-bold hover:text-secondary transition-colors underline decoration-2 underline-offset-4">See all instructors</a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-8">
                <div class="text-center group bg-surface p-6 rounded-2xl border border-gray-100 hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 hover:-translate-y-1">
                    <div class="relative w-32 h-32 mx-auto mb-5 rounded-full overflow-hidden border-4 border-navy shadow-inner group-hover:border-primary transition-colors duration-300">
                        <img src="https://ui-avatars.com/api/?name=R+S&size=512&background=F7941D&color=fff" alt="Instructor" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <h3 class="text-lg font-bold text-mainText mb-1">Rahul Sharma</h3>
                    <p class="text-primary font-bold text-xs uppercase tracking-wider mb-3">Performance Marketing</p>
                    <p class="text-mutedText text-sm leading-relaxed">Spent over ₹10Cr in ad spend. Helping students replicate 5x ROAS strategies.</p>
                </div>

                <div class="text-center group bg-surface p-6 rounded-2xl border border-gray-100 hover:shadow-xl hover:shadow-secondary/5 transition-all duration-300 hover:-translate-y-1">
                    <div class="relative w-32 h-32 mx-auto mb-5 rounded-full overflow-hidden border-4 border-navy shadow-inner group-hover:border-secondary transition-colors duration-300">
                        <img src="https://ui-avatars.com/api/?name=P+G&size=512&background=D04A02&color=fff" alt="Instructor" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <h3 class="text-lg font-bold text-mainText mb-1">Priya Gupta</h3>
                    <p class="text-secondary font-bold text-xs uppercase tracking-wider mb-3">UI/UX Architect</p>
                    <p class="text-mutedText text-sm leading-relaxed">Former lead designer at top startups. Master of design systems and Figma.</p>
                </div>

                <div class="text-center group bg-surface p-6 rounded-2xl border border-gray-100 hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 hover:-translate-y-1">
                    <div class="relative w-32 h-32 mx-auto mb-5 rounded-full overflow-hidden border-4 border-navy shadow-inner group-hover:border-primary transition-colors duration-300">
                        <img src="https://ui-avatars.com/api/?name=A+S&size=512&background=F7941D&color=fff" alt="Instructor" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <h3 class="text-lg font-bold text-mainText mb-1">Aman Singh</h3>
                    <p class="text-primary font-bold text-xs uppercase tracking-wider mb-3">Fullstack Engineer</p>
                    <p class="text-mutedText text-sm leading-relaxed">10+ years coding robust backends. Specializes in Laravel and enterprise patterns.</p>
                </div>

                <div class="text-center group bg-surface p-6 rounded-2xl border border-gray-100 hover:shadow-xl hover:shadow-secondary/5 transition-all duration-300 hover:-translate-y-1">
                    <div class="relative w-32 h-32 mx-auto mb-5 rounded-full overflow-hidden border-4 border-navy shadow-inner group-hover:border-secondary transition-colors duration-300">
                        <img src="https://ui-avatars.com/api/?name=R+M&size=512&background=D04A02&color=fff" alt="Instructor" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-500">
                    </div>
                    <h3 class="text-lg font-bold text-mainText mb-1">Rohit Mehra</h3>
                    <p class="text-secondary font-bold text-xs uppercase tracking-wider mb-3">Agency Scaling</p>
                    <p class="text-mutedText text-sm leading-relaxed">Scaled his digital agency to 7 figures. Now teaching the exact blueprint.</p>
                </div>
            </div>
        </div>
    </section>

    <section class="py-24 bg-surface border-y border-gray-100 relative overflow-hidden" x-data="{ activeSlide: 0, slides: [0, 1, 2] }">
        <div class="absolute right-0 top-0 w-1/3 h-full brand-gradient opacity-[0.03] rounded-l-full blur-3xl pointer-events-none"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
            <h2 class="text-3xl md:text-4xl font-black text-mainText text-center mb-12">Stories from Our Graduates</h2>

            <div class="relative max-w-4xl mx-auto">
                <div class="overflow-hidden pb-4">
                    <div class="flex transition-transform duration-500 ease-in-out" :style="'transform: translateX(-' + (activeSlide * 100) + '%)'">

                        <div class="w-full flex-shrink-0 px-4">
                            <div class="bg-navy rounded-3xl p-8 md:p-10 border border-primary/10 flex flex-col md:flex-row gap-8 items-center shadow-lg shadow-primary/5">
                                <img src="https://images.unsplash.com/photo-1573496359142-b8d87734a5a2?q=80&w=200&auto=format&fit=crop" alt="User" class="w-28 h-28 rounded-full shadow-md shrink-0 object-cover border-4 border-white">
                                <div>
                                    <div class="flex gap-1 text-yellow-400 mb-3">
                                        @for($i=0; $i<5; $i++) <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118z"/></svg> @endfor
                                    </div>
                                    <p class="text-lg md:text-xl text-mainText font-medium italic mb-6 leading-relaxed">"Before the Pro Mastery bundle, my freelancing career was stagnant. The advanced UI lessons completely flipped my portfolio. Landed a high-paying remote US client the following month!"</p>
                                    <h4 class="font-bold text-mainText text-lg">Kiran L.</h4>
                                    <p class="text-sm text-primary font-bold">Freelance Web Designer</p>
                                </div>
                            </div>
                        </div>

                        <div class="w-full flex-shrink-0 px-4">
                            <div class="bg-navy rounded-3xl p-8 md:p-10 border border-primary/10 flex flex-col md:flex-row gap-8 items-center shadow-lg shadow-primary/5">
                                <img src="https://images.unsplash.com/photo-1560250097-0b93528c311a?q=80&w=200&auto=format&fit=crop" alt="User" class="w-28 h-28 rounded-full shadow-md shrink-0 object-cover border-4 border-white">
                                <div>
                                    <div class="flex gap-1 text-yellow-400 mb-3">
                                        @for($i=0; $i<5; $i++) <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118z"/></svg> @endfor
                                    </div>
                                    <p class="text-lg md:text-xl text-mainText font-medium italic mb-6 leading-relaxed">"The marketing theories out there are mostly outdated. Rahul's classes literally dissect live campaigns that are running right now. Best ROI ever."</p>
                                    <h4 class="font-bold text-mainText text-lg">Vikram P.</h4>
                                    <p class="text-sm text-primary font-bold">Marketing Agency Owner</p>
                                </div>
                            </div>
                        </div>

                        <div class="w-full flex-shrink-0 px-4">
                            <div class="bg-navy rounded-3xl p-8 md:p-10 border border-primary/10 flex flex-col md:flex-row gap-8 items-center shadow-lg shadow-primary/5">
                                <img src="https://images.unsplash.com/photo-1531427186611-ecfd6d936c79?q=80&w=200&auto=format&fit=crop" alt="User" class="w-28 h-28 rounded-full shadow-md shrink-0 object-cover border-4 border-white">
                                <div>
                                    <div class="flex gap-1 text-yellow-400 mb-3">
                                        @for($i=0; $i<5; $i++) <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118z"/></svg> @endfor
                                    </div>
                                    <p class="text-lg md:text-xl text-mainText font-medium italic mb-6 leading-relaxed">"I transitioned from a mechanical engineer to a fullstack dev using the courses here. The community Q&A is what kept me going during the hard topics."</p>
                                    <h4 class="font-bold text-mainText text-lg">Nitin A.</h4>
                                    <p class="text-sm text-primary font-bold">Junior Software Engineer</p>
                                </div>
                            </div>
                        </div>

                    </div>
                </div>

                <div class="flex justify-center gap-2.5 mt-4">
                    <template x-for="slide in slides">
                        <button @click="activeSlide = slide"
                                :class="{ 'bg-primary w-8': activeSlide === slide, 'bg-gray-300 w-2.5 hover:bg-primary/50': activeSlide !== slide }"
                                class="h-2.5 rounded-full transition-all duration-300 pointer-events-auto" aria-label="Go to slide"></button>
                    </template>
                </div>
            </div>
        </div>
    </section>

    <section class="py-20 bg-navy">
        <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-14">
                <h2 class="text-3xl md:text-4xl font-black text-mainText mb-4">Frequently Asked Questions</h2>
                <p class="text-lg text-mutedText">Everything you need to know about the product and billing.</p>
            </div>

            <div class="space-y-4" x-data="{ activeAccordion: null }">
                <div class="bg-surface border border-gray-200 rounded-2xl overflow-hidden transition-all duration-300 hover:border-primary/30" :class="{ 'shadow-lg border-primary/50 ring-2 ring-primary/5': activeAccordion === 1 }">
                    <button @click="activeAccordion = activeAccordion === 1 ? null : 1" class="w-full px-6 py-5 text-left flex justify-between items-center focus:outline-none">
                        <span class="font-bold text-mainText text-lg" :class="{ 'text-primary': activeAccordion === 1 }">Do I get access to future updates?</span>
                        <div class="w-8 h-8 rounded-full flex items-center justify-center transition-colors duration-300" :class="{ 'bg-primary/10': activeAccordion === 1, 'bg-gray-50': activeAccordion !== 1 }">
                            <svg class="w-5 h-5 text-mutedText transform transition-transform duration-300" :class="{ 'rotate-180 text-primary': activeAccordion === 1 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </button>
                    <div x-show="activeAccordion === 1" x-collapse>
                        <div class="px-6 pb-6 text-mutedText leading-relaxed">
                            Yes! Once you enroll in a course or bundle, you get lifetime access to that specific content, including all future updates, modules, and resources added to it. The digital landscape changes fast, and we keep our curriculum updated so you're never left behind.
                        </div>
                    </div>
                </div>

                <div class="bg-surface border border-gray-200 rounded-2xl overflow-hidden transition-all duration-300 hover:border-primary/30" :class="{ 'shadow-lg border-primary/50 ring-2 ring-primary/5': activeAccordion === 2 }">
                    <button @click="activeAccordion = activeAccordion === 2 ? null : 2" class="w-full px-6 py-5 text-left flex justify-between items-center focus:outline-none">
                        <span class="font-bold text-mainText text-lg" :class="{ 'text-primary': activeAccordion === 2 }">Is there a refund policy?</span>
                        <div class="w-8 h-8 rounded-full flex items-center justify-center transition-colors duration-300" :class="{ 'bg-primary/10': activeAccordion === 2, 'bg-gray-50': activeAccordion !== 2 }">
                            <svg class="w-5 h-5 text-mutedText transform transition-transform duration-300" :class="{ 'rotate-180 text-primary': activeAccordion === 2 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </button>
                    <div x-show="activeAccordion === 2" x-collapse>
                        <div class="px-6 pb-6 text-mutedText leading-relaxed">
                            Absolutely. We offer a 7-day, no-questions-asked money-back guarantee. If you log in and realize the teaching style or content isn't exactly what you need right now, just email our support team and we'll process the refund immediately.
                        </div>
                    </div>
                </div>

                <div class="bg-surface border border-gray-200 rounded-2xl overflow-hidden transition-all duration-300 hover:border-primary/30" :class="{ 'shadow-lg border-primary/50 ring-2 ring-primary/5': activeAccordion === 3 }">
                    <button @click="activeAccordion = activeAccordion === 3 ? null : 3" class="w-full px-6 py-5 text-left flex justify-between items-center focus:outline-none">
                        <span class="font-bold text-mainText text-lg" :class="{ 'text-primary': activeAccordion === 3 }">Do I need prior experience?</span>
                        <div class="w-8 h-8 rounded-full flex items-center justify-center transition-colors duration-300" :class="{ 'bg-primary/10': activeAccordion === 3, 'bg-gray-50': activeAccordion !== 3 }">
                            <svg class="w-5 h-5 text-mutedText transform transition-transform duration-300" :class="{ 'rotate-180 text-primary': activeAccordion === 3 }" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </button>
                    <div x-show="activeAccordion === 3" x-collapse>
                        <div class="px-6 pb-6 text-mutedText leading-relaxed">
                            It depends on the course track. We have "Zero to Hero" tracks designed for complete beginners, and "Masterclass" tracks meant for professionals looking to scale. Every course clearly lists its prerequisites on the details page.
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-10">
                <p class="text-mutedText font-medium">Still have questions? <a href="{{ route('web.contact') }}" class="text-primary font-bold hover:underline underline-offset-4">Contact support</a></p>
            </div>
        </div>
    </section>

    {{-- <section class="py-24 bg-surface border-t border-gray-100">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between items-end mb-14 gap-6">
                <div class="max-w-2xl">
                    <h2 class="text-3xl md:text-4xl font-black text-mainText mb-4">Insights & Resources</h2>
                    <p class="text-lg text-mutedText">Read our latest articles on tech trends, career growth, and digital strategies.</p>
                </div>
                <a href="#" class="inline-flex items-center gap-2 text-primary font-bold hover:text-secondary transition-colors group bg-primary/5 px-5 py-2.5 rounded-xl">
                    View all posts
                    <svg class="w-5 h-5 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                <article class="group relative bg-surface rounded-3xl overflow-hidden border border-gray-100 hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 flex flex-col hover:-translate-y-1.5">
                    <a href="#" class="absolute inset-0 z-10"></a>
                    <div class="relative h-52 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1522071820081-009f0129c71c?q=80&w=600&auto=format&fit=crop" alt="Blog Post" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute top-4 left-4">
                            <span class="bg-surface/95 backdrop-blur-sm px-3 py-1.5 rounded-md text-[10px] font-black uppercase tracking-wider text-mainText shadow-sm">Career</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="flex items-center gap-3 text-[11px] text-mutedText font-bold uppercase tracking-wider mb-3">
                            <span>Oct 24, 2026</span>
                            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                            <span>5 min read</span>
                        </div>
                        <h3 class="text-xl font-bold text-mainText mb-3 group-hover:text-primary transition-colors leading-snug line-clamp-2">How to Transition from Junior to Senior Developer in 12 Months</h3>
                        <p class="text-mutedText text-sm mb-6 line-clamp-2 leading-relaxed">The gap between junior and senior isn't just about syntax. It's about system design, communication, and taking ownership.</p>

                        <div class="mt-auto pt-4 border-t border-gray-100 flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name=A+S&background=F7941D&color=fff" alt="Author" class="w-8 h-8 rounded-full">
                            <span class="text-sm font-bold text-mainText">Aman Singh</span>
                        </div>
                    </div>
                </article>

                <article class="group relative bg-surface rounded-3xl overflow-hidden border border-gray-100 hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 flex flex-col hover:-translate-y-1.5">
                    <a href="#" class="absolute inset-0 z-10"></a>
                    <div class="relative h-52 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1561070791-2526d30994b5?q=80&w=600&auto=format&fit=crop" alt="Blog Post" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute top-4 left-4">
                            <span class="bg-surface/95 backdrop-blur-sm px-3 py-1.5 rounded-md text-[10px] font-black uppercase tracking-wider text-mainText shadow-sm">Design</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="flex items-center gap-3 text-[11px] text-mutedText font-bold uppercase tracking-wider mb-3">
                            <span>Oct 18, 2026</span>
                            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                            <span>8 min read</span>
                        </div>
                        <h3 class="text-xl font-bold text-mainText mb-3 group-hover:text-primary transition-colors leading-snug line-clamp-2">The 2026 UI/UX Design Trends You Can't Ignore</h3>
                        <p class="text-mutedText text-sm mb-6 line-clamp-2 leading-relaxed">From spatial interfaces to AI-generated components, here is what hiring managers are looking for in design portfolios this year.</p>

                        <div class="mt-auto pt-4 border-t border-gray-100 flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name=P+G&background=D04A02&color=fff" alt="Author" class="w-8 h-8 rounded-full">
                            <span class="text-sm font-bold text-mainText">Priya Gupta</span>
                        </div>
                    </div>
                </article>

                <article class="group relative bg-surface rounded-3xl overflow-hidden border border-gray-100 hover:shadow-xl hover:shadow-primary/5 transition-all duration-300 flex flex-col hover:-translate-y-1.5">
                    <a href="#" class="absolute inset-0 z-10"></a>
                    <div class="relative h-52 overflow-hidden">
                        <img src="https://images.unsplash.com/photo-1460925895917-afdab827c52f?q=80&w=600&auto=format&fit=crop" alt="Blog Post" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute top-4 left-4">
                            <span class="bg-surface/95 backdrop-blur-sm px-3 py-1.5 rounded-md text-[10px] font-black uppercase tracking-wider text-mainText shadow-sm">Marketing</span>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-grow">
                        <div class="flex items-center gap-3 text-[11px] text-mutedText font-bold uppercase tracking-wider mb-3">
                            <span>Oct 12, 2026</span>
                            <span class="w-1 h-1 rounded-full bg-gray-300"></span>
                            <span>6 min read</span>
                        </div>
                        <h3 class="text-xl font-bold text-mainText mb-3 group-hover:text-primary transition-colors leading-snug line-clamp-2">Meta Ads vs. TikTok Ads: Where Should Your Budget Go?</h3>
                        <p class="text-mutedText text-sm mb-6 line-clamp-2 leading-relaxed">An analytical breakdown of conversion costs across major platforms and how to distribute your ad spend efficiently.</p>

                        <div class="mt-auto pt-4 border-t border-gray-100 flex items-center gap-3">
                            <img src="https://ui-avatars.com/api/?name=R+S&background=F7941D&color=fff" alt="Author" class="w-8 h-8 rounded-full">
                            <span class="text-sm font-bold text-mainText">Rahul Sharma</span>
                        </div>
                    </div>
                </article>
            </div>
        </div>
    </section> --}}

    <section class="py-24 relative overflow-hidden bg-navy">
        <div class="absolute top-0 right-0 w-full h-full brand-gradient opacity-[0.03] object-cover z-0"></div>
        <div class="absolute -top-40 -right-40 w-[30rem] h-[30rem] bg-primary rounded-full filter blur-[120px] opacity-20 z-0"></div>
        <div class="absolute -bottom-40 -left-40 w-[30rem] h-[30rem] bg-secondary rounded-full filter blur-[120px] opacity-10 z-0"></div>

        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10 text-center">

            <div class="mb-20">
                <h2 class="text-4xl md:text-5xl lg:text-6xl font-black text-mainText mb-6 tracking-tight">
                    Ready to Build Your <br/>
                    <span class="text-white bg-clip-text brand-gradient">Digital Empire?</span>
                </h2>
                <p class="text-xl text-mutedText mb-10 max-w-2xl mx-auto font-medium">
                    Join thousands of ambitious individuals turning their skills into high-paying careers and successful businesses.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4">
                    <a href="{{ route('register') }}" class="px-8 py-4 rounded-xl brand-gradient text-white font-bold text-lg hover:shadow-lg hover:shadow-primary/30 hover:-translate-y-1 transition-all duration-300">
                        Create Free Account
                    </a>
                    <a href="#courses" class="px-8 py-4 rounded-xl bg-surface text-mainText font-bold text-lg border border-gray-200 hover:border-primary/50 hover:bg-gray-50 transition-all shadow-sm">
                        Browse Courses
                    </a>
                </div>
            </div>

            <div class="bg-surface rounded-[2.5rem] p-8 md:p-12 shadow-2xl shadow-primary/5 border border-primary/10 relative overflow-hidden text-left">
                <div class="absolute top-0 right-0 w-32 h-32 bg-primary/10 rounded-bl-full -mr-16 -mt-16 z-0"></div>

                <div class="relative z-10 flex flex-col md:flex-row items-center gap-10">
                    <div class="md:w-1/2">
                        <div class="w-14 h-14 bg-primary/10 rounded-2xl flex items-center justify-center text-primary mb-5">
                            <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-3xl font-black text-mainText mb-2">The Weekly Edge</h3>
                        <p class="text-mutedText leading-relaxed">Get 1 actionable tip on marketing, design, or coding delivered to your inbox every Tuesday.</p>
                    </div>

                    <div class="md:w-1/2 w-full">
                        <form class="flex flex-col gap-3">
                            <input type="email" placeholder="Enter your email address" class="w-full px-5 py-4 bg-navy border border-gray-200 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-mainText placeholder-gray-400 font-medium" required>
                            <button type="submit" class="w-full px-5 py-4 bg-mainText text-white font-bold rounded-xl hover:bg-black transition-all duration-300 flex justify-center items-center gap-2 group shadow-md">
                                Subscribe Now
                                <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                            </button>
                        </form>
                        <p class="text-xs text-center text-mutedText mt-4 flex items-center justify-center gap-1.5 font-medium">
                            <svg class="w-3.5 h-3.5 text-green-500" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M5 9V7a5 5 0 0110 0v2a2 2 0 012 2v5a2 2 0 01-2 2H5a2 2 0 01-2-2v-5a2 2 0 012-2zm8-2v2H7V7a3 3 0 016 0z" clip-rule="evenodd"/></svg>
                            No spam. Unsubscribe anytime.
                        </p>
                    </div>
                </div>
            </div>

        </div>
    </section>

</div>

@push('scripts')
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('homepageConfig', () => ({
            // Global root data placeholder
        }));

        Alpine.data('statsCounter', () => ({
            started: false,
            stats: { students: 0, courses: 0, reviews: 0, years: 0 },
            target: { students: 50, courses: 30, reviews: 10, years: 5 }, // Adjust values as needed

            startCounters() {
                if(this.started) return;
                this.started = true;

                this.animateValue('students', 0, this.target.students, 2000);
                this.animateValue('courses', 0, this.target.courses, 2000);
                this.animateValue('reviews', 0, this.target.reviews, 2000);
                this.animateValue('years', 0, this.target.years, 2000);
            },

            animateValue(key, start, end, duration) {
                let current = start;
                const increment = end > start ? 1 : -1;
                const stepTime = Math.abs(Math.floor(duration / (end - start)));
                const timer = setInterval(() => {
                    current += increment;
                    this.stats[key] = current;
                    if (current == end) {
                        clearInterval(timer);
                    }
                }, stepTime);
            }
        }));
    });
</script>
@endpush
@endsection
