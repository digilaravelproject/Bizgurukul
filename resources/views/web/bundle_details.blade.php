@extends('web.layouts.app')

@section('title', $bundle->title . ' | ' . config('app.name'))

@section('content')
<div class="bg-surface min-h-screen text-mainText pb-16">

    <div class="bg-navy border-b border-gray-100 py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-xs font-bold uppercase tracking-wider" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('home') }}" class="text-mutedText hover:text-primary transition-colors">Home</a></li>
                    <li class="flex items-center space-x-2">
                        <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <a href="{{ route('home') }}#bundles" class="text-mutedText hover:text-primary transition-colors">Bundles</a>
                    </li>
                    <li class="flex items-center space-x-2">
                        <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="text-primary truncate">{{ $bundle->title }}</span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="bg-navy relative border-b border-gray-100 overflow-hidden">
        <div class="absolute inset-0 z-0 opacity-[0.03]" style="background-image: radial-gradient(rgb(var(--color-primary)) 1px, transparent 1px); background-size: 30px 30px;"></div>
        <div class="absolute top-0 right-0 w-[30%] h-[100%] bg-primary/10 rounded-full blur-[100px] pointer-events-none z-0"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

                <div class="lg:col-span-7">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-secondary/10 text-secondary font-bold text-[10px] uppercase tracking-widest mb-4">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path></svg>
                        Mastery Bundle
                    </div>

                    <h1 class="text-3xl md:text-5xl font-black text-mainText mb-4 leading-tight">
                        {{ $bundle->title }}
                    </h1>

                    <p class="text-base text-mutedText mb-6 max-w-2xl leading-relaxed">
                        {{ Str::limit(strip_tags($bundle->description), 160) }}
                    </p>

                    <div class="flex flex-wrap gap-3">
                        <div class="bg-white border border-gray-200 rounded-lg px-4 py-2 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-md bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                            </div>
                            <div>
                                <div class="text-[10px] text-mutedText font-bold uppercase tracking-wider">Courses</div>
                                <div class="text-sm font-black text-mainText">{{ $bundle->courses->count() }} Included</div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-lg px-4 py-2 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-md bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <div class="text-[10px] text-mutedText font-bold uppercase tracking-wider">Access</div>
                                <div class="text-sm font-black text-mainText">Lifetime Updates</div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-lg px-4 py-2 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-md bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <div class="text-[10px] text-mutedText font-bold uppercase tracking-wider">Level</div>
                                <div class="text-sm font-black text-mainText">Beginner to Pro</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 relative">
                    <div class="aspect-video rounded-2xl overflow-hidden shadow-lg border-4 border-white bg-gray-100 relative group">
                        <img src="{{ $bundle->thumbnail_url ?? 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=800&auto=format&fit=crop' }}" alt="{{ $bundle->title }}" class="w-full h-full object-cover">
                    </div>
                </div>

            </div>
        </div>
    </section>

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <div class="lg:col-span-8 space-y-8">

                <div class="bg-white rounded-2xl p-6 md:p-8 border border-gray-100 shadow-sm">
                    <h2 class="text-xl font-black text-mainText mb-4">About This Bundle</h2>
                    <div class="prose prose-sm max-w-none text-mutedText leading-relaxed">
                        {!! $bundle->description !!}
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 md:p-8 border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-4">
                        <h2 class="text-xl font-black text-mainText">Curriculum Inside</h2>
                        <span class="px-3 py-1 bg-navy rounded text-xs font-bold text-mainText">{{ $bundle->courses->count() }} Courses</span>
                    </div>

                    <div class="space-y-4">
                        @foreach($bundle->courses as $index => $course)
                        <a href="{{ route('course.show', $course->slug ?? $course->id) }}" class="group block border border-gray-100 rounded-xl p-4 hover:border-primary/50 hover:bg-navy transition-all duration-300">
                            <div class="flex flex-col sm:flex-row gap-4 items-center sm:items-start">

                                <div class="w-full sm:w-40 aspect-video rounded-lg overflow-hidden bg-gray-100 shrink-0 border border-gray-200">
                                    <img src="{{ $course->thumbnail_url ?? 'https://images.unsplash.com/photo-1498050108023-c5249f4df085?q=80&w=400&auto=format&fit=crop' }}" alt="{{ $course->title }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                                </div>

                                <div class="flex-grow w-full">
                                    <div class="flex items-center justify-between mb-1">
                                        <span class="text-[10px] font-bold text-primary uppercase tracking-wider">{{ $course->category->name ?? 'Course' }}</span>
                                        <span class="text-[10px] font-bold text-mutedText">{{ $course->lessons_count ?? $course->lessons->count() }} Lessons</span>
                                    </div>
                                    <h3 class="text-base font-bold text-mainText mb-1 group-hover:text-primary transition-colors line-clamp-1">{{ $course->title }}</h3>
                                    <p class="text-xs text-mutedText line-clamp-2 leading-relaxed">{{ Str::limit(strip_tags($course->description), 100) }}</p>
                                </div>

                                <div class="hidden sm:flex shrink-0 items-center justify-center pt-3">
                                    <div class="w-8 h-8 rounded-full bg-white border border-gray-200 flex items-center justify-center text-mutedText group-hover:bg-primary group-hover:border-primary group-hover:text-white transition-all duration-300">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                                    </div>
                                </div>
                            </div>
                        </a>
                        @endforeach
                    </div>
                </div>

            </div>

            <div class="lg:col-span-4 sticky top-24">

                <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6 mb-6">
                    <div class="text-center mb-6">
                        <div class="text-[10px] font-bold text-primary uppercase tracking-widest mb-1">{{ $bundle->title }} Investment</div>
                        <div class="flex justify-center items-end gap-2">
                            <span class="text-4xl font-black text-mainText tracking-tight">₹{{ number_format($effectivePrice, 0) }}</span>
                            @if ($effectivePrice < $bundle->website_price)
                                <span class="text-lg text-gray-400 line-through mb-1">₹{{ number_format($bundle->website_price, 0) }}</span>
                            @endif
                        </div>
                        @if($isUpgrade)
                            <div class="mt-2 inline-block px-2.5 py-1 bg-red-50 text-red-600 font-bold text-[10px] rounded uppercase tracking-wider">Upgrade Discount Applied</div>
                        @else
                            @if ($effectivePrice < $bundle->website_price)
                            <div class="mt-2 text-green-600 font-bold text-xs">You save ₹{{ number_format($bundle->website_price - $effectivePrice, 0) }}!</div>
                            @endif
                        @endif
                    </div>

                    <div class="space-y-3 mb-6">
                        @auth
                            <a href="{{ route('student.checkout', ['type' => 'bundle', 'id' => $bundle->id]) }}" class="w-full flex justify-center items-center py-3.5 rounded-xl brand-gradient text-white font-bold text-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                                {{ $isUpgrade ? 'Upgrade Now' : 'Enroll Now' }}
                            </a>
                        @else
                            <a href="{{ route('register', ['intent' => 'bundle', 'id' => $bundle->id]) }}" class="w-full flex justify-center items-center py-3.5 rounded-xl brand-gradient text-white font-bold text-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                                Register & Enroll
                            </a>
                        @endauth
                    </div>

                    <ul class="space-y-3 pt-4 border-t border-gray-100">
                        <li class="flex items-start gap-2.5 text-sm text-mainText font-medium">
                            <svg class="w-4 h-4 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Full Access to {{ $bundle->courses->count() }} Courses
                        </li>
                        <li class="flex items-start gap-2.5 text-sm text-mainText font-medium">
                            <svg class="w-4 h-4 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            All Future Updates Included
                        </li>
                        <li class="flex items-start gap-2.5 text-sm text-mainText font-medium">
                            <svg class="w-4 h-4 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Premium Community Access
                        </li>
                        <li class="flex items-start gap-2.5 text-sm text-mainText font-medium">
                            <svg class="w-4 h-4 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Official Certification
                        </li>
                    </ul>
                </div>

                <div class="bg-navy rounded-2xl border border-gray-200 p-5 flex items-center gap-4">
                    <div class="w-12 h-12 rounded-full bg-white flex items-center justify-center shrink-0 border border-gray-100 shadow-sm">
                        <svg class="w-6 h-6 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8h2a2 2 0 012 2v6a2 2 0 01-2 2h-2v4l-4-4H9a1.994 1.994 0 01-1.414-.586m0 0L11 14h4a2 2 0 002-2V6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2v4l.586-.586z"></path></svg>
                    </div>
                    <div>
                        <h4 class="text-sm font-bold text-mainText">Need Help?</h4>
                        <p class="text-xs text-mutedText">Reach out to our 24/7 support team.</p>
                        <a href="{{ route('web.contact') }}" class="text-xs font-bold text-primary hover:underline mt-1 inline-block">Contact Us</a>
                    </div>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
