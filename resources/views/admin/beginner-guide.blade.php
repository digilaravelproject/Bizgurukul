@extends('layouts.admin')

@section('content')
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-gray-100 py-8 px-4">
        <div class="max-w-6xl mx-auto space-y-8">
            {{-- Header --}}
            <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4">
                <div>
                    <h1 class="text-4xl font-bold text-mainText mb-2">Beginner's Guide Management</h1>
                    <p class="text-mutedText">Manage and organize training videos for different experience levels</p>
                </div>
                <div class="flex gap-3">
                    <div class="px-4 py-3 bg-white rounded-xl border border-gray-200 shadow-sm">
                        <div class="text-xs font-bold text-mutedText uppercase tracking-wide">Total Videos</div>
                        <div class="text-2xl font-bold text-primary">{{ $videos->count() }}</div>
                    </div>
                </div>
            </div>

            {{-- Alerts --}}
            @if(session('success'))
                <div class="p-4 bg-green-50 border border-green-200 text-green-700 rounded-xl flex items-start gap-3">
                    <i class="fas fa-check-circle mt-1"></i>
                    <div>{{ session('success') }}</div>
                </div>
            @endif
            @if($errors->any())
                <div class="p-4 bg-red-50 border border-red-200 text-red-700 rounded-xl">
                    <div class="font-bold mb-2">Please fix the following errors:</div>
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $err)
                            <li>{{ $err }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                {{-- Left: Form --}}
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden sticky top-8">
                        <div class="bg-gradient-to-r from-primary to-blue-600 px-6 py-6">
                            <h2 class="text-white font-bold text-lg flex items-center gap-2">
                                <i class="fas fa-plus-circle"></i>
                                Add New Video
                            </h2>
                        </div>

                        <form action="{{ route('admin.beginner-guide.store') }}" method="POST" enctype="multipart/form-data" class="p-6 space-y-5">
                            @csrf
                            
                            <div>
                                <label class="block text-xs font-bold text-mainText uppercase tracking-wider mb-2">Video Title</label>
                                <input type="text" name="title" value="{{ old('title') }}" placeholder="e.g., Orientation & Mindset" required
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition" />
                                @error('title')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-mainText uppercase tracking-wider mb-2">Category</label>
                                <select name="category" required class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition">
                                    <option value="">Select category...</option>
                                    <option value="foundation" {{ old('category') == 'foundation' ? 'selected' : '' }}>Foundation</option>
                                    <option value="growth" {{ old('category') == 'growth' ? 'selected' : '' }}>Growth</option>
                                    <option value="scale" {{ old('category') == 'scale' ? 'selected' : '' }}>Scale</option>
                                </select>
                                @error('category')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-mainText uppercase tracking-wider mb-2">Description</label>
                                <textarea name="description" rows="3" placeholder="Brief overview of the video content..." 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition resize-none">{{ old('description') }}</textarea>
                                @error('description')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-mainText uppercase tracking-wider mb-2">Resources (Links / Notes)</label>
                                <textarea name="resources" rows="2" placeholder="Add relevant resources or notes..." 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition resize-none">{{ old('resources') }}</textarea>
                                @error('resources')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-mainText uppercase tracking-wider mb-2">Video File</label>
                                <div class="relative">
                                    <input type="file" name="video" accept="video/*" required 
                                        class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition file:mr-3 file:px-3 file:py-1.5 file:rounded file:border-0 file:text-xs file:font-bold file:bg-primary/10 file:text-primary" />
                                </div>
                                @error('video')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-mainText uppercase tracking-wider mb-2">Video Order</label>
                                <input type="number" name="order_column" value="{{ old('order_column', 0) }}" 
                                    class="w-full px-4 py-3 rounded-lg border border-gray-200 focus:border-primary focus:ring-2 focus:ring-primary/10 outline-none transition" />
                                <p class="text-xs text-mutedText mt-1">Lower numbers appear first</p>
                                @error('order_column')<span class="text-xs text-red-600">{{ $message }}</span>@enderror
                            </div>

                            <button type="submit" class="w-full bg-gradient-to-r from-primary to-blue-600 text-white py-4 rounded-lg font-bold uppercase tracking-wider hover:shadow-lg transition-all active:scale-95">
                                <i class="fas fa-upload mr-2"></i> Add Video
                            </button>
                        </form>
                    </div>
                </div>

                {{-- Right: Videos List & Preview --}}
                <div class="lg:col-span-2 space-y-8">
                    {{-- Videos List --}}
                    <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                        <div class="bg-gradient-to-r from-gray-50 to-gray-100 px-6 py-6 border-b border-gray-200">
                            <h2 class="text-mainText font-bold text-lg flex items-center gap-2">
                                <i class="fas fa-video"></i>
                                Uploaded Videos
                            </h2>
                        </div>

                        @if($videos->count() > 0)
                            <div class="divide-y divide-gray-200">
                                @foreach($videos->groupBy('category') as $category => $categoryVideos)
                                    <div class="p-6">
                                        <h3 class="text-sm font-bold text-primary uppercase tracking-wider mb-4 capitalize flex items-center gap-2">
                                            <span class="w-3 h-3 rounded-full bg-primary"></span>
                                            {{ $category }}
                                        </h3>
                                        <div class="space-y-3">
                                            @foreach($categoryVideos->sortBy('order_column') as $video)
                                                <button onclick="previewVideo({{ $video->id }}, '{{ addslashes($video->title) }}', '{{ addslashes($video->description) }}', '{{ addslashes($video->resources ?? '') }}', '{{ asset($video->video_url) }}')" 
                                                    class="w-full flex items-start justify-between bg-gray-50 p-4 rounded-lg hover:bg-blue-50 hover:border-l-4 hover:border-l-primary transition-all group cursor-pointer text-left">
                                                    <div class="flex-1 min-w-0">
                                                        <div class="flex items-center gap-3 mb-1">
                                                            <span class="inline-block w-6 h-6 bg-primary text-white rounded-full text-xs font-bold flex items-center justify-center">
                                                                {{ $loop->iteration }}
                                                            </span>
                                                            <h4 class="font-bold text-mainText truncate">{{ $video->title }}</h4>
                                                        </div>
                                                        <p class="text-xs text-mutedText line-clamp-2 ml-9">{{ $video->description }}</p>
                                                    </div>
                                                    <?php /*<form method="POST" action="{{ route('admin.beginner-guide.destroy', $video->id) }}" 
                                                        onclick="event.stopPropagation();"
                                                        class="ml-3 opacity-0 group-hover:opacity-100 transition-opacity">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" onclick="return confirm('Delete this video?')" 
                                                            class="flex-shrink-0 w-8 h-8 rounded-lg bg-red-50 text-red-600 hover:bg-red-100 flex items-center justify-center transition-colors">
                                                            <i class="fas fa-trash-alt text-xs"></i>
                                                        </button>
                                                    </form> */?>
                                                </button>
                                            @endforeach
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        @else
                            <div class="p-12 text-center">
                                <i class="fas fa-film text-4xl text-gray-300 mb-3"></i>
                                <p class="text-mutedText font-medium">No videos uploaded yet</p>
                                <p class="text-xs text-mutedText mt-1">Start by adding your first training video using the form</p>
                            </div>
                        @endif
                    </div>

                    {{-- Video Preview --}}
                    <div id="preview-container" class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden">
                        {{-- Video Player --}}
                        <div class="w-full bg-black">
                            <video id="preview-video" width="100%" height="auto" controls style="display: block; max-width: 100%; background-color: #000; cursor: pointer;" onclick="this.paused ? this.play() : this.pause();">
                                <source id="preview-source" src="" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>

                        {{-- Video Details --}}
                        <div class="p-6 space-y-6">
                            {{-- Title & Description --}}
                            <div>
                                <h3 id="preview-title" class="text-xl font-bold text-mainText mb-2">Select a video to preview</h3>
                                <p id="preview-description" class="text-sm text-mutedText leading-relaxed">Click on any video from the list to view its details</p>
                            </div>

                            {{-- Resources --}}
                            <div id="resources-section">
                                <h4 class="text-sm font-bold text-mainText mb-3 pb-3 border-b border-gray-200">Resources & Links</h4>
                                <div id="preview-resources" class="text-sm text-mutedText leading-relaxed whitespace-pre-wrap">No resources available</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function previewVideo(id, title, description, resources, videoUrl) {
            const titleEl = document.getElementById('preview-title');
            const descEl = document.getElementById('preview-description');
            const resourcesEl = document.getElementById('preview-resources');
            const sourceEl = document.getElementById('preview-source');
            const videoEl = document.getElementById('preview-video');
            
            // Update text content
            titleEl.textContent = title;
            descEl.textContent = description;
            
            // Update resources
            if (resources && resources.trim()) {
                resourcesEl.textContent = resources;
            } else {
                resourcesEl.textContent = 'No resources available';
            }
            
            // Update video source
            sourceEl.src = videoUrl;
            
            // Reset and reload video
            videoEl.load();
            videoEl.play();
            
            // Scroll to preview
            setTimeout(function() {
                document.getElementById('preview-container').scrollIntoView({ 
                    behavior: 'smooth',
                    block: 'start'
                });
            }, 100);
        }
        
        // Initialize with first video on page load
        document.addEventListener('DOMContentLoaded', function() {
            setTimeout(function() {
                const firstButton = document.querySelector('button[onclick*="previewVideo"]');
                if (firstButton) {
                    // Extract onclick parameters and simulate click
                    firstButton.click();
                }
            }, 300);
        });
    </script>
@endsection


