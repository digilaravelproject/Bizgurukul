@php
    if (auth()->user()->hasRole('Admin')) {
        $adminUrl = url('/admin/dashboard');
        echo "<script>window.location.href = '$adminUrl';</script>";
        exit();
    }
@endphp

@extends('layouts.user.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>

<div class="space-y-8 pb-12 font-sans text-mainText" x-data="dashboardHandler()">

    {{-- 1. WELCOME HERO SECTION --}}
    <div class="relative overflow-hidden rounded-[2rem] bg-navy p-8 md:p-12 text-customWhite border border-primary/20 shadow-2xl">
        {{-- Background Accents --}}
        <div class="absolute -top-24 -right-24 h-96 w-96 rounded-full bg-primary/20 blur-[100px] animate-pulse"></div>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-8">
            <div class="max-w-2xl">
                <div class="flex items-center gap-3 mb-4">
                    <span class="bg-primary/20 backdrop-blur-md px-4 py-1.5 rounded-full text-[10px] font-black uppercase tracking-[2px] border border-primary/20 text-primary">
                        Student Ecosystem
                    </span>
                    <span class="flex items-center gap-1.5 text-[10px] font-black uppercase bg-emerald-500/20 px-4 py-1.5 rounded-full border border-emerald-500/30 text-emerald-400">
                        <span class="w-2 h-2 rounded-full bg-emerald-400 animate-ping"></span> Account Active
                    </span>
                </div>
                <h1 class="text-4xl md:text-5xl font-mainText tracking-tighter mb-4">
                    Great to see you, {{ explode(' ', $user->name)[0] }}!
                </h1>
                <p class="text-lg text-customWhite/70 font-medium max-w-lg leading-relaxed">
                    You have mastered <span class="text-customWhite font-black underline decoration-primary decoration-2">{{ $myCourses->sum('completed_lessons') }} lessons</span> across your enrolled programs.
                </p>
            </div>

            {{-- Quick Referral Link Card --}}
            <div class="w-full md:w-auto min-w-[320px]">
                <div class="bg-surface/10 backdrop-blur-2xl border border-customWhite/10 p-6 rounded-[2rem] shadow-2xl relative overflow-hidden group">
                    <p class="text-[10px] font-black text-customWhite/40 uppercase tracking-[3px] mb-3 text-center">Your Affiliate Link</p>
                    <div class="flex items-center gap-2 bg-navy/50 p-2 rounded-xl border border-customWhite/5">
                        <input type="text" readonly value="{{ $referralLink }}" class="bg-transparent border-none text-xs text-primary font-bold w-full focus:ring-0 truncate select-all">
                        <button onclick="copyToClipboard('{{ $referralLink }}')" class="bg-primary text-customWhite p-2.5 rounded-lg hover:bg-secondary transition-all active:scale-95 shadow-lg">
                            <i class="fas fa-copy"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- 2. STATS GRID --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
        @php
            $stats = [
                ['Enrolled Courses', $myCourses->count(), 'fa-graduation-cap', 'primary'],
                ['Pending Earnings', '₹'.number_format($pendingEarnings, 0), 'fa-clock', 'secondary'],
                ['Lessons Completed', $myCourses->sum('completed_lessons'), 'fa-check-circle', 'secondary'],
                ['Total Payouts', '₹'.number_format($totalEarnings, 0), 'fa-wallet', 'primary']
            ];
        @endphp

        @foreach($stats as $stat)
        <div class="bg-surface rounded-3xl p-6 border border-primary/5 shadow-sm hover:shadow-xl transition-all group flex flex-col justify-between h-40">
            <div class="flex justify-between items-start">
                <p class="text-[10px] font-black text-mutedText uppercase tracking-widest">{{ $stat[0] }}</p>
                <div class="w-10 h-10 rounded-xl bg-{{ $stat[3] }}/10 text-{{ $stat[3] }} flex items-center justify-center transition-transform group-hover:scale-110 group-hover:rotate-6">
                    <i class="fas {{ $stat[2] }}"></i>
                </div>
            </div>
            <h3 class="text-3xl font-black text-mainText tracking-tighter">{{ $stat[1] }}</h3>
        </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        {{-- 3. EARNINGS ANALYTICS --}}
        <div class="lg:col-span-2 bg-surface rounded-[2.5rem] border border-primary/10 shadow-sm p-8 flex flex-col">
            <div class="flex items-center justify-between mb-10">
                <div>
                    <h3 class="text-xl font-black text-mainText uppercase tracking-tighter">Earnings Performance</h3>
                    <p class="text-xs text-mutedText font-medium">Revenue flow for the last 7 days</p>
                </div>
                <select class="bg-navy/5 border-none rounded-xl text-[10px] font-black uppercase tracking-widest px-4 py-2 outline-none cursor-pointer hover:bg-navy/10 transition">
                    <option>Last 7 Days</option>
                    <option>Current Month</option>
                </select>
            </div>
            <div id="earningsChart" class="flex-1 min-h-[300px]"></div>
        </div>

        {{-- 4. AFFILIATE LINK GENERATOR --}}
        <div class="bg-navy rounded-[2.5rem] p-8 text-customWhite relative overflow-hidden shadow-2xl border border-primary/20">
            <div class="absolute top-0 right-0 w-32 h-32 brand-gradient opacity-10 blur-3xl"></div>

            <h3 class="text-xl font-black uppercase tracking-widest mb-8 text-primary flex items-center gap-3">
                <i class="fas fa-link"></i> Smart Generator
            </h3>

            <div class="space-y-6" x-data="{ type: 'course', selectedId: '', generated: '' }">
                <div>
                    <label class="text-[10px] font-black uppercase text-customWhite/40 tracking-[2px] mb-3 block">Target Category</label>
                    <select x-model="type" class="w-full bg-customWhite/5 border border-customWhite/10 rounded-2xl px-5 py-4 text-sm font-bold text-customWhite focus:border-primary outline-none transition-all">
                        <option value="course" class="bg-navy">Individual Course</option>
                        <option value="bundle" class="bg-navy">Premium Bundle</option>
                    </select>
                </div>

                <div>
                    <label class="text-[10px] font-black uppercase text-customWhite/40 tracking-[2px] mb-3 block">Choose Product</label>
                    <select x-model="selectedId" class="w-full bg-customWhite/5 border border-customWhite/10 rounded-2xl px-5 py-4 text-sm font-bold text-customWhite focus:border-primary outline-none transition-all">
                        <option value="" class="bg-navy text-mutedText">-- Choose from list --</option>
                        <template x-if="type === 'course'">
                            @foreach($allCourses as $c) <option value="{{ $c->id }}" class="bg-navy text-customWhite">{{ $c->title }}</option> @endforeach
                        </template>
                        <template x-if="type === 'bundle'">
                            @foreach($allBundles as $b) <option value="{{ $b->id }}" class="bg-navy text-customWhite">{{ $b->title }}</option> @endforeach
                        </template>
                    </select>
                </div>

                <button @click="generated = '{{ url('/') }}/' + type + '/' + selectedId + '?ref={{ $user->referral_code }}'"
                    class="brand-gradient w-full py-5 rounded-2xl font-black uppercase text-[11px] tracking-[3px] shadow-xl shadow-primary/30 hover:scale-105 active:scale-95 transition-all text-customWhite">
                    Generate Link
                </button>

                <div x-show="generated" x-transition x-cloak class="mt-4 p-4 rounded-2xl bg-customWhite/5 border border-primary/20 space-y-4">
                    <p class="text-[9px] font-black text-primary uppercase tracking-[2px]">Your Unique Link:</p>
                    <p class="text-xs font-medium text-customWhite/60 break-all bg-black/20 p-3 rounded-lg border border-customWhite/5 select-all" x-text="generated"></p>
                    <button @click="navigator.clipboard.writeText(generated); alert('Copied!')" class="w-full bg-customWhite text-navy py-3 rounded-xl font-black text-[10px] uppercase tracking-widest hover:bg-primary hover:text-customWhite transition-all">
                        Copy to Clipboard
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- 5. COURSE PROGRESS --}}
    <div class="pt-8">
        <div class="flex items-center justify-between mb-8">
            <h3 class="text-2xl font-black text-mainText uppercase tracking-tighter">Resume Mastery</h3>
            <a href="{{ url('/courses') }}" class="text-[11px] font-black uppercase text-primary border-b-2 border-primary/20 pb-1 tracking-widest hover:text-secondary hover:border-secondary transition-all">All Programs <i class="fas fa-arrow-right ml-1"></i></a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            @forelse($myCourses as $course)
                <div class="bg-surface rounded-[2.5rem] border border-primary/5 overflow-hidden shadow-sm group hover:shadow-2xl transition-all duration-500 flex flex-col h-full">
                    <div class="relative h-44 overflow-hidden">
                        <img src="{{ $course->thumbnail_url }}" class="w-full h-full object-cover group-hover:scale-110 transition-transform duration-1000">
                        <div class="absolute inset-0 bg-gradient-to-t from-navy/60 to-transparent"></div>
                        <span class="absolute top-4 left-4 px-3 py-1 rounded bg-customWhite/90 backdrop-blur-md text-[9px] font-black uppercase text-primary shadow-sm border border-primary/10">
                            {{ $course->category->name ?? 'Course' }}
                        </span>
                    </div>

                    <div class="p-6 flex flex-col flex-1">
                        <h4 class="text-lg font-black text-mainText leading-tight mb-6 line-clamp-1 uppercase tracking-tight">{{ $course->title }}</h4>

                        <div class="mt-auto space-y-4">
                            <div class="flex justify-between text-[10px] font-black uppercase tracking-widest">
                                <span class="text-mutedText">Progress</span>
                                <span class="text-primary">{{ $course->progress_percent }}%</span>
                            </div>
                            <div class="w-full h-1.5 bg-navy/5 rounded-full overflow-hidden">
                                <div class="bg-primary h-full rounded-full transition-all duration-1000"
                                     style="width: {{ $course->progress_percent }}%"></div>
                            </div>
                            <div class="flex justify-between items-center pt-2">
                                <p class="text-[9px] font-bold text-mutedText uppercase">{{ $course->completed_lessons }}/{{ $course->total_lessons }} Lessons</p>
                                <a href="{{ url('/course/learn/'.$course->id) }}" class="brand-gradient text-customWhite px-5 py-2 rounded-lg font-black text-[10px] uppercase tracking-widest shadow-md hover:translate-x-1 transition-all">Resume</a>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-span-full py-16 text-center bg-primary/5 rounded-[2.5rem] border-2 border-dashed border-primary/10 opacity-50 font-black uppercase tracking-widest">
                    No active enrollments found.
                </div>
            @endforelse
        </div>
    </div>
