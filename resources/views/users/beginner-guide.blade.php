@extends('layouts.user.app')

@php $progressData = $progressData ?? []; @endphp

@section('content')
    <!-- Video.js for legacy/fallback support -->
    <link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />
    <script src="https://vjs.zencdn.net/8.10.0/video.min.js"></script>

    <style>
        .custom-scrollbar::-webkit-scrollbar { width: 3px; }
        .custom-scrollbar::-webkit-scrollbar-track { background: transparent; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(var(--color-primary), 0.1); border-radius: 10px; }
        .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(var(--color-primary), 0.2); }

        .roadmap-card {
            background: rgba(var(--color-bg-card), 0.95);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(var(--color-white), 0.05);
        }

        .category-badge-foundation { @apply bg-primary; }
        .category-badge-growth { background: #f59e0b; }
        .category-badge-scale { background: #10b981; }

        /* Iframe optimization */
        .video-container iframe {
            border-radius: 1.5rem;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.5);
        }
    </style>

    <div class="max-w-[1600px] mx-auto pb-10">
        {{-- Slim Meta Header --}}
        <div class="flex flex-col md:flex-row md:items-end justify-between gap-6 mb-8 pt-4">
            <div class="space-y-1">
                <div class="flex items-center gap-3">
                    <span class="w-1.5 h-10 bg-primary rounded-full"></span>
                    <h1 class="text-3xl font-black text-mainText tracking-tight uppercase">Roadmap <span class="text-primary/80">Guide</span></h1>
                </div>
                <p class="text-[10px] font-black text-mutedText uppercase tracking-[0.2em] opacity-60 ml-5">Master modules for {{ auth()->user()->name }}</p>
            </div>

            {{-- Compact Progress --}}
            @php
                $allVideos = $categories->pluck('videos')->flatten();
                $totalCount = $allVideos->count();
                $_progress = $progressData ?? [];
                $completedCount = $allVideos->filter(fn($v) => isset($_progress[$v->id]) && $_progress[$v->id]['completed'])->count();
                $globalPercent = $totalCount > 0 ? round(($completedCount / $totalCount) * 100) : 0;
            @endphp
            <div class="flex items-center gap-5 bg-surface/40 p-4 pr-6 rounded-2xl border border-white/5">
                <div class="relative w-12 h-12 flex items-center justify-center">
                    <svg class="w-full h-full transform -rotate-90">
                        <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="4" fill="transparent" class="text-white/5" />
                        <circle cx="24" cy="24" r="20" stroke="currentColor" stroke-width="4" fill="transparent"
                            class="text-primary" stroke-dasharray="{{ 2 * pi() * 20 }}"
                            stroke-dashoffset="{{ 2 * pi() * 20 * (1 - $globalPercent/100) }}" stroke-linecap="round" />
                    </svg>
                    <span class="absolute text-[10px] font-black text-mainText">{{ $globalPercent }}%</span>
                </div>
                <div>
                    <div class="text-[9px] font-black text-mutedText uppercase tracking-widest opacity-50">Mastery Score</div>
                    <div class="text-sm font-black text-mainText">{{ $completedCount }} / {{ $totalCount }} Modules</div>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:items-start">
            {{-- Main Viewing Area --}}
            <div class="lg:col-span-8 space-y-6">
                @if(isset($selected))
                    {{-- Secure Video Player --}}
                    <div class="relative w-full aspect-video rounded-3xl overflow-hidden bg-black shadow-2xl ring-1 ring-white/10 video-container">
                        @if($selected->bunny_video_id)
                            @php
                                $libraryId = config('services.bunny.library_id');
                                $videoId = $selected->bunny_video_id;
                                $securityKey = env('BUNNY_SECURITY_KEY', '');
                                $expires = time() + 14400; // 4 hours from now
                                $token = hash('sha256', $securityKey . $videoId . $expires);
                                $bunnySrc = "https://iframe.mediadelivery.net/embed/{$libraryId}/{$videoId}?token={$token}&expires={$expires}&autoplay=true";
                            @endphp
                            <iframe id="bunny-player" src="{{ $bunnySrc }}" loading="lazy"
                                style="border:0;position:absolute;top:0;height:100%;width:100%;"
                                allow="accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;" allowfullscreen>
                            </iframe>
                        @elseif($selected->bunny_embed_url)
                            @php
                                // If it's pure iframe code, we'll try to refine it for this container
                                $embed = $selected->bunny_embed_url;
                                if(str_contains($embed, '<iframe')) {
                                    $embed = str_replace(['width="', 'height="'], ['width="100%" height="100%" data-old="', 'data-old2="'], $embed);
                                    // Add ID for postMessage if not present
                                    if(!str_contains($embed, 'id="')) {
                                        $embed = str_replace('<iframe', '<iframe id="bunny-player"', $embed);
                                    }
                                }
                            @endphp
                            {!! $embed !!}
                        @else
                            <video id="roadmap-player" class="video-js vjs-big-play-button vjs-fluid h-full w-full"
                                controls preload="auto" oncontextmenu="return false;">
                                <source src="{{ $selected->video_url }}" type="video/mp4">
                            </video>
                        @endif
                    </div>

                    {{-- Module Metadata --}}
                    <div class="bg-surface p-8 rounded-[2rem] border border-white/5 relative overflow-hidden">
                        <div class="relative z-10">
                            <div class="flex flex-col md:flex-row md:items-start justify-between gap-6 mb-10">
                                <div class="space-y-3">
                                    <div class="flex items-center gap-3">
                                        <span class="px-4 py-1 rounded-full text-[9px] font-black text-white bg-primary uppercase tracking-widest shadow-lg shadow-primary/20">
                                            {{ optional($selected->category_rel)->name }}
                                        </span>
                                        @php $_curProg = $progressData ?? []; @endphp
                                        @if(isset($_curProg[$selected->id]) && $_curProg[$selected->id]['completed'])
                                            <div class="flex items-center gap-1.5 px-3 py-1 rounded-full bg-emerald-500/10 text-emerald-500 text-[9px] font-black uppercase tracking-wider">
                                                <i class="fas fa-check-double"></i> Verified
                                            </div>
                                        @endif
                                    </div>
                                    <h2 class="text-2xl md:text-3xl font-black text-mainText leading-tight tracking-tight">{{ $selected->title }}</h2>
                                </div>

                                <button id="mark-complete-btn" class="flex-shrink-0 px-8 py-4 rounded-xl font-black text-[10px] uppercase tracking-[0.2em] transition-all
                                    {{ (isset($_curProg[$selected->id]) && $_curProg[$selected->id]['completed']) ? 'bg-emerald-500/10 text-emerald-500 border border-emerald-500/20' : 'bg-primary text-white hover:scale-[1.05]' }}">
                                    <i class="fas fa-certificate mr-2"></i>
                                    {{ (isset($_curProg[$selected->id]) && $_curProg[$selected->id]['completed']) ? 'Validated' : 'Validate Module' }}
                                </button>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-8 border-t border-white/5 pt-8">
                                <div class="space-y-4">
                                    <p class="text-[10px] font-black text-mutedText uppercase tracking-widest opacity-50 flex items-center gap-2">
                                        <i class="fas fa-info-circle text-primary"></i> Introduction
                                    </p>
                                    <p class="text-mainText/70 font-medium text-sm leading-relaxed">
                                        {{ $selected->description ?: 'No introductory text available for this session.' }}
                                    </p>
                                </div>
                                @if($selected->resources)
                                <div class="space-y-4">
                                    <p class="text-[10px] font-black text-mutedText uppercase tracking-widest opacity-50 flex items-center gap-2">
                                        <i class="fas fa-link text-primary"></i> Session Links
                                    </p>
                                    <div class="p-5 rounded-2xl bg-white/5 border border-white/5 text-xs font-bold text-primary leading-relaxed whitespace-pre-wrap">
                                        {{ $selected->resources }}
                                    </div>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-surface rounded-[3rem] p-24 text-center border-2 border-dashed border-white/5">
                        <i class="fas fa-map-marked-alt text-6xl text-white/5 mb-6"></i>
                        <h2 class="text-xl font-black text-mainText uppercase tracking-widest">Select Your Session</h2>
                        <p class="text-xs text-mutedText font-bold max-w-sm mx-auto mt-2 opacity-60 italic">Please pick a training module from the roadmap to begin.</p>
                    </div>
                @endif
            </div>

            {{-- Compact Sidebar Roadmap --}}
            <div class="lg:col-span-4 space-y-6 lg:sticky lg:top-24">
                <div class="roadmap-card rounded-[2.5rem] flex flex-col h-[calc(100vh-12rem)] overflow-hidden shadow-2xl border border-white/5 ring-1 ring-black">
                    <div class="p-6 border-b border-white/5 flex items-center gap-3">
                        <i class="fas fa-route text-primary text-sm opacity-60"></i>
                        <h3 class="text-mainText font-black text-xs uppercase tracking-[0.15em]">Step-by-Step Roadmap</h3>
                    </div>

                    <div class="flex-1 overflow-y-auto custom-scrollbar p-4 space-y-8">
                        @foreach($categories as $category)
                            @php
                                $catVideos = $category->videos;
                                if($catVideos->isEmpty()) continue;
                                $_prog = $progressData ?? [];
                                $catCompleted = $catVideos->filter(fn($v) => isset($_prog[$v->id]) && $_prog[$v->id]['completed'])->count();
                                $catPerc = round(($catCompleted / $catVideos->count()) * 100);
                            @endphp

                            <div class="space-y-4">
                                <div class="flex items-center justify-between px-2 mb-1">
                                    <h4 class="text-[9px] font-black text-mutedText uppercase tracking-[0.2em] opacity-40">{{ $category->name }}</h4>
                                    <span class="text-[8px] font-black text-primary/60 uppercase tracking-widest">{{ $catPerc }}% Done</span>
                                </div>

                                <div class="space-y-2">
                                    @foreach($catVideos as $v)
                                        @php
                                            $isActive = isset($selected) && $selected->id == $v->id;
                                            $_inProg = $progressData ?? [];
                                            $isDone = isset($_inProg[$v->id]) && $_inProg[$v->id]['completed'];
                                        @endphp
                                        <a href="?video={{ $v->id }}"
                                            class="flex items-center gap-4 p-4 rounded-2xl transition-all duration-300 group
                                            {{ $isActive ? 'bg-primary/10 border border-primary/20 ring-1 ring-primary/10' : 'hover:bg-white/5' }}">

                                            <div class="flex-shrink-0 w-8 h-8 rounded-lg flex items-center justify-center font-black text-[10px]
                                                {{ $isActive ? 'bg-primary text-white shadow-lg shadow-primary/20' : ($isDone ? 'bg-emerald-500/20 text-emerald-500' : 'bg-white/5 text-mutedText group-hover:bg-white/10') }}">
                                                @if($isDone)
                                                    <i class="fas fa-check"></i>
                                                @else
                                                    {{ $loop->iteration }}
                                                @endif
                                            </div>

                                            <div class="flex-1 min-w-0">
                                                <h5 class="text-xs font-black leading-snug truncate {{ $isActive ? 'text-primary' : 'text-mainText/90 group-hover:text-mainText' }}">
                                                    {{ $v->title }}
                                                </h5>
                                                <span class="text-[8px] font-bold text-mutedText uppercase tracking-widest opacity-40 italic">
                                                    {{ $category->name }} Module
                                                </span>
                                            </div>

                                            @if($isActive)
                                                <div class="w-1 h-3 bg-primary animate-pulse rounded-full shadow-[0_0_8px_rgba(var(--color-primary),1)]"></div>
                                            @endif
                                        </a>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const videoEl = document.getElementById('roadmap-player');
            const bunnyIframe = document.getElementById('bunny-player');
            let player = null;
            let lastSaved = 0;
            @php $_finalProg = $progressData ?? []; @endphp
            let isCompleted = {{ (isset($selected) && isset($_finalProg[$selected->id]) && $_finalProg[$selected->id]['completed']) ? 'true' : 'false' }};
            let lastWatchedTime = {{ (isset($selected) && isset($_finalProg[$selected->id])) ? $_finalProg[$selected->id]['seconds'] : 0 }};
            const videoId = "{{ isset($selected) ? $selected->id : '' }}";

            function updateUIOnComplete() {
                // 1. Update Main Button
                const markBtn = document.getElementById('mark-complete-btn');
                if (markBtn) {
                    markBtn.innerHTML = '<i class="fas fa-check-double mr-2"></i> Validated';
                    markBtn.classList.remove('bg-primary', 'text-white', 'hover:scale-[1.05]');
                    markBtn.classList.add('bg-emerald-500/10', 'text-emerald-500', 'border', 'border-emerald-500/20');
                }

                // 2. Update Sidebar Icon (if applicable)
                const activeVideoLink = document.querySelector('a.border-l-primary');
                if (activeVideoLink) {
                    const statusDiv = activeVideoLink.querySelector('.w-6.h-6');
                    if (statusDiv) {
                        statusDiv.innerHTML = '<i class="fas fa-check text-[10px]"></i>';
                        statusDiv.classList.remove('bg-primary/10', 'text-primary');
                        statusDiv.classList.add('bg-emerald-500', 'text-white');
                    }
                }

                // 3. Update Circular Progress Mastery Score
                updateCircularProgress();

                // 4. Show Toast
                showToast("Module validated successfully!");
            }

            function updateCircularProgress() {
                const totalModules = {{ $categories->pluck('videos')->flatten()->count() }};
                // Count checkmarks in sidebar
                const completedModules = document.querySelectorAll('.fa-check').length;
                const percent = totalModules > 0 ? Math.round((completedModules / totalModules) * 100) : 0;
                
                const circle = document.querySelector('circle.text-primary');
                const percentText = document.querySelector('.absolute.text-\\[10px\\].font-black');
                const countText = document.querySelector('.text-sm.font-black.text-mainText');

                if (circle) {
                    const radius = 20;
                    const circumference = 2 * Math.PI * radius;
                    const offset = circumference * (1 - percent / 100);
                    circle.style.strokeDashoffset = offset;
                }
                if (percentText) percentText.innerText = percent + '%';
                if (countText) countText.innerText = `${completedModules} / ${totalModules} Modules`;
            }

            function showToast(message) {
                const toast = document.createElement('div');
                toast.className = 'fixed bottom-8 left-1/2 -translate-x-1/2 bg-gray-900 text-white px-6 py-3 rounded-2xl shadow-2xl z-[9999] flex items-center gap-3 animate-bounce';
                toast.innerHTML = `<i class="fas fa-certificate text-yellow-400"></i> <span class="text-[10px] font-black uppercase tracking-widest">${message}</span>`;
                document.body.appendChild(toast);
                setTimeout(() => toast.remove(), 4000);
            }

            // 1. Bunny Stream API Event Listener
            window.addEventListener('message', function(event) {
                try {
                    const data = JSON.parse(event.data);
                    if (data.context === 'player.js' || data.event) {
                        if (data.event === 'ready') {
                            if (lastWatchedTime > 0 && !isCompleted && bunnyIframe) {
                                bunnyIframe.contentWindow.postMessage(JSON.stringify({
                                    context: 'player.js',
                                    method: 'setCurrentTime',
                                    value: lastWatchedTime
                                }), '*');
                            }
                        }

                        if (data.event === 'timeupdate' && !isCompleted) {
                            let now = 0;
                            let duration = 0;
                            if (typeof data.value === 'object') {
                                now = Math.floor(data.value.seconds || 0);
                                duration = Math.floor(data.value.duration || 0);
                            } else {
                                now = Math.floor(data.value || 0);
                            }
                            
                            if (duration > 0 && now >= (duration * 0.90)) {
                                isCompleted = true;
                                saveProgress(now, true, true);
                            } 
                            else if (now > 0 && now % 15 === 0 && now !== lastSaved) {
                                lastSaved = now;
                                saveProgress(now, false, false);
                            }
                        }

                        if (data.event === 'ended' && !isCompleted) {
                            isCompleted = true;
                            saveProgress(9999, true, true);
                        }
                    }
                } catch (e) {}
            });

            if (videoEl) {
                player = videojs(videoEl, { fluid: true });
                player.ready(function() {
                    if (lastWatchedTime > 0 && !isCompleted) {
                        player.currentTime(lastWatchedTime);
                    }
                });
                player.on('timeupdate', function() {
                    if (isCompleted) return;
                    const now = Math.floor(player.currentTime());
                    const duration = Math.floor(player.duration());
                    if (duration > 0 && now >= (duration * 0.90)) {
                        isCompleted = true;
                        saveProgress(now, true, true);
                    } 
                    else if (now > 0 && now % 15 === 0 && now !== lastSaved) {
                        lastSaved = now;
                        saveProgress(now, false, false);
                    }
                });
                player.on('ended', function() {
                    if (!isCompleted) {
                        isCompleted = true;
                        saveProgress(Math.floor(player.duration()), true, true);
                    }
                });
            }

            const markBtn = document.getElementById('mark-complete-btn');
            if (markBtn) {
                markBtn.addEventListener('click', function() {
                    if (isCompleted) return;
                    const duration = player ? Math.floor(player.duration()) : 0;
                    this.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Syncing...';
                    saveProgress(duration, true, true);
                });
            }

            function saveProgress(seconds, completed, shouldReload = false) {
                if (!videoId) return;

                // Optimistically update UI
                if (completed) {
                    updateUIOnComplete();
                }

                fetch("{{ route('student.progress.update') }}", {
                    method: 'POST',
                    keepalive: true,
                    headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}', 'Content-Type': 'application/json' },
                    body: JSON.stringify({ seconds, completed, video_id: videoId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.status === 'saved') {
                        // console.log('Roadmap progress saved');
                        if (shouldReload) {
                            updateCircularProgress();
                        }
                    }
                })
                .catch(err => {
                    console.error('Sync failed:', err);
                });
                if (completed) isCompleted = true;
            }
        });
    </script>
@endsection
