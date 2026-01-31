@extends('layouts.admin')
@section('title', 'Create New Course')

@section('content')
<div class="max-w-6xl mx-auto font-sans text-mainText">

    {{-- Header --}}
    <div class="mb-8">
        <a href="{{ route('admin.courses.index') }}" class="text-xs font-bold text-mutedText hover:text-primary transition-colors flex items-center gap-1 mb-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
            Back to Dashboard
        </a>
        <h2 class="text-3xl font-black text-mainText tracking-tight">Create New Course</h2>
    </div>

    {{-- Wizard Steps (Static for Create) --}}
    @include('admin.courses.partials._wizard_steps', ['activeTab' => 'basic', 'course' => null])

    {{-- Content --}}
    @include('admin.courses.partials._basic')

</div>

{{-- Shared Scripts --}}
<script>
    async function fetchSubCategories(catId) {
        const subSelector = document.getElementById('sub_selector');

        if(!catId) {
            subSelector.innerHTML = '<option value="">Select Sub Category</option>';
            return;
        }

        try {
            const res = await fetch(`/admin/courses/sub-categories/${catId}`);
            const data = await res.json();

            subSelector.innerHTML = '<option value="">Select Sub Category</option>';
            data.forEach(item => {
                subSelector.innerHTML += `<option value="${item.id}">${item.name}</option>`;
            });
        } catch (error) {
            console.error("Error fetching subcategories:", error);
        }
    }
</script>
@endsection
