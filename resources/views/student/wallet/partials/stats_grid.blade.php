<div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-7 gap-4 md:gap-6">
    @if ($dashboardData['is_filtered'])
        <div
            class="bg-surface rounded-2xl p-5 border-2 border-indigo-500 shadow-lg shadow-indigo-500/20 relative overflow-hidden group hover:-translate-y-1 transition duration-300">
            <div class="absolute top-0 right-0 w-full h-full bg-gradient-to-bl from-indigo-500/10 to-transparent"></div>
            <div class="absolute -right-2 -bottom-2 text-6xl text-indigo-500/10 -rotate-12 group-hover:rotate-0 transition-transform duration-500">
                <i class="fas fa-calendar-alt"></i>
            </div>
            <p class="text-[10px] md:text-sm font-bold text-indigo-400 uppercase tracking-widest mb-1 relative z-10">
                Range Earnings
            </p>
            <h3 class="text-xl md:text-2xl font-black text-mainText tracking-tight relative z-10">
                ₹{{ number_format($dashboardData['range_earnings'], 2) }}
            </h3>
        </div>
    @endif

    <div
        class="bg-surface rounded-2xl p-5 border border-primary/10 shadow-sm relative overflow-hidden group hover:-translate-y-1 transition duration-300">
        <div class="absolute -right-2 -bottom-2 text-6xl text-blue-500/5 -rotate-12 group-hover:rotate-0 transition-transform duration-500">
            <i class="fas fa-chart-line"></i>
        </div>
        <p class="text-[10px] md:text-sm font-bold text-mutedText uppercase tracking-widest mb-1 relative z-10">Total
            Revenue</p>
        <h3 class="text-xl md:text-2xl font-black text-mainText tracking-tight relative z-10">
            ₹{{ number_format($dashboardData['total_earnings'], 2) }}</h3>
    </div>

    <div
        class="bg-surface rounded-2xl p-5 border-2 border-emerald-500/30 shadow-lg relative overflow-hidden group hover:-translate-y-1 transition duration-300">
        <div class="absolute top-0 right-0 w-full h-full bg-gradient-to-bl from-emerald-500/5 to-transparent"></div>
        <div class="absolute -right-2 -bottom-2 text-6xl text-emerald-500/10 -rotate-12 group-hover:rotate-0 transition-transform duration-500">
            <i class="fas fa-wallet"></i>
        </div>
        <p
            class="text-[10px] md:text-sm font-bold text-emerald-500 uppercase tracking-widest mb-1 relative z-10 flex items-center gap-2">
            <i class="fas fa-check-circle"></i> Available
        </p>
        <h3 class="text-xl md:text-2xl font-black text-mainText tracking-tight relative z-10">
            ₹{{ number_format($dashboardData['available_balance'], 2) }}</h3>
        @if ($dashboardData['tds_enabled'])
            <p class="text-[9px] font-bold text-mutedText/60 uppercase tracking-tighter relative z-10">Payout:
                ₹{{ number_format($dashboardData['available_balance_net'], 2) }}</p>
        @endif
    </div>

    <div
        class="bg-surface rounded-2xl p-5 border border-amber-500/30 shadow-sm relative overflow-hidden group hover:-translate-y-1 transition duration-300">
        <div class="absolute -right-2 -bottom-2 text-6xl text-amber-500/10 -rotate-12 group-hover:rotate-0 transition-transform duration-500">
            <i class="fas fa-clock"></i>
        </div>
        <p
            class="text-[10px] md:text-sm font-bold text-amber-500 uppercase tracking-widest mb-1 relative z-10 flex items-center gap-2">
            <i class="fas fa-hourglass-half text-xs"></i> On Hold
        </p>
        <h3 class="text-xl md:text-2xl font-black text-mainText tracking-tight relative z-10">
            ₹{{ number_format($dashboardData['on_hold_balance'], 2) }}</h3>
        @if ($dashboardData['tds_enabled'])
            <p class="text-[9px] font-bold text-mutedText/60 uppercase tracking-tighter relative z-10">Net:
                ₹{{ number_format($dashboardData['on_hold_balance_net'], 2) }}</p>
        @endif
    </div>

    <div
        class="bg-surface rounded-2xl p-5 border border-blue-500/30 shadow-sm relative overflow-hidden group hover:-translate-y-1 transition duration-300">
        <div class="absolute -right-2 -bottom-2 text-6xl text-blue-500/10 -rotate-12 group-hover:rotate-0 transition-transform duration-500">
            <i class="fas fa-sync fa-spin-slow"></i>
        </div>
        <p
            class="text-[10px] md:text-sm font-bold text-blue-500 uppercase tracking-widest mb-1 relative z-10 flex items-center gap-2">
            <i class="fas fa-spinner fa-spin text-xs"></i> Pending
        </p>
        <h3 class="text-xl md:text-2xl font-black text-mainText tracking-tight relative z-10">
            ₹{{ number_format($dashboardData['pending_balance'], 2) }}</h3>
    </div>

    <div
        class="bg-surface rounded-2xl p-5 border border-primary/10 shadow-sm relative overflow-hidden group hover:-translate-y-1 transition duration-300">
        <div class="absolute -right-2 -bottom-2 text-6xl text-primary/10 -rotate-12 group-hover:rotate-0 transition-transform duration-500">
            <i class="fas fa-hand-holding-usd"></i>
        </div>
        <p class="text-[10px] md:text-sm font-bold text-mutedText uppercase tracking-widest mb-1 relative z-10">Total
            Paid</p>
        <h3 class="text-xl md:text-2xl font-black text-mainText tracking-tight relative z-10">
            ₹{{ number_format($dashboardData['total_withdrawn'], 2) }}</h3>
    </div>

    @if ($dashboardData['tds_enabled'])
        <div
            class="bg-surface rounded-2xl p-5 border border-red-500/10 shadow-sm relative overflow-hidden group hover:-translate-y-1 transition duration-300">
            <div class="absolute -right-2 -bottom-2 text-6xl text-red-500/10 -rotate-12 group-hover:rotate-0 transition-transform duration-500">
                <i class="fas fa-file-invoice-dollar"></i>
            </div>
            <p class="text-[10px] md:text-sm font-bold text-red-500/70 uppercase tracking-widest mb-1 relative z-10">TDS
                Deducted</p>
            <h3 class="text-xl md:text-2xl font-black text-mainText tracking-tight relative z-10">
                ₹{{ number_format($dashboardData['total_tds'], 2) }}</h3>
        </div>
    @endif
</div>
