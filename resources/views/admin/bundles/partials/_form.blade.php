@props(['bundle' => null, 'courses' => [], 'allBundles' => [], 'selectedCourses' => [], 'selectedBundles' => []])

<form action="{{ $bundle ? route('admin.bundles.update', $bundle->id) : route('admin.bundles.store') }}" method="POST"
    enctype="multipart/form-data" class="space-y-8">
    @csrf
    @if ($bundle)
        @method('PUT')
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-12 gap-8">
        {{-- Left Column: Basic Details --}}
        <div class="lg:col-span-8 space-y-6">
            {{-- Title --}}
            <div class="bg-surface rounded-3xl p-6 border border-primary/5 shadow-lg shadow-primary/5">
                <label class="block text-sm font-bold text-mainText mb-2">Bundle Title</label>
                <input type="text" name="title" value="{{ old('title', $bundle->title ?? '') }}"
                       class="w-full h-12 px-4 bg-white border border-gray-300 rounded-xl focus:border-primary focus:ring-0 transition-all font-medium placeholder-mutedText/50"
                       placeholder="e.g., Full Stack Developer Mastery Bundle">
                @error('title')
                    <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                @enderror
            </div>

            {{-- Description (Rich Text) --}}
            <div class="bg-surface rounded-3xl p-6 border border-primary/5 shadow-lg shadow-primary/5">
                <label class="block text-sm font-bold text-mainText mb-2">Description</label>
                <div id="quill-editor" class="bg-white">
                    {!! old('description', $bundle->description ?? '') !!}
                </div>
                <input type="hidden" name="description" id="description-input"
                    value="{{ old('description', $bundle->description ?? '') }}">
                @error('description')
                    <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                @enderror
            </div>

            {{-- Content Selection (Courses & Bundles) --}}
            <div class="bg-surface rounded-3xl p-6 border border-primary/5 shadow-lg shadow-primary/5">
                <h3 class="text-lg font-bold text-mainText mb-4">Bundle Contents</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Courses Selection --}}
                    <div>
                        <label class="block text-xs font-bold text-mutedText uppercase tracking-wider mb-3">Select
                            Courses</label>
                        <div class="max-h-64 overflow-y-auto pr-2 space-y-2 custom-scrollbar">
                            @foreach ($courses as $course)
                                <label
                                    class="flex items-center p-3 rounded-xl border border-gray-100 hover:border-primary/30 hover:bg-primary/5 cursor-pointer transition-all group">
                                    <input type="checkbox" name="courses[]" value="{{ $course->id }}"
                                        {{ in_array($course->id, old('courses', $selectedCourses)) ? 'checked' : '' }}
                                        class="w-5 h-5 rounded-lg border-gray-300 text-primary focus:ring-primary/20">
                                    <span
                                        class="ml-3 text-sm font-medium text-mainText group-hover:text-primary">{{ $course->title }}</span>
                                </label>
                            @endforeach
                        </div>
                    </div>

                    {{-- Bundles Selection (Recursive) --}}
                    <div>
                        <label class="block text-xs font-bold text-mutedText uppercase tracking-wider mb-3">Include
                            Other Bundles</label>
                        <div class="max-h-64 overflow-y-auto pr-2 space-y-2 custom-scrollbar">
                            @forelse($allBundles as $bItem)
                                <label
                                    class="flex items-center p-3 rounded-xl border border-gray-100 hover:border-purple-500/30 hover:bg-purple-50 cursor-pointer transition-all group">
                                    <input type="checkbox" name="bundles[]" value="{{ $bItem->id }}"
                                        {{ in_array($bItem->id, old('bundles', $selectedBundles)) ? 'checked' : '' }}
                                        class="w-5 h-5 rounded-lg border-gray-300 text-purple-600 focus:ring-purple-500/20">
                                    <span
                                        class="ml-3 text-sm font-medium text-mainText group-hover:text-purple-700">{{ $bItem->title }}</span>
                                </label>
                            @empty
                                <p class="text-xs text-mutedText italic p-2">No other bundles available.</p>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>

            {{-- Pricing & Commission (Moved to Main Column) --}}
            <div class="bg-surface rounded-3xl p-6 border border-primary/5 shadow-lg shadow-primary/5 space-y-6">
                <h3 class="text-lg font-bold text-mainText flex items-center gap-2">
                    <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z">
                        </path>
                    </svg>
                    Pricing & Commission
                </h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Website Price --}}
                    <div>
                        <label class="block text-sm font-bold text-mainText mb-2">Website Price (₹)</label>
                        <input type="number" step="0.01" name="website_price" id="website_price" value="{{ old('website_price', $bundle->website_price ?? '0.00') }}"
                               class="w-full h-12 px-4 bg-white border border-gray-300 rounded-xl focus:border-primary focus:ring-0 transition-all font-bold text-mainText text-lg placeholder-gray-400" placeholder="0.00">
                        @error('website_price')
                            <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Affiliate Price --}}
                    <div>
                        <label class="block text-sm font-bold text-mainText mb-2">Affiliate Price (₹) <span class="text-red-500">*</span></label>
                        <input type="number" step="0.01" name="affiliate_price" value="{{ old('affiliate_price', $bundle->affiliate_price ?? '') }}"
                               class="w-full h-12 px-4 bg-white border border-gray-300 rounded-xl focus:border-primary focus:ring-0 transition-all font-bold text-mainText text-lg placeholder-gray-400" placeholder="0.00" required>
                        @error('affiliate_price')
                            <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="h-px bg-gray-100 dark:bg-gray-700/50"></div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Discount --}}
                    <div>
                        <label class="block text-sm font-bold text-mainText mb-2">Discount</label>
                        <div class="flex gap-2">
                            <select name="discount_type"
                                class="w-1/3 h-12 px-3 bg-white border border-gray-300 rounded-xl focus:border-primary focus:ring-0 text-xs font-bold uppercase tracking-wider">
                                <option value="">None</option>
                                <option value="flat"
                                    {{ old('discount_type', $bundle->discount_type ?? '') == 'flat' ? 'selected' : '' }}>
                                    Flat (₹)</option>
                                <option value="percentage"
                                    {{ old('discount_type', $bundle->discount_type ?? '') == 'percentage' ? 'selected' : '' }}>
                                    % (Percent)</option>
                            </select>
                             <input type="number" step="0.01" name="discount_value" value="{{ old('discount_value', $bundle->discount_value ?? '') }}"
                                    class="w-2/3 h-12 px-4 bg-white border border-gray-300 rounded-xl focus:border-primary focus:ring-0 font-bold text-mainText" placeholder="Value">
                        </div>
                        @error('discount_type')
                            <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                        @error('discount_value')
                            <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- Commission --}}
                    <div>
                        <label class="block text-sm font-bold text-mainText mb-2">Commission</label>
                        <div class="flex gap-2">
                            <select name="commission_type"
                                class="w-1/3 h-12 px-3 bg-white border border-gray-300 rounded-xl focus:border-primary focus:ring-0 text-xs font-bold uppercase tracking-wider">
                                <option value="">None</option>
                                <option value="flat"
                                    {{ old('commission_type', $bundle->commission_type ?? '') == 'flat' ? 'selected' : '' }}>
                                    Flat (₹)</option>
                                <option value="percentage"
                                    {{ old('commission_type', $bundle->commission_type ?? '') == 'percentage' ? 'selected' : '' }}>
                                    % (Percent)</option>
                            </select>
                             <input type="number" step="0.01" name="commission_value" value="{{ old('commission_value', $bundle->commission_value ?? '') }}"
                                    class="w-2/3 h-12 px-4 bg-white border border-gray-300 rounded-xl focus:border-primary focus:ring-0 font-bold text-mainText" placeholder="Value">
                        </div>
                        @error('commission_type')
                            <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                        @error('commission_value')
                            <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                        @enderror
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Settings & Media --}}
        <div class="lg:col-span-4 space-y-6">
            {{-- Preference Index & Publish Status --}}
            <div class="bg-surface rounded-3xl p-6 border border-primary/5 shadow-lg shadow-primary/5 space-y-4">
                {{-- Preference Index --}}
                <div>
                    <label class="block text-sm font-bold text-mainText">Preference Index</label>
                    <p class="text-[10px] text-mutedText mt-1 mb-3">Order/Rank for capped logic or sorting (Higher = Priority)</p>
                    <input type="number" name="preference_index" value="{{ old('preference_index', $bundle->preference_index ?? 0) }}"
                           class="w-full h-11 px-4 bg-white border border-gray-300 rounded-xl focus:border-primary focus:ring-0 transition-all font-bold text-mainText placeholder-gray-400" placeholder="0">
                    @error('preference_index')
                        <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                    @enderror
                </div>

                <div class="h-px bg-gray-100"></div>

                <div class="flex items-center justify-between">
                    <div>
                        <label class="block text-sm font-bold text-mainText">Publish Bundle</label>
                        <p class="text-xs text-mutedText mt-1">Make visible to students</p>
                    </div>
                    <label class="relative inline-flex items-center cursor-pointer">
                        <input type="hidden" name="is_published" value="0">
                        <input type="checkbox" name="is_published" value="1" class="sr-only peer"
                            {{ old('is_published', $bundle->is_published ?? false) ? 'checked' : '' }}>
                        <div
                            class="w-11 h-6 bg-gray-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary">
                        </div>
                    </label>
                </div>
            </div>

            {{-- Pricing & Commission moved to left column --}}

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

                    <label
                        class="flex flex-col items-center justify-center w-full h-48 border-2 border-dashed border-gray-300 rounded-2xl cursor-pointer hover:bg-gray-50 hover:border-primary/50 transition-all group overflow-hidden relative">

                        {{-- Placeholder (Icon and Text) - Only visible if no image exists --}}
                        <div x-show="!imagePreview" class="flex flex-col items-center justify-center pt-5 pb-6">
                            <svg class="w-10 h-10 mb-3 text-gray-400 group-hover:text-primary transition-colors"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z">
                                </path>
                            </svg>
                            <p class="text-xs text-mutedText font-medium">Click to upload thumbnail</p>
                            <p class="text-[10px] text-mutedText/60 mt-1">PNG, JPG, WebP up to 5MB</p>
                        </div>

                        {{-- Image Preview - Takes over the whole box --}}
                        <template x-if="imagePreview">
                            <div class="absolute inset-0 w-full h-full">
                                <img :src="imagePreview" class="w-full h-full object-cover">
                                {{-- Overlay on hover to show "Change" --}}
                                <div
                                    class="absolute inset-0 bg-black/40 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                                    <div
                                        class="bg-white/20 backdrop-blur-md px-4 py-2 rounded-lg border border-white/30 text-white text-xs font-bold uppercase tracking-wider">
                                        Change Image
                                    </div>
                                </div>
                            </div>
                        </template>

                        {{-- Hidden File Input --}}
                        <input type="file" name="thumbnail" class="hidden" accept="image/*"
                            @change="handleFile($event)" />
                    </label>
                </div>
                <div class="mt-2 flex items-center justify-between px-1">
                    <p class="text-[10px] text-mutedText/60 font-medium">Supported: PNG, JPG, WebP</p>
                    <p class="text-[10px] text-mutedText/60 font-medium">Max Size: 5MB</p>
                </div>

                @error('thumbnail')
                    <p class="text-red-500 text-xs mt-1 font-bold">{{ $message }}</p>
                @enderror
            </div>
        </div>
    </div>

    {{-- Actions --}}
    <div class="flex items-center justify-end gap-4 pt-6 border-t border-primary/5">
        <a href="{{ route('admin.bundles.index') }}"
            class="px-6 py-3 rounded-xl border border-transparent font-bold text-mutedText hover:bg-primary/5 transition-all">Cancel</a>
        <button type="submit"
            class="px-8 py-3 rounded-xl brand-gradient text-customWhite font-bold shadow-lg shadow-primary/30 hover:shadow-primary/50 hover:-translate-y-1 transition-all">
            {{ $bundle ? 'Update Bundle' : 'Create Bundle' }}
        </button>
    </div>
