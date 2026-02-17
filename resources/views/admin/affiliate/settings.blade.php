@extends('layouts.admin')

@section('title', 'Affiliate Settings')

@section('content')
<div class="space-y-10 font-sans text-mainText max-w-5xl mx-auto pb-20">

    {{-- Header --}}
    <div class="flex justify-between items-end">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-mainText">Affiliate & Commission Settings</h1>
            <p class="text-mutedText mt-1 text-sm font-medium">Manage global configurations and commission structures.</p>
        </div>
        <div class="h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary border border-primary/20 shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
        </div>
    </div>

    {{-- System Settings Card --}}
    <div class="bg-surface rounded-3xl shadow-xl shadow-primary/5 border border-primary/10 overflow-hidden">
        <div class="p-6 border-b border-primary/5 bg-navy/5">
            <h3 class="text-sm font-black text-mainText uppercase tracking-wider flex items-center gap-2">
                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                System Controls
            </h3>
        </div>
        <div class="p-8">
            <form action="{{ route('admin.affiliate.settings.update') }}" method="POST" class="space-y-6">
                @csrf

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="p-5 border border-primary/10 rounded-2xl bg-white hover:border-primary/30 transition-all group">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-base font-bold text-mainText group-hover:text-primary transition-colors">Affiliate Module</h3>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="affiliate_module_enabled" value="1" class="sr-only peer" {{ $affiliateEnabled ? 'checked' : '' }}>
                                <div class="w-10 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary transition-colors"></div>
                            </label>
                        </div>
                        <p class="text-xs text-mutedText leading-relaxed">Master switch. If disabled, all affiliate tracking and commissions are paused.</p>
                    </div>

                    <div class="p-5 border border-primary/10 rounded-2xl bg-white hover:border-primary/30 transition-all group">
                        <div class="flex justify-between items-start mb-2">
                            <h3 class="text-base font-bold text-mainText group-hover:text-primary transition-colors">Course Selling</h3>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="course_selling_enabled" value="1" class="sr-only peer" {{ $courseSellingEnabled ? 'checked' : '' }}>
                                <div class="w-10 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary transition-colors"></div>
                            </label>
                        </div>
                        <p class="text-xs text-mutedText leading-relaxed">Allow affiliates to sell individual courses by default. Can be overridden per user.</p>
                    </div>
                </div>

                <div class="flex justify-end pt-2">
                    <button type="submit" class="bg-primary text-white px-8 py-3 rounded-xl text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:bg-secondary hover:shadow-xl hover:-translate-y-0.5 transition-all">
                        Save System Settings
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Global Commission Rules --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left: Add Rule Form --}}
        <div class="lg:col-span-1">
            <div class="bg-surface rounded-3xl border border-primary/10 shadow-xl shadow-primary/5 overflow-hidden sticky top-6">
                <div class="p-6 border-b border-primary/5 bg-navy/30">
                    <h3 class="text-sm font-black text-mainText uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Add Global Rule
                    </h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.affiliate.rules.store') }}" method="POST" class="space-y-5">
                        @csrf
                        {{-- Hidden Affiliate ID for Global --}}
                        <input type="hidden" name="affiliate_id" value="">

                        <div x-data="{ scope: 'all' }">
                            <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2">Scope</label>
                            <select name="product_type" x-model="scope" class="w-full text-xs font-bold bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-0 mb-3 text-mainText">
                                <option value="">All Products</option>
                                <option value="course">Specific Course</option>
                                <option value="bundle">Specific Bundle</option>
                            </select>

                            <div x-show="scope === 'course'" class="animate-fade-in" style="display: none;">
                                <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2">Select Course</label>
                                <select name="product_id" class="w-full text-xs font-bold bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-0 mb-3 text-mainText">
                                    <option value="">-- Select Course --</option>
                                    @foreach($courses as $c) <option value="{{ $c->id }}">{{ $c->title }}</option> @endforeach
                                </select>
                            </div>
                             <div x-show="scope === 'bundle'" class="animate-fade-in" style="display: none;">
                                <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2">Select Bundle</label>
                                <select name="product_id" class="w-full text-xs font-bold bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-0 mb-3 text-mainText">
                                    <option value="">-- Select Bundle --</option>
                                    @foreach($bundles as $b) <option value="{{ $b->id }}">{{ $b->title }}</option> @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2">Type</label>
                                <select name="commission_type" class="w-full text-xs font-bold bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-0 text-mainText">
                                    <option value="percent">Percent %</option>
                                    <option value="fixed">Fixed ₹</option>
                                </select>
                            </div>
                             <div>
                                <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2">Amount</label>
                                <input type="number" step="0.01" name="amount" placeholder="0.00" class="w-full text-xs font-bold bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-0 text-mainText">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3.5 bg-secondary hover:bg-navy text-customWhite rounded-xl shadow-lg transition-all font-black text-[10px] uppercase tracking-widest mt-2">
                            Create Global Rule
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Right: Rules List --}}
        <div class="lg:col-span-2">
            <div class="bg-surface rounded-3xl border border-primary/10 shadow-xl shadow-primary/5 overflow-hidden">
                <div class="p-6 border-b border-primary/5 bg-navy/30 flex justify-between items-center">
                    <h3 class="text-sm font-black text-mainText uppercase tracking-wider">Active Global Rules</h3>
                    <span class="bg-primary/10 text-primary px-2 py-1 rounded-lg text-[10px] font-black">{{ $globalRules->count() }} Rules</span>
                </div>

                <div class="divide-y divide-primary/5">
                    @forelse($globalRules as $rule)
                    <div class="p-5 flex items-center justify-between hover:bg-navy/5 transition-colors group">
                        <div class="flex items-center gap-4">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-primary/10 to-primary/5 flex items-center justify-center text-primary border border-primary/10">
                                @if(str_contains($rule->product_type, 'Course'))
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path></svg>
                                @elseif(str_contains($rule->product_type, 'Bundle'))
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                @else
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                @endif
                            </div>
                            <div>
                                <h4 class="text-xs font-black text-mainText uppercase tracking-wide">
                                    {{ $rule->product->title ?? 'All Products' }}
                                </h4>
                                <span class="text-[10px] text-mutedText font-bold">
                                    {{ $rule->product_type ? (str_contains($rule->product_type, 'Bundle') ? 'Bundle Specific' : 'Course Specific') : 'Global Scope' }}
                                </span>
                            </div>
                        </div>

                        <div class="flex items-center gap-6">
                            <span class="px-3 py-1 rounded-lg bg-green-100 text-green-700 text-xs font-black">
                                {{ number_format($rule->amount, 2) }} {{ $rule->commission_type == 'percent' ? '%' : '₹' }}
                            </span>

                            <form action="{{ route('admin.affiliate.rules.delete', $rule->id) }}" method="POST" onsubmit="return confirm('Delete this global rule?');">
                                @csrf @method('DELETE')
                                <button class="text-mutedText hover:text-red-500 transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </div>
                    </div>
                    @empty
                    <div class="p-8 text-center">
                        <div class="inline-flex items-center justify-center h-16 w-16 rounded-full bg-navy/5 text-mutedText mb-4">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                        </div>
                        <h3 class="text-sm font-bold text-mainText">No active global rules</h3>
                        <p class="text-xs text-mutedText mt-1">Default system commission will apply.</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

    </div>
</div>
@endsection
