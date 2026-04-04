@props([
    'placeholder' => 'Search...',
    'showDateFilter' => true,
    'showExport' => true,
    'exportAction' => 'exportData',
    'searchDebounce' => '500ms'
])

<div class="flex flex-col md:flex-row justify-between items-center gap-4 bg-surface p-6 rounded-2xl border border-primary/10 shadow-sm mb-6">
    {{-- Left: Search --}}
    <div class="relative w-full md:w-80 group">
        <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none">
            <i class="fas fa-search text-xs text-mutedText group-focus-within:text-primary transition-colors"></i>
        </div>
        <input type="text" 
               x-model="search" 
               @input.debounce.{{ $searchDebounce }}="updateTable()"
               placeholder="{{ $placeholder }}" 
               class="w-full bg-surface border border-primary/10 rounded-xl pl-11 pr-4 py-2.5 text-sm font-medium text-mainText focus:border-primary outline-none focus:ring-4 focus:ring-primary/5 transition-all shadow-sm">
        <div x-show="loading" class="absolute inset-y-0 right-0 pr-4 flex items-center" x-cloak>
            <i class="fas fa-circle-notch fa-spin text-xs text-primary"></i>
        </div>
    </div>

    {{-- Right: Date Filters, Per Page & Export --}}
    <div class="flex flex-wrap items-center gap-3 w-full md:w-auto">
        {{-- Per Page Selector --}}
        <div class="relative">
            <select x-model="perPage" 
                    @change="updateTable()" 
                    class="appearance-none bg-surface border border-primary/10 rounded-xl pl-4 pr-8 py-2 text-[10px] font-black uppercase text-mutedText focus:border-primary outline-none focus:ring-4 focus:ring-primary/5 transition-all shadow-sm">
                <option value="20">20 Per Page</option>
                <option value="30">30 Per Page</option>
                <option value="50">50 Per Page</option>
                <option value="100">100 Per Page</option>
                <option value="200">200 Per Page</option>
            </select>
            <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-mutedText/40">
                <i class="fas fa-chevron-down text-[8px]"></i>
            </div>
        </div>

        @if($showDateFilter)
            <div class="flex items-center gap-2 bg-primary/5 p-1 rounded-xl border border-primary/10">
                <input type="date" 
                       x-model="startDate" 
                       @change="updateTable()"
                       class="bg-transparent border-none text-xs font-bold text-mainText focus:ring-0 px-2 py-1 placeholder-mutedText">
                <span class="text-mutedText text-[10px] font-black uppercase tracking-widest">to</span>
                <input type="date" 
                       x-model="endDate" 
                       @change="updateTable()"
                       class="bg-transparent border-none text-xs font-bold text-mainText focus:ring-0 px-2 py-1 placeholder-mutedText">
            </div>
            
            <button @click="resetFilters()" 
                    class="p-2.5 bg-primary/5 hover:bg-primary/10 text-mutedText hover:text-primary rounded-xl transition-all border border-primary/10" 
                    title="Reset Filters">
                <i class="fas fa-undo-alt text-xs"></i>
            </button>
        @endif

        {{ $slot }}

        @if($showExport)
            <button @click="{{ $exportAction }}()" 
                    class="flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-lg shadow-green-600/20 active:scale-95 group">
                <svg class="w-4 h-4 group-hover:bounce" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                <span>Export Excel</span>
            </button>
        @endif
    </div>
</div>
