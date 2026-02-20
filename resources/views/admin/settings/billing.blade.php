@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-black text-mainText tracking-tight">Billing & Company Settings</h2>
    <p class="text-sm text-mutedText font-medium mt-1">Configure company details that will appear on user invoices.</p>
</div>

<div class="bg-surface rounded-2xl border border-primary/10 shadow-sm p-6 md:p-8 max-w-4xl xl:max-w-5xl relative overflow-hidden">
     <!-- Aesthetic Accents -->
     <div class="absolute -top-24 -right-24 w-72 h-72 bg-primary/5 blur-[80px] rounded-full pointer-events-none"></div>

    <form action="{{ route('admin.settings.billing.update') }}" method="POST" enctype="multipart/form-data" class="space-y-8 relative z-10">
        @csrf

        {{-- Basic Information --}}
        <div>
            <h3 class="text-lg font-black text-mainText uppercase tracking-widest mb-4 flex items-center gap-2">
                <i class="fas fa-building text-primary"></i> Basic Info
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Site Name --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Company / Site Name <span class="text-red-500">*</span></label>
                    <input type="text" name="site_name" value="{{ old('site_name', $settings['site_name']) }}" required placeholder="e.g. BizGurukul Pvt Ltd"
                        class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all shadow-sm">
                    @error('site_name') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Email --}}
                <div>
                    <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Support Email <span class="text-red-500">*</span></label>
                    <input type="email" name="company_email" value="{{ old('company_email', $settings['company_email']) }}" required placeholder="support@company.com"
                        class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all shadow-sm">
                    @error('company_email') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- Phone --}}
                <div>
                    <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Support Phone <span class="text-red-500">*</span></label>
                    <input type="text" name="company_phone" value="{{ old('company_phone', $settings['company_phone']) }}" required placeholder="+91 9876543210"
                        class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all shadow-sm">
                    @error('company_phone') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <hr class="border-primary/5">

        {{-- Address Information --}}
        <div>
            <h3 class="text-lg font-black text-mainText uppercase tracking-widest mb-4 flex items-center gap-2">
                <i class="fas fa-map-marker-alt text-primary"></i> Registered Address
            </h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Address Line --}}
                <div class="md:col-span-2">
                    <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Street Address <span class="text-red-500">*</span></label>
                    <input type="text" name="company_address" value="{{ old('company_address', $settings['company_address']) }}" required placeholder="123 Corporate Tower, Phase 2"
                        class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all shadow-sm">
                    @error('company_address') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- City --}}
                <div>
                    <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">City <span class="text-red-500">*</span></label>
                    <input type="text" name="company_city" value="{{ old('company_city', $settings['company_city']) }}" required placeholder="New Delhi"
                        class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all shadow-sm">
                    @error('company_city') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                </div>

                {{-- State & Zip --}}
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">State <span class="text-red-500">*</span></label>
                        <input type="text" name="company_state" value="{{ old('company_state', $settings['company_state']) }}" required placeholder="Delhi"
                            class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all shadow-sm">
                        @error('company_state') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                    <div>
                        <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">ZIP Code <span class="text-red-500">*</span></label>
                        <input type="text" name="company_zip" value="{{ old('company_zip', $settings['company_zip']) }}" required placeholder="110001"
                            class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all shadow-sm">
                        @error('company_zip') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <hr class="border-primary/5">

        {{-- Branding & Logo --}}
        <div>
            <h3 class="text-lg font-black text-mainText uppercase tracking-widest mb-4 flex items-center gap-2">
                <i class="fas fa-paint-brush text-primary"></i> Branding
            </h3>

            <div>
                <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Company Invoice Logo</label>

                <div class="flex items-start gap-6">
                    {{-- Current Logo Preview --}}
                    @if($settings['company_logo'])
                        <div class="w-32 h-32 bg-white rounded-2xl border border-primary/10 shadow-sm flex items-center justify-center p-2 shrink-0">
                            <img src="{{ asset('storage/' . $settings['company_logo']) }}" alt="Logo" class="max-w-full max-h-full object-contain">
                        </div>
                    @else
                        <div class="w-32 h-32 bg-navy rounded-2xl border border-primary/10 border-dashed flex flex-col items-center justify-center text-mutedText shrink-0">
                            <i class="fas fa-image text-3xl mb-2"></i>
                            <span class="text-[10px] uppercase font-bold tracking-widest">No Logo</span>
                        </div>
                    @endif

                    {{-- Upload Input --}}
                    <div class="w-full">
                        <input type="file" name="company_logo" accept="image/*"
                            class="block w-full text-sm text-mutedText
                            file:mr-4 file:py-2.5 file:px-6
                            file:rounded-xl file:border-0
                            file:text-sm file:font-bold file:uppercase file:tracking-widest
                            file:bg-primary/10 file:text-primary
                            hover:file:bg-primary border border-primary/10 rounded-xl bg-navy transition-all cursor-pointer">
                        <p class="text-[11px] text-mutedText mt-3 bg-navy p-3 rounded-xl border border-primary/5 inline-block">
                            <i class="fas fa-info-circle text-primary mr-1"></i> Recommended: PNG with transparent background. Max size: 2MB.
                        </p>
                        @error('company_logo') <p class="text-red-500 text-xs font-bold mt-2">{{ $message }}</p> @enderror
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-6 border-t border-primary/10">
            <button type="submit" class="bg-primary text-white hover:bg-primary/90 px-8 py-3.5 rounded-xl font-bold uppercase tracking-widest text-sm transition-all shadow-lg flex items-center gap-3">
                <i class="fas fa-save"></i> Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
