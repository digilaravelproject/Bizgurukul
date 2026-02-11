<div class="hidden md:block overflow-hidden rounded-2xl border border-primary/5 bg-surface shadow-sm relative animate-fade-in">
    <table class="w-full text-left text-sm">
        <thead class="bg-primary/5 text-[10px] uppercase font-bold text-mutedText border-b border-primary/5 tracking-widest">
            <tr>
                <th class="px-6 py-4 text-primary">Package Identity</th>
                <th class="px-6 py-4">Structure</th>
                <th class="px-6 py-4">Pricing</th>
                <th class="px-6 py-4">Stats</th>
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
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span class="text-[10px] text-red-400 line-through">₹{{ number_format($package->price) }}</span>
                            <span class="text-green-600 font-black text-sm">₹{{ number_format($package->discount_price) }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <div class="text-[11px] font-bold text-mainText">Sold: {{ $package->used_count }}</div>
                        <span class="text-[9px] font-black {{ $package->is_active ? 'text-green-500' : 'text-red-500' }}">{{ $package->is_active ? 'ACTIVE' : 'INACTIVE' }}</span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-1 opacity-0 group-hover:opacity-100 transition-all">
                            <button @click="openModal('edit', {{ $package->id }})" class="p-2 text-mutedText hover:text-primary transition-all"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
                            <button @click="deletePackage({{ $package->id }})" class="p-2 text-mutedText hover:text-red-600 transition-all"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                        </div>
                    </td>
                </tr>
            @empty
                <tr><td colspan="5" class="px-6 py-12 text-center text-mutedText text-xs italic">No packages found.</td></tr>
            @endforelse
        </tbody>
    </table>
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
