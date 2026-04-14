@extends('layouts.user.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>

<style>
    .premium-shadow {
        box-shadow: 0 20px 40px -15px rgba(0, 0, 0, 0.05);
    }
    .brand-gradient {
        background: linear-gradient(135deg, rgb(var(--color-primary)) 0%, rgb(var(--color-secondary)) 100%);
    }
    @media (max-width: 640px) {
        .speedometer-container {
            width: 100% !important;
            max-width: 320px !important;
        }
    }
    .needle-glow {
        filter: drop-shadow(0 0 8px rgba(var(--color-primary), 0.8));
    }
    .text-glow {
        text-shadow: 0 0 15px rgba(var(--color-primary), 0.3);
    }
</style>

<div class="space-y-8">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 md:gap-4">
        <div>
            <h1 class="text-2xl md:text-3xl font-black text-mainText tracking-tight uppercase">My <span class="text-primary">Rewards</span></h1>
            <p class="text-xs md:text-sm text-mutedText font-medium mt-1">Track your progress and unlock premium milestones.</p>
        </div>
        <div class="bg-surface px-5 py-3 md:px-6 md:py-3 rounded-2xl border border-primary/10 premium-shadow flex flex-col md:block items-center text-center md:text-left">
            <span class="text-[9px] md:text-[10px] font-black text-mutedText uppercase tracking-widest block">Total Earnings</span>
            <span class="text-lg md:text-xl font-black text-primary">₹{{ number_format($earningsStats['all_time']) }}</span>
        </div>
    </div>

    {{-- ACHIEVEMENTS & SPEEDOMETER --}}
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 lg:gap-8 items-stretch">
        {{-- Speedometer Card --}}
        <div class="lg:col-span-5 xl:col-span-4 bg-surface rounded-[2.5rem] p-6 sm:p-8 lg:p-10 border border-primary/10 premium-shadow relative overflow-hidden flex flex-col items-center justify-center text-center group">
            <div class="absolute -top-24 -left-24 w-64 h-64 bg-primary/5 blur-[80px] rounded-full pointer-events-none group-hover:bg-primary/10 transition-colors duration-1000"></div>

            <div class="relative z-10 w-full max-w-sm mx-auto sm:max-w-none">
                <div class="flex items-center justify-center gap-4 mb-8 md:mb-10">
                    <span class="h-px w-8 md:w-12 bg-primary/20"></span>
                    <h3 class="text-[10px] md:text-[11px] font-black text-mutedText uppercase tracking-[4px]">Rank mastery</h3>
                    <span class="h-px w-8 md:w-12 bg-primary/20"></span>
                </div>

                {{-- Semi-Circle Segmented Speedometer --}}
                <div class="relative w-full aspect-[4/3] flex items-center justify-center -mt-6 sm:-mt-4">
                    @php
                        $totalActive = $allMilestones->count();
                        $dashTotal = 125.66; 
                        $gap = 2;
                        $segmentWidth = ($dashTotal - (($totalActive - 1) * $gap)) / ($totalActive ?: 1);
                        
                        $achievedCount = 0;
                        $currentIndex = -1;
                        foreach($allMilestones as $idx => $m) {
                            $status = $userMilestones[$m->id] ?? 'locked';
                            if(in_array($status, ['unlocked', 'claimed'])) {
                                $achievedCount++;
                            } elseif($currentIndex === -1) {
                                $currentIndex = $idx;
                            }
                        }
                        $currentMilestonePercent = $achievementData['percentage'] ?? 0;
                    @endphp

                    <svg viewBox="0 0 100 70" class="w-full h-auto max-w-[320px] filter drop-shadow-[0_10px_20px_rgba(0,0,0,0.1)]">
                        <defs>
                            <linearGradient id="achieved-gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" style="stop-color:rgb(var(--color-primary))" />
                                <stop offset="100%" style="stop-color:rgb(var(--color-secondary))" />
                            </linearGradient>
                            <linearGradient id="target-gradient" x1="0%" y1="0%" x2="100%" y2="0%">
                                <stop offset="0%" style="stop-color:#F59E0B" />
                                <stop offset="100%" style="stop-color:#ef4444" />
                            </linearGradient>
                            <filter id="glow">
                                <feGaussianBlur stdDeviation="1.5" result="coloredBlur"/>
                                <feMerge>
                                    <feMergeNode in="coloredBlur"/>
                                    <feMergeNode in="SourceGraphic"/>
                                </feMerge>
                            </filter>
                        </defs>

                        {{-- Glow Layer --}}
                        <path d="M 10 55 A 40 40 0 0 1 90 55" fill="none" stroke="rgba(var(--color-primary), 0.03)" stroke-width="18" stroke-linecap="round" />
                        
                        {{-- Background Track --}}
                        <path d="M 10 55 A 40 40 0 0 1 90 55" fill="none" stroke="rgba(var(--color-primary), 0.08)" stroke-width="14" stroke-linecap="round" />

                        {{-- Segments --}}
                        @foreach($allMilestones as $idx => $m)
                            @php
                                $status = $userMilestones[$m->id] ?? 'locked';
                                $isAchieved = in_array($status, ['unlocked', 'claimed']);
                                $isTarget = ($idx === $currentIndex);
                                // The dash offset formula to place each segment correctly
                                // Since dashTotal is 125.66 (pi*r), we move backward from the end
                                $startOffset = $dashTotal - ($idx * ($segmentWidth + $gap));
                            @endphp
                            
                            {{-- Base track segments --}}
                            <path d="M 10 55 A 40 40 0 0 1 90 55" 
                                  fill="none" 
                                  stroke="{{ $isAchieved ? 'url(#achieved-gradient)' : 'rgba(var(--color-primary), 0.04)' }}" 
                                  stroke-width="12" 
                                  stroke-linecap="round"
                                  stroke-dasharray="{{ $segmentWidth }} {{ $dashTotal }}" 
                                  stroke-dashoffset="{{ $startOffset }}"
                                  class="transition-all duration-700" />
                            
                            @if($isTarget)
                                {{-- Progress within current segment --}}
                                <path d="M 10 55 A 40 40 0 0 1 90 55" 
                                      fill="none" 
                                      stroke="url(#target-gradient)" 
                                      stroke-width="12" 
                                      stroke-linecap="round"
                                      stroke-dasharray="{{ ($currentMilestonePercent / 100) * $segmentWidth }} {{ $dashTotal }}" 
                                      stroke-dashoffset="{{ $startOffset }}"
                                      filter="url(#glow)"
                                      class="transition-all duration-1000 ease-out" />
                            @endif
                        @endforeach

                        {{-- Needle --}}
                        @php
                            $currentPos = $achievedCount + ($currentIndex !== -1 ? ($currentMilestonePercent / 100) : 0);
                            $totalPos = $totalActive ?: 1;
                            $needleAngle = 180 * ($currentPos / $totalPos); // 0% = Left, 100% = Right
                        @endphp
                        <g transform="rotate({{ $needleAngle }} 50 55)" class="transition-all duration-[1500ms] ease-out origin-[50px_55px]">
                            <path d="M 50 55 L 14 55" stroke="rgba(0,0,0,0.1)" stroke-width="4" stroke-linecap="round" transform="translate(0, 1.5)" />
                            <path d="M 50 55 L 14 55" stroke="#fff" stroke-width="3" stroke-linecap="round" class="needle-glow" />
                        </g>
                    </svg>

                    <div class="absolute bottom-2 sm:bottom-0 left-1/2 -translate-x-1/2 text-center w-full pointer-events-none">
                        <span class="block text-4xl sm:text-6xl font-black text-mainText tracking-tighter leading-none text-glow">{{ round($currentMilestonePercent) }}%</span>
                        <div class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full bg-primary/10 border border-primary/20 mt-2">
                             <span class="w-1.5 h-1.5 rounded-full bg-primary animate-pulse"></span>
                             <span class="text-[9px] sm:text-[10px] font-black text-primary uppercase tracking-[2px]">Next Level</span>
                        </div>
                    </div>
                </div>

                {{-- Rank Stats --}}
                <div class="mt-4 sm:mt-6 grid grid-cols-2 gap-3 sm:gap-4 text-left">
                    <div class="bg-surface/50 border border-primary/10 rounded-[1.5rem] p-4 sm:p-5 transition-all hover:bg-primary/5 hover:border-primary/30">
                        <p class="text-[8px] sm:text-[9px] font-black text-mutedText uppercase tracking-widest leading-none">Current Status</p>
                        <h4 class="text-xs sm:text-base font-black text-mainText mt-2 flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-success/10 flex items-center justify-center shrink-0">
                                <i class="fas fa-crown text-success text-[10px] sm:text-xs"></i>
                            </div>
                            <span class="truncate">{{ $achievementData['current_milestone'] ? $achievementData['current_milestone']->short_title : 'Novice' }}</span>
                        </h4>
                    </div>
                    <div class="bg-surface/50 border border-secondary/10 rounded-[1.5rem] p-4 sm:p-5 transition-all hover:bg-secondary/5 hover:border-secondary/30">
                        <p class="text-[8px] sm:text-[9px] font-black text-mutedText uppercase tracking-widest leading-none">Target Goal</p>
                        <h4 class="text-xs sm:text-base font-black text-primary mt-2 flex items-center gap-2">
                            <div class="w-6 h-6 rounded-lg bg-primary/10 flex items-center justify-center shrink-0">
                                <i class="fas fa-rocket text-primary text-[10px] sm:text-xs"></i>
                            </div>
                            <span class="truncate">{{ $achievementData['next_achievement'] ? $achievementData['next_achievement']->short_title : 'Max' }}</span>
                        </h4>
                    </div>
                </div>

                @if($achievementData['next_achievement'])
                <div class="mt-6 sm:mt-8 p-4 sm:p-5 bg-navy/40 rounded-[1.5rem] sm:rounded-[2rem] border border-primary/10 backdrop-blur-sm">
                    <div class="flex justify-between items-center mb-3">
                        <span class="text-[9px] sm:text-[10px] font-black text-mutedText uppercase tracking-widest">Requirement</span>
                        <span class="text-xs sm:text-sm font-black text-primary">₹{{ number_format($achievementData['remaining_to_next']) }}</span>
                    </div>
                    <div class="w-full h-2 bg-primary/10 rounded-full overflow-hidden p-[1px] border border-white/5">
                        <div class="h-full bg-primary transition-all duration-1500 shadow-[0_0_10px_rgba(var(--color-primary),0.6)] rounded-full" style="width: {{ $currentMilestonePercent }}%"></div>
                    </div>
                </div>
                @else
                <div class="mt-6 sm:mt-8 py-5 px-8 brand-gradient rounded-[1.5rem] sm:rounded-[2rem] shadow-2xl shadow-primary/30 flex items-center gap-4 justify-center text-white">
                    <i class="fas fa-trophy text-xl sm:text-2xl"></i>
                    <span class="text-[10px] sm:text-xs font-black uppercase tracking-[3px]">Elite Level reached</span>
                </div>
                @endif
            </div>
        </div>

        {{-- Achievement Milestones Card --}}
        <div class="lg:col-span-7 xl:col-span-8 bg-surface rounded-[2.5rem] p-6 sm:p-8 border border-primary/10 premium-shadow relative overflow-hidden flex flex-col">
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
                        
                        $progress = $milestoneProgress[$milestone->id] ?? 0;
                        $percent = min(100, ($progress / $milestone->target_amount) * 100);
                        
                        $isUpcoming = $milestone->start_date && $milestone->start_date->isFuture();
                        $isExpired = $milestone->end_date && $milestone->end_date->isPast();
                    @endphp
                    <div class="relative group p-4 rounded-3xl border transition-all duration-500 {{ $isLocked ? 'bg-primary/5 border-primary/5' : 'bg-surface border-primary/20 shadow-xl shadow-primary/5' }} {{ $isUpcoming ? 'opacity-50 grayscale' : '' }}">
                        @if($isUpcoming)
                            <div class="absolute inset-0 z-20 flex items-center justify-center pointer-events-none">
                                <span class="px-4 py-2 bg-primary/90 text-white text-[10px] font-black uppercase tracking-[3px] rounded-full shadow-2xl rotate-12">Upcoming</span>
                            </div>
                        @endif

                        <div class="flex flex-col sm:flex-row gap-4 sm:gap-6 {{ $isUpcoming ? 'blur-[2px]' : '' }}">
                            <div class="relative shrink-0 flex justify-center sm:block">
                                @if($milestone->reward_image)
                                    <img src="{{ url('storage/' . $milestone->reward_image) }}" alt="{{ $milestone->title }}" class="h-24 w-24 sm:h-20 sm:w-20 rounded-2xl object-cover ring-2 ring-primary/10">
                                @else
                                    <div class="h-24 w-24 sm:h-20 sm:w-20 rounded-2xl bg-primary/10 flex items-center justify-center text-primary text-4xl sm:text-3xl">
                                        <i class="fas fa-gift"></i>
                                    </div>
                                @endif

                                @if($isClaimed)
                                    <div class="absolute -top-2 -right-2 h-7 w-7 rounded-full bg-success text-white flex items-center justify-center text-[12px] shadow-lg border-2 border-white">
                                        <i class="fas fa-check"></i>
                                    </div>
                                @endif
                            </div>

                            <div class="flex-1 min-w-0 text-center sm:text-left">
                                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-2">
                                    <div class="order-2 sm:order-1">
                                        <h4 class="text-sm md:text-base font-black text-mainText truncate uppercase">{{ $milestone->short_title }}</h4>
                                        <div class="flex items-center justify-center sm:justify-start gap-2 mt-0.5">
                                            <i class="far fa-calendar-alt text-[9px] text-mutedText"></i>
                                            <span class="text-[9px] font-bold text-mutedText uppercase tracking-wider">
                                                {{ $milestone->start_date ? $milestone->start_date->format('M d') : 'Start' }} - {{ $milestone->end_date ? $milestone->end_date->format('M d, Y') : 'Life' }}
                                            </span>
                                        </div>
                                    </div>
                                    <span class="shrink-0 text-sm font-black order-1 sm:order-2 {{ $isLocked ? 'text-mutedText' : 'text-primary' }}">₹{{ number_format($milestone->target_amount) }}</span>
                                </div>
                                <p class="text-[10px] sm:text-xs text-mutedText font-medium line-clamp-2 mt-2 leading-relaxed">{{ $milestone->reward_description }}</p>

                                <div class="mt-5">
                                    @if($isUpcoming)
                                        <div class="flex items-center gap-2 text-[9px] font-black text-primary/60 uppercase tracking-widest mt-2">
                                            <i class="fas fa-clock"></i> Starts in {{ $milestone->start_date->diffForHumans() }}
                                        </div>
                                    @elseif($isLocked)
                                        <div class="space-y-2">
                                            <div class="flex justify-between text-[9px] font-black uppercase tracking-widest text-mutedText">
                                                <span>Progress</span>
                                                <span>₹{{ number_format($progress) }} / ₹{{ number_format($milestone->target_amount) }}</span>
                                            </div>
                                            <div class="w-full h-2 bg-navy rounded-full overflow-hidden p-[1px]">
                                                <div class="h-full brand-gradient rounded-full shadow-[0_0_10px_rgba(var(--color-primary),0.5)] transition-all duration-1000" style="width: {{ $percent }}%"></div>
                                            </div>
                                        </div>
                                    @elseif($isUnlocked)
                                        <button onclick="claimReward({{ $milestone->id }})"
                                                class="w-full py-2.5 bg-primary text-white text-[10px] font-black uppercase tracking-widest rounded-xl hover:bg-secondary transition-all hover:scale-[1.02] active:scale-95 shadow-lg shadow-primary/20">
                                            Claim Reward
                                        </button>
                                    @else
                                        <div class="inline-flex items-center gap-2 px-3 py-1.5 rounded-full bg-success/10 text-success text-[10px] font-black uppercase tracking-widest mt-1">
                                            <i class="fas fa-check-circle"></i> Milestone Achieved
                                        </div>
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
