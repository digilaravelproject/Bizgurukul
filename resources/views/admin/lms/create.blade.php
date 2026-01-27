<x-admin-layout>
    <x-slot name="header">
        <h2 class="font-bold text-xl text-slate-800">Create New Course</h2>
    </x-slot>

    <div class="max-w-2xl bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
        <form action="{{ route('admin.courses.store') }}" method="POST">
            @csrf
            <div class="space-y-5">
                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Course Title</label>
                    <input type="text" name="title" required placeholder="Enter course name"
                        class="w-full border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500">
                </div>

                <div>
                    <label class="block text-sm font-bold text-slate-700 mb-1">Description</label>
                    <textarea name="description" rows="4" placeholder="What is this course about?"
                        class="w-full border-slate-200 rounded-xl focus:ring-2 focus:ring-indigo-500"></textarea>
                </div>

                <div class="flex space-x-3 pt-4 border-t border-slate-100">
                    <a href="{{ route('admin.courses.index') }}"
                        class="flex-1 text-center py-3 bg-slate-100 text-slate-600 rounded-xl font-bold hover:bg-slate-200">
                        Cancel
                    </a>
                    <button type="submit"
                        class="flex-1 py-3 bg-indigo-600 text-white rounded-xl font-bold hover:bg-indigo-700 shadow-lg shadow-indigo-200">
                        Create Course
                    </button>
                </div>
            </div>
        </form>
    </div>
</x-admin-layout>
