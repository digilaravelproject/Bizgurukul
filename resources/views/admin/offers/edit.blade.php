@extends('layouts.admin')

@section('content')
<div class="max-w-4xl mx-auto space-y-6">
    {{-- Top Navigation & Header --}}
    <div class="flex items-center justify-between">
        <div>
            <a href="{{ route('admin.offers.index') }}" class="text-xs font-bold text-mutedText hover:text-primary transition-colors flex items-center gap-1 mb-1">
                <i class="fas fa-arrow-left"></i> Back to Offer Manager
            </a>
            <h2 class="text-2xl font-black text-mainText">Edit <span class="text-primary">Offer #{{ $offer->id }}</span></h2>
        </div>
        <span class="px-3 py-1 rounded-full text-xs font-black uppercase tracking-wider {{ $offer->status === 'Active' ? 'bg-emerald-50 text-emerald-600 border border-emerald-200' : ($offer->status === 'Expired' ? 'bg-rose-50 text-rose-600 border border-rose-200' : 'bg-gray-100 text-gray-600') }}">
            Status: {{ $offer->status }}
        </span>
    </div>

    {{-- Main Form Card --}}
    <div class="bg-surface rounded-3xl p-6 md:p-8 border border-primary/10 shadow-sm">
        <form action="{{ route('admin.offers.update', $offer->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Title --}}
                <div class="md:col-span-2 space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-mutedText ml-1">Offer Title <span class="text-rose-500">*</span></label>
                    <input type="text" name="title" value="{{ old('title', $offer->title) }}" required class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none font-bold text-mainText transition-all">
                </div>

                {{-- Reward Value --}}
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-mutedText ml-1">Reward Value (₹) <span class="text-rose-500">*</span></label>
                    <input type="number" step="0.01" name="reward_value" value="{{ old('reward_value', $offer->reward_value) }}" required class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none font-bold text-mainText transition-all">
                </div>

                {{-- Reward Type --}}
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-mutedText ml-1">Reward Type <span class="text-rose-500">*</span></label>
                    <select name="reward_type" required class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none font-bold text-mainText transition-all">
                        <option value="cash" {{ old('reward_type', $offer->reward_type) === 'cash' ? 'selected' : '' }}>Cash Bonus (₹)</option>
                        <option value="gift" {{ old('reward_type', $offer->reward_type) === 'gift' ? 'selected' : '' }}>Gift / Voucher</option>
                        <option value="trip" {{ old('reward_type', $offer->reward_type) === 'trip' ? 'selected' : '' }}>International / Domestic Trip</option>
                        <option value="gadget" {{ old('reward_type', $offer->reward_type) === 'gadget' ? 'selected' : '' }}>Gadget (iPhone, Laptop, etc.)</option>
                        <option value="custom" {{ old('reward_type', $offer->reward_type) === 'custom' ? 'selected' : '' }}>Custom Special Reward</option>
                    </select>
                </div>

                {{-- Target Amount --}}
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-mutedText ml-1">Target Sales/Earnings Amount (₹)</label>
                    <input type="number" step="0.01" name="target_amount" value="{{ old('target_amount', $offer->target_amount) }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none font-bold text-mainText transition-all">
                </div>

                {{-- Banner Image Upload & Current Preview --}}
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-mutedText ml-1">Offer Banner / Image</label>
                    @if($offer->image)
                        <div class="flex items-center gap-3 mb-2">
                            <img src="{{ $offer->image_url }}" alt="{{ $offer->title }}" class="w-12 h-12 rounded-xl object-cover border border-primary/20 shadow-sm">
                            <span class="text-xs text-mutedText font-medium">Current Image Saved</span>
                        </div>
                    @endif
                    <input type="file" name="image" accept="image/*" class="w-full rounded-xl border border-gray-200 px-3 py-2 text-xs focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none font-medium text-mainText file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary hover:file:bg-primary/20">
                </div>

                {{-- Start Date --}}
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-mutedText ml-1">Start Date & Time</label>
                    <input type="datetime-local" name="start_date" value="{{ old('start_date', $offer->start_date ? $offer->start_date->format('Y-m-d\TH:i') : '') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none font-bold text-mainText transition-all">
                </div>

                {{-- End Date --}}
                <div class="space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-mutedText ml-1">End Date & Time (Expiration)</label>
                    <input type="datetime-local" name="end_date" value="{{ old('end_date', $offer->end_date ? $offer->end_date->format('Y-m-d\TH:i') : '') }}" class="w-full rounded-xl border border-gray-200 px-4 py-3 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none font-bold text-mainText transition-all">
                </div>

                {{-- Description --}}
                <div class="md:col-span-2 space-y-2">
                    <label class="block text-xs font-black uppercase tracking-wider text-mutedText ml-1">Offer Description & Terms</label>
                    <textarea name="description" rows="4" class="w-full rounded-xl border border-gray-200 p-4 text-sm focus:border-primary focus:ring-2 focus:ring-primary/20 outline-none font-medium text-mainText transition-all">{{ old('description', $offer->description) }}</textarea>
                </div>

                {{-- Status Toggle --}}
                <div class="md:col-span-2 flex items-center gap-3 p-4 rounded-xl bg-gray-50 border border-gray-100">
                    <input type="checkbox" name="is_active" id="is_active" value="1" {{ old('is_active', $offer->is_active) ? 'checked' : '' }} class="w-4 h-4 rounded text-primary focus:ring-primary">
                    <label for="is_active" class="text-xs font-bold text-mainText cursor-pointer">
                        Enable Offer (Active)
                    </label>
                </div>
            </div>

            {{-- Submit Action --}}
            <div class="flex items-center justify-end gap-3 pt-4 border-t border-gray-100">
                <a href="{{ route('admin.offers.index') }}" class="px-5 py-2.5 rounded-xl bg-gray-100 text-gray-600 font-bold text-xs hover:bg-gray-200 transition-all">
                    Cancel
                </a>
                <button type="submit" class="px-6 py-2.5 rounded-xl bg-primary text-white font-bold text-xs hover:bg-primary/90 transition-all shadow-md">
                    Update Offer
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
