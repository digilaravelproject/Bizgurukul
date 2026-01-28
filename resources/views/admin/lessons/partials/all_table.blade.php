<div class="overflow-hidden bg-white border border-slate-200 shadow-sm rounded-2xl">
    <table class="w-full text-left border-collapse">
        <thead class="bg-slate-50/80 border-b border-slate-200">
            <tr>
                <th class="px-6 py-4 text-[11px] font-black text-slate-500 uppercase tracking-wider">Lesson Details</th>
                <th class="px-6 py-4 text-[11px] font-black text-slate-500 uppercase tracking-wider">Course Category</th>
                <th class="px-6 py-4 text-[11px] font-black text-slate-500 uppercase tracking-wider text-center">Video
                    Status</th>
                <th class="px-6 py-4 text-[11px] font-black text-slate-500 uppercase tracking-wider text-right">Actions
                </th>
            </tr>
        </thead>
        <tbody class="divide-y divide-slate-100">
            @forelse($lessons as $lesson)
                <tr class="hover:bg-slate-50/50 transition-all group">
                    <td class="px-6 py-4">
                        <div class="flex flex-col">
                            <span
                                class="text-sm font-bold text-slate-800 group-hover:text-[#0777be] transition-colors line-clamp-1">{{ $lesson->title }}</span>
                            <span class="text-[10px] text-slate-400 font-mono mt-0.5">ID: #{{ $lesson->id }} | Order:
                                {{ $lesson->order_column }}</span>
                        </div>
                    </td>
                    <td class="px-6 py-4">
                        <span
                            class="inline-flex items-center px-2.5 py-1 rounded-lg text-[11px] font-bold bg-slate-100 text-slate-600 border border-slate-200">
                            {{ $lesson->course->title ?? 'Unassigned' }}
                        </span>
                    </td>
                    <td class="px-6 py-4 text-center">
                        @if ($lesson->hls_path)
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-green-50 text-green-600 border border-green-100 uppercase tracking-tighter">
                                <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-1.5 animate-pulse"></span>
                                Secure HLS
                            </span>
                        @else
                            <span
                                class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-black bg-amber-50 text-amber-600 border border-amber-100 uppercase tracking-tighter">
                                <span class="w-1.5 h-1.5 rounded-full bg-amber-500 mr-1.5"></span>
                                No Video
                            </span>
                        @endif
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end items-center gap-2">
                            {{-- Edit Button --}}
                            <a href="{{ route('admin.lessons.edit', $lesson->id) }}"
                                class="p-2 text-slate-400 hover:text-[#0777be] hover:bg-blue-50 rounded-xl transition-all"
                                title="Edit Lesson">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>

                            {{-- Delete Button for Lesson --}}
                            <button type="button"
                                onclick="confirmLessonDelete({{ $lesson->id }}, '{{ addslashes($lesson->title) }}')"
                                class="p-2 text-slate-400 hover:text-red-600 transition-all">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>

                            {{-- Hidden Form: Iska ID match hona chahiye script se --}}
                            <form id="lesson-delete-form-{{ $lesson->id }}"
                                action="{{ route('admin.lessons.delete', $lesson->id) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="4" class="px-6 py-20 text-center">
                        <div class="flex flex-col items-center">
                            <svg class="w-12 h-12 text-slate-200 mb-3" fill="none" stroke="currentColor"
                                viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                                    d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" />
                            </svg>
                            <p class="text-slate-400 font-medium italic">Bhai, koi lessons nahi mile. Naya add karein!
                            </p>
                        </div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>

<div class="mt-6 lesson-pagination-wrapper">
    {{ $lessons->links() }}
</div>
