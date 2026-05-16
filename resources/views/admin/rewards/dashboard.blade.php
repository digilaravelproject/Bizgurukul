@extends('layouts.admin')

@section('title', 'Rewards & Leaderboard Mastery')

@section('content')
<div class="p-6 space-y-8 animate-fade-in">
    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
        <div>
            <h1 class="text-3xl font-black text-slate-800 tracking-tight">REWARD <span class="text-indigo-600">MASTERY</span></h1>
            <p class="text-slate-500 font-medium">Global tracking, progress metrics, and performance analytics.</p>
        </div>
        <div class="flex gap-3">
            <div class="bg-white px-4 py-2 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-indigo-50 flex items-center justify-center text-indigo-600">
                    <i class="fas fa-users text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Total Achievers</p>
                    <p class="text-lg font-black text-slate-800 leading-none">{{ $achievers->total() }}</p>
                </div>
            </div>
            <div class="bg-white px-4 py-2 rounded-2xl border border-slate-200 shadow-sm flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <i class="fas fa-trophy text-lg"></i>
                </div>
                <div>
                    <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">Rewards Claimed</p>
                    <p class="text-lg font-black text-slate-800 leading-none">{{ $timeline->total() }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- 2x2 Grid Layout for the Dashboard Sections --}}
    <div class="grid grid-cols-1 xl:grid-cols-2 gap-8">
        
        {{-- Section 1: Achievers List --}}
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-xl shadow-slate-200/50 flex flex-col overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="font-black text-slate-800 uppercase tracking-widest flex items-center gap-3">
                    <i class="fas fa-award text-indigo-500"></i> Recent Achievers
                </h3>
            </div>
            <div class="flex-1 overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50/80">
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">User</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Earnings</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Milestones</th>
                            <th class="px-6 py-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse($achievers as $user)
                        <tr class="hover:bg-slate-50 transition-colors group">
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-3">
                                    <img src="{{ $user->profile_image_url }}" class="w-10 h-10 rounded-xl object-cover ring-2 ring-slate-100" />
                                    <div>
                                        <p class="font-bold text-slate-800 text-sm">{{ $user->name }}</p>
                                        <p class="text-[10px] text-slate-400 font-medium">{{ $user->email }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 text-sm font-black text-indigo-600">₹{{ number_format($user->commissions_sum_amount) }}</td>
                            <td class="px-6 py-4">
                                <div class="flex -space-x-2">
                                    @foreach($user->userAchievements->take(3) as $ua)
                                        <div class="w-7 h-7 rounded-full bg-indigo-100 border-2 border-white flex items-center justify-center text-[8px] font-bold text-indigo-600" title="{{ $ua->achievement->short_title }}">
                                            {{ substr($ua->achievement->short_title, 0, 1) }}
                                        </div>
                                    @endforeach
                                    @if($user->userAchievements->count() > 3)
                                        <div class="w-7 h-7 rounded-full bg-slate-100 border-2 border-white flex items-center justify-center text-[8px] font-bold text-slate-400">
                                            +{{ $user->userAchievements->count() - 3 }}
                                        </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-6 py-4">
                                <a href="{{ route('admin.users.show', $user->id) }}" class="text-slate-400 hover:text-indigo-600 transition-colors">
                                    <i class="fas fa-external-link-alt text-xs"></i>
                                </a>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="px-6 py-12 text-center text-slate-400 font-medium italic">No achievers found yet.</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($achievers->hasPages())
            <div class="p-4 bg-slate-50 border-t border-slate-100">
                {{ $achievers->appends(['achievers_page' => $achievers->currentPage()])->links() }}
            </div>
            @endif
        </div>

        {{-- Section 2: Progress Tracker --}}
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-xl shadow-slate-200/50 flex flex-col overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="font-black text-slate-800 uppercase tracking-widest flex items-center gap-3">
                    <i class="fas fa-spinner text-indigo-500 animate-spin-slow"></i> Progress Metrics
                </h3>
                <span class="text-[10px] font-black text-slate-400 uppercase">Closest to Target</span>
            </div>
            <div class="p-6 flex-1 space-y-6">
                @forelse($progressTracker as $user)
                    @php
                        $earned = $user->commissions_sum_amount ?: 0;
                        $target = $user->next_milestone_target;
                        $remaining = $target - $earned;
                        $percent = $target > 0 ? min(100, ($earned / $target) * 100) : 0;
                    @endphp
                    <div class="space-y-2 group">
                        <div class="flex justify-between items-end">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-lg bg-slate-100 flex items-center justify-center text-slate-400 font-bold text-xs group-hover:bg-indigo-50 group-hover:text-indigo-600 transition-colors">
                                    {{ substr($user->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="font-bold text-slate-800 text-sm">{{ $user->name }}</p>
                                    <p class="text-[9px] text-indigo-500 font-black uppercase tracking-widest">Next Achievement: {{ $user->next_milestone_title }}</p>
                                </div>
                            </div>
                            <div class="text-right">
                                <p class="text-sm font-bold text-slate-600 leading-none mb-1">Needs</p>
                                <p class="text-sm font-black text-slate-900 leading-none">₹{{ number_format($remaining) }}</p>
                            </div>
                        </div>
                        <div class="w-full h-3 bg-slate-100 rounded-full overflow-hidden p-[1px] border border-slate-200/50">
                            <div class="h-full bg-indigo-500 rounded-full transition-all duration-1000 shadow-[0_0_10px_rgba(79,70,229,0.4)]" style="width: {{ $percent }}%"></div>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-slate-400 font-medium italic">No active progress data available.</div>
                @endforelse
            </div>
            @if($progressTracker->hasPages())
            <div class="p-4 bg-slate-50 border-t border-slate-100">
                {{ $progressTracker->appends(['progress_page' => $progressTracker->currentPage()])->links() }}
            </div>
            @endif
        </div>

        {{-- Section 3: Top Performers (Leaderboard) --}}
        <div class="bg-indigo-900 rounded-[2.5rem] p-8 text-white shadow-2xl shadow-indigo-900/30 relative overflow-hidden">
            {{-- Decorative elements --}}
            <div class="absolute -top-24 -right-24 w-64 h-64 bg-white/5 rounded-full blur-3xl"></div>
            <div class="absolute -bottom-24 -left-24 w-64 h-64 bg-indigo-500/10 rounded-full blur-3xl"></div>

            <h3 class="font-black text-white uppercase tracking-[0.2em] mb-8 flex items-center gap-4 relative z-10">
                <span class="w-10 h-10 rounded-xl bg-white/10 flex items-center justify-center">
                    <i class="fas fa-chart-line"></i>
                </span>
                Hall of Fame
            </h3>

            <div class="space-y-4 relative z-10">
                @forelse($leaderboard as $index => $user)
                    @php
                        $rankColors = [
                            0 => 'bg-yellow-400 text-yellow-900',
                            1 => 'bg-slate-300 text-slate-800',
                            2 => 'bg-amber-600 text-white'
                        ];
                        $rankColor = $rankColors[$index] ?? 'bg-white/10 text-white';
                    @endphp
                    <div class="flex items-center justify-between p-4 rounded-2xl bg-white/5 border border-white/5 hover:bg-white/10 transition-all hover:translate-x-2">
                        <div class="flex items-center gap-5">
                            <span class="w-8 h-8 rounded-lg flex items-center justify-center font-black text-xs {{ $rankColor }}">
                                {{ $index + 1 }}
                            </span>
                            <div class="flex items-center gap-3">
                                <img src="{{ $user->profile_image_url }}" class="w-10 h-10 rounded-full border-2 border-white/20" />
                                <div>
                                    <p class="font-bold text-sm tracking-tight text-white">{{ $user->name }}</p>
                                    <p class="text-[10px] text-indigo-200 font-black uppercase tracking-widest">Active Partner</p>
                                </div>
                            </div>
                        </div>
                        <div class="text-right">
                            <p class="text-[10px] font-black text-indigo-300 uppercase tracking-widest leading-none mb-1">Lifetime</p>
                            <p class="text-lg font-black tracking-tighter text-white">₹{{ number_format($user->commissions_sum_amount) }}</p>
                        </div>
                    </div>
                @empty
                    <div class="py-12 text-center text-white/40 font-medium italic">Leaderboard is waiting for champions.</div>
                @endforelse
            </div>
        </div>

        {{-- Section 4: Early Achievers Timeline --}}
        <div class="bg-white rounded-[2rem] border border-slate-200 shadow-xl shadow-slate-200/50 flex flex-col overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex justify-between items-center bg-slate-50/50">
                <h3 class="font-black text-slate-800 uppercase tracking-widest flex items-center gap-3">
                    <i class="fas fa-history text-indigo-500"></i> Unlock Timeline
                </h3>
            </div>
            <div class="p-6 flex-1 relative">
                {{-- Vertical Line --}}
                <div class="absolute left-10 top-8 bottom-8 w-px bg-slate-100"></div>

                <div class="space-y-8 relative">
                    @forelse($timeline as $item)
                        <div class="flex gap-6 items-start group">
                            <div class="relative z-10 shrink-0">
                                <div class="w-8 h-8 rounded-full bg-white border-2 border-indigo-500 flex items-center justify-center text-indigo-600 shadow-lg group-hover:scale-125 transition-transform">
                                    <i class="fas fa-check text-[10px]"></i>
                                </div>
                            </div>
                            <div class="pt-1 flex-1">
                                <div class="flex justify-between items-start">
                                    <div>
                                        <p class="font-black text-slate-800 text-sm uppercase tracking-tight">{{ $item->user->name }}</p>
                                        <p class="text-xs text-indigo-600 font-bold mt-0.5">Unlocked <span class="uppercase tracking-widest">{{ $item->achievement->short_title }}</span></p>
                                    </div>
                                    <span class="text-[10px] font-bold text-slate-400 bg-slate-50 px-2 py-1 rounded-lg">{{ $item->unlocked_at->diffForHumans() }}</span>
                                </div>
                                <p class="text-[10px] text-slate-400 font-medium mt-2 leading-relaxed">Achieved with reward target of <span class="font-bold">₹{{ number_format($item->achievement->target_amount) }}</span></p>
                            </div>
                        </div>
                    @empty
                        <div class="py-12 text-center text-slate-400 font-medium italic">No completion history recorded.</div>
                    @endforelse
                </div>
            </div>
            @if($timeline->hasPages())
            <div class="p-4 bg-slate-50 border-t border-slate-100">
                {{ $timeline->appends(['timeline_page' => $timeline->currentPage()])->links() }}
            </div>
            @endif
        </div>

    </div>
</div>

<style>
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(10px); }
        to { opacity: 1; transform: translateY(0); }
    }
    .animate-fade-in {
        animation: fadeIn 0.6s ease-out forwards;
    }
    .animate-spin-slow {
        animation: spin 3s linear infinite;
    }
</style>
@endsection
