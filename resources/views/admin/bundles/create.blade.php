@extends('layouts.admin')
@section('title', 'Create Bundle')

@section('content')
<div class="font-sans text-mainText min-h-screen">
    <div class="flex items-center gap-4 mb-8">
        <a href="{{ route('admin.bundles.index') }}" class="p-2 rounded-xl bg-surface border border-primary/10 hover:bg-primary/5 text-mutedText transition-colors">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path></svg>
        </a>
        <div>
            <h2 class="text-2xl font-extrabold tracking-tight text-mainText">Create New Bundle</h2>
            <p class="text-sm text-mutedText font-medium">Combine courses into a single package.</p>
        </div>
    </div>

    @include('admin.bundles.partials._form', [
        'bundle' => null,
        'courses' => $courses,
        'allBundles' => $allBundles,
        'selectedCourses' => [],
        'selectedBundles' => []
    ])
</div>
@endsection
