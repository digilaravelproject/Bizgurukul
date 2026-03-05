@extends('web.layouts.app')

@section('title', 'Refund Policy | ' . config('app.name', 'Skills Pehle'))

@section('content')
<div class="relative overflow-hidden bg-navy text-mainText font-sans min-h-screen pt-12 pb-24">

    <div class="absolute inset-0 z-0 opacity-[0.03]" style="background-image: radial-gradient(rgb(var(--color-primary)) 1px, transparent 1px); background-size: 40px 40px;"></div>
    <div class="absolute top-0 left-0 w-[40%] h-[30%] bg-secondary/10 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

        <div class="text-center mb-12 animate-fade-in-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary font-bold text-xs tracking-widest uppercase mb-6 shadow-sm">
                Payments & Cancellations
            </div>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-mainText mb-4">
                Refund <span class="text-white bg-clip-text brand-gradient">Policy</span>
            </h1>
            <p class="text-mutedText">
                This Refund Policy applies to all digital products and courses offered on Skillspehle, owned and operated by Shrivardhankar Enterprises (“Company”, “we”, “our”, or “us”).
            </p>
        </div>

        <div class="bg-surface rounded-3xl p-8 md:p-12 shadow-xl shadow-primary/5 border border-primary/10">

            <div class="bg-navy border-2 border-primary/20 rounded-2xl p-6 md:p-8 mb-10 flex flex-col md:flex-row gap-6 items-center">
                <div class="w-20 h-20 rounded-full bg-primary/10 flex items-center justify-center text-primary shrink-0">
                    <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                </div>
                <div>
                    <h2 class="text-2xl font-black text-mainText mb-2">Final Sale & Instant Access</h2>
                    <p class="text-mutedText leading-relaxed">
                        Due to the digital nature of these products and the immediate delivery of content upon successful payment, all purchases are final and non-refundable. Once a purchase is completed and course access has been granted, the Company does not provide refunds, cancellations, or exchanges under any circumstances.
                    </p>
                </div>
            </div>

            <div class="space-y-10">

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">1</span>
                        Nature of Digital Products
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        All products available on the Skillspehle platform are digital educational courses that provide users with immediate access to proprietary learning materials, including but not limited to recorded videos, downloadable resources, and other training content. By purchasing any course or digital product from this platform, you agree to the terms stated in this Refund Policy.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">2</span>
                        Customer Responsibility Before Purchase
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        Customers are strongly encouraged to carefully review the course description, curriculum, pricing, and any related information before completing their purchase. By proceeding with the purchase, the customer confirms that they have reviewed the course details and determined that the product meets their requirements.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">3</span>
                        Mandatory Acceptance of Policies
                    </h2>
                    <p class="text-mutedText leading-relaxed mb-4">
                        During the checkout process on the Skillspehle website, customers are required to review and accept the Terms & Conditions and Refund Policy before completing the payment.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">A confirmation checkbox is displayed on the checkout page stating that the customer has read and agreed to the Terms & Conditions and Refund Policy. A clickable link is provided so that customers can review these policies before proceeding.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Customers cannot complete the purchase unless they actively select the checkbox confirming their acceptance of these policies.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">By selecting this checkbox and completing the purchase, the customer explicitly acknowledges that they have read, understood, and agreed to be bound by the Terms & Conditions and this Refund Policy, including the condition that all purchases are non-refundable.</span>
                        </li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">4</span>
                        Limited Exceptions
                    </h2>
                    <p class="text-mutedText leading-relaxed mb-4">
                        Refunds may only be considered under exceptional circumstances, including:
                    </p>
                    <ul class="space-y-3 mb-4">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-mutedText"><strong>Duplicate Payment:</strong> A customer accidentally completes two or more payments for the same course due to a technical error.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <span class="text-mutedText"><strong>Payment Deducted but No Access Granted:</strong> A payment is successfully processed but the customer does not receive access to the purchased course due to a verified technical issue.</span>
                        </li>
                    </ul>
                    <p class="text-mutedText leading-relaxed italic">
                        In such cases, customers must contact the support team within 48 hours of the transaction and provide valid proof of payment. After verification of the issue, the Company may take appropriate action at its sole discretion.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">5</span>
                        Policy Acceptance
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        By purchasing any product or course from Skillspehle, the customer acknowledges that they have read, understood, and agreed to this Refund Policy.
                    </p>
                </section>

            </div>

            <div class="mt-16 bg-navy rounded-2xl p-6 md:p-8 border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-xl font-black text-mainText mb-2">Need Help?</h3>
                    <p class="text-mutedText text-sm mb-2">For payment-related concerns or technical issues regarding course access, please contact us.</p>
                    <p class="text-xs font-bold text-mutedText uppercase tracking-wider">Operated by: <span class="text-mainText">Shrivardhankar Enterprises</span></p>
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
