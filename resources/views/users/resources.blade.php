@extends('layouts.user.app')

@section('content')
    <div class="max-w-[1600px] mx-auto pb-12">
        {{-- Tabs Navigation --}}
        <div class="mb-8 border-b border-gray-200">
            <div class="flex gap-6 overflow-x-auto no-scrollbar">
                <button class="tab-trigger whitespace-nowrap px-4 py-4 font-bold text-mainText border-b-2 border-primary transition-colors" data-tab="product-knowledge">Product Knowledge</button>
                <button class="tab-trigger whitespace-nowrap px-4 py-4 font-bold text-mutedText hover:text-mainText border-b-2 border-transparent transition-colors" data-tab="beginners-guide">Beginners Guide</button>
                @foreach($resourceCategories as $category)
                    <button class="tab-trigger whitespace-nowrap px-4 py-4 font-bold text-mutedText hover:text-mainText border-b-2 border-transparent transition-colors text-capitalize" data-tab="cat-{{ $category->id }}">{{ $category->name }}</button>
                @endforeach
            </div>
        </div>

        {{-- Tab Content: Product Knowledge --}}
        <div id="product-knowledge-tab" class="tab-content transition-opacity duration-300">
            @if($productKnowledge->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($productKnowledge as $resource)
                        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100 overflow-hidden group flex flex-col h-full">
                            {{-- Top Gradient & Icon Area --}}
                            @php
                                $colors = [
                                    ['from-blue-400', 'to-blue-600', 'text-blue-500'],
                                    ['from-red-400', 'to-red-600', 'text-red-500'],
                                    ['from-green-400', 'to-green-600', 'text-green-500'],
                                    ['from-purple-400', 'to-purple-600', 'text-purple-500']
                                ];
                                $colorSet = $colors[$loop->index % 4];
                                $icon = 'fa-file-alt';
                                if(str_contains(strtolower($resource->file_type), 'video') || str_contains(strtolower($resource->file_path), 'mp4')) {
                                    $icon = 'fa-play-circle';
                                    $colorSet = $colors[0];
                                } elseif(str_contains(strtolower($resource->file_type), 'pdf') || str_contains(strtolower($resource->title), 'presentation')) {
                                    $icon = 'fa-desktop';
                                    $colorSet = $colors[1];
                                }
                            @endphp
                            <div class="h-40 bg-gradient-to-b {{ $colorSet[0] }} {{ $colorSet[1] }} opacity-90 flex items-center justify-center relative overflow-hidden">
                                <div class="absolute inset-0 bg-white/10 transform rotate-12 scale-150"></div>
                                <div class="relative w-20 h-20 bg-white rounded-2xl shadow-inner flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300">
                                    <i class="fas {{ $icon }} text-4xl {{ $colorSet[2] }}"></i>
                                </div>
                            </div>
                            <div class="p-5 flex-1 flex flex-col justify-between bg-white text-center border-t border-gray-100">
                                <h3 class="font-bold text-mainText text-lg mb-2 line-clamp-2">{{ $resource->title }}</h3>
                                <p class="text-xs font-bold text-mutedText uppercase tracking-wider mb-4">{{ $resource->file_type ?? 'RESOURCE' }}</p>
                                @if($resource->file_path)
                                    <a href="{{ $resource->file_path }}" target="_blank" class="block w-full py-2.5 px-4 rounded-lg bg-gray-50 hover:bg-primary/10 text-primary font-bold text-sm border border-gray-200 hover:border-primary/20 transition-colors">View Resource</a>
                                @else
                                    <button disabled class="w-full py-2.5 px-4 rounded-lg bg-gray-50 text-gray-400 font-bold text-sm border border-gray-100 cursor-not-allowed">Not Available</button>
                                @endif
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl p-12 border border-gray-100 shadow-sm text-center">
                    <div class="w-20 h-20 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-4"><i class="fas fa-folder-open text-3xl text-gray-300"></i></div>
                    <h3 class="text-lg font-bold text-mainText mb-2">No Resources Found</h3>
                    <p class="text-mutedText">Product knowledge resources will appear here once they are added.</p>
                </div>
            @endif
        </div>

        {{-- Tab Content: Beginners Guide --}}
        <div id="beginners-guide-tab" class="tab-content hidden transition-opacity duration-300">
            @if($beginnersGuide->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($beginnersGuide as $video)
                        <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100 overflow-hidden group flex flex-col h-full">
                            <div class="h-40 bg-gradient-to-b from-blue-400 to-blue-600 opacity-90 flex items-center justify-center relative overflow-hidden">
                                <div class="absolute inset-0 bg-white/10 transform -rotate-12 scale-150"></div>
                                <div class="absolute top-3 left-3 bg-white/20 backdrop-blur-sm rounded-full px-3 py-1 border border-white/30 text-white text-xs font-bold uppercase tracking-wider">{{ $video->category }}</div>
                                <div class="relative w-20 h-20 bg-white rounded-2xl shadow-inner flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300"><i class="fas fa-play text-4xl text-blue-500 ml-1"></i></div>
                            </div>
                            <div class="p-5 flex-1 flex flex-col justify-between bg-white text-center border-t border-gray-100">
                                <h3 class="font-bold text-mainText text-lg mb-2 line-clamp-2">{{ $video->title }}</h3>
                                <p class="text-xs text-mutedText line-clamp-2 mb-4">{{ $video->description }}</p>
                                <a href="{{ route('student.beginner-guide') }}?video={{ $video->id }}" class="block w-full py-2.5 px-4 rounded-lg bg-gray-50 hover:bg-primary/10 text-primary font-bold text-sm border border-gray-200 hover:border-primary/20 transition-colors">Watch Video</a>
                            </div>
                        </div>
                    @endforeach
                </div>
            @else
                <div class="bg-white rounded-2xl p-12 border border-gray-100 shadow-sm text-center">
                    <div class="w-20 h-20 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-4"><i class="fas fa-video text-3xl text-gray-300"></i></div>
                    <h3 class="text-lg font-bold text-mainText mb-2">No Videos Found</h3>
                    <p class="text-mutedText">Beginner's guide videos will appear here once they are added.</p>
                </div>
            @endif
        </div>

        {{-- Dynamic Category Tabs --}}
        @foreach($resourceCategories as $category)
            <div id="cat-{{ $category->id }}-tab" class="tab-content hidden transition-opacity duration-300">
                @if($category->resources->count() > 0)
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                        @foreach($category->resources as $resource)
                            <div class="bg-white rounded-xl shadow-sm hover:shadow-md transition-shadow duration-300 border border-gray-100 overflow-hidden group flex flex-col h-full">
                                {{-- Top Gradient & Icon Area --}}
                                @php
                                    $colors = [
                                        ['from-emerald-400', 'to-emerald-600', 'text-emerald-500'],
                                        ['from-amber-400', 'to-amber-600', 'text-amber-500'],
                                        ['from-cyan-400', 'to-cyan-600', 'text-cyan-500'],
                                        ['from-rose-400', 'to-rose-600', 'text-rose-500']
                                    ];
                                    $colorSet = $colors[$loop->index % 4];
                                @endphp
                                <div class="h-40 bg-gradient-to-b {{ $colorSet[0] }} {{ $colorSet[1] }} opacity-90 flex items-center justify-center relative overflow-hidden">
                                    <div class="absolute inset-0 bg-white/10 transform rotate-12 scale-150"></div>
                                    <div class="relative w-20 h-20 bg-white rounded-2xl shadow-inner flex items-center justify-center transform group-hover:scale-110 transition-transform duration-300">
                                        <i class="fas {{ $resource->icon ?? 'fa-link' }} text-4xl {{ $colorSet[2] }}"></i>
                                    </div>
                                </div>
                                <div class="p-5 flex-1 flex flex-col justify-between bg-white text-center border-t border-gray-100">
                                    <h3 class="font-bold text-mainText text-lg mb-2 line-clamp-2">{{ $resource->title }}</h3>
                                    <p class="text-xs font-bold text-mutedText uppercase tracking-wider mb-4">{{ $category->name }}</p>
                                    <a href="{{ $resource->link_url }}" target="_blank" class="block w-full py-2.5 px-4 rounded-lg bg-gray-50 hover:bg-primary/10 text-primary font-bold text-sm border border-gray-200 hover:border-primary/20 transition-colors">Access Resource</a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="bg-white rounded-2xl p-12 border border-gray-100 shadow-sm text-center">
                        <div class="w-20 h-20 mx-auto bg-gray-50 rounded-full flex items-center justify-center mb-4"><i class="fas fa-link text-3xl text-gray-300"></i></div>
                        <h3 class="text-lg font-bold text-mainText mb-2">Empty Category</h3>
                        <p class="text-mutedText">No resources have been added to this category yet.</p>
                    </div>
                @endif
            </div>
        @endforeach
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const tabs = document.querySelectorAll('.tab-trigger');
            const contents = document.querySelectorAll('.tab-content');

            tabs.forEach(tab => {
                tab.addEventListener('click', () => {
                    const targetId = tab.dataset.tab + '-tab';
                    
                    tabs.forEach(t => {
                        t.classList.remove('text-mainText', 'border-primary');
                        t.classList.add('text-mutedText', 'border-transparent');
                    });
                    tab.classList.remove('text-mutedText', 'border-transparent');
                    tab.classList.add('text-mainText', 'border-primary');
                    
                    contents.forEach(content => {
                        if (content.id === targetId) {
                            content.classList.remove('hidden');
                            setTimeout(() => {
                                content.classList.add('opacity-100');
                                content.classList.remove('opacity-0');
                            }, 50);
                        } else {
                            content.classList.add('opacity-0');
                            content.classList.remove('opacity-100');
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
