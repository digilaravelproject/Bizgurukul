@extends('layouts.admin')

@section('title', 'Permission for ' . $user->name)

@section('content')
<div class="max-w-5xl mx-auto space-y-8 animate-fade-in pb-20">

    {{-- Breadcrumb --}}
    <nav class="flex items-center gap-2 text-xs font-bold uppercase tracking-widest text-mutedText">
        <a href="{{ route('admin.affiliate.users.index') }}" class="hover:text-primary transition-colors">Affiliate Users</a>
        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
        <span class="text-mainText">Edit Permissions</span>
    </nav>

    {{-- Header --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-3xl font-extrabold text-mainText tracking-tight">User Permissions</h1>
            <p class="text-mutedText mt-1 text-sm font-medium">Fine-tune affiliate capabilities for <span class="text-primary font-bold">{{ $user->name }}</span>.</p>
        </div>
        <div class="h-12 w-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary border border-primary/20 shadow-inner">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"></path></svg>
        </div>
    </div>

    {{-- Alerts --}}
    @if(session('success'))
        <div class="bg-green-500/10 border border-green-500/20 text-green-600 px-4 py-3 rounded-xl flex items-center gap-2 font-bold animate-fade-in-down">
            <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
            {{ session('success') }}
        </div>
    @endif

    @if($errors->any())
        <div class="bg-red-500/10 border border-red-500/20 text-red-600 px-4 py-3 rounded-xl flex flex-col gap-1 font-bold animate-fade-in-down">
            <span>Please fix the following errors:</span>
            <ul class="list-disc list-inside text-xs font-medium">
                @foreach($errors->all() as $error) <li>{{ $error }}</li> @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left Column: Settings Form --}}
        <div class="lg:col-span-2 space-y-8">
             <form action="{{ route('admin.affiliate.users.update', $user->id) }}" method="POST" class="space-y-6">
                @csrf
                @method('PUT')

                {{-- Basic Permissions Card --}}
                <div class="bg-surface rounded-3xl border border-primary/10 shadow-xl shadow-primary/5 overflow-hidden">
                    <div class="p-8 space-y-8">

                        {{-- Course Selling Status --}}
                        <div class="grid grid-cols-1 gap-4">
                            <div>
                                <label class="text-sm font-black uppercase tracking-wider text-mainText">Course Selling Access</label>
                                <p class="text-xs text-mutedText mt-1 leading-relaxed">Overrides the global setting for this specific user.</p>
                            </div>
                            <div>
                                <div class="grid grid-cols-3 gap-3">
                                    @php
                                        $currentStatus = $user->affiliateSettings->can_sell_courses ?? null;
                                    @endphp
                                    <label class="relative group cursor-pointer">
                                        <input type="radio" name="can_sell_courses" value="1" {{ $currentStatus === true ? 'checked' : '' }} class="peer hidden">
                                        <div class="h-12 rounded-xl border-2 border-primary/10 bg-primary/5 flex items-center justify-center text-xs font-black uppercase tracking-widest text-mutedText transition-all peer-checked:bg-primary peer-checked:text-white peer-checked:border-primary peer-checked:shadow-lg hover:border-primary/30">
                                            Allow
                                        </div>
                                    </label>

                                    <label class="relative group cursor-pointer">
                                        <input type="radio" name="can_sell_courses" value="0" {{ $currentStatus === false ? 'checked' : '' }} class="peer hidden">
                                        <div class="h-12 rounded-xl border-2 border-primary/10 bg-primary/5 flex items-center justify-center text-xs font-black uppercase tracking-widest text-mutedText transition-all peer-checked:bg-secondary peer-checked:text-white peer-checked:border-secondary peer-checked:shadow-lg hover:border-secondary/30">
                                            Deny
                                        </div>
                                    </label>

                                    <label class="relative group cursor-pointer">
                                        <input type="radio" name="can_sell_courses" value="" {{ is_null($currentStatus) ? 'checked' : '' }} class="peer hidden">
                                        <div class="h-12 rounded-xl border-2 border-primary/10 bg-primary/5 flex items-center justify-center text-[10px] md:text-xs font-black uppercase tracking-widest text-mutedText transition-all peer-checked:bg-navy peer-checked:text-white peer-checked:border-navy peer-checked:shadow-lg hover:border-navy/30">
                                            Default
                                        </div>
                                    </label>
                                </div>
                            </div>
                        </div>

                        <hr class="border-primary/5">

                        {{-- Bundle Restrictions Card --}}
                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <div>
                                    <label class="text-sm font-black uppercase tracking-wider text-mainText">Bundle Restrictions</label>
                                    <p class="text-xs text-mutedText mt-1">Select bundles this user is allowed to sell. No selection = All Allowed.</p>
                                </div>
                            </div>

                            <div class="max-h-60 overflow-y-auto pr-2 custom-scrollbar">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-3">
                                    @php
                                        $allowedIds = $user->affiliateSettings->allowed_bundle_ids ?? [];
                                        if(!is_array($allowedIds)) $allowedIds = json_decode($allowedIds, true) ?? [];
                                    @endphp

                                    @foreach($bundles as $bundle)
                                    <label class="relative group cursor-pointer">
                                        <input type="checkbox" name="allowed_bundle_ids[]" value="{{ $bundle->id }}" {{ in_array($bundle->id, $allowedIds) ? 'checked' : '' }} class="peer hidden">
                                        <div class="p-3 rounded-xl border border-primary/10 group-hover:border-primary/30 transition-all flex items-center gap-3 peer-checked:border-primary peer-checked:bg-primary/5 bg-surface">
                                            <div class="h-8 w-8 flex-shrink-0 rounded-md overflow-hidden bg-primary/10">
                                                @if($bundle->thumbnail_url)
                                                    <img src="{{ $bundle->thumbnail_url }}" class="h-full w-full object-cover">
                                                @endif
                                            </div>
                                            <div class="flex-1">
                                                <h4 class="text-[11px] font-black text-mainText leading-tight">{{ $bundle->title }}</h4>
                                            </div>
                                            <div class="h-5 w-5 rounded-full border border-primary/20 flex items-center justify-center transition-all peer-checked:bg-primary peer-checked:border-primary">
                                                <svg class="w-3 h-3 text-white scale-0 peer-checked:scale-100 transition-transform" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                            </div>
                                        </div>
                                    </label>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                    </div>

                    {{-- Form Actions --}}
                     <div class="px-8 py-6 bg-navy/5 border-t border-primary/5 flex items-center justify-end gap-4">
                        <a href="{{ route('admin.affiliate.users.index') }}" class="text-xs font-black uppercase tracking-widest text-mutedText hover:text-mainText transition-colors">Cancel</a>
                        <button type="submit" class="brand-gradient px-8 py-3 rounded-xl text-customWhite text-xs font-black uppercase tracking-widest shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">
                            Save Settings
                        </button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Right Column: Specific Commission Rules --}}
        <div class="lg:col-span-1 space-y-6">

            {{-- Add New Rule Card --}}
            <div class="bg-surface rounded-3xl border border-primary/10 shadow-xl shadow-primary/5 overflow-hidden">
                <div class="p-6 border-b border-primary/5 bg-navy/30">
                     <h3 class="text-sm font-black text-mainText uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Add Custom Commission
                    </h3>
                </div>
                <div class="p-6">
                    <form action="{{ route('admin.affiliate.users.rules.store', $user->id) }}" method="POST" class="space-y-4">
                        @csrf
                        {{-- Product Scope --}}
                        <div x-data="{ scope: 'all' }">
                            <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2">Scope</label>
                            <select name="product_type" x-model="scope" class="w-full text-xs font-bold bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-0 mb-3">
                                <option value="">All Products</option>
                                <option value="course">Specific Course</option>
                                <option value="bundle">Specific Bundle</option>
                            </select>

                            {{-- Dynamic Selects --}}
                            <div x-show="scope === 'course'" class="animate-fade-in">
                                <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2">Select Course</label>
                                <select name="product_id" class="w-full text-xs font-bold bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-0 mb-3">
                                    <option value="">-- Select Course --</option>
                                    @foreach($courses as $c) <option value="{{ $c->id }}">{{ $c->title }}</option> @endforeach
                                </select>
                            </div>
                             <div x-show="scope === 'bundle'" class="animate-fade-in">
                                <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2">Select Bundle</label>
                                <select name="product_id" class="w-full text-xs font-bold bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-0 mb-3">
                                    <option value="">-- Select Bundle --</option>
                                    @foreach($allBundles as $b) <option value="{{ $b->id }}">{{ $b->title }}</option> @endforeach
                                </select>
                            </div>
                        </div>

                        <div class="grid grid-cols-2 gap-3">
                            <div>
                                <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2">Type</label>
                                <select name="commission_type" class="w-full text-xs font-bold bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-0">
                                    <option value="percent">Percent %</option>
                                    <option value="fixed">Fixed ₹</option>
                                </select>
                            </div>
                             <div>
                                <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2">Amount</label>
                                <input type="number" step="0.01" name="amount" placeholder="0.00" class="w-full text-xs font-bold bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-0">
                            </div>
                        </div>

                        <button type="submit" class="w-full py-3 bg-secondary hover:bg-navy text-customWhite rounded-xl shadow-lg transition-all font-black text-[10px] uppercase tracking-widest">
                            Add Rule
                        </button>
                    </form>
                </div>
            </div>

            {{-- Existing Rules List --}}
            <div class="space-y-3">
                <h4 class="text-xs font-black uppercase text-mutedText tracking-widest ml-1">Active Rules</h4>
                @forelse($user->commissionRules as $rule)
                <div class="bg-surface rounded-2xl border border-primary/10 p-4 flex justify-between items-center shadow-sm group hover:border-primary/30 transition-all">
                    <div>
                        <div class="flex items-center gap-2 mb-1">
                            @if($rule->product_type)
                                <span class="bg-navy/10 text-navy px-1.5 py-0.5 rounded text-[9px] font-black uppercase">
                                    {{ str_contains($rule->product_type, 'Bundle') ? 'Bundle' : 'Course' }}
                                </span>
                                <span class="text-xs font-bold text-mainText line-clamp-1 w-24" title="{{ $rule->product->title ?? '' }}">
                                    {{ $rule->product->title ?? 'Specific Item' }}
                                </span>
                            @else
                                <span class="bg-green-100 text-green-700 px-1.5 py-0.5 rounded text-[9px] font-black uppercase">Global</span>
                            @endif
                        </div>
                        <div class="text-sm font-black text-primary">
                            {{ number_format($rule->amount, 2) }} {{ $rule->commission_type == 'percent' ? '%' : '₹' }}
                        </div>
                    </div>

                    <form action="{{ route('admin.affiliate.users.rules.delete', $rule->id) }}" method="POST" onsubmit="return confirm('Delete this rule?');">
                        @csrf @method('DELETE')
                        <button class="h-8 w-8 rounded-full flex items-center justify-center text-mutedText hover:text-secondary hover:bg-secondary/10 transition-colors">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        </button>
                    </form>
                </div>
                @empty
                    <div class="text-center py-6 border-2 border-dashed border-primary/10 rounded-2xl">
                        <p class="text-[10px] font-bold text-mutedText uppercase tracking-wider">No specific rules</p>
                    </div>
                @endforelse
            </div>

        </div>

    </div>
</div>

<style>
    /* Custom Scrollbar for bundle list */
    .custom-scrollbar::-webkit-scrollbar { width: 4px; }
    .custom-scrollbar::-webkit-scrollbar-track { background: rgba(0,0,0,0.05); }
    .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(0,0,0,0.2); border-radius: 10px; }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,0.4); }
</style>
@endsection
