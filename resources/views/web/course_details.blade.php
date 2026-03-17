@extends('web.layouts.app')

@section('title', $course->title . ' | ' . config('app.name'))

@section('content')
<div class="bg-surface min-h-screen text-mainText pb-16">

    <div class="bg-navy border-b border-gray-100 py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <nav class="flex text-xs font-bold uppercase tracking-wider" aria-label="Breadcrumb">
                <ol class="flex items-center space-x-2">
                    <li><a href="{{ route('home') }}" class="text-mutedText hover:text-primary transition-colors">Home</a></li>
                    <li class="flex items-center space-x-2">
                        <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <a href="{{ route('home') }}#courses" class="text-mutedText hover:text-primary transition-colors">Courses</a>
                    </li>
                    <li class="flex items-center space-x-2">
                        <svg class="w-3 h-3 text-gray-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path></svg>
                        <span class="text-primary truncate">{{ $course->title }}</span>
                    </li>
                </ol>
            </nav>
        </div>
    </div>

    <section class="bg-navy relative border-b border-gray-100 overflow-hidden">
        <div class="absolute inset-0 z-0 opacity-[0.03]" style="background-image: radial-gradient(rgb(var(--color-primary)) 1px, transparent 1px); background-size: 30px 30px;"></div>
        <div class="absolute top-0 left-0 w-[30%] h-[100%] bg-primary/10 rounded-full blur-[100px] pointer-events-none z-0"></div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10 md:py-14 relative z-10">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-center">

                <div class="lg:col-span-7">
                    <div class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded bg-primary/10 text-primary font-bold text-[10px] uppercase tracking-widest mb-4">
                        <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path d="M5 4a2 2 0 012-2h6a2 2 0 012 2v14l-5-2.5L5 18V4z"></path></svg>
                        {{ $course->category->name ?? 'Premium Course' }}
                    </div>

                    <h1 class="text-3xl md:text-5xl font-black text-mainText mb-4 leading-tight">
                        {{ $course->title }}
                    </h1>

                    <p class="text-base text-mutedText mb-6 max-w-2xl leading-relaxed">
                        {{ Str::limit(strip_tags($course->description), 160) }}
                    </p>

                    <div class="flex flex-wrap gap-3">
                        <div class="bg-white border border-gray-200 rounded-lg px-4 py-2 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-md bg-primary/10 text-primary flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                            </div>
                            <div>
                                <div class="text-[10px] text-mutedText font-bold uppercase tracking-wider">Curriculum</div>
                                <div class="text-sm font-black text-mainText">{{ $course->lessons_count ?? $course->lessons->count() }} Modules</div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-lg px-4 py-2 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-md bg-secondary/10 text-secondary flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <div class="text-[10px] text-mutedText font-bold uppercase tracking-wider">Access</div>
                                <div class="text-sm font-black text-mainText">Lifetime</div>
                            </div>
                        </div>

                        <div class="bg-white border border-gray-200 rounded-lg px-4 py-2 flex items-center gap-3">
                            <div class="w-8 h-8 rounded-md bg-green-100 text-green-600 flex items-center justify-center shrink-0">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <div class="text-[10px] text-mutedText font-bold uppercase tracking-wider">Certificate</div>
                                <div class="text-sm font-black text-mainText">Included</div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="lg:col-span-5 relative">
                    <div class="aspect-video rounded-2xl overflow-hidden shadow-lg border-4 border-white bg-gray-100 relative group">
                        @if ($course->demo_video_url)
                            <video controls class="w-full h-full object-cover" poster="{{ $course->thumbnail_url }}">
                                <source src="{{ $course->demo_video_url }}" type="video/mp4">
                            </video>
                        @else
                            <img src="{{ $course->thumbnail_url ?? 'https://images.unsplash.com/photo-1516321318423-f06f85e504b3?q=80&w=800&auto=format&fit=crop' }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                            <div class="absolute inset-0 bg-black/20 flex items-center justify-center pointer-events-none">
                                <div class="w-12 h-12 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center border border-white/30">
                                    <svg class="w-6 h-6 text-white fill-current" viewBox="0 0 20 20"><path d="M6.3 2.841A1.5 1.5 0 004 4.11v11.78a1.5 1.5 0 002.3 1.269l9.344-5.89a1.5 1.5 0 000-2.538L6.3 2.84z"></path></svg>
                                </div>
                            </div>
                        @endif
                    </div>
                </div>

            </div>
        </div>
    </section>

    @if($bundle)
    <div class="bg-navy border-b border-gray-100 py-3">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col sm:flex-row items-center justify-between gap-4 text-center sm:text-left">
                <div class="flex items-center gap-3">
                    <span class="px-2 py-1 bg-primary/10 text-primary text-[10px] font-black uppercase tracking-widest rounded">Bundle Offer</span>
                    <p class="text-sm font-bold text-mainText">Get this course + {{ $bundle->courses->count() - 1 }} others in the <span class="text-primary">{{ $bundle->title }}</span></p>
                </div>
                <a href="{{ route('bundles.show', $bundle->slug ?? $bundle->id) }}" class="text-xs font-bold bg-white text-mainText border border-gray-200 px-4 py-1.5 rounded-lg hover:border-primary hover:text-primary transition-colors">View Bundle</a>
            </div>
        </div>
    </div>
    @endif

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-10">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">

            <div class="lg:col-span-8 space-y-8">

                <div class="bg-white rounded-2xl p-6 md:p-8 border border-gray-100 shadow-sm">
                    <h2 class="text-xl font-black text-mainText mb-4">Course Description</h2>
                    <div class="prose prose-sm max-w-none text-mutedText leading-relaxed">
                        {!! $course->description !!}
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 md:p-8 border border-gray-100 shadow-sm">
                    <div class="flex items-center justify-between mb-6 border-b border-gray-100 pb-4">
                        <h2 class="text-xl font-black text-mainText">Curriculum</h2>
                        <span class="px-3 py-1 bg-navy rounded text-xs font-bold text-mainText">{{ $course->lessons_count ?? $course->lessons->count() }} Lessons</span>
                    </div>

                    <div class="space-y-3">
                        @forelse($course->lessons as $index => $lesson)
                            <div class="group flex items-center justify-between p-4 rounded-xl border border-gray-100 hover:border-primary/30 hover:bg-navy transition-all duration-300">
                                <div class="flex items-center gap-4">
                                    <div class="w-8 h-8 rounded-lg bg-gray-50 flex items-center justify-center text-mutedText font-bold text-xs group-hover:bg-primary group-hover:text-white transition-colors">
                                        {{ str_pad($index + 1, 2, '0', STR_PAD_LEFT) }}
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-mainText">{{ $lesson->title }}</h4>
                                        <p class="text-[10px] text-mutedText uppercase tracking-wider mt-0.5">{{ $lesson->type === 'video' ? 'Video Lesson' : 'Resource Material' }}</p>
                                    </div>
                                </div>
                                <div class="text-mutedText shrink-0">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                </div>
                            </div>
                        @empty
                            <div class="text-center py-6">
                                <p class="text-sm text-mutedText">Curriculum details are being updated.</p>
                            </div>
                        @endforelse
                    </div>
                </div>

            </div>

            <div class="lg:col-span-4 sticky top-24">

                <div class="bg-white rounded-2xl border border-gray-200 shadow-lg p-6 mb-6">
                    <div class="text-center mb-6">
                        @if($bundle)
                            <div class="text-[10px] font-bold text-primary uppercase tracking-widest mb-1">{{ $bundle->title }} Bundle Membership</div>
                            <div class="flex justify-center items-end gap-2">
                                <span class="text-4xl font-black text-mainText tracking-tight">₹{{ number_format($bundle->final_price, 0) }}</span>
                                @if ($bundle->final_price < $bundle->website_price)
                                    <span class="text-lg text-gray-400 line-through mb-1">₹{{ number_format($bundle->website_price, 0) }}</span>
                                @endif
                            </div>
                            <p class="text-[10px] text-mutedText font-bold mt-2 uppercase tracking-tight">Get this course + {{ $bundle->courses->count() - 1 }} more</p>
                        @else
                            <div class="text-[10px] font-bold text-mutedText uppercase tracking-widest mb-1">Total Investment</div>
                            <div class="flex justify-center items-end gap-2">
                                <span class="text-4xl font-black text-mainText tracking-tight">₹{{ number_format($course->final_price, 0) }}</span>
                                @if ($course->final_price < $course->website_price)
                                    <span class="text-lg text-gray-400 line-through mb-1">₹{{ number_format($course->website_price, 0) }}</span>
                                @endif
                            </div>
                        @endif
                    </div>

                    <div class="space-y-3 mb-6">
                        @auth
                            @if($bundle)
                                <a href="{{ route('student.checkout', ['type' => 'bundle', 'id' => $bundle->id]) }}" class="w-full flex justify-center items-center py-3.5 rounded-xl brand-gradient text-white font-bold text-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                                    Enroll in Bundle
                                </a>
                            @else
                                <a href="{{ route('student.checkout', ['type' => 'course', 'id' => $course->id]) }}" class="w-full flex justify-center items-center py-3.5 rounded-xl brand-gradient text-white font-bold text-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                                    Enroll in Course
                                </a>
                            @endif
                        @else
                            @if($bundle)
                                <a href="{{ route('register', ['intent' => 'bundle', 'id' => $bundle->id]) }}" class="w-full flex justify-center items-center py-3.5 rounded-xl brand-gradient text-white font-bold text-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                                    Get Bundle Access
                                </a>
                            @else
                                <a href="{{ route('register', ['intent' => 'course', 'id' => $course->id]) }}" class="w-full flex justify-center items-center py-3.5 rounded-xl brand-gradient text-white font-bold text-sm hover:shadow-md hover:-translate-y-0.5 transition-all duration-200">
                                    Register & Enroll
                                </a>
                            @endif
                        @endauth
                    </div>

                    <ul class="space-y-3 pt-4 border-t border-gray-100">
                        @if($bundle)
                            <li class="flex items-start gap-2.5 text-sm text-mainText font-medium">
                                <svg class="w-4 h-4 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                Includes All {{ $bundle->courses->count() }} Courses
                            </li>
                        @endif
                        <li class="flex items-start gap-2.5 text-sm text-mainText font-medium">
                            <svg class="w-4 h-4 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            {{ $course->lessons_count ?? $course->lessons->count() }} On-demand videos
                        </li>
                        <li class="flex items-start gap-2.5 text-sm text-mainText font-medium">
                            <svg class="w-4 h-4 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Full lifetime access
                        </li>
                        <li class="flex items-start gap-2.5 text-sm text-mainText font-medium">
                            <svg class="w-4 h-4 text-green-500 shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            Official Certification
                        </li>
                    </ul>
                </div>


                <div class="bg-navy rounded-2xl border border-gray-200 p-5 flex items-center gap-4 mb-6">
                    <img src="https://ui-avatars.com/api/?name=Expert+Instructor&background=F7941D&color=fff" alt="Instructor" class="w-12 h-12 rounded-full object-cover border-2 border-white shrink-0 shadow-sm">
                    <div>
                        <h4 class="text-sm font-bold text-mainText">Expert Practitioner</h4>
                        <p class="text-[10px] text-primary font-bold uppercase tracking-widest mt-0.5">Industry Mentor</p>
                    </div>
                </div>

                <div class="bg-white border border-gray-200 rounded-xl p-4 text-center">
                    <p class="text-xs text-mutedText font-medium flex items-center justify-center gap-1.5">
                        <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                        Secure, encrypted checkout.
                    </p>
                </div>

            </div>
        </div>
    </div>

</div>
@endsection
