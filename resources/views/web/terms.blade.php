@extends('web.layouts.app')

@section('title', 'Terms & Conditions | ' . config('app.name', 'Skills Pehle'))

@section('content')
<div class="relative overflow-hidden bg-navy text-mainText font-sans min-h-screen pt-12 pb-24">

    <div class="absolute inset-0 z-0 opacity-[0.03]" style="background-image: radial-gradient(rgb(var(--color-primary)) 1px, transparent 1px); background-size: 40px 40px;"></div>
    <div class="absolute top-0 right-0 w-[40%] h-[30%] bg-primary/10 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">

        <div class="text-center mb-12 animate-fade-in-down">
            <div class="inline-flex items-center gap-2 px-4 py-2 rounded-full bg-primary/10 border border-primary/20 text-primary font-bold text-xs tracking-widest uppercase mb-6 shadow-sm">
                Legal Information
            </div>
            <h1 class="text-4xl md:text-5xl font-black tracking-tight text-mainText mb-4">
                Terms & <span class="text-white bg-clip-text brand-gradient">Conditions</span>
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
                        About Skillspehle
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        Skillspehle (“Company”, “we”, “our”, “us”) operates the website www.skillspehle.com and provides digital educational programs, training materials, and related services (collectively referred to as the “Services”). By accessing our website, enrolling in a course, or participating in any program offered through Skillspehle, you agree to comply with these Terms & Conditions, along with our Privacy Policy and Refund Policy. If you do not agree with these terms, you should not use our Services.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">2</span>
                        Scope of Services
                    </h2>
                    <p class="text-mutedText leading-relaxed mb-4">
                        Skillspehle provides structured educational content designed to help individuals develop digital skills and business knowledge. Skillspehle courses are designed to be purely practical and expert-led. While we prioritize the quality and practicality of the content, we do not guarantee visually high-end production. The focus is on actionable knowledge and skills, not cinematic presentation.
                    </p>
                    <p class="text-mainText font-bold mb-3">Our Services may include:</p>
                    <ul class="space-y-3 mb-4">
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-mutedText">Pre-recorded video lessons</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-mutedText">Learning resources and tools</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-mutedText">Community or support access (if applicable)</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-mutedText">Affiliate or referral opportunities (where available)</span>
                        </li>
                    </ul>
                    <p class="text-mutedText leading-relaxed italic">
                        All content is provided strictly for educational and informational purposes. Skillspehle does not provide employment, investment advice, legal advice, or guaranteed business opportunities.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">3</span>
                        Results and Performance
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        The application of knowledge gained from our programs depends on individual effort, execution, experience, and market conditions. While we may share testimonials or case studies to illustrate possible outcomes, such examples represent individual experiences and are not assurances of similar results. By enrolling in our programs, you acknowledge that outcomes vary and that you are responsible for your own decisions and actions.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">4</span>
                        Eligibility
                    </h2>
                    <p class="text-mainText font-bold mb-3">To use our Services, you must:</p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Be at least 18 years of age, or</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Access the platform under the supervision of a legal guardian.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">You agree to provide accurate and complete information during registration or purchase.</span>
                        </li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">5</span>
                        Payments and Access
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        Access to paid programs is granted upon successful completion of payment. All pricing, offers, and promotions are subject to change at the Company’s discretion. Payments are processed through secure third-party payment providers. By making a purchase, you confirm that the payment information provided is accurate and authorized. All purchases are governed by our Refund Policy.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">6</span>
                        Intellectual Property
                    </h2>
                    <p class="text-mutedText leading-relaxed mb-4">
                        All materials available on Skillspehle, including but not limited to videos, text, graphics, branding, and course materials, are the intellectual property of the Company. Users may access the content for personal learning purposes only.
                    </p>
                    <p class="text-mainText font-bold mb-3">You may not:</p>
                    <ul class="space-y-3 mb-4">
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Copy, reproduce, or distribute course materials.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Share login credentials.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Record or publicly publish any part of the content.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Use the material for commercial resale.</span>
                        </li>
                    </ul>
                    <p class="text-mutedText leading-relaxed italic">
                        Unauthorized use may result in suspension of access and further action where appropriate.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">7</span>
                        Affiliate Program (If Applicable)
                    </h2>
                    <p class="text-mutedText leading-relaxed mb-4">
                        Where Skillspehle offers an affiliate or referral program, participants act as independent promoters and not as employees or representatives of the Company.
                    </p>
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Affiliates must ensure that all promotional activities are truthful, ethical, and compliant with applicable laws.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Misleading claims, false representations, or guarantees of income are strictly prohibited.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">The Company reserves the right to review, suspend, or terminate affiliate accounts that do not adhere to these standards.</span>
                        </li>
                    </ul>

                    <div class="bg-red-50 border border-red-200 rounded-xl p-5 flex gap-4 items-start shadow-sm">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-red-800 font-bold mb-1">Important Safety Warning</h4>
                            <p class="text-red-700 text-sm leading-relaxed font-semibold">
                                Never take any registration amount (full or in parts) from your lead into your account, if you’re found doing so, your affiliate id might get suspended or permanently disabled. Make the registration only through officially designated payment methods (affiliate link, Skillspehle Instamojo link, Skillspehle official UPI, or net banking to the company’s account).
                            </p>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">8</span>
                        User Conduct
                    </h2>
                    <p class="text-mutedText leading-relaxed mb-3">Users agree to use the platform responsibly and lawfully.</p>
                    <p class="text-mainText font-bold mb-3">You agree not to:</p>
                    <ul class="space-y-3 mb-4">
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Engage in fraudulent activity.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Attempt unauthorized access to the platform.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Disrupt or interfere with the website’s functionality.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Post unlawful or harmful content.</span>
                        </li>
                    </ul>
                    <p class="text-mutedText leading-relaxed italic">
                        The Company reserves the right to restrict or terminate access for violations of these terms.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">9</span>
                        Communication
                    </h2>
                    <p class="text-mutedText leading-relaxed mb-4">
                        By registering or purchasing from Skillspehle, you consent to receive communications related to:
                    </p>
                    <ul class="space-y-3 mb-4">
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Course access</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Account updates</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Service notifications</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Promotional or educational updates</span>
                        </li>
                    </ul>
                    <p class="text-mutedText leading-relaxed italic">
                        You may opt out of promotional communications at any time.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">10</span>
                        Limitation of Liability
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        Skillspehle strives to provide accurate and valuable educational content. However, the Services are provided on an “as available” basis without guarantees of specific outcomes. To the extent permitted by applicable law, the Company shall not be liable for indirect, incidental, or consequential damages arising from the use of the Services. Any liability, where established, shall be limited to the amount paid for the specific program or service.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">11</span>
                        Third-Party Services
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        The platform may include integrations or links to third-party tools or services. Skillspehle is not responsible for the content, policies, or practices of third-party providers. Users are encouraged to review third-party policies separately.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">12</span>
                        Modifications to Terms
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        Skillspehle may update these terms periodically. Continued use of the platform after changes are published constitutes acceptance of the revised terms.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">13</span>
                        Governing Law and Jurisdiction
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        These terms shall be governed by the laws of India. Any disputes arising from or related to the use of Skillspehle shall be subject to the jurisdiction of the courts located in Mumbai, Maharashtra.
                    </p>
                </section>

            </div>

            <div class="mt-16 bg-navy rounded-2xl p-6 md:p-8 border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-xl font-black text-mainText mb-2">Have a Question?</h3>
                    <p class="text-mutedText text-sm">For support, queries, or concerns, please reach out to our team.</p>
                </div>
                <div class="flex flex-col gap-3 w-full md:w-auto">
                    <a href="mailto:support@skillspehle.com" class="px-6 py-3 rounded-xl bg-surface border border-gray-200 text-mainText font-bold hover:border-primary/50 hover:text-primary transition-colors flex items-center justify-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Email Support
                    </a>
                    <div class="text-center md:text-right">
                        <span class="text-xs font-bold text-mutedText uppercase tracking-wider">GSTIN:</span>
                        <span class="text-sm font-bold text-mainText ml-1">27HCHPS9578D1ZS</span>
                    </div>
                </div>
            </div>

        </div>
    </div>
</div>
@endsection
