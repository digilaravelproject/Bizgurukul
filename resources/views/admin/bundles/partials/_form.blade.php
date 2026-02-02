@props(['bundle' => null, 'courses' => [], 'allBundles' => [], 'selectedCourses' => [], 'selectedBundles' => []])

<form action="{{ $bundle ? route('admin.bundles.update', $bundle->id) : route('admin.bundles.store') }}"
      method="POST" enctype="multipart/form-data" class="space-y-8">
    @csrf
    @if($bundle) @method('PUT') @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Left Column: Basic Details --}}
        <div class="lg:col-span-8 space-y-6">
            {{-- Title --}}
            <div class="bg-surface rounded-3xl p-6 border border-primary/5 shadow-lg shadow-primary/5">
                <label class="block text-sm font-bold text-mainText mb-2">Bundle Title</label>
                <input type="text" name="title" value="{{ old('title', $bundle->title ?? '') }}"
                       class="w-full h-12 px-4 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:border-primary focus:ring-0 transition-all font-medium placeholder-mutedText/50"
                       placeholder="e.g., Full Stack Developer Mastery Bundle">
                @error('title') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
            </div>

            {{-- Description --}}
            <div class="bg-surface rounded-3xl p-6 border border-primary/5 shadow-lg shadow-primary/5">
                <label class="block text-sm font-bold text-mainText mb-2">Description</label>
                <textarea name="description" rows="5"
                          class="w-full p-4 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:border-primary focus:ring-0 transition-all font-medium placeholder-mutedText/50"
                          placeholder="What's included in this bundle?">{{ old('description', $bundle->description ?? '') }}</textarea>
            </div>

            {{-- Content Selection (Courses & Bundles) --}}
            <div class="bg-surface rounded-3xl p-6 border border-primary/5 shadow-lg shadow-primary/5">
                <h3 class="text-lg font-bold text-mainText mb-4">Bundle Contents</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Courses Selection --}}
                    <div>
                        <label class="block text-xs font-bold text-mutedText uppercase tracking-wider mb-3">Select Courses</label>
                        <div class="max-h-64 overflow-y-auto pr-2 space-y-2 custom-scrollbar">
                            @foreach($courses as $course)
                                <label class="flex items-center p-3 rounded-xl border border-gray-100 hover:border-primary/30 hover:bg-primary/5 cursor-pointer transition-all group">
                                    <input type="checkbox" name="courses[]" value="{{ $course->id }}"
                                           {{ in_array($course->id, old('courses', $selectedCourses)) ? 'checked' : '' }}
                                           class="w-5 h-5 rounded-lg border-gray-300 text-primary focus:ring-primary/20">
                                    <span class="ml-3 text-sm font-medium text-mainText group-hover:text-primary">{{ $course->title }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Bundles Selection (Recursive) --}}
                    <div>
                        <label class="block text-xs font-bold text-mutedText uppercase tracking-wider mb-3">Include Other Bundles</label>
                        <div class="max-h-64 overflow-y-auto pr-2 space-y-2 custom-scrollbar">
                            @forelse($allBundles as $bItem)
                                <label class="flex items-center p-3 rounded-xl border border-gray-100 hover:border-purple-500/30 hover:bg-purple-50 cursor-pointer transition-all group">
                                    <input type="checkbox" name="bundles[]" value="{{ $bItem->id }}"
                                           {{ in_array($bItem->id, old('bundles', $selectedBundles)) ? 'checked' : '' }}
                                           class="w-5 h-5 rounded-lg border-gray-300 text-purple-600 focus:ring-purple-500/20">
                                    <span class="ml-3 text-sm font-medium text-mainText group-hover:text-purple-700">{{ $bItem->title }}</span>
                                </label>
                            @empty
                                <p class="text-xs text-mutedText italic p-2">No other bundles available.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Settings & Media --}}
        <div class="lg:col-span-4 space-y-6">
            {{-- Publish Status --}}
            <div class="bg-surface rounded-3xl p-6 border border-primary/5 shadow-lg shadow-primary/5">
                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-sm font-bold text-mainText">Publish Bundle</label>
                        <p class="text-xs text-mutedText mt-1">Make visible to students</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_published" value="0">
                        <input type="checkbox" name="is_published" value="1" class="sr-only peer"
                               {{ old('is_published', $bundle->is_published ?? false) ? 'checked' : '' }}>
                        <div class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                    </label>
                </div>
            </div>

            {{-- Price --}}
            <div class="bg-surface rounded-3xl p-6 border border-primary/5 shadow-lg shadow-primary/5">
                <label class="block text-sm font-bold text-mainText mb-2">Price (₹)</label>
                <div class="relative">
                    <span class="absolute inset-y-0 left-0 pl-4 flex items-center text-mutedText font-bold">₹</span>
                    <input type="number" step="0.01" name="price" value="{{ old('price', $bundle->price ?? '0.00') }}"
                           class="w-full h-12 pl-8 pr-4 bg-gray-50 border-transparent rounded-xl focus:bg-white focus:border-primary focus:ring-0 transition-all font-bold text-lg text-mainText">
                </div>
                @error('price') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
            </div>

           {{-- Thumbnail section with In-box Preview --}}
<div class="bg-surface rounded-3xl p-6 border border-primary/5 shadow-lg shadow-primary/5">
    <label class="block text-sm font-bold text-mainText mb-2">Thumbnail</label>

    <div x-data="{
            imagePreview: '{{ $bundle && $bundle->thumbnail_url ? $bundle->thumbnail_url : '' }}',
            handleFile(event) {
                const file = event.target.files[0];
                if (file) {
                    this.imagePreview = URL.createObjectURL(file);
                }
            }
         }" class="relative">

        <label class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer hover:bg-gray-50 hover:border-primary/50 transition-all group overflow-hidden relative">

            {{-- Placeholder (Icon and Text) - Only visible if no image exists --}}
            <div x-show="!imagePreview" class="flex flex-col items-center justify-center pt-5 pb-6">
                <svg class="w-10 h-10 mb-3 text-gray-400 group-hover:text-primary transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                </svg>
                <p class="text-xs text-mutedText font-medium">Click to upload thumbnail</p>
                <p class="text-[10px] text-mutedText/60 mt-1">PNG, JPG up to 2MB</p>
            </div>

            {{-- Image Preview - Takes over the whole box --}}
            <template x-if="imagePreview">
                <div class="absolute inset-0 w-full h-full">
                    <img :src="imagePreview" class="w-full h-full object-cover">
                    {{-- Overlay on hover to show "Change" --}}
                    <div class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                        <div class="bg-white/20 backdrop-blur-md px-4 py-2 rounded-lg border border-white/30 text-white text-xs font-bold uppercase tracking-wider">
                            Change Image
                        </div>
                    </div>
                </div>
            </template>

            {{-- Hidden File Input --}}
            <input type="file" name="thumbnail" class="hidden" accept="image/*" @change="handleFile($event)" />
        </label>
    </div>

    @error('thumbnail') <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p> @enderror
</div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-4 pt-6 border-t border-primary/5">
        <a href="{{ route('admin.bundles.index') }}" class="px-6 py-3 rounded-xl border border-transparent font-bold text-mutedText hover:bg-primary/5 transition-all">Cancel</a>
        <button type="submit" class="px-8 py-3 rounded-xl brand-gradient text-customWhite font-bold shadow-lg shadow-primary/30 hover:shadow-primary/50 hover:-translate-y-1 transition-all">
            {{ $bundle ? 'Update Bundle' : 'Create Bundle' }}
        </button>
    </div>
</form>
