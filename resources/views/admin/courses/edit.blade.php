@extends('layouts.admin')
@section('title', 'Edit Course')

@section('content')
<div class="max-w-6xl mx-auto font-sans text-mainText">

    {{-- Header --}}
    <div class="mb-8 flex flex-col md:flex-row justify-between items-start md:items-end gap-4">
        <div>
            <a href="{{ route('admin.courses.index') }}" class="text-xs font-bold text-mutedText hover:text-primary transition-colors flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                Back to Dashboard
            </a>
            <h2 class="text-3xl font-black text-mainText tracking-tight">Edit Course</h2>
            <p class="text-sm font-medium text-primary mt-1">{{ $course->title }}</p>
        </div>

        @if($course->is_published)
            <span class="px-4 py-1.5 rounded-full bg-green-100 text-green-700 text-[10px] font-black uppercase tracking-widest border border-green-200 flex items-center gap-2 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-green-500 animate-pulse"></span> Published
            </span>
        @else
            <span class="px-4 py-1.5 rounded-full bg-gray-100 text-gray-600 text-[10px] font-black uppercase tracking-widest border border-gray-200 flex items-center gap-2 shadow-sm">
                <span class="w-2 h-2 rounded-full bg-gray-400"></span> Draft Mode
            </span>
        @endif
    </div>

    {{-- Wizard Steps --}}
    @include('admin.courses.partials._wizard_steps', ['activeTab' => $activeTab, 'course' => $course])

    {{-- Dynamic Content --}}
    <div class="transition-all duration-300">
        @if($activeTab === 'basic')
            @include('admin.courses.partials._basic')
        @elseif($activeTab === 'lessons')
            @include('admin.courses.partials._lessons')
        @elseif($activeTab === 'resources')
            @include('admin.courses.partials._resources')
        @elseif($activeTab === 'settings')
            @include('admin.courses.partials._settings')
        @endif
    </div>

</div>

<script>
    // Shared functions for Edit page
    async function fetchSubCategories(catId) {
        const subSelector = document.getElementById('sub_selector');

        if(!catId) {
            subSelector.innerHTML = '<option value="">Select Sub Category</option>';
            return;
        }

        try {
            const res = await fetch(`/admin/courses/sub-categories/${catId}`);
            const data = await res.json();
            let currentSub = '{{ $course->sub_category_id }}';

            subSelector.innerHTML = '<option value="">Select Sub Category</option>';
            data.forEach(item => {
                let selected = item.id == currentSub ? 'selected' : '';
                subSelector.innerHTML += `<option value="${item.id}" ${selected}>${item.name}</option>`;
            });
        } catch (error) {
            console.error("Error fetching subcategories:", error);
        }
    }
</script>
@endsection
