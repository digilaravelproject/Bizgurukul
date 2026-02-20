@extends('layouts.user.app')

@section('content')
    <div class="space-y-16 pb-12">
        {{-- Header Section --}}
        <div class="border-l-4 border-indigo-600 pl-6 mb-10 animate-fade-in-down">
            <h2 class="text-3xl font-black text-mainText uppercase italic tracking-tighter leading-none">My Learning</h2>
            <p class="text-[11px] text-mutedText font-bold uppercase tracking-[0.2em] mt-1 italic">Your Empire of Knowledge</p>
        </div>

        {{-- 1. Bundles Section --}}
        @foreach($myBundles as $bundle)
        <div class="space-y-8 animate-fade-in-up">
            <div class="flex items-center gap-4">
                 <div class="w-12 h-12 rounded-xl bg-primary/10 flex items-center justify-center text-primary text-xl">
                    <i class="fas fa-layer-group"></i>
                </div>
                <div>
                     <h3 class="text-2xl font-black text-mainText uppercase italic tracking-tight">{{ $bundle->title }}</h3>
                     <p class="text-xs text-mutedText font-bold uppercase tracking-wider">Bundle Content</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($bundle->courses as $course)
                     @include('users.partials.course-card', ['course' => $course])
                @endforeach
            </div>
        </div>
        @endforeach

        {{-- 2. Direct Courses Section --}}
        @if($directCourses->isNotEmpty())
        <div class="space-y-8 animate-fade-in-up">
             <div class="flex items-center gap-4">
                 <div class="w-12 h-12 rounded-xl bg-secondary/10 flex items-center justify-center text-secondary text-xl">
                    <i class="fas fa-book-open"></i>
                </div>
                <div>
                     <h3 class="text-2xl font-black text-mainText uppercase italic tracking-tight">Individual Courses</h3>
                     <p class="text-xs text-mutedText font-bold uppercase tracking-wider">Directly Purchased</p>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8">
                @foreach($directCourses as $course)
                     @include('users.partials.course-card', ['course' => $course])
                @endforeach
            </div>
        </div>
        @endif

        @if($myBundles->isEmpty() && $directCourses->isEmpty())
            <div class="py-24 text-center italic text-mutedText font-black uppercase text-sm tracking-[0.3em] opacity-40 animate-pulse">
                You haven't purchased any courses yet.
            </div>
        @endif
    </div>
@endsection
