<div class="space-y-6 animate-fade-in-up" x-data="{ addingLesson: false }">

    {{-- Header --}}
    <div class="bg-surface rounded-[2rem] border border-primary shadow-lg shadow-primary/5 p-6 flex flex-col md:flex-row justify-between items-center gap-4">
        <div>
            <h3 class="text-lg font-black text-mainText">Lessons</h3>
            <p class="text-xs text-mutedText font-medium mt-1">Manage lessons, videos, and documents.</p>
        </div>
        <button @click="$dispatch('open-lesson-modal')"
                class="brand-gradient px-6 py-3 rounded-xl text-customWhite text-xs font-black uppercase tracking-widest shadow-md hover:shadow-lg hover:-translate-y-0.5 transition-all flex items-center gap-2">
            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
            Add New Lesson
        </button>
    </div>

    {{-- LESSONS GRID --}}
    @if(count($course->lessons) > 0)
        <div id="lessons-grid-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            @foreach($course->lessons as $lesson)
            <div class="group relative bg-surface rounded-[1.5rem] border border-primary hover:border-primary/30 shadow-sm hover:shadow-xl hover:-translate-y-1 transition-all duration-300 overflow-hidden flex flex-col h-full">

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
                    @if($lesson->lesson_file_url)
                    <button
                        type="button"
                        @click="$dispatch('open-preview', {
                            type: '{{ $lesson->type }}',
                            url: '{{ $lesson->lesson_file_url }}',
                            title: '{{ addslashes($lesson->title) }}'
                        })"
                        class="absolute inset-0 w-full h-full flex items-center justify-center bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity backdrop-blur-[1px] cursor-pointer z-20">

                        <div class="h-12 w-12 rounded-full bg-surface flex items-center justify-center text-primary shadow-xl transform scale-0 group-hover:scale-100 transition-transform duration-300 hover:scale-110 hover:bg-white">
                            @if($lesson->type == 'video')
                                {{-- Play Icon for Video --}}
                                <svg class="w-6 h-6 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            @else
                                {{-- Eye Icon for Doc --}}
                                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                            @endif
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
                            @if(!$lesson->hls_path)
                                <span class="flex items-center gap-1 text-[10px] font-black uppercase text-amber-600 bg-amber-50 px-2 py-1 rounded-md">
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
            @endforeach
        </div>
    @else
        <div class="text-center py-16 bg-surface border-2 border-dashed border-primary rounded-[2rem] flex flex-col items-center justify-center">
            <div class="h-16 w-16 rounded-full bg-primary/5 flex items-center justify-center mb-4 text-primary">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
            </div>
            <p class="text-mainText font-bold text-sm">No lessons yet</p>
            <p class="text-mutedText text-xs mt-1 mb-4">Start building your curriculum now</p>
            <button @click="$dispatch('open-lesson-modal')" class="text-xs font-black text-primary hover:underline uppercase tracking-widest">Add First Lesson</button>
        </div>
    @endif

    {{-- Next Step Button --}}
    <div class="flex justify-end pt-6">
        <a href="{{ route('admin.courses.edit', ['id' => $course->id, 'tab' => 'resources']) }}" class="text-primary font-bold text-xs uppercase tracking-widest hover:underline">Skip to Resources →</a>
    </div>

    {{-- MODAL FOR ADDING LESSON --}}
    <div x-data="{ show: false, lType: 'video' }"
         @open-lesson-modal.window="show = true"
         @close-lesson-modal.window="show = false"
         x-show="show" x-cloak
         class="fixed inset-0 z-50 flex items-center justify-center p-4">

        <div class="absolute inset-0 bg-mainText/40 backdrop-blur-sm transition-opacity" @click="show = false"></div>

        <div class="relative w-full max-w-lg bg-surface rounded-[2rem] shadow-2xl p-8 border border-primary transform transition-all" x-transition.scale.origin.bottom>
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black text-mainText">Add New Lesson</h3>
                <button @click="show = false" class="text-mutedText hover:text-secondary"><svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg></button>
            </div>

            <form id="addLessonForm" action="{{ route('admin.courses.lesson.store', $course->id) }}" method="POST" enctype="multipart/form-data" class="space-y-5">
                @csrf
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Title</label>
                    <input type="text" name="title" required class="w-full h-12 rounded-xl bg-white px-4 text-sm font-bold text-mainText border border-gray-300 focus:border-primary focus:ring-0 transition-all outline-none" placeholder="Lesson Name">
                </div>

                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Content Type</label>
                    <div class="grid grid-cols-2 gap-3">
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="video" x-model="lType" class="hidden peer">
                            <div class="h-12 rounded-xl border border-primary bg-primary/5 flex items-center justify-center text-xs font-bold text-mutedText peer-checked:bg-primary peer-checked:text-customWhite peer-checked:border-primary peer-checked:shadow-lg peer-checked:shadow-primary/20 transition-all">Video</div>
                        </label>
                        <label class="cursor-pointer">
                            <input type="radio" name="type" value="document" x-model="lType" class="hidden peer">
                            <div class="h-12 rounded-xl border border-primary bg-primary/5 flex items-center justify-center text-xs font-bold text-mutedText peer-checked:bg-secondary peer-checked:text-customWhite peer-checked:border-secondary peer-checked:shadow-lg peer-checked:shadow-secondary/20 transition-all">Document</div>
                        </label>
                    </div>
                </div>

                {{-- Thumbnail Upload --}}
                <div x-show="lType === 'video'" class="animate-fade-in">
                    <label class="block text-xs font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Thumbnail (Optional)</label>
                    <input type="file" name="thumbnail" accept="image/*" class="w-full text-xs text-mutedText file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-primary/20 file:text-primary hover:file:bg-primary hover:file:text-customWhite cursor-pointer transition-all">
                    <p class="text-[10px] text-mutedText mt-1 ml-1">If empty, a frame will be auto-selected. Max 5MB (Auto-compressed).</p>
                </div>

                {{-- File Inputs --}}
                <div x-show="lType === 'video'" class="animate-fade-in">
                    <label class="block text-xs font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Video File (MP4) - Max 5GB</label>
                    <input type="file" name="video_file" accept="video/*" class="w-full text-xs text-mutedText file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-primary/20 file:text-primary hover:file:bg-primary hover:file:text-customWhite cursor-pointer transition-all">
                </div>
                <div x-show="lType === 'document'" class="animate-fade-in">
                    <label class="block text-xs font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Document (PDF/DOCX)</label>
                    <input type="file" name="document_file" accept=".pdf,.doc,.docx" class="w-full text-xs text-mutedText file:mr-4 file:py-2.5 file:px-4 file:rounded-xl file:border-0 file:text-[10px] file:font-black file:uppercase file:bg-primary/20 file:text-primary hover:file:bg-primary hover:file:text-customWhite cursor-pointer transition-all">
                </div>

                <button type="submit" class="w-full brand-gradient py-3.5 rounded-xl font-black text-xs uppercase text-customWhite shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">Add Lesson</button>
            </form>
        </div>
    </div>

    {{-- INCLUDE PREVIEW MODAL --}}
    @include('admin.courses.partials._preview_modal')

