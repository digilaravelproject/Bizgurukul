<div x-data="{
        show: false,
        type: '',
        url: '',
        title: '',
        init() {
            window.addEventListener('open-preview', (event) => {
                this.type = event.detail.type;
                this.url = event.detail.url;
                this.title = event.detail.title;
                this.show = true;
            });
        },
        closePreview() {
            this.show = false;
            setTimeout(() => { this.url = ''; this.type = ''; }, 300);
        }
    }"
    @keydown.escape.window="closePreview()"
    x-show="show"
    x-cloak
    class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6"
    style="display: none;">

    {{-- Backdrop --}}
    <div x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0"
         x-transition:enter-end="opacity-100"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100"
         x-transition:leave-end="opacity-0"
         @click="closePreview()"
         class="absolute inset-0 bg-navy/90 backdrop-blur-sm"></div>

    {{-- Modal Content --}}
    <div x-show="show"
         x-transition:enter="transition ease-out duration-300"
         x-transition:enter-start="opacity-0 scale-95 translate-y-4"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-200"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-4"
         class="relative w-full max-w-5xl bg-surface rounded-2xl md:rounded-[2rem] shadow-2xl overflow-hidden border border-primary flex flex-col max-h-[85vh]">

        {{-- Header --}}
        <div class="flex justify-between items-center p-4 md:p-6 border-b border-primary/5 bg-surface z-10 shrink-0">
            <h3 class="text-base md:text-lg font-black text-mainText truncate pr-4" x-text="title">Preview</h3>
            <button @click="closePreview()" class="h-9 w-9 md:h-10 md:w-10 rounded-xl bg-primary/5 text-mutedText hover:bg-primary hover:text-white transition-all flex items-center justify-center shrink-0">
                <svg class="w-5 h-5 md:w-6 md:h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
            </button>
        </div>

        {{-- Body (Dynamic Content) --}}
        <div class="flex-1 bg-black/5 overflow-hidden relative flex items-center justify-center p-2 md:p-4">

            {{-- 1. Video Player --}}
            <template x-if="show && type === 'video' && url">
                <div class="w-full h-full flex items-center justify-center bg-black rounded-xl overflow-hidden shadow-inner">
                    <video :src="url" controls autoplay playsinline class="max-w-full max-h-full w-full h-auto aspect-video focus:outline-none"></video>
                </div>
            </template>

            {{-- 2. Image Viewer (NEW & IMPROVED) --}}
            <template x-if="show && type === 'image' && url">
                <div class="w-full h-full flex items-center justify-center">
                    {{-- object-contain ensures the image fits inside without cropping --}}
                    <img :src="url" class="max-w-full max-h-full object-contain rounded-lg shadow-sm" alt="Preview">
                </div>
            </template>

            {{-- 3. PDF/Document Viewer --}}
            <template x-if="show && type === 'document' && url">
                <iframe :src="url" class="w-full h-full rounded-xl border border-gray-200 bg-white min-h-[50vh] md:min-h-[60vh]"></iframe>
            </template>

            {{-- Loading State --}}
            <div x-show="!url" class="absolute inset-0 flex items-center justify-center">
                <div class="flex flex-col items-center gap-3">
                    <div class="w-10 h-10 border-4 border-primary/30 border-t-primary rounded-full animate-spin"></div>
                    <span class="text-xs font-bold text-mutedText animate-pulse">Loading content...</span>
                </div>
            </div>
        </div>
    </div>
</div>
