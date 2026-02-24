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
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>


<style>
    /* Premium Animations */
    .stagger-1 { animation: fadeUp 0.6s ease-out 0.1s both; }
    .stagger-2 { animation: fadeUp 0.6s ease-out 0.2s both; }
    .stagger-3 { animation: fadeUp 0.6s ease-out 0.3s both; }
    .stagger-4 { animation: fadeUp 0.6s ease-out 0.4s both; }

    @keyframes fadeUp {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }

    /* Soft shadow for premium feel */
    .premium-shadow {
        box-shadow: 0 10px 40px -10px rgba(var(--color-primary) / 0.15);
    }

    .hover-lift {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-lift:hover {
        transform: translateY(-4px);
        box-shadow: 0 15px 40px -10px rgba(var(--color-primary) / 0.25);
    }
</style>

<div class="space-y-4 md:space-y-8 pb-12 font-sans text-mainText" x-data="dashboardHandler()">

    {{-- 1. HEADER: PREMIUM WELCOME & REFERRAL --}}
    <div class="stagger-1 rounded-[1.5rem] md:rounded-[2.5rem] bg-surface p-5 md:p-10 border border-primary/10 relative overflow-hidden premium-shadow">
        {{-- Aesthetic Background Accents --}}
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary/5 blur-[80px] rounded-full pointer-events-none"></div>
        <div class="absolute -bottom-24 -left-24 w-72 h-72 bg-secondary/5 blur-[60px] rounded-full pointer-events-none"></div>

        <div class="relative z-10 flex flex-col md:flex-row justify-between items-center gap-4 md:gap-8 text-center md:text-left">
            {{-- User Welcome --}}
            <div class="space-y-1.5 md:space-y-3">
                <div class="flex items-center justify-center md:justify-start gap-3">
                    <span class="bg-primary/10 text-primary px-3 py-1 md:px-4 md:py-1.5 rounded-full text-[10px] md:text-xs font-bold uppercase tracking-widest border border-primary/20">
                        Partner Dashboard
                    </span>
                </div>
                <h1 class="text-2xl md:text-5xl font-black tracking-tight text-mainText leading-tight">
                    Welcome, <br class="md:hidden"> <span class="bg-clip-text text-white brand-gradient">{{ $user->name }}</span>
                </h1>
            </div>
        </div>
    </div>

    {{-- 2. EARNINGS OVERVIEW --}}
    <div class="stagger-2 grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
        @php
            $earningCards = [
                ['title' => "Today's Revenue", 'amount' => $earningsStats['today'], 'icon' => 'fa-calendar-day'],
                ['title' => "7 Day Performance", 'amount' => $earningsStats['last_7_days'], 'icon' => 'fa-chart-line'],
                ['title' => "30 Day Overview", 'amount' => $earningsStats['last_30_days'], 'icon' => 'fa-calendar-alt'],
                ['title' => "Lifetime Wealth", 'amount' => $earningsStats['all_time'], 'icon' => 'fa-trophy']
            ];
        @endphp

        @foreach($earningCards as $card)
        <div class="bg-surface rounded-2xl md:rounded-[2rem] p-5 md:p-6 border border-primary/10 premium-shadow hover-lift relative overflow-hidden group">
            <div class="absolute top-0 right-0 w-24 h-24 bg-primary/5 rounded-bl-full pointer-events-none transition-transform group-hover:scale-110"></div>

            <div class="relative z-10 flex items-center justify-between">
                <div class="flex-1">
                    <p class="text-[10px] md:text-xs font-bold text-mutedText uppercase tracking-widest mb-1">{{ $card['title'] }}</p>
                    <h3 class="text-2xl md:text-3xl font-black text-mainText tracking-tight">₹{{ number_format($card['amount']) }}</h3>
                </div>
                <div class="w-10 h-10 md:w-12 md:h-12 rounded-xl md:rounded-2xl bg-primary/10 flex items-center justify-center text-primary text-base md:text-xl group-hover:rotate-12 transition-transform duration-300">
                    <i class="fas {{ $card['icon'] }}"></i>
                </div>
            </div>
        </div>
        @endforeach
    </div>


    {{-- 3. DUAL ANALYTICS SECTION --}}
    <div class="stagger-3 grid grid-cols-1 lg:grid-cols-4 gap-6">
        {{-- Graph: Earnings Trend --}}
        <div class="lg:col-span-2 bg-surface rounded-[1.5rem] md:rounded-[2.5rem] p-6 border border-primary/10 premium-shadow relative overflow-hidden">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-8 relative z-10">
                <div>
                    <h3 class="text-sm font-black text-mainText uppercase tracking-widest flex items-center gap-3">
                        <i class="fas fa-chart-area text-primary"></i> Revenue Trajectory
                    </h3>
                </div>
            </div>
            <div id="earningsChart" class="w-full h-[300px] relative z-10"></div>
        </div>

        {{-- Reward Gauge (Integrated) --}}
        <div class="bg-surface rounded-[2.5rem] p-6 border border-primary/10 premium-shadow relative overflow-hidden flex flex-col items-center justify-center text-center">
            <div class="absolute -top-12 -left-12 w-48 h-48 bg-primary/5 blur-[50px] rounded-full pointer-events-none"></div>

            <div class="relative z-10 w-full">
                <h3 class="text-[10px] font-black text-mutedText uppercase tracking-[3px] mb-6">
                    Rank Status
                </h3>

                {{-- Semi-Circle Gauge --}}
                <div class="relative w-full h-32 mx-auto flex items-center justify-center">
                    <svg viewBox="0 0 100 60" class="w-full h-full transform transition-all duration-1000">
                        <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none" stroke="rgba(var(--color-primary), 0.05)" stroke-width="10" stroke-linecap="round" />
                        <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none" stroke="url(#dash-speed-gradient)" stroke-width="10" stroke-linecap="round"
                                stroke-dasharray="125.66" stroke-dashoffset="{{ 125.66 * (1 - ($achievementData['percentage'] / 100)) }}" class="transition-all duration-[2000ms] ease-out" />

                        <defs>
                            <linearGradient id="dash-speed-gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" style="stop-color:rgb(var(--color-primary))" />
                                <stop offset="100%" style="stop-color:rgb(var(--color-secondary))" />
                            </linearGradient>
                        </defs>

                        {{-- Needle --}}
                        <g transform="rotate({{ -90 + (180 * ($achievementData['percentage'] / 100)) }} 50 50)" class="transition-all duration-[2000ms] ease-out origin-center">
                            <line x1="50" y1="50" x2="20" y2="50" stroke="white" stroke-width="2" stroke-linecap="round" />
                            <circle cx="50" cy="50" r="3" fill="white" />
                        </g>
                    </svg>

                    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 text-center">
                        <span class="text-xl font-black text-mainText tracking-tighter">{{ round($achievementData['percentage']) }}%</span>
                    </div>
                </div>

                <div class="mt-4">
                    <p class="text-[9px] font-black text-mutedText uppercase tracking-widest">Level</p>
                    <h4 class="text-xs font-black text-mainText uppercase mt-1">
                        {{ $achievementData['current_milestone'] ? $achievementData['current_milestone']->short_title : 'Novice' }}
                    </h4>

                    <a href="{{ route('student.rewards') }}" class="mt-4 inline-flex items-center gap-2 text-[9px] font-black text-primary uppercase tracking-widest hover:text-white transition-colors">
                        Rewards Hub <i class="fas fa-arrow-right"></i>
                    </a>
                </div>
            </div>
        </div>

        {{-- Affiliate Link Generator (Enhanced) --}}
        <div class="bg-surface rounded-3xl p-6 border border-primary/10 premium-shadow flex flex-col relative overflow-hidden" x-data="{ type: 'general', expiryOption: 'no_expiry' }">
             {{-- Background Accent --}}
             <div class="absolute -right-10 -top-10 w-32 h-32 bg-secondary/10 blur-[40px] rounded-full pointer-events-none"></div>

             <div class="mb-6 relative z-10">
                <h3 class="text-xl font-black text-mainText uppercase tracking-widest flex items-center gap-3">
                    <i class="fas fa-bolt text-secondary"></i> Link Generator
                </h3>
                <p class="text-sm text-mutedText font-medium mt-1">Create & share your affiliate links</p>
            </div>

            <form action="{{ route('student.affiliate.link.generate') }}" method="POST" class="flex flex-col gap-4 relative z-10">
                @csrf

                {{-- Type Selection --}}
                <div>
                    <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-1.5">Type</label>
                    <div class="relative">
                        <select x-model="type" name="type" class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-xs font-bold text-mainText focus:border-primary outline-none appearance-none cursor-pointer">
                            <option value="general">All Bundles (General)</option>
                            <option value="specific_bundle">Specific Bundle</option>
                            @if($canSellCourses)
                                <option value="specific_course">Specific Course</option>
                            @endif
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-mutedText text-xs pointer-events-none"></i>
                    </div>
                </div>

                {{-- Bundle Selection --}}
                <div x-show="type === 'specific_bundle'" x-transition style="display: none;">
                    <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-1.5">Select Bundle</label>
                    <div class="relative">
                        <select name="target_id_bundle" :required="type === 'specific_bundle'" class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-xs font-bold text-mainText focus:border-primary outline-none appearance-none cursor-pointer">
                            <option value="">-- Choose Bundle --</option>
                            @foreach($availableBundles as $bundle)
                                <option value="{{ $bundle->id }}">{{ $bundle->title }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-mutedText text-xs pointer-events-none"></i>
                    </div>
                </div>

                {{-- Course Selection --}}
                @if($canSellCourses)
                <div x-show="type === 'specific_course'" x-transition style="display: none;">
                    <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-1.5">Select Course</label>
                    <div class="relative">
                        <select name="target_id_course" :required="type === 'specific_course'" class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-xs font-bold text-mainText focus:border-primary outline-none appearance-none cursor-pointer">
                            <option value="">-- Choose Course --</option>
                            @foreach($availableCourses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-mutedText text-xs pointer-events-none"></i>
                    </div>
                </div>
                @endif

                {{-- Expiry Option --}}
                <div>
                    <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-1.5">Expiry</label>
                    <div class="relative">
                        <select x-model="expiryOption" class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-xs font-bold text-mainText focus:border-primary outline-none appearance-none cursor-pointer">
                            <option value="no_expiry">Lifetime</option>
                            <option value="custom">Set Date</option>
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-mutedText text-xs pointer-events-none"></i>
                    </div>
                </div>

                {{-- Custom Date Input --}}
                <div x-show="expiryOption === 'custom'" x-transition style="display: none;">
                     <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-1.5">Select Date</label>
                    <input type="date" name="expiry_date" min="{{ date('Y-m-d') }}" :required="expiryOption === 'custom'" class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-xs font-bold text-mainText focus:border-primary outline-none placeholder-mutedText uppercase">
                </div>

                {{-- Generate Button --}}
                <button type="submit" class="w-full bg-secondary text-navy hover:bg-white hover:text-navy py-3 rounded-xl font-black uppercase text-[11px] tracking-[3px] shadow-lg transition-all active:scale-95 flex items-center justify-center gap-2 mt-2">
                    <i class="fas fa-magic"></i> Generate Link
                </button>
            </form>
        </div>
    </div>

    {{-- My Links Table (New Section) --}}
    <div class="stagger-3 mt-8 bg-surface rounded-[2.5rem] border border-primary/10 premium-shadow overflow-hidden">
        <div class="p-8 border-b border-primary/10">
            <h3 class="text-xl font-black text-mainText uppercase tracking-widest flex items-center gap-3">
                <i class="fas fa-link text-primary"></i> My Active Links
            </h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-navy/50 text-xs uppercase text-mutedText font-bold tracking-widest">
                    <tr>
                        <th class="px-8 py-5">Created</th>
                        <th class="px-8 py-5">Target</th>
                        <th class="px-8 py-5">Link / URL</th>
                        <th class="px-8 py-5">Clicks</th>
                        <th class="px-8 py-5">Expires</th>
                        <th class="px-8 py-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary/5">
                    @forelse ($links as $link)
                        <tr class="hover:bg-primary/5 transition-colors">
                            <td class="px-8 py-5 text-sm font-bold text-mainText">{{ $link->created_at->format('d M Y') }}</td>
                            <td class="px-8 py-5">
                                <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black bg-navy text-primary uppercase tracking-widest border border-primary/20">
                                    {{ str_replace(['specific_', '_'], ['', ' '], $link->target_type) }}
                                </span>
                                @if($link->target_type == 'specific_bundle' && $link->bundle)
                                    <span class="block text-xs font-semibold text-mutedText mt-1">{{ $link->bundle->title }}</span>
                                @elseif($link->target_type == 'specific_course' && $link->course)
                                    <span class="block text-xs font-semibold text-mutedText mt-1">{{ $link->course->title }}</span>
                                @elseif($link->target_type == 'general')
                                    <span class="block text-xs font-semibold text-mutedText mt-1">General Link</span>
                                @endif
                            </td>
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-3">
                                    <input type="text" readonly value="{{ url('/u/' . $link->slug) }}" class="text-xs font-mono text-mutedText bg-navy border border-primary/10 rounded-lg p-2 w-48 focus:ring-0">
                                    <button onclick="copyToClipboard('{{ url('/u/' . $link->slug) }}')" class="text-primary hover:text-white transition-colors text-xs font-black uppercase tracking-widest">Copy</button>
                                </div>
                            </td>
                            <td class="px-8 py-5 text-sm font-black text-mainText">{{ $link->clicks }}</td>
                            <td class="px-8 py-5 text-sm font-semibold text-mutedText">
                                {{ $link->expires_at ? $link->expires_at->format('d M Y, h:i A') : 'Never' }}
                            </td>
                            <td class="px-8 py-5 text-right">
                                <form action="{{ route('student.affiliate.link.delete', $link->id) }}" method="POST" onsubmit="return confirm('Disable this link?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-400 p-2 transition-colors">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-8 py-12 text-center text-mutedText font-semibold">No active links found. Generate one to get started!</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($links->hasPages())
            <div class="p-6 border-t border-primary/10">
                {{ $links->links() }}
            </div>
        @endif
    </div>

    {{-- 4. SECONDARY STATS ROW --}}
    <div class="stagger-4 grid grid-cols-2 md:grid-cols-4 gap-4 md:gap-6">
         @php
            $secStats = [
                ['label' => 'Programs Enrolled', 'val' => $myCourses->count() + $myBundles->count(), 'icon' => 'fa-graduation-cap'],
                ['label' => 'Lessons Mastered', 'val' => $myCourses->sum('completed_lessons'), 'icon' => 'fa-check-double'],
                ['label' => 'Pending Payout', 'val' => '₹'.number_format($secondaryStats['pending_earnings']), 'icon' => 'fa-hourglass-half'],
                ['label' => 'Total Withdrawn', 'val' => '₹'.number_format($secondaryStats['total_payouts']), 'icon' => 'fa-wallet']
            ];
        @endphp
        @foreach($secStats as $stat)
        <div class="bg-surface p-6 rounded-2xl border border-primary/10 flex flex-col items-center justify-center text-center gap-2 hover-lift premium-shadow">
            <i class="fas {{ $stat['icon'] }} text-primary text-2xl mb-2 opacity-90"></i>
            <h4 class="text-2xl font-black text-mainText">{{ $stat['val'] }}</h4>
            <p class="text-[10px] font-bold uppercase tracking-widest text-mutedText">{{ $stat['label'] }}</p>
        </div>
        @endforeach
    </div>

    {{-- 5. RESUME LEARNING SECTION --}}
    @if($myCourses->isNotEmpty())
    <div class="stagger-4 pt-4">
        <div class="flex items-center justify-between mb-8 border-b border-primary/10 pb-4">
            <h3 class="text-2xl font-black text-mainText uppercase tracking-widest flex items-center gap-3">
                <i class="fas fa-play-circle text-primary"></i> Resume Mastery
            </h3>
            <a href="{{ url('/courses') }}" class="text-xs font-bold uppercase text-primary hover:text-secondary transition-colors tracking-widest flex items-center gap-2">
                View All <i class="fas fa-arrow-right"></i>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($myCourses->take(4) as $course)
            <a href="{{ url('/course/learn/'.$course->id) }}" class="group block hover-lift">
                <div class="bg-surface rounded-[2rem] overflow-hidden border border-primary/10 premium-shadow h-full flex flex-col">
                    <div class="relative h-44 overflow-hidden">
                        <img src="{{ $course->thumbnail_url }}" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-700">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 to-transparent opacity-60"></div>
                        <div class="absolute top-4 left-4 bg-surface/90 backdrop-blur-sm px-4 py-1.5 rounded-full shadow-sm">
                            <p class="text-[10px] font-bold uppercase text-primary tracking-widest">{{ $course->category->name ?? 'Course' }}</p>
                        </div>
                    </div>
                    <div class="p-6 flex flex-col flex-1">
                        <h4 class="text-lg font-black text-mainText leading-snug mb-4 line-clamp-2 group-hover:text-primary transition-colors">{{ $course->title }}</h4>

                        <div class="mt-auto space-y-3">
                            <div class="flex justify-between text-xs font-bold uppercase tracking-widest text-mutedText">
                                <span>{{ $course->completed_lessons }}/{{ $course->total_lessons }} Lessons</span>
                                <span class="text-primary">{{ $course->progress_percent }}%</span>
                            </div>
                            <div class="w-full h-2 bg-navy rounded-full overflow-hidden shadow-inner">
                                <div class="h-full rounded-full brand-gradient transition-all duration-1000" style="width: {{ $course->progress_percent }}%"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </a>
            @endforeach
        </div>
    </div>
    @endif

</div>

<script>
    const bundles = @json($availableBundles);
    const courses = @json($availableCourses);

    function toggleTargetSelect() {
        const type = document.getElementById('target_type').value;
        const container = document.getElementById('target_id_container');
        const select = document.getElementById('target_id');

        // Clear existing options
        select.innerHTML = '';
        container.style.display = 'none';

        if (type === 'bundle') {
            container.style.display = 'block';
            bundles.forEach(b => {
                const opt = document.createElement('option');
                opt.value = b.id;
                opt.text = b.title;
                select.appendChild(opt);
            });
        } else if (type === 'course') {
            container.style.display = 'block';
            courses.forEach(c => {
                const opt = document.createElement('option');
                opt.value = c.id;
                opt.text = c.title;
                select.appendChild(opt);
            });
        }
    }
    // Initialize on load
    document.addEventListener('DOMContentLoaded', toggleTargetSelect);
</script>

<script>
    function dashboardHandler() {
        return {
            init() {
                this.renderEarningsChart();
                // Category Chart removed/replaced by Link Generator
            },
            renderEarningsChart() {
                const isMobile = window.innerWidth < 768;
                const options = {
                    series: [{ name: 'Earnings', data: @json($graphData['data']) }],
                    chart: {
                        type: 'area',
                        height: isMobile ? 250 : 350,
                        toolbar: { show: false },
                        fontFamily: 'var(--font-main)',
                        background: 'transparent'
                    },
                    colors: ['rgb(var(--color-primary))'],
                    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.4, opacityTo: 0.0, stops: [0, 100] } },
                    dataLabels: { enabled: false },
                    stroke: { curve: 'smooth', width: isMobile ? 2 : 4 },
                    xaxis: {
                        categories: @json($graphData['labels']),
                        labels: {
                            show: !isMobile,
                            style: { colors: 'rgb(var(--color-text-muted))', fontSize: '11px', fontWeight: 600 }
                        },
                        axisBorder: { show: false },
                        axisTicks: { show: false }
                    },
                    yaxis: {
                        show: !isMobile,
                        labels: { style: { colors: 'rgb(var(--color-text-muted))', fontWeight: 600 }, formatter: (val) => "₹" + val.toFixed(0) }
                    },
                    grid: {
                        borderColor: 'rgba(var(--color-text-muted), 0.1)',
                        strokeDashArray: 4,
                        padding: {
                            left: isMobile ? -10 : 0,
                            right: isMobile ? -10 : 0
                        }
                    },
                    tooltip: { theme: 'light' }
                };
                new ApexCharts(document.querySelector("#earningsChart"), options).render();
            },

        }
    }


    function copyToClipboard(text) {
        navigator.clipboard.writeText(text).then(() => {
            const el = document.createElement('div');
            el.innerHTML = `
                <div class="fixed top-6 right-6 bg-surface text-mainText px-6 py-4 rounded-2xl shadow-2xl border border-primary/20 flex items-center gap-4 z-50 animate-fade-in-down">
                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center text-primary text-lg">
                        <i class="fas fa-check"></i>
                    </div>
                    <div>
                        <h4 class="font-black text-sm uppercase tracking-widest">Copied!</h4>
                        <p class="text-xs font-semibold text-mutedText">Link copied to clipboard</p>
                    </div>
                </div>`;
            document.body.appendChild(el);
            setTimeout(() => {
                el.style.opacity = '0';
                el.style.transition = 'opacity 0.3s ease';
                setTimeout(() => el.remove(), 300);
            }, 2500);
        });
    }
</script>
@endsection
