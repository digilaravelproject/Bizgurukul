@extends('layouts.user.app')
@section('title', 'Commission Structure')

@section('content')
<div class="space-y-8 font-sans text-mainText pb-12">
    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 animate-fade-in-down">
        <div>
            <div class="flex items-center gap-3 mb-2">
                <a href="{{ route('student.affiliate.dashboard') }}" class="p-2 rounded-xl bg-surface border border-primary/10 hover:bg-primary/5 text-mutedText transition-colors">
                    <i class="fas fa-arrow-left"></i>
                </a>
                <h1 class="text-3xl font-black tracking-tight text-mainText">Commission Structure</h1>
            </div>
            <p class="text-mutedText text-base font-medium ml-12">Understand your earning potential at every level.</p>
        </div>
    </div>

    {{-- Info Card --}}
    <div class="bg-navy rounded-[2rem] p-8 border border-primary/20 relative overflow-hidden animate-fade-in-up">
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary/10 rounded-full blur-3xl pointer-events-none"></div>
        <div class="relative z-10 flex gap-6 items-start">
            <div class="hidden md:flex w-14 h-14 rounded-2xl bg-white/10 items-center justify-center text-customWhite text-2xl flex-shrink-0">
                <i class="fas fa-info-circle"></i>
            </div>
            <div>
                <h3 class="text-xl font-bold text-customWhite mb-2">How Commission Works</h3>
                <p class="text-gray-300 leading-relaxed max-w-2xl">
                    Your commission is determined by the bundle <strong>YOU own</strong>.
                    <br>
                    <ul class="list-disc list-inside mt-2 space-y-1 text-sm text-gray-400">
                        <li>If you own a <strong>Higher Bundle</strong> and sell a Lower Bundle, you get the <span class="text-white font-bold">Full Commission</span> of the sold bundle.</li>
                        <li>If you own a <strong>Lower Bundle</strong> and sell a Higher Bundle, your commission is <span class="text-amber-400 font-bold">Capped</span> at your own bundle's commission limit.</li>
                    </ul>
                </p>
            </div>
        </div>
    </div>

    {{-- Commission Matrix Table --}}
    <div class="animate-fade-in-up" style="animation-delay: 0.1s;">
        <div class="bg-white rounded-2xl shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] border border-slate-100 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100">
                            <th class="p-6 text-sm font-bold text-slate-800 uppercase tracking-wider whitespace-nowrap bg-slate-50/80 backdrop-blur-sm sticky left-0 z-10 border-r border-slate-100">
                                If You Own &darr;
                            </th>
                            @foreach($bundles as $bundle)
                                <th class="p-6 text-center min-w-[200px]">
                                    <div class="text-sm font-bold text-slate-800 mb-1">Sell {{ $bundle->title }}</div>
                                    <div class="text-xs text-primary font-bold bg-primary/10 inline-block px-2 py-1 rounded-md">
                                        ({{ $bundle->formatted_commission }} Commission)
                                    </div>
                                </th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @foreach($bundles as $ownedBundle)
                            @php
                                $isCurrentUserRow = ($userHighestBundleId == $ownedBundle->id);
                            @endphp
                            <tr class="transition-colors hover:bg-slate-50/50 {{ $isCurrentUserRow ? 'bg-gradient-to-r from-primary/5 to-transparent' : '' }}">
                                <td class="p-6 text-sm whitespace-nowrap bg-white sticky left-0 z-10 border-r border-slate-100 {{ $isCurrentUserRow ? 'bg-primary/[0.02]' : '' }}">
                                    <div class="flex items-center gap-3">
                                        @if($isCurrentUserRow)
                                            <div class="w-2 h-2 rounded-full bg-primary animate-pulse"></div>
                                        @endif
                                        <span class="{{ $isCurrentUserRow ? 'font-black text-primary' : 'font-bold text-slate-700' }}">
                                            {{ $ownedBundle->title }}
                                        </span>
                                        @if($isCurrentUserRow)
                                            <span class="text-[9px] font-bold uppercase tracking-wider bg-primary text-white px-2 py-0.5 rounded-full ml-2">
                                                Your Level
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                @foreach($bundles as $soldBundle)
                                    @php
                                        $cellData = $matrix[$ownedBundle->id][$soldBundle->id];
                                    @endphp
                                    <td class="p-6 text-center">
                                        <div class="flex flex-col items-center gap-1.5">
                                            <span class="text-lg {{ $isCurrentUserRow ? 'font-black text-slate-900' : 'font-bold text-slate-700' }}">
                                                {{ $cellData['formatted_amount'] }}
                                            </span>

                                            @if($cellData['status'] == 'capped')
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-rose-600 bg-rose-50 px-2.5 py-1 rounded-md border border-rose-100/50">
                                                    (Capped)
                                                </span>
                                            @else
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-100/50">
                                                    Full
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection
