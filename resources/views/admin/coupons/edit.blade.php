@extends('layouts.admin')

@section('title', isset($coupon) ? 'Edit Coupon' : 'Create Coupon')

@section('content')
    <div class="max-w-5xl mx-auto py-8">
        {{-- Header Section --}}
        <div class="flex items-center justify-between mb-8 px-4">
            <div>
                <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">
                    {{ isset($coupon) ? 'Edit Coupon' : 'Generate New Coupon' }}
                </h2>
                <p class="text-[10px] text-slate-400 font-bold uppercase tracking-widest mt-1">Setup discounts for your LMS
                    items</p>
            </div>
            <a href="{{ route('admin.coupons.index') }}"
                class="text-[10px] font-black text-slate-400 hover:text-[#0777be] uppercase tracking-widest transition-all">
                ← Back to List
            </a>
        </div>

        {{-- Form Container --}}
        <form action="{{ route('admin.coupons.store') }}" method="POST"
            class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-8">
            @csrf
            @if (isset($coupon))
                <input type="hidden" name="id" value="{{ $coupon->id }}">
            @endif

            {{-- Row 1: Code & Target --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-700 uppercase ml-1">Coupon Code</label>
                    <input type="text" name="code" value="{{ old('code', $coupon->code ?? '') }}" required
                        class="w-full rounded-2xl border-slate-200 bg-slate-50 px-6 py-4 text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-[#0777be]/10 transition-all uppercase"
                        placeholder="e.g. FLAT50">
                </div>

                <div class="space-y-2" x-data="{ selected: '{{ old('item_data', isset($coupon) ? $coupon->couponable_type . ':' . $coupon->couponable_id : '') }}' }">
                    <label class="block text-xs font-black text-slate-700 uppercase ml-1">Assign To Item</label>
                    <select x-model="selected"
                        class="w-full rounded-2xl border-slate-200 bg-slate-50 px-6 py-4 text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-[#0777be]/10 transition-all">
                        <option value="">-- Choose Course or Bundle --</option>
                        <optgroup label="Single Courses">
                            @foreach ($courses as $course)
                                <option value="App\Models\Course:{{ $course->id }}">{{ $course->title }}</option>
                            @endforeach
                        </optgroup>
                        <optgroup label="Bundles">
                            @foreach ($bundles as $bundle)
                                <option value="App\Models\Bundle:{{ $bundle->id }}">{{ $bundle->title }}</option>
                            @endforeach
                        </optgroup>
                    </select>
                    <input type="hidden" name="item_type" :value="selected.split(':')[0]">
                    <input type="hidden" name="item_id" :value="selected.split(':')[1]">
                </div>
            </div>

            {{-- Row 2: Type, Value & Limit --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-700 uppercase ml-1">Discount Type</label>
                    <select name="type"
                        class="w-full rounded-2xl border-slate-200 bg-slate-50 px-6 py-4 text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-[#0777be]/10 transition-all">
                        <option value="fixed" {{ old('type', $coupon->type ?? '') == 'fixed' ? 'selected' : '' }}>Fixed
                            (₹)</option>
                        <option value="percentage"
                            {{ old('type', $coupon->type ?? '') == 'percentage' ? 'selected' : '' }}>Percentage (%)
                        </option>
                    </select>
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-700 uppercase ml-1">Discount Value</label>
                    <input type="number" name="value" value="{{ old('value', $coupon->value ?? '') }}" required
                        class="w-full rounded-2xl border-slate-200 bg-slate-50 px-6 py-4 text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-[#0777be]/10 transition-all">
                </div>

                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-700 uppercase ml-1">Usage Limit</label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit ?? 1) }}"
                        class="w-full rounded-2xl border-slate-200 bg-slate-50 px-6 py-4 text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-[#0777be]/10 transition-all">
                </div>
            </div>

            {{-- Row 3: Expiry --}}
            <div class="space-y-2 max-w-md">
                <label class="block text-xs font-black text-slate-700 uppercase ml-1">Expiry Date (Optional)</label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date', $coupon->expiry_date ?? '') }}"
                    class="w-full rounded-2xl border-slate-200 bg-slate-50 px-6 py-4 text-sm font-bold text-slate-700 focus:bg-white focus:ring-4 focus:ring-[#0777be]/10 transition-all">
            </div>

            {{-- Submit Button: Balanced Size --}}
            <div class="flex justify-start pt-6 border-t border-slate-50">
                <button type="submit"
                    class="inline-flex items-center justify-center bg-[#0777be] text-white px-10 py-4 rounded-2xl font-black shadow-lg shadow-blue-100 uppercase tracking-widest text-xs active:scale-95 transition-all hover:bg-[#0666a3] group">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ isset($coupon) ? 'Update Coupon' : 'Generate & Active Coupon' }}
                </button>
            </div>
        </form>
    </div>
@endsection
