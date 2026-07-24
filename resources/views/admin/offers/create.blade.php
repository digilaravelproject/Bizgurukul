@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Top Navigation & Header --}}
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.offers.index') }}" class="text-xs font-bold text-mutedText hover:text-primary transition-colors flex items-center gap-1 mb-1">
                <i class="fas fa-arrow-left"></i> Back to Offer Manager
            </a>
            <h2 class="text-2xl font-black text-mainText">Create <span class="text-primary">Time-Sensitive Offer</span></h2>
        </div>
    </div>

    {{-- Main Form Card --}}
    <div class="bg-surface rounded-3xl p-6 md:p-8 border border-primary/10 shadow-sm">
        <form action="{{ route('admin.offers.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Title --}}
                <div class="md:col-span-2 space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-mutedText ml-1">Offer Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title') }}" required placeholder="e.g. Monsoon Special Double Cash Reward" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none font-bold text-mainText transition-all">
                </div>

                {{-- Reward Value --}}
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-mutedText ml-1">Reward Value (₹) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="reward_value" value="{{ old('reward_value', '0.00') }}" required placeholder="e.g. 5000.00" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none font-bold text-mainText transition-all">
                </div>

                {{-- Reward Type --}}
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-mutedText ml-1">Reward Type <span class="text-rose-500">*</span></label>
                    <select name="reward_type" required class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none font-bold text-mainText transition-all">
                        <option value="cash" {{ old('reward_type') === 'cash' ? 'selected' : '' }}>Cash Bonus (₹)</option>
                        <option value="gift" {{ old('reward_type') === 'gift' ? 'selected' : '' }}>Gift / Voucher</option>
                        <option value="trip" {{ old('reward_type') === 'trip' ? 'selected' : '' }}>International / Domestic Trip</option>
                        <option value="gadget" {{ old('reward_type') === 'gadget' ? 'selected' : '' }}>Gadget (iPhone, Laptop, etc.)</option>
                        <option value="custom" {{ old('reward_type') === 'custom' ? 'selected' : '' }}>Custom Special Reward</option>
                    </select>
                </div>

                {{-- Target Amount (Optional) --}}
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-mutedText ml-1">Target Sales/Earnings Amount (₹)</label>
                    <input type="number" step="0.01" name="target_amount" value="{{ old('target_amount', '0.00') }}" placeholder="0.00 (Leave 0 for no threshold)" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none font-bold text-mainText transition-all">
                    <p class="text-[11px] text-mutedText ml-1">Threshold earnings required by student to qualify for this offer.</p>
                </div>

                {{-- Banner Image Upload --}}
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-mutedText ml-1">Offer Banner / Image</label>
                    <input type="file" name="image" accept="image/*" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-xs focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none font-medium text-mainText file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                    <p class="text-[11px] text-mutedText ml-1">Recommended size: 800x400px (PNG, JPG, WEBP).</p>
                </div>

                {{-- Start Date --}}
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-mutedText ml-1">Start Date & Time</label>
                    <input type="datetime-local" name="start_date" value="{{ old('start_date') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none font-bold text-mainText transition-all">
                </div>

                {{-- End Date --}}
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-mutedText ml-1">End Date & Time (Expiration)</label>
                    <input type="datetime-local" name="end_date" value="{{ old('end_date') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none font-bold text-mainText transition-all">
                    <p class="text-[11px] text-mutedText ml-1">Offer automatically becomes <strong>Expired & Included</strong> after this date.</p>
                </div>

                {{-- Description --}}
                <div class="md:col-span-2 space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-mutedText ml-1">Offer Description & Terms</label>
                    <textarea name="description" rows="4" placeholder="Enter terms, conditions, or promotional description for students..." class="w-full rounded-xl border border-gray-200 p-4 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none font-medium text-mainText transition-all">{{ old('description') }}</textarea>
                </div>

                {{-- Status Toggle --}}
                <div class="md:col-span-2 flex items-center gap-3 p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', '1') ? 'checked' : '' }} class="w-4 h-4 rounded text-primary focus:ring-primary">
                    <label for="is_active" class="text-xs font-bold text-mainText cursor-pointer">
                        Enable Offer (Active)
                        <span class="block text-[11px] font-medium text-mutedText">Disabling turns off the offer regardless of date window.</span>
                    </label>
                </div>
            </div>

            {{-- Submit Action --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.offers.index') }}" class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-600 font-bold text-xs hover:bg-gray-200 transition-all">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary text-white font-bold text-xs hover:bg-primary/90 transition-all shadow-md">
                    Create Offer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
