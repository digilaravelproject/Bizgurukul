@extends('web.layouts.app')

@section('title', 'Contact Us | ' . config('app.name', 'Skills Pehle'))

@section('content')
<div class="py-20 bg-navy">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-5xl font-bold text-mainText mb-6">Get in <span class="text-primary">Touch</span></h1>
        <p class="text-xl text-mutedText leading-relaxed mb-12">
            Have questions about our courses or platform? We're here to help.
        </p>
    </div>
</div>

<div class="py-16 bg-white shrink">
    <div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8">
        <form action="#" method="POST" class="bg-gray-50 p-8 rounded-2xl border border-gray-100">
            @csrf
            <div class="mb-6">
                <label for="name" class="block text-sm font-bold text-mainText mb-2">Your Name</label>
                <input type="text" id="name" name="name" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary px-4 py-3" required>
            </div>

            <div class="mb-6">
                <label for="email" class="block text-sm font-bold text-mainText mb-2">Email Address</label>
                <input type="email" id="email" name="email" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary px-4 py-3" required>
            </div>

            <div class="mb-6">
                <label for="message" class="block text-sm font-bold text-mainText mb-2">Message</label>
                <textarea id="message" name="message" rows="5" class="w-full rounded-lg border-gray-300 shadow-sm focus:border-primary focus:ring-primary px-4 py-3" required></textarea>
            </div>

            <button type="button" class="w-full bg-primary hover:bg-secondary text-white font-bold py-4 rounded-xl transition-colors shadow-lg shadow-primary/30">
                Send Message
            </button>
        </form>
    </div>
</div>
@endsection
