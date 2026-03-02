@extends('web.layouts.app')

@section('title', 'Refund Policy | ' . config('app.name', 'Skills Pehle'))

@section('content')
<div class="relative overflow-hidden bg-navy text-mainText font-sans min-h-screen pt-12 pb-24">

    <div class="absolute inset-0 z-0 opacity-[0.03]" style="background-image: radial-gradient(rgb(var(--color-primary)) 1px, transparent 1px); background-size: 40px 40px;"></div>
    <div class="absolute top-0 left-0 w-[40%] h-[30%] bg-secondary/10 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

        <div class="text-center mb-12 animate-fade-in-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary font-bold text-xs tracking-widest uppercase mb-6 shadow-sm">
                Money-Back Guarantee
            </div>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-mainText mb-4">
                Refund & Cancellation <span class="text-white bg-clip-text brand-gradient">Policy</span>
            </h1>
        </div>

        <div class="bg-surface rounded-3xl p-8 md:p-12 shadow-xl shadow-primary/5 border border-primary/10">

            <div class="bg-navy border-2 border-primary/20 rounded-2xl p-6 md:p-8 mb-10 flex flex-col md:flex-row gap-6 items-center">
                <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-mainText mb-2">Our 7-Day Guarantee</h2>
                    <p class="text-mutedText leading-relaxed">
                        We offer a 7-day, no-questions-asked money-back guarantee. If you log in and realize the teaching style or content isn't exactly what you need right now, let us know within 7 days of purchase and we'll process your refund immediately.
                    </p>
                </div>
            </div>

            <div class="space-y-10">

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">1</span>
                        Eligibility Criteria
                    </h2>
                    <p class="text-mutedText leading-relaxed mb-4">
                        To be eligible for a refund under our guarantee, the following conditions must be met:
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">The refund request is submitted within exactly <strong>7 days (168 hours)</strong> of your original purchase timestamp.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Your account must be in good standing and not in violation of our Terms & Conditions (e.g., no account sharing).</span>
                        </li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">2</span>
                        Non-Refundable Scenarios
                    </h2>
                    <p class="text-mutedText leading-relaxed mb-4">
                        We cannot process refunds under the following circumstances:
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            <span class="text-mutedText">Requests made after the 7-day period has expired.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            <span class="text-mutedText">Accounts that have downloaded bulk materials for offline use before requesting a refund.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-red-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            <span class="text-mutedText">Accounts suspended due to fraudulent activity or intellectual property theft.</span>
                        </li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">3</span>
                        Refund Processing Time
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        Once your refund request is approved, the refund will be initiated immediately on our end. However, please allow <strong>5 to 7 business days</strong> for the funds to reflect in your original payment method (Credit Card, UPI, Net Banking, etc.) depending on your bank's processing times.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">4</span>
                        How to Request a Refund
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        To request a refund, please send an email to our support team from your registered email address. Please include your <strong>Order ID</strong> or the email address used to purchase the course to help us process it faster.
                    </p>
                </section>

            </div>

            <div class="mt-16 bg-navy rounded-2xl p-6 md:p-8 border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-xl font-black text-mainText mb-2">Ready to initiate a refund?</h3>
                    <p class="text-mutedText text-sm">Send us an email with your Order ID.</p>
                </div>
                <div class="flex flex-col gap-3 w-full md:w-auto">
                    <a href="mailto:support@skillspehle.com" class="px-6 py-3 rounded-xl brand-gradient text-white font-bold hover:shadow-lg hover:shadow-primary/30 transition-all duration-300 flex items-center justify-center gap-2 group hover:-translate-y-0.5">
                        <svg class="w-5 h-5 group-hover:-rotate-12 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Email Support
                    </a>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
