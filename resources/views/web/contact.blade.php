@extends('web.layouts.app')

@section('title', 'Contact Us | ' . config('app.name', 'Skills Pehle'))

@section('content')
<div class="relative overflow-hidden bg-navy text-mainText font-sans min-h-screen pt-12 pb-24">

    <div class="absolute inset-0 z-0 opacity-[0.03]" style="background-image: radial-gradient(rgb(var(--color-primary)) 1px, transparent 1px); background-size: 40px 40px;"></div>
    <div class="absolute top-0 right-0 w-[40%] h-[30%] bg-primary/10 rounded-full blur-[120px] pointer-events-none z-0"></div>
    <div class="absolute bottom-0 left-0 w-[30%] h-[40%] bg-secondary/10 rounded-full blur-[100px] pointer-events-none z-0"></div>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

        <div class="text-center mb-16 animate-fade-in-down max-w-3xl mx-auto">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary font-bold text-xs tracking-widest uppercase mb-6 shadow-sm">
                We're Here To Help
            </div>
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-black tracking-tight text-mainText mb-6 leading-tight">
                Get in <span class="text-white bg-clip-text brand-gradient">Touch.</span>
            </h1>
            <p class="text-lg md:text-xl text-mutedText font-medium leading-relaxed">
                Have questions about our courses, your account, or affiliate opportunities? Drop us a message and our support team will get back to you shortly.
            </p>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-12 items-start">

            <div class="lg:col-span-5 space-y-8 animate-fade-in-down" style="animation-delay: 0.1s;">

                <div class="bg-surface rounded-3xl p-8 shadow-xl shadow-primary/5 border border-primary/10">
                    <h3 class="text-2xl font-black text-mainText mb-6">Contact Information</h3>

                    <div class="space-y-6">
                        <div class="flex items-start gap-4 group">
                            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-300 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-mutedText uppercase tracking-wider mb-1">Email Support</p>
                                <a href="mailto:support@skillspehle.com" class="text-lg font-bold text-mainText hover:text-primary transition-colors">support@skillspehle.com</a>
                                <p class="text-sm text-mutedText mt-1">We aim to reply within 24 hours.</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 group">
                            <div class="w-12 h-12 rounded-xl bg-secondary/10 flex items-center justify-center text-secondary group-hover:bg-secondary group-hover:text-white transition-colors duration-300 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-mutedText uppercase tracking-wider mb-1">Headquarters</p>
                                <p class="text-lg font-bold text-mainText">Mumbai, Maharashtra</p>
                                <p class="text-sm text-mutedText mt-1">India</p>
                            </div>
                        </div>

                        <div class="flex items-start gap-4 group">
                            <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary group-hover:bg-primary group-hover:text-white transition-colors duration-300 shrink-0">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-mutedText uppercase tracking-wider mb-1">Company Details</p>
                                <p class="text-lg font-bold text-mainText">SkillsPehle</p>
                                <p class="text-sm text-mutedText mt-1">GSTIN: 27HCHPS9578D1ZS</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-red-50 border border-red-200 rounded-3xl p-6 shadow-sm relative overflow-hidden">
                    <div class="absolute top-0 right-0 w-16 h-16 bg-red-100 rounded-bl-full -mr-4 -mt-4 z-0"></div>
                    <div class="relative z-10 flex gap-4 items-start">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600 shrink-0 mt-1">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-red-800 font-black text-lg mb-2">Safety Warning</h4>
                            <p class="text-red-700 text-sm leading-relaxed font-medium">
                                Never make payments to personal numbers or scan unverified QR codes. Only use officially provided payment methods directly on the <strong>www.skillspehle.com</strong> website or our official payment gateway.
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="lg:col-span-7 animate-fade-in-down" style="animation-delay: 0.2s;">
                <div class="bg-surface p-8 md:p-10 rounded-3xl shadow-2xl shadow-primary/5 border border-gray-100 relative overflow-hidden">

                    <div class="mb-8">
                        <h2 class="text-3xl font-black text-mainText mb-2">Send us a Message</h2>
                        <p class="text-mutedText font-medium">Fill out the form below and we'll be in touch.</p>
                    </div>

                    @if(session('success'))
                        <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 font-bold text-sm animate-fade-in">
                            <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mb-6 p-4 rounded-xl bg-red-500/10 border border-red-500/20 text-red-500 font-bold text-sm animate-fade-in">
                            <i class="fas fa-exclamation-circle mr-2"></i> {{ session('error') }}
                        </div>
                    @endif

                    <form action="{{ route('web.contact.submit') }}" method="POST" class="space-y-6">
                        @csrf

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="name" class="block text-sm font-bold text-mainText mb-2 ml-1">Full Name</label>
                                <input type="text" id="name" name="name" value="{{ old('name') }}" placeholder="John Doe" class="w-full bg-navy border border-gray-200 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-mainText px-5 py-3.5 font-medium placeholder-gray-400" required>
                            </div>

                            <div>
                                <label for="email" class="block text-sm font-bold text-mainText mb-2 ml-1">Email Address</label>
                                <input type="email" id="email" name="email" value="{{ old('email') }}" placeholder="john@example.com" class="w-full bg-navy border border-gray-200 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-mainText px-5 py-3.5 font-medium placeholder-gray-400" required>
                            </div>
                        </div>

                        <div>
                            <label for="subject" class="block text-sm font-bold text-mainText mb-2 ml-1">Subject</label>
                            <select id="subject" name="subject" class="w-full bg-navy border border-gray-200 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-mainText px-5 py-3.5 font-medium appearance-none cursor-pointer" required>
                                <option value="" disabled selected>Select a topic...</option>
                                <option value="course_inquiry">Course Inquiry</option>
                                <option value="payment_issue">Payment / Billing Issue</option>
                                <option value="affiliate">Affiliate Program</option>
                                <option value="technical_support">Technical Support</option>
                                <option value="other">Other</option>
                            </select>
                        </div>

                        <div>
                            <label for="message" class="block text-sm font-bold text-mainText mb-2 ml-1">Your Message</label>
                            <textarea id="message" name="message" rows="5" placeholder="How can we help you today?" class="w-full bg-navy border border-gray-200 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-mainText px-5 py-3.5 font-medium placeholder-gray-400 resize-none" required>{{ old('message') }}</textarea>
                        </div>

                        <button type="submit" class="w-full brand-gradient hover:shadow-lg hover:shadow-primary/30 text-white font-bold py-4 rounded-xl transition-all duration-300 flex items-center justify-center gap-2 group hover:-translate-y-0.5 mt-4">
                            Send Message
                            <svg class="w-5 h-5 group-hover:translate-x-1 group-hover:-translate-y-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path></svg>
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
