@extends('web.layouts.app')

@section('title', 'About Us | ' . config('app.name', 'Skills Pehle'))

@section('content')
<div class="relative overflow-hidden bg-navy text-mainText font-sans min-h-screen pt-12 pb-24">

    <div class="absolute inset-0 z-0 opacity-[0.03]" style="background-image: radial-gradient(rgb(var(--color-primary)) 1px, transparent 1px); background-size: 40px 40px;"></div>
    <div class="absolute top-0 left-0 w-[40%] h-[30%] bg-primary/10 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="absolute bottom-0 right-0 w-[30%] h-[40%] bg-secondary/10 rounded-full blur-[100px] pointer-events-none z-0"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

        <div class="text-center mb-20 animate-fade-in-down max-w-3xl mx-auto">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary font-bold text-xs tracking-widest uppercase mb-6 shadow-sm">
                Welcome to SkillsPehle
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tight text-mainText mb-6 leading-tight">
                Real Skills for the <br />
                <span class="text-white bg-clip-text brand-gradient">Real World.</span>
            </h1>
            <p class="text-lg md:text-xl text-mutedText font-medium leading-relaxed">
                We bridge the gap between theoretical knowledge and practical execution. Learn directly from experts and build a high-income career or business.
            </p>
        </div>

        <div class="bg-surface rounded-3xl p-8 md:p-12 shadow-xl shadow-primary/5 border border-primary/10 mb-16 relative overflow-hidden">
            <div class="absolute top-0 right-0 w-32 h-32 bg-primary/5 rounded-bl-full -mr-4 -mt-4 z-0"></div>

            <div class="relative z-10 grid grid-cols-1 md:grid-cols-2 gap-12 items-center">
                <div>
                    <h2 class="text-3xl font-black text-mainText mb-6">Our "No-Fluff" Promise</h2>
                    <p class="text-mutedText leading-relaxed mb-6">
                        At SkillsPehle, we believe that education should be measured by the results you can generate, not just the certificates you collect.
                    </p>
                    <p class="text-mutedText leading-relaxed mb-6">
                        While other platforms focus on visually cinematic productions, <strong>we prioritize actionable insights and depth of content.</strong> Our lessons are purely practical and expert-led, designed to help you develop real business knowledge that you can apply from day one.
                    </p>
                    <div class="flex items-center gap-4 text-primary font-bold">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Execution over theory. Action over aesthetics.
                    </div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                    <div class="bg-navy p-6 rounded-2xl border border-gray-100 text-center flex flex-col items-center justify-center transform hover:-translate-y-1 transition-transform shadow-sm">
                        <div class="w-12 h-12 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h4 class="font-bold text-mainText">Pre-Recorded<br/>Lessons</h4>
                    </div>
                    <div class="bg-navy p-6 rounded-2xl border border-gray-100 text-center flex flex-col items-center justify-center transform translate-y-4 hover:translate-y-3 transition-transform shadow-sm">
                        <div class="w-12 h-12 bg-secondary/10 text-secondary rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        </div>
                        <h4 class="font-bold text-mainText">Tools &<br/>Resources</h4>
                    </div>
                    <div class="bg-navy p-6 rounded-2xl border border-gray-100 text-center flex flex-col items-center justify-center transform hover:-translate-y-1 transition-transform shadow-sm">
                        <div class="w-12 h-12 bg-primary/10 text-primary rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                        </div>
                        <h4 class="font-bold text-mainText">Community<br/>Support</h4>
                    </div>
                    <div class="bg-navy p-6 rounded-2xl border border-gray-100 text-center flex flex-col items-center justify-center transform translate-y-4 hover:translate-y-3 transition-transform shadow-sm">
                        <div class="w-12 h-12 bg-secondary/10 text-secondary rounded-xl flex items-center justify-center mb-4">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                        </div>
                        <h4 class="font-bold text-mainText">Affiliate<br/>Opportunities</h4>
                    </div>
                </div>
            </div>
        </div>

        <div class="mb-16">
            <div class="text-center mb-12">
                <h2 class="text-3xl font-black text-mainText mb-4">What Drives Us</h2>
                <p class="text-mutedText">The core principles that shape the SkillsPehle ecosystem.</p>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="bg-surface border border-gray-100 rounded-2xl p-8 hover:-translate-y-2 transition-transform duration-300 hover:shadow-xl hover:shadow-primary/5 text-center">
                    <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6 text-primary">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4M7.835 4.697a3.42 3.42 0 001.946-.806 3.42 3.42 0 014.438 0 3.42 3.42 0 001.946.806 3.42 3.42 0 013.138 3.138 3.42 3.42 0 00.806 1.946 3.42 3.42 0 010 4.438 3.42 3.42 0 00-.806 1.946 3.42 3.42 0 01-3.138 3.138 3.42 3.42 0 00-1.946.806 3.42 3.42 0 01-4.438 0 3.42 3.42 0 00-1.946-.806 3.42 3.42 0 01-3.138-3.138 3.42 3.42 0 00-.806-1.946 3.42 3.42 0 010-4.438 3.42 3.42 0 00.806-1.946 3.42 3.42 0 013.138-3.138z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-mainText mb-3">Integrity First</h3>
                    <p class="text-mutedText text-sm leading-relaxed">
                        We promote ethical practices. We don't believe in misleading claims or false promises. Your results depend on your effort, and we are completely transparent about that.
                    </p>
                </div>

                <div class="bg-surface border border-gray-100 rounded-2xl p-8 hover:-translate-y-2 transition-transform duration-300 hover:shadow-xl hover:shadow-secondary/5 text-center">
                    <div class="w-14 h-14 bg-secondary/10 rounded-full flex items-center justify-center mx-auto mb-6 text-secondary">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-mainText mb-3">Action Oriented</h3>
                    <p class="text-mutedText text-sm leading-relaxed">
                        Theory alone won't get you hired or scale your business. Our ecosystem is built around taking action, executing strategies, and analyzing real-world outcomes.
                    </p>
                </div>

                <div class="bg-surface border border-gray-100 rounded-2xl p-8 hover:-translate-y-2 transition-transform duration-300 hover:shadow-xl hover:shadow-primary/5 text-center">
                    <div class="w-14 h-14 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-6 text-primary">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <h3 class="text-xl font-bold text-mainText mb-3">Community Driven</h3>
                    <p class="text-mutedText text-sm leading-relaxed">
                        When you join SkillsPehle, you aren't just buying a course. You are entering an ecosystem of like-minded individuals, unlocking powerful networking and affiliate earning opportunities.
                    </p>
                </div>
            </div>
        </div>

        <div class="bg-navy rounded-3xl p-8 md:p-12 text-center border-2 border-primary/20 relative overflow-hidden">
            <div class="absolute inset-0 bg-gradient-to-br from-primary/5 to-secondary/5 pointer-events-none"></div>
            <div class="relative z-10 max-w-2xl mx-auto">
                <h2 class="text-3xl font-black text-mainText mb-4">Ready to Master Your Craft?</h2>
                <p class="text-mutedText mb-8">
                    Stop watching cinematic theories and start executing real business strategies today. The digital world moves fast—make sure you're keeping up.
                </p>
                <div class="flex flex-col sm:flex-row justify-center gap-4 mb-10">
                    <a href="{{ route('home') }}#bundles" class="px-8 py-4 rounded-xl brand-gradient text-white font-bold hover:shadow-lg hover:shadow-primary/30 hover:-translate-y-0.5 transition-all duration-300">
                        Explore Our Courses
                    </a>
                    <a href="{{ route('web.contact') }}" class="px-8 py-4 rounded-xl bg-surface text-mainText font-bold border border-gray-200 hover:border-primary/50 transition-all duration-300">
                        Contact Support
                    </a>
                </div>

                <div class="border-t border-gray-200/60 pt-6 flex flex-col md:flex-row justify-center items-center gap-4 text-sm font-medium">
                    <span class="text-mutedText"><strong>Company:</strong> SkillsPehle</span>
                    <span class="hidden md:inline text-gray-300">•</span>
                    <span class="text-mutedText"><strong>HQ:</strong> Mumbai, Maharashtra, India</span>
                    <span class="hidden md:inline text-gray-300">•</span>
                    {{-- <span class="text-mutedText"><strong>GSTIN:</strong> 27HCHPS9578D1ZS</span> --}}
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
