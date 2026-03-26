@extends('layouts.admin')

@section('content')
<div class="mb-6">
    <h2 class="text-3xl font-black text-mainText tracking-tight">Wallet & Withdrawal Settings</h2>
    <p class="text-sm text-mutedText font-medium mt-1">Configure automated withdrawal holding periods and wallet behavior.</p>
</div>

<div class="bg-surface rounded-2xl border border-primary/10 shadow-sm p-6 md:p-8 max-w-4xl relative overflow-hidden">
     <!-- Aesthetic Accents -->
     <div class="absolute -top-24 -right-24 w-72 h-72 bg-primary/5 blur-[80px] rounded-full pointer-events-none"></div>

    <form action="{{ route('admin.settings.wallet.update') }}" method="POST" class="space-y-10 relative z-10">
        @csrf

        {{-- Tax Deduction (TDS) --}}
        <div>
            <h3 class="text-lg font-black text-mainText uppercase tracking-widest mb-4 flex items-center gap-2">
                <i class="fas fa-hand-holding-usd text-primary"></i> Tax Deduction (TDS)
            </h3>

            <div class="grid grid-cols-1 gap-6">
                <div class="max-w-md">
                    <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-3">TDS Deduction (2%)</label>
                    <div class="flex items-center gap-4 bg-navy p-4 rounded-xl border border-primary/10">
                        <div class="flex-1">
                            <p class="text-sm font-bold text-mainText">Enable TDS Calculation</p>
                        </div>
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="tds_enabled" value="1" {{ old('tds_enabled', $settings['tds_enabled']) ? 'checked' : '' }} class="sr-only peer">
                            <div class="w-11 h-6 bg-navy/50 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary border border-primary/10"></div>
                        </label>
                    </div>
                </div>
            </div>
        </div>

        {{-- Withdrawal Timing --}}
        <div>
            <h3 class="text-lg font-black text-mainText uppercase tracking-widest mb-4 flex items-center gap-2">
                <i class="fas fa-clock text-primary"></i> Withdrawal Timing
            </h3>

            <div class="grid grid-cols-1 gap-6">
                {{-- Commission Holding Period --}}
                <div class="max-w-md">
                    <label class="block text-xs font-bold text-mutedText uppercase tracking-widest mb-2">Commission Holding Period (Hours) <span class="text-red-500">*</span></label>
                    <div class="relative group">
                        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
                            <i class="fas fa-lock text-primary/40 group-focus-within:text-primary transition-colors"></i>
                        </div>
                        <input type="number" name="commission_holding_hours" value="{{ old('commission_holding_hours', $settings['commission_holding_hours']) }}" required min="0"
                            class="w-full bg-navy border border-primary/10 rounded-xl pl-12 pr-4 py-3.5 text-sm font-bold text-mainText focus:border-primary outline-none focus:ring-1 focus:ring-primary transition-all shadow-sm"
                            placeholder="e.g. 24">
                    </div>
                    <p class="text-[11px] text-mutedText mt-3 bg-navy p-3 rounded-xl border border-primary/5">
                        <i class="fas fa-info-circle text-primary mr-1"></i> Time before a commission moves from <strong>Hold</strong> to <strong>Available</strong> for withdrawal.
                    </p>
                    @error('commission_holding_hours') <p class="text-red-500 text-xs font-bold mt-1">{{ $message }}</p> @enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end pt-6 border-t border-primary/10">
            <button type="submit" class="bg-primary text-white hover:bg-primary/90 px-8 py-3.5 rounded-xl font-bold uppercase tracking-widest text-sm transition-all shadow-lg flex items-center gap-3">
                <i class="fas fa-save"></i> Save Wallet Settings
            </button>
        </div>
    </form>
</div>
@endsection
