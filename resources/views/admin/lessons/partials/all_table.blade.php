<div class="overflow-hidden bg-white border border-gray-200 shadow-sm rounded-xl">
    <table class="w-full text-left border-collapse">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Lesson Title</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase">Course Name</th>
                <th class="px-6 py-4 text-xs font-bold text-gray-500 uppercase text-right">Actions</th>
            </tr>
        </thead>
        <tbody class="divide-y divide-gray-100">
            @forelse($lessons as $lesson)
                <tr class="hover:bg-gray-50 transition">
                    <td class="px-6 py-4 font-bold text-gray-900">{{ $lesson->title }}</td>
                    <td class="px-6 py-4 text-sm text-gray-500">{{ $lesson->course->title ?? 'N/A' }}</td>
                    <td class="px-6 py-4 text-right">
                        <div class="flex justify-end gap-3">
                            <a href="{{ route('admin.lessons.edit', $lesson->id) }}"
                                class="text-blue-500 hover:underline text-sm font-bold transition">Edit</a>
                            <button onclick="confirmDelete({{ $lesson->id }}, '{{ addslashes($lesson->title) }}')"
                                class="text-red-500 hover:underline text-sm font-bold transition">Delete</button>
                            <form id="delete-form-{{ $lesson->id }}"
                                action="{{ route('admin.lessons.delete', $lesson->id) }}" method="POST" class="hidden">
                                @csrf @method('DELETE')
                            </form>
                        </div>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="3" class="px-6 py-12 text-center text-gray-400">No lessons found.</td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
<div class="mt-4 lesson-pagination-wrapper">
    {{ $lessons->links() }}
</div>
