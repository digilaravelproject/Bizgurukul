<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 md:gap-8 animate-fade-in-up">

    {{-- UPLOAD BOX (Sticky on Desktop, Normal on Mobile) --}}
    <div class="lg:col-span-1 order-1 lg:order-none">
        <div class="bg-surface rounded-2xl md:rounded-[2rem] border border-primary shadow-lg shadow-primary/5 p-5 md:p-8 static lg:sticky lg:top-6">
            <h4 class="text-sm font-black text-mainText uppercase tracking-widest mb-4 md:mb-6">Upload Material</h4>

            <form action="{{ route('admin.courses.resource.store', $course->id) }}" method="POST" enctype="multipart/form-data" class="space-y-4 md:space-y-5">
                @csrf

                {{-- Title Input --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Title</label>
                    <input type="text" name="title" required
                        class="w-full h-12 rounded-xl bg-white px-4 text-sm font-bold text-mainText border border-gray-300 focus:border-primary focus:ring-0 transition-all outline-none"
                        placeholder="e.g. Source Code">
                </div>

                {{-- File Input with Preview Logic --}}
                <div x-data="{ fileName: null, fileType: null, filePreview: null }">
                    <label class="block text-xs font-black uppercase tracking-widest text-mutedText mb-2 ml-1">File</label>

                    <label class="relative flex flex-col items-center justify-center w-full h-32 rounded-xl border-2 border-dashed border-primary/20 bg-primary/5 hover:bg-navy cursor-pointer transition-all group overflow-hidden">

                        {{-- Placeholder --}}
                        <div class="flex flex-col items-center justify-center pt-5 pb-6" x-show="!fileName">
                            <svg class="w-8 h-8 mb-2 text-primary/50 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                            <p class="text-xs text-mutedText font-bold">Click to upload</p>
                        </div>

                        {{-- Preview Box --}}
                        <div x-show="fileName" class="absolute inset-0 bg-surface flex items-center justify-center p-4" x-cloak>
                            <div class="flex items-center gap-3 w-full">
                                {{-- Icon based on type --}}
                                <div class="h-10 w-10 shrink-0 rounded-lg bg-navy text-primary flex items-center justify-center overflow-hidden">
                                    <template x-if="fileType && fileType.startsWith('image/')">
                                        <img :src="filePreview" class="h-full w-full object-cover">
                                    </template>
                                    <template x-if="!fileType || !fileType.startsWith('image/')">
                                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                                    </template>
                                </div>
                                <div class="min-w-0 flex-1">
                                    <p class="text-xs font-black text-mainText truncate" x-text="fileName"></p>
                                    <p class="text-[10px] text-primary font-bold cursor-pointer hover:underline">Change File</p>
                                </div>
                            </div>
                        </div>

                        <input type="file" name="file" required class="hidden"
                               @change="
                                   const file = $event.target.files[0];
                                   if(file){
                                       fileName = file.name;
                                       fileType = file.type;
                                       if(file.type.startsWith('image/')){
                                           const reader = new FileReader();
                                           reader.onload = e => filePreview = e.target.result;
                                           reader.readAsDataURL(file);
                                       } else {
                                           filePreview = null;
                                       }
                                   }
                               ">
                    </label>
                </div>

                <button class="w-full brand-gradient py-3.5 rounded-xl text-customWhite text-xs font-black uppercase shadow-lg hover:shadow-xl hover:-translate-y-0.5 transition-all">Upload File</button>
            </form>
        </div>
    </div>

    {{-- RESOURCES LIST --}}
    <div class="lg:col-span-2 space-y-4 order-2">
        @forelse($course->resources as $r)

        {{-- URL Logic Fix: Check if it's already a full URL --}}
        @php
            // $fileUrl = Str::startsWith($r->file_path, 'http') ? $r->file_path : Storage::url($r->file_path);
            $fileUrl = $r->file_path;
            $ext = strtolower(pathinfo($r->file_path, PATHINFO_EXTENSION));
        @endphp

        <div class="group flex flex-col sm:flex-row justify-between items-center p-5 bg-surface rounded-2xl border border-navy hover:border-navy shadow-sm hover:shadow-md transition-all">

            {{-- Info --}}
            <div class="flex items-center gap-4 w-full sm:w-auto mb-4 sm:mb-0">
                <div class="h-12 w-12 rounded-xl bg-navy text-primary flex items-center justify-center shrink-0">
                    @if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp']))
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                    @elseif($ext == 'pdf')
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                    @else
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    @endif
                </div>
                <div class="min-w-0">
                    <h5 class="text-sm font-bold text-mainText truncate max-w-[200px] sm:max-w-xs" title="{{ $r->title }}">{{ $r->title }}</h5>
                    <p class="text-[10px] text-mutedText font-bold uppercase">{{ $ext }} FILE</p>
                </div>
            </div>

       {{-- Actions --}}
            <div class="flex items-center gap-2 w-full sm:w-auto justify-end">

                {{-- Determine Preview Type --}}
                @php
                    $previewType = 'document'; // Default
                    if(in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif'])) {
                        $previewType = 'image';
                    }
                @endphp

                {{-- Preview Button --}}
                <button type="button"
                    @click="$dispatch('open-preview', {
                        type: '{{ $previewType }}',
                        url: '{{ $fileUrl }}',
                        title: '{{ addslashes($r->title) }}'
                    })"
                    class="px-4 py-2 rounded-xl bg-primary/5 text-primary text-xs font-bold hover:bg-primary hover:text-customWhite transition-all flex items-center gap-2 cursor-pointer">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg>
                    Preview
                </button>

                {{-- Delete Button --}}
                <button onclick="deleteResource({{ $r->id }})" class="p-2 rounded-xl text-mutedText hover:text-customWhite hover:bg-secondary transition-colors" title="Delete">
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                </button>
            </div>

            <form id="delete-resource-{{ $r->id }}" action="{{ route('admin.courses.resource.delete', ['id' => $r->id]) }}" method="POST" class="hidden">@csrf @method('DELETE')</form>
        </div>
        @empty
        <div class="text-center py-10 bg-surface border-2 border-dashed border-primary rounded-[2rem]">
            <div class="h-16 w-16 rounded-full bg-primary/5 flex items-center justify-center mb-4 mx-auto text-primary">
                <svg class="w-8 h-8" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
            </div>
            <p class="text-mutedText font-medium text-sm">No resources added yet.</p>
        </div>
        @endforelse
    </div>

    {{-- Next Step --}}
    <div class="lg:col-span-3 flex justify-end pt-6 order-3">
        <a href="{{ route('admin.courses.edit', ['id' => $course->id, 'tab' => 'settings']) }}" class="text-primary font-bold text-xs uppercase tracking-widest hover:underline">Proceed to Publish →</a>
    </div>

    {{-- Important: Include Preview Modal (Ensures it works even if not in layout) --}}
    @include('admin.courses.partials._preview_modal')
</div>

<script>
function deleteResource(id) {
    Swal.fire({
        title: 'Delete Resource?',
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Delete',
        customClass: {
            popup: 'rounded-[2rem] p-6 bg-surface font-sans',
            title: 'text-xl font-bold text-mainText',
            confirmButton: 'bg-secondary text-customWhite px-6 py-2.5 rounded-xl font-bold shadow-lg ml-2',
            cancelButton: 'bg-primary/5 text-mainText px-6 py-2.5 rounded-xl font-bold hover:bg-navy'
        },
        buttonsStyling: false
    }).then((result) => {
        if (result.isConfirmed) document.getElementById('delete-resource-'+id).submit();
    });
}
</script>
