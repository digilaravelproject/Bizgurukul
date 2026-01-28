<div class="overflow-x-auto bg-white border border-slate-200 shadow-sm rounded-[2rem]">
    <table class="w-full text-left border-collapse min-w-[800px]">
        <thead class="bg-slate-50/80 border-b border-slate-200">
            <tr>
                <th class="px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest">Course Info</th>
                <th class="px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest text-center">Lessons
                    Count</th>
                <th class="px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest">Pricing</th>
                <th class="px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest text-center">
                    Visibility</th>
                <th class="px-6 py-5 text-[11px] font-black text-slate-500 uppercase tracking-widest text-right">Actions
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($courses as $course)
                <tr class="hover:bg-slate-50/50 transition-all group">
                    {{-- Course Info with Thumbnail --}}
                    <td class="px-6 py-4">
                        <div class="flex items-center gap-4">
                            <div
                                class="w-12 h-12 rounded-xl bg-slate-100 overflow-hidden border border-slate-200 flex-shrink-0 shadow-sm">
                                @if ($course->thumbnail)
                                    <img src="{{ asset('storage/' . $course->thumbnail) }}"
                                        class="w-full h-full object-cover">
                                @else
                                    <div
                                        class="w-full h-full flex items-center justify-center text-slate-400 font-bold text-[10px] uppercase">
                                        No Img</div>
                                @endif
                            </div>
                            <div>
                                <p
                                    class="text-sm font-black text-slate-800 group-hover:text-[#0777be] transition-colors line-clamp-1">
                                    {{ $course->title }}</p>
                                <p class="text-[10px] text-slate-400 font-bold tracking-tight uppercase">Created:
                                    {{ $course->created_at->format('d M, Y') }}</p>
                            </div>
                        </div>
                    </td>

                    {{-- Lessons Count Badge --}}
                    <td class="px-6 py-4 text-center">
                        <span
                            class="inline-flex items-center px-3 py-1 rounded-lg text-[11px] font-black bg-indigo-50 text-indigo-600 border border-indigo-100">
                            {{ $course->lessons_count }} Lessons
                        </span>
                    </td>

                    {{-- Price --}}
                    <td class="px-6 py-4 font-black text-slate-700 text-sm">
                        ₹{{ number_format($course->price, 2) }}
                    </td>

                    {{-- Status Badge --}}
                    <td class="px-6 py-4 text-center">
                        @if ($course->is_published)
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-green-50 text-green-600 border border-green-100 uppercase">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5"></span> Published
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-2.5 py-1 rounded-full text-[10px] font-black bg-slate-100 text-slate-400 border border-slate-200 uppercase tracking-tighter">
                                <span class="w-1.5 h-1.5 rounded-full bg-slate-400 mr-1.5"></span> Draft
                            </span>
                        @endif
                    </td>

                    {{-- Action Buttons --}}
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end items-center gap-2">
                            <a href="{{ route('admin.courses.edit', $course->id) }}"
                                class="p-2 text-slate-400 hover:text-[#0777be] hover:bg-blue-50 rounded-xl transition-all"
                                title="Edit Course">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            <button onclick="confirmDelete({{ $course->id }}, '{{ addslashes($course->title) }}')"
                                class="p-2 text-slate-400 hover:text-red-600 hover:bg-red-50 rounded-xl transition-all"
                                title="Delete Course">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                            <form id="delete-form-{{ $course->id }}"
                                action="{{ route('admin.courses.delete', $course->id) }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-6 py-24 text-center">
                        <div class="flex flex-col items-center">
                            <p class="text-slate-400 font-bold italic tracking-wider uppercase text-[10px]">No records
                                found in database</p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

{{-- Pagination Links Wrapper --}}
<div class="mt-6 pagination-wrapper">
    {{ $courses->links() }}
</div>

<style>
    /* Mobile scrollbar styling for a cleaner look */
    .overflow-x-auto::-webkit-scrollbar {
        height: 5px;
    }

    .overflow-x-auto::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 10px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb {
        background: #cbd5e1;
        border-radius: 10px;
    }

    .overflow-x-auto::-webkit-scrollbar-thumb:hover {
        background: #94a3b8;
    }
</style>
