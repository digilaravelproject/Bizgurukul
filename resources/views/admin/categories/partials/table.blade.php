{{-- 3A. DESKTOP TABLE --}}
<div class="hidden md:block overflow-hidden rounded-2xl border border-primary/5 bg-surface shadow-sm relative animate-fade-in">
    <table class="w-full text-left text-sm">
        <thead class="bg-primary/5 text-[10px] uppercase font-bold text-mutedText border-b border-primary/5 tracking-widest">
            <tr>
                <th class="px-6 py-4">Category Details</th>
                <th class="px-6 py-4">Sub-Categories</th>
                <th class="px-6 py-4">Status</th>
                <th class="px-6 py-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-primary/5">
            @forelse($categories as $category)
                <tr class="hover:bg-primary/[0.02] transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="h-9 w-9 rounded-lg bg-primary/10 flex items-center justify-center text-primary font-bold text-sm">
                                {{ substr($category->name, 0, 1) }}
                            </div>
                            <div>
                                <div class="font-bold text-mainText text-sm group-hover:text-primary transition-colors">{{ $category->name }}</div>
                                <div class="text-[10px] text-mutedText">{{ $category->slug }}</div>
                            </div>
                        </div>
                    </td>

                    <td class="px-6 py-4">
                        <div class="flex flex-wrap gap-1.5 max-w-xs">
                            @forelse($category->subCategories as $sub)
                                <button @click.stop="openModal('edit', {{ $sub }})"
                                    class="px-2.5 py-1 rounded-md bg-navy border border-primary/10 text-[10px] font-bold text-mutedText hover:bg-primary hover:text-customWhite hover:border-primary transition cursor-pointer">
                                    {{ $sub->name }}
                                </button>
                            @empty
                                <span class="text-[10px] text-mutedText/40 italic">Standalone</span>
                            @endforelse
                        </div>
                    </td>

                    <td class="px-6 py-4">
                        @if($category->is_active)
                            <span class="inline-flex items-center rounded-full bg-green-50 px-2.5 py-1 text-[10px] font-bold text-green-600 border border-green-100">ACTIVE</span>
                        @else
                            <span class="inline-flex items-center rounded-full bg-red-50 px-2.5 py-1 text-[10px] font-bold text-secondary border border-red-100">INACTIVE</span>
                        @endif
                    </td>

                    <td class="px-6 py-4 text-right">
                        <button @click="openModal('edit', {{ $category }})" class="p-2 text-mutedText hover:text-primary hover:bg-primary/5 rounded-lg transition-all">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-12 text-center text-mutedText text-xs italic">No categories found matching your search.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- 3B. MOBILE CARDS --}}
<div class="md:hidden space-y-4 animate-fade-in">
    @forelse($categories as $category)
        <div class="bg-surface border border-primary/5 rounded-2xl p-4 shadow-sm">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="h-8 w-8 rounded bg-primary/10 flex items-center justify-center text-primary font-bold text-xs">
                        {{ substr($category->name, 0, 1) }}
                    </div>
                    <h3 class="text-mainText font-bold text-sm">{{ $category->name }}</h3>
                </div>
                <button @click="openModal('edit', {{ $category }})" class="text-mutedText p-1.5 bg-navy rounded-lg border border-primary/10"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
            </div>

            <div class="space-y-3">
                <div class="bg-navy p-3 rounded-xl border border-primary/5">
                    <span class="text-[9px] font-bold text-mutedText block mb-2 uppercase tracking-tighter">Sub-Categories</span>
                    <div class="flex flex-wrap gap-1.5">
                        @forelse($category->subCategories as $sub)
                            <button @click.stop="openModal('edit', {{ $sub }})" class="px-2 py-1 rounded bg-surface border border-primary/10 text-[9px] font-bold text-mainText">{{ $sub->name }}</button>
                        @empty
                            <span class="text-[9px] text-mutedText/40 italic">None</span>
                        @endforelse
                    </div>
                </div>
                <div class="flex items-center justify-between text-[10px]">
                    <span class="text-mutedText uppercase font-bold tracking-widest">Status</span>
                    <span class="{{ $category->is_active ? 'text-green-600' : 'text-secondary' }} font-bold">{{ $category->is_active ? 'ACTIVE' : 'INACTIVE' }}</span>
                </div>
            </div>
        </div>
    @empty
        <div class="text-center text-mutedText py-10 text-xs italic">No records found.</div>
    @endforelse
</div>

{{-- PAGINATION --}}
@if($categories->hasPages())
    <div class="mt-8 flex items-center justify-between px-2">
        <span class="text-[10px] text-mutedText font-bold uppercase tracking-widest">Page {{ $categories->currentPage() }}</span>
        <div class="flex gap-2">
            @if($categories->onFirstPage())
                <span class="px-4 py-2 bg-surface/50 border border-primary/5 rounded-xl text-[10px] font-bold text-mutedText opacity-50 cursor-not-allowed">PREVIOUS</span>
            @else
                <a href="{{ $categories->previousPageUrl() }}" class="px-4 py-2 bg-surface border border-primary/10 rounded-xl text-[10px] font-bold text-mainText hover:bg-primary hover:text-customWhite transition shadow-sm">PREVIOUS</a>
            @endif

            @if($categories->hasMorePages())
                <a href="{{ $categories->nextPageUrl() }}" class="px-4 py-2 bg-surface border border-primary/10 rounded-xl text-[10px] font-bold text-mainText hover:bg-primary hover:text-customWhite transition shadow-sm">NEXT PAGE</a>
            @else
                <span class="px-4 py-2 bg-surface/50 border border-primary/5 rounded-xl text-[10px] font-bold text-mutedText opacity-50 cursor-not-allowed">NEXT PAGE</span>
            @endif
        </div>
    </div>
@endif