</form>

@push('styles')
    <link href="https://cdn.quilljs.com/1.3.6/quill.snow.css" rel="stylesheet">
    <style>
        /* Quill Customization */
        .ql-toolbar.ql-snow {
            border-top-left-radius: 0.75rem;
            border-top-right-radius: 0.75rem;
            border-color: #e5e7eb;
            background-color: #f9fafb;
            padding: 12px;
        }

        .ql-container.ql-snow {
            border-bottom-left-radius: 0.75rem;
            border-bottom-right-radius: 0.75rem;
            border-color: #e5e7eb;
            min-height: 250px;
            /* Increased height */
            font-size: 1rem;
            font-family: inherit;
        }

        .ql-editor {
            min-height: 250px;
        }

        .ql-toolbar button:hover .ql-stroke,
        .ql-toolbar button.ql-active .ql-stroke {
            stroke: #4f46e5 !important;
            /* Primary color */
        }

        .ql-toolbar button:hover .ql-fill,
        .ql-toolbar button.ql-active .ql-fill {
            fill: #4f46e5 !important;
        }
    </style>
@endpush

@push('scripts')
    <script src="https://cdn.quilljs.com/1.3.6/quill.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var quill = new Quill('#quill-editor', {
                theme: 'snow',
                placeholder: "What's included in this bundle?",
                modules: {
                    toolbar: [
                        ['bold', 'italic', 'underline'],
                        [{
                            'list': 'ordered'
                        }, {
                            'list': 'bullet'
                        }],
                        [{
                            'header': [1, 2, 3, false]
                        }],
                        ['link', 'image'],
                        [{
                            'align': []
                        }],
                        ['clean']
                    ]
                }
            });

            // Sync content
            quill.on('text-change', function() {
                document.getElementById('description-input').value = quill.root.innerHTML;
            });

            // Client-side validation for pricing
            const form = document.querySelector('form');
            const websitePriceInput = document.getElementById('website_price');

            // Helper to validate value against type and base price
            function validateField(typeInputName, valueInputName, errorMsgBase) {
                const typeInput = document.querySelector(`select[name="${typeInputName}"]`);
                const valueInput = document.querySelector(`input[name="${valueInputName}"]`);

                if (!typeInput || !valueInput) return;

                const validate = () => {
                    const type = typeInput.value;
                    const value = parseFloat(valueInput.value);
                    const price = parseFloat(websitePriceInput.value);

                    if (isNaN(value)) {
                        valueInput.setCustomValidity('');
                        return;
                    }

                    if (type === 'percentage' && value > 100) {
                        valueInput.setCustomValidity('Percentage cannot exceed 100%.');
                    } else if (type === 'flat' && !isNaN(price) && value > price) {
                        valueInput.setCustomValidity('Value cannot exceed Website Price.');
                    } else {
                        valueInput.setCustomValidity('');
                    }
                };

                typeInput.addEventListener('change', validate);
                valueInput.addEventListener('input', validate);
                websitePriceInput.addEventListener('input', validate);
            }

            validateField('discount_type', 'discount_value', 'Discount');
            validateField('commission_type', 'commission_value', 'Commission');
        });
    </script>
@endpush
