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
    </style>

    <div class="max-w-[1600px] mx-auto pb-12">
        {{-- Tabs Navigation --}}
        <div class="mb-6 border-b border-gray-200">
            <div class="flex gap-6">
                <a href="#" class="px-4 py-4 font-bold text-mainText border-b-2 border-primary">Beginner's Guide Training</a>
                <a href="#" class="px-4 py-4 font-bold text-mutedText hover:text-mainText transition-colors">Workbook</a>
            </div>
        </div>

        {{-- Progress Indicator --}}
        <div class="mb-8 pb-6 border-b border-gray-200">
            <div class="flex justify-center gap-3 flex-wrap">
                @php
                    $allVideos = $videos->sortBy('order_column');
                    $videoCount = $allVideos->count();
                @endphp
                @foreach($allVideos as $idx => $video)
                    @php
                        $isCompleted = isset($progressData[$video->id]) && $progressData[$video->id]['completed'];
                        $isCurrent = isset($selected) && $selected->id == $video->id;
                    @endphp
                    <a href="?video={{ $video->id }}" 
                        class="w-10 h-10 rounded-full flex items-center justify-center font-bold text-sm transition-all
                        {{ $isCurrent ? 'bg-primary text-white ring-2 ring-primary ring-offset-2' : ($isCompleted ? 'bg-green-500 text-white' : 'border-2 border-gray-300 text-mainText hover:border-primary') }}">
                        @if($isCompleted)
                            <i class="fas fa-check"></i>
                        @else
                            {{ $idx + 1 }}
                        @endif
                    </a>
                @endforeach
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
            {{-- LEFT: Video Player & Content --}}
            <div class="lg:col-span-8 space-y-8">
                @if(isset($selected) && $selected)
                    {{-- Category Description Box --}}
                    <div class="bg-blue-600 text-white rounded-lg p-6 space-y-2">
                        <h2 class="text-2xl font-bold capitalize">{{ $selected->category }}</h2>
                        <p class="text-sm leading-relaxed">{{ $selected->description }}</p>
                    </div>
                    {{-- Video Player --}}
                    <div class="relative group rounded-2xl overflow-hidden shadow-2xl bg-black aspect-video border border-gray-200/10">
                        <video id="beginner-video" class="video-js vjs-big-play-button vjs-fluid"
                            controls preload="auto" oncontextmenu="return false;">
                            <source src="{{ $selected->video_url }}" type="video/mp4">
                        </video>
                    </div>

                    {{-- Progress Bar --}}
                    <div class="bg-white rounded-lg p-4 border border-gray-200">
                        <div class="h-1.5 w-full bg-gray-100 rounded-full overflow-hidden">
                            <div id="progress-bar" class="h-full bg-blue-500 transition-all duration-500" style="width: 0%"></div>
                        </div>
                        <p class="mt-2 text-[10px] font-bold text-mutedText uppercase tracking-widest" id="progress-label">0% watched</p>
                    </div>

                    {{-- Description & Resources Tabs --}}
                    <div class="bg-white rounded-lg border border-gray-200 overflow-hidden">
                        {{-- Tabs --}}
                        <div class="flex border-b border-gray-200">
                            <button class="tab-trigger flex-1 px-6 py-3 font-bold text-blue-600 border-b-2 border-blue-600" data-tab="description">
                                Description
                            </button>
                            <button class="tab-trigger flex-1 px-6 py-3 font-bold text-gray-500 hover:text-blue-600 transition-colors" data-tab="resources">
                                Resources
                            </button>
                        </div>

                        {{-- Tab Content --}}
                        <div class="p-6">
                            <div id="description-tab" class="tab-content prose prose-slate max-w-none text-sm text-gray-700 leading-relaxed">
                                {!! nl2br(e($selected->description)) !!}
                            </div>
                            <div id="resources-tab" class="tab-content hidden prose prose-slate max-w-none text-sm text-gray-700 leading-relaxed">
                                @if($selected->resources)
                                    {!! nl2br(e($selected->resources)) !!}
                                @else
                                    <p class="text-gray-500">No resources available for this video.</p>
                                @endif
                            </div>
                        </div>
                    </div>
                @else
                    <div class="bg-surface rounded-2xl p-8 border border-gray-100 shadow-sm text-center">
                        <p class="text-mutedText">No sample video available.</p>
                    </div>
                @endif
            </div>

            {{-- RIGHT: Categories & Videos List --}}
            <div class="lg:col-span-4">
                <div class="bg-white rounded-lg border border-gray-200 shadow-sm overflow-hidden">
                    {{-- Categories with Collapsible Sections --}}
                    @php
                        $categoryOrder = ['foundation','growth','scale'];
                        $grouped = $videos->groupBy('category');
                    @endphp

                    @foreach($categoryOrder as $catIndex => $cat)
                        @php
                            $catVideos = $grouped->get($cat, collect());
                            $isExpanded = ($catIndex == 0) ? 'true' : 'false';
                        @endphp

                        <div x-data="{ open: {{ $isExpanded }} }" class="border-b border-gray-200 last:border-b-0">
                            {{-- Category Header --}}
                            <button @click="open = !open" 
                                class="w-full flex items-center justify-between p-4 hover:bg-gray-50 transition-colors">
                                <div class="flex items-center gap-2 flex-1 text-left">
                                    <span class="text-sm font-bold text-mainText capitalize">{{ $cat }}</span>
                                </div>
                                <div class="flex items-center gap-3">
                                    @php
                                        $catCompletedCount = $catVideos->filter(function($v) use ($progressData) {
                                            return isset($progressData[$v->id]) && $progressData[$v->id]['completed'];
                                        })->count();
                                        $catTotalCount = $catVideos->count();
                                    @endphp
                                    <span class="text-[10px] font-bold text-mutedText whitespace-nowrap">
                                        Progress {{ round(($catCompletedCount / max($catTotalCount, 1)) * 100) }}%
                                    </span>
                                    <svg class="w-4 h-4 text-mutedText transition-transform" :class="open ? 'rotate-180' : ''"
                                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                    </svg>
                                </div>
                            </button>

                            {{-- Category Videos List --}}
                            <div x-show="open" x-transition class="bg-gray-50 border-t border-gray-200 divide-y divide-gray-200">
                                @php
                                    $prevCompleted = true;
                                @endphp
                                @foreach($catVideos->sortBy('order_column') as $video)
                                    @php
                                        $isCompleted = isset($progressData[$video->id]) && $progressData[$video->id]['completed'];
                                        $isLocked = !$prevCompleted && !$isCompleted;
                                        $isCurrent = isset($selected) && $selected->id == $video->id;
                                        $prevCompleted = $isCompleted;
                                    @endphp
                                    <a href="?video={{ $video->id }}"
                                        class="flex items-center gap-3 p-4 transition-all hover:bg-white group
                                        {{ $isCurrent ? 'bg-blue-50 border-l-4 border-l-blue-500' : 'hover:border-l-4 hover:border-l-blue-300' }}
                                        {{ $isLocked ? 'opacity-50 cursor-not-allowed' : '' }}"
                                        @if($isLocked) @click.prevent="alert('Almost there! Complete the previous video to unlock this one.')" @endif>

                                        {{-- Video Title --}}
                                        <div class="flex-1 min-w-0">
                                            <h4 class="text-xs font-bold leading-snug {{ $isCurrent ? 'text-blue-600' : 'text-mainText' }} truncate">
                                                {{ $video->title }}
                                            </h4>
                                        </div>

                                        {{-- Status Icon --}}
                                        @if($isCompleted)
                                            <div class="flex-shrink-0 w-6 h-6 bg-green-100 rounded-full flex items-center justify-center">
                                                <i class="fas fa-check text-green-600 text-xs"></i>
                                            </div>
                                        @elseif($isLocked)
                                            <div class="flex-shrink-0">
                                                <i class="fas fa-lock text-gray-400"></i>
                                            </div>
                                        @else
                                            @if($isCurrent)
                                                <div class="flex-shrink-0">
                                                    <span class="flex h-2 w-2 relative">
                                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-blue-500 opacity-75"></span>
                                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-blue-500"></span>
                                                    </span>
                                                </div>
                                            @endif
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

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Tab switching
            document.querySelectorAll('.tab-trigger').forEach(trigger => {
                trigger.addEventListener('click', function() {
                    const tabName = this.dataset.tab;
                    
                    // Hide all tabs
                    document.querySelectorAll('.tab-content').forEach(tab => tab.classList.add('hidden'));
                    
                    // Remove active state from all triggers
                    document.querySelectorAll('.tab-trigger').forEach(t => {
                        t.classList.remove('text-blue-600', 'border-b-2', 'border-b-blue-600');
                        t.classList.add('text-gray-500');
                    });
                    
                    // Show selected tab
                    document.getElementById(tabName + '-tab').classList.remove('hidden');
                    
                    // Add active state to trigger
                    this.classList.add('text-blue-600', 'border-b-2', 'border-b-blue-600');
                    this.classList.remove('text-gray-500');
                });
            });

            const videoEl = document.getElementById('beginner-video');
            if (!videoEl) return;

            const player = videojs(videoEl);
            let lastSaved = 0;
            let isCompleted = {{ isset($selected) && isset($progressData[$selected->id]) && $progressData[$selected->id]['completed'] ? 'true' : 'false' }};

            function updateProgressDisplay(percent) {
                document.getElementById('progress-bar').style.width = percent + '%';
                document.getElementById('progress-label').innerText = Math.round(percent) + '% watched';
            }

            player.on('loadedmetadata', function() {
                const saved = {{ json_encode($progressData) }};
                if (saved && saved[{{ isset($selected) ? $selected->id : 'null' }}]?.seconds && player.duration()) {
                    const percent = (saved[{{ isset($selected) ? $selected->id : 'null' }}].seconds / Math.floor(player.duration())) * 100;
                    updateProgressDisplay(percent);
                }
            });

            player.on('timeupdate', function() {
                const now = Math.floor(player.currentTime());
                const duration = Math.floor(player.duration());
                const percent = duration > 0 ? (now / duration) * 100 : 0;
                updateProgressDisplay(percent);

                if (!isCompleted && now % 10 === 0 && now !== lastSaved) {
                    lastSaved = now;
                    saveProgress(now, false);
                }
            });

            player.on('ended', function() {
                if (!isCompleted) {
                    saveProgress(Math.floor(player.duration()), true);
                    setTimeout(() => location.reload(), 500);
                }
            });

            function saveProgress(seconds, completed) {
                fetch("{{ route('student.progress.update') }}", {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json'
                    },
                    body: JSON.stringify({ seconds, completed, video_id: {{ isset($selected) ? $selected->id : 'null' }} })
                });
                if (completed) isCompleted = true;
            }
        });
    </script>
@endsection
