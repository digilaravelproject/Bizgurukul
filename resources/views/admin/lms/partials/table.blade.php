<div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 border-b border-gray-200">
            <tr>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider">Course Details</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-center">Lessons</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase tracking-wider text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($courses as $course)
                <tr class="hover:bg-gray-50/80 transition group">
                    <td class="px-6 py-4 font-bold text-gray-900 group-hover:text-[#0777be]">{{ $course->title }}</td>
                    <td class="px-6 py-4 text-center">
                        <span class="px-3 py-1 text-xs font-bold text-[#0777be] bg-blue-50 rounded-full">
                            {{ $course->lessons_count }} Lessons
                        </span>
                    </td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-3">
                            {{-- Edit Button --}}
                            <a href="{{ route('admin.courses.create', $course->id) }}"
                                class="p-2 text-gray-400 hover:text-blue-600 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
                                </svg>
                            </a>
                            {{-- Delete Button --}}
                            <button onclick="confirmDelete({{ $course->id }}, '{{ addslashes($course->title) }}')"
                                class="p-2 text-gray-400 hover:text-red-500 transition">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                </svg>
                            </button>
                            {{-- Hidden Form --}}
                            <form id="delete-form-{{ $course->id }}"
                                action="{{ route('admin.courses.delete', $course->id) }}" method="POST" class="hidden">
                                @csrf
                                @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-6 py-12 text-center text-gray-400">No courses found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4 pagination-wrapper">{{ $courses->links() }}</div>
