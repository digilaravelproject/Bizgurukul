@extends('layouts.admin')

@section('title', isset($coupon) ? 'Edit Coupon' : 'Create Coupon')

@section('content')
    {{-- Select2 CSS aur Direct Fallback Style --}}
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <style>
        .select2-container--default .select2-selection--multiple {
            border: 1px solid #e2e8f0 !important;
            border-radius: 1rem !important;
            padding: 10px !important;
            background-color: #f8fafc !important;
            min-height: 55px !important;
        }

        .select2-container--default.select2-container--focus .select2-selection--multiple {
            border-color: #0777be !important;
            background-color: #fff !important;
        }
    </style>

    <div class="max-w-5xl mx-auto py-8 px-4" x-data="{ couponType: '{{ old('coupon_type', $coupon->coupon_type ?? 'general') }}' }">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">
                {{ isset($coupon) ? 'Edit Coupon' : 'Generate New Coupon' }}
            </h2>
            <a href="{{ route('admin.coupons.index') }}"
                class="text-[10px] font-black text-slate-400 hover:text-[#0777be] uppercase tracking-widest transition-all">
                ← Back to Manager
            </a>
        </div>

        {{-- Main Form --}}
        <form action="{{ route('admin.coupons.store') }}" method="POST"
            class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-8">
            @csrf
            @if (isset($coupon))
                <input type="hidden" name="id" value="{{ $coupon->id }}">
            @endif

            {{-- 8-Digit Auto Code Field --}}
            <div class="max-w-md space-y-2">
                <label class="block text-xs font-black text-slate-700 uppercase ml-1">Coupon Code (8 Digits)</label>
                <div class="relative">
                    <input type="text" name="code" id="coupon_code" value="{{ old('code', $coupon->code ?? '') }}"
                        required readonly
                        class="w-full rounded-2xl border-slate-200 bg-slate-50 px-6 py-4 text-sm font-black text-[#0777be] tracking-widest uppercase shadow-sm">
                    @if (!isset($coupon))
                        <button type="button" onclick="generateRandomCode()"
                            class="absolute right-4 top-1/2 -translate-y-1/2 text-[10px] font-black text-[#0777be] uppercase bg-blue-50 px-3 py-1 rounded-lg hover:bg-blue-100 transition-all">
                            Regenerate
                        </button>
                    @endif
                </div>
            </div>

            {{-- Scope Selection (General vs Specific) --}}
            <div class="space-y-4 bg-slate-50 p-6 rounded-3xl border border-slate-100">
                <label class="block text-xs font-black text-slate-700 uppercase ml-1 tracking-wider">Coupon Application
                    Scope</label>
                <div class="flex items-center gap-10">
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="radio" name="coupon_type" value="general" x-model="couponType"
                            class="w-4 h-4 text-[#0777be] border-slate-300">
                        <span class="text-sm font-black text-slate-600 group-hover:text-[#0777be] transition-colors">General
                            (No Selection Required)</span>
                    </label>
                    <label class="flex items-center gap-3 cursor-pointer group">
                        <input type="radio" name="coupon_type" value="specific" x-model="couponType"
                            class="w-4 h-4 text-[#0777be] border-slate-300">
                        <span
                            class="text-sm font-black text-slate-600 group-hover:text-[#0777be] transition-colors">Specific
                            (Select Multiple Items)</span>
                    </label>
                </div>
            </div>

            {{-- Multiple Selection: Shown only if Specific is selected --}}
            <div x-show="couponType === 'specific'" x-transition
                class="grid grid-cols-1 md:grid-cols-2 gap-8 border-l-4 border-[#0777be] pl-8 py-2">
                <div class="space-y-3">
                    <label class="block text-[11px] font-black text-[#0777be] uppercase tracking-widest ml-1">Target
                        Multiple Courses</label>
                    <select name="courses[]" multiple class="js-select2-multiple w-full">
                        @foreach ($courses as $course)
                            <option value="{{ $course->id }}"
                                {{ isset($coupon) && in_array($course->id, json_decode($coupon->selected_courses ?? '[]')) ? 'selected' : '' }}>
                                {{ $course->title }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="space-y-3">
                    <label class="block text-[11px] font-black text-[#0777be] uppercase tracking-widest ml-1">Target
                        Multiple Bundles</label>
                    <select name="bundles[]" multiple class="js-select2-multiple w-full">
                        @foreach ($bundles as $bundle)
                            <option value="{{ $bundle->id }}"
                                {{ isset($coupon) && in_array($bundle->id, json_decode($coupon->selected_bundles ?? '[]')) ? 'selected' : '' }}>
                                {{ $bundle->title }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            {{-- Discount Rules --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-700 uppercase ml-1">Discount Type</label>
                    <select name="type"
                        class="w-full rounded-2xl border-slate-200 bg-slate-50 px-6 py-4 text-sm font-bold text-slate-700">
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
                        class="w-full rounded-2xl border-slate-200 bg-slate-50 px-6 py-4 font-bold text-slate-700 shadow-sm">
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-700 uppercase ml-1">Usage Limit</label>
                    <input type="number" name="usage_limit" value="{{ old('usage_limit', $coupon->usage_limit ?? 1) }}"
                        class="w-full rounded-2xl border-slate-200 bg-slate-50 px-6 py-4 font-bold text-slate-700 shadow-sm">
                </div>
            </div>

            {{-- Expiry --}}
            <div class="space-y-2 max-w-sm">
                <label class="block text-xs font-black text-slate-700 uppercase ml-1">Expiry Date</label>
                <input type="date" name="expiry_date" value="{{ old('expiry_date', $coupon->expiry_date ?? '') }}"
                    class="w-full rounded-2xl border-slate-200 bg-slate-50 px-6 py-4 font-bold text-slate-600 shadow-sm">
            </div>

            <div class="flex justify-start pt-8 border-t border-slate-50">
                <button type="submit"
                    class="bg-[#0777be] text-white px-12 py-5 rounded-2xl font-black shadow-lg shadow-blue-100 uppercase tracking-widest text-xs transition-all hover:bg-[#0666a3] active:scale-95">
                    {{ isset($coupon) ? 'Update Coupon' : 'Generate & Active Coupon' }}
                </button>
            </div>
        </form>
    </div>

    {{-- Scripts Load directly for 100% functionality --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            function initSelect2() {
                $('.js-select2-multiple').select2({
                    placeholder: "Click to select multiple items...",
                    allowClear: true,
                    width: '100%'
                });
            }
            initSelect2();

            // Re-init for Alpine toggles
            document.addEventListener('alpine:init', () => {
                initSelect2();
            });
        });

        // 8 Digit Generator
        function generateRandomCode() {
            const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
            let result = '';
            for (let i = 0; i < 8; i++) {
                result += chars.charAt(Math.floor(Math.random() * chars.length));
            }
            document.getElementById('coupon_code').value = result;
        }

        @if (!isset($coupon))
            window.onload = generateRandomCode;
        @endif
    </script>
@endsection
