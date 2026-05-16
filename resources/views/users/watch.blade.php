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

        /* Prevent selection and drag */
        .no-select {
            -webkit-user-select: none;
            -moz-user-select: none;
            -ms-user-select: none;
            user-select: none;
        }

    </style>

    <div class="max-w-[1600px] mx-auto pb-12 no-select">
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
                <div class="relative group rounded-2xl overflow-hidden shadow-2xl bg-black w-full aspect-video border border-gray-200/10">
                    @if($currentLesson && ($currentLesson->bunny_video_id || $currentLesson->bunny_embed_url))
                        @php
                            $libraryId = config('services.bunny.library_id');
                            $videoId = $currentLesson->bunny_video_id;

                            // Generate Secure Token (Pseudo-code implementation for Bunny Token Auth)
                            $securityKey = env('BUNNY_SECURITY_KEY', '');
                            $expires = time() + 14400; // 4 hours from now
                            $token = hash('sha256', $securityKey . $videoId . $expires);

                            if($currentLesson->bunny_embed_url && empty($currentLesson->bunny_video_id)) {
                                $bunnySrc = $currentLesson->bunny_embed_url;
                            } else {
                                $bunnySrc = "https://iframe.mediadelivery.net/embed/{$libraryId}/{$videoId}?token={$token}&expires={$expires}";
                            }

                            // Add parameters for autoplay, preload, etc.
                            $bunnySrc .= (str_contains($bunnySrc, '?') ? '&' : '?') . 'autoplay=true&loop=false&muted=false&preload=true&responsive=true';
                        @endphp
                        {{-- iframe fallback with 16:9 intrinsic ratio container --}}
                        <div style="position:relative;padding-top:56.25%;">
                            <iframe id="bunny-player" src="{{ $bunnySrc }}"
                                loading="lazy"
                                style="border:0;position:absolute;top:0;height:100%;width:100%;"
                                allow="accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;"
                                allowfullscreen="true">
                            </iframe>
                        </div>

                    @else
                        {{-- Legacy video.js player for old lessons --}}
                        <video id="course-video" class="video-js vjs-big-play-button vjs-fluid h-full w-full"
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
                    @endif

                    {{-- Dynamic Watermark Container (Single instance) --}}
                    <div id="video-watermark" class="video-watermark">
                        {{ auth()->user()->email }} ({{ auth()->id() }})
                    </div>

                    {{-- Blackout Overlay (Soft DRM) --}}
                    <div id="video-blackout" class="absolute inset-0 bg-black z-[100] flex flex-col items-center justify-center text-white text-center p-6" style="display: none;">
                         <div id="protection-message" class="space-y-4 max-w-md">
                            <i class="fas fa-user-shield text-4xl text-primary mb-2"></i>
                            <p class="font-bold text-lg">Protected Content</p>
                            <p id="protection-text" class="text-xs opacity-60">Screen recording or sharing is strictly prohibited.</p>
                            
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
                            <button id="mark-complete" 
                                data-lesson-id="{{ $currentLesson->id }}"
                                class="px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all {{ ($progress && $progress->is_completed) ? 'bg-green-50 text-green-600 border border-green-100' : 'bg-primary text-white shadow-md hover:bg-secondary' }}">
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
                            <div id="course-progress-bar" class="h-full bg-primary transition-all duration-500" style="width: {{ $percent }}%"></div>
                        </div>
                        <p class="mt-2 text-[10px] font-bold text-mutedText uppercase tracking-widest"><span id="course-progress-percent">{{ round($percent) }}</span>% Complete</p>
                    </div>

                    {{-- Lessons List --}}
                    <div class="flex-1 overflow-y-auto custom-scrollbar">
                        @foreach ($course->lessons->sortBy('order_column') as $lesson)
                            @php
                                $isCurrent = ($currentLesson && $currentLesson->id == $lesson->id);
                                $isCompleted = optional($lesson->progress)->is_completed;
                            @endphp
                            <a href="{{ route('student.watch', [$course->id, $lesson->id]) }}"
                                data-lesson-item-id="{{ $lesson->id }}"
                                class="lesson-link group flex items-center gap-4 p-5 border-b border-gray-50 transition-all hover:bg-gray-50/50
                                {{ $isCurrent ? 'bg-primary/5 border-l-4 border-l-primary active-lesson' : '' }}">

                                <div class="icon-container flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-bold
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

    @php
        $lessons = $course->lessons->sortBy('order_column')->values();
        $currentIndex = $lessons->search(fn($l) => $l->id == ($currentLesson->id ?? 0));
        $nextLesson = ($currentIndex !== false) ? $lessons->get($currentIndex + 1) : null;
        $nextUrl = $nextLesson ? route('student.watch', [$course->id, $nextLesson->id]) : null;
    @endphp

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // --- Elements & State ---
            const bunnyIframe = document.getElementById('bunny-player');
            const markBtn = document.getElementById('mark-complete');
            const blackout = document.getElementById('video-blackout');
            const progressBar = document.getElementById('course-progress-bar');
            const progressPercent = document.getElementById('course-progress-percent');
            
            const lessonId = "{{ $currentLesson ? $currentLesson->id : '' }}";
            const nextLessonUrl = "{{ $nextUrl }}";
            let isAlreadyCompleted = {{ ($progress && $progress->is_completed) ? 'true' : 'false' }};
            let lastWatchedTime = {{ optional($progress)->last_watched_second ?? 0 }};
            
            let bunnyDuration = 0;
            let lastSavedSecond = lastWatchedTime;
            let isProcessing = false;
            let autoRedirectTriggered = false;

            console.log('Video Tracking Initialized:', { lessonId, isAlreadyCompleted, lastWatchedTime, nextLessonUrl });

            // --- 1. Bunny Stream Integration ---
            if (bunnyIframe) {
                // Subscribe to events explicitly (Standard Player.js)
                function sendToBunny(method, value = null) {
                    if (bunnyIframe && bunnyIframe.contentWindow) {
                        try {
                            const message = JSON.stringify({
                                context: 'player.js',
                                method: method,
                                value: value
                            });
                            bunnyIframe.contentWindow.postMessage(message, '*');
                        } catch(e) {
                            console.error('Error sending message to Bunny:', e);
                        }
                    }
                }

                function subscribe() {
                    console.log('Attempting to subscribe to Bunny events...');
                    ['timeupdate', 'ended', 'pause', 'play'].forEach(evt => {
                        sendToBunny('addEventListener', evt);
                    });
                }

                window.addEventListener('message', function(event) {
                    // Safety check for origin if needed, but Bunny uses multiple subdomains
                    // if (!event.origin.includes('mediadelivery.net')) return;

                    try {
                        let data = event.data;
                        if (typeof data === 'string') {
                            try { data = JSON.parse(data); } catch(e) { return; }
                        }
                        
                        if (!data || (!data.event && !data.method)) return;
                        const eventName = data.event || data.method;

                        // Event: Ready
                        if (eventName === 'ready' || eventName === 'player:ready') {
                            console.log('Bunny Player Ready Event Received');
                            subscribe();
                            
                            // Seek to last watched position if not completed and we have a valid time
                            if (lastWatchedTime > 2 && !isAlreadyCompleted) {
                                console.log('Seeking to last watched position:', lastWatchedTime);
                                sendToBunny('setCurrentTime', lastWatchedTime);
                            }
                        }

                        // Event: Time Update
                        if (eventName === 'timeupdate') {
                            let now = 0;
                            let duration = 0;

                            if (typeof data.value === 'object' && data.value !== null) {
                                now = parseFloat(data.value.seconds || data.value.currentTime || 0);
                                duration = parseFloat(data.value.duration || 0);
                            } else {
                                now = parseFloat(data.value || 0);
                            }

                            if (duration > 0) bunnyDuration = duration;
                            
                            // Only proceed if we have valid numbers
                            if (isNaN(now) || now <= 0) return;

                            // Threshold completion (95%)
                            if (!isAlreadyCompleted && !isProcessing && bunnyDuration > 0) {
                                if (now >= (bunnyDuration * 0.95)) {
                                    console.log('Auto-completion threshold (95%) reached at ' + now + 's of ' + bunnyDuration + 's');
                                    triggerCompletion(now, true);
                                } else if (now >= lastSavedSecond + 20) { 
                                    // Save every 20 seconds to reduce server load but keep accuracy
                                    lastSavedSecond = Math.floor(now);
                                    saveProgress(lastSavedSecond, false);
                                }
                            }
                        }

                        // Event: Ended
                        if (eventName === 'ended') {
                            console.log('Video ended event received.');
                            if (!isAlreadyCompleted && !isProcessing) {
                                triggerCompletion(bunnyDuration || lastSavedSecond || 0, true);
                            } else if (isAlreadyCompleted && !autoRedirectTriggered) {
                                console.log('Video ended and already completed, triggering redirect');
                                handleNextLessonRedirect();
                            }
                        }
                    } catch (e) {
                        console.error('Player Message Error:', e);
                    }
                });

                // Fail-safe: Try to subscribe multiple times in case ready event was missed
                const subscribeInterval = setInterval(() => {
                    if (isProcessing) return;
                    subscribe();
                }, 3000);
                
                // Stop interval after 15 seconds to save resources
                setTimeout(() => clearInterval(subscribeInterval), 15000);
            }

            // --- 2. Manual Completion Button ---
            if (markBtn) {
                markBtn.addEventListener('click', function() {
                    if (isProcessing) return;
                    
                    if (isAlreadyCompleted) {
                        console.log('Already completed, jumping to next lesson');
                        handleNextLessonRedirect();
                        return;
                    }
                    
                    console.log('Manual completion button clicked');
                    triggerCompletion(bunnyDuration || lastSavedSecond || 0, true);
                });
            }

            // --- 3. Core Logic ---
            function triggerCompletion(seconds, shouldRedirect) {
                if (isProcessing) return;
                
                console.log('Triggering lesson completion...', { seconds, shouldRedirect });
                isProcessing = true;
                
                if (markBtn) {
                    markBtn.disabled = true;
                    markBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Finishing...';
                }

                saveProgress(seconds, true, (data) => {
                    if (data && data.status === 'saved') {
                        console.log('Completion saved successfully on server');
                        isAlreadyCompleted = true;
                        updateSidebarAndProgress();
                        
                        if (shouldRedirect) {
                            const redirectUrl = data.next_url || nextLessonUrl;
                            if (redirectUrl && !autoRedirectTriggered) {
                                autoRedirectTriggered = true;
                                showToast("Lesson Completed! Next lesson loading...", "success");
                                console.log('Redirecting to next lesson:', redirectUrl);
                                setTimeout(() => { window.location.href = redirectUrl; }, 1500);
                            } else {
                                console.log('No next lesson found or already redirected');
                                showToast("Course Completed! Well done!", "success");
                                finishButtonUI();
                                isProcessing = false;
                            }
                        } else {
                            showToast("Progress Saved!");
                            finishButtonUI();
                            isProcessing = false;
                        }
                    } else {
                        console.error('Failed to save completion on server', data);
                        isProcessing = false;
                        if (markBtn) {
                            markBtn.disabled = false;
                            markBtn.innerHTML = '<i class="fas fa-check mr-2"></i> Mark as Complete';
                        }
                        showToast("Connection issue. Please try again.", "error");
                    }
                });
            }

            function handleNextLessonRedirect() {
                if (nextLessonUrl && !autoRedirectTriggered) {
                    autoRedirectTriggered = true;
                    showToast("Loading next lesson...", "success");
                    setTimeout(() => { window.location.href = nextLessonUrl; }, 500);
                } else if (!nextLessonUrl) {
                    showToast("No more lessons in this course.", "info");
                }
            }

            function finishButtonUI() {
                if (markBtn) {
                    markBtn.innerHTML = '<i class="fas fa-check-double mr-2"></i> Completed';
                    markBtn.className = 'px-6 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider transition-all bg-green-50 text-green-600 border border-green-100 cursor-default';
                    markBtn.disabled = false; 
                }
            }

            function saveProgress(seconds, completed, callback = null) {
                if (!lessonId) {
                    console.warn('Cannot save progress: No lessonId');
                    return;
                }

                const payload = {
                    lesson_id: lessonId,
                    seconds: Math.floor(seconds),
                    is_completed: completed
                };
                
                console.log('Sending progress update to server:', payload);

                fetch("{{ route('student.progress.update') }}", {
                    method: "POST",
                    headers: {
                        "Content-Type": "application/json",
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    },
                    body: JSON.stringify(payload)
                })
                .then(res => {
                    if (!res.ok) throw new Error('Server returned ' + res.status);
                    return res.json();
                })
                .then(data => {
                    if (callback) callback(data);
                })
                .catch(err => {
                    console.error('Fetch Save Error:', err);
                    if (callback) callback(null);
                });
            }

            function updateSidebarAndProgress() {
                const currentLink = document.querySelector(`.lesson-link[data-lesson-item-id="${lessonId}"]`);
                if (currentLink) {
                    const iconContainer = currentLink.querySelector('.icon-container');
                    if (iconContainer && !iconContainer.querySelector('.fa-check')) {
                        iconContainer.innerHTML = '<i class="fas fa-check"></i>';
                        iconContainer.className = 'icon-container flex-shrink-0 w-8 h-8 rounded-full flex items-center justify-center text-[10px] font-bold bg-green-100 text-green-600';
                    }
                }

                // Recalculate progress percentage
                const total = {{ $course->lessons->count() }};
                const completed = document.querySelectorAll('.icon-container .fa-check').length;
                const newPercent = total > 0 ? Math.round((completed / total) * 100) : 0;

                if (progressBar) progressBar.style.width = newPercent + '%';
                if (progressPercent) progressPercent.innerText = newPercent;
            }

            function showToast(msg, type = "success") {
                const existing = document.querySelector('.custom-toast');
                if (existing) existing.remove();

                const toast = document.createElement('div');
                toast.className = `custom-toast fixed bottom-8 left-1/2 -translate-x-1/2 px-6 py-3 rounded-2xl shadow-2xl z-[9999] flex items-center gap-3 animate-in fade-in slide-in-from-bottom-4 duration-300 ${type === 'error' ? 'bg-red-600' : (type === 'info' ? 'bg-blue-600' : 'bg-gray-900')} text-white`;
                const icon = type === 'error' ? 'fa-exclamation-circle' : (type === 'info' ? 'fa-info-circle' : 'fa-check-circle text-green-400');
                toast.innerHTML = `<i class="fas ${icon}"></i> <span class="text-xs font-bold uppercase tracking-wider">${msg}</span>`;
                document.body.appendChild(toast);
                
                setTimeout(() => {
                    toast.classList.add('opacity-0', 'transition-opacity', 'duration-500');
                    setTimeout(() => toast.remove(), 500);
                }, 3000);
            }

            // --- 4. Security & DRM ---
            function pauseVideo() {
                if (bunnyIframe) {
                    sendToBunny('pause');
                }
            }

            window.addEventListener('blur', () => {
                setTimeout(() => {
                    if (document.activeElement !== bunnyIframe) {
                        if (blackout) blackout.style.display = 'flex';
                        pauseVideo();
                    }
                }, 500);
            });

            window.addEventListener('focus', () => { 
                if (blackout) blackout.style.display = 'none'; 
            });

            document.addEventListener('visibilitychange', () => { 
                if (document.hidden) {
                    if (blackout) blackout.style.display = 'flex';
                    pauseVideo();
                }
            });

            const watermark = document.getElementById('video-watermark');
            if (watermark) {
                const moveWatermark = () => {
                    const container = document.querySelector('.aspect-video');
                    if (container) {
                        const x = Math.random() * (container.offsetWidth - 180);
                        const y = Math.random() * (container.offsetHeight - 60);
                        watermark.style.transform = `translate(${x}px, ${y}px)`;
                        watermark.style.opacity = Math.random() * 0.15 + 0.05;
                    }
                };
                moveWatermark();
                setInterval(moveWatermark, 8000);
            }
        });
    </script>
@endsection