</div>

<script>
function deleteLesson(id) {
    Swal.fire({
        title: 'Delete Lesson?',
        text: "Are you sure? This cannot be undone.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Yes, Delete',
        customClass: {
            popup: 'rounded-[2rem] p-6 bg-surface font-sans',
            title: 'text-xl font-bold text-mainText',
            confirmButton: 'bg-secondary text-customWhite px-6 py-2.5 rounded-xl font-bold shadow-lg ml-2',
            cancelButton: 'bg-primary text-mainText px-6 py-2.5 rounded-xl font-bold hover:bg-primary'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) document.getElementById('delete-lesson-'+id).submit();
    });
}
</script>
<script>
    function checkProcessingStatus() {
        // Check if any 'Processing' badge exists
        if (document.querySelectorAll('.animate-pulse').length > 0) {
            fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newGrid = doc.getElementById('lessons-grid-container');
                    if (newGrid) {
                        document.getElementById('lessons-grid-container').innerHTML = newGrid.innerHTML;
                    }
                });
        }
    }

    // Har 10 second mein check karega
    setInterval(checkProcessingStatus, 10000);

    // AJAX Form Submission for uploading video without blocking UI
    document.getElementById('addLessonForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const form = this;
        const formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');

        // Hide Modal
        window.dispatchEvent(new CustomEvent('close-lesson-modal'));

        // Setup XHR
        const xhr = new XMLHttpRequest();
        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        // Disable button while preparing request
        if(submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Starting...';
        }

        // Show placeholder grid item
        const grid = document.getElementById('lessons-grid-container');
        const placeholderId = 'uploading-' + Date.now();

        if (grid) {
            const placeholderHTML = `
            <div id="${placeholderId}" class="group relative bg-surface rounded-[1.5rem] border border-secondary shadow-sm overflow-hidden flex flex-col h-full animate-pulse border-dashed">
                <div class="relative h-44 w-full bg-secondary/10 flex items-center justify-center flex-col gap-2">
                    <svg class="w-10 h-10 text-secondary animate-bounce" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span class="text-xs font-black text-secondary uppercase tracking-widest text-center px-4">Uploading<br><span id="${placeholderId}-percent" class="text-xl">0%</span></span>
                </div>
                <div class="p-5 flex flex-col flex-1">
                    <h4 class="text-sm font-bold text-mainText line-clamp-2 leading-snug mb-2">${formData.get('title') || 'New Lesson'}</h4>
                    <div class="mt-auto pt-4 border-t border-dashed border-primary flex items-center justify-between">
                        <span class="text-[10px] font-black uppercase text-secondary bg-secondary/10 px-2 py-1 rounded-md">Uploading to Server...</span>
                    </div>
                </div>
            </div>`;
            grid.insertAdjacentHTML('afterbegin', placeholderHTML);
        }

        xhr.upload.onprogress = function(event) {
            if (event.lengthComputable) {
                const percentComplete = Math.round((event.loaded / event.total) * 100);
                const percentEl = document.getElementById(`${placeholderId}-percent`);
                if (percentEl) {
                    percentEl.innerText = percentComplete + '%';
                }
            }
        };

        xhr.onload = function() {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Add Lesson';
            }
            if (xhr.status >= 200 && xhr.status < 300) {
                if(typeof toastr !== 'undefined') toastr.success('Uploaded successfully! Processing started in background.');
                form.reset(); // clear form

                // Refresh grid to load the real lesson card
                fetch(window.location.href)
                .then(response => response.text())
                .then(html => {
                    const parser = new DOMParser();
                    const doc = parser.parseFromString(html, 'text/html');
                    const newGrid = doc.getElementById('lessons-grid-container');
                    if (newGrid && grid) {
                        grid.innerHTML = newGrid.innerHTML;
                    } else if (grid) {
                        // Fallback: Just reload the page if grid refreshing fails but upload worked
                        window.location.reload();
                    }
                });

            } else {
                // Remove placeholder
                const placeholder = document.getElementById(placeholderId);
                if (placeholder) placeholder.remove();

                try {
                    const res = JSON.parse(xhr.responseText);
                    if (res.message && typeof toastr !== 'undefined') toastr.error(res.message);
                    if (res.errors && typeof toastr !== 'undefined') {
                        for(let key in res.errors) {
                            toastr.error(res.errors[key][0]);
                        }
                    }
                } catch(e) {
                    if(typeof toastr !== 'undefined') toastr.error('Upload failed: ' + xhr.statusText);
                }
            }
        };

        xhr.onerror = function() {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Add Lesson';
            }
            const placeholder = document.getElementById(placeholderId);
            if (placeholder) placeholder.remove();
            if(typeof toastr !== 'undefined') toastr.error('Network Error. Upload interrupted.');
        };

        // Send Request
        xhr.send(formData);
    });
</script>
