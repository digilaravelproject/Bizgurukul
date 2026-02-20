@extends('layouts.admin')

@section('content')
<div x-data="dashboard()" x-init="init()" class="space-y-8 font-sans text-mainText">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-mainText">Dashboard Overview</h1>
            <p class="text-mutedText mt-1 text-sm">Welcome back, Super Admin. Here's what's happening today.</p>
        </div>
        <div class="flex items-center gap-3 bg-white p-1.5 rounded-xl border border-primary/10 shadow-sm">
            <span class="text-xs font-medium text-mutedText px-3" x-text="'Last updated: ' + lastUpdated"></span>
            <button @click="fetchStats()" class="p-2 text-primary hover:bg-primary/10 rounded-lg transition-colors">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" :class="{'animate-spin': loading}">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                </svg>
            </button>
        </div>
    </div>

    {{-- Key Metrics Grid --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">

        {{-- Revenue Card --}}
        <div class="bg-surface rounded-2xl p-6 shadow-sm border border-primary/5 relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-primary/5 rounded-full group-hover:bg-primary/10 transition-colors"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-primary/10 rounded-lg text-primary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <div class="flex items-center gap-1 text-xs font-bold px-2 py-1 rounded-full"
                         :class="stats.revenue_growth >= 0 ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'">
                        <span x-text="stats.revenue_growth >= 0 ? '+' : ''"></span>
                        <span x-text="stats.revenue_growth"></span>%
                    </div>
                </div>
                <p class="text-sm font-medium text-mutedText">Total Revenue</p>
                <h3 class="text-2xl font-bold text-mainText mt-1" x-text="formatCurrency(stats.total_revenue)"></h3>
            </div>
        </div>

        {{-- Active Students --}}
        <div class="bg-surface rounded-2xl p-6 shadow-sm border border-primary/5 relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-secondary/5 rounded-full group-hover:bg-secondary/10 transition-colors"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-secondary/10 rounded-lg text-secondary">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                    </div>
                    <span class="text-xs font-bold px-2 py-1 rounded-full bg-primary/10 text-primary">
                        +<span x-text="stats.new_users_today"></span> New
                    </span>
                </div>
                <p class="text-sm font-medium text-mutedText">Total Students</p>
                <h3 class="text-2xl font-bold text-mainText mt-1" x-text="stats.total_users"></h3>
            </div>
        </div>

        {{-- Courses --}}
        <div class="bg-surface rounded-2xl p-6 shadow-sm border border-primary/5 relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-blue-50 rounded-full group-hover:bg-blue-100 transition-colors"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-blue-50 rounded-lg text-blue-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                    </div>
                    <span class="text-xs font-bold px-2 py-1 rounded-full bg-blue-50 text-blue-600">
                        <span x-text="stats.active_courses"></span> Active
                    </span>
                </div>
                <p class="text-sm font-medium text-mutedText">Total Courses</p>
                <h3 class="text-2xl font-bold text-mainText mt-1" x-text="stats.total_courses"></h3>
            </div>
        </div>

        {{-- Commissions --}}
        <div class="bg-surface rounded-2xl p-6 shadow-sm border border-primary/5 relative overflow-hidden group hover:shadow-md transition-all duration-300">
            <div class="absolute -right-6 -top-6 w-24 h-24 bg-orange-50 rounded-full group-hover:bg-orange-100 transition-colors"></div>
            <div class="relative z-10">
                <div class="flex justify-between items-start mb-4">
                    <div class="p-2 bg-orange-50 rounded-lg text-orange-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                    </div>
                    <span class="text-xs font-bold px-2 py-1 rounded-full bg-orange-100 text-orange-700">
                        Pending
                    </span>
                </div>
                <p class="text-sm font-medium text-mutedText">Pending Payouts</p>
                <h3 class="text-2xl font-bold text-mainText mt-1" x-text="formatCurrency(stats.pending_commission)"></h3>
            </div>
        </div>
    </div>

    {{-- Main Content Split --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left Column (Chart & Top Courses) --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- Chart Section --}}
            <div class="bg-surface p-6 rounded-2xl shadow-sm border border-primary/10">
                <div class="flex flex-col sm:flex-row justify-between items-center mb-6 gap-4">
                    <div>
                        <h3 class="text-lg font-bold text-mainText">Revenue Analytics</h3>
                        <p class="text-sm text-mutedText">Financial performance over time.</p>
                    </div>
                    <div class="flex bg-navy p-1 rounded-xl">
                        <button @click="period = 'week'; fetchStats()" :class="period === 'week' ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-mutedText hover:text-mainText'" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all">Week</button>
                        <button @click="period = 'month'; fetchStats()" :class="period === 'month' ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-mutedText hover:text-mainText'" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all">Month</button>
                        <button @click="period = '6months'; fetchStats()" :class="period === '6months' ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-mutedText hover:text-mainText'" class="px-4 py-1.5 rounded-lg text-xs font-bold transition-all">6 Months</button>
                    </div>
                </div>
                <div class="relative h-80 w-full">
                    <canvas id="salesChart"></canvas>
                </div>
            </div>

            {{-- Top Courses --}}
            <div class="bg-surface p-6 rounded-2xl shadow-sm border border-primary/10">
                <h3 class="text-lg font-bold text-mainText mb-4">Top Performing Courses</h3>
                <div class="space-y-4">
                    <template x-for="course in stats.top_courses" :key="course.id">
                        <div class="flex items-center gap-4 p-3 rounded-xl hover:bg-navy transition-colors border border-transparent hover:border-primary/5">
                            <div class="w-16 h-12 bg-navy rounded-lg overflow-hidden flex-shrink-0">
                                <template x-if="course.thumbnail">
                                    <img :src="course.thumbnail_url" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!course.thumbnail">
                                    <div class="w-full h-full flex items-center justify-center bg-primary/10 text-primary">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    </div>
                                </template>
                            </div>
                            <div class="flex-1">
                                <h4 class="font-bold text-mainText text-sm line-clamp-1" x-text="course.title"></h4>
                                <p class="text-xs text-mutedText">Price: <span class="font-semibold text-primary" x-text="formatCurrency(course.website_price)"></span></p>
                            </div>
                            <div class="text-right">
                                <span class="block text-lg font-black text-mainText" x-text="course.users_count"></span>
                                <span class="text-[10px] uppercase font-bold text-mutedText tracking-wider">Students</span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>
        </div>

        {{-- Right Column (Transactions & Registrations) --}}
        <div class="space-y-8">

            {{-- Recent Registrations --}}
            <div class="bg-surface p-6 rounded-2xl shadow-sm border border-primary/10">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-mainText">New Students</h3>
                    <a href="#" class="text-xs font-bold text-primary hover:text-secondary">View All</a>
                </div>
                <div class="space-y-4">
                    <template x-for="user in stats.recent_registrations" :key="user.id">
                        <div class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-full bg-navy flex items-center justify-center text-sm font-bold text-primary border border-primary/10 overflow-hidden">
                                <template x-if="user.profile_picture">
                                    <img :src="'/storage/' + user.profile_picture" class="w-full h-full object-cover">
                                </template>
                                <template x-if="!user.profile_picture">
                                    <span x-text="user.name.charAt(0)"></span>
                                </template>
                            </div>
                            <div>
                                <p class="text-sm font-bold text-mainText" x-text="user.name"></p>
                                <p class="text-xs text-mutedText" x-text="new Date(user.created_at).toLocaleDateString()"></p>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- Recent Transactions --}}
            <div class="bg-surface p-6 rounded-2xl shadow-sm border border-primary/10">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="text-lg font-bold text-mainText">Recent Transactions</h3>
                    <a href="#" class="text-xs font-bold text-primary hover:text-secondary">View All</a>
                </div>
                <div class="space-y-4">
                    <template x-for="txn in stats.recent_transactions" :key="txn.id">
                        <div class="flex items-center justify-between p-3 rounded-xl bg-navy/50 border border-primary/5">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-surface flex items-center justify-center text-xs font-bold text-mainText shadow-sm">
                                    <span x-text="txn.user ? txn.user.name.charAt(0) : '?'"></span>
                                </div>
                                <div>
                                    <p class="text-xs font-bold text-mainText" x-text="txn.user ? txn.user.name : 'Unknown'"></p>
                                    <p class="text-[10px] uppercase font-bold tracking-wider"
                                       :class="txn.status === 'success' ? 'text-green-600' : 'text-orange-600'"
                                       x-text="txn.status"></p>
                                </div>
                            </div>
                            <span class="text-sm font-bold text-mainText" x-text="formatCurrency(txn.amount)"></span>
                        </div>
                    </template>
                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('dashboard', () => ({
            loading: false,
            period: 'month',
            stats: @json($stats),
            lastUpdated: new Date().toLocaleTimeString(),

            init() {
                this.$nextTick(() => { this.renderChart(this.stats.chart || {}); });
                setInterval(() => { this.fetchStats(); }, 300000);
            },

            async fetchStats() {
                this.loading = true;
                try {
                    const response = await fetch(`{{ route('admin.dashboard.stats') }}?period=${this.period}`);
                    if (!response.ok) throw new Error('Network error');
                    const data = await response.json();
                    this.stats = data.aggregate;
                    this.lastUpdated = new Date().toLocaleTimeString();
                    this.renderChart(data.chart);
                } catch (error) {
                    console.error('Error:', error);
                } finally {
                    this.loading = false;
                }
            },

            formatCurrency(value) {
                return new Intl.NumberFormat('en-IN', {
                    style: 'currency', currency: 'INR', maximumFractionDigits: 0
                }).format(value);
            },

            renderChart(chartData) {
                const canvas = document.getElementById('salesChart');
                if (!canvas) return;
                const ctx = canvas.getContext('2d');
                if (window.salesChartInstance) window.salesChartInstance.destroy();

                const gradient = ctx.createLinearGradient(0, 0, 0, 300);
                gradient.addColorStop(0, 'rgba(247, 148, 29, 0.2)'); // Brand Orange Low Opacity
                gradient.addColorStop(1, 'rgba(247, 148, 29, 0.0)');

                window.salesChartInstance = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: chartData.labels,
                        datasets: [{
                            label: 'Revenue',
                            data: chartData.data,
                            borderColor: '#F7941D', // Brand Primary
                            backgroundColor: gradient,
                            borderWidth: 2,
                            pointBackgroundColor: '#FFFFFF',
                            pointBorderColor: '#F7941D',
                            pointBorderWidth: 2,
                            pointRadius: 4,
                            pointHoverRadius: 6,
                            fill: true,
                            tension: 0.4
                        }]
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: { display: false },
                            tooltip: {
                                backgroundColor: '#FFFFFF',
                                titleColor: '#2D2D2D',
                                bodyColor: '#555555',
                                borderColor: 'rgba(0,0,0,0.05)',
                                borderWidth: 1,
                                padding: 10,
                                displayColors: false,
                                titleFont: { family: "'Outfit', sans-serif", size: 13 },
                                bodyFont: { family: "'Outfit', sans-serif", size: 12 },
                                callbacks: {
                                    label: (c) => new Intl.NumberFormat('en-IN', { style: 'currency', currency: 'INR' }).format(c.parsed.y)
                                }
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: 'rgba(0,0,0,0.03)', drawBorder: false },
                                ticks: {
                                    color: '#888888',
                                    font: { family: "'Outfit', sans-serif", size: 11 },
                                    callback: (v) => '₹' + v.toLocaleString('en-IN')
                                }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: '#888888', font: { family: "'Outfit', sans-serif", size: 11 } }
                            }
                        }
                    }
                });
            }
        }));
    });
</script>
@endpush
@endsection
