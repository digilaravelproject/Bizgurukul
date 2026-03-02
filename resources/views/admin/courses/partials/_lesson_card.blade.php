<div id="lesson-card-{{ $lesson->id }}" class="group relative bg-surface rounded-[1.5rem] border border-primary hover:border-primary/30 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col h-full animate-fade-in-up">

    {{-- 1. Thumbnail Area --}}
    <div class="relative h-44 w-full bg-primary/5 overflow-hidden">
        @if($lesson->thumbnail)
            <img src="{{ $lesson->thumbnail_url }}" class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110">
        @elseif($lesson->type == 'video')
            <div class="h-full w-full flex items-center justify-center text-primary/20 group-hover:text-primary/40 transition-colors">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
            </div>
        @else
            <div class="h-full w-full flex items-center justify-center text-secondary/20 group-hover:text-secondary/40 transition-colors">
                <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 24 24"><path d="M14 2H6a2 2 0 00-2 2v16a2 2 0 002 2h12a2 2 0 002-2V8l-6-6z"/></svg>
            </div>
        @endif

        {{-- Type Badge --}}
        <div class="absolute top-3 right-3 z-10">
            <span class="px-2 py-1 rounded-lg bg-black/50 backdrop-blur text-[10px] font-bold text-customWhite uppercase border border-white/10 shadow-sm">
                {{ $lesson->type }}
            </span>
        </div>

        {{-- PREVIEW OVERLAY BUTTON --}}
        @if($lesson->type == 'video' && $lesson->video_path)
            <button
                type="button"
                @click="$dispatch('open-preview', {
                    type: '{{ $lesson->type }}',
                    url: '{{ $lesson->admin_video_url }}', {{-- Using direct MP4 for Admin --}}
                    title: {{ json_encode($lesson->title) }}
                })"
                class="absolute inset-0 w-full h-full flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-[1px] cursor-pointer z-20">

                <div class="h-12 w-12 rounded-full bg-surface flex items-center justify-center text-primary shadow-xl transform scale-0 group-hover:scale-100 transition-transform duration-300 hover:scale-110 hover:bg-white">
                    {{-- Play Icon for Video --}}
                    <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </div>
            </button>
        @elseif($lesson->type == 'document' && $lesson->document_path)
             <button
                type="button"
                @click="$dispatch('open-preview', {
                    type: '{{ $lesson->type }}',
                    url: '{{ $lesson->lesson_file_url }}',
                    title: {{ json_encode($lesson->title) }}
                })"
                class="absolute inset-0 w-full h-full flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-[1px] cursor-pointer z-20">

                <div class="h-12 w-12 rounded-full bg-surface flex items-center justify-center text-primary shadow-xl transform scale-0 group-hover:scale-100 transition-transform duration-300 hover:scale-110 hover:bg-white">
                    {{-- Eye Icon for Doc --}}
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
            </button>
        @endif
    </div>

    {{-- 2. Content Area --}}
    <div class="p-5 flex flex-col flex-1">
        <h4 class="text-sm font-bold text-mainText line-clamp-2 leading-snug mb-2" title="{{ $lesson->title }}">
            {{ $lesson->title }}
        </h4>

        <div class="mt-auto pt-4 border-t border-dashed border-primary flex items-center justify-between">
            @if($lesson->type == 'video')
                @if($lesson->hls_path == 'failed')
                    <span class="flex items-center gap-1 text-[10px] font-black uppercase text-red-600 bg-red-50 px-2 py-1 rounded-md">
                        <span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Failed
                    </span>
                 @elseif(!$lesson->hls_path)
                    <span class="processing-badge flex items-center gap-1 text-[10px] font-black uppercase text-amber-600 bg-amber-50 px-2 py-1 rounded-md" data-lesson-id="{{ $lesson->id }}">
                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Processing
                    </span>
                @else
                    <span class="flex items-center gap-1 text-[10px] font-black uppercase text-green-600 bg-green-50 px-2 py-1 rounded-md">
                        <span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Ready
                    </span>
                @endif
            @else
                 <span class="flex items-center gap-1 text-[10px] font-black uppercase text-blue-600 bg-blue-50 px-2 py-1 rounded-md">
                    <span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Document
                </span>
            @endif

            <button onclick="deleteLesson({{ $lesson->id }})" class="p-2 rounded-lg text-mutedText hover:bg-secondary/10 hover:text-secondary transition-colors" title="Delete Lesson">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
            <form id="delete-lesson-{{ $lesson->id }}" action="{{ route('admin.courses.lesson.delete', ['id' => $lesson->id]) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
        </div>
    </div>
</div>
