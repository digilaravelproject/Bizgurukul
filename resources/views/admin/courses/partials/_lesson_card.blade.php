<div id="lesson-card-{{ $lesson->id }}" class="group relative bg-surface rounded-[1.5rem] border border-primary hover:border-primary/30 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col h-full animate-fade-in-up" x-data="{ editing: false }">

    {{-- 1. Thumbnail Area --}}
    <div class="relative h-44 w-full bg-primary/5 overflow-hidden">

        @if($lesson->thumbnail_url)
            {{-- Manual upload OR Bunny proxy thumbnail --}}
            <img src="{{ $lesson->thumbnail_url }}" loading="lazy"
                 class="h-full w-full object-cover transition-transform duration-700 group-hover:scale-110"
                 alt="{{ $lesson->title }}"
                 onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
            {{-- Fallback if proxy fails --}}
            <div style="display:none" class="h-full w-full flex flex-col items-center justify-center gap-2" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);">
                <div class="w-12 h-12 rounded-full bg-orange-500/20 flex items-center justify-center border border-orange-400/30">
                    <svg class="w-6 h-6 ml-1 text-orange-400" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </div>
                <span class="text-[10px] font-bold text-orange-300/70 uppercase tracking-widest">🐰 Bunny Stream</span>
            </div>
        @elseif($lesson->type == 'video' && $lesson->is_bunny)
            {{-- Bunny video with no thumbnail yet — styled placeholder --}}
            <div class="h-full w-full flex flex-col items-center justify-center gap-2" style="background: linear-gradient(135deg, #1a1a2e 0%, #16213e 50%, #0f3460 100%);">
                <div class="w-12 h-12 rounded-full bg-orange-500/20 flex items-center justify-center border border-orange-400/30">
                    <svg class="w-6 h-6 ml-1 text-orange-400" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </div>
                <span class="text-[10px] font-bold text-orange-300/70 uppercase tracking-widest">🐰 Bunny Stream</span>
            </div>
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
            @if($lesson->is_bunny)
                <span class="px-2 py-1 rounded-lg bg-orange-500/80 backdrop-blur text-[10px] font-bold text-white uppercase border border-orange-400/20 shadow-sm">🐰 Bunny</span>
            @else
                <span class="px-2 py-1 rounded-lg bg-black/50 backdrop-blur text-[10px] font-bold text-customWhite uppercase border border-white/10 shadow-sm">{{ $lesson->type }}</span>
            @endif
        </div>

        {{-- Edit Button — always visible, above play overlay --}}
        <button @click.stop="editing = true" class="absolute top-3 left-3 z-30 p-1.5 rounded-lg bg-white/90 backdrop-blur text-slate-700 shadow hover:bg-primary hover:text-white transition-all" title="Edit lesson">
            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z"/></svg>
        </button>

        {{-- PREVIEW OVERLAY BUTTON --}}
        @if($lesson->type == 'video' && ($lesson->is_bunny || $lesson->video_path))
            <button
                type="button"
                @click="$dispatch('open-preview', {
                    type: '{{ $lesson->is_bunny ? 'bunny_video' : 'video' }}',
                    url: '{{ $lesson->is_bunny ? $lesson->player_url : $lesson->admin_video_url }}',
                    title: {{ json_encode($lesson->title) }}
                })"
                class="absolute inset-0 w-full h-full flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-[1px] cursor-pointer z-20">
                <div class="h-12 w-12 rounded-full bg-surface flex items-center justify-center text-primary shadow-xl transform scale-0 group-hover:scale-100 transition-transform duration-300 hover:scale-110 hover:bg-white">
                    <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                </div>
            </button>
        @elseif($lesson->type == 'document' && $lesson->document_path)
            <button
                type="button"
                @click="$dispatch('open-preview', {
                    type: 'document',
                    url: '{{ $lesson->lesson_file_url }}',
                    title: {{ json_encode($lesson->title) }}
                })"
                class="absolute inset-0 w-full h-full flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-[1px] cursor-pointer z-20">
                <div class="h-12 w-12 rounded-full bg-surface flex items-center justify-center text-secondary shadow-xl transform scale-0 group-hover:scale-100 transition-transform duration-300 hover:scale-110 hover:bg-white">
                    <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                </div>
            </button>
        @endif
    </div>

    {{-- 2. Content Area --}}
    <div class="p-5 flex flex-col flex-1">
        <h4 class="text-sm font-bold text-mainText line-clamp-2 leading-snug mb-2" title="{{ $lesson->title }}">{{ $lesson->title }}</h4>

        <div class="mt-auto pt-4 border-t border-dashed border-primary flex items-center justify-between">
            @if($lesson->type == 'video')
                @if($lesson->is_bunny)
                    <span class="flex items-center gap-1 text-[10px] font-black uppercase text-green-600 bg-green-50 px-2 py-1 rounded-md"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Ready</span>
                @elseif($lesson->hls_path == 'failed')
                    <span class="flex items-center gap-1 text-[10px] font-black uppercase text-red-600 bg-red-50 px-2 py-1 rounded-md"><span class="w-1.5 h-1.5 rounded-full bg-red-500"></span> Failed</span>
                @elseif($lesson->hls_path)
                    <span class="flex items-center gap-1 text-[10px] font-black uppercase text-green-600 bg-green-50 px-2 py-1 rounded-md"><span class="w-1.5 h-1.5 rounded-full bg-green-500"></span> Ready</span>
                @elseif($lesson->video_path)
                    <span class="processing-badge flex items-center gap-1 text-[10px] font-black uppercase text-amber-600 bg-amber-50 px-2 py-1 rounded-md" data-lesson-id="{{ $lesson->id }}"><span class="w-1.5 h-1.5 rounded-full bg-amber-500 animate-pulse"></span> Processing</span>
                @else
                    <span class="flex items-center gap-1 text-[10px] font-black uppercase text-slate-400 bg-slate-50 px-2 py-1 rounded-md"><span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span> No Video</span>
                @endif
            @else
                <span class="flex items-center gap-1 text-[10px] font-black uppercase text-blue-600 bg-blue-50 px-2 py-1 rounded-md"><span class="w-1.5 h-1.5 rounded-full bg-blue-500"></span> Document</span>
            @endif

            <button onclick="deleteLesson({{ $lesson->id }})" class="p-2 rounded-lg text-mutedText hover:bg-secondary/10 hover:text-secondary transition-colors" title="Delete Lesson">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
            </button>
            <form id="delete-lesson-{{ $lesson->id }}" action="{{ route('admin.courses.lesson.delete', ['id' => $lesson->id]) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
        </div>
    </div>

    {{-- INLINE EDIT MODAL --}}
    <div x-show="editing" x-cloak class="absolute inset-0 z-30 bg-surface/95 backdrop-blur-sm flex flex-col p-5 rounded-[1.5rem]" x-transition.opacity>
        <div class="flex justify-between items-center mb-4">
            <h5 class="text-sm font-black text-mainText">Edit Lesson</h5>
            <button @click="editing = false" class="text-mutedText hover:text-secondary transition-colors">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        <form action="{{ route('admin.courses.lesson.update', $lesson->id) }}" method="POST" enctype="multipart/form-data"
              class="flex flex-col gap-3 flex-1"
              x-ref="editForm{{ $lesson->id }}"
              @submit.prevent="
                const form = $refs['editForm{{ $lesson->id }}'];
                const fd = new FormData(form);
                fetch(form.action, {method:'POST', headers:{'X-Requested-With':'XMLHttpRequest'}, body: fd})
                .then(r=>r.json()).then(res=>{
                    if(res.success){
                        document.getElementById('lesson-card-{{ $lesson->id }}').outerHTML = res.html;
                        if(typeof toastr !== 'undefined') toastr.success(res.message);
                    } else {
                        if(typeof toastr !== 'undefined') toastr.error(res.message || 'Update failed');
                    }
                }).catch(()=>{ if(typeof toastr !== 'undefined') toastr.error('Network error'); });
              ">
            @csrf

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-1">Title</label>
                <input type="text" name="title" value="{{ $lesson->title }}" required
                    class="w-full h-10 rounded-xl bg-white px-3 text-sm font-bold text-mainText border border-gray-200 focus:border-primary focus:ring-0 outline-none transition-all">
            </div>

            <div>
                <label class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-1">Thumbnail</label>
                @if($lesson->thumbnail_url)
                    <img src="{{ $lesson->thumbnail_url }}" class="w-full h-20 object-cover rounded-lg mb-2 border border-gray-100">
                @endif
                <input type="file" name="thumbnail" accept="image/*"
                    class="w-full text-[10px] text-mutedText file:mr-3 file:py-2 file:px-3 file:rounded-lg file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-primary/10 file:text-primary hover:file:bg-primary hover:file:text-white cursor-pointer transition-all">
                <p class="text-[9px] text-mutedText mt-1">Max 5MB. Leave empty to keep existing thumbnail.</p>
            </div>

            <button type="submit" class="mt-auto w-full brand-gradient py-2.5 rounded-xl font-black text-[11px] uppercase text-white shadow-md hover:-translate-y-0.5 transition-all">
                Save Changes
            </button>
        </form>
    </div>
</div>
