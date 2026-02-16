<div class="hidden md:block overflow-hidden rounded-2xl border border-primary/5 bg-surface shadow-sm relative animate-fade-in">
    <table class="w-full text-left text-sm">
        <thead class="bg-primary/5 text-[10px] uppercase font-bold text-mutedText border-b border-primary/5 tracking-widest">
            <tr>
                <th class="px-6 py-4">Package Name</th>
                <th class="px-6 py-4">Works On</th>
                <th class="px-6 py-4 text-red-500">Purchase Price</th>
                <th class="px-6 py-4 text-green-600">Discount Provided</th>
                <th class="px-6 py-4">Sales & Status</th>
                <th class="px-6 py-4 text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-primary/5">
            @forelse($packages as $package)
                <tr class="hover:bg-primary/[0.02] transition-colors group">
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div class="h-9 w-9 rounded-lg bg-primary/10 flex items-center justify-center text-primary font-black text-[10px]">PKG</div>
                            <div>
                                <div class="font-bold text-mainText text-sm uppercase tracking-wider">{{ $package->name }}</div>
                                <div class="text-[9px] text-mutedText italic uppercase tracking-tighter">{{ $package->type }} discount</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col gap-1">
                            @if(count($package->selected_courses ?? []) > 0) <span class="text-[10px] font-bold text-primary">{{ count($package->selected_courses) }} Courses</span> @endif
                            @if(count($package->selected_bundles ?? []) > 0) <span class="text-[10px] font-bold text-secondary">{{ count($package->selected_bundles) }} Bundles</span> @endif
                            @if(empty($package->selected_courses) && empty($package->selected_bundles))
                                <span class="text-[10px] font-black text-mutedText uppercase tracking-widest bg-navy px-2 py-1 rounded">General</span>
                            @endif
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-red-500 font-bold text-sm">₹{{ number_format($package->selling_price, 2) }}</span>
                    </td>
                    <td class="px-6 py-4">
                        <span class="text-green-600 font-black text-sm">
                            {{ $package->type == 'percentage' ? $package->discount_value . '%' : '₹' . number_format($package->discount_value, 2) }}
                        </span>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-[11px] font-bold text-mainText mb-0.5">Claims: {{ $package->used_count }}</div>
                        <span class="text-[9px] font-black {{ $package->is_active ? 'text-green-500' : 'text-red-500' }}">{{ $package->is_active ? 'LIVE' : 'PAUSED' }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-2 px-2">
                            <button @click="openModal('edit', {{ $package->id }})"
                                class="p-2 text-mutedText hover:text-primary hover:bg-primary/10 rounded-xl transition-all border border-transparent hover:border-primary/20 shadow-sm"
                                title="Edit Package">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                            </button>
                            <button @click="deletePackage({{ $package->id }})"
                                class="p-2 text-mutedText hover:text-red-600 hover:bg-red-500/10 rounded-xl transition-all border border-transparent hover:border-red-500/20 shadow-sm"
                                title="Delete Package">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                            </button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="6" class="px-6 py-12 text-center text-mutedText text-xs italic">No packages found.</td></tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- 2. MOBILE CARDS (Responsive) --}}
<div class="md:hidden space-y-4 animate-fade-in">
    @forelse($packages as $package)
        <div class="bg-surface border border-primary/5 rounded-2xl p-5 shadow-sm relative overflow-hidden">
            {{-- Status Strip --}}
            <div class="absolute left-0 top-0 bottom-0 w-1.5 {{ $package->is_active ? 'bg-green-500' : 'bg-red-500' }}"></div>

            <div class="flex items-center justify-between mb-4 pl-3">
                <div class="flex items-center gap-4">
                    <div class="h-10 w-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-black text-xs">PK</div>
                    <div>
                        <h3 class="text-mainText font-bold text-sm uppercase tracking-wider">{{ $package->name }}</h3>
                        <span class="text-[9px] font-black {{ $package->is_active ? 'text-green-500' : 'text-red-500' }} uppercase tracking-widest">{{ $package->is_active ? 'LIVE & ACTIVE' : 'PAUSED' }}</span>
                    </div>
                </div>
                <div class="flex gap-1">
                    <button @click="openModal('edit', {{ $package->id }})" class="p-2 text-mutedText hover:text-primary transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
                    <button @click="deletePackage({{ $package->id }})" class="p-2 text-red-500 hover:text-red-700 transition-colors"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 pl-3">
                <div class="bg-navy/30 p-3 rounded-xl border border-primary/5">
                    <p class="text-[9px] font-bold text-mutedText uppercase mb-1">Purchase Price</p>
                    <p class="text-red-500 font-bold text-sm">₹{{ number_format($package->selling_price, 2) }}</p>
                </div>
                <div class="bg-navy/30 p-3 rounded-xl border border-primary/5">
                    <p class="text-[9px] font-bold text-mutedText uppercase mb-1">Savings Value</p>
                    <p class="text-green-600 font-black text-sm">{{ $package->type == 'percentage' ? $package->discount_value . '%' : '₹' . number_format($package->discount_value, 2) }}</p>
                </div>
                <div class="col-span-2 bg-navy/30 p-3 rounded-xl border border-primary/5 flex justify-between items-center">
                    <p class="text-[9px] font-bold text-mutedText uppercase">Valid For</p>
                    <div class="flex gap-2">
                        @if(empty($package->selected_courses) && empty($package->selected_bundles))
                            <span class="text-[9px] font-black text-mutedText uppercase bg-navy px-2 py-1 rounded">General Scope</span>
                        @else
                            @if(count($package->selected_courses ?? []) > 0) <span class="text-[9px] font-bold text-primary italic">{{ count($package->selected_courses) }} Courses</span> @endif
                            @if(count($package->selected_bundles ?? []) > 0) <span class="text-[9px] font-bold text-secondary italic">{{ count($package->selected_bundles) }} Bundles</span> @endif
                        @endif
                    </div>
                </div>
            </div>
            <div class="mt-4 pl-3 flex justify-between items-center border-t border-primary/5 pt-3">
                <span class="text-[10px] text-mutedText italic font-medium uppercase tracking-tighter">{{ $package->type }} discount model</span>
                <span class="text-[10px] font-bold text-mainText">Total Sold: {{ $package->used_count }}</span>
            </div>
        </div>
    @empty
        <div class="text-center text-mutedText py-10 text-xs italic">No packages found.</div>
    @endforelse
</div>

@if($packages->hasPages())
    <div class="mt-8 flex items-center justify-between px-2 pagination">
        <span class="text-[10px] text-mutedText font-bold uppercase tracking-widest">Page {{ $packages->currentPage() }}</span>
        <div class="flex gap-2">
            @if(!$packages->onFirstPage()) <a href="{{ $packages->previousPageUrl() }}" class="px-4 py-2 bg-surface border rounded-xl text-[10px] font-bold text-mainText hover:bg-primary hover:text-white transition shadow-sm">PREVIOUS</a> @endif
            @if($packages->hasMorePages()) <a href="{{ $packages->nextPageUrl() }}" class="px-4 py-2 bg-surface border rounded-xl text-[10px] font-bold text-mainText hover:bg-primary hover:text-white transition shadow-sm">NEXT</a> @endif
        </div>
    </div>
@endif