</div>

<script>
    function dashboardHandler() {
        return {
            init() {
                this.renderChart();
            },
            renderChart() {
                const options = {
                    series: [{ name: 'Income', data: @json($chartData) }],
                    chart: { type: 'area', height: 300, toolbar: { show: false }, fontFamily: 'Outfit, sans-serif' },
                    colors: ['#F7941D'], // Matching your primary brand color
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.3, opacityTo: 0, stops: [0, 90, 100] } },
                    stroke: { curve: 'smooth', width: 4 },
                    xaxis: {
                        categories: @json($chartLabels),
                        labels: { style: { colors: '#555555', fontSize: '10px', fontWeight: 700 } },
                        axisBorder: { show: false }, axisTicks: { show: false }
                    },
                    yaxis: { labels: { style: { colors: '#555555', fontWeight: 700 }, formatter: (val) => "₹" + val.toFixed(0) } },
                    grid: { borderColor: 'rgba(247, 148, 29, 0.05)', strokeDashArray: 5 },
                    tooltip: { theme: 'light', y: { formatter: (val) => "₹" + val } }
                };
                new ApexCharts(document.querySelector("#earningsChart"), options).render();
            }
        }
    }

    function copyToClipboard(text) {
        navigator.clipboard.writeText(text);
        alert('Copied to clipboard!');
    }
</script>
@endsection
