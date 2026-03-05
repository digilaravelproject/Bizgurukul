@extends('layouts.admin')

{{-- Toastr CSS ko head mein bhej rahe hain --}}
@push('styles')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <style>
        /* Progress bar color customization */
        #toast-container>.toast {
            opacity: 1 !important;
        }
    </style>
@endpush

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-bold text-slate-800">{{ isset($lesson) ? 'Edit' : 'Create' }} Lesson</h2>
            <a href="{{ route('admin.courses.index') }}"
                class="text-sm font-bold text-slate-500 hover:text-slate-700 transition">← Back to Lessons</a>
        </div>

        <div class="bg-white p-8 rounded-[2rem] shadow-sm border border-slate-200">
            {{-- form action aur enctype zaroori hai video upload ke liye --}}
            <form action="{{ route('admin.lessons.store') }}" method="POST" enctype="multipart/form-data" id="lessonForm">
                @csrf
                @if (isset($lesson))
                    <input type="hidden" name="id" value="{{ $lesson->id }}">
                @endif

                <div class="space-y-6">
                    {{-- 1. Course Selection Dropdown --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Select Course</label>
                        <select name="course_id" required
                            class="w-full rounded-xl {{ $errors->has('course_id') ? 'border-red-500' : 'border-slate-200' }} focus:ring-2 focus:ring-[#0777be]/20 focus:border-[#0777be] px-4 py-3 bg-gray-50">
                            <option value="">-- Click to Select Course --</option>
                            @foreach ($courses as $course)
                                <option value="{{ $course->id }}"
                                    {{ old('course_id', $selected_course->id ?? '') == $course->id ? 'selected' : '' }}>
                                    {{ $course->title }}
                                </option>
                            @endforeach
                        </select>
                        @error('course_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- 2. Lesson Title --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Lesson Title</label>
                        <input type="text" name="title" required value="{{ old('title', $lesson->title ?? '') }}"
                            class="w-full rounded-xl {{ $errors->has('title') ? 'border-red-500' : 'border-slate-200' }} px-4 py-3 focus:ring-2 focus:ring-[#0777be]/20"
                            placeholder="e.g. Setting up Environment">
                    </div>

                    {{-- 3. Description --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Description (Optional)</label>
                        <textarea name="description" rows="3"
                            class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-2 focus:ring-[#0777be]/20">{{ old('description', $lesson->description ?? '') }}</textarea>
                    </div>

                    {{-- 4. Bunny.net Fields --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Bunny Video ID (Required for Video Lessons)</label>
                        <input type="text" name="bunny_video_id" value="{{ old('bunny_video_id', $lesson->bunny_video_id ?? '') }}"
                            class="w-full rounded-xl {{ $errors->has('bunny_video_id') ? 'border-red-500' : 'border-slate-200' }} px-4 py-3 focus:ring-2 focus:ring-[#0777be]/20"
                            placeholder="e.g. 5f3e7a...">
                        @error('bunny_video_id')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Bunny Embed URL or Full Iframe Tag (Recommended)</label>
                        <input type="text" name="bunny_embed_url" value="{{ old('bunny_embed_url', $lesson->bunny_embed_url ?? '') }}"
                            class="w-full rounded-xl {{ $errors->has('bunny_embed_url') ? 'border-red-500' : 'border-slate-200' }} px-4 py-3 focus:ring-2 focus:ring-[#0777be]/20"
                            placeholder="Paste direct link or full <iframe...> tag here">
                        @error('bunny_embed_url')
                            <p class="text-red-500 text-xs mt-1">{{ $message }}</p>
                        @enderror
                        <p class="text-[10px] text-slate-400 mt-1">Copy "Embed Code" from Bunny Dashboard and paste it here. We will handle the rest.</p>
                    </div>

                    {{-- Submit Button with Processing Log --}}
                    <div class="pt-6 border-t border-slate-100 flex gap-4">
                        <button type="submit" id="submitBtn"
                            class="flex-1 bg-[#0777be] text-white py-4 rounded-2xl font-black shadow-lg shadow-blue-100 hover:bg-[#0666a3] active:scale-95 transition-all uppercase tracking-widest">
                            {{ isset($lesson) ? 'UPDATE LESSON' : 'SAVE LESSON' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Loader logic has been removed as it's no longer necessary with Bunny.net direct links --}}
@endsection

@push('scripts')
    {{-- Toastr & jQuery --}}
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>

    <script>
        // 1. Toastr Setup
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right"
        };

        $(document).ready(function() {
            // Flash Messages capture
            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error("{{ $error }}");
                @endforeach
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif
        });
        // Removed JS loader logic since no files are being uploaded
    </script>
@endpush
