@extends('layouts.user.app')
@section('title', 'Commission Structure')

@section('content')
<div class="space-y-6 font-sans text-mainText pb-8">
    {{-- Header: Adjusted for better readability --}}
    <div class="flex items-center gap-4 animate-fade-in-down">
        <a href="{{ route('student.affiliate.dashboard') }}" class="p-2.5 rounded-xl bg-surface border border-primary/10 hover:bg-primary/5 text-mutedText transition-colors flex items-center justify-center">
            <i class="fas fa-arrow-left text-base"></i>
        </a>
        <div>
            <h1 class="text-2xl md:text-3xl font-black tracking-tight text-mainText">Commission Structure</h1>
            <p class="text-sm md:text-base font-medium text-mutedText mt-1">Earning potential based on your bundle level.</p>
        </div>
    </div>

    {{-- Info Card: Readable Text --}}
    <div class="bg-slate-50 rounded-xl p-5 border border-slate-200 relative overflow-hidden animate-fade-in-up">
        <div class="relative z-10 flex gap-4 items-start md:items-center">
            <div class="flex w-12 h-12 rounded-xl bg-primary/10 items-center justify-center text-primary text-xl flex-shrink-0 mt-1 md:mt-0">
                <i class="fas fa-info-circle"></i>
            </div>
            <div class="text-sm md:text-base">
                <h3 class="font-bold text-slate-900 mb-1 text-base md:text-lg">How it works</h3>
                <p class="text-slate-600 leading-relaxed">
                    Earnings are <span class="text-rose-600 font-bold">Capped</span> if you sell a bundle higher than the one you own.
                    Upgrade to unlock <span class="text-emerald-600 font-bold">Full Commission</span>.
                </p>
            </div>
        </div>
    </div>

    <div class="animate-fade-in-up" style="animation-delay: 0.1s;">

        {{-- ========================================== --}}
        {{-- MOBILE VIEW: CARDS (Visible only on small screens) --}}
        {{-- ========================================== --}}
        <div class="block md:hidden space-y-5">
            @foreach($bundles as $ownedBundle)
                @php
                    $isCurrentUserRow = ($userHighestBundleId == $ownedBundle->id);
                @endphp
                <div class="bg-white rounded-xl shadow-sm border {{ $isCurrentUserRow ? 'border-primary ring-1 ring-primary/20' : 'border-slate-200' }} overflow-hidden">

                    {{-- Card Header: What You Own --}}
                    <div class="bg-slate-50 px-5 py-4 border-b border-slate-100 flex justify-between items-center {{ $isCurrentUserRow ? 'bg-primary/5' : '' }}">
                        <div class="flex flex-col">
                            <span class="text-xs text-slate-500 font-bold uppercase tracking-wider mb-1">If you own</span>
                            <span class="text-lg font-black text-slate-900">{{ $ownedBundle->title }}</span>
                        </div>
                        @if($isCurrentUserRow)
                            <span class="text-xs font-bold uppercase bg-primary text-white px-3 py-1.5 rounded-md shadow-sm">
                                Your Level
                            </span>
                        @endif
                    </div>

                    {{-- Card Body: What You Sell --}}
                    <div class="p-5 space-y-4">
                        @foreach($bundles as $soldBundle)
                            @php
                                $cellData = $matrix[$ownedBundle->id][$soldBundle->id];
                            @endphp
                            <div class="flex justify-between items-center pb-4 border-b border-slate-100 last:border-0 last:pb-0">
                                <div class="flex flex-col">
                                    <span class="text-xs text-slate-500 font-medium mb-0.5">Sell</span>
                                    <span class="text-sm font-bold text-slate-800">{{ $soldBundle->title }}</span>
                                </div>
                                <div class="flex flex-col items-end">
                                    <span class="text-base font-black text-slate-900">{{ $cellData['formatted_amount'] }}</span>
                                    @if($cellData['status'] == 'capped')
                                        <span class="text-xs font-bold text-rose-500 flex items-center gap-1.5 mt-1">
                                            <i class="fas fa-lock text-[10px]"></i> CAPPED
                                        </span>
                                    @else
                                        <span class="text-xs font-bold text-emerald-500 flex items-center gap-1.5 mt-1">
                                            <i class="fas fa-check-circle text-[10px]"></i> FULL
                                        </span>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        {{-- ========================================== --}}
        {{-- DESKTOP VIEW: TABLE (Hidden on small screens) --}}
        {{-- ========================================== --}}
        <div class="hidden md:block bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse table-auto">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200">
                            <th class="p-4 text-xs font-black text-slate-500 uppercase tracking-widest sticky left-0 z-10 bg-slate-50 border-r border-slate-200">
                                If You Own ↓
                            </th>
                            @foreach($bundles as $bundle)
                                <th class="p-4 text-center min-w-[160px] border-r border-slate-100 last:border-r-0">
                                    <div class="text-sm font-black text-slate-900 uppercase italic mb-1">{{ $bundle->title }}</div>
                                    <div class="text-xs text-primary font-bold">
                                        Max: {{ $bundle->formatted_commission }}
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
                            <tr class="transition-colors {{ $isCurrentUserRow ? 'bg-primary/[0.03]' : 'hover:bg-slate-50' }}">
                                <td class="p-4 whitespace-nowrap sticky left-0 z-10 border-r border-slate-200 bg-inherit">
                                    <div class="flex items-center gap-3">
                                        <span class="text-sm {{ $isCurrentUserRow ? 'font-black text-primary' : 'font-bold text-slate-800' }}">
                                            {{ $ownedBundle->title }}
                                        </span>
                                        @if($isCurrentUserRow)
                                            <span class="text-[10px] font-bold uppercase bg-primary text-white px-2 py-1 rounded">
                                                YOU
                                            </span>
                                        @endif
                                    </div>
                                </td>

                                @foreach($bundles as $soldBundle)
                                    @php
                                        $cellData = $matrix[$ownedBundle->id][$soldBundle->id];
                                    @endphp
                                    <td class="p-4 text-center border-r border-slate-50 last:border-r-0">
                                        <div class="flex flex-col items-center">
                                            <span class="text-base {{ $isCurrentUserRow ? 'font-black text-slate-900' : 'font-bold text-slate-800' }}">
                                                {{ $cellData['formatted_amount'] }}
                                            </span>

                                            @if($cellData['status'] == 'capped')
                                                <span class="text-xs font-bold text-rose-500 flex items-center gap-1 mt-1">
                                                    <i class="fas fa-lock text-[10px]"></i> CAPPED
                                                </span>
                                            @else
                                                <span class="text-xs font-bold text-emerald-500 flex items-center gap-1 mt-1">
                                                    <i class="fas fa-check-circle text-[10px]"></i> FULL
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
