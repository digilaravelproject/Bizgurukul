<div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead class="border-b border-gray-200 bg-gray-50">
                <tr>
                    <th class="px-6 py-4 text-xs font-bold tracking-wider text-gray-500 uppercase">Course Details</th>
                    <th class="px-6 py-4 text-xs font-bold tracking-wider text-center text-gray-500 uppercase">Lessons
                        Count</th>
                    <th class="px-6 py-4 text-xs font-bold tracking-wider text-right text-gray-500 uppercase">Actions
                    </th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
                @forelse($courses as $course)
                    <tr class="transition-colors hover:bg-gray-50/80 group">
                        <td class="px-6 py-4">
                            <span
                                class="text-sm font-bold text-gray-900 group-hover:text-[#0777be]">{{ $course->title }}</span>
                        </td>
                        <td class="px-6 py-4 text-center">
                            <span class="px-3 py-1 text-xs font-bold text-[#0777be] bg-blue-50 rounded-full">
                                {{ $course->lessons_count }} Lessons
                            </span>
                        </td>
                        <td class="px-6 py-4 text-right">
                            <button onclick="confirmDelete({{ $course->id }})"
                                class="text-sm font-bold text-red-500 hover:underline">Delete</button>
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
</div>

<div class="mt-4 pagination-wrapper">
    {{ $courses->links() }}
</div>
