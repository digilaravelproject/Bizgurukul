@extends('layouts.user.app')

@section('content')
    <div class="space-y-10">
        {{-- 1. Course Header Section --}}
        <div class="flex flex-col lg:flex-row gap-10 items-start">
            <div class="flex-1 space-y-4">
                <div
                    class="inline-flex items-center gap-2 px-4 py-1.5 bg-primary/10 text-primary rounded-xl border border-primary/10 shadow-sm">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    <span class="text-xs font-bold uppercase tracking-wider">Premium Content</span>
                </div>
                <h1 class="text-4xl font-bold text-mainText leading-none">
                    {{ $course->title }}
                </h1>
                <p class="text-mutedText text-sm font-medium leading-relaxed max-w-2xl">
                    {{ $course->description }}
                </p>
            </div>

            <div
                class="bg-customWhite p-8 rounded-3xl border border-primary/10 shadow-xl flex flex-col items-center gap-4 min-w-[280px]">
                <p class="text-xs font-bold text-mutedText uppercase tracking-wider">One-Time Enrollment</p>
                <p class="text-4xl font-bold text-mainText">
                    ₹{{ number_format($course->price, 2) }}
                </p>

                {{-- Payment Button Logic --}}
                @if (Auth::check() && $course->isPurchasedBy(Auth::id()))
                    <button
                        class="w-full bg-emerald-500 text-white py-4 rounded-2xl text-xs font-bold uppercase tracking-wider shadow-lg shadow-emerald-200 cursor-default"
                        disabled>
                        Already Enrolled
                    </button>
                @else
                    <a href="{{ route('student.checkout', ['type' => 'course', 'id' => $course->id]) }}"
                        class="w-full block text-center bg-primary text-white py-4 rounded-2xl text-xs font-bold uppercase tracking-wider shadow-lg shadow-primary/20 hover:bg-secondary hover:shadow-xl active:scale-95 transition-all">
                        Buy This Course
                    </a>
                @endif
            </div>
        </div>

        {{-- 2. Course Content & Curriculum --}}
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
            <div class="lg:col-span-2 space-y-8">
                <div
                    class="relative aspect-video bg-black rounded-3xl overflow-hidden shadow-2xl border-4 border-customWhite group">
                    <video src="{{ $course->demo_video_url }}" class="w-full h-full object-contain" controls
                        controlsList="nodownload" poster="{{ $course->thumbnail }}">
                        Your browser does not support the video tag.
                    </video>
                </div>

                <div class="bg-customWhite p-10 rounded-3xl border border-primary/10 shadow-sm">
                    <h3 class="text-xl font-bold text-mainText mb-6 tracking-tight">What you'll learn
                    </h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        @foreach (explode("\n", $course->short_description) as $point)
                            @if (trim($point))
                                <div class="flex items-center gap-3 text-mutedText text-sm font-medium">
                                    <svg class="w-5 h-5 text-primary" fill="currentColor" viewBox="0 0 20 20">
                                        <path
                                            d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" />
                                    </svg>
                                    {{ trim($point) }}
                                </div>
                            @endif
                        @endforeach
                    </div>
                </div>
            </div>

            {{-- 3. Lesson Sidebar --}}
            <div class="space-y-6">
                <div class="bg-navy rounded-3xl p-8 text-white shadow-xl">
                    <h3 class="text-sm font-bold uppercase tracking-wider mb-6 border-b border-primary/10 pb-4 text-white/90">
                        Course Curriculum
                    </h3>
                    <div class="space-y-4">
                        @forelse($course->lessons as $index => $lesson)
                            <div
                                class="group flex items-center justify-between p-4 bg-primary/10 rounded-2xl hover:bg-primary transition-all cursor-not-allowed opacity-70">
                                <div class="flex items-center gap-4">
                                    <span
                                        class="text-xs font-bold text-primary group-hover:text-white">0{{ $index + 1 }}</span>
                                    <span
                                        class="text-sm font-medium text-white/90">{{ $lesson->title }}</span>
                                </div>
                                <svg class="w-4 h-4 text-white/50 group-hover:text-white" fill="none"
                                    stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z" />
                                </svg>
                            </div>
                        @empty
                            <p class="text-xs text-white/50 text-center py-4">Syllabus being updated...</p>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>
    </div>


@endsection
