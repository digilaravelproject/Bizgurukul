@extends('layouts.user.app')
@section('title', 'My Certificates')

@section('content')
<main class="min-h-screen bg-slate-50 pt-20 pb-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Section --}}
        <div class="mb-8">
            <h1 class="text-3xl font-extrabold text-mainText tracking-tight mb-2">My <span class="text-primary">Certificates</span></h1>
            <p class="text-sm text-mutedText max-w-2xl">
                View your progress and download certificates for the courses you have completed. You must complete at least 90% of a course to generate its certificate.
            </p>
        </div>

        @if(session('error'))
            <div class="mb-8 bg-red-50 border-l-4 border-red-500 p-4 rounded-r-xl shadow-sm">
                <div class="flex">
                    <div class="flex-shrink-0">
                        <svg class="h-5 w-5 text-red-500" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/>
                        </svg>
                    </div>
                    <div class="ml-3">
                        <p class="text-sm text-red-700 font-medium">{{ session('error') }}</p>
                    </div>
                </div>
            </div>
        @endif

        {{-- Courses Grid --}}
        @if($myCourses->isEmpty())
            <div class="bg-white rounded-3xl p-12 text-center shadow-sm border border-slate-100">
                <div class="w-20 h-20 bg-primary/10 rounded-full flex items-center justify-center mx-auto mb-4">
                    <svg class="w-10 h-10 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <h3 class="text-lg font-bold text-mainText mb-2">No Unlocked Courses Found</h3>
                <p class="text-mutedText text-sm max-w-sm mx-auto">
                    You haven't unlocked any courses yet. Enroll in a course to start your learning journey and earn certificates!
                </p>
                <a href="{{ route('student.courses.index') }}" class="inline-block mt-6 px-6 py-3 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/30 hover:-translate-y-1 transition-all duration-300">
                    Browse Courses
                </a>
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($myCourses as $course)
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-100 overflow-hidden hover:shadow-md transition-shadow flex flex-col h-full">
                        {{-- Course Image --}}
                        <div class="relative h-48 bg-slate-200">
                            @if($course->thumbnail)
                                <img src="{{ asset('storage/' . $course->thumbnail) }}" alt="{{ $course->title }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-slate-400">
                                    <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                            @endif

                            {{-- Progress Badge Overlay --}}
                            <div class="absolute top-4 right-4 bg-white/90 backdrop-blur-sm px-3 py-1.5 rounded-full shadow-sm flex items-center gap-2">
                                <div class="w-2 h-2 rounded-full {{ $course->progress_percent >= 90 ? 'bg-emerald-500' : 'bg-amber-500' }}"></div>
                                <span class="text-xs font-bold text-mainText">{{ $course->progress_percent }}% Done</span>
                            </div>
                        </div>

                        {{-- Content --}}
                        <div class="p-6 flex flex-col flex-1">
                            <h3 class="text-lg font-bold text-mainText mb-2 line-clamp-2">{{ $course->title }}</h3>

                            {{-- Progress Bar --}}
                            <div class="mt-auto pt-4">
                                <div class="flex justify-between items-center text-xs font-bold mb-2">
                                    <span class="text-mutedText">Completion Progress</span>
                                    <span class="{{ $course->progress_percent >= 90 ? 'text-emerald-500' : 'text-primary' }}">{{ $course->progress_percent }}%</span>
                                </div>
                                <div class="w-full bg-slate-100 rounded-full h-2 mb-6 overflow-hidden">
                                    <div class="{{ $course->progress_percent >= 90 ? 'bg-emerald-500' : 'brand-gradient' }} h-2 rounded-full transition-all duration-1000" style="width: {{ $course->progress_percent }}%"></div>
                                </div>

                                {{-- Action Button --}}
                                @if($course->progress_percent >= 90)
                                    <a href="{{ route('student.certificates.generate', $course->id) }}" target="_blank" class="w-full flex items-center justify-center gap-2 py-3 bg-primary text-white font-bold rounded-xl shadow-lg shadow-primary/30 hover:-translate-y-1 hover:shadow-xl hover:shadow-primary/40 transition-all duration-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                        Generate Certificate
                                    </a>
                                @else
                                    <button disabled class="w-full flex items-center justify-center gap-2 py-3 bg-slate-100 text-slate-400 font-bold rounded-xl cursor-not-allowed transition-all duration-300">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
                                        Needs 90% Completion
                                    </button>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        @endif

    </div>
</main>
@endsection
