<div class="animate-fade-in">
    @if ($bundles->count() > 0)
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
            @foreach ($bundles as $bundle)
                <div
                    class="group relative bg-surface rounded-[2rem] border border-primary/10 shadow-lg shadow-primary/5 hover:shadow-2xl hover:shadow-primary/20 hover:-translate-y-1.5 transition-all duration-500 ease-out flex flex-col overflow-hidden h-full">

                    <div class="relative h-52 w-full overflow-hidden bg-primary/5">
                        @if ($bundle->thumbnail_url)
                            <img src="{{ $bundle->thumbnail_url }}"
                                class="h-full w-full object-cover transition-transform duration-700 ease-out group-hover:scale-110 will-change-transform">
                        @else
                            <div
                                class="h-full w-full brand-gradient flex items-center justify-center transition-transform duration-700 ease-out group-hover:scale-110">
                                <span
                                    class="text-5xl font-black text-white/20">{{ strtoupper(substr($bundle->title, 0, 4)) }}</span>
                            </div>
                        @endif

                        <div class="absolute inset-0 bg-gradient-to-t from-black/60 via-transparent to-transparent opacity-60"></div>

                        <div class="absolute top-4 left-4 z-10 flex gap-2">
                             <span class="inline-flex items-center px-3 py-1.5 rounded-full bg-navy/80 backdrop-blur-md border border-white/10 text-[10px] font-black text-customWhite shadow-sm">
                                 Slab: {{ $bundle->preference_index ?? 0 }}
                            </span>
                        </div>

                        <div class="absolute top-4 right-4 z-10">
                            @if ($bundle->is_published)
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-surface/95 backdrop-blur-md border border-white/20 shadow-md">
                                    <span class="relative flex h-2 w-2">
                                        <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                                        <span class="relative inline-flex rounded-full h-2 w-2 bg-green-500"></span>
                                    </span>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-green-700">Live</span>
                                </span>
                            @else
                                <span class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-surface/95 backdrop-blur-md border border-white/20 shadow-md">
                                    <span class="h-2 w-2 rounded-full bg-gray-400"></span>
                                    <span class="text-[10px] font-extrabold uppercase tracking-wider text-gray-600">Draft</span>
                                </span>
                            @endif
                        </div>

                        <div class="absolute bottom-4 left-4 z-10">
                            <span class="inline-block px-3 py-1 rounded-lg bg-black/40 backdrop-blur-md text-[11px] font-bold text-customWhite border border-white/10 shadow-sm">
                                Course Bundle
                            </span>
                        </div>
                    </div>

                    <div class="p-6 flex flex-col flex-1 relative">
                        <h3 class="text-lg font-bold text-mainText leading-snug line-clamp-2 mb-3 group-hover:text-primary transition-colors duration-300">
                            {{ $bundle->title }}
                        </h3>

                        <div class="flex items-center gap-4 mb-6">
                            <div class="flex items-center gap-1.5 text-xs font-semibold text-mutedText bg-primary/5 px-2 py-1 rounded-md">
                                <svg class="w-3.5 h-3.5 text-primary" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253" />
                                </svg>
                                {{ $bundle->courses_count ?? 0 }} Courses
                            </div>
                            @if($bundle->child_bundles_count > 0)
                                <span class="text-mutedText/30">|</span>
                                <div class="text-xs font-semibold text-mutedText truncate max-w-[100px]">
                                    {{ $bundle->child_bundles_count }} Bundles
                                </div>
                            @endif
                        </div>

                        <div class="mt-auto pt-4 border-t border-dashed border-primary/10 flex items-center justify-between">
                            <div class="flex flex-col">
                                @php
                                    $discountAmount = 0;
                                    if ($bundle->discount_type == 'percent' || $bundle->discount_type == 'percentage') {
                                        $discountAmount = ($bundle->website_price * $bundle->discount_value) / 100;
                                    } else {
                                        $discountAmount = $bundle->discount_value;
                                    }
                                    $bundleFinalPrice = $bundle->website_price - $discountAmount;
                                @endphp
                                <div class="flex items-center gap-2">
                                    <span class="text-xl font-extrabold text-primary">₹{{ number_format($bundleFinalPrice) }}</span>
                                    @if ($discountAmount > 0)
                                        <span class="px-1.5 py-0.5 rounded-lg bg-green-500/10 text-[9px] font-black text-green-600 uppercase tracking-tighter">
                                            -₹{{ number_format($discountAmount) }} OFF
                                        </span>
                                    @endif
                                </div>
                                <div class="flex flex-col mt-1">
                                    <span class="text-[10px] font-bold text-mutedText flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-mutedText/20"></span>
                                        Web: ₹{{ number_format($bundle->website_price) }}
                                    </span>
                                    <span class="text-[10px] font-bold text-mutedText flex items-center gap-1.5">
                                        <span class="w-1.5 h-1.5 rounded-full bg-mutedText/20"></span>
                                        Aff: ₹{{ number_format($bundle->affiliate_price) }}
                                    </span>
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <a href="{{ route('admin.bundles.edit', $bundle->id) }}" title="Edit Bundle"
                                    class="h-10 w-10 flex items-center justify-center rounded-xl bg-primary/5 text-primary hover:bg-primary hover:text-customWhite transition-all duration-300 shadow-sm hover:shadow-lg hover:-translate-y-0.5">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                    </svg>
                                </a>

                                <button onclick="confirmDelete('{{ $bundle->id }}')" title="Delete Bundle"
                                    class="h-10 w-10 flex items-center justify-center rounded-xl bg-secondary/10 text-secondary hover:bg-secondary hover:text-customWhite transition-all duration-300 shadow-sm hover:shadow-lg hover:-translate-y-0.5">
                                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                    </svg>
                                </button>

                                <form id="delete-form-{{ $bundle->id }}" action="{{ route('admin.bundles.destroy', $bundle->id) }}" method="POST" class="hidden">
                                    @csrf @method('DELETE')
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        @if ($bundles->hasPages())
            <div class="mt-10 px-6 py-4 bg-surface border border-primary/10 rounded-[1.5rem] shadow-lg shadow-primary/5 flex flex-col sm:flex-row items-center justify-between gap-4">
                <span class="text-xs font-bold text-mutedText">
                    Showing Page <span class="text-primary">{{ $bundles->currentPage() }}</span> of {{ $bundles->lastPage() }}
                </span>
                <div class="scale-90 origin-center sm:origin-right w-full sm:w-auto flex justify-center">
                    {{ $bundles->links('pagination::simple-tailwind') }}
                </div>
            </div>
        @endif
    @else
        <div class="flex flex-col items-center justify-center py-24 bg-surface border-2 border-dashed border-primary/10 rounded-[2.5rem] text-center animate-fade-in">
            <div class="h-24 w-24 rounded-full bg-primary/5 flex items-center justify-center mb-6 animate-bounce-slow ring-8 ring-primary/5">
                <svg class="w-12 h-12 text-primary/50" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                </svg>
            </div>
            <h3 class="text-xl font-extrabold text-mainText mb-2">No Bundles Found</h3>
            <p class="text-sm text-mutedText font-medium max-w-xs mx-auto mb-8">Start creating bundles to group your courses.</p>
            <a href="{{ route('admin.bundles.create') }}" class="inline-flex items-center justify-center px-6 py-3 rounded-xl bg-primary text-customWhite text-sm font-bold shadow-lg shadow-primary/30 hover:bg-secondary hover:-translate-y-1 transition-all duration-300">
                Create First Bundle →
            </a>
        </div>
    @endif
</div>
