@extends('layouts.user.app')

@section('content')
    <div class="min-h-screen bg-[#0f172a] text-white p-4 lg:p-6 font-sans">

        {{-- Main Grid Layout --}}
        <div class="max-w-[1600px] mx-auto grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8">

            {{-- LEFT COLUMN: Video Player & Details --}}
            <div class="lg:col-span-8 space-y-6">

                {{-- Tab Navigation (Design as per your image) --}}
                <div class="flex items-center gap-2 mb-4">
                    <button class="px-6 py-2 bg-[#ff6b35] text-white font-bold rounded-md">Course</button>
                    <button
                        class="px-6 py-2 bg-white text-slate-800 font-bold rounded-md border border-slate-200">Session</button>
                </div>

                {{-- 1. Video Player Container --}}
                <div
                    class="relative w-full rounded-lg overflow-hidden shadow-2xl bg-black border border-slate-700/50 aspect-video">
                    <video id="courseVideo" src="{{ $currentLesson->lesson_file_url }}" class="w-full h-full object-contain"
                        controls playsinline controlsList="nodownload" data-lesson="{{ $currentLesson->id }}"
                        data-start="{{ $progress->last_watched_second ?? 0 }}"
                        data-completed="{{ $progress->is_completed ?? 0 }}">
                    </video>
                </div>

                {{-- 2. Lesson Info Card --}}
                <div class="bg-slate-800/40 backdrop-blur-md rounded-xl p-6 border border-slate-700/30">
                    <div class="flex items-center gap-3 mb-2">
                        <span class="px-3 py-1 bg-indigo-500 text-white text-[10px] font-black uppercase rounded-full">
                            Lesson {{ $loop->iteration ?? '01' }}
                        </span>
                        <span class="text-slate-400 text-[11px] font-bold uppercase">{{ $course->title }}</span>
                    </div>
                    <h1 class="text-2xl font-black italic uppercase text-white">{{ $currentLesson->title }}</h1>
                    <p class="mt-4 text-slate-300 text-sm leading-relaxed">{{ $currentLesson->description }}</p>
                </div>
            </div>

            {{-- RIGHT COLUMN: Playlist --}}
            <div class="lg:col-span-4">
                <div
                    class="bg-white rounded-lg border border-slate-200 overflow-hidden shadow-xl flex flex-col h-[calc(100vh-3rem)] sticky top-6">

                    {{-- Playlist Header --}}
                    <div class="p-5 bg-[#fff0e6] border-l-4 border-[#ff6b35]">
                        <h3 class="text-lg font-bold text-slate-800 leading-tight">
                            {{ $course->title }}
                        </h3>
                    </div>

                    {{-- Scrollable List --}}
                    <div class="flex-1 overflow-y-auto custom-scrollbar">
                        @foreach ($course->lessons as $lesson)
                            <a href="{{ route('student.watch', [$course->id, $lesson->id]) }}"
                                class="flex items-center gap-4 p-4 border-b border-slate-100 transition-all
                                {{ $currentLesson->id == $lesson->id ? 'bg-slate-50' : 'bg-white hover:bg-slate-50' }}">

                                <div class="flex-shrink-0 flex items-center gap-3">
                                    {{-- Lesson Number --}}
                                    <span class="text-slate-500 font-medium text-sm w-4">{{ $loop->iteration }}.</span>

                                    {{-- Play Icon Circle with CSS Triangle --}}
                                    <div
                                        class="relative w-8 h-8 rounded-full flex items-center justify-center border-2 {{ $currentLesson->id == $lesson->id ? 'border-[#ff6b35] bg-[#ff6b35]' : 'border-[#ff6b35] bg-white' }}">
                                        <div
                                            class="play-triangle {{ $currentLesson->id == $lesson->id ? 'play-white' : 'play-orange' }}">
                                        </div>
                                    </div>
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h4 class="text-sm font-semibold text-slate-700 truncate">
                                        {{ $lesson->title }}
                                    </h4>
                                </div>

                                {{-- Completion Checkmark --}}
                                @php
                                    $isCompleted = false;
                                    if (isset($lesson->progress) && $lesson->progress->is_completed) {
                                        $isCompleted = true;
                                    }
                                @endphp

                                @if ($isCompleted)
                                    <div class="text-emerald-500 flex-shrink-0">
                                        <span class="text-xl">✓</span>
                                    </div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .custom-scrollbar::-webkit-scrollbar {
            width: 6px;
        }

        .custom-scrollbar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .custom-scrollbar::-webkit-scrollbar-thumb {
            background: #ccc;
            border-radius: 10px;
        }

        /* CSS Play Triangle */
        .play-triangle {
            width: 0;
            height: 0;
            border-style: solid;
            border-width: 5px 0 5px 8px;
            margin-left: 2px;
        }

        .play-orange {
            border-color: transparent transparent transparent #ff6b35;
        }

        .play-white {
            border-color: transparent transparent transparent white;
        }
    </style>

    <script>
        const video = document.getElementById('courseVideo');
        const lessonId = video.dataset.lesson;
        const startPos = parseFloat(video.dataset.start);
        let isAlreadyCompleted = parseInt(video.dataset.completed) === 1;

        // Resume logic (without autoplay)
        video.onloadedmetadata = function() {
            if (startPos > 0) {
                video.currentTime = startPos;
            }
        };

        let lastSaved = 0;
        video.ontimeupdate = function() {
            if (isAlreadyCompleted) return;

            const now = Math.floor(video.currentTime);
            if (now % 5 === 0 && now !== lastSaved) {
                lastSaved = now;
                saveProgress(now, false);
            }
        };

        video.onended = function() {
            if (isAlreadyCompleted) return;
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
                })
                .then(response => {
                    if (completed) isAlreadyCompleted = true;
                })
                .catch(err => console.error(err));
        }
    </script>
@endsection
