@extends('layouts.user.app')

@section('content')
    <div class="space-y-12 pb-8">
        {{-- Compact Header --}}
        <div class="mb-10">
            <h2 class="text-2xl font-bold text-mainText tracking-tight">My <span class="text-primary">Learning</span></h2>
            <p class="text-sm text-mutedText font-medium mt-1">Access all your unlocked content in one place.</p>
        </div>

        {{-- 1. Bundles Section --}}
        @if($myBundles->isNotEmpty())
        <div class="space-y-10">
            @foreach($myBundles as $bundle)
            <div class="space-y-6">
                <div class="flex items-center gap-3">
                     <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary text-lg">
                        <i class="fas fa-layer-group"></i>
                    </div>
                    <div>
                         <h3 class="text-lg font-bold text-mainText">{{ $bundle->title }}</h3>
                         <p class="text-[10px] text-mutedText font-bold uppercase tracking-wider">Bundle Content</p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($bundle->courses as $course)
                         @include('users.partials.course-card', ['course' => $course])
                    @endforeach
                </div>
            </div>
            @endforeach
            <div class="mt-4">
                {{ $myBundles->links() }}
            </div>
        </div>
        @endif

        {{-- 2. Direct Courses Section --}}
        @if($directCourses->isNotEmpty())
        <div class="space-y-6 mt-12">
             <div class="flex items-center gap-3">
                 <div class="w-10 h-10 rounded-xl bg-gray-100 flex items-center justify-center text-mainText text-lg">
                    <i class="fas fa-book-open"></i>
                </div>
                <div>
                     <h3 class="text-lg font-bold text-mainText">Standalone Courses</h3>
                     <p class="text-[10px] text-mutedText font-bold uppercase tracking-wider">Direct Access</p>
                </div>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                @foreach($directCourses as $course)
                     @include('users.partials.course-card', ['course' => $course])
                @endforeach
            </div>
            <div class="mt-4">
                {{ $directCourses->links() }}
            </div>
        </div>
        @endif

        @if($myBundles->isEmpty() && $directCourses->isEmpty())
            <div class="py-20 text-center bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                <p class="text-xs font-bold text-mutedText uppercase tracking-widest opacity-60">You haven't unlocked any courses yet.</p>
            </div>
        @endif
    </div>
@endsection
