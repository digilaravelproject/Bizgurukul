{{-- VIDEO.JS CDNs (Required for HLS playback) --}}
<link href="https://vjs.zencdn.net/8.10.0/video-js.css" rel="stylesheet" />
<script src="https://vjs.zencdn.net/8.10.0/video.min.js"></script>

<div x-data="previewModal"
     @keydown.escape.window="closePreview()"
     x-show="show"
     x-cloak
     class="fixed inset-0 z-[100] flex items-center justify-center p-4 sm:p-6">

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

        {{-- Body --}}
        <div class="flex-1 bg-black/5 overflow-hidden relative flex items-center justify-center p-2 md:p-4">

            {{-- Video Player Container --}}
            <div x-show="type === 'video'" class="w-full h-full flex items-center justify-center bg-black rounded-xl overflow-hidden shadow-inner min-h-[50vh] md:min-h-[60vh]" id="preview-video-container"></div>

            {{-- Image Viewer --}}
            <template x-if="show && type === 'image' && url">
                <div class="w-full h-full flex items-center justify-center">
                    <img :src="url" class="max-w-full max-h-full object-contain rounded-lg shadow-sm" alt="Preview" @load="loading = false">
                </div>
            </template>

            {{-- PDF Viewer --}}
            <template x-if="show && type === 'document' && url">
                <iframe :src="url" class="w-full h-full rounded-xl border border-gray-200 bg-white min-h-[50vh] md:min-h-[60vh]" @load="loading = false"></iframe>
            </template>

            {{-- Loading State --}}
            <div x-show="loading && show" class="absolute inset-0 flex items-center justify-center z-[110]">
                <div class="flex flex-col items-center gap-3 bg-surface/50 p-6 rounded-3xl backdrop-blur-sm">
                    <div class="w-10 h-10 border-4 border-primary/30 border-t-primary rounded-full animate-spin"></div>
                    <span class="text-xs font-bold text-mutedText animate-pulse">Loading content...</span>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
document.addEventListener('alpine:init', () => {
    Alpine.data('previewModal', () => ({
        show: false,
        type: '',
        url: '',
        title: '',
        player: null,
        loading: false,

        init() {
            window.addEventListener('open-preview', (event) => {
                this.type = event.detail.type;
                this.url = event.detail.url;
                this.title = event.detail.title;
                this.show = true;
                this.loading = true;

                if (this.type === 'video') {
                    // Delay slightly to ensure modal is visible before mounting player
                    setTimeout(() => { this.initPlayer(); }, 100);
                } else {
                    setTimeout(() => { this.loading = false; }, 3000);
                }
            });
        },

        initPlayer() {
            if (this.player) {
                try { this.player.dispose(); } catch(e) {}
                this.player = null;
            }

            const container = document.getElementById('preview-video-container');
            if (!container) return;

            // Cleanly re-inject the video element
            container.innerHTML = '<video id="admin-preview-player" class="video-js vjs-big-play-button vjs-fluid w-full h-full" playsinline></video>';

            // Check if VideoJS is loaded properly
            if (typeof videojs === 'undefined') {
                console.error('VideoJS library is missing!');
                this.loading = false;
                container.innerHTML = '<div class="text-white text-sm">Error: Video Player Library not loaded.</div>';
                return;
            }

            const videoTimeout = setTimeout(() => {
                this.loading = false;
            }, 5000);

            setTimeout(() => {
                this.player = videojs('admin-preview-player', {
                    fluid: true,
                    autoplay: true,
                    controls: true,
                    preload: 'auto',
                    playbackRates: [0.5, 1, 1.25, 1.5, 2],
                    html5: {
                        vhs: {
                            withCredentials: true, // IMPORTANT: Allows fetching encrypted keys
                            overrideNative: true   // IMPORTANT: Forces Video.js to handle HLS everywhere
                        }
                    }
                });

                this.player.ready(() => {
                    clearTimeout(videoTimeout);
                    this.loading = false;
                });

                this.player.on('error', () => {
                    clearTimeout(videoTimeout);
                    this.loading = false;
                    console.error("Video player encountered an error");
                });

                this.player.on('loadedmetadata', () => {
                    clearTimeout(videoTimeout);
                    this.loading = false;
                });

                // Set the source based on file extension
                const isHLS = this.url.includes('.m3u8');
                this.player.src({
                    src: this.url,
                    type: isHLS ? 'application/x-mpegURL' : 'video/mp4'
                });
            }, 50);
        },

        closePreview() {
            this.show = false;
            if (this.player) {
                try { this.player.pause(); } catch(e) {}
            }
            // Dispose player after modal close transition finishes
            setTimeout(() => {
                if (this.player) {
                    try { this.player.dispose(); } catch(e) {}
                    this.player = null;
                }
                this.url = '';
                this.type = '';
                this.loading = false;
            }, 300);
        }
    }));
});
</script>
