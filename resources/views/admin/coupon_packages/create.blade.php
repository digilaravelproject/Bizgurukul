@extends('layouts.admin')

@section('title', isset($package) ? 'Edit Package' : 'Create Package')

@section('content')
    {{-- Select2 CSS aur Custom Styling --}}
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

    <div class="max-w-5xl mx-auto py-8 px-4">
        {{-- Header --}}
        <div class="flex items-center justify-between mb-8">
            <h2 class="text-2xl font-black text-slate-800 tracking-tight uppercase">
                {{ isset($package) ? 'Edit Coupon Package' : 'Generate New Package' }}
            </h2>
            <a href="{{ route('admin.coupon-packages.index') }}"
                class="text-[10px] font-black text-slate-400 hover:text-[#0777be] uppercase tracking-widest transition-all">
                ← Back to Manager
            </a>
        </div>

        {{-- Main Form --}}
        <form action="{{ route('admin.coupon-packages.store') }}" method="POST"
            class="bg-white p-10 rounded-[2.5rem] border border-slate-100 shadow-sm space-y-8">
            @csrf
            @if (isset($package))
                <input type="hidden" name="id" value="{{ $package->id }}">
            @endif

            {{-- Row 1: Package Name --}}
            <div class="space-y-2">
                <label class="block text-xs font-black text-slate-700 uppercase ml-1">Package Name (Title)</label>
                <input type="text" name="name" value="{{ old('name', $package->name ?? '') }}" required
                    placeholder="e.g. MEGA SAVER COMBO 2024"
                    class="w-full rounded-2xl border-slate-200 bg-slate-50 px-6 py-4 text-sm font-black text-[#0777be] tracking-widest uppercase shadow-sm focus:bg-white focus:ring-4 focus:ring-blue-50 outline-none transition-all">
            </div>

            {{-- Row 2: Multiple Item Selection --}}
            <div class="space-y-4 bg-slate-50 p-6 rounded-3xl border border-slate-100">
                <label class="block text-xs font-black text-slate-700 uppercase ml-1 tracking-wider">Include Premium
                    Items</label>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    {{-- Courses Selection --}}
                    <div class="space-y-3">
                        <label class="block text-[11px] font-black text-[#0777be] uppercase tracking-widest ml-1">Included
                            Courses</label>
                        <select name="courses[]" multiple class="js-select2-multiple w-full">
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}"
                                    {{ isset($package) && is_array($package->selected_courses) && in_array($course->id, $package->selected_courses) ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- Bundles Selection --}}
                    <div class="space-y-3">
                        <label class="block text-[11px] font-black text-[#0777be] uppercase tracking-widest ml-1">Included
                            Bundles</label>
                        <select name="bundles[]" multiple class="js-select2-multiple w-full">
                            @foreach ($bundles as $bundle)
                                <option value="{{ $bundle->id }}"
                                    {{ isset($package) && is_array($package->selected_bundles) && in_array($bundle->id, $package->selected_bundles) ? 'selected' : '' }}>
                                    {{ $bundle->title }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>
            </div>

            {{-- Row 3: Pricing Rules --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-700 uppercase ml-1">Original Price (MRP ₹)</label>
                    <input type="number" name="price" value="{{ old('price', $package->price ?? '') }}" required
                        class="w-full rounded-2xl border-slate-200 bg-slate-50 px-6 py-4 font-bold text-red-400 shadow-sm focus:bg-white outline-none transition-all"
                        placeholder="5000">
                </div>
                <div class="space-y-2">
                    <label class="block text-xs font-black text-slate-700 uppercase ml-1">Selling Price (Discounted
                        ₹)</label>
                    <input type="number" name="discount_price"
                        value="{{ old('discount_price', $package->discount_price ?? '') }}" required
                        class="w-full rounded-2xl border-slate-200 bg-slate-50 px-6 py-4 font-bold text-green-600 shadow-sm focus:bg-white outline-none transition-all"
                        placeholder="1999">
                </div>
            </div>

            {{-- Row 4: Description --}}
            <div class="space-y-2">
                <label class="block text-xs font-black text-slate-700 uppercase ml-1">Package Description</label>
                <textarea name="description" rows="3"
                    class="w-full rounded-2xl border-slate-200 bg-slate-50 px-6 py-4 text-sm font-bold text-slate-600 shadow-sm focus:bg-white outline-none transition-all"
                    placeholder="Briefly explain the benefit of this package...">{{ old('description', $package->description ?? '') }}</textarea>
            </div>

            {{-- Submit --}}
            <div class="flex justify-start pt-8 border-t border-slate-50">
                <button type="submit"
                    class="bg-[#0777be] text-white px-12 py-5 rounded-2xl font-black shadow-lg shadow-blue-100 uppercase tracking-widest text-xs transition-all hover:bg-[#0666a3] active:scale-95">
                    {{ isset($package) ? 'Update Package Settings' : 'Create & Launch Package' }}
                </button>
            </div>
        </form>
    </div>

    {{-- Scripts Load --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script>
        $(document).ready(function() {
            $('.js-select2-multiple').select2({
                placeholder: "Click to include items...",
                allowClear: true,
                width: '100%'
            });
        });
    </script>
@endsection
