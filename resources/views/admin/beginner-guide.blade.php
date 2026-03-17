@extends('layouts.admin')

@section('content')
    <div class="min-h-screen bg-gray-50/50 py-8 px-4">
        <div class="max-w-7xl mx-auto space-y-6">
            {{-- Simplified Header --}}
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-black text-mainText tracking-tight uppercase">Roadmap Modules</h1>
                    <p class="text-sm text-mutedText font-medium opacity-70 italic">Manage your training videos via Bunny.net streams</p>
                </div>
                <div class="flex items-center gap-3 bg-white p-3 rounded-2xl shadow-sm border border-gray-100">
                    <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary">
                        <i class="fas fa-video"></i>
                    </div>
                    <div>
                        <div class="text-[10px] font-black text-mutedText uppercase tracking-widest">Global Count</div>
                        <div class="text-xl font-black text-mainText leading-none">{{ $videos->count() }}</div>
                    </div>
                </div>
            </div>

            {{-- Alerts --}}
            @if(session('success'))
                <div class="p-4 bg-emerald-50 border border-emerald-100 text-emerald-700 rounded-xl text-sm font-bold animate-fade-in-down shadow-sm">
                    <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
                {{-- Form Section --}}
                <div class="lg:col-span-4">
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden sticky top-8">
                        <div class="p-6 border-b border-gray-50 bg-gray-50/50 flex items-center gap-3">
                            <span class="w-8 h-8 rounded-lg bg-primary text-white flex items-center justify-center text-xs">
                                <i class="fas fa-plus"></i>
                            </span>
                            <h2 class="font-black text-mainText text-sm uppercase tracking-wider">Module Config</h2>
                        </div>

                        <form action="{{ route('admin.beginner-guide.store') }}" method="POST" class="p-6 space-y-5">
                            @csrf
                            
                            <div class="space-y-1">
                                <label class="text-[10px] font-black text-mutedText uppercase tracking-[0.15em] ml-1">Video Title</label>
                                <input type="text" name="title" value="{{ old('title') }}" placeholder="Orientation 1.0" required
                                    class="w-full px-4 py-3 rounded-xl border-gray-200 text-sm font-bold focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all outline-none" />
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-mutedText uppercase tracking-[0.15em] ml-1">Category</label>
                                    <select name="category" required class="w-full px-4 py-3 rounded-xl border-gray-200 text-sm font-bold bg-gray-50 focus:border-primary outline-none">
                                        <option value="foundation" {{ old('category') == 'foundation' ? 'selected' : '' }}>Foundation</option>
                                        <option value="growth" {{ old('category') == 'growth' ? 'selected' : '' }}>Growth</option>
                                        <option value="scale" {{ old('category') == 'scale' ? 'selected' : '' }}>Scale</option>
                                    </select>
                                </div>
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-mutedText uppercase tracking-[0.15em] ml-1">Order Index</label>
                                    <input type="number" name="order_column" value="{{ old('order_column', 0) }}" 
                                        class="w-full px-4 py-3 rounded-xl border-gray-200 text-sm font-bold focus:border-primary outline-none" />
                                </div>
                            </div>

                            <div class="p-4 rounded-2xl bg-primary/5 border border-primary/10 space-y-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <i class="fas fa-external-link-alt text-primary text-xs"></i>
                                    <span class="text-[10px] font-black text-primary uppercase tracking-widest">Bunny Stream Settings</span>
                                </div>
                                
                                <div class="space-y-1">
                                    <input type="text" name="bunny_video_id" value="{{ old('bunny_video_id') }}" placeholder="Video ID (e.g. 5f3e7a...)"
                                        class="w-full px-3 py-2.5 rounded-lg border-gray-200 text-xs font-semibold focus:border-primary outline-none" />
                                </div>

                                <div class="space-y-1">
                                    <textarea name="bunny_embed_url" rows="2" placeholder="OR Iframe Embed Code..."
                                        class="w-full px-3 py-2.5 rounded-lg border-gray-200 text-xs font-semibold focus:border-primary outline-none resize-none leading-relaxed">{{ old('bunny_embed_url') }}</textarea>
                                </div>
                            </div>

                            <div class="space-y-4 pt-2">
                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-mutedText uppercase tracking-[0.15em] ml-1">Module Overview</label>
                                    <textarea name="description" rows="2" placeholder="Brief metadata for user info panel..." 
                                        class="w-full px-4 py-3 rounded-xl border-gray-200 text-xs font-medium focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all resize-none leading-relaxed">{{ old('description') }}</textarea>
                                </div>

                                <div class="space-y-1">
                                    <label class="text-[10px] font-black text-mutedText uppercase tracking-[0.15em] ml-1">Resources & Links</label>
                                    <textarea name="resources" rows="2" placeholder="PDF links, checklist URL, etc." 
                                        class="w-full px-4 py-3 rounded-xl border-gray-200 text-xs font-medium focus:border-primary focus:ring-4 focus:ring-primary/5 outline-none transition-all resize-none leading-relaxed">{{ old('resources') }}</textarea>
                                </div>
                            </div>

                            <button type="submit" class="w-full h-14 bg-primary text-white rounded-2xl font-black uppercase tracking-[0.2em] text-xs shadow-lg shadow-primary/20 hover:scale-[1.02] active:scale-[0.98] transition-all">
                                <i class="fas fa-save mr-2"></i> Deploy Module
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Roadmap Section --}}
                <div class="lg:col-span-8 space-y-6">
                    {{-- Modules Grid --}}
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="px-6 py-5 border-b border-gray-50 flex items-center justify-between bg-gray-50/30">
                            <h2 class="text-mainText font-black text-sm uppercase tracking-wider">Module Database</h2>
                        </div>

                        <div class="divide-y divide-gray-50">
                            @php $found = false; @endphp
                            @foreach(['foundation', 'growth', 'scale'] as $cat)
                                @php $catVideos = $videos->where('category', $cat)->sortBy('order_column'); @endphp
                                @if($catVideos->count() > 0)
                                    @php $found = true; @endphp
                                    <div class="p-6">
                                        <div class="flex items-center gap-3 mb-4">
                                            <div class="px-3 py-1 rounded-full bg-primary/10 text-primary text-[9px] font-black uppercase tracking-widest">
                                                {{ $cat }}
                                            </div>
                                            <div class="h-px flex-1 bg-gray-50"></div>
                                        </div>
                                        
                                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                            @foreach($catVideos as $video)
                                                <div class="group relative flex flex-col p-4 rounded-2xl bg-gray-50/50 border border-gray-100 hover:bg-white hover:shadow-md transition-all duration-300">
                                                    <div class="flex items-start justify-between mb-2">
                                                        <div class="w-8 h-8 rounded-lg bg-white border border-gray-100 flex items-center justify-center font-black text-primary text-[10px] shadow-sm">
                                                            {{ $loop->iteration }}
                                                        </div>
                                                        <div class="flex gap-1.5 opacity-0 group-hover:opacity-100 transition-opacity">
                                                            <button 
                                                                data-id="{{ $video->id }}"
                                                                data-title="{{ $video->title }}"
                                                                data-desc="{{ $video->description }}"
                                                                data-res="{{ $video->resources }}"
                                                                data-bunnyid="{{ $video->bunny_video_id }}"
                                                                data-bunnyembed="{{ $video->bunny_embed_url }}"
                                                                data-vurl="{{ $video->video_url }}"
                                                                onclick="previewModule(this)"
                                                                class="w-8 h-8 rounded-lg bg-primary text-white flex items-center justify-center hover:scale-110 transition-transform">
                                                                <i class="fas fa-play text-[9px]"></i>
                                                            </button>
                                                            <form action="{{ route('admin.beginner-guide.destroy', $video->id) }}" method="POST" onsubmit="return confirm('Remove module database entry?')">
                                                                @csrf
                                                                @method('DELETE')
                                                                <button type="submit" class="w-8 h-8 rounded-lg bg-rose-500 text-white flex items-center justify-center hover:scale-110 transition-transform">
                                                                    <i class="fas fa-trash-alt text-[9px]"></i>
                                                                </button>
                                                            </form>
                                                        </div>
                                                    </div>
                                                    <h4 class="font-black text-mainText text-xs truncate leading-relaxed">{{ $video->title }}</h4>
                                                    <p class="text-[9px] text-mutedText font-bold uppercase tracking-widest opacity-60">
                                                        Index #{{ $video->order_column }} • {{ $video->bunny_video_id ? 'CDN Stream' : 'Legacy' }}
                                                    </p>
                                                </div>
                                            @endforeach
                                        </div>
                                    </div>
                                @endif
                            @endforeach
                            
                            @if(!$found)
                                <div class="p-16 text-center">
                                    <div class="w-16 h-16 bg-gray-50 rounded-2xl flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                        <i class="fas fa-video-slash text-2xl text-gray-200"></i>
                                    </div>
                                    <h3 class="text-sm font-black text-mainText uppercase tracking-widest">No Roadmap Modules</h3>
                                    <p class="text-xs text-mutedText font-medium max-w-xs mx-auto mt-1 italic">Deploy your first training module using the configuration panel.</p>
                                </div>
                            @endif
                        </div>
                    </div>

                    {{-- Admin Preview Panel --}}
                    <div id="preview-panel" class="hidden animate-fade-in-down">
                        <div class="bg-navy rounded-[2rem] p-4 shadow-2xl overflow-hidden border border-white/10 ring-1 ring-black/20">
                            <div class="relative aspect-video rounded-2xl overflow-hidden bg-black shadow-inner border border-white/5">
                                <div id="module-player-wrapper" class="w-full h-full flex items-center justify-center">
                                    {{-- Video/Iframe Content --}}
                                </div>
                            </div>
                            
                            <div class="p-6 pt-8 bg-white rounded-2xl -mt-5 relative z-10 shadow-xl border border-gray-100">
                                <div class="flex items-center gap-2 mb-4">
                                    <div class="w-1.5 h-1.5 rounded-full bg-primary"></div>
                                    <span class="text-[10px] font-black text-primary uppercase tracking-[0.2em]">Module Live Test</span>
                                </div>
                                <h3 id="panel-title" class="text-xl font-black text-mainText mb-2">Select a video</h3>
                                <p id="panel-desc" class="text-xs text-mutedText font-medium leading-relaxed opacity-80"></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    @php
        $libId = config('services.bunny.library_id');
        $secKey = env('BUNNY_SECURITY_KEY', '');
    @endphp

    <script>
        const LIBRARY_ID = "{{ $libId }}";
        const SECURITY_KEY = "{{ $secKey }}";

        function previewModule(el) {
            const data = el.dataset;
            const panel = document.getElementById('preview-panel');
            const wrapper = document.getElementById('module-player-wrapper');
            const titleEl = document.getElementById('panel-title');
            const descEl = document.getElementById('panel-desc');

            panel.classList.remove('hidden');
            titleEl.textContent = data.title;
            descEl.textContent = data.desc || 'No metadata description provided for this module.';

            // Reset wrapper and player
            wrapper.innerHTML = "";

            let finalSrc = "";
            let useIframe = false;

            if (data.bunnyid && data.bunnyid.length > 5) {
                // Secure Bunny Preview Logic
                const expires = Math.floor(Date.now() / 1000) + 14400; // 4 hours
                // Note: Simple hash estimation for admin preview (Real Hash logic needs true crypto or server-side pass)
                // We'll use a direct link if security isn't hyper-critical in admin preview, OR just render what is given
                const bunnySrc = `https://iframe.mediadelivery.net/embed/${LIBRARY_ID}/${data.bunnyid}?autoplay=false&preload=true&responsive=true`;
                finalSrc = bunnySrc;
                useIframe = true;
            } else if (data.bunnyembed && data.bunnyembed.includes('iframe')) {
                // Direct Embed Tag
                wrapper.innerHTML = data.bunnyembed;
                return;
            } else if (data.vurl) {
                // Fallback URL
                finalSrc = data.vurl;
                useIframe = finalSrc.includes('iframe') || finalSrc.includes('mediadelivery');
            }

            if (useIframe) {
                const iframe = document.createElement('iframe');
                iframe.src = finalSrc;
                iframe.style.width = '100%';
                iframe.style.height = '100%';
                iframe.style.border = '0';
                iframe.allow = 'accelerometer;gyroscope;autoplay;encrypted-media;picture-in-picture;';
                iframe.allowFullscreen = true;
                wrapper.appendChild(iframe);
            } else if (finalSrc) {
                const video = document.createElement('video');
                video.controls = true;
                video.className = "w-full h-full bg-black";
                const source = document.createElement('source');
                source.src = finalSrc;
                source.type = "video/mp4";
                video.appendChild(source);
                wrapper.appendChild(video);
                video.load();
            }

            // Smooth scroll to preview
            setTimeout(() => {
                panel.scrollIntoView({ behavior: 'smooth', block: 'center' });
            }, 50);
        }
    </script>
@endsection
