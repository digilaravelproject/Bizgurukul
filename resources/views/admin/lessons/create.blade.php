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
            <a href="{{ route('admin.lessons.all') }}"
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
                            class="w-full rounded-xl border-slate-200 focus:ring-2 focus:ring-[#0777be]/20 focus:border-[#0777be] px-4 py-3 bg-gray-50 @error('course_id') border-red-500 @enderror">
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
                            class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-2 focus:ring-[#0777be]/20 @error('title') border-red-500 @enderror"
                            placeholder="e.g. Setting up Environment">
                    </div>

                    {{-- 3. Description --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Description (Optional)</label>
                        <textarea name="description" rows="3"
                            class="w-full rounded-xl border-slate-200 px-4 py-3 focus:ring-2 focus:ring-[#0777be]/20">{{ old('description', $lesson->description ?? '') }}</textarea>
                    </div>

                    {{-- 4. Video Upload Logic --}}
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Lesson Video</label>
                        <div
                            class="relative group border-2 border-dashed {{ $errors->has('video') ? 'border-red-300 bg-red-50' : 'border-slate-200' }} rounded-2xl p-8 hover:border-[#0777be] hover:bg-slate-50 transition-all text-center">
                            <input type="file" name="video" accept="video/*" id="videoInput"
                                class="absolute inset-0 w-full h-full opacity-0 cursor-pointer">
                            <div class="space-y-2">
                                <svg class="w-12 h-12 {{ $errors->has('video') ? 'text-red-400' : 'text-slate-400' }} mx-auto"
                                    fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12">
                                    </path>
                                </svg>
                                <p class="text-sm font-bold text-slate-600" id="videoFileName">Drag video here or click to
                                    upload</p>
                                <p class="text-[10px] text-slate-400 uppercase font-black">MP4, MOV up to 100MB</p>
                            </div>
                        </div>
                    </div>

                    {{-- Submit Button with Processing Log --}}
                    <div class="pt-6 border-t border-slate-100 flex gap-4">
                        <button type="submit" id="submitBtn"
                            class="flex-1 bg-[#0777be] text-white py-4 rounded-2xl font-black shadow-lg shadow-blue-100 hover:bg-[#0666a3] active:scale-95 transition-all uppercase tracking-widest">
                            {{ isset($lesson) ? 'UPDATE LESSON' : 'SAVE & PROCESS VIDEO' }}
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>

    {{-- Full Screen Processing Loader --}}
    <div id="loaderOverlay"
        class="fixed inset-0 bg-slate-900/80 backdrop-blur-sm z-[9999] hidden flex items-center justify-center text-center p-4">
        <div class="space-y-6">
            <div class="relative flex items-center justify-center">
                <div class="animate-spin rounded-full h-20 w-20 border-t-4 border-b-4 border-white"></div>
                <div class="absolute text-white font-black text-xs">FFMPEG</div>
            </div>
            <div class="space-y-2">
                <p class="text-white font-black text-2xl uppercase tracking-tighter" id="loaderText">Processing Your
                    Video...</p>
                <p class="text-white/60 text-sm font-medium italic">Bhai, thoda wait karo. Video encode ho rahi hai...</p>
            </div>
            {{-- Real-time Submission Log Placeholder --}}
            <div class="bg-black/40 border border-white/10 rounded-xl p-4 max-w-xs mx-auto text-left">
                <p class="text-[10px] text-green-400 font-mono" id="logText">> Initializing Upload...</p>
            </div>
        </div>
    </div>
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

        // 2. Video Input Filename Change logic
        document.getElementById('videoInput').onchange = function() {
            if (this.files[0]) {
                document.getElementById('videoFileName').innerHTML =
                    `Selected: <span class="text-[#0777be]">${this.files[0].name}</span>`;
            }
        };

        // 3. Form Submission Log & Loader Logic
        document.getElementById('lessonForm').onsubmit = function() {
            // Screen Lock & Loader Show
            document.getElementById('loaderOverlay').classList.remove('hidden');

            const btn = document.getElementById('submitBtn');
            const log = document.getElementById('logText');

            btn.disabled = true;
            btn.innerText = "PROCESSING...";

            // Browser Console Log
            console.log("Form submitted. Lesson processing started...");

            // Simulated step-by-step logs for UI
            setTimeout(() => {
                log.innerHTML += "<br>> Uploading to Server...";
            }, 1000);
            setTimeout(() => {
                log.innerHTML += "<br>> Starting HLS Transcoding...";
            }, 3000);
            setTimeout(() => {
                log.innerHTML += "<br>> Running FFmpeg X264 Filter...";
            }, 5000);
        };
    </script>
@endpush
