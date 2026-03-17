@extends('layouts.user.app')

@section('content')
    <div class="max-w-7xl mx-auto px-4 py-8" x-data="{ tab: 'bundles', videoModal: false, activeVideo: '' }">

        {{-- Compact Header --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-12">
            <div>
                <h2 class="text-2xl font-bold text-mainText tracking-tight">
                    Course <span class="text-primary">Catalog</span>
                </h2>
                <p class="text-sm text-mutedText font-medium mt-1">Upgrade your skills with our curated learning paths.</p>
            </div>

            {{-- Minimal Tab Switcher --}}
            <div class="inline-flex p-1 bg-gray-200/50 rounded-xl border border-gray-200 shadow-sm">
                <button @click="tab = 'bundles'"
                    :class="tab === 'bundles' ? 'bg-primary text-white shadow-sm' : 'text-mutedText hover:text-mainText'"
                    class="px-6 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-layer-group text-[10px]"></i>
                    Bundles
                </button>
                <button @click="tab = 'courses'"
                    :class="tab === 'courses' ? 'bg-primary text-white shadow-sm' : 'text-mutedText hover:text-mainText'"
                    class="px-6 py-2 rounded-lg text-xs font-bold uppercase tracking-wider transition-all duration-200 flex items-center gap-2">
                    <i class="fas fa-book-open text-[10px]"></i>
                    Courses
                </button>
            </div>
        </div>

        {{-- Grid Container --}}
        <div class="relative">

            {{-- Bundles Grid --}}
            <div x-show="tab === 'bundles'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

                @forelse($bundles as $bundle)
                    @php
                        $isUnlocked = auth()->check() && $bundle->isPurchasedBy(auth()->id());
                        $canUpgrade =
                            auth()->check() &&
                            auth()->user()->canUpgradeBundles() &&
                            $bundle->preference_index > auth()->user()->maxBundlePreferenceIndex();
                        $effectivePrice = $bundle->getEffectivePriceForUser(auth()->user());
                        $originalPrice = (auth()->check() && auth()->user()->referrer) ? $bundle->affiliate_price : $bundle->final_price;
                        $upgradeTimeLeft = auth()->check() ? auth()->user()->upgradeTimeLeftSeconds() : 0;
                    @endphp

                    <div
                        x-data="{
                            time: {{ $upgradeTimeLeft }},
                            effectivePrice: '₹{{ number_format($effectivePrice, 0) }}',
                            originalPrice: '₹{{ number_format($originalPrice, 0) }}'
                        }"
                        x-init="if (time > 0) setInterval(() => { if (time > 0) time--; }, 1000)"
                        class="bg-surface rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col h-full group relative">
                        {{-- Countdown Overlay if Upgradable --}}
                        @if (!$isUnlocked && $canUpgrade && $upgradeTimeLeft > 0)
                            <div x-show="time > 0"
                                class="absolute -top-1 -right-1 bg-red-500 text-white text-[9px] font-black uppercase px-3 py-1.5 rounded-bl-xl z-20 shadow-md flex items-center gap-1.5">
                                <i class="fas fa-clock animate-pulse"></i>
                                <span class="tracking-widest"
                                    x-text="Math.floor(time / 3600).toString().padStart(2, '0') + ':' + Math.floor((time % 3600) / 60).toString().padStart(2, '0') + ':' + Math.floor(time % 60).toString().padStart(2, '0')"></span>
                            </div>
                        @endif

                        {{-- Compact Image --}}
                        <div class="relative h-40 overflow-hidden bg-gray-100">
                            <img src="{{ $bundle->thumbnail_url }}"
                                class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500" loading="lazy">

                            @if ($isUnlocked)
                                <div
                                    class="absolute top-3 right-3 bg-green-500/90 text-white text-[9px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1 shadow-sm z-10">
                                    <div class="w-1 h-1 bg-white rounded-full animate-pulse"></div>
                                    PURCHASED
                                </div>
                            @endif
                        </div>

                        {{-- Card Content --}}
                        <div class="p-5 flex flex-col flex-grow relative z-10">
                            <h3
                                class="text-base font-bold text-mainText leading-snug group-hover:text-primary transition-colors line-clamp-1">
                                {{ $bundle->title }}
                            </h3>

                            <p class="text-xs text-mutedText mt-2 line-clamp-2 leading-relaxed">
                                {{ strip_tags($bundle->description) }}
                            </p>

                            {{-- Footer Meta (Price hidden, Buttons Full Width) --}}
                            <div class="mt-auto pt-5 border-t border-gray-50">
                                @if ($isUnlocked)
                                    <a href="{{ route('student.my-courses') }}"
                                        class="w-full flex items-center justify-center bg-green-50 text-green-700 border border-green-200 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-green-100 hover:shadow-sm transition-all duration-300 transform hover:-translate-y-0.5">
                                        <i class="fas fa-play-circle mr-2 text-sm"></i> View Course
                                    </a>
                                @else
                                    <a href="{{ route('student.checkout', ['type' => 'bundle', 'id' => $bundle->id]) }}"
                                        class="w-full flex items-center justify-center bg-primary text-white px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-secondary hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5">
                                        @if ($canUpgrade)
                                            <i x-show="time > 0" class="fas fa-arrow-up mr-2"></i>
                                            <span x-text="time > 0 ? 'Upgrade Now' : 'Unlock Now'">Upgrade Now</span>
                                        @else
                                            <i class="fas fa-unlock-alt mr-2"></i> Unlock Now
                                        @endif
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full py-16 text-center bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                        <p class="text-xs font-bold text-mutedText uppercase tracking-widest">No bundles active</p>
                    </div>
                @endforelse
            </div>

            {{-- Courses Grid --}}
            <div x-show="tab === 'courses'" x-transition:enter="transition ease-out duration-300"
                x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">

                @forelse($courses as $course)
                    @php
                        $isUnlocked = auth()->check() && $course->isPurchasedBy(auth()->id());
                        $coursePrice = (auth()->check() && auth()->user()->referrer) ? $course->affiliate_price : $course->final_price;
                    @endphp

                    <div
                        class="bg-surface rounded-2xl border border-gray-200 shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden flex flex-col h-full group relative">
                        {{-- Image Container --}}
                        <div class="relative h-40 overflow-hidden bg-gray-100">
                            <img src="{{ $course->thumbnail_url }}"
                                class="w-full h-full object-cover grayscale-[0.3] group-hover:grayscale-0 transition-all duration-500" loading="lazy">

                            {{-- PURCHASED Badge (Same as Bundle) --}}
                            @if ($isUnlocked)
                                <div
                                    class="absolute top-3 right-3 bg-green-500/90 text-white text-[9px] font-bold px-2.5 py-1 rounded-full flex items-center gap-1 shadow-sm z-10">
                                    <div class="w-1 h-1 bg-white rounded-full animate-pulse"></div>
                                    PURCHASED
                                </div>
                            @endif

                            {{-- Play Minimal Overlay --}}
                            <div
                                class="absolute inset-0 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all duration-300 bg-mainText/40 backdrop-blur-[1px]">
                                <button @click="videoModal = true; activeVideo = '{{ $course->demo_video_url }}'"
                                    class="bg-white text-primary h-12 w-12 rounded-full flex items-center justify-center shadow-lg transform scale-75 group-hover:scale-100 transition-all z-20">
                                    <i class="fas fa-play text-sm ml-1"></i>
                                </button>
                            </div>

                            {{-- Lock Icon for locked courses --}}
                            @if (!$isUnlocked)
                                <div
                                    class="absolute top-2.5 left-2.5 bg-gray-900/60 backdrop-blur-sm p-1.5 rounded-lg border border-white/10 z-10">
                                    <i class="fas fa-lock text-white text-[10px]"></i>
                                </div>
                            @endif
                        </div>

                        <div class="p-5 flex flex-col flex-grow relative z-10">
                            <h3
                                class="text-base font-bold text-mainText group-hover:text-primary transition-colors leading-snug line-clamp-1">
                                {{ $course->title }}
                            </h3>
                            <p class="text-xs text-mutedText mt-2 line-clamp-2 leading-relaxed">
                                {{ strip_tags($course->description) }}
                            </p>

                            {{-- Footer Meta (Price hidden, Buttons Full Width) --}}
                            <div class="mt-auto pt-5 border-t border-gray-50">
                                {{-- Conditional Start/Unlock button layout --}}
                                @if ($isUnlocked)
                                    <a href="{{ route('student.watch', $course->id) }}"
                                        class="w-full flex items-center justify-center bg-green-50 text-green-700 border border-green-200 px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-green-100 hover:shadow-sm transition-all duration-300 transform hover:-translate-y-0.5">
                                        <i class="fas fa-play mr-2 text-sm"></i> Start Learning
                                    </a>
                                @else
                                    <a href="{{ route('student.courses.show', $course->id) }}"
                                        class="w-full flex items-center justify-center bg-primary text-white px-4 py-2.5 rounded-xl text-xs font-bold uppercase tracking-wider hover:bg-secondary hover:shadow-md transition-all duration-300 transform hover:-translate-y-0.5">
                                        <i class="fas fa-unlock-alt mr-2"></i> Unlock Now
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @empty
                    <div
                        class="col-span-full py-16 text-center bg-gray-50/50 rounded-2xl border border-dashed border-gray-200">
                        <p class="text-xs font-bold text-mutedText uppercase tracking-widest">No courses active</p>
                    </div>
                @endforelse
            </div>
        </div>

        {{-- Pagination --}}
        <div class="mt-12" x-show="tab === 'courses'">
            {{ $courses->links() }}
        </div>

        {{-- Video Modal (Minimal) --}}
        <div x-show="videoModal" x-cloak
            class="fixed inset-0 z-[100] flex items-center justify-center p-4 bg-mainText/40 backdrop-blur-md"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">

            <div @click="videoModal = false; activeVideo = ''" class="absolute inset-0"></div>

            <div class="relative bg-black w-full max-w-4xl aspect-video rounded-2xl overflow-hidden shadow-2xl border border-gray-800"
                x-show="videoModal" x-transition:enter="transition ease-out duration-300 transform scale-95"
                x-transition:enter-end="scale-100">

                <button @click="videoModal = false; activeVideo = ''"
                    class="absolute top-4 right-4 z-10 text-white/50 hover:text-white transition-all p-2 bg-white/10 rounded-lg backdrop-blur-md">
                    <i class="fas fa-times"></i>
                </button>

                <template x-if="activeVideo">
                    <video :src="activeVideo" class="w-full h-full object-contain" controls autoplay
                        controlsList="nodownload"></video>
                </template>
            </div>
        </div>
    </div>
@endsection
