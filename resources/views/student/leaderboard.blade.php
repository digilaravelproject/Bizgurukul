@extends('layouts.user.app')

@section('title', 'Leaderboard | ' . config('app.name', 'Skills Pehle'))

@push('styles')
<style>
    .board-bg {
        background: linear-gradient(135deg, #f0eefd 0%, #fae8eb 100%);
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.7);
        backdrop-filter: blur(10px);
        -webkit-backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.5);
    }

    .glass-card-solid {
        background: #ffffff;
        border: 1px solid rgba(0, 0, 0, 0.05);
        box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.02);
    }

    .avatar-initials {
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #1e293b 0%, #0f172a 100%);
        color: white;
        font-weight: 900;
        text-transform: uppercase;
    }

    .crown-gold { color: #FFB300; filter: drop-shadow(0px 4px 6px rgba(255, 179, 0, 0.4)); }
    .crown-silver { color: #B0BEC5; filter: drop-shadow(0px 4px 6px rgba(176, 190, 197, 0.4)); }
    .crown-bronze { color: #BCAAA4; filter: drop-shadow(0px 4px 6px rgba(188, 170, 164, 0.4)); }

    .rank-badge-gold { background-color: #FFB300; color: white; }
    .rank-badge-silver { background-color: #B0BEC5; color: white; }
    .rank-badge-bronze { background-color: #BCAAA4; color: white; }

    .border-gold { border-color: #FFB300; }
    .border-silver { border-color: #B0BEC5; }
    .border-bronze { border-color: #BCAAA4; }

    .nav-tab.active {
        color: var(--color-primary);
        border-bottom: 2px solid var(--color-primary);
        font-weight: 800;
    }
    .nav-tab {
        color: var(--color-mutedText);
        font-weight: 600;
        border-bottom: 2px solid transparent;
        transition: all 0.2s ease;
    }
    .nav-tab:hover:not(.active) {
        color: var(--color-mainText);
        border-bottom: 2px solid rgba(0,0,0,0.1);
    }

    .metric-card-orange { background: #ffece5; }
    .metric-card-blue { background: #e5edff; }
    .metric-card-pink { background: #ffe5f1; }
</style>
@endpush

@section('content')
<div class="pb-24 font-sans text-mainText" x-data="leaderboardData()">

    <div class="flex items-center gap-3 mb-6 px-2 md:px-0">
        <i class="fas fa-trophy text-2xl text-mainText"></i>
        <h1 class="text-2xl font-black text-mainText">Leaderboard</h1>
    </div>

    <div class="flex flex-col xl:flex-row gap-6">

        {{-- LEFT COLUMN: THE BOARD --}}
        <div class="flex-[2] board-bg rounded-[2rem] p-6 md:p-10 relative overflow-hidden shadow-sm">

            {{-- Loading Overlay --}}
            <div x-show="loading" class="absolute inset-0 bg-white/50 backdrop-blur-sm z-50 flex flex-col items-center justify-center rounded-[2rem]">
                <i class="fas fa-circle-notch fa-spin text-4xl text-primary mb-4"></i>
                <p class="font-bold text-mainText tracking-widest uppercase text-xs">Fetching Ranks...</p>
            </div>

            {{-- Navigation Tabs --}}
            <div class="flex flex-wrap items-center justify-center gap-6 md:gap-12 mb-16 border-b border-primary/5 pb-2">
                <template x-for="f in availableFilters" :key="f.value">
                    <button @click="setFilter(f.value)"
                        :class="filter === f.value ? 'active' : ''"
                        class="nav-tab pb-3 text-sm md:text-base tracking-wide px-2">
                        <span x-text="f.label"></span>
                    </button>
                </template>
            </div>

            {{-- THE ELITE TRIO (Top 3) --}}
            <div class="flex flex-col md:flex-row justify-center items-end gap-8 md:gap-12 mb-16 relative z-10" x-show="topThree.length > 0">

                {{-- Rank 2 (Silver) --}}
                <template x-if="topThree[1]">
                    <div class="flex flex-col items-center order-2 md:order-1 relative w-full md:w-auto animate-fade-in-up" style="animation-delay: 100ms;">
                        <i class="fas fa-crown text-3xl crown-silver mb-2"></i>
                        <div class="relative group">
                            <template x-if="!hasImage(topThree[1].profile_picture)">
                                <div class="w-24 h-24 rounded-full border-4 border-silver avatar-initials text-3xl shadow-lg z-20 relative" x-text="getInitials(topThree[1].name)"></div>
                            </template>
                            <template x-if="hasImage(topThree[1].profile_picture)">
                                <img :src="topThree[1].profile_picture" @@error="topThree[1].profile_picture = null" class="w-24 h-24 rounded-full border-4 border-silver object-cover shadow-lg z-20 relative bg-white">
                            </template>
                            <div class="absolute -bottom-3 left-1/2 -translate-x-1/2 w-7 h-7 rounded-sm rank-badge-silver flex items-center justify-center font-black text-xs shadow-md z-30 transform rotate-45">
                                <span class="-rotate-45 block">2</span>
                            </div>
                        </div>
                        <div class="mt-6 text-center">
                            <h3 class="text-sm font-black text-mainText" x-text="topThree[1].name"></h3>
                            <p class="text-[11px] font-bold text-mutedText mt-1">₹<span x-text="formatMoney(topThree[1].earnings)"></span></p>
                        </div>
                    </div>
                </template>

                {{-- Rank 1 (Gold) --}}
                <template x-if="topThree[0]">
                    <div class="flex flex-col items-center order-1 md:order-2 relative w-full md:w-auto z-20 animate-fade-in-down mb-6 md:mb-12">
                        <i class="fas fa-crown text-5xl crown-gold mb-3 animate-bounce"></i>
                        <div class="relative group">
                            <template x-if="!hasImage(topThree[0].profile_picture)">
                                <div class="w-32 h-32 rounded-full border-4 border-gold avatar-initials text-4xl shadow-2xl z-20 relative" x-text="getInitials(topThree[0].name)"></div>
                            </template>
                            <template x-if="hasImage(topThree[0].profile_picture)">
                                <img :src="topThree[0].profile_picture" @@error="topThree[0].profile_picture = null" class="w-32 h-32 rounded-full border-4 border-gold object-cover shadow-2xl z-20 relative bg-white">
                            </template>
                            <div class="absolute -bottom-4 left-1/2 -translate-x-1/2 w-9 h-9 rounded-sm rank-badge-gold flex items-center justify-center font-black text-sm shadow-xl z-30 transform rotate-45">
                                <span class="-rotate-45 block">1</span>
                            </div>
                        </div>
                        <div class="mt-8 text-center bg-white/40 px-6 py-2 rounded-full backdrop-blur-md shadow-sm border border-white/60">
                            <h3 class="text-base font-black text-mainText" x-text="topThree[0].name"></h3>
                            <p class="text-sm font-black text-primary mt-0.5">₹<span x-text="formatMoney(topThree[0].earnings)"></span></p>
                        </div>
                    </div>
                </template>

                {{-- Rank 3 (Bronze) --}}
                <template x-if="topThree[2]">
                    <div class="flex flex-col items-center order-3 relative w-full md:w-auto animate-fade-in-up" style="animation-delay: 200ms;">
                        <i class="fas fa-crown text-2xl crown-bronze mb-2"></i>
                        <div class="relative group">
                            <template x-if="!hasImage(topThree[2].profile_picture)">
                                <div class="w-20 h-20 rounded-full border-4 border-bronze avatar-initials text-2xl shadow-md z-20 relative" x-text="getInitials(topThree[2].name)"></div>
                            </template>
                            <template x-if="hasImage(topThree[2].profile_picture)">
                                <img :src="topThree[2].profile_picture" @@error="topThree[2].profile_picture = null" class="w-20 h-20 rounded-full border-4 border-bronze object-cover shadow-md z-20 relative bg-white">
                            </template>
                            <div class="absolute -bottom-2 lg:-bottom-3 left-1/2 -translate-x-1/2 w-6 h-6 rounded-sm rank-badge-bronze flex items-center justify-center font-black text-[10px] shadow-sm z-30 transform rotate-45">
                                <span class="-rotate-45 block">3</span>
                            </div>
                        </div>
                        <div class="mt-5 lg:mt-6 text-center">
                            <h3 class="text-xs font-black text-mainText" x-text="topThree[2].name"></h3>
                            <p class="text-[10px] font-bold text-mutedText mt-0.5">₹<span x-text="formatMoney(topThree[2].earnings)"></span></p>
                        </div>
                    </div>
                </template>

            </div>

            {{-- EMPTY STATE --}}
            <div x-show="topThree.length === 0 && !loading" style="display: none;" class="text-center py-20 opacity-60">
                <i class="fas fa-ghost text-5xl mb-4"></i>
                <h3 class="text-xl font-bold">No High Scores Yet!</h3>
                <p class="text-sm mt-2">Check back later or change the time filter.</p>
            </div>

            {{-- LIST (4 to 10) --}}
            <div class="glass-card-solid rounded-[1.5rem] overflow-hidden relative z-10" x-show="restOfTopTen.length > 0">
                <div class="divide-y divide-gray-100">
                    <template x-for="user in restOfTopTen" :key="user.rank">
                        <div class="flex items-center justify-between p-4 md:px-6 md:py-4 hover:bg-gray-50 transition-colors">
                            <div class="flex items-center gap-4 md:gap-6">
                                <span class="text-sm font-black text-mainText w-4 text-center" x-text="user.rank"></span>

                                <template x-if="!hasImage(user.profile_picture)">
                                    <div class="w-10 h-10 rounded-full avatar-initials text-sm shadow-sm" x-text="getInitials(user.name)"></div>
                                </template>
                                <template x-if="hasImage(user.profile_picture)">
                                    <img :src="user.profile_picture" @@error="user.profile_picture = null" class="w-10 h-10 rounded-full object-cover shadow-sm bg-gray-100">
                                </template>

                                <span class="text-sm font-black text-mainText uppercase tracking-wide" x-text="user.name"></span>
                            </div>
                            <div class="text-right">
                                <span class="text-sm font-black text-mainText">₹<span x-text="formatMoney(user.earnings)"></span></span>
                            </div>
                        </div>
                    </template>
                </div>
            </div>

        </div>

        {{-- RIGHT COLUMN: USER SNAPSHOT --}}
        <div class="flex-1 flex flex-col gap-4">

            {{-- Profile Card --}}
            <div class="glass-card-solid rounded-[1.5rem] p-8 text-center flex flex-col items-center justify-center min-h-[200px]">
                <template x-if="!hasImage(userData.profile_picture)">
                    <div class="w-24 h-24 rounded-full avatar-initials text-3xl shadow-sm mb-4" x-text="getInitials(userData.name)"></div>
                </template>
                <template x-if="hasImage(userData.profile_picture)">
                    <img :src="userData.profile_picture" @@error="userData.profile_picture = null" class="w-24 h-24 rounded-full object-cover shadow-sm bg-gray-100 mb-4">
                </template>

                <h2 class="text-lg font-black text-mainText uppercase tracking-widest" x-text="userData.name"></h2>
                <p class="text-xs font-bold text-mutedText uppercase tracking-widest mt-1">Platform Partner</p>
            </div>

            {{-- Total Earnings --}}
            <div class="metric-card-orange rounded-[1.5rem] p-6 shadow-sm border border-orange-100/50 flex">
                <div class="bg-white w-10 h-10 flex items-center justify-center rounded-xl shadow-sm text-mainText mr-4 shrink-0">
                    <i class="fas fa-wallet"></i>
                </div>
                <div class="flex-1">
                    <p class="text-xs font-bold text-mainText mb-1 uppercase tracking-wider">Earnings (<span x-text="getFilterLabel()"></span>)</p>
                    <h3 class="text-2xl font-black text-[#ff6b35]">₹<span x-text="formatMoney(userData.earnings)"></span></h3>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4">
                {{-- Rank --}}
                <div class="metric-card-blue rounded-[1.5rem] p-6 shadow-sm border border-blue-100/50 flex flex-col justify-center">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-chart-line text-mainText"></i>
                        <p class="text-[10px] md:text-xs font-bold text-mainText uppercase tracking-wider">Your Rank</p>
                    </div>
                    <h3 class="text-xl md:text-2xl font-black text-[#3f7cf6]" x-text="userData.rank"></h3>
                </div>

                {{-- Sale Count --}}
                <div class="metric-card-pink rounded-[1.5rem] p-6 shadow-sm border border-pink-100/50 flex flex-col justify-center">
                    <div class="flex items-center gap-3 mb-2">
                        <i class="fas fa-tags text-mainText"></i>
                        <p class="text-[10px] md:text-xs font-bold text-mainText uppercase tracking-wider">Sale Count</p>
                    </div>
                    <h3 class="text-xl md:text-2xl font-black text-[#f63f82]" x-text="userData.sale_count || 0"></h3>
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
                { label: 'All Time', value: 'all_time' }
            ],

            init() {
                this.fetchData();
            },

            setFilter(newFilter) {
                if (this.filter === newFilter) return;
                this.filter = newFilter;
                this.fetchData();
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
                            const lb = data.leaderboard || [];
                            this.topThree = lb.slice(0, 3);
                            this.restOfTopTen = lb.slice(3, 10);

                            this.userData = {
                                name: data.user_name,
                                rank: data.user_rank,
                                earnings: data.user_earnings,
                                sale_count: data.user_sale_count,
                                profile_picture: data.user_profile_picture
                            };
                        }
                    })
                    .catch(err => {
                        console.error('Leaderboard Fetch Error:', err);
                    })
                    .finally(() => {
                        this.loading = false;
                    });
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
