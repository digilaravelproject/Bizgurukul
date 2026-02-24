@extends('layouts.user.app')

@section('content')
<div class="space-y-8 font-sans text-mainText">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
        <div>
            <h1 class="text-3xl font-black tracking-tight text-mainText">Affiliate Links</h1>
            <p class="text-sm font-medium text-mutedText mt-1">Generate and share your unique referral links.</p>
        </div>

        {{-- Compact Referral Code --}}
        <div class="flex items-center gap-3 bg-surface border border-primary/10 pl-5 pr-2 py-2 rounded-2xl shadow-xl shadow-primary/5 group/code cursor-pointer relative"
            x-data="{ copied: false, code: '{{ auth()->user()->referral_code }}' }"
            @click="navigator.clipboard.writeText(code); copied = true; setTimeout(() => copied = false, 2000)">

            <div class="flex flex-col">
                <span class="text-[9px] font-black text-mutedText uppercase tracking-widest">Your Referral Code</span>
                <span class="text-xl font-black tracking-widest text-primary font-mono uppercase" x-text="code"></span>
            </div>

            <div class="w-10 h-10 rounded-xl bg-primary/5 flex items-center justify-center text-primary group-hover/code:bg-primary group-hover/code:text-white transition-all duration-300">
                <i class="fas" :class="copied ? 'fa-check' : 'fa-copy'"></i>
            </div>

            {{-- Clean Toast Feedback --}}
            <div x-show="copied" x-cloak x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-2" x-transition:enter-end="opacity-100 translate-y-0"
                class="absolute -bottom-10 left-1/2 -translate-x-1/2 px-3 py-1 bg-emerald-500 text-white text-[10px] font-black uppercase tracking-widest rounded-lg shadow-xl z-50">
                Copied!
            </div>
        </div>
    </div>

    {{-- Link Generator --}}
    <div class="bg-surface rounded-[2rem] shadow-xl border border-primary/10 p-8 relative overflow-hidden" x-data="{ type: 'general', selectedId: '', expiryOption: 'no_expiry' }">
        {{-- Background Aesthetics --}}
        <div class="absolute top-0 right-0 w-64 h-64 bg-primary/5 rounded-bl-full pointer-events-none"></div>

        <h3 class="text-xl font-black text-mainText border-b border-primary/10 pb-4 mb-6 flex items-center gap-3">
            <i class="fas fa-link text-primary"></i> Create New Link
        </h3>

        <form action="{{ route('student.affiliate-links.store') }}" method="POST" class="flex flex-col lg:flex-row gap-4 items-end">
            @csrf

            {{-- Flexible Container for Inputs --}}
            <div class="flex-1 grid grid-cols-1 md:grid-cols-12 gap-4 w-full">

                {{-- Dynamic Form Fields --}}
                <div class="md:col-span-4 lg:col-span-4">
                    <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Type</label>
                    <div class="relative group">
                        <select x-model="type" name="type" class="w-full h-12 px-4 rounded-xl bg-white text-mainText border border-primary/20 focus:border-primary focus:ring-1 focus:ring-primary outline-none appearance-none font-bold shadow-sm transition-all hover:border-primary/50 text-sm">
                            <option value="general">All Bundles (General)</option>
                            <option value="specific_bundle">Specific Bundle</option>
                            @if($canSellCourses)
                                <option value="specific_course">Specific Course</option>
                            @endif
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-mutedText pointer-events-none group-hover:text-primary transition-colors"></i>
                    </div>
                </div>

                {{-- Specific Bundle Select --}}
                <div class="md:col-span-4 lg:col-span-4" x-show="type === 'specific_bundle'" x-transition style="display: none;">
                    <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Select Bundle</label>
                    <div class="relative group">
                        <select name="target_id_bundle" :required="type === 'specific_bundle'" class="w-full h-12 px-4 rounded-xl bg-white text-mainText border border-primary/20 focus:border-primary focus:ring-1 focus:ring-primary outline-none appearance-none font-bold shadow-sm transition-all hover:border-primary/50 text-sm">
                            <option value="">-- Choose Bundle --</option>
                            @foreach($bundles as $bundle)
                                <option value="{{ $bundle->id }}">{{ $bundle->title }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-mutedText pointer-events-none group-hover:text-primary transition-colors"></i>
                    </div>
                </div>

                {{-- Specific Course Select --}}
                @if($canSellCourses)
                <div class="md:col-span-4 lg:col-span-4" x-show="type === 'specific_course'" x-transition style="display: none;">
                    <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Select Course</label>
                    <div class="relative group">
                        <select name="target_id_course" :required="type === 'specific_course'" class="w-full h-12 px-4 rounded-xl bg-white text-mainText border border-primary/20 focus:border-primary focus:ring-1 focus:ring-primary outline-none appearance-none font-bold shadow-sm transition-all hover:border-primary/50 text-sm">
                            <option value="">-- Choose Course --</option>
                            @foreach($courses as $course)
                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </select>
                        <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-mutedText pointer-events-none group-hover:text-primary transition-colors"></i>
                    </div>
                </div>
                @endif

                {{-- EXPIRY OPTION --}}
                 <div class="md:col-span-3 lg:col-span-3">
                    <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Expiry</label>
                    <div class="relative group">
                        <select x-model="expiryOption" class="w-full h-12 px-4 rounded-xl bg-white text-mainText border border-primary/20 focus:border-primary focus:ring-1 focus:ring-primary outline-none appearance-none font-bold shadow-sm transition-all hover:border-primary/50 text-sm">
                            <option value="no_expiry">Lifetime</option>
                            <option value="custom">Set Date</option>
                        </select>
                        <i class="fas fa-hourglass-half absolute right-4 top-1/2 -translate-y-1/2 text-mutedText pointer-events-none group-hover:text-primary transition-colors"></i>
                    </div>
                </div>

                {{-- CUSTOM DATE INPUT --}}
                <div class="md:col-span-3 lg:col-span-3" x-show="expiryOption === 'custom'" x-transition>
                    <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Select Date</label>
                    <input type="date" name="expiry_date" min="{{ date('Y-m-d') }}" :required="expiryOption === 'custom'"
                        class="w-full h-12 px-4 rounded-xl bg-white text-mainText border border-primary/20 focus:border-primary focus:ring-1 focus:ring-primary outline-none font-bold shadow-sm transition-all hover:border-primary/50 text-sm uppercase">
                </div>
            </div>

            {{-- Create Button (Fixed Width) --}}
            <div class="w-full lg:w-48">
                <button type="submit" class="w-full h-12 brand-gradient text-white font-black uppercase tracking-widest rounded-xl shadow-lg hover:shadow-primary/25 hover:-translate-y-0.5 transition-all active:scale-95 flex items-center justify-center gap-2 text-sm">
                    <i class="fas fa-magic"></i> Generate
                </button>
            </div>
        </form>
    </div>

    {{-- Existing Links Table --}}
    <div class="bg-surface rounded-[2rem] shadow-xl border border-primary/10 overflow-hidden">
        <div class="p-6 border-b border-primary/10 flex items-center gap-3">
             <div class="w-3 h-3 rounded-full bg-red-500"></div>
             <div class="w-3 h-3 rounded-full bg-yellow-500"></div>
             <div class="w-3 h-3 rounded-full bg-green-500"></div>
             <span class="ml-2 text-xs font-bold text-mutedText uppercase tracking-widest">Active Links</span>
        </div>

        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-navy/5 border-b border-primary/10 text-xs uppercase text-mutedText font-black tracking-wider">
                        <th class="px-8 py-5">Target Product</th>
                        <th class="px-8 py-5">Affiliate Link</th>
                        <th class="px-8 py-5 text-center">Clicks</th>
                        <th class="px-8 py-5 text-center">Expiry</th>
                        <th class="px-8 py-5 text-center">Status</th>
                        <th class="px-8 py-5 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-semibold text-mainText divide-y divide-primary/5">
                    @forelse($links as $link)
                    <tr class="hover:bg-navy/30 transition group">
                        <td class="px-8 py-5">
                             @if($link->target_type === 'general')
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-blue-500/10 flex items-center justify-center text-blue-500"><i class="fas fa-layer-group"></i></div>
                                    <span class="text-mainText">All Bundles</span>
                                </div>
                            @elseif($link->target_type === 'specific_course')
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-purple-500/10 flex items-center justify-center text-purple-500"><i class="fas fa-graduation-cap"></i></div>
                                    <span class="text-mainText">{{ $link->course->title ?? 'Deleted Course' }}</span>
                                </div>
                            @elseif($link->target_type === 'specific_bundle')
                                <div class="flex items-center gap-3">
                                    <div class="w-10 h-10 rounded-lg bg-emerald-500/10 flex items-center justify-center text-emerald-500"><i class="fas fa-box-open"></i></div>
                                    <span class="text-mainText">{{ $link->bundle->title ?? 'Deleted Bundle' }}</span>
                                </div>
                            @endif
                        </td>
                        <td class="px-8 py-5">
                            <div class="flex items-center gap-2 bg-navy px-3 py-2 rounded-lg border border-primary/10 max-w-fit relative" x-data="{ copied: false }">
                                <span class="text-xs font-mono text-mutedText truncate max-w-[200px]">{{ route('affiliate.redirect', $link->slug) }}</span>
                                <button @click="navigator.clipboard.writeText('{{ route('affiliate.redirect', $link->slug) }}'); copied = true; setTimeout(() => copied = false, 2000)"
                                    class="text-primary hover:text-white transition" title="Copy">
                                    <i class="fas fa-copy"></i>
                                </button>

                                {{-- Inline Toast --}}
                                <div x-show="copied"
                                    x-transition:enter="transition ease-out duration-300"
                                    x-transition:enter-start="opacity-0 -translate-y-2"
                                    x-transition:enter-end="opacity-100 translate-y-0"
                                    x-transition:leave="transition ease-in duration-300"
                                    x-transition:leave-start="opacity-100 translate-y-0"
                                    x-transition:leave-end="opacity-0 -translate-y-2"
                                    class="absolute -top-10 left-1/2 -translate-x-1/2 px-3 py-1 bg-primary text-white text-[10px] font-black uppercase tracking-widest rounded-lg shadow-xl z-50 flex items-center gap-2 pointer-events-none">
                                    <i class="fas fa-check-circle"></i> Copied
                                    <div class="absolute top-full left-1/2 -translate-x-1/2 border-[6px] border-transparent border-t-primary"></div>
                                </div>
                            </div>
                        </td>
                        <td class="px-8 py-5 text-center">
                            <span class="bg-navy px-3 py-1 rounded-full text-xs font-bold text-mainText border border-primary/10 shadow-sm">{{ $link->clicks }}</span>
                        </td>
                        <td class="px-8 py-5 text-center">
                             @if($link->expires_at)
                                <span class="bg-navy px-3 py-1 rounded-lg text-xs font-bold text-mutedText border border-primary/10">
                                    {{ $link->expires_at->format('d M, Y') }}
                                </span>
                             @else
                                <span class="bg-navy px-3 py-1 rounded-lg text-[10px] font-bold text-emerald-500 border border-emerald-500/20 uppercase tracking-widest">
                                    Lifetime
                                </span>
                             @endif
                        </td>
                        <td class="px-8 py-5 text-center">
                             @if($link->is_deleted)
                                <span class="text-red-500 text-[10px] font-black uppercase tracking-widest bg-red-500/10 px-2 py-1 rounded-md">Inactive</span>
                             @elseif($link->expires_at && $link->expires_at->isPast())
                                <span class="text-red-500 text-[10px] font-black uppercase tracking-widest bg-red-500/10 px-2 py-1 rounded-md">Expired</span>
                             @else
                                <span class="text-emerald-500 text-[10px] font-black uppercase tracking-widest bg-emerald-500/10 px-2 py-1 rounded-md">Active</span>
                             @endif
                        </td>
                        <td class="px-8 py-5 text-right">
                             <form action="{{ route('student.affiliate-links.destroy', $link->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this link?');" class="inline-block">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="w-8 h-8 rounded-full flex items-center justify-center bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition-all shadow-sm">
                                    <i class="fas fa-trash-alt text-xs"></i>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-8 py-16 text-center">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <i class="fas fa-link text-4xl text-mutedText/30"></i>
                                <p class="text-mutedText font-medium">No affiliate links created yet.</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
