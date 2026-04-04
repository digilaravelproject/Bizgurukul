@extends('layouts.admin')
@section('title', 'Qualified Users - ' . $achievement->title)

@section('content')
    <div class="font-sans text-mainText min-h-screen space-y-8 animate-fade-in">
        {{-- Header --}}
        <div class="flex items-center justify-between gap-4">
            <div class="flex items-center gap-4">
                <a href="{{ route('admin.achievements.index') }}"
                   class="h-12 w-12 flex items-center justify-center rounded-2xl bg-surface border border-primary/10 text-mutedText hover:text-primary hover:border-primary/30 transition-all duration-300 shadow-xl shadow-primary/5">
                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M15 19l-7-7 7-7" />
                    </svg>
                </a>
                <div>
                    <h2 class="text-3xl font-extrabold tracking-tight text-mainText">Qualified Users</h2>
                    <p class="text-sm font-medium text-mutedText">For milestone: <span class="text-primary font-black">{{ $achievement->title }}</span></p>
                </div>
            </div>
            
            <div class="bg-surface border border-primary/10 rounded-2xl p-4 shadow-xl shadow-primary/5 flex items-center gap-6">
                <div class="text-center">
                    <p class="text-[10px] font-black uppercase tracking-widest text-mutedText">Target</p>
                    <p class="text-lg font-black text-primary">₹{{ number_format($achievement->target_amount, 0) }}</p>
                </div>
                <div class="w-px h-8 bg-primary/10"></div>
                <div class="text-center">
                    <p class="text-[10px] font-black uppercase tracking-widest text-mutedText">Unlocks</p>
                    <p class="text-lg font-black text-success">{{ $qualifiedUsers->total() }}</p>
                </div>
            </div>
        </div>

        {{-- Table --}}
        <div class="bg-surface border border-primary/10 rounded-[2.5rem] shadow-2xl shadow-primary/5 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="border-b border-primary/5 bg-primary/[0.02]">
                            <th class="px-8 py-6 text-xs font-black text-mainText uppercase tracking-widest">Student</th>
                            <th class="px-8 py-6 text-xs font-black text-mainText uppercase tracking-widest">Achievement Date</th>
                            <th class="px-8 py-6 text-xs font-black text-mainText uppercase tracking-widest">Status</th>
                            <th class="px-8 py-6 text-xs font-black text-mainText uppercase tracking-widest text-right">Contact</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary/5">
                        @forelse($qualifiedUsers as $userAchievement)
                            <tr class="hover:bg-primary/[0.01] transition-colors group">
                                <td class="px-8 py-6">
                                    <div class="flex items-center gap-4">
                                        <div class="h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary font-black text-lg border border-primary/20 group-hover:scale-110 transition-transform">
                                            {{ substr($userAchievement->user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="text-sm font-black text-mainText">{{ $userAchievement->user->name }}</div>
                                            <div class="text-[11px] font-bold text-mutedText">{{ $userAchievement->user->email }}</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <div class="text-[11px] font-black text-mainText uppercase tracking-wider">
                                        {{ $userAchievement->unlocked_at ? $userAchievement->unlocked_at->format('d M Y') : 'N/A' }}
                                    </div>
                                    <div class="text-[10px] font-bold text-mutedText">
                                        {{ $userAchievement->unlocked_at ? $userAchievement->unlocked_at->format('h:i A') : '' }}
                                    </div>
                                </td>
                                <td class="px-8 py-6">
                                    <span class="inline-flex items-center px-3 py-1 rounded-full {{ $userAchievement->status === 'claimed' ? 'bg-success/10 text-success' : 'bg-warning/10 text-warning' }} text-[10px] font-black uppercase tracking-widest">
                                        {{ $userAchievement->status }}
                                    </span>
                                </td>
                                <td class="px-8 py-6 text-right">
                                    <div class="flex justify-end gap-2">
                                        @if($userAchievement->user->phone)
                                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $userAchievement->user->phone) }}" target="_blank"
                                               class="h-10 w-10 flex items-center justify-center rounded-xl bg-success/10 text-success hover:bg-success hover:text-white transition-all shadow-lg shadow-success/10">
                                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                                                    <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893a11.821 11.821 0 00-3.48-8.413z"/>
                                                </svg>
                                            </a>
                                        @endif
                                        <a href="mailto:{{ $userAchievement->user->email }}"
                                           class="h-10 w-10 flex items-center justify-center rounded-xl bg-primary/10 text-primary hover:bg-primary hover:text-white transition-all shadow-lg shadow-primary/10">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z" />
                                            </svg>
                                        </a>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-8 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center space-y-3 opacity-40">
                                        <svg class="w-16 h-16 text-mutedText" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13.732 4c-.76-1.01-1.93-2-3.732-2s-2.972.99-3.732 2m9.464 0a4.354 4.354 0 010 3.14M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z" />
                                        </svg>
                                        <p class="text-sm font-black uppercase tracking-widest">No users qualified yet</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            @if($qualifiedUsers->hasPages())
                <div class="px-8 py-6 border-t border-primary/5 bg-primary/[0.02]">
                    {{ $qualifiedUsers->links() }}
                </div>
            @endif
        </div>
    </div>
@endsection
