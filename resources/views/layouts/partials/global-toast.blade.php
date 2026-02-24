<div x-data="{
        show: {{ session('success') ? 'true' : 'false' }},
        message: '{{ session('success') }}'
    }"
    x-init="if(show) setTimeout(() => show = false, 5000)"
    x-show="show"
    x-cloak
    x-transition:enter="transition ease-out duration-500"
    x-transition:enter-start="opacity-0 translate-x-10 scale-90"
    x-transition:enter-end="opacity-100 translate-x-0 scale-100"
    x-transition:leave="transition ease-in duration-500"
    x-transition:leave-start="opacity-100 translate-x-0 scale-100"
    x-transition:leave-end="opacity-0 translate-x-10 scale-90"
    class="fixed top-20 right-6 md:right-10 z-[9999] pointer-events-none">

    <div class="bg-surface border-2 border-emerald-500/20 p-5 rounded-3xl shadow-2xl flex items-center gap-5 premium-shadow backdrop-blur-xl max-w-sm pointer-events-auto">
        <div class="w-12 h-12 rounded-2xl bg-emerald-500/10 flex items-center justify-center text-emerald-500 shadow-inner">
            <i class="fas fa-check-circle text-2xl"></i>
        </div>
        <div class="flex-1">
            <div class="flex items-center justify-between mb-1">
                <p class="text-[10px] font-black uppercase tracking-[0.2em] text-emerald-500">Operation Success</p>
                <button @click="show = false" class="text-mutedText hover:text-mainText transition-colors">
                    <i class="fas fa-times text-[10px]"></i>
                </button>
            </div>
            <p class="text-sm font-bold text-mainText leading-snug" x-text="message"></p>
        </div>
    </div>
</div>

<style>
    .premium-shadow {
        box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25);
    }
</style>
