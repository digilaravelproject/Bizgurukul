@extends('web.layouts.app')

@section('title', 'Privacy Policy | ' . config('app.name', 'Skills Pehle'))

@section('content')
<div class="relative overflow-hidden bg-navy text-mainText font-sans min-h-screen pt-12 pb-24">

    <div class="absolute inset-0 z-0 opacity-[0.03]" style="background-image: radial-gradient(rgb(var(--color-primary)) 1px, transparent 1px); background-size: 40px 40px;"></div>
    <div class="absolute top-0 right-0 w-[40%] h-[30%] bg-primary/10 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

        <div class="text-center mb-12 animate-fade-in-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary font-bold text-xs tracking-widest uppercase mb-6 shadow-sm">
                Data & Security
            </div>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-mainText mb-4">
                Privacy <span class="text-white bg-clip-text brand-gradient">Policy</span>
            </h1>
        </div>

        <div class="bg-surface rounded-3xl p-8 md:p-12 shadow-xl shadow-primary/5 border border-primary/10">

            <div class="mb-10 pb-6 border-b border-gray-100 flex flex-col md:flex-row gap-6 justify-between items-start md:items-center">
                <div>
                    <p class="text-sm font-bold text-mutedText uppercase tracking-wider mb-1">Website</p>
                    <a href="https://www.skillspehle.com" class="text-primary font-bold hover:underline">www.skillspehle.com</a>
                </div>
                <div>
                    <p class="text-sm font-bold text-mutedText uppercase tracking-wider mb-1">Company</p>
                    <p class="text-mainText font-bold">Skillspehle</p>
                </div>
            </div>

            <div class="space-y-10">

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">1</span>
                        Information We Collect
                    </h2>
                    <p class="text-mutedText leading-relaxed mb-4">
                        When you register for an account, purchase a course, or subscribe to our newsletter, we may collect the following information:
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText"><strong>Personal Identification:</strong> Name, email address, phone number.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText"><strong>Account Data:</strong> Usernames, passwords, and course progress.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText"><strong>Payment Information:</strong> Processed securely via our third-party payment gateways (we do not store your credit/debit card details on our servers).</span>
                        </li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">2</span>
                        How We Use Your Information
                    </h2>
                    <p class="text-mutedText leading-relaxed mb-4">
                        We use your data strictly to enhance your learning experience. Your information allows us to:
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span class="text-mutedText">Provide, operate, and maintain our website and courses.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span class="text-mutedText">Process transactions and send related information, including purchase confirmations and invoices.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            <span class="text-mutedText">Send you technical notices, updates, security alerts, and support messages.</span>
                        </li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">3</span>
                        Data Protection & Security
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        We implement a variety of security measures to maintain the safety of your personal information. All sensitive data exchanged between your browser and our website happens over a secure SSL communication channel and is encrypted. We <strong>never</strong> sell, trade, or rent your personal identification information to others.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">4</span>
                        Your Rights
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        You have the right to access, correct, or delete your personal data at any time. You can also opt-out of our marketing communications by clicking the "unsubscribe" link at the bottom of any promotional email we send.
                    </p>
                </section>

            </div>

            <div class="mt-16 bg-navy rounded-2xl p-6 md:p-8 border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-xl font-black text-mainText mb-2">Privacy Concerns?</h3>
                    <p class="text-mutedText text-sm">Contact our data protection team directly.</p>
                </div>
                <div class="flex flex-col gap-3 w-full md:w-auto">
                    <a href="mailto:support@skillspehle.com" class="px-6 py-3 rounded-xl bg-surface border border-gray-200 text-mainText font-bold hover:border-primary/50 hover:text-primary transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        support@skillspehle.com
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
