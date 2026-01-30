<div class="animate-fade-in space-y-6">

    {{-- A. DESKTOP VIEW --}}
    <div
        class="hidden md:block bg-white border border-primary/5 rounded-[2rem] shadow-xl shadow-primary/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead>
                    <tr class="bg-primary/5 border-b border-primary/5">
                        <th class="px-8 py-6 text-[10px] font-black text-primary uppercase tracking-widest">Course Info
                        </th>
                        <th class="px-6 py-6 text-[10px] font-black text-primary uppercase tracking-widest">Category</th>
                        <th class="px-6 py-6 text-[10px] font-black text-primary uppercase tracking-widest">Stats</th>
                        <th class="px-6 py-5 text-[10px] font-black text-primary uppercase tracking-widest">Price</th>
                        <th class="px-6 py-5 text-[10px] font-black text-primary uppercase tracking-widest">Status</th>
                        <th class="px-8 py-6 text-[10px] font-black text-primary uppercase tracking-widest text-right">
                            Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary/5">
                    @forelse($courses as $course)
                        <tr class="group hover:bg-primary/[0.02] transition-colors duration-200">
                            {{-- Course --}}
                            <td class="px-8 py-5">
                                <div class="flex items-center gap-4">
                                    <div
                                        class="relative h-12 w-16 flex-shrink-0 rounded-xl overflow-hidden shadow-sm border border-primary/10">
                                        @if ($course->thumbnail)
                                            <img src="{{ $course->thumbnail }}"
                                                class="h-full w-full object-cover group-hover:scale-110 transition-transform duration-500">
                                        @else
                                            <div
                                                class="h-full w-full brand-gradient flex items-center justify-center text-white font-black text-xs">
                                                {{ strtoupper(substr($course->title, 0, 1)) }}
                                            </div>
                                        @endif
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-mainText line-clamp-1 max-w-[200px]">
                                            {{ $course->title }}</h4>
                                        <span
                                            class="inline-flex mt-1 items-center px-1.5 py-0.5 rounded text-[9px] font-bold bg-gray-100 text-mutedText">ID:
                                            #{{ $course->id }}</span>
                                    </div>
                                </div>
                            </td>

                            {{-- Category --}}
                            <td class="px-6 py-5">
                                <div class="flex flex-col text-xs font-bold text-mainText">
                                    <span>{{ $course->category?->name ?? '—' }}</span>
                                    @if ($course->subCategory)
                                        <span
                                            class="text-[10px] font-semibold text-mutedText mt-0.5">{{ $course->subCategory->name }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Stats --}}
                            <td class="px-6 py-5">
                                <div class="flex items-center gap-2 text-xs font-bold text-mutedText">
                                    <div class="p-1.5 rounded-lg bg-primary/5 text-primary">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                            </path>
                                        </svg>
                                    </div>
                                    {{ $course->lessons_count ?? 0 }} Lessons
                                </div>
                            </td>

                            {{-- Price --}}
                            <td class="px-6 py-5">
                                <span
                                    class="text-sm font-black text-mainText">₹{{ number_format($course->final_price ?? 0) }}</span>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-5">
                                @if ($course->is_published)
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-green-50 text-green-600 border border-green-200">Published</span>
                                @else
                                    <span
                                        class="inline-flex items-center px-2.5 py-1 rounded-lg text-[10px] font-black uppercase tracking-widest bg-gray-50 text-gray-500 border border-gray-200">Draft</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-8 py-5 text-right">
                                <a href="{{ route('admin.courses.edit', $course->id) }}"
                                    class="inline-flex items-center justify-center p-2.5 rounded-xl text-mutedText hover:text-primary hover:bg-primary/5 border border-transparent transition-all duration-200 group-hover:shadow-sm">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                        </path>
                                    </svg>
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-mutedText font-bold">No courses found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- B. MOBILE CARDS --}}
    <div class="md:hidden grid grid-cols-1 gap-4">
        @forelse($courses as $course)
            <div class="bg-white rounded-[1.5rem] p-5 shadow-sm border border-primary/10 relative overflow-hidden">
                <div class="flex gap-4">
                    <div
                        class="h-20 w-20 rounded-2xl flex-shrink-0 overflow-hidden shadow-inner border border-primary/5">
                        @if ($course->thumbnail)
                            <img src="{{ $course->thumbnail }}" class="h-full w-full object-cover">
                        @else
                            <div
                                class="h-full w-full brand-gradient flex items-center justify-center text-white font-bold text-sm">
                                {{ strtoupper(substr($course->title, 0, 1)) }}</div>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0 py-1">
                        <div class="flex justify-between items-start mb-1">
                            <span
                                class="text-[10px] font-bold px-2 py-0.5 rounded-md bg-primary/5 text-primary">{{ $course->category?->name ?? 'Uncategorized' }}</span>
                            <div class="h-2 w-2 rounded-full"
                                :class="{{ $course->is_published ? "'bg-green-500'" : "'bg-gray-300'" }}"></div>
                        </div>
                        <h3 class="text-sm font-bold text-mainText leading-tight line-clamp-2 mb-1">
                            {{ $course->title }}</h3>
                        <p class="text-xs font-black text-mainText">₹{{ number_format($course->final_price) }}</p>
                    </div>
                </div>
                <div class="mt-4 pt-3 border-t border-dashed border-primary/10 flex items-center justify-between">
                    <div class="flex items-center text-xs font-medium text-mutedText">
                        <svg class="w-3.5 h-3.5 mr-1 text-primary" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                            </path>
                        </svg>
                        {{ $course->lessons_count }} Lessons
                    </div>
                    <a href="{{ route('admin.courses.edit', $course->id) }}"
                        class="text-xs font-bold text-primary flex items-center">Edit Details →</a>
                </div>
            </div>
        @empty
            <div class="text-center py-10 text-mutedText text-sm">No courses found.</div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if ($courses->hasPages())
        <div
            class="px-6 py-4 bg-white border border-primary/5 rounded-[1.5rem] shadow-sm flex items-center justify-between">
            <span class="text-xs font-bold text-mutedText">Page <span
                    class="text-primary">{{ $courses->currentPage() }}</span></span>
            <div class="scale-90 origin-right">{{ $courses->links('pagination::simple-tailwind') }}</div>
        </div>
    @endif
</div>
