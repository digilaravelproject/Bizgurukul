<div class="bg-surface rounded-[2rem] border border-primary shadow-xl shadow-primary/5 p-8 animate-fade-in-up">
    <form action="{{ isset($course) ? route('admin.courses.update', $course->id) : route('admin.courses.store') }}"
          method="POST"
          enctype="multipart/form-data"
          class="space-y-8">
        @csrf

        {{-- IMPORTANT: Update ke liye PUT method --}}
        @if(isset($course)) @method('PUT') @endif

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
            {{-- LEFT COLUMN: Inputs --}}
            <div class="lg:col-span-2 space-y-6">
                {{-- Title --}}
                <div class="group">
                    <label class="block text-xs font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Course Title</label>
                    <input type="text" name="title" value="{{ old('title', $course->title ?? '') }}" required
                        class="w-full h-14 rounded-2xl bg-white px-5 text-sm font-bold text-mainText border border-gray-300 focus:border-primary focus:ring-0 transition-all outline-none placeholder-mutedText/40"
                        placeholder="e.g. Master Full Stack Development">
                </div>

                {{-- Category & SubCategory --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Category --}}
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Category</label>
                        <div class="relative">
                            <select name="category_id" id="cat_selector" required @change="fetchSubCategories($event.target.value)"
                                class="w-full h-12 rounded-xl bg-white px-4 text-sm font-bold text-mainText border border-gray-300 focus:border-primary focus:ring-0 transition-all outline-none cursor-pointer">
                                <option value="">Select Category</option>
                                @foreach($categories as $c)
                                    <option value="{{$c->id}}" {{ (old('category_id', $course->category_id ?? '') == $c->id) ? 'selected' : '' }}>{{$c->name}}</option>
                                @endforeach
                            </select>
                            {{-- Custom Arrow with Focus Color Match --}}
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>

                    {{-- Sub Category --}}
                    <div>
                        <label class="block text-xs font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Sub Category</label>
                        <div class="relative">
                            <select name="sub_category_id" id="sub_selector"
                                class="w-full h-12 rounded-xl bg-white px-4 text-sm font-bold text-mainText border border-gray-300 focus:border-primary focus:ring-0 transition-all outline-none cursor-pointer">
                                <option value="">Select Sub Category</option>
                                @if(isset($course) && $course->subCategory)
                                    <option value="{{ $course->sub_category_id }}" selected>{{ $course->subCategory->name }}</option>
                                @endif
                            </select>
                            {{-- Custom Arrow with Focus Color Match --}}
                            <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-primary">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Description --}}
                <div>
                    <label class="block text-xs font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Description</label>
                    <textarea name="description" rows="5"
                        class="w-full rounded-2xl bg-white px-5 py-4 text-sm font-medium text-mainText border border-gray-300 focus:border-primary focus:ring-0 transition-all outline-none placeholder-mutedText/40 resize-none"
                        placeholder="What will students learn in this course?">{{ old('description', $course->description ?? '') }}</textarea>
                </div>
            </div>

            {{-- RIGHT COLUMN: Media Only (Price moved to Publish) --}}
            <div class="lg:col-span-1 space-y-6">

                {{-- Thumbnail Upload --}}
                <div x-data="imageUploader()">
                    <label class="block text-xs font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Thumbnail</label>

                    <div class="relative w-full h-64 rounded-2xl border-2 border-dashed border-primary bg-primary/20 hover:bg-navy hover:border-primary transition-all flex flex-col items-center justify-center cursor-pointer overflow-hidden group"
                         @dragover.prevent="dragover = true"
                         @dragleave.prevent="dragover = false"
                         @drop.prevent="handleDrop($event)"
                         @click="$refs.fileInput.click()">

                        {{-- Preview Image --}}
                        <template x-if="previewUrl">
                            <div class="absolute inset-0">
                                <img :src="previewUrl" class="w-full h-full object-cover transition-transform duration-500 group-hover:scale-110">
                                <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                    <span class="text-white text-xs font-bold bg-white/20 backdrop-blur-md px-4 py-2 rounded-full border border-white/30">Change Image</span>
                                </div>
                            </div>
                        </template>

                        {{-- Default State --}}
                        <div x-show="!previewUrl" class="text-center p-6">
                            <div class="w-14 h-14 rounded-full bg-surface flex items-center justify-center mx-auto mb-3 shadow-md shadow-primary text-primary group-hover:scale-110 transition-transform">
                                <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                            </div>
                            <p class="text-sm font-bold text-mainText">Click or Drag image</p>
                            <p class="text-[10px] text-mutedText/60 mt-1">Supported: PNG, JPG, WebP | Max 5MB</p>
                        </div>

                        <input type="file" name="thumbnail" x-ref="fileInput" class="hidden" @change="handleFileSelect" accept="image/*" {{ isset($course) ? '' : 'required' }}>
                    </div>
                    <div class="mt-2 flex items-center justify-between px-1">
                        <p class="text-[10px] text-mutedText/60 font-medium">Supported: PNG, JPG, WebP</p>
                        <p class="text-[10px] text-mutedText/60 font-medium">Max Size: 5MB</p>
                    </div>
                </div>
            </div>
        </div>
<input type="hidden" name="redirect_tab" value="lessons">
        {{-- Footer Actions --}}
        <div class="pt-6 border-t border-primary/5 flex justify-end">
            <button type="submit" class="brand-gradient px-10 py-4 rounded-xl text-customWhite text-xs font-black uppercase tracking-widest shadow-lg shadow-primary/20 hover:shadow-primary/40 hover:-translate-y-1 transition-all transform active:scale-95">
                {{ isset($course) ? 'Update & Continue →' : 'Save & Continue →' }}
            </button>
        </div>
    </form>
</div>

{{-- JS for Image Upload --}}
<script>
function imageUploader() {
    return {
        previewUrl: '{{ isset($course) && $course->thumbnail ? $course->thumbnail : "" }}',
        dragover: false,
        handleFileSelect(e) {
            const file = e.target.files[0];
            if (file) this.readFile(file);
        },
        handleDrop(e) {
            this.dragover = false;
            const file = e.dataTransfer.files[0];
            if (file) {
                this.$refs.fileInput.files = e.dataTransfer.files;
                this.readFile(file);
            }
        },
        readFile(file) {
            const reader = new FileReader();
            reader.onload = e => this.previewUrl = e.target.result;
            reader.readAsDataURL(file);
        }
    }
}
</script>
