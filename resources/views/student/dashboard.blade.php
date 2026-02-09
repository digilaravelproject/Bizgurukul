@php
    if (auth()->user()->hasRole('Admin')) {
        $adminUrl = url('/admin/dashboard');
        echo "<script>window.location.href = '$adminUrl';</script>";
        exit();
    }
@endphp
@extends('layouts.user.app')

@section('content')
    {{-- ApexCharts CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

    <div class="space-y-8 pb-12">

        {{-- 1. HERO SECTION (Modern Gradient Banner with Brand Colors) --}}
        <div class="relative overflow-hidden rounded-3xl brand-gradient shadow-xl shadow-primary/20 text-white">
            {{-- Background Pattern --}}
            <div class="absolute top-0 right-0 -mt-10 -mr-10 w-64 h-64 bg-white/10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -mb-10 -ml-10 w-40 h-40 bg-white/10 rounded-full blur-3xl"></div>

            <div
                class="relative z-10 p-8 md:p-10 flex flex-col md:flex-row items-start md:items-center justify-between gap-6">
                <div>
                    <div class="flex items-center gap-2 mb-2">
                        <span
                            class="bg-white/20 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider border border-white/10">
                            Student Dashboard
                        </span>
                        @if (isset($myCourses) && $myCourses->count() > 0)
                            <span
                                class="bg-emerald-500/20 text-emerald-100 backdrop-blur-md px-3 py-1 rounded-full text-xs font-bold flex items-center gap-1 border border-emerald-500/30">
                                <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 animate-pulse"></span> Active
                            </span>
                        @endif
                    </div>
                    <h1 class="text-3xl md:text-4xl font-extrabold tracking-tight mb-2">
                        Hello, {{ explode(' ', $user->name)[0] }}! 👋
                    </h1>
                    <p class="text-white/90 text-sm md:text-base max-w-xl leading-relaxed">
                        You've learned <strong>{{ isset($myCourses) ? $myCourses->sum('completed_lessons') : 0 }}
                            lessons</strong> so far. Keep pushing your limits!
                    </p>
                </div>

                {{-- Quick Action / Wallet --}}
                <div
                    class="bg-white/10 backdrop-blur-lg border border-white/20 p-4 rounded-2xl min-w-[180px] text-center shadow-lg">
                    <p class="text-xs text-white/80 uppercase tracking-wider font-bold mb-1">Total Earnings</p>
                    <h2 class="text-2xl font-extrabold text-white">₹{{ number_format($totalEarnings ?? 0, 2) }}</h2>
                </div>
            </div>
        </div>

        {{-- 2. STATS OVERVIEW --}}
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Enrolled Courses --}}
            <div
                class="bg-customWhite rounded-2xl p-6 border border-primary/10 shadow-sm hover:shadow-md transition-all group flex items-center gap-5">
                <div
                    class="w-14 h-14 rounded-2xl bg-blue-500/10 text-blue-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-mutedText text-xs font-bold uppercase tracking-wider">Enrolled Courses</p>
                    <h3 class="text-2xl font-extrabold text-mainText mt-1">{{ isset($myCourses) ? $myCourses->count() : 0 }}
                    </h3>
                </div>
            </div>

            {{-- Bundles --}}
            <div
                class="bg-customWhite rounded-2xl p-6 border border-primary/10 shadow-sm hover:shadow-md transition-all group flex items-center gap-5">
                <div
                    class="w-14 h-14 rounded-2xl bg-purple-500/10 text-purple-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10">
                        </path>
                    </svg>
                </div>
                <div>
                    <p class="text-mutedText text-xs font-bold uppercase tracking-wider">Active Bundles</p>
                    <h3 class="text-2xl font-extrabold text-mainText mt-1">{{ isset($myBundles) ? $myBundles->count() : 0 }}
                    </h3>
                </div>
            </div>

            {{-- Completed Lessons --}}
            <div
                class="bg-customWhite rounded-2xl p-6 border border-primary/10 shadow-sm hover:shadow-md transition-all group flex items-center gap-5">
                <div
                    class="w-14 h-14 rounded-2xl bg-emerald-500/10 text-emerald-600 flex items-center justify-center group-hover:scale-110 transition-transform">
                    <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div>
                    <p class="text-mutedText text-xs font-bold uppercase tracking-wider">Lessons Done</p>
                    <h3 class="text-2xl font-extrabold text-mainText mt-1">
                        {{ isset($myCourses) ? $myCourses->sum('completed_lessons') : 0 }}</h3>
                </div>
            </div>
        </div>

        {{-- 3. MY COURSES SECTION --}}
        <div>
            <div class="flex items-center justify-between mb-6">
                <h3 class="text-xl font-bold text-mainText flex items-center gap-2">
                    <span class="w-1 h-6 bg-primary rounded-full"></span>
                    My Courses
                </h3>
                <a href="{{ url('/courses') }}"
                    class="group flex items-center gap-1 text-sm font-bold text-primary hover:opacity-80 transition-colors">
                    Explore Library
                    <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3">
                        </path>
                    </svg>
                </a>
            </div>

            @if (isset($myCourses) && $myCourses->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach ($myCourses as $course)
                        <div
                            class="bg-customWhite rounded-3xl overflow-hidden border border-primary/10 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 group flex flex-col h-full">
                            {{-- Thumbnail with Play Overlay --}}
                            <div class="relative h-48 bg-navy/5 overflow-hidden">
                                <img src="{{ $course->thumbnail_url ?? 'https://via.placeholder.com/400x200?text=Course' }}"
                                    alt="{{ $course->title }}"
                                    class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-700">

                                <div
                                    class="absolute inset-0 bg-black/20 group-hover:bg-black/40 transition-colors flex items-center justify-center">
                                    <div
                                        class="w-12 h-12 bg-white/90 rounded-full flex items-center justify-center shadow-lg opacity-0 group-hover:opacity-100 transform scale-75 group-hover:scale-100 transition-all duration-300">
                                        <svg class="w-5 h-5 text-primary ml-0.5" fill="currentColor" viewBox="0 0 24 24">
                                            <path d="M8 5v14l11-7z" />
                                        </svg>
                                    </div>
                                </div>

                                {{-- Category Badge --}}
                                @if ($course->category)
                                    <span
                                        class="absolute top-4 left-4 bg-white/90 backdrop-blur text-xs font-bold px-3 py-1 rounded-full text-mainText shadow-sm uppercase tracking-wide">
                                        {{ $course->category->name }}
                                    </span>
                                @endif
                            </div>

                            {{-- Card Body --}}
                            <div class="p-6 flex-1 flex flex-col">
                                <h4 class="text-mainText font-bold text-lg leading-snug mb-2 line-clamp-2">
                                    {{ $course->title }}
                                </h4>

                                {{-- Progress Section --}}
                                <div class="mt-auto pt-4">
                                    <div class="flex justify-between items-end mb-2">
                                        <span class="text-xs font-bold text-mutedText uppercase">Progress</span>
                                        <span
                                            class="text-xs font-bold text-primary">{{ $course->progress_percent ?? 0 }}%</span>
                                    </div>
                                    <div class="w-full bg-navy/5 rounded-full h-2 overflow-hidden">
                                        <div class="bg-primary h-2 rounded-full transition-all duration-1000 ease-out"
                                            style="width: {{ $course->progress_percent ?? 0 }}%"></div>
                                    </div>
                                    <div class="mt-3 flex justify-between items-center">
                                        <span class="text-[11px] font-medium text-mutedText">
                                            {{ $course->completed_lessons ?? 0 }} / {{ $course->total_lessons ?? 0 }}
                                            Lessons
                                        </span>
                                        <a href="{{ url('/course/learn/' . $course->id) }}"
                                            class="text-xs font-bold bg-primary/10 text-primary px-4 py-2 rounded-xl hover:bg-primary hover:text-white transition-all">
                                            {{ ($course->progress_percent ?? 0) > 0 ? 'Resume' : 'Start' }}
                                        </a>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                {{-- Empty State --}}
                <div class="bg-customWhite rounded-3xl border border-dashed border-primary/20 p-12 text-center">
                    <div class="w-20 h-20 bg-primary/5 rounded-full flex items-center justify-center mx-auto mb-6">
                        <svg class="w-10 h-10 text-primary/50" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-mainText">Start Your Journey</h3>
                    <p class="text-mutedText mt-2 mb-8 max-w-md mx-auto">You haven't enrolled in any courses yet. Browse our
                        catalog to find your next skill.</p>
                    <a href="{{ url('/courses') }}"
                        class="inline-flex items-center px-8 py-3 rounded-xl brand-gradient text-white font-bold text-sm shadow-lg shadow-primary/20 hover:opacity-90 transition-all hover:-translate-y-0.5">
                        Browse Courses
                    </a>
                </div>
            @endif
        </div>

        {{-- 4. MY BUNDLES (If Any) --}}
        @if (isset($myBundles) && $myBundles->count() > 0)
            <div class="pt-8 border-t border-navy/5">
                <h3 class="text-xl font-bold text-mainText mb-6 flex items-center gap-2">
                    <span class="w-1 h-6 bg-purple-600 rounded-full"></span>
                    My Bundles
                </h3>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    @foreach ($myBundles as $bundle)
                        <div
                            class="bg-customWhite rounded-2xl p-4 border border-primary/10 shadow-sm hover:shadow-md transition-all flex gap-4 items-center">
                            <div class="w-16 h-16 rounded-xl bg-navy/5 overflow-hidden flex-shrink-0">
                                <img src="{{ $bundle->thumbnail_url ?? 'https://via.placeholder.com/150' }}"
                                    class="w-full h-full object-cover">
                            </div>
                            <div class="flex-1 min-w-0">
                                <h4 class="font-bold text-mainText truncate">{{ $bundle->title }}</h4>
                                <a href="{{ url('/bundle/' . $bundle->slug) }}"
                                    class="text-xs text-purple-600 font-bold hover:underline">View Content</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif

        {{-- 5. AFFILIATE & EARNINGS SECTION --}}
        <div class="mt-8 bg-customWhite rounded-3xl border border-primary/10 shadow-xl shadow-primary/5 overflow-hidden">
            <div class="p-8 border-b border-navy/5 flex flex-col md:flex-row items-center justify-between gap-4">
                <div>
                    <h3 class="text-xl font-bold text-mainText">Earnings Overview</h3>
                    <p class="text-sm text-mutedText">Track your referral income performance.</p>
                </div>
                <div
                    class="flex items-center gap-2 text-sm bg-emerald-500/10 text-emerald-600 px-4 py-2 rounded-full font-bold border border-emerald-500/20">
                    <span>Lifetime Earnings:</span>
                    <span>₹{{ number_format($totalEarnings ?? 0, 2) }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 divide-y lg:divide-y-0 lg:divide-x divide-navy/5">
                {{-- Chart Area --}}
                <div class="lg:col-span-2 p-6">
                    <div id="earningsChart" class="w-full h-[300px]"></div>
                </div>

                {{-- Link Generator Tool --}}
                <div class="lg:col-span-1 p-6 bg-navy/5" x-data="{
                    type: 'course',
                    selectedId: '',
                    baseUrl: '{{ url('/') }}',
                    refCode: '{{ $user->referral_code ?? '' }}',
                    generated: '',
                    generate() {
                        if (!this.selectedId) return;
                        let path = this.type === 'course' ? '/course/' : '/bundle/';
                        this.generated = this.baseUrl + path + this.selectedId + '?ref=' + this.refCode;
                    }
                }">

                    <h4 class="text-sm font-bold text-mainText uppercase tracking-wider mb-4">Link Generator</h4>

                    {{-- Main Link --}}
                    <div class="mb-6 p-4 bg-customWhite rounded-xl border border-primary/10">
                        <label class="text-[10px] font-bold text-mutedText uppercase mb-1 block">Your Default Link</label>
                        <div class="flex gap-2" x-data="{ copied: false }">
                            <input type="text" readonly value="{{ $referralLink ?? '' }}"
                                class="w-full bg-transparent text-xs text-mainText font-medium focus:outline-none truncate">
                            <button
                                @click="navigator.clipboard.writeText('{{ $referralLink ?? '' }}'); copied=true; setTimeout(()=>copied=false, 2000)"
                                class="text-primary hover:opacity-80 text-xs font-bold">
                                <span x-show="!copied">COPY</span>
                                <span x-show="copied" class="text-emerald-500">DONE</span>
                            </button>
                        </div>
                    </div>

                    {{-- Custom Generator --}}
                    <div class="space-y-3">
                        <label class="text-xs font-bold text-mutedText">Create Custom Product Link</label>
                        <select x-model="type" @change="selectedId=''; generated=''"
                            class="w-full bg-customWhite border border-primary/10 rounded-xl px-4 py-2.5 text-xs font-bold text-mainText focus:ring-2 focus:ring-primary outline-none">
                            <option value="course">Specific Course</option>
                            <option value="bundle">Specific Bundle</option>
                        </select>

                        <select x-model="selectedId" @change="generate()"
                            class="w-full bg-customWhite border border-primary/10 rounded-xl px-4 py-2.5 text-xs font-bold text-mainText focus:ring-2 focus:ring-primary outline-none">
                            <option value="">Select Product...</option>
                            <template x-if="type === 'course'">
                                @foreach ($allCourses ?? [] as $c)
                                    <option value="{{ $c->id }}">{{ $c->title }}</option>
                                @endforeach
                            </template>
                            <template x-if="type === 'bundle'">
                                @foreach ($allBundles ?? [] as $b)
                                    <option value="{{ $b->id }}">{{ $b->title }}</option>
                                @endforeach
                            </template>
                        </select>

                        <div x-show="generated" style="display:none;" class="pt-2">
                            <button @click="navigator.clipboard.writeText(generated); alert('Link Copied to Clipboard!')"
                                class="w-full py-3 rounded-xl bg-primary text-white text-xs font-bold shadow-lg shadow-primary/20 hover:opacity-90 transition-all">
                                Copy Generated Link
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    {{-- CHART SCRIPT --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var options = {
                series: [{
                    name: 'Earnings',
                    data: @json($chartData ?? [])
                }],
                chart: {
                    type: 'area',
                    height: 300,
                    toolbar: {
                        show: false
                    },
                    fontFamily: 'inherit',
                    animations: {
                        enabled: true
                    }
                },
                colors: ['#4F46E5'],
                fill: {
                    type: 'gradient',
                    gradient: {
                        shadeIntensity: 1,
                        opacityFrom: 0.4,
                        opacityTo: 0.05,
                        stops: [0, 90, 100]
                    }
                },
                dataLabels: {
                    enabled: false
                },
                stroke: {
                    curve: 'smooth',
                    width: 3
                },
                xaxis: {
                    categories: @json($chartLabels ?? []),
                    axisBorder: {
                        show: false
                    },
                    axisTicks: {
                        show: false
                    },
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '11px',
                            fontWeight: 600
                        }
                    }
                },
                yaxis: {
                    labels: {
                        style: {
                            colors: '#94a3b8',
                            fontSize: '11px',
                            fontWeight: 600
                        },
                        formatter: (value) => "₹" + value
                    }
                },
                grid: {
                    borderColor: '#f3f4f6',
                    strokeDashArray: 4,
                    padding: {
                        left: 10,
                        right: 0
                    }
                },
                tooltip: {
                    theme: 'light',
                    y: {
                        formatter: (val) => "₹" + val
                    }
                }
            };
            var chart = new ApexCharts(document.querySelector("#earningsChart"), options);
            chart.render();
        });
    </script>
@endsection
