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
                            // Note: Add BUNNY_SECURITY_KEY to your .env file
                            $securityKey = env('BUNNY_SECURITY_KEY', '');
                            $expires = time() + 14400; // 4 hours from now
                            $token = hash('sha256', $securityKey . $videoId . $expires);

                            if($currentLesson->bunny_embed_url && empty($currentLesson->bunny_video_id)) {
                                $bunnySrc = $currentLesson->bunny_embed_url;
                            } else {
                                $bunnySrc = "https://iframe.mediadelivery.net/embed/{$libraryId}/{$videoId}?token={$token}&expires={$expires}";
                            }

                            // Add parameters for autoplay, preload, etc. if required
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
                        {{-- Legacy video.js player for old lessons that have video_path or hls_path --}}
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

                        {{-- Dynamic Watermark Container for Video.js ONLY --}}
                        <div id="video-watermark" class="video-watermark">
                            {{ auth()->user()->email }} ({{ auth()->id() }})
                        </div>
                    @endif



                    {{-- Dynamic Watermark Container --}}
                    <div id="video-watermark" class="video-watermark">
                        {{ auth()->user()->email }} ({{ auth()->id() }})
                    </div>

                    {{-- Blackout Overlay (Soft DRM) --}}
                    <div id="video-blackout" class="absolute inset-0 bg-black z-[100] flex flex-col items-center justify-center text-white text-center p-6" style="display: none;">
                         <div id="protection-message" class="space-y-4 max-w-md">
                            <i class="fas fa-user-shield text-4xl text-primary mb-2"></i>
                            <p class="font-bold text-lg">Protected Content</p>
                            <p id="protection-text" class="text-xs opacity-60">Screen recording or sharing is strictly prohibited.</p>
                            
                            {{-- Actionable Fix for Hardware Acceleration --}}
                            <div id="hardware-acceleration-fix" class="mt-6 p-4 bg-white/5 rounded-xl border border-white/10 hidden">
                                <p class="text-[11px] font-bold text-primary uppercase tracking-widest mb-2">Likely Solution</p>
                                <p class="text-xs opacity-80 mb-4">It looks like Hardware Acceleration is disabled in your browser. This is required for secure video playback.</p>
                                <div class="flex flex-col gap-2">
                                    <a href="https://support.google.com/chrome/answer/95414?hl=en" target="_blank" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-[10px] font-bold transition-all">Enable in Chrome/Edge</a>
                                    <a href="https://support.mozilla.org/en-US/kb/performance-settings" target="_blank" class="px-4 py-2 bg-white/10 hover:bg-white/20 rounded-lg text-[10px] font-bold transition-all">Enable in Firefox</a>
                                </div>
                            </div>

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

            @if(!$currentLesson || (!$currentLesson->bunny_video_id && !$currentLesson->bunny_embed_url))
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
            @endif

            @if(!$currentLesson || (!$currentLesson->bunny_video_id && !$currentLesson->bunny_embed_url))
                // Prevent standard download right-click for video.js
                player.on('contextmenu', function(e) { e.preventDefault(); });
            @endif



            // Soft DRM Protection Logic
            const blackout = document.getElementById('video-blackout');
            const bunnyIframe = document.getElementById('bunny-player');
            let blackoutTimeout = null;

            function showBlackout(reason = 'general') {
                // console.log('Blackout triggered. Reason:', reason);
                if (blackoutTimeout) clearTimeout(blackoutTimeout);
                
                blackout.style.display = 'flex';
                
                // Show specific message for hardware acceleration
                const hardwareFix = document.getElementById('hardware-acceleration-fix');
                const protectionText = document.getElementById('protection-text');
                
                if (reason === 'no-drm') {
                    protectionText.innerText = 'Your browser security settings are preventing playback.';
                    hardwareFix.classList.remove('hidden');
                } else if (reason === 'devtools') {
                    protectionText.innerText = 'Please close Developer Tools to continue.';
                    hardwareFix.classList.add('hidden');
                } else if (reason === 'focus-loss') {
                    protectionText.innerText = 'Playback paused. Please focus on this tab to continue.';
                    hardwareFix.classList.add('hidden');
                } else {
                    protectionText.innerText = 'Screen recording or sharing is strictly prohibited.';
                    hardwareFix.classList.add('hidden');
                }
                
                // Pause legacy video.js if exists
                if (typeof player !== 'undefined' && player && !player.paused()) {
                    player.pause();
                }
                // Pause Bunny.net iframe via postMessage if exists
                if (bunnyIframe && bunnyIframe.contentWindow) {
                    bunnyIframe.contentWindow.postMessage('{"method":"pause"}', '*');
                }
            }

            function hideBlackout() {
                blackout.style.display = 'none';
                if (blackoutTimeout) clearTimeout(blackoutTimeout);
            }

            // DRM/Hardware Acceleration Check
            async function checkDRMSupport() {
                try {
                    const config = [{
                        initDataTypes: ['cenc'],
                        videoCapabilities: [{ contentType: 'video/mp4; codecs="avc1.42E01E"', robustness: 'SW_SECURE_DECODE' }]
                    }];
                    const access = await navigator.requestMediaKeySystemAccess('com.widevine.alpha', config);
                    // console.log('Widevine DRM supported.');
                } catch (e) {
                    console.warn('Widevine DRM not supported or Hardware Acceleration disabled.', e);
                    // We don't show blackout immediately, but if playback fails, we know why
                    window.drmFailed = true;
                }
            }
            checkDRMSupport();

            // 0. Advanced Action: DevTools Detector (Refined)
            const devtoolsThreshold = 250; 
            setInterval(() => {
                const widthDiff = window.outerWidth - window.innerWidth;
                const heightDiff = window.outerHeight - window.innerHeight;
                
                if (widthDiff > devtoolsThreshold || heightDiff > devtoolsThreshold) {
                    if (blackout.style.display !== 'flex') {
                        showBlackout('devtools');
                    }
                }
            }, 2000);

            // 1. Focus Tracking (with Debounce to prevent false positives on iframe clicks)
            window.addEventListener('blur', function() {
                // Short delay to check if focus shifted to our own iframe
                blackoutTimeout = setTimeout(() => {
                    const activeEl = document.activeElement;
                    if (activeEl && (activeEl.tagName.toLowerCase() === 'iframe' || activeEl === bunnyIframe)) {
                        // console.log('Focus shifted to player iframe, ignoring blur.');
                        return;
                    }
                    showBlackout('focus-loss');
                }, 500);
            });
            
            window.addEventListener('focus', () => {
                // console.log('Window regained focus.');
                hideBlackout();
            });

            // 2. Visibility API (Tab Switching)
            document.addEventListener('visibilitychange', function() {
                if (document.hidden) {
                    showBlackout('tab-hidden');
                } else {
                    hideBlackout();
                }
            });

            // 3. Complete Keyboard Block & Shortcuts
            window.addEventListener('keydown', function(e) {
                // Prevention: Block Screen Capture, Save, Inspect, Print and Source view
                if (
                    e.key === 'PrintScreen' ||
                    (e.ctrlKey && (e.key === 'p' || e.key === 's' || e.key === 'u')) ||
                    (e.ctrlKey && e.shiftKey && (e.key === 'I' || e.key === 'J' || e.key === 'C')) ||
                    e.key === 'F12'
                ) {
                    e.preventDefault();
                    showBlackout('keyboard-shortcut');
                    // Auto-hide after 3 seconds for keyboard accidental triggers
                    setTimeout(hideBlackout, 3000);
                    
                    try { navigator.clipboard.writeText(""); } catch(err) {}
                    return false;
                }

                // Playback shortcuts (only if video player exists and body is target)
                if(typeof player !== 'undefined' && e.target === document.body) {
                    if(e.code === 'Space') { e.preventDefault(); player.paused() ? player.play() : player.pause(); }
                    if(e.code === 'ArrowRight') { player.currentTime(player.currentTime() + 10); }
                    if(e.code === 'ArrowLeft') { player.currentTime(player.currentTime() - 10); }
                }
            });

            // 4. Moving Watermark (Advanced Random X/Y)
            const watermark = document.getElementById('video-watermark');

            function moveWatermark() {
                const container = document.querySelector('.video-js') || document.querySelector('.aspect-video');
                if(!container || !watermark) return;

                const x = Math.random() * (container.offsetWidth - 150);
                const y = Math.random() * (container.offsetHeight - 50);

                watermark.style.transform = `translate(${x}px, ${y}px)`;
                watermark.style.opacity = Math.random() * (0.4 - 0.1) + 0.1;
            }
            setInterval(moveWatermark, 5000);
            moveWatermark();

            // --- UI Update Helpers ---
            function updateUIOnComplete() {
                // 1. Update Main Button
                const markBtn = document.getElementById('mark-complete');
                if (markBtn) {
                    markBtn.innerHTML = '<i class="fas fa-check-double mr-2"></i> Completed';
                    markBtn.classList.remove('bg-primary', 'text-white', 'hover:bg-secondary', 'shadow-md');
                    markBtn.classList.add('bg-green-50', 'text-green-600', 'border', 'border-green-100');
                    markBtn.disabled = true;
                }

                // 2. Update Sidebar Icon for Current Lesson
                const activeLessonSidebar = document.querySelector('a.bg-primary\\/5') || document.querySelector('a.border-l-primary');
                if (activeLessonSidebar) {
                    const iconDiv = activeLessonSidebar.querySelector('.flex-shrink-0.w-8.h-8');
                    if (iconDiv && !iconDiv.querySelector('.fa-check')) {
                        iconDiv.innerHTML = '<i class="fas fa-check"></i>';
                        iconDiv.classList.remove('bg-primary', 'text-white', 'bg-gray-100', 'text-mutedText');
                        iconDiv.classList.add('bg-green-100', 'text-green-600');
                    }
                }

                // 3. Update Mastery Bar
                updateSidebarMastery();

                // 4. Show Toast
                showToast("Video progress saved!");
            }

            function updateSidebarMastery() {
                const totalLessons = {{ $course->lessons->count() }};
                // Re-count all lessons with checkmarks in the sidebar
                const completedLessons = document.querySelectorAll('.fa-check').length;
                const percent = totalLessons > 0 ? Math.round((completedLessons / totalLessons) * 100) : 0;
                
                const progressBar = document.getElementById('course-progress-bar');
                const percentSpan = document.getElementById('course-progress-percent');

                if (progressBar) progressBar.style.width = percent + '%';
                if (percentSpan) percentSpan.innerText = percent;
            }

            function showToast(message) {
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-6 py-3 rounded-2xl shadow-2xl z-[9999] flex items-center gap-3 animate-bounce';
                toast.innerHTML = `<i class="fas fa-check-circle text-green-400"></i> <span class="text-xs font-bold uppercase tracking-wider">${message}</span>`;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 4000);
            }

            // --- Progress Tracking Core ---
            let lastSaved = 0;
            const lessonId = "{{ $currentLesson ? $currentLesson->id : '' }}";
            let isAlreadyCompleted = "{{ ($progress && $progress->is_completed) ? 1 : 0 }}" == 1;
            let lastWatchedTime = {{ optional($progress)->last_watched_second ?? 0 }};

            // Bunny Stream API Event Listener
            window.addEventListener('message', function(event) {
                try {
                    // Check if message is from Bunny
                    if (typeof event.data !== 'string') return;
                    const data = JSON.parse(event.data);
                    
                    if (data.event === 'ready') {
                        if (lastWatchedTime > 0 && !isAlreadyCompleted && bunnyIframe) {
                            bunnyIframe.contentWindow.postMessage(JSON.stringify({
                                context: 'player.js',
                                method: 'setCurrentTime',
                                value: lastWatchedTime
                            }), '*');
                        }
                    }

                    if (data.event === 'timeupdate' && !isAlreadyCompleted) {
                        let now = 0;
                        let duration = 0;

                        if (typeof data.value === 'object') {
                            now = Math.floor(data.value.seconds || 0);
                            duration = Math.floor(data.value.duration || 0);
                        } else {
                            now = Math.floor(data.value || 0);
                        }
                        
                        // Auto-complete if >= 90% watched
                        if (duration > 0 && now >= (duration * 0.90)) {
                            console.log('Video threshold reached (90%) - marking complete');
                            isAlreadyCompleted = true;
                            saveProgress(now, true, true);
                        } 
                        else if (now > 0 && now % 15 === 0 && now !== lastSaved) {
                            lastSaved = now;
                            saveProgress(now, false, false);
                        }
                    }

                    if (data.event === 'ended' && !isAlreadyCompleted) {
                        console.log('Video ended - marking complete');
                        isAlreadyCompleted = true;
                        saveProgress(9999, true, true);
                    }
                } catch (e) {
                    // Not a JSON message or not for us
                }
            });

            @if(!$currentLesson || (!$currentLesson->bunny_video_id && !$currentLesson->bunny_embed_url))
                player.ready(function() {
                    if (lastWatchedTime > 0 && !isAlreadyCompleted) {
                        player.currentTime(lastWatchedTime);
                    }
                });

                player.on('timeupdate', function() {
                    if (isAlreadyCompleted) return;
                    const now = Math.floor(player.currentTime());
                    const duration = Math.floor(player.duration());

                    if (duration > 0 && now >= (duration * 0.90)) {
                        console.log('Video threshold reached (90%) - marking complete');
                        isAlreadyCompleted = true;
                        saveProgress(now, true, true);
                    } 
                    else if (now > 0 && now % 15 === 0 && now !== lastSaved) {
                        lastSaved = now;
                        saveProgress(now, false, false);
                    }
                });

                player.on('ended', function() {
                    if (!isAlreadyCompleted) {
                        console.log('Video ended - marking complete');
                        isAlreadyCompleted = true;
                        saveProgress(Math.floor(player.duration() || 9999), true, true);
                    }
                });
            @endif

            document.getElementById('mark-complete')?.addEventListener('click', function() {
                if (isAlreadyCompleted) return;
                const duration = (typeof player !== 'undefined' && player) ? Math.floor(player.duration() || 0) : 0;
                this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Updating...';
                saveProgress(duration, true, true);
            });

            function saveProgress(seconds, completed, shouldReload = false) {
                if (!lessonId) return;
                
                // Optimistically update UI if completed
                if (completed) {
                    updateUIOnComplete();
                }

                fetch("{{ route('student.progress.update') }}", {
                    method: "POST",
                    keepalive: true,
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
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'saved') {
                        console.log('Progress synced successfully');
                        // If it was a manual click or auto-end, we might want to go to next lesson eventually, 
                        // but for now, let's just stay on the page as requested for "automatic" feedback.
                        if (shouldReload) {
                           // Instead of immediate reload, maybe just update sidebar again to be sure
                           updateSidebarMastery();
                        }
                    }
                })
                .catch(err => {
                    console.error('Progress sync failed:', err);
                });
            }
        });
    </script>
@endsection
