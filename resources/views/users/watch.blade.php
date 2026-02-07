@extends('layouts.user.app')

@section('content')
    <div class="min-h-screen bg-[#0f172a] text-white p-4 lg:p-6 font-sans">

        {{-- Main Grid Layout --}}
        <div class="max-w-[1600px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

            {{-- LEFT COLUMN: Video Player & Details (Span 8/12 = 66% width) --}}
            <div class="lg:col-span-8 space-y-6">

                {{-- 1. Video Player Container --}}
                <div
                    class="relative w-full rounded-[2rem] overflow-hidden shadow-2xl bg-black border border-slate-700/50 group aspect-video">
                    <video id="courseVideo" src="{{ $currentLesson->lesson_file_url }}" class="w-full h-full object-contain"
                        controls autoplay controlsList="nodownload" data-lesson="{{ $currentLesson->id }}"
                        data-start="{{ $progress->last_watched_second ?? 0 }}">
                    </video>
                </div>

                {{-- 2. Lesson Info Card --}}
                <div class="bg-slate-800/40 backdrop-blur-md rounded-[2rem] p-6 border border-slate-700/30">
                    <div class="flex items-start justify-between gap-4">
                        <div>
                            <div class="flex items-center gap-3 mb-2">
                                <span
                                    class="px-3 py-1 bg-indigo-500 text-white text-[10px] font-black uppercase tracking-widest rounded-full shadow-lg shadow-indigo-500/30">
                                    Lesson {{ $loop->iteration ?? '01' }}
                                </span>
                                <span class="text-slate-400 text-[11px] font-bold uppercase tracking-wider">
                                    {{ $course->title }}
                                </span>
                            </div>
                            <h1 class="text-2xl md:text-3xl font-black italic uppercase tracking-tight text-white">
                                {{ $currentLesson->title }}
                            </h1>
                        </div>
                    </div>
                    <div class="mt-4 pt-4 border-t border-slate-700/50">
                        <p class="text-slate-300 text-sm leading-relaxed font-medium">
                            {{ $currentLesson->description }}
                        </p>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Playlist (Span 4/12 = 33% width) --}}
            <div class="lg:col-span-4">

                {{-- Sticky Sidebar Container --}}
                <div
                    class="bg-slate-900 rounded-[2rem] border border-slate-800 overflow-hidden shadow-xl flex flex-col h-[calc(100vh-3rem)] sticky top-6">

                    {{-- Sidebar Header --}}
                    <div class="p-6 bg-slate-900 border-b border-slate-800 z-10">
                        <h3 class="text-sm font-black text-white italic tracking-widest uppercase">
                            Course Curriculum
                        </h3>
                        <div class="flex items-center justify-between mt-2">
                            <p class="text-[10px] text-slate-400 font-bold uppercase tracking-wider">
                                {{ $course->lessons->count() }} Lessons
                            </p>
                            <div class="h-1 w-20 bg-slate-800 rounded-full overflow-hidden">
                                <div class="h-full bg-indigo-500 w-1/3"></div> {{-- Dynamic Progress Bar --}}
                            </div>
                        </div>
                    </div>

                    {{-- Scrollable List --}}
                    <div class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-3">
                        @foreach ($course->lessons as $lesson)
                            <a href="{{ route('student.watch', [$course->id, $lesson->id]) }}"
                                class="relative group flex items-center gap-4 p-4 rounded-xl transition-all duration-300 border
                           {{ $currentLesson->id == $lesson->id
                               ? 'bg-gradient-to-r from-indigo-600 to-indigo-700 border-indigo-500 shadow-lg shadow-indigo-900/50 translate-x-1'
                               : 'bg-slate-800/40 border-slate-700/50 hover:bg-slate-800 hover:border-slate-600' }}">

                                {{-- Number / Status Icon --}}
                                <div class="flex-shrink-0">
                                    @if ($currentLesson->id == $lesson->id)
                                        <div
                                            class="w-8 h-8 rounded-full bg-white/20 flex items-center justify-center text-white text-[10px] font-black italic">
                                            <div class="w-2 h-2 bg-white rounded-full animate-pulse"></div>
                                        </div>
                                    @elseif($lesson->is_completed)
                                        <div
                                            class="w-8 h-8 rounded-full bg-emerald-500/20 flex items-center justify-center text-emerald-400 border border-emerald-500/30">
                                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                                viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                    @else
                                        <div
                                            class="w-8 h-8 rounded-full bg-slate-700 flex items-center justify-center text-[10px] font-bold text-slate-400">
                                            {{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}
                                        </div>
                                    @endif
                                </div>

                                {{-- Text --}}
                                <div class="flex-1 min-w-0">
                                    <h4
                                        class="text-[11px] font-black uppercase italic tracking-tight truncate
                                    {{ $currentLesson->id == $lesson->id ? 'text-white' : 'text-slate-300 group-hover:text-white' }}">
                                        {{ $lesson->title }}
                                    </h4>
                                    <span
                                        class="text-[9px] font-bold uppercase tracking-wider block mt-0.5
                                    {{ $currentLesson->id == $lesson->id ? 'text-indigo-200' : 'text-slate-500' }}">
                                        {{ $lesson->type }}
                                    </span>
                                </div>
                            </a>
                        @endforeach
                    </div>

                    {{-- Bottom Fade (Optional visual polish) --}}
                    <div
                        class="h-6 bg-gradient-to-t from-slate-900 to-transparent pointer-events-none absolute bottom-0 w-full">
                    </div>
                </div>
            </div>

        </div>
    </div>

    <style>
        /* Custom Scrollbar for the Playlist */
        .custom-scrollbar::-webkit-scrollbar {
            width: 5px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: transparent;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #334155;
            border-radius: 20px;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb:hover {
            background: #475569;
        }
    </style>

    <script>
        const video = document.getElementById('courseVideo');
        const lessonId = video.dataset.lesson;
        const startPos = video.dataset.start;

        // 1. Resume from last watched position
        video.onloadedmetadata = function() {
            if (startPos > 0) video.currentTime = startPos;
        };

        // 2. Save progress every 5 seconds
        let lastSaved = 0;
        video.ontimeupdate = function() {
            const now = Math.floor(video.currentTime);
            if (now % 5 === 0 && now !== lastSaved) {
                lastSaved = now;
                saveProgress(now, false);
            }
        };

        // 3. Mark as completed
        video.onended = function() {
            saveProgress(video.duration, true);
        };

        function saveProgress(seconds, completed) {
            fetch("{{ route('student.progress.update') }}", {
                method: "POST",
                headers: {
                    "Content-Type": "application/json",
                    "X-CSRF-TOKEN": "{{ csrf_token() }}"
                },
                body: JSON.stringify({
                    lesson_id: lessonId,
                    seconds: seconds,
                    completed: completed
                })
            }).catch(err => console.error(err));
        }
    </script>
@endsection
