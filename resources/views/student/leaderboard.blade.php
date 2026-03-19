@extends('layouts.user.app')

@section('title', 'Leaderboard | ' . config('app.name', 'Skills Pehle'))

@push('styles')
<style>
    /* Clean & Modern Mesh Background using Platform Colors */
    .dashboard-bg {
        background-color: rgb(var(--color-bg-body));
        background-image:
            radial-gradient(at 0% 0%, rgba(var(--color-primary) / 0.15) 0px, transparent 50%),
            radial-gradient(at 100% 100%, rgba(var(--color-secondary) / 0.15) 0px, transparent 50%);
        background-attachment: fixed;
    }

    /* Premium Bento Box / Glass Cards using Platform Card Colors */
    .bento-card {
        background: rgba(var(--color-bg-card) / 0.85);
        backdrop-filter: blur(16px);
        -webkit-backdrop-filter: blur(16px);
        border: 1px solid rgba(var(--color-primary) / 0.08);
        border-radius: 1.5rem;
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .bento-card:hover {
        box-shadow: 0 10px 25px -5px rgba(var(--color-primary) / 0.15);
    }

    /* Unique Podium Styling */
    .top-rank-card {
        position: relative;
        overflow: hidden;
    }

    .top-rank-card::before {
        content: '';
        position: absolute;
        top: 0; left: 0; right: 0; height: 4px;
    }

    /* Keep Medals distinct */
    .rank-1::before { background: linear-gradient(90deg, #F59E0B, #FBBF24); } /* Gold */
    .rank-2::before { background: linear-gradient(90deg, #94A3B8, #CBD5E1); } /* Silver */
    .rank-3::before { background: linear-gradient(90deg, #B45309, #D97706); } /* Bronze */

    /* Glow effect for Rank 1 */
    .rank-1-glow {
        position: absolute;
        inset: -20px;
        background: radial-gradient(circle, rgba(245, 158, 11, 0.15) 0%, transparent 60%);
        z-index: -1;
        pointer-events: none;
        animation: pulse-glow 3s infinite alternate;
    }

    @keyframes pulse-glow {
        0% { opacity: 0.5; transform: scale(0.95); }
        100% { opacity: 1; transform: scale(1.05); }
    }

    /* Segmented Filters (Pill style) - Padding & Font-size removed for Tailwind usage */
    .filter-pill {
        border-radius: 9999px;
        font-weight: 600;
        transition: all 0.2s ease;
        position: relative;
        z-index: 1;
        color: var(--color-mutedText);
    }

    .filter-pill:hover:not(.filter-active) {
        color: var(--color-mainText);
        background: rgba(var(--color-primary) / 0.05);
    }

    .filter-active {
        background: rgb(var(--color-primary));
        color: white !important;
        box-shadow: 0 4px 14px 0 rgba(var(--color-primary) / 0.3);
    }

    .brand-gradient-text {
        background: linear-gradient(135deg, rgb(var(--color-primary)) 0%, rgb(var(--color-secondary)) 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }
</style>
@endpush

@section('content')
<div class="dashboard-bg min-h-screen pb-20 pt-8" x-data="leaderboardData()">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row items-start md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-black text-mainText tracking-tight">
                    Top <span class="brand-gradient-text">Performers</span>
                </h1>
                <p class="text-sm text-mutedText font-medium mt-1">Hall of Fame at Skills Pehle</p>
            </div>

            {{-- Modern Time Filters (Desktop) --}}
            <div class="hidden md:flex bg-surface p-1 rounded-full shadow-sm border border-primary/10">
                <template x-for="f in availableFilters" :key="f.value">
                    <button @click="setFilter(f.value)"
                            class="filter-pill px-4 lg:px-5 py-2 text-sm"
                            :class="filter === f.value ? 'filter-active' : 'text-mutedText'">
                        <span x-text="f.label"></span>
                    </button>
                </template>
            </div>
        </div>

        {{-- Mobile Filters (Shows only on small screens, Fits in ONE line without scroll) --}}
        <div class="flex md:hidden bg-surface p-1 rounded-full shadow-sm border border-primary/10 mb-6 w-full justify-between items-center gap-0.5">
            <template x-for="f in availableFilters" :key="f.value">
                <button @click="setFilter(f.value)"
                        class="filter-pill flex-1 flex items-center justify-center px-0.5 py-2 text-[10px] xs:text-[11px] sm:text-xs leading-none whitespace-nowrap tracking-tight"
                        :class="filter === f.value ? 'filter-active' : 'text-mutedText'">
                    <span x-text="f.label"></span>
                </button>
            </template>
        </div>

        {{-- Loader --}}
        <div x-show="loading" class="flex flex-col items-center justify-center py-20">
            <div class="w-12 h-12 border-4 border-primary/20 border-t-primary rounded-full animate-spin mb-4"></div>
            <p class="text-sm font-bold text-mutedText uppercase tracking-widest">Loading Data...</p>
        </div>

        {{-- Main Grid Layout (Profile Left, Leaderboard Right) --}}
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-8" x-show="!loading" style="display: none;">

            {{-- LEFT COLUMN: USER STATS --}}
            <div class="lg:col-span-4 space-y-6">

                {{-- Main User Profile Card --}}
                <div class="bento-card p-6 relative overflow-hidden">
                    <div class="absolute top-0 right-0 p-4 opacity-5 text-6xl text-primary">
                        <i class="fas fa-chart-pie"></i>
                    </div>

                    <p class="text-xs font-bold text-mutedText uppercase tracking-widest mb-4">Your Profile</p>

                    <div class="flex items-center gap-4 mb-6">
                        <div class="relative">
                            <template x-if="!hasImage(userData.profile_picture)">
                                <div class="w-16 h-16 rounded-full bg-primary/10 text-primary flex items-center justify-center text-xl font-black shadow-inner" x-text="getInitials(userData.name)"></div>
                            </template>
                            <template x-if="hasImage(userData.profile_picture)">
                                <img :src="userData.profile_picture" @@error="userData.profile_picture = null" class="w-16 h-16 rounded-full object-cover border-2 border-surface shadow-md">
                            </template>
                            <div class="absolute -bottom-1 -right-1 w-5 h-5 bg-green-500 border-2 border-surface rounded-full"></div>
                        </div>
                        <div>
                            <h2 class="text-xl font-bold text-mainText truncate max-w-[180px]" x-text="userData.name"></h2>
                            <p class="text-sm text-mutedText font-medium">Partner</p>
                        </div>
                    </div>

                    <div class="bg-surface rounded-2xl p-5 border border-primary/5 shadow-sm">
                        <p class="text-xs font-bold text-mutedText mb-1">Total Earnings (<span x-text="getFilterLabel()"></span>)</p>
                        <h3 class="text-3xl font-black text-mainText">₹<span x-text="formatMoney(userData.earnings)"></span></h3>
                    </div>
                </div>

                {{-- Mini Stats Grid --}}
                <div class="grid grid-cols-2 gap-4">
                    <div class="bento-card p-5 text-center flex flex-col justify-center">
                        <i class="fas fa-trophy text-primary text-xl mb-2"></i>
                        <p class="text-xs font-bold text-mutedText mb-1">Your Rank</p>
                        <p class="text-2xl font-black text-mainText">#<span x-text="userData.rank"></span></p>
                    </div>
                    <div class="bento-card p-5 text-center flex flex-col justify-center">
                        <i class="fas fa-bolt text-secondary text-xl mb-2"></i>
                        <p class="text-xs font-bold text-mutedText mb-1">Total Sales</p>
                        <p class="text-2xl font-black text-mainText" x-text="userData.sale_count || 0"></p>
                    </div>
                </div>

            </div>

            {{-- RIGHT COLUMN: LEADERBOARD SHOWCASE --}}
            <div class="lg:col-span-8">

                {{-- TOP 3 CREATIVE SHOWCASE (Unique Horizontal Layout) --}}
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8 items-end" x-show="topThree.length > 0">

                    {{-- Rank 2 (Left) --}}
                    <template x-if="topThree[1]">
                        <div class="bento-card top-rank-card rank-2 p-5 text-center flex flex-col items-center md:mb-4">
                            <div class="w-8 h-8 rounded-full bg-slate-100 text-slate-600 font-black text-xs flex items-center justify-center mb-3">#2</div>
                            <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-slate-200 mb-3 shadow-sm bg-surface">
                                <template x-if="!hasImage(topThree[1].profile_picture)"><div class="w-full h-full flex items-center justify-center font-bold text-slate-400" x-text="getInitials(topThree[1].name)"></div></template>
                                <template x-if="hasImage(topThree[1].profile_picture)"><img :src="topThree[1].profile_picture" class="w-full h-full object-cover"></template>
                            </div>
                            <h3 class="text-sm font-bold text-mainText truncate w-full" x-text="topThree[1].name"></h3>
                            <p class="text-sm font-black text-slate-500 mt-1">₹<span x-text="formatMoney(topThree[1].earnings)"></span></p>
                        </div>
                    </template>

                    {{-- Rank 1 (Center - Elevated) --}}
                    <template x-if="topThree[0]">
                        <div class="bento-card top-rank-card rank-1 p-6 text-center flex flex-col items-center relative z-10 border-amber-200/50 shadow-xl">
                            <div class="rank-1-glow"></div>
                            <i class="fas fa-crown text-amber-500 text-2xl mb-2 animate-bounce"></i>
                            <div class="w-20 h-20 rounded-full overflow-hidden border-4 border-amber-400 mb-4 shadow-md bg-surface">
                                <template x-if="!hasImage(topThree[0].profile_picture)"><div class="w-full h-full flex items-center justify-center font-bold text-2xl text-amber-500" x-text="getInitials(topThree[0].name)"></div></template>
                                <template x-if="hasImage(topThree[0].profile_picture)"><img :src="topThree[0].profile_picture" class="w-full h-full object-cover"></template>
                            </div>
                            <h3 class="text-base font-black text-mainText truncate w-full" x-text="topThree[0].name"></h3>
                            <div class="mt-2 bg-amber-50 text-amber-700 px-4 py-1.5 rounded-lg text-sm font-black border border-amber-200/60 inline-block">
                                ₹<span x-text="formatMoney(topThree[0].earnings)"></span>
                            </div>
                        </div>
                    </template>

                    {{-- Rank 3 (Right) --}}
                    <template x-if="topThree[2]">
                        <div class="bento-card top-rank-card rank-3 p-5 text-center flex flex-col items-center md:mb-4">
                            <div class="w-8 h-8 rounded-full bg-orange-50 text-orange-600 font-black text-xs flex items-center justify-center mb-3">#3</div>
                            <div class="w-16 h-16 rounded-full overflow-hidden border-2 border-orange-200/60 mb-3 shadow-sm bg-surface">
                                <template x-if="!hasImage(topThree[2].profile_picture)"><div class="w-full h-full flex items-center justify-center font-bold text-orange-400" x-text="getInitials(topThree[2].name)"></div></template>
                                <template x-if="hasImage(topThree[2].profile_picture)"><img :src="topThree[2].profile_picture" class="w-full h-full object-cover"></template>
                            </div>
                            <h3 class="text-sm font-bold text-mainText truncate w-full" x-text="topThree[2].name"></h3>
                            <p class="text-sm font-black text-orange-600/80 mt-1">₹<span x-text="formatMoney(topThree[2].earnings)"></span></p>
                        </div>
                    </template>
                </div>

                {{-- REST OF THE LEADERBOARD (2-Column Compact Grid) --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-3" x-show="restOfTopTen.length > 0">
                    <template x-for="user in restOfTopTen" :key="user.rank">
                        <div class="bento-card p-3 flex items-center justify-between hover:bg-surface/50 cursor-default">
                            <div class="flex items-center gap-3">
                                <div class="w-7 text-center text-xs font-black text-mutedText" x-text="user.rank"></div>

                                <div class="relative w-10 h-10 rounded-full overflow-hidden bg-primary/5">
                                    <template x-if="!hasImage(user.profile_picture)">
                                        <div class="w-full h-full flex items-center justify-center text-xs font-bold text-primary" x-text="getInitials(user.name)"></div>
                                    </template>
                                    <template x-if="hasImage(user.profile_picture)">
                                        <img :src="user.profile_picture" class="w-full h-full object-cover">
                                    </template>
                                </div>

                                <div class="flex flex-col">
                                    <span class="text-sm font-bold text-mainText leading-tight max-w-[100px] sm:max-w-[120px] truncate" x-text="user.name"></span>
                                </div>
                            </div>

                            <div class="text-right pl-2">
                                <span class="text-sm font-black text-primary">₹<span x-text="formatMoney(user.earnings)"></span></span>
                            </div>
                        </div>
                    </template>
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
                { label: 'Daily', value: 'today' },
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
                
                // Debounced fetch
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
                        this.loading = false;
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
