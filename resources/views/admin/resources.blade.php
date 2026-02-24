@extends('layouts.admin')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4">
        <div class="max-w-[1600px] mx-auto space-y-8">
            
            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-bold text-mainText mb-2">Resource Center</h1>
                    <p class="text-mutedText">Manage documents, PDFs, presentations, and guide videos for students.</p>
                </div>
            </div>

            {{-- Tabs Navigation --}}
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-2 overflow-x-auto">
                <div class="flex justify-center gap-2 min-w-max">
                    <button class="tab-trigger flex-1 md:flex-none px-6 py-3 font-bold rounded-xl transition-all duration-300 bg-primary/10 text-primary" data-tab="product-knowledge">
                        <i class="fas fa-file-alt mr-2 relative top-[1px]"></i> Product Knowledge
                    </button>
                    <button class="tab-trigger flex-1 md:flex-none px-6 py-3 font-bold rounded-xl transition-all duration-300 text-mutedText hover:bg-gray-50 hover:text-mainText" data-tab="beginners-guide">
                        <i class="fas fa-play-circle mr-2 relative top-[1px]"></i> Beginner's Guide
                    </button>
                </div>
            </div>

            {{-- Tab Content: Product Knowledge --}}
            <div id="product-knowledge-tab" class="tab-content transition-opacity duration-300">
                <div class="flex items-center justify-between mb-6 pl-2 border-l-4 border-primary">
                    <h2 class="text-xl font-bold text-mainText ml-3">Product Knowledge Materials</h2>
                    <span class="text-xs font-bold text-mutedText uppercase tracking-widest">{{ $productKnowledge->count() }} Items</span>
                </div>
                
                @if($productKnowledge->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($productKnowledge as $resource)
                            <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border border-gray-100 overflow-hidden group flex flex-col h-full">
                                {{-- Top Gradient & Icon Area --}}
                                @php
                                    $colors = [
                                        ['from-blue-400', 'to-blue-600', 'text-blue-500', 'bg-blue-50'],
                                        ['from-rose-400', 'to-rose-600', 'text-rose-500', 'bg-rose-50'],
                                        ['from-emerald-400', 'to-emerald-600', 'text-emerald-500', 'bg-emerald-50'],
                                        ['from-amber-400', 'to-amber-500', 'text-amber-500', 'bg-amber-50'],
                                        ['from-violet-400', 'to-violet-600', 'text-violet-500', 'bg-violet-50']
                                    ];
                                    $colorSet = $colors[$loop->index % 5];
                                    
                                    $icon = 'fa-file-alt'; // Default icon
                                    $typeLower = strtolower($resource->file_type ?? '');
                                    $pathLower = strtolower($resource->file_path ?? '');
                                    $titleLower = strtolower($resource->title ?? '');

                                    if(str_contains($typeLower, 'video') || str_contains($pathLower, 'mp4')) {
                                        $icon = 'fa-file-video';
                                        $colorSet = $colors[0]; // Blue
                                    } elseif(str_contains($typeLower, 'pdf')) {
                                        $icon = 'fa-file-pdf';
                                        $colorSet = $colors[1]; // Rose
                                    } elseif(str_contains($titleLower, 'presentation') || str_contains($typeLower, 'ppt')) {
                                        $icon = 'fa-desktop';
                                        $colorSet = $colors[4]; // Violet
                                    } elseif(str_contains($typeLower, 'image') || str_contains($pathLower, 'jpg') || str_contains($pathLower, 'png')) {
                                        $icon = 'fa-file-image';
                                        $colorSet = $colors[2]; // Emerald
                                    }
                                @endphp
                                
                                <div class="h-40 bg-gradient-to-br {{ $colorSet[0] }} {{ $colorSet[1] }} relative overflow-hidden flex items-center justify-center">
                                    {{-- Background decorative shapes --}}
                                    <div class="absolute -top-10 -right-10 w-32 h-32 bg-white/10 rounded-full blur-xl transform group-hover:scale-150 transition-transform duration-700"></div>
                                    <div class="absolute -bottom-10 -left-10 w-32 h-32 bg-black/10 rounded-full blur-xl transform group-hover:scale-150 transition-transform duration-700"></div>
                                    
                                    <div class="relative w-24 h-24 bg-white rounded-[2rem] shadow-xl flex items-center justify-center transform group-hover:scale-110 group-hover:rotate-3 transition-all duration-300">
                                        <div class="absolute inset-0 rounded-[2rem] {{ $colorSet[3] }} opacity-50"></div>
                                        <i class="fas {{ $icon }} text-5xl {{ $colorSet[2] }} relative drop-shadow-sm"></i>
                                    </div>
                                </div>
                                
                                {{-- Content Area --}}
                                <div class="p-6 flex-1 flex flex-col justify-between bg-white relative z-10">
                                    <div class="text-center mb-6">
                                        <div class="inline-block px-3 py-1 rounded-full {{ $colorSet[3] }} {{ $colorSet[2] }} text-[10px] font-bold uppercase tracking-widest mb-3">
                                            {{ $resource->file_type ?? 'Document' }}
                                        </div>
                                        <h3 class="font-bold text-mainText text-lg line-clamp-2 leading-tight group-hover:text-primary transition-colors">{{ $resource->title }}</h3>
                                    </div>
                                    
                                    @if($resource->file_path)
                                        <a href="{{ $resource->file_path }}" target="_blank" 
                                           class="flex items-center justify-center w-full py-3.5 px-4 rounded-xl bg-gray-50 hover:bg-primary hover:text-white text-mainText font-bold text-sm transition-all duration-300 group/btn border border-gray-100 hover:border-transparent hover:shadow-lg hover:shadow-primary/30">
                                            <span>View Resource</span>
                                            <i class="fas fa-external-link-alt ml-2 text-xs opacity-50 group-hover/btn:opacity-100 group-hover/btn:-translate-y-0.5 group-hover/btn:translate-x-0.5 transition-all"></i>
                                        </a>
                                    @else
                                        <button disabled class="w-full py-3.5 px-4 rounded-xl bg-gray-50 text-gray-400 font-bold text-sm border border-gray-100 cursor-not-allowed">
                                            <i class="fas fa-lock mr-2 text-xs mt-[1px]"></i> File Unavailable
                                        </button>
                                    @endif
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-3xl p-16 border border-gray-100 shadow-sm text-center">
                        <div class="w-24 h-24 mx-auto bg-blue-50 rounded-full flex items-center justify-center mb-6 shadow-inner">
                            <i class="fas fa-folder-open text-4xl text-blue-400"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-mainText mb-2">No Resources Found</h3>
                        <p class="text-mutedText">Product knowledge materials will appear here once they are added.</p>
                    </div>
                @endif
            </div>

            {{-- Tab Content: Beginners Guide --}}
            <div id="beginners-guide-tab" class="tab-content hidden transition-opacity duration-300">
                <div class="flex items-center justify-between mb-6 pl-2 border-l-4 border-blue-500">
                    <h2 class="text-xl font-bold text-mainText ml-3">Beginner's Guide Videos</h2>
                    <span class="text-xs font-bold text-mutedText uppercase tracking-widest">{{ $beginnersGuide->count() }} Videos</span>
                </div>

                @if($beginnersGuide->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($beginnersGuide as $index => $video)
                            <div class="bg-white rounded-2xl shadow-sm hover:shadow-lg hover:-translate-y-1 transition-all duration-300 border border-gray-100 overflow-hidden group flex flex-col h-full">
                                {{-- Thumbnail/Top Area --}}
                                <div class="h-44 bg-slate-900 relative overflow-hidden group-hover:shadow-[inset_0_0_50px_rgba(0,0,0,0.5)] transition-all">
                                    {{-- Fake Video Thumbnail Background --}}
                                    <div class="absolute inset-0 opacity-40 bg-[radial-gradient(ellipse_at_center,_var(--tw-gradient-stops))] from-slate-700 via-slate-900 to-black"></div>
                                    
                                    {{-- Category Badge --}}
                                    <div class="absolute top-4 left-4 z-20">
                                        <span class="bg-white/10 backdrop-blur-md text-white text-[10px] font-bold uppercase tracking-widest px-3 py-1.5 rounded-full border border-white/20 shadow-sm">
                                            {{ $video->category }}
                                        </span>
                                    </div>
                                    
                                    {{-- Video Number Badge --}}
                                    <div class="absolute top-4 right-4 z-20">
                                        <div class="w-8 h-8 rounded-full bg-black/40 backdrop-blur-md border border-white/20 flex items-center justify-center text-white font-bold text-xs">
                                            {{ $index + 1 }}
                                        </div>
                                    </div>

                                    {{-- Play Button --}}
                                    <div class="absolute inset-0 flex items-center justify-center z-10">
                                        <div class="w-16 h-16 bg-white/10 backdrop-blur-sm rounded-full flex items-center justify-center border-2 border-white/30 group-hover:scale-110 group-hover:bg-blue-600 group-hover:border-blue-500 transition-all duration-300 shadow-xl group-hover:shadow-blue-500/50">
                                            <i class="fas fa-play text-white text-xl ml-1 group-hover:text-white transition-colors"></i>
                                        </div>
                                    </div>
                                </div>
                                
                                {{-- Content Area --}}
                                <div class="p-6 flex-1 flex flex-col justify-between bg-white relative">
                                    <div class="mb-6">
                                        <h3 class="font-bold text-mainText text-lg mb-2 line-clamp-2 leading-snug group-hover:text-blue-600 transition-colors">{{ $video->title }}</h3>
                                        <p class="text-sm text-mutedText line-clamp-2 leading-relaxed">{{ $video->description }}</p>
                                    </div>
                                    
                                    <a href="{{ asset('storage/' . $video->video_path) }}" target="_blank"
                                       class="flex items-center justify-center w-full py-3.5 px-4 rounded-xl bg-blue-50 hover:bg-blue-600 hover:text-white text-blue-600 font-bold text-sm transition-all duration-300 border border-blue-100 hover:border-transparent hover:shadow-lg hover:shadow-blue-600/30">
                                        <i class="fas fa-eye mr-2 relative top-[1px]"></i> View Video
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-3xl p-16 border border-gray-100 shadow-sm text-center">
                        <div class="w-24 h-24 mx-auto bg-slate-100 rounded-full flex items-center justify-center mb-6 shadow-inner">
                            <i class="fas fa-film text-4xl text-slate-400"></i>
                        </div>
                        <h3 class="text-2xl font-bold text-mainText mb-2">No Videos Found</h3>
                        <p class="text-mutedText">Beginner's guide videos will appear here once they are added.</p>
                        <a href="{{ route('admin.beginner-guide') }}" class="inline-block mt-6 px-6 py-3 bg-primary text-white font-bold rounded-xl hover:shadow-lg hover:-translate-y-1 transition-all">
                            Add First Video
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab-trigger');
            const contents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const targetId = tab.dataset.tab + '-tab';
                    
                    // Reset styling for all tabs
                    tabs.forEach(t => {
                        t.classList.remove('bg-primary/10', 'text-primary');
                        t.classList.add('text-mutedText', 'hover:bg-gray-50', 'hover:text-mainText');
                    });
                    
                    // Apply active styling to clicked tab
                    tab.classList.remove('text-mutedText', 'hover:bg-gray-50', 'hover:text-mainText');
                    tab.classList.add('bg-primary/10', 'text-primary');
                    
                    // Handle content switching with a smooth fade
                    contents.forEach(content => {
                        if (content.id === targetId) {
                            content.classList.remove('hidden');
                            // Tiny delay to ensure display:block is rendered before fading in
                            setTimeout(() => {
                                content.classList.remove('opacity-0');
                                content.classList.add('opacity-100');
                            }, 30);
                        } else {
                            content.classList.remove('opacity-100');
                            content.classList.add('opacity-0');
                            // Wait for fade out to finish before hiding
                            setTimeout(() => {
                                content.classList.add('hidden');
                            }, 300);
                        }
                    });
                });
            });
        });
    </script>
@endsection
