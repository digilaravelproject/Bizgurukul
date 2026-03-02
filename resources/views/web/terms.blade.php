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
                    <p class="text-mainText font-bold">SkillsPehle</p>
                </div>
            </div>

            <div class="space-y-10">

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">1</span>
                        About SkillsPehle
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        SkillsPehle (“Company”, “we”, “our”, or “us”) operates the website and provides digital educational programs, training materials, and related services (collectively referred to as “Services”). By accessing our website, enrolling in a course, or participating in any offer through the platform, you agree to comply with these Terms and Conditions along with our Privacy Policy and Refund Policy. If you do not agree, you should not use the platform.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">2</span>
                        Scope of Services
                    </h2>
                    <p class="text-mutedText leading-relaxed mb-4">
                        Our courses are structured and content is designed to help individuals develop practical business knowledge. The lessons are intended to be purely practical and expert-led. While we prioritize the quality and depth of content, we do not guarantee visually high-end production; the focus is on actionable insights rather than cinematic presentation.
                    </p>
                    <p class="text-mainText font-bold mb-3">Our services may include:</p>
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
                            <span class="text-mutedText">Community support (if applicable)</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <svg class="w-5 h-5 text-primary shrink-0 mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                            <span class="text-mutedText">Affiliate and referral opportunities (where available)</span>
                        </li>
                    </ul>
                    <p class="text-mutedText leading-relaxed italic">
                        All content is strictly for educational and informational purposes. It does not guarantee employment, investment advice, legal advice, or professional certification.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">3</span>
                        Results and Performance
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        Any results gained from our programs depend on your effort, execution, experience, and market conditions. We share testimonials and case studies to illustrate possible outcomes, but such examples are not assurances that you will achieve similar results. You acknowledge that results vary and you are responsible for your own decisions.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">4</span>
                        Eligibility
                    </h2>
                    <p class="text-mainText font-bold mb-3">To use this platform, you must:</p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Be at least 18 years of age.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">If under 18, use the platform under the supervision of a parent or legal guardian.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Provide accurate and complete information during registration and purchase.</span>
                        </li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">5</span>
                        Payments and Refund
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        All payments must be paid according to the pricing plans described on the website. We use secure third-party payment gateways. By making a purchase, you confirm that you are authorized to use the payment method. Refunds are governed by our separate Refund Policy.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">6</span>
                        Intellectual Property
                    </h2>
                    <p class="text-mutedText leading-relaxed mb-4">
                        All content, including but not limited to text, graphics, branding, and videos, is the intellectual property of SkillsPehle.
                    </p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Users are granted a personal, non-exclusive license for personal use only.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">You may not copy, share, or distribute course content or login credentials.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Sharing access with the public or other individuals is strictly prohibited.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Unauthorized commercial use of content will lead to immediate account suspension and further legal action.</span>
                        </li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">7</span>
                        Affiliate Program (If Applicable)
                    </h2>
                    <p class="text-mutedText leading-relaxed mb-4">
                        If you participate as an affiliate, you must act with integrity and follow ethical practices.
                    </p>
                    <ul class="space-y-3 mb-6">
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Promotions must be truthful, ethical, and compliant with local laws.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Misleading claims or false promises about potential earnings are strictly prohibited.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">We reserve the right to review and terminate affiliate accounts if these terms are violated.</span>
                        </li>
                    </ul>

                    <div class="bg-red-50 border border-red-200 rounded-xl p-5 flex gap-4 items-start shadow-sm">
                        <div class="w-10 h-10 rounded-full bg-red-100 flex items-center justify-center text-red-600 shrink-0">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                        </div>
                        <div>
                            <h4 class="text-red-800 font-bold mb-1">Safety Warning</h4>
                            <p class="text-red-700 text-sm leading-relaxed">
                                Never take payments on personal numbers or scam links. If you do so, your account may get permanently disabled. Only use officially provided payment methods (e.g., SkillsPehle website link, Instamojo, official UPI/Net Banking of the company).
                            </p>
                        </div>
                    </div>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">8</span>
                        User Conduct
                    </h2>
                    <p class="text-mainText font-bold mb-3">You agree not to:</p>
                    <ul class="space-y-3">
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Engage in fraudulent or illegal activities.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Attempt to hack or disrupt the website's functionality.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Post harmful or offensive content.</span>
                        </li>
                        <li class="flex items-start gap-3">
                            <div class="w-1.5 h-1.5 rounded-full bg-secondary mt-2.5 shrink-0"></div>
                            <span class="text-mutedText">Violate the Intellectual Property of the company.</span>
                        </li>
                    </ul>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">9</span>
                        Communication
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        By signing up, you agree to receive updates and notifications via email, WhatsApp, or other communication channels. You can opt out at any time.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">10</span>
                        Limitation of Liability
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        Our services are provided on an "as-is" basis. SkillsPehle shall not be liable for any indirect, incidental, or consequential damages resulting from the use of our services. Any established liability shall be limited to the amount paid by the user for the specific service.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">11</span>
                        External Links
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        The website may contain links to third-party websites. We do not take responsibility for the content or practices of these external sites.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">12</span>
                        Modifications to Terms
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        SkillsPehle reserves the right to modify these terms at any time. Continued use of the platform after updates are posted constitutes acceptance of the new terms.
                    </p>
                </section>

                <section>
                    <h2 class="text-2xl font-black text-mainText mb-4 flex items-center gap-3">
                        <span class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-sm">13</span>
                        Governing Law and Jurisdiction
                    </h2>
                    <p class="text-mutedText leading-relaxed">
                        These terms are governed by the laws of India. Any disputes will be subject to the exclusive jurisdiction of the courts located in Mumbai, Maharashtra.
                    </p>
                </section>

            </div>

            <div class="mt-16 bg-navy rounded-2xl p-6 md:p-8 border border-gray-100 flex flex-col md:flex-row items-center justify-between gap-6">
                <div>
                    <h3 class="text-xl font-black text-mainText mb-2">Have a Question?</h3>
                    <p class="text-mutedText text-sm">For queries or concerns, please reach out to our team.</p>
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
