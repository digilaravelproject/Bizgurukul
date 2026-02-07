@extends('layouts.user.app')

@section('content')
    <div class="space-y-10">
        {{-- Header Section --}}
        <div class="border-l-4 border-indigo-600 pl-6 mb-10">
            <h2 class="text-3xl font-black text-slate-800 uppercase italic tracking-tighter leading-none">My Learning</h2>
            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1 italic">Courses you've enrolled in
            </p>
        </div>

        {{-- Course Grid --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl">
            @forelse($courses as $course)
                <div
                    class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-500 flex flex-col h-[550px] w-full max-w-[320px]">

                    {{-- Thumbnail --}}
                    <div class="relative h-64 overflow-hidden bg-slate-100">
                        <img src="{{ $course->thumbnail }}" class="w-full h-full object-cover">
                        <div class="absolute top-6 right-6">
                            <span
                                class="bg-emerald-500 text-white text-[9px] font-black uppercase px-3 py-1 rounded-full shadow-lg italic">Purchased</span>
                        </div>
                    </div>

                    {{-- Content --}}
                    <div class="p-8 flex-grow flex flex-col justify-between">
                        <div class="space-y-4">
                            <h3 class="text-lg font-black text-slate-800 uppercase italic leading-tight tracking-tight">
                                {{ $course->title }}
                            </h3>
                            <p class="text-xs text-slate-400 font-medium italic leading-relaxed line-clamp-4">
                                {{ $course->description }}
                            </p>
                        </div>

                        {{-- Footer Button --}}
                        <div class="pt-6 border-t border-slate-50 mt-auto">
                            <a href="{{ route('student.watch', $course->id) }}"
                                class="w-full block text-center bg-indigo-600 text-white py-4 rounded-2xl text-[10px] font-black uppercase italic tracking-widest hover:bg-slate-900 transition-all shadow-lg active:scale-95">
                                Start Learning Now
                            </a>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full py-24 text-center italic text-slate-300 font-black uppercase text-sm tracking-[0.3em] opacity-40">
                    You haven't purchased any courses yet.
                </div>
            @endforelse
        </div>
    </div>
@endsection
