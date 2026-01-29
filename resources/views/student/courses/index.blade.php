@extends('layouts.user.app')

@section('content')
    <div class="space-y-10" x-data="{ videoModal: false, activeVideo: '' }">
        {{-- Header Section --}}
        <div class="border-l-4 border-indigo-600 pl-6 mb-10">
            <h2 class="text-3xl font-black text-slate-800 uppercase italic tracking-tighter leading-none">Premium Courses
            </h2>
            <p class="text-[11px] text-slate-400 font-bold uppercase tracking-[0.2em] mt-1 italic">Master your skills</p>
        </div>

        {{-- Course Grid: 3 per row --}}
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 max-w-6xl">
            @forelse($courses as $course)
                {{-- Card with Fixed Height and Narrow Width --}}
                <div
                    class="bg-white rounded-[2.5rem] border border-slate-100 shadow-sm overflow-hidden group hover:shadow-2xl transition-all duration-500 flex flex-col h-[550px] w-full max-w-[320px] mx-auto">

                    {{-- Thumbnail: Tall Portrait Height --}}
                    <div class="relative h-64 overflow-hidden bg-slate-100">
                        <img src="{{ asset('storage/' . $course->thumbnail) }}"
                            class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">

                        {{-- Play Overlay --}}
                        <div
                            class="absolute inset-0 bg-slate-900/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                            {{-- FFmpeg converted MP4 URLs work better with direct asset() links --}}
                            <button
                                @click="videoModal = true; activeVideo = '{{ asset('storage/' . $course->demo_video_url) }}'"
                                class="bg-white text-indigo-600 p-5 rounded-full shadow-2xl transform scale-75 group-hover:scale-100 transition-all duration-500">
                                <svg class="w-8 h-8" fill="currentColor" viewBox="0 0 20 20">
                                    <path
                                        d="M6.3 2.841A1.5 1.5 0 004 4.11V15.89a1.5 1.5 0 002.3 1.269l9.333-5.89a1.5 1.5 0 000-2.538L6.3 2.841z" />
                                </svg>
                            </button>
                        </div>
                    </div>

                    {{-- Content Section: Vertical Spacing --}}
                    <div class="p-8 flex-grow flex flex-col justify-between">
                        <div class="space-y-4">
                            <h3
                                class="text-lg font-black text-slate-800 uppercase italic leading-tight tracking-tight group-hover:text-indigo-600 transition-colors">
                                {{ $course->title }}
                            </h3>
                            <p class="text-xs text-slate-400 font-medium italic leading-relaxed line-clamp-6">
                                {{ $course->description }}
                            </p>
                        </div>

                        {{-- Footer: Positioned at the bottom --}}
                        <div class="flex flex-col gap-4 pt-6 border-t border-slate-50 mt-auto">
                            <div class="flex justify-between items-end">
                                <div class="leading-none">
                                    <p class="text-[9px] text-slate-400 font-black uppercase italic mb-1 tracking-widest">
                                        Pricing</p>
                                    <p class="text-2xl font-black text-indigo-600 italic tracking-tighter">
                                        ₹{{ number_format($course->price, 2) }}
                                    </p>
                                </div>
                                <a href="{{ route('student.courses.show', $course->id) }}"
                                    class="bg-slate-900 text-white px-8 py-4 rounded-2xl text-[10px] font-black uppercase italic tracking-widest hover:bg-indigo-600 transition-all shadow-lg active:scale-95">
                                    Enroll
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div
                    class="col-span-full py-24 text-center italic text-slate-300 font-black uppercase text-sm tracking-[0.3em] opacity-40">
                    No courses available yet.
                </div>
            @endforelse
        </div>

        {{-- Video Modal Logic (Support for FFmpeg MP4) --}}
        <div x-show="videoModal" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-10"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">

            <div @click="videoModal = false; activeVideo = ''" class="absolute inset-0 bg-slate-900/95 backdrop-blur-xl">
            </div>

            <div class="relative bg-black w-full max-w-4xl aspect-video rounded-[3rem] overflow-hidden shadow-2xl border-4 border-white/10"
                x-show="videoModal" x-transition:enter="transition ease-out duration-300 transform scale-90"
                x-transition:enter-end="scale-100">

                <button @click="videoModal = false; activeVideo = ''"
                    class="absolute top-8 right-8 z-10 text-white/50 hover:text-white transition-all bg-white/10 p-2 rounded-full backdrop-blur-md">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>

                {{-- Using <video> tag for local FFmpeg files to avoid 404/Playback issues --}}
                <template x-if="activeVideo">
                    <video :src="activeVideo" class="w-full h-full object-contain" controls autoplay
                        controlsList="nodownload"></video>
                </template>
            </div>
        </div>
    </div>
@endsection
