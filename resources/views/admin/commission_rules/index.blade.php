@extends('layouts.admin')

@section('title', 'Commission Rules')

@section('content')
    <div class="p-6 font-sans text-mainText" x-data="{
        showModal: false,
        productType: '',
        commissionType: 'percent'
    }">
        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-mainText">Commission Rules</h1>
                <p class="text-mutedText mt-1 text-sm font-medium">Manage and prioritize affiliate commission structures.</p>
            </div>
            <button @click="showModal = true"
                class="brand-gradient text-customWhite px-6 py-3 rounded-xl font-bold shadow-lg shadow-primary/30 hover:scale-[1.02] active:scale-[0.98] transition-all flex items-center gap-2 text-sm uppercase tracking-wider">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Add New Rule
            </button>
        </div>

        {{-- Rules Table Card --}}
        <div class="bg-surface rounded-2xl shadow-sm border border-primary/10 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-primary/5 text-[11px] uppercase font-black text-primary tracking-widest border-b border-primary/5">
                        <tr>
                            <th class="px-6 py-5">Priority Scope</th>
                            <th class="px-6 py-5">Affiliate</th>
                            <th class="px-6 py-5">Product Target</th>
                            <th class="px-6 py-5">Commission</th>
                            <th class="px-6 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary/5">
                        @forelse($rules as $rule)
                            <tr class="hover:bg-primary/5 transition-colors group">
                                <td class="px-6 py-4">
                                    @if($rule->affiliate_id && $rule->product_type)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-secondary/10 text-secondary border border-secondary/20 uppercase tracking-tighter">
                                            Highest (User + Product)
                                        </span>
                                    @elseif($rule->affiliate_id)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-indigo-100 text-indigo-700 border border-indigo-200 uppercase tracking-tighter">
                                            User Specific
                                        </span>
                                    @elseif($rule->product_type)
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-primary/10 text-primary border border-primary/20 uppercase tracking-tighter">
                                            Product Specific
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-bold bg-mutedText/10 text-mutedText border border-mutedText/20 uppercase tracking-tighter">
                                            Global Default
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-2">
                                        <div class="w-8 h-8 rounded-full bg-navy/30 flex items-center justify-center text-primary font-bold text-xs border border-primary/5">
                                            {{ substr($rule->affiliate ? $rule->affiliate->name : 'A', 0, 1) }}
                                        </div>
                                        <span class="font-bold text-mainText">{{ $rule->affiliate ? $rule->affiliate->name : 'All Affiliates' }}</span>
                                    </div>
                                </td>
                                <td class="px-6 py-4">
                                    @if($rule->product_type)
                                        <div class="flex flex-col">
                                            <span class="text-[9px] font-black text-primary/60 uppercase tracking-tighter">{{ class_basename($rule->product_type) }}</span>
                                            <span class="font-bold text-mainText">{{ $rule->product ? $rule->product->title : 'Unknown Item' }}</span>
                                        </div>
                                    @else
                                        <span class="text-mutedText italic font-medium">Universal Coverage</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-black text-primary text-base">
                                    {{ number_format($rule->amount, 0) }}<span class="text-[10px] ml-0.5 text-mainText uppercase font-bold">{{ $rule->commission_type === 'percent' ? '%' : 'INR' }}</span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <form action="{{ route('admin.affiliate.rules.delete', $rule->id) }}" method="POST" onsubmit="return confirm('Permanently delete this rule?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="p-2 text-mutedText hover:text-secondary hover:bg-secondary/10 rounded-xl transition-all group">
                                            <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-20 text-center">
                                    <div class="flex flex-col items-center opacity-30">
                                        <svg class="w-16 h-16 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                                        <p class="text-lg font-bold uppercase tracking-widest">No Priority Rules</p>
                                    </div>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($rules->hasPages())
                <div class="px-6 py-4 bg-primary/5 border-t border-primary/5">
                    {{ $rules->links() }}
                </div>
            @endif
        </div>

        {{-- Create Modal (Ultra-Modern) --}}
        <div x-show="showModal"
             class="fixed inset-0 z-50 overflow-y-auto"
             style="display: none;"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100"
             x-transition:leave-end="opacity-0">

            <div class="fixed inset-0 bg-navy/60 backdrop-blur-md transition-opacity" @click="showModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-3xl bg-surface p-8 shadow-2xl transition-all w-full max-w-lg border border-primary/10"
                     x-show="showModal"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 translate-y-8 scale-95"
                     x-transition:enter-end="opacity-100 translate-y-0 scale-100">

                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-2xl font-black text-mainText tracking-tight">New Commission Rule</h3>
                        <button @click="showModal = false" class="text-mutedText hover:text-primary transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form action="{{ route('admin.affiliate.rules.store') }}" method="POST" class="space-y-6">
                        @csrf

                        {{-- Affiliate Selection --}}
                        <div>
                            <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2 ml-1">Affiliate Target</label>
                            <div class="relative">
                                <select name="affiliate_id" class="w-full bg-navy/20 border-primary/10 rounded-2xl py-3.5 text-sm font-bold text-mainText focus:ring-primary focus:border-primary appearance-none cursor-pointer">
                                    <option value="">Universal (Global Default)</option>
                                    @foreach($affiliates as $affiliate)
                                        <option value="{{ $affiliate->id }}">{{ $affiliate->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none text-primary">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Product Scope --}}
                        <div class="grid grid-cols-2 gap-4 bg-primary/5 p-4 rounded-2xl border border-primary/5">
                            <div class="col-span-1">
                                <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2 ml-1">Type</label>
                                <select name="product_type" x-model="productType" class="w-full bg-surface border-primary/10 rounded-xl py-3 text-sm font-bold text-mainText focus:ring-primary focus:border-primary">
                                    <option value="">All Items</option>
                                    <option value="course">Course</option>
                                    <option value="bundle">Bundle</option>
                                </select>
                            </div>

                            <div class="col-span-1" x-show="productType !== ''" x-cloak>
                                <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2 ml-1">Select Item</label>
                                <select name="product_id" class="w-full bg-surface border-primary/10 rounded-xl py-3 text-sm font-bold text-mainText focus:ring-primary focus:border-primary">
                                    <template x-if="productType === 'course'">
                                        <optgroup label="Courses">
                                            @foreach($courses as $course)
                                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                                            @endforeach
                                        </optgroup>
                                    </template>

                                    <template x-if="productType === 'bundle'">
                                        <optgroup label="Bundles">
                                            @foreach($bundles as $bundle)
                                                <option value="{{ $bundle->id }}">{{ $bundle->title }}</option>
                                            @endforeach
                                        </optgroup>
                                    </template>
                                </select>
                            </div>
                        </div>

                        {{-- Commission --}}
                        <div class="grid grid-cols-2 gap-6">
                            <div>
                                <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2 ml-1">Commission Type</label>
                                <select name="commission_type" x-model="commissionType" class="w-full bg-navy/20 border-primary/10 rounded-2xl py-3.5 text-sm font-bold text-mainText focus:ring-primary focus:border-primary">
                                    <option value="percent">Percentage (%)</option>
                                    <option value="fixed">Fixed (₹)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2 ml-1">Value</label>
                                <div class="relative">
                                    <input type="number" name="amount" step="0.01" required placeholder="0.00"
                                        class="w-full bg-navy/20 border-primary/10 rounded-2xl py-3.5 text-sm font-black text-primary focus:ring-primary focus:border-primary placeholder-primary/30">
                                    <div class="absolute inset-y-0 right-4 flex items-center pointer-events-none font-bold text-xs text-mutedText uppercase">
                                        <span x-text="commissionType === 'percent' ? '%' : 'INR'"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="pt-6 flex flex-col gap-3">
                            <button type="submit" class="brand-gradient w-full py-4 text-customWhite rounded-2xl text-sm font-black uppercase tracking-[2px] shadow-xl shadow-primary/20 hover:opacity-90 transition-all flex justify-center items-center gap-2">
                                Save Rule Configuration
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7l5 5m0 0l-5 5m5-5H6"></path></svg>
                            </button>
                            <button type="button" @click="showModal = false" class="w-full py-3 text-xs font-bold text-mutedText hover:text-secondary uppercase tracking-widest transition-colors">Discard Changes</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
