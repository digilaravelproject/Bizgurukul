@extends('layouts.admin')

@section('content')
<div class="mb-6 flex items-center justify-between">
    <div>
        <h2 class="text-3xl font-black text-mainText tracking-tight">Add New Tax</h2>
        <p class="text-sm text-mutedText font-medium mt-1">Create a new tax slab to apply on checkouts.</p>
    </div>
    <a href="{{ route('admin.taxes.index') }}" class="text-sm font-bold text-mutedText hover:text-primary transition flex items-center gap-2">
        <i class="fas fa-arrow-left"></i> Back to Taxes
    </a>
</div>

<div class="bg-surface rounded-2xl border border-primary/10 shadow-sm p-6 md:p-8 max-w-3xl">
    <form action="{{ route('admin.taxes.store') }}" method="POST" class="space-y-6">
        @csrf

        {{-- Tax Name --}}
        <div>
            <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Tax Name <span class="text-red-500">*</span></label>
            <input type="text" name="name" value="{{ old('name') }}" required placeholder="e.g. GST 18%"
                class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all">
            @error('name') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            {{-- Type --}}
            <div>
                <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Calculation <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select name="type" required class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none appearance-none cursor-pointer">
                        <option value="percentage" {{ old('type') == 'percentage' ? 'selected' : '' }}>Percentage (%)</option>
                        <option value="flat" {{ old('type') == 'flat' ? 'selected' : '' }}>Flat Amount (₹)</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-mutedText text-xs pointer-events-none"></i>
                </div>
                @error('type') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Tax Configuration --}}
            <div>
                <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Tax Type <span class="text-red-500">*</span></label>
                <div class="relative">
                    <select name="tax_type" required class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none appearance-none cursor-pointer">
                        <option value="inclusive" {{ old('tax_type') == 'inclusive' ? 'selected' : '' }}>Inclusive (Extract from Total)</option>
                        <option value="exclusive" {{ old('tax_type') == 'exclusive' ? 'selected' : '' }}>Exclusive (Add to Total)</option>
                    </select>
                    <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-mutedText text-xs pointer-events-none"></i>
                </div>
                @error('tax_type') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
            </div>

            {{-- Value --}}
            <div>
                <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Tax Value <span class="text-red-500">*</span></label>
                <input type="number" step="0.01" min="0" name="value" value="{{ old('value') }}" required placeholder="e.g. 18.00"
                    class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all">
                @error('value') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
            </div>
        </div>

        {{-- Status --}}
        <div>
            <label class="flex items-center gap-3 cursor-pointer">
                <div class="relative">
                    <input type="checkbox" name="is_active" value="1" class="sr-only peer" {{ old('is_active', true) ? 'checked' : '' }}>
                    <div class="w-11 h-6 bg-navy rounded-full peer peer-checked:bg-primary transition-colors border border-primary/20"></div>
                    <div class="absolute left-1 top-1 bg-white w-4 h-4 rounded-full transition-transform peer-checked:translate-x-5"></div>
                </div>
                <span class="text-sm font-bold text-mainText">Active Tax</span>
            </label>
            <p class="text-[10px] text-mutedText mt-1 pl-14">Turn off to temporarily disable this tax during checkout.</p>
        </div>

        <hr class="border-primary/5">

        <div class="flex justify-end gap-4 pt-2">
            <a href="{{ route('admin.taxes.index') }}" class="px-6 py-3 rounded-xl font-bold text-mutedText hover:bg-navy transition">Cancel</a>
            <button type="submit" class="bg-primary text-white hover:bg-primary/90 px-8 py-3 rounded-xl font-bold transition-all shadow-lg flex items-center gap-2">
                Save Tax
            </button>
        </div>
    </form>
</div>
@endsection
