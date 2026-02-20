@extends('layouts.user.app')

@section('content')
    <link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />
    <script src="https://vjs.zencdn.net/8.10.0/video.min.js"></script>

    <style>
        .video-js .vjs-big-play-button {
            background-color: #FF6B35 !important;
            border-radius: 50% !important;
            width: 80px !important;
            height: 80px !important;
            line-height: 80px !important;
            margin-top: -40px !important;
            margin-left: -40px !important;
            border: none !important;
            box-shadow: 0 10px 25px -5px rgba(255, 107, 53, 0.4) !important;
        }

        .vjs-menu-button-popup .vjs-menu { width: 10em; }
        .video-js .vjs-control-bar { background-color: rgba(15, 23, 42, 0.9); backdrop-filter: blur(8px); }
        .vjs-icon-placeholder:before { color: white; }

        /* Watermark */
        .video-watermark {
            position: absolute;
            z-index: 10;
            color: rgba(255, 255, 255, 0.2);
            font-size: 14px;
            pointer-events: none;
            user-select: none;
            font-weight: bold;
            text-shadow: 1px 1px 2px rgba(0,0,0,0.5);
            transition: all 1s ease-in-out;
        }

        @media print {
            body { display: none !important; }
        }

        .video-js {
            user-select: none !important;
            -webkit-user-select: none !important;
            -moz-user-select: none !important;
            -ms-user-select: none !important;
        }

    </style>

    <div class="max-w-[1600px] mx-auto pb-12">
        {{-- Slim Breadcrumb --}}
        <div class="flex items-center gap-2 mb-6 text-[10px] font-bold uppercase tracking-widest text-mutedText">
            <a href="{{ route('student.my-courses') }}" class="hover:text-primary transition-colors">My Learning</a>
            <i class="fas fa-chevron-right text-[8px] opacity-30"></i>
            <span class="text-mainText">{{ $course->title }}</span>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- LEFT: Player & Info --}}
            <div class="lg:col-span-8 space-y-8">
                {{-- 1. Custom Player --}}
                <div class="relative group rounded-2xl overflow-hidden shadow-2xl bg-black aspect-video border border-gray-200/10">
                    <video id="course-video" class="video-js vjs-big-play-button vjs-fluid"
                        controls
                        preload="auto"
                        poster="{{ optional($currentLesson)->thumbnail_url ?? $course->thumbnail_url }}"
                        oncontextmenu="return false;">
                        @if($currentLesson)
                            @php
                                $hlsUrl = $currentLesson->lesson_file_url;
                                $mp4Url = $currentLesson->video_path ? Storage::url($currentLesson->video_path) : null;
                                $isHls = str_ends_with($hlsUrl, '.m3u8');
                            @endphp

                            @if($isHls)
                                <source src="{{ $hlsUrl }}" type="application/x-mpegURL">
                            @endif

                            @if($mp4Url)
                                <source src="{{ $mp4Url }}" type="video/mp4">
                            @endif
                        @endif
                    </video>



                    {{-- Dynamic Watermark Container --}}
                    <div id="video-watermark" class="video-watermark">
                        {{ auth()->user()->email }} ({{ auth()->id() }})
                    </div>

                    {{-- Blackout Overlay (Soft DRM) --}}
                    <div id="video-blackout" class="absolute inset-0 bg-black z-[100] flex flex-col items-center justify-center text-white text-center p-6" style="display: none;">
                         <div class="space-y-4">
                            <div class="w-16 h-16 bg-white/10 rounded-full flex items-center justify-center mx-auto">
                                <i class="fas fa-shield-alt text-2xl text-primary"></i>
                            </div>
                            <p class="font-bold text-lg">Screen Protection Active</p>
                            <p class="text-xs opacity-60">Playback is disabled while screen sharing or tab is inactive.</p>
                            <p class="text-[10px] uppercase tracking-widest text-primary font-bold mt-4">Focus to Resume</p>
                        </div>
                    </div>
                </div>




                {{-- 2. Lesson Content --}}
                <div class="bg-surface rounded-2xl p-8 border border-gray-100 shadow-sm">
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-6">
                        <div class="space-y-1">
                            <span class="text-[10px] font-bold text-primary uppercase tracking-widest">
                                Lesson {{ $currentLesson ? $course->lessons->where('id', '<', $currentLesson->id)->count() + 1 : '0' }} of {{ $course->lessons->count() }}
                            </span>
                            <h1 class="text-2xl font-bold text-mainText leading-tight">
                                {{ $currentLesson ? $currentLesson->title : 'No Lessons Found' }}
                            </h1>
                        </div>

                        @if($currentLesson)
                        <div class="flex items-center gap-3">
                            <button id="mark-complete" class="px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ ($progress && $progress->is_completed) ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-primary text-white shadow-md hover:bg-secondary' }}">
                                <i class="fas fa-{{ ($progress && $progress->is_completed) ? 'check-double' : 'check' }} mr-2"></i>
                                {{ ($progress && $progress->is_completed) ? 'Completed' : 'Mark as Complete' }}
                            </button>
                        </div>
                        @endif
                    </div>

                    <div class="prose prose-slate max-w-none text-sm text-mutedText leading-relaxed">
                        {{ $currentLesson ? $currentLesson->description : 'No description available.' }}
                    </div>
                </div>
            </div>

            {{-- RIGHT: Playlist --}}
            <div class="lg:col-span-4">
                <div class="bg-surface rounded-2xl border border-gray-200 shadow-sm flex flex-col h-[calc(100vh-8rem)] sticky top-24 overflow-hidden">
                    {{-- Header --}}
                    <div class="p-6 border-b border-gray-50">
                        <h3 class="text-base font-bold text-mainText leading-tight">Course Content</h3>
                        <div class="mt-2 h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                            @php
                                $totalLessons = $course->lessons->count();
                                $completedCount = $course->lessons->filter(fn($l) => optional($l->progress)->is_completed)->count();
                                $percent = $totalLessons > 0 ? ($completedCount / $totalLessons) * 100 : 0;
                            @endphp
                            <div class="h-full bg-primary transition-all duration-500" style="width: {{ $percent }}%"></div>
                        </div>
                        <p class="mt-2 text-[10px] font-bold text-mutedText uppercase tracking-widest">{{ round($percent) }}% Complete</p>
                    </div>

                    {{-- Lessons List --}}
                    <div class="flex-1 overflow-y-auto custom-scrollbar">
                        @foreach ($course->lessons->sortBy('order_column') as $lesson)
                            @php
                                $isCurrent = ($currentLesson && $currentLesson->id == $lesson->id);
                                $isCompleted = optional($lesson->progress)->is_completed;
                            @endphp
                            <a href="{{ route('student.watch', [$course->id, $lesson->id]) }}"
                                class="group flex items-center gap-4 p-5 border-b border-gray-50 transition-all hover:bg-gray-50/50
                                {{ $isCurrent ? 'bg-primary/5 border-l-4 border-l-primary' : '' }}">

                                <div class="flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-bold
                                    {{ $isCurrent ? 'bg-primary text-white' : ($isCompleted ? 'bg-green-100 text-green-600' : 'bg-gray-100 text-mutedText') }}">
                                    @if($isCompleted)
                                        <i class="fas fa-check"></i>
                                    @else
                                        {{ $loop->iteration }}
                                    @endif
                                </div>

                                <div class="flex-1 min-w-0">
                                    <h4 class="text-xs font-bold leading-snug {{ $isCurrent ? 'text-primary' : 'text-mainText group-hover:text-primary' }} transition-colors truncate">
                                        {{ $lesson->title }}
                                    </h4>
                                    <span class="text-[9px] font-bold text-mutedText uppercase tracking-tighter">Video Lesson</span>
                                </div>

                                @if($isCurrent)
                                    <div class="flex-shrink-0">
                                        <span class="flex h-2 w-2 relative">
                                            <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-primary opacity-75"></span>
                                            <span class="relative inline-flex rounded-full h-2 w-2 bg-primary"></span>
                                        </span>
                                    </div>
                                @endif
                            </a>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            if (!window.videojs) return console.error("Video.js not loaded");

            const player = videojs('course-video', {
                fluid: true,
                playbackRates: [0.5, 1, 1.25, 1.5, 2],
                html5: {
                    vhs: {
                        withCredentials: true
                    },
                    nativeVideoTracks: false,
                    nativeAudioTracks: false,
                    nativeTextTracks: false
                },
                controlBar: {
                    children: [
                        'playToggle',
                        'volumePanel',
                        'currentTimeDisplay',
                        'timeDivider',
                        'durationDisplay',
                        'progressControl',
                        'liveDisplay',
                        'remainingTimeDisplay',
                        'customControlSpacer',
                        'playbackRateMenuButton',
                        'chaptersButton',
                        'descriptionsButton',
                        'subsCapsButton',
                        'audioTrackButton',
                        'fullscreenToggle'
                    ]
                }
            });

            player.on('error', function() {
                const error = player.error();
                const errorDisplay = document.createElement('div');
                errorDisplay.className = 'absolute inset-0 flex items-center justify-center bg-black/90 text-white p-6 text-center z-50';
                errorDisplay.innerHTML = `
                    <div class="space-y-4">
                        <div class="w-16 h-16 bg-red-500/20 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="fas fa-play text-2xl text-red-500"></i>
                        </div>
                        <p class="font-bold text-lg">Unable to play video</p>
                        <p class="text-sm opacity-60">This video might be restricted or your session may have expired.</p>
                        <button onclick="location.reload()" class="px-6 py-2 bg-primary text-white rounded-full text-sm font-bold mt-4 hover:scale-105 transition-transform">Reload Player</button>
                    </div>
                `;
                document.querySelector('.video-js').appendChild(errorDisplay);
            });

            // Prevent standard download right-click
            player.on('contextmenu', function(e) { e.preventDefault(); });



            // Soft DRM Protection Logic
            const blackout = document.getElementById('video-blackout');

            function showBlackout() {
                blackout.style.display = 'flex';
                if (player && !player.paused()) {
                    player.pause();
                }
            }

            function hideBlackout() {
                blackout.style.display = 'none';
            }

            // 1. Focus Tracking
            window.addEventListener('blur', showBlackout);
            window.addEventListener('focus', hideBlackout);

            // 2. Visibility API (Tab Switching)
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    showBlackout();
                } else {
                    hideBlackout();
                }
            });

            // 3. Screen Capture Shortcut (PrintScreen)
            window.addEventListener('keyup', function(e) {
                if (e.key === 'PrintScreen') {
                    showBlackout();
                    setTimeout(hideBlackout, 2000);
                    // Add to clipboard empty string if possible (hacky)
                    navigator.clipboard.writeText("");
                }
            });

            // Keyboard Shortcuts & Protection
            document.addEventListener('keydown', function(e) {
                // Prevention: Block Save, Inspect, and Source view
                if (e.ctrlKey && (e.key === 's' || e.key === 'u' || (e.shiftKey && (e.key === 'I' || e.key === 'C' || e.key === 'J')))) {
                    e.preventDefault();
                    return false;
                }
                if (e.key === 'F12' || e.key === 'PrintScreen') {
                    showBlackout();
                    setTimeout(hideBlackout, 1000);
                    e.preventDefault();
                    return false;
                }

                // Playback shortcuts
                if(e.code === 'Space' && e.target === document.body) {
                    e.preventDefault();
                    player.paused() ? player.play() : player.pause();
                }
                if(e.code === 'ArrowRight' && e.target === document.body) player.currentTime(player.currentTime() + 10);
                if(e.code === 'ArrowLeft' && e.target === document.body) player.currentTime(player.currentTime() - 10);
            });

            // Watermark Logic - Moves every 8 seconds to a new random spot
            const watermark = document.getElementById('video-watermark');

            function moveWatermark() {
                const container = document.querySelector('.video-js');
                if(!container) return;
                const maxX = container.offsetWidth - watermark.offsetWidth - 40;
                const maxY = container.offsetHeight - watermark.offsetHeight - 40;
                const randomX = Math.floor(Math.random() * maxX) + 20;
                const randomY = Math.floor(Math.random() * maxY) + 20;

                watermark.style.transition = 'all 1s ease-in-out';
                watermark.style.left = randomX + 'px';
                watermark.style.top = randomY + 'px';
                watermark.style.opacity = Math.random() * (0.3 - 0.1) + 0.1; // Pulsing opacity
            }
            setInterval(moveWatermark, 8000);
            moveWatermark();

            // Progress Tracking
            let lastSaved = 0;
            const lessonId = "{{ $currentLesson ? $currentLesson->id : '' }}";
            let isAlreadyCompleted = "{{ ($progress && $progress->is_completed) ? 1 : 0 }}" == 1;

            player.on('timeupdate', function() {
                if (isAlreadyCompleted) return;
                const now = Math.floor(player.currentTime());
                if (now % 10 === 0 && now !== lastSaved) {
                    lastSaved = now;
                    saveProgress(now, false);
                }
            });

            player.on('ended', function() {
                if (!isAlreadyCompleted) {
                    saveProgress(Math.floor(player.duration()), true);
                    location.reload(); // Refresh to update list
                }
            });

            document.getElementById('mark-complete')?.addEventListener('click', function() {
                saveProgress(Math.floor(player.duration()), true);
                location.reload();
            });

            function saveProgress(seconds, completed) {
                if (!lessonId) return;
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


        });
    </script>
@endsection
