@extends('layouts.admin')

@section('content')
    <div class="max-w-5xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="font-bold text-xl text-slate-800">
                {{ isset($course) ? 'Edit Course' : 'Create New Course' }}
            </h2>
            <a href="{{ route('admin.courses.index') }}"
                class="px-4 py-2 text-sm font-bold text-slate-600 bg-slate-100 rounded-xl hover:bg-slate-200 transition">←
                Back</a>
        </div>

        <div class="bg-white p-8 rounded-2xl shadow-sm border border-slate-200">
            <form action="{{ route('admin.courses.store') }}" method="POST">
                @csrf

                {{-- Hidden ID field for Update --}}
                @if (isset($course))
                    <input type="hidden" name="id" value="{{ $course->id }}">
                @endif

                <div class="space-y-6">
                    {{-- Course Title --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Course Title</label>
                        <input type="text" name="title" required value="{{ old('title', $course->title ?? '') }}"
                            placeholder="Enter course name"
                            class="w-full rounded-xl border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-[#0777be]/30 focus:border-[#0777be]">
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Description</label>
                        <textarea name="description" rows="4" placeholder="What is this course about?"
                            class="w-full rounded-xl border-slate-200 px-4 py-2.5 focus:ring-2 focus:ring-[#0777be]/30 focus:border-[#0777be]">{{ old('description', $course->description ?? '') }}</textarea>
                    </div>

                    <div class="flex justify-end gap-3 pt-6 border-t border-slate-100">
                        <a href="{{ route('admin.courses.index') }}"
                            class="px-6 py-2.5 rounded-xl bg-slate-100 text-slate-700 font-semibold">Cancel</a>
                        <button type="submit"
                            class="px-6 py-2.5 rounded-xl bg-[#0777be] text-white font-semibold shadow-md hover:bg-[#0777be]/90 transition">
                            {{ isset($course) ? 'Update Course' : 'Create Course' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
@endsection
