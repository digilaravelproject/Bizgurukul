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
                @include('admin.courses.partials._lesson_card', ['lesson' => $lesson])
            @endforeach
        </div>
    @else
        <div id="no-lessons-placeholder" class="text-center py-16 bg-surface border-2 border-dashed border-primary rounded-[2rem] flex flex-col items-center justify-center">
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

                {{-- Video Inputs (Bunny.net) --}}
                <div x-show="lType === 'video'" class="animate-fade-in space-y-4">
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Bunny Embed URL / Iframe Tag</label>
                        <input type="text" name="bunny_embed_url" class="w-full h-12 rounded-xl bg-white px-4 text-sm font-bold text-mainText border border-gray-300 focus:border-primary focus:ring-0 transition-all outline-none" placeholder="Paste link or <iframe...> tag">
                        <p class="text-[9px] text-primary font-bold mt-1 ml-1 uppercase">Best Method: Paste the "Embed Code" from Bunny.</p>
                    </div>
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Bunny Video ID (Alternative)</label>
                        <input type="text" name="bunny_video_id" class="w-full h-12 rounded-xl bg-white px-4 text-sm font-bold text-mainText border border-gray-300 focus:border-primary focus:ring-0 transition-all outline-none" placeholder="e.g. 5f3e...">
                    </div>
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
        // Find all cards that are currently marked as "Processing"
        const processingBadges = document.querySelectorAll('.processing-badge');

        if (processingBadges.length === 0) return;

        // Fetch latest cards for those processing
        fetch(window.location.href, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.text())
        .then(html => {
            const parser = new DOMParser();
            const doc = parser.parseFromString(html, 'text/html');

            processingBadges.forEach(badge => {
                const lessonId = badge.getAttribute('data-lesson-id');
                const newCard = doc.getElementById('lesson-card-' + lessonId);
                const oldCard = document.getElementById('lesson-card-' + lessonId);

                // If it finished processing in the response, swap out the card
                if (newCard && oldCard && !newCard.querySelector('.processing-badge')) {
                    oldCard.outerHTML = newCard.outerHTML;
                    if (typeof toastr !== 'undefined') toastr.success('Lesson processing completed!');
                }
            });
        });
    }

    // Check every 10 seconds for jobs in processing state
    setInterval(checkProcessingStatus, 10000);

    // AJAX Form Submission without blocking UI
    document.getElementById('addLessonForm').addEventListener('submit', async function(e) {
        e.preventDefault();

        const form = this;
        let formData = new FormData(form);
        const submitBtn = form.querySelector('button[type="submit"]');

        // Hide Modal
        window.dispatchEvent(new CustomEvent('close-lesson-modal'));

        if(submitBtn) {
            submitBtn.disabled = true;
            submitBtn.innerHTML = 'Starting...';
        }

        // Add Grid container if it does not exist
        let grid = document.getElementById('lessons-grid-container');
        if (!grid) {
            const placeholder = document.getElementById('no-lessons-placeholder');
            if(placeholder) {
                placeholder.insertAdjacentHTML('afterend', '<div id="lessons-grid-container" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6"></div>');
                grid = document.getElementById('lessons-grid-container');
                placeholder.remove();
            }
        }

        const placeholderId = 'uploading-' + Date.now();

        if (grid) {
            const placeholderHTML = `
            <div id="${placeholderId}" class="group relative bg-surface rounded-[1.5rem] border border-secondary shadow-sm overflow-hidden flex flex-col h-full animate-fade-in-up border-dashed">
                <div class="relative h-44 w-full bg-secondary/10 flex items-center justify-center flex-col gap-2">
                    <svg class="w-10 h-10 text-secondary animate-pulse" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"/></svg>
                    <span class="text-xs font-black text-secondary uppercase tracking-widest text-center px-4">Saving...</span>
                </div>
                <div class="p-5 flex flex-col flex-1">
                    <h4 class="text-sm font-bold text-mainText line-clamp-2 leading-snug mb-2">${formData.get('title') || 'New Lesson'}</h4>
                </div>
            </div>`;
            grid.insertAdjacentHTML('afterbegin', placeholderHTML);
        }

        // Send immediately to store endpoint
        sendStandardForm(form, formData, submitBtn, placeholderId);
    });

    // Handles the actual lesson creation
    function sendStandardForm(form, formData, submitBtn, placeholderId) {
        const xhr = new XMLHttpRequest();
        xhr.open('POST', form.action, true);
        xhr.setRequestHeader('Accept', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        xhr.onload = function() {
            if (submitBtn) {
                submitBtn.disabled = false;
                submitBtn.innerHTML = 'Add Lesson';
            }
            if (xhr.status >= 200 && xhr.status < 300) {
                if(typeof toastr !== 'undefined') toastr.success('Lesson created successfully!');
                form.reset();

                const response = JSON.parse(xhr.responseText);
                const placeholder = document.getElementById(placeholderId);

                if (placeholder && response.html) {
                     placeholder.outerHTML = response.html;
                } else if (placeholder) {
                     placeholder.remove();
                     window.location.reload();
                }
            } else {
                cleanupPlaceholder(submitBtn, placeholderId);
                try {
                    const res = JSON.parse(xhr.responseText);
                    if (res.message && typeof toastr !== 'undefined') toastr.error(res.message);
                    if (res.errors && typeof toastr !== 'undefined') {
                        Object.keys(res.errors).forEach(key => toastr.error(res.errors[key][0]));
                    }
                } catch(e) {}
            }
        };

        xhr.onerror = function() {
            cleanupPlaceholder(submitBtn, placeholderId);
            if(typeof toastr !== 'undefined') toastr.error('Network Error. Creation interrupted.');
        };
        xhr.send(formData);
    }

    function cleanupPlaceholder(submitBtn, placeholderId) {
        if (submitBtn) {
            submitBtn.disabled = false;
            submitBtn.innerHTML = 'Add Lesson';
        }
        const placeholder = document.getElementById(placeholderId);
        if (placeholder) placeholder.remove();
    }
</script>
