@extends('layouts.user.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<div class="space-y-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-3xl font-black text-mainText tracking-tight uppercase">My Rewards</h1>
            <p class="text-mutedText font-medium mt-1">Track your progress and unlock premium milestones.</p>
        </div>
        <div class="bg-surface px-6 py-3 rounded-2xl border border-primary/10 premium-shadow">
            <span class="text-[10px] font-black text-mutedText uppercase tracking-widest block">Total Earnings</span>
            <span class="text-xl font-black text-primary">₹{{ number_format($earningsStats['all_time']) }}</span>
        </div>
    </div>

    {{-- ACHIEVEMENTS & SPEEDOMETER --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8">
        {{-- Speedometer Card --}}
        <div class="lg:col-span-1 bg-surface rounded-[2.5rem] p-8 border border-primary/10 premium-shadow relative overflow-hidden flex flex-col items-center justify-center text-center">
            <div class="absolute -top-12 -left-12 w-48 h-48 bg-primary/5 blur-[50px] rounded-full pointer-events-none"></div>

            <div class="relative z-10 w-full">
                <h3 class="text-xs font-black text-mutedText uppercase tracking-[3px] mb-8">
                    Earnings Velocity
                </h3>

                {{-- Semi-Circle Speedometer --}}
                <div class="relative w-64 h-40 mx-auto flex items-center justify-center">
                    <svg viewBox="0 0 100 60" class="w-full h-full transform transition-all duration-1000">
                        {{-- Background Track --}}
                        <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none" stroke="rgba(var(--color-primary), 0.05)" stroke-width="10" stroke-linecap="round" />
                        {{-- Progress Track --}}
                        <path d="M 10 50 A 40 40 0 0 1 90 50" fill="none" stroke="url(#speed-gradient)" stroke-width="10" stroke-linecap="round"
                                stroke-dasharray="125.66" stroke-dashoffset="{{ 125.66 * (1 - ($achievementData['percentage'] / 100)) }}" class="transition-all duration-[2000ms] ease-out" />

                        <defs>
                            <linearGradient id="speed-gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" style="stop-color:rgb(var(--color-primary))" />
                                <stop offset="100%" style="stop-color:rgb(var(--color-secondary))" />
                            </linearGradient>
                        </defs>

                        {{-- Needle --}}
                        <g transform="rotate({{ -90 + (180 * ($achievementData['percentage'] / 100)) }} 50 50)" class="transition-all duration-[2000ms] ease-out origin-center">
                            <line x1="50" y1="50" x2="15" y2="50" stroke="white" stroke-width="2" stroke-linecap="round" />
                            <circle cx="50" cy="50" r="4" fill="white" />
                        </g>
                    </svg>

                    <div class="absolute bottom-2 left-1/2 -translate-x-1/2 text-center">
                        <span class="block text-3xl font-black text-mainText tracking-tight">{{ round($achievementData['percentage']) }}%</span>
                        <span class="text-[10px] font-bold text-mutedText uppercase tracking-widest">To Next Rank</span>
                    </div>
                </div>

                <div class="mt-8 space-y-4">
                    <div class="flex items-center justify-between p-5 bg-primary/5 rounded-[2rem] border border-primary/5">
                        <div class="text-left">
                            <p class="text-[9px] font-black text-mutedText uppercase tracking-[2px]">Current Level</p>
                            <h4 class="text-base font-black text-mainText uppercase mt-1">
                                {{ $achievementData['current_milestone'] ? $achievementData['current_milestone']->short_title : 'Novice' }}
                            </h4>
                        </div>
                        <div class="h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary shadow-inner">
                            <i class="fas fa-crown text-lg"></i>
                        </div>
                    </div>

                    @if($achievementData['next_achievement'])
                    <div class="pt-2">
                        <p class="text-[11px] font-bold text-mutedText uppercase tracking-widest leading-relaxed">
                            Upgrade to: <span class="text-primary">{{ $achievementData['next_achievement']->short_title }}</span>
                        </p>
                        <p class="text-[10px] font-medium text-mutedText uppercase tracking-widest mt-1">
                            ₹{{ number_format($achievementData['remaining_to_next']) }} Remaining
                        </p>
                    </div>
                    @else
                    <div class="pt-2 inline-flex items-center gap-2 bg-success/10 px-4 py-2 rounded-full border border-success/10">
                        <span class="h-2 w-2 rounded-full bg-success"></span>
                        <span class="text-[10px] font-black text-success uppercase tracking-widest">Ultimate Rank Achieved</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Achievement Milestones Card --}}
        <div class="lg:col-span-2 bg-surface rounded-[2.5rem] p-8 border border-primary/10 premium-shadow relative overflow-hidden flex flex-col">
            <div class="flex items-center justify-between mb-8">
                <h3 class="text-lg font-black text-mainText uppercase tracking-widest flex items-center gap-3">
                    <i class="fas fa-trophy text-secondary"></i> Reward Milestones
                </h3>
                <div class="flex gap-2">
                    <span class="px-3 py-1 bg-success/10 text-success text-[9px] font-black uppercase tracking-widest rounded-full border border-success/20">Unlocked: {{ count(array_filter($userMilestones, fn($s) => $s != 'locked')) }}</span>
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 flex-1">
                @foreach($allMilestones as $milestone)
                    @php
                        $status = $userMilestones[$milestone->id] ?? 'locked';
                        $isLocked = $status === 'locked';
                        $isClaimed = $status === 'claimed';
                        $isUnlocked = $status === 'unlocked';
                    @endphp
                    <div class="relative group p-4 rounded-3xl border transition-all duration-500 {{ $isLocked ? 'bg-primary/5 border-primary/5 opacity-70 grayscale' : 'bg-surface border-primary/20 shadow-xl shadow-primary/5' }}">
                        <div class="flex gap-4">
                            <div class="relative shrink-0">
                                @if($milestone->reward_image)
                                    <img src="{{ url('storage/' . $milestone->reward_image) }}" alt="{{ $milestone->title }}" class="h-16 w-16 rounded-2xl object-cover ring-2 ring-primary/10">
                                @else
                                    <div class="h-16 w-16 rounded-2xl bg-primary/10 flex items-center justify-center text-primary text-2xl">
                                        <i class="fas fa-gift"></i>
                                    </div>
                                @endif

                                @if($isClaimed)
                                    <div class="absolute -top-2 -right-2 h-6 w-6 rounded-full bg-success text-white flex items-center justify-center text-[10px] shadow-lg">
                                        <i class="fas fa-check"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0">
                                <div class="flex items-center justify-between gap-2">
                                    <h4 class="text-sm font-black text-mainText truncate uppercase">{{ $milestone->short_title }}</h4>
                                    <span class="shrink-0 text-[10px] font-black {{ $isLocked ? 'text-mutedText' : 'text-primary' }}">₹{{ number_format($milestone->target_amount) }}</span>
                                </div>
                                <p class="text-[10px] text-mutedText font-medium line-clamp-1 mt-1">{{ $milestone->reward_description }}</p>

                                <div class="mt-3">
                                    @if($isLocked)
                                        <div class="w-full h-1.5 bg-navy rounded-full overflow-hidden">
                                            <div class="h-full bg-mutedText/30" style="width: {{ min(100, ($earningsStats['all_time'] / $milestone->target_amount) * 100) }}%"></div>
                                        </div>
                                    @elseif($isUnlocked)
                                        <button onclick="claimReward({{ $milestone->id }})"
                                                class="w-full py-2 bg-primary text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-secondary transition-colors shadow-lg shadow-primary/20">
                                            Claim Reward
                                        </button>
                                    @else
                                        <span class="flex items-center gap-2 text-[10px] font-black text-success uppercase tracking-widest">
                                            <i class="fas fa-check-double"></i> Reward claimed
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<script>
    function claimReward(achievementId) {
        if (!confirm('Would you like to claim this reward?')) return;

        fetch(`/student/achievements/${achievementId}/claim`, {
            method: 'POST',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Content-Type': 'application/json',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Celebration Confetti
                confetti({
                    particleCount: 150,
                    spread: 70,
                    origin: { y: 0.6 },
                    colors: ['#8B5CF6', '#D946EF', '#ffffff']
                });

                const el = document.createElement('div');
                el.innerHTML = `
                    <div class="fixed top-6 right-6 bg-success text-white px-6 py-4 rounded-2xl shadow-2xl flex items-center gap-4 z-50 animate-fade-in-down">
                        <div class="w-10 h-10 rounded-full bg-white/20 flex items-center justify-center text-white text-lg">
                            <i class="fas fa-gift"></i>
                        </div>
                        <div>
                            <h4 class="font-black text-sm uppercase tracking-widest">Congratulations!</h4>
                            <p class="text-xs font-semibold">${data.message}</p>
                        </div>
                    </div>`;
                document.body.appendChild(el);

                setTimeout(() => {
                    location.reload();
                }, 2000);
            } else {
                alert(data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Something went wrong. Please try again.');
        });
    }
</script>
@endsection
