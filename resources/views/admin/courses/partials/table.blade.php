<div class="animate-[fadeIn_0.5s_ease-out]">

    {{-- A. DESKTOP VIEW (Table) --}}
    <div
        class="hidden md:block overflow-hidden rounded-2xl border border-white/5 bg-navy/40 backdrop-blur-md shadow-xl relative">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-sm text-mutedText">
                <thead class="bg-white/5 text-xs uppercase font-bold text-white border-b border-white/5 tracking-wider">
                    <tr>
                        <th class="px-6 py-5">Course</th>
                        <th class="px-6 py-5">Category</th>
                        <th class="px-6 py-5">Stats</th>
                        <th class="px-6 py-5">Price</th>
                        <th class="px-6 py-5">Status</th>
                        <th class="px-6 py-5 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-white/5">
                    @forelse($courses as $course)
                        <tr class="hover:bg-white/[0.02] transition-colors group">

                            {{-- Course Info --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-4">
                                    {{-- THUMBNAIL LOGIC --}}
                                    <div class="relative h-12 w-20 flex-shrink-0">
                                        @if ($course->thumbnail)
                                            <img src="{{ $course->thumbnail }}"
                                                class="h-full w-full object-cover rounded-lg ring-1 ring-white/10 shadow-sm"
                                                alt="Thumb">
                                        @else
                                            <div
                                                class="h-full w-full rounded-lg bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border border-white/10 flex items-center justify-center text-white font-bold text-sm tracking-wider shadow-inner">
                                                {{ strtoupper(substr($course->title, 0, 2)) }}
                                            </div>
                                        @endif
                                    </div>

                                    <div>
                                        <div class="font-bold text-white text-sm line-clamp-1 max-w-[200px]"
                                            title="{{ $course->title }}">
                                            {{ $course->title }}
                                        </div>
                                        <div class="text-[10px] text-mutedText mt-0.5 font-mono">ID:
                                            #{{ $course->id }}</div>
                                    </div>
                                </div>
                            </td>

                            {{-- Category --}}
                            <td class="px-6 py-4">
                                <div class="flex flex-col">
                                    <span
                                        class="text-xs text-white font-medium">{{ $course->category?->name ?? 'Uncategorized' }}</span>
                                    @if ($course->subCategory)
                                        <span class="text-[10px] text-mutedText">{{ $course->subCategory->name }}</span>
                                    @endif
                                </div>
                            </td>

                            {{-- Stats (Lessons) --}}
                            <td class="px-6 py-4">
                                <div class="flex items-center gap-1.5 text-xs font-medium text-slate-300">
                                    <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M14.752 11.168l-3.197-2.132A1 1 0 0010 9.87v4.263a1 1 0 001.555.832l3.197-2.132a1 1 0 000-1.664z">
                                        </path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                    </svg>
                                    {{ $course->lessons_count ?? 0 }} Lessons
                                </div>
                            </td>

                            {{-- Price --}}
                            <td class="px-6 py-4">
                                <span
                                    class="font-mono text-white font-bold">₹{{ number_format($course->final_price ?? 0, 0) }}</span>
                            </td>

                            {{-- Status --}}
                            <td class="px-6 py-4">
                                @if ($course->is_published)
                                    <span
                                        class="inline-flex items-center rounded-md bg-green-500/10 px-2.5 py-1 text-xs font-bold text-green-400 border border-green-500/20">Published</span>
                                @else
                                    <span
                                        class="inline-flex items-center rounded-md bg-amber-500/10 px-2.5 py-1 text-xs font-bold text-amber-400 border border-amber-500/20">Draft</span>
                                @endif
                            </td>

                            {{-- Actions --}}
                            <td class="px-6 py-4 text-right">
                                <div class="flex items-center justify-end gap-2 opacity-90">
                                    <a href="{{ route('admin.courses.edit', $course->id) }}"
                                        class="px-3 py-1.5 rounded-lg bg-primary/10 text-primary border border-primary/20 text-xs font-bold hover:bg-primary hover:text-white transition flex items-center gap-1">
                                        <span>Edit</span>
                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                            </path>
                                        </svg>
                                    </a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-mutedText">
                                <div class="flex flex-col items-center justify-center opacity-50">
                                    <svg class="w-12 h-12 mb-3" fill="none" stroke="currentColor"
                                        viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253">
                                        </path>
                                    </svg>
                                    <p>No courses found matching your criteria.</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- B. MOBILE VIEW (Cards) --}}
    <div class="md:hidden space-y-4">
        @forelse($courses as $course)
            <div class="bg-[#1E293B] border border-white/10 rounded-2xl p-4 shadow-lg relative overflow-hidden">
                {{-- Top: Image & Status --}}
                <div class="flex items-start justify-between mb-3">
                    <div class="h-16 w-24 flex-shrink-0">
                        @if ($course->thumbnail)
                            <img src="{{ asset('storage/' . $course->thumbnail) }}"
                                class="h-full w-full object-cover rounded-lg border border-white/10" alt="Thumb">
                        @else
                            <div
                                class="h-full w-full rounded-lg bg-gradient-to-br from-indigo-500/20 to-purple-500/20 border border-white/10 flex items-center justify-center text-white font-bold text-lg shadow-inner">
                                {{ strtoupper(substr($course->title, 0, 2)) }}
                            </div>
                        @endif
                    </div>

                    @if ($course->is_published)
                        <span
                            class="inline-flex items-center rounded-md bg-green-500/10 px-2 py-1 text-[10px] font-bold text-green-400 border border-green-500/20">Published</span>
                    @else
                        <span
                            class="inline-flex items-center rounded-md bg-amber-500/10 px-2 py-1 text-[10px] font-bold text-amber-400 border border-amber-500/20">Draft</span>
                    @endif
                </div>

                {{-- Middle: Title & Category --}}
                <div class="mb-4">
                    <h3 class="text-white font-bold text-sm mb-1 line-clamp-1">{{ $course->title }}</h3>
                    <p class="text-xs text-mutedText flex items-center gap-1">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z">
                            </path>
                        </svg>
                        {{ $course->category?->name ?? 'No Category' }}
                    </p>
                </div>

                {{-- Bottom Grid: Price, Lessons, Action --}}
                <div class="bg-white/5 rounded-xl p-3 border border-white/5 grid grid-cols-3 gap-2 items-center">
                    <div class="text-center border-r border-white/10">
                        <p class="text-[10px] uppercase text-mutedText font-bold">Price</p>
                        <p class="text-xs text-white font-mono font-bold">₹{{ number_format($course->final_price, 0) }}
                        </p>
                    </div>
                    <div class="text-center border-r border-white/10">
                        <p class="text-[10px] uppercase text-mutedText font-bold">Lessons</p>
                        <p class="text-xs text-white font-bold">{{ $course->lessons_count }}</p>
                    </div>
                    <div>
                        <a href="{{ route('admin.courses.edit', $course->id) }}"
                            class="flex items-center justify-center w-full py-1.5 rounded-lg bg-primary text-white text-xs font-bold hover:bg-indigo-600 transition">
                            Edit
                        </a>
                    </div>
                </div>
            </div>
        @empty
            <div class="text-center text-mutedText py-8">No courses found.</div>
        @endforelse
    </div>

    {{-- PAGINATION --}}
    @if ($courses->hasPages())
        <div
            class="mt-4 px-4 py-3 border border-white/5 bg-navy/40 rounded-xl flex items-center justify-between shadow-lg pagination-wrapper">
            <span class="text-xs text-mutedText font-medium">
                Showing <span class="text-white font-bold">{{ $courses->firstItem() }}</span> - <span
                    class="text-white font-bold">{{ $courses->lastItem() }}</span> of <span
                    class="text-white font-bold">{{ $courses->total() }}</span>
            </span>
            <div class="flex gap-1 pagination">
                {{ $courses->links('pagination::simple-tailwind') }}
            </div>
        </div>
    @endif
</div>
