@extends('layouts.admin')

@section('content')
    <div class="max-w-4xl mx-auto space-y-6">
        <div class="flex items-center justify-between">
            <h2 class="text-xl font-black text-slate-800 tracking-tight">
                {{ $course->exists ? 'Edit Course' : 'Create New Course' }}</h2>
            <a href="{{ route('admin.courses.index') }}"
                class="text-sm font-bold text-slate-500 hover:text-slate-700 transition">← Back to List</a>
        </div>

        <div class="bg-white p-8 rounded-[2.5rem] shadow-sm border border-slate-200">
            <form action="{{ route('admin.courses.store') }}" method="POST" enctype="multipart/form-data" id="courseMainForm">
                @csrf
                @if ($course->exists)
                    <input type="hidden" name="id" value="{{ $course->id }}">
                @endif

                <div class="space-y-6">
                    {{-- Title --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Course Title</label>
                        <input type="text" name="title" required value="{{ old('title', $course->title) }}"
                            class="w-full rounded-2xl border-slate-200 px-5 py-4 focus:ring-4 focus:ring-[#0777be]/10"
                            placeholder="Enter course name">
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        {{-- Price --}}
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Pricing (INR)</label>
                            <input type="number" name="price" required value="{{ old('price', $course->price ?? 0) }}"
                                class="w-full rounded-2xl border-slate-200 px-5 py-4 focus:ring-4 focus:ring-[#0777be]/10"
                                placeholder="e.g. 4999">
                        </div>

                        {{-- Demo Video File --}}
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Demo Video File</label>
                            <input type="file" name="demo_video" accept="video/*"
                                class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:bg-indigo-50 file:text-indigo-600">
                            @if ($course->demo_video_url)
                                <p class="text-[10px] text-green-600 mt-2 font-bold uppercase tracking-widest">● Video File
                                    Attached</p>
                            @endif
                        </div>
                    </div>

                    {{-- Description --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Course Description</label>
                        <textarea name="description" rows="4"
                            class="w-full rounded-2xl border-slate-200 px-5 py-4 focus:ring-4 focus:ring-[#0777be]/10"
                            placeholder="Provide details about the course content...">{{ old('description', $course->description) }}</textarea>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6 items-center">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-2">Thumbnail Image</label>
                            <input type="file" name="thumbnail" accept="image/*"
                                class="text-xs text-slate-500 file:bg-slate-100 file:border-0 file:rounded-full file:px-4 file:py-2">
                        </div>

                        <div class="flex items-center justify-between p-4 bg-slate-50 rounded-2xl border border-slate-200">
                            <span class="text-sm font-bold text-slate-700">Publish Status</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" name="is_published" value="1"
                                    {{ $course->is_published ? 'checked' : '' }} class="sr-only peer">
                                <div
                                    class="w-11 h-6 bg-slate-300 rounded-full peer peer-checked:bg-green-500 after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-full">
                                </div>
                            </label>
                        </div>
                    </div>

                    <button type="submit" id="saveBtn"
                        class="w-full bg-[#0777be] text-white py-5 rounded-3xl font-black shadow-lg uppercase tracking-widest active:scale-95 transition-all hover:bg-[#0666a3]">
                        {{ $course->exists ? 'Update Course Details' : 'Initialize & Save Course' }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    {{-- Loader Overlay with English Logs --}}
    <div id="processingOverlay"
        class="fixed inset-0 bg-slate-900/95 backdrop-blur-md z-[9999] hidden flex items-center justify-center text-center">
        <div class="space-y-6 max-w-sm px-6">
            <div class="animate-spin rounded-full h-16 w-16 border-t-4 border-white mx-auto"></div>
            <p class="text-white font-black text-xl uppercase tracking-widest">Processing Assets...</p>
            <div id="processLogs"
                class="bg-black/40 border border-white/10 rounded-xl p-4 text-left font-mono text-[10px] text-green-400">
                > Uploading media files to storage...
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script>
        document.getElementById('courseMainForm').onsubmit = function() {
            document.getElementById('processingOverlay').classList.remove('hidden');
            document.getElementById('saveBtn').disabled = true;
            const logs = document.getElementById('processLogs');
            setTimeout(() => {
                logs.innerHTML += "<br>> Verifying database integrity...";
            }, 1200);
            setTimeout(() => {
                logs.innerHTML += "<br>> Finalizing course synchronization...";
            }, 2800);
        };
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif
    </script>
@endpush
