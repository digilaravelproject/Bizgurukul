@props([
    'records' => null,
    'perPageOptions' => [20, 50, 100, 200]
])

<div class="px-8 py-5 flex flex-col md:flex-row justify-between items-center gap-4 border-t border-primary/5">
    {{-- Left: Record Status --}}
    <div class="flex items-center gap-4">
        @if($records)
            <span class="text-[10px] font-black uppercase text-mutedText tracking-widest bg-primary/5 px-4 py-2 rounded-xl border border-primary/10">
                Total Records: <span class="text-primary">{{ $records->total() }}</span>
            </span>
            <span class="text-[10px] font-black uppercase text-mutedText tracking-widest italic opacity-60">
                Showing {{ $records->firstItem() }}-{{ $records->lastItem() }} of {{ $records->total() }}
            </span>
        @endif
    </div>

    {{-- Right: Pagination Links (REDUCED SIZE) --}}
    <div id="pagination-container" class="flex items-center gap-2">
        @if($records)
            {{ $records->links('components.admin.table.pagination_links') }}
        @endif
    </div>
</div>
