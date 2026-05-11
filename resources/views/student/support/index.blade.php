@extends('layouts.user.app')

@section('title', 'Help & Support | ' . config('app.name', 'Skills Pehle'))

@section('content')
<div class="relative overflow-hidden bg-navy text-mainText font-sans min-h-screen pt-8 pb-24">
    <!-- Decorative background elements matching DockIt branding -->
    <div class="absolute inset-0 z-0 opacity-[0.03]" style="background-image: radial-gradient(rgb(var(--color-primary)) 1px, transparent 1px); background-size: 40px 40px;"></div>
    <div class="absolute top-0 right-0 w-[40%] h-[30%] bg-primary/10 rounded-full blur-[120px] pointer-events-none z-0"></div>

    <div class="max-w-4xl mx-auto px-4 sm:px-6 relative z-10">
        <div class="mb-10">
            <h1 class="text-3xl md:text-4xl font-black tracking-tight text-mainText mb-4">
                Help & <span class="text-white bg-clip-text brand-gradient">Support</span>
            </h1>
            <p class="text-mutedText font-medium">
                Need assistance? Send us a message and our support team will respond to your registered email.
            </p>
        </div>

        <div class="bg-surface p-8 md:p-10 rounded-3xl shadow-2xl shadow-primary/5 border border-primary/10 relative overflow-hidden">
            @if(session('success'))
                <div class="mb-6 p-4 rounded-xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 font-bold text-sm">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            <form action="{{ route('student.support.submit') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label for="name" class="block text-sm font-bold text-mainText mb-2 ml-1">Full Name</label>
                        <!-- Readonly Name Field - Strictly uneditable -->
                        <input type="text" id="name" value="{{ auth()->user()->name }}" readonly disabled
                            class="w-full bg-navy border border-gray-200/50 rounded-xl text-mutedText px-5 py-3.5 font-medium cursor-not-allowed select-none opacity-70">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-bold text-mainText mb-2 ml-1">Email Address</label>
                        <!-- Readonly Email Field - Strictly uneditable -->
                        <input type="email" id="email" value="{{ auth()->user()->email }}" readonly disabled
                            class="w-full bg-navy border border-gray-200/50 rounded-xl text-mutedText px-5 py-3.5 font-medium cursor-not-allowed select-none opacity-70">
                    </div>
                </div>

                <div>
                    <label for="subject" class="block text-sm font-bold text-mainText mb-2 ml-1">Subject</label>
                    <select id="subject" name="subject" class="w-full bg-navy border border-gray-200 rounded-xl focus:border-primary focus:ring-2 focus:ring-primary/20 transition-all text-mainText px-5 py-3.5 font-medium appearance-none cursor-pointer" required>
                        <option value="" disabled selected>Select a topic...</option>
                        <option value="Course Inquiry">Course Inquiry</option>
                        <option value="Payment / Billing Issue">Payment / Billing Issue</option>
                        <option value="Technical Support">Technical Support</option>
                        <option value="Other">Other</option>
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
@endsection