@extends('layouts.user.app')

@section('title', 'Leaderboard | ' . config('app.name', 'Skills Pehle'))

@push('styles')
<style>
    /* Premium Deep Mesh Background */
    .dashboard-bg {
        background-color: rgb(var(--color-bg-body));
        background-image:
            radial-gradient(circle at 15% 50%, rgba(var(--color-primary) / 0.08), transparent 25%),
            radial-gradient(circle at 85% 30%, rgba(var(--color-secondary) / 0.08), transparent 25%);
        background-attachment: fixed;
    }

    /* Refined Premium Glass Effect */
    .premium-glass {
        background: rgba(var(--color-bg-card) / 0.6);
        backdrop-filter: blur(24px);
        -webkit-backdrop-filter: blur(24px);
        border: 1px solid rgba(var(--color-primary) / 0.1);
        box-shadow: 0 10px 40px -10px rgba(0,0,0,0.08);
        border-radius: 1.5rem;
        transition: all 0.3s ease;
    }

    .premium-glass:hover {
        border-color: rgba(var(--color-primary) / 0.25);
        box-shadow: 0 15px 50px -10px rgba(var(--color-primary) / 0.15);
    }

    /* VIP User Card Styling */
    .vip-card {
        background: linear-gradient(135deg, rgba(var(--color-primary)/0.95), rgba(var(--color-secondary)/0.95));
        position: relative;
        overflow: hidden;
        color: white;
        border-radius: 1.5rem;
        box-shadow: 0 20px 40px -10px rgba(var(--color-primary)/0.4);
    }

    .vip-card::after {
        content: '';
        position: absolute;
        top: -50%; right: -50%; bottom: -50%; left: -50%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 60%);
        transform: rotate(30deg);
        pointer-events: none;
    }

    /* Podium Styles */
    .podium-avatar {
        position: relative;
        border-radius: 50%;
        padding: 4px;
        background: rgba(var(--color-bg-card) / 1);
        box-shadow: 0 8px 25px -5px rgba(0,0,0,0.1);
    }

    /* Medals & Glows */
    .gold-glow { box-shadow: 0 0 30px 0px rgba(245, 158, 11, 0.4); }
    .silver-glow { box-shadow: 0 0 20px 0px rgba(148, 163, 184, 0.3); }
    .bronze-glow { box-shadow: 0 0 20px 0px rgba(180, 83, 9, 0.3); }

    .rank-text-gradient {
        background: linear-gradient(to right, #bf953f, #fcf6ba, #b38728, #fbf5b7, #aa771c);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    /* Sleek Filter Pills */
    .sleek-filter {
        border-radius: 999px;
        font-weight: 700;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        color: var(--color-mutedText);
    }
    .sleek-filter:hover:not(.active-filter) {
        color: var(--color-mainText);
        background: rgba(var(--color-primary) / 0.05);
    }
    .active-filter {
        background: rgb(var(--color-primary));
        color: white !important;
        box-shadow: 0 4px 15px rgba(var(--color-primary) / 0.3);
    }

    /* List Item Hover */
    .list-item-hover {
        transition: transform 0.2s ease, background 0.2s ease;
    }
    .list-item-hover:hover {
        transform: translateX(6px);
        background: rgba(var(--color-primary) / 0.03);
    }
</style>
@endpush

@section('content')
<div class="dashboard-bg min-h-screen pb-24 pt-10" x-data="leaderboardData()" x-cloak>
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Premium Header --}}
        <div class="flex flex-col md:flex-row items-center justify-between gap-6 mb-12 relative z-10">
            <div class="text-center md:text-left">
                <h1 class="text-4xl md:text-5xl font-black text-mainText tracking-tight mb-2">
                    Hall of <span class="text-primary">Fame</span>
                </h1>
                <p class="text-sm md:text-base text-mutedText font-medium tracking-wide">Celebrating the top achievers of Skills Pehle</p>
            </div>

            {{-- Desktop Filters --}}
            <div class="hidden md:flex bg-surface/80 backdrop-blur-md p-1.5 rounded-full border border-primary/10 shadow-sm">
                <template x-for="f in availableFilters" :key="f.value">
                    <button @click="setFilter(f.value)"
                            class="sleek-filter px-6 py-2.5 text-sm tracking-wide"
                            :class="filter === f.value ? 'active-filter' : ''">
                        <span x-text="f.label"></span>
                    </button>
                </template>
            </div>

            {{-- Mobile Filters --}}
            <div class="flex md:hidden bg-surface/80 backdrop-blur-md p-1 rounded-full border border-primary/10 w-full justify-between overflow-x-auto hide-scrollbar">
                <template x-for="f in availableFilters" :key="f.value">
                    <button @click="setFilter(f.value)"
                            class="sleek-filter flex-1 px-3 py-2 text-xs whitespace-nowrap"
                            :class="filter === f.value ? 'active-filter' : ''">
                        <span x-text="f.label"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Loader (Elegant Spinner) --}}
        <div x-show="loading" class="flex flex-col items-center justify-center py-32 space-y-4">
            <div class="relative w-16 h-16">
                <div class="absolute inset-0 rounded-full border-4 border-primary/20"></div>
                <div class="absolute inset-0 rounded-full border-4 border-primary border-t-transparent animate-spin"></div>
            </div>
            <p class="text-xs font-bold text-primary uppercase tracking-widest animate-pulse">Fetching Elite Data...</p>
        </div>

        {{-- Main Content Grid --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10" x-show="!loading" style="display: none;">

            {{-- LEADERBOARD SHOWCASE (Now Full Width) --}}
            <div class="lg:col-span-12 space-y-12">

                {{-- The Podium (Top 3) --}}
                <div class="flex flex-row items-end justify-center gap-2 sm:gap-6 pt-10" x-show="topThree.length > 0">

                    {{-- Rank 2 (Silver) --}}
                    <template x-if="topThree[1]">
                        <div class="w-1/3 flex flex-col items-center group">
                            <div class="text-slate-400 font-black text-xl sm:text-2xl mb-2 opacity-50">#2</div>
                            <div class="podium-avatar silver-glow mb-4 z-10 w-20 h-20 sm:w-24 sm:h-24 transition-transform group-hover:-translate-y-2 duration-300">
                                <div class="w-full h-full rounded-full overflow-hidden border-4 border-slate-300">
                                    <template x-if="!hasImage(topThree[1].profile_picture)"><div class="w-full h-full flex items-center justify-center bg-slate-100 font-bold text-2xl text-slate-500" x-text="getInitials(topThree[1].name)"></div></template>
                                    <template x-if="hasImage(topThree[1].profile_picture)"><img :src="topThree[1].profile_picture" class="w-full h-full object-cover"></template>
                                </div>
                            </div>
                            <div class="premium-glass w-full pt-8 pb-4 px-2 text-center -mt-12 rounded-t-3xl border-b-0 rounded-b-xl relative z-0">
                                <h3 class="text-xs sm:text-sm font-bold text-mainText truncate w-full px-1" x-text="topThree[1].name"></h3>
                                <p class="text-sm sm:text-base font-black text-slate-500 mt-1">₹<span x-text="formatMoney(topThree[1].earnings)"></span></p>
                            </div>
                        </div>
                    </template>

                    {{-- Rank 1 (Gold - King) --}}
                    <template x-if="topThree[0]">
                        <div class="w-1/3 flex flex-col items-center group relative z-20 -mt-10">
                            <i class="fas fa-crown text-amber-400 text-3xl sm:text-4xl mb-2 drop-shadow-lg animate-bounce"></i>
                            <div class="podium-avatar gold-glow mb-4 z-10 w-28 h-28 sm:w-36 sm:h-36 transition-transform group-hover:-translate-y-2 duration-300">
                                <div class="w-full h-full rounded-full overflow-hidden border-4 border-amber-400">
                                    <template x-if="!hasImage(topThree[0].profile_picture)"><div class="w-full h-full flex items-center justify-center bg-amber-50 font-bold text-3xl text-amber-600" x-text="getInitials(topThree[0].name)"></div></template>
                                    <template x-if="hasImage(topThree[0].profile_picture)"><img :src="topThree[0].profile_picture" class="w-full h-full object-cover"></template>
                                </div>
                                <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 bg-amber-500 text-white text-xs font-black px-3 py-1 rounded-full shadow-lg border-2 border-white">#1</div>
                            </div>
                            <div class="premium-glass w-full pt-10 pb-6 px-2 text-center -mt-14 rounded-t-[2.5rem] rounded-b-2xl border-amber-200/50 bg-gradient-to-b from-amber-50/10 to-transparent relative z-0">
                                <h3 class="text-sm sm:text-lg font-black text-mainText truncate w-full px-1" x-text="topThree[0].name"></h3>
                                <div class="mt-2 bg-gradient-to-r from-amber-500 to-yellow-500 text-white px-4 py-1.5 rounded-xl text-sm sm:text-base font-black inline-block shadow-md">
                                    ₹<span x-text="formatMoney(topThree[0].earnings)"></span>
                                </div>
                            </div>
                        </div>
                    </template>

                    {{-- Rank 3 (Bronze) --}}
                    <template x-if="topThree[2]">
                        <div class="w-1/3 flex flex-col items-center group">
                            <div class="text-orange-400/70 font-black text-xl sm:text-2xl mb-2 opacity-50">#3</div>
                            <div class="podium-avatar bronze-glow mb-4 z-10 w-20 h-20 sm:w-24 sm:h-24 transition-transform group-hover:-translate-y-2 duration-300">
                                <div class="w-full h-full rounded-full overflow-hidden border-4 border-orange-300">
                                    <template x-if="!hasImage(topThree[2].profile_picture)"><div class="w-full h-full flex items-center justify-center bg-orange-50 font-bold text-2xl text-orange-600" x-text="getInitials(topThree[2].name)"></div></template>
                                    <template x-if="hasImage(topThree[2].profile_picture)"><img :src="topThree[2].profile_picture" class="w-full h-full object-cover"></template>
                                </div>
                            </div>
                            <div class="premium-glass w-full pt-8 pb-4 px-2 text-center -mt-12 rounded-t-3xl border-b-0 rounded-b-xl relative z-0">
                                <h3 class="text-xs sm:text-sm font-bold text-mainText truncate w-full px-1" x-text="topThree[2].name"></h3>
                                <p class="text-sm sm:text-base font-black text-orange-600 mt-1">₹<span x-text="formatMoney(topThree[2].earnings)"></span></p>
                            </div>
                        </div>
                    </template>
                </div>

                {{-- Rest of the Top 10 (Sleek List) --}}
                <div class="premium-glass p-2 sm:p-4" x-show="restOfTopTen.length > 0">
                    <div class="space-y-1">
                        <template x-for="user in restOfTopTen" :key="user.rank">
                            <div class="list-item-hover flex items-center justify-between p-3 sm:p-4 rounded-xl cursor-default">
                                <div class="flex items-center gap-4 sm:gap-6">
                                    <div class="w-8 text-center">
                                        <span class="text-sm sm:text-base font-black text-mutedText opacity-70" x-text="'#' + user.rank"></span>
                                    </div>

                                    <div class="relative w-12 h-12 rounded-full overflow-hidden bg-surface border border-primary/10 shadow-sm">
                                        <template x-if="!hasImage(user.profile_picture)">
                                            <div class="w-full h-full flex items-center justify-center text-sm font-bold text-primary" x-text="getInitials(user.name)"></div>
                                        </template>
                                        <template x-if="hasImage(user.profile_picture)">
                                            <img :src="user.profile_picture" class="w-full h-full object-cover">
                                        </template>
                                    </div>

                                    <div class="flex flex-col">
                                        <span class="text-sm sm:text-base font-bold text-mainText" x-text="user.name"></span>
                                        <span class="text-xs font-medium text-mutedText">Top Performer</span>
                                    </div>
                                </div>

                                <div class="text-right">
                                    <span class="text-base sm:text-lg font-black text-primary">₹<span x-text="formatMoney(user.earnings)"></span></span>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>

            {{-- BOTTOM: VIP USER PROFILE (Now horizontal at the bottom) --}}
            <div class="lg:col-span-12 border-t border-primary/5 pt-16">
                <div class="space-y-8">

                    {{-- Title for Side Column --}}
                    <h3 class="text-sm font-bold text-mutedText uppercase tracking-widest pl-2">Your Standing</h3>

                    {{-- VIP Profile Card --}}
                    <div class="vip-card p-8 md:p-10">
                        <div class="absolute top-0 right-0 p-8 opacity-20 text-8xl">
                            <i class="fas fa-fingerprint"></i>
                        </div>

                        <div class="flex flex-col md:flex-row items-center justify-between gap-8 relative z-10">
                            <div class="flex items-center gap-6">
                                <div class="relative">
                                    <template x-if="!hasImage(userData.profile_picture)">
                                        <div class="w-20 h-20 rounded-full bg-white/20 backdrop-blur-md flex items-center justify-center text-2xl font-black shadow-inner border border-white/30" x-text="getInitials(userData.name)"></div>
                                    </template>
                                    <template x-if="hasImage(userData.profile_picture)">
                                        <img :src="userData.profile_picture" x-on:error="userData.profile_picture = null" class="w-20 h-20 rounded-full object-cover border-4 border-white/50 shadow-xl">
                                    </template>
                                    <div class="absolute bottom-1 right-1 w-5 h-5 bg-green-400 border-2 border-primary rounded-full shadow-md"></div>
                                </div>
                                <div class="text-left">
                                    <h2 class="text-2xl md:text-3xl font-black tracking-wide" x-text="userData.name"></h2>
                                    <p class="text-sm font-medium text-white/70 uppercase tracking-widest mt-1">Exclusive Partner Member</p>
                                </div>
                            </div>

                            <div class="flex flex-col md:items-end text-center md:text-right">
                                <p class="text-xs font-bold text-white/70 mb-1 uppercase tracking-wider">Total Earnings (<span x-text="getFilterLabel()"></span>)</p>
                                <h3 class="text-4xl md:text-5xl font-black tracking-tight">₹<span x-text="formatMoney(userData.earnings)"></span></h3>
                            </div>
                        </div>
                    </div>

                    {{-- Bottom Stats Grid (Horizontal on Desktop) --}}
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <div class="premium-glass p-6 flex flex-col justify-center items-start relative overflow-hidden group">
                            <div class="absolute -right-4 -top-4 text-primary/10 text-6xl transition-transform group-hover:scale-110"><i class="fas fa-trophy"></i></div>
                            <p class="text-xs font-bold text-mutedText mb-1 uppercase tracking-wider relative z-10">Current Rank</p>
                            <p class="text-4xl font-black text-mainText relative z-10">#<span x-text="userData.rank"></span></p>
                        </div>
                        <div class="premium-glass p-6 flex flex-col justify-center items-start relative overflow-hidden group">
                            <div class="absolute -right-4 -top-4 text-secondary/10 text-6xl transition-transform group-hover:scale-110"><i class="fas fa-chart-line"></i></div>
                            <p class="text-xs font-bold text-mutedText mb-1 uppercase tracking-wider relative z-10">Total Sales</p>
                            <p class="text-4xl font-black text-mainText relative z-10" x-text="userData.sale_count || 0"></p>
                        </div>
                    </div>

                </div>
            </div>

        </div>
    </div>
</div>

@push('scripts')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
<script>
    document.addEventListener('alpine:init', () => {
        Alpine.data('leaderboardData', () => ({
            filter: 'last_30_days',
            loading: true,
            topThree: [],
            restOfTopTen: [],
            debounceTimer: null,
            userData: {
                name: 'Loading...',
                rank: '-',
                earnings: 0,
                sale_count: 0,
                profile_picture: null
            },
            availableFilters: [
                { label: 'Weekly', value: 'last_7_days' },
                { label: 'Monthly', value: 'last_30_days' },
                { label: 'Yearly', value: 'this_year' },
                { label: 'All Time', value: 'all_time' }
            ],

            init() {
                this.fetchData();
            },

            setFilter(newFilter) {
                if (this.filter === newFilter) return;
                this.filter = newFilter;

                if (this.debounceTimer) clearTimeout(this.debounceTimer);
                this.debounceTimer = setTimeout(() => {
                    this.fetchData();
                }, 300);
            },

            getFilterLabel() {
                const f = this.availableFilters.find(x => x.value === this.filter);
                return f ? f.label : 'Period';
            },

            fetchData() {
                this.loading = true;

                // Simulated delay hatane ke liye ya custom behavior ke liye yahan fetch setup hai
                fetch(`{{ route('student.leaderboard.data') }}?filter=${this.filter}`)
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            this.renderData(data);
                        }
                    })
                    .catch(err => {
                        console.error('Leaderboard Fetch Error:', err);
                    })
                    .finally(() => {
                        // Smooth transition experience ke liye chota sa timeout
                        setTimeout(() => {
                            this.loading = false;
                        }, 200);
                    });
            },

            renderData(data) {
                const lb = data.leaderboard || [];
                this.topThree = lb.slice(0, 3);
                this.restOfTopTen = lb.slice(3, 10);

                this.userData = {
                    name: data.user_name,
                    rank: data.user_rank || '-',
                    earnings: data.user_earnings || 0,
                    sale_count: data.user_sale_count || 0,
                    profile_picture: data.user_profile_picture
                };
            },

            formatMoney(amount) {
                if(amount === undefined || amount === null) return '0';
                return Number(amount).toLocaleString('en-IN');
            },

            hasImage(url) {
                if (!url) return false;
                if (url.includes('default-avatar')) return false;
                if (url.includes('default.png')) return false;
                if (url.includes('ui-avatars.com')) return false;
                return true;
            },

            getInitials(name) {
                if (!name) return '?';
                const parts = name.trim().split(' ');
                if (parts.length === 1) return parts[0].substring(0, 2).toUpperCase();
                return (parts[0][0] + parts[parts.length - 1][0]).toUpperCase();
            }
        }));
    });
</script>
@endpush
@endsection
