<div class="bg-surface rounded-[2rem] border border-primary shadow-xl shadow-primary/5 p-8 animate-fade-in-up">
    <form action="{{ route('admin.courses.update', $course->id) }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-10 items-start">

            {{-- LEFT COLUMN: Pricing & Economics --}}
            <div class="space-y-6">
                {{-- Pricing Section --}}
                <div class="bg-primary/5 p-6 rounded-2xl border border-primary/20 shadow-sm">
                    <h4 class="text-xs font-black uppercase tracking-widest text-primary flex items-center gap-2 mb-6">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                        Financial Configuration
                    </h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        {{-- Website Price --}}
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black uppercase text-mutedText ml-1">Website Price (₹) <span class="text-red-500">*</span></label>
                            <input type="number" name="website_price" value="{{ old('website_price', $course->website_price ?? '') }}" required min="0"
                                class="w-full h-12 rounded-xl bg-white px-4 text-sm font-bold border border-gray-300 focus:border-primary focus:ring-0 outline-none transition-all text-mainText" placeholder="Selling Price">
                        </div>

                        {{-- Affiliate Price --}}
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black uppercase text-mutedText ml-1">Affiliate Price (₹) <span class="text-red-500">*</span></label>
                            <input type="number" name="affiliate_price" value="{{ old('affiliate_price', $course->affiliate_price ?? '') }}" required min="0"
                                class="w-full h-12 rounded-xl bg-white px-4 text-sm font-bold border border-gray-300 focus:border-primary focus:ring-0 outline-none transition-all text-mainText" placeholder="Affiliate Price">
                        </div>
                    </div>
                </div>

                {{-- Discount & Commission Row --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    {{-- Discount Section --}}
                    <div class="bg-primary/5 p-6 rounded-2xl border border-primary/20 flex flex-col justify-between">
                        <h4 class="text-xs font-black uppercase tracking-widest text-primary flex items-center gap-2 mb-4">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z" />
                            </svg>
                            Discount
                        </h4>
                        <div class="space-y-3">
                            <input type="number" name="discount_value" value="{{ old('discount_value', $course->discount_value ?? 0) }}" min="0"
                                class="w-full h-11 rounded-xl bg-white px-4 text-sm font-bold border border-gray-300 focus:border-primary focus:ring-0 outline-none" placeholder="Value">

                            <div class="relative">
                                <select name="discount_type" class="w-full h-11 rounded-xl bg-white px-4 text-sm font-bold border border-gray-300 focus:border-primary focus:ring-0 outline-none appearance-none cursor-pointer text-mainText">
                                    <option value="fixed" {{ (old('discount_type', $course->discount_type ?? '') == 'fixed') ? 'selected' : '' }}>Fixed (₹)</option>
                                    <option value="percent" {{ (old('discount_type', $course->discount_type ?? '') == 'percent') ? 'selected' : '' }}>Percent (%)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-primary">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Commission Section --}}
                    <div class="bg-primary/5 p-6 rounded-2xl border border-primary/20 flex flex-col justify-between">
                        <h4 class="text-xs font-black uppercase tracking-widest text-primary flex items-center gap-2 mb-4">
                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z" />
                            </svg>
                            Commission
                        </h4>
                        <div class="space-y-3">
                            <input type="number" name="commission_value" value="{{ old('commission_value', $course->commission_value ?? 0) }}" min="0"
                                class="w-full h-11 rounded-xl bg-white px-4 text-sm font-bold border border-gray-300 focus:border-primary focus:ring-0 outline-none" placeholder="Value">

                            <div class="relative">
                                <select name="commission_type" class="w-full h-11 rounded-xl bg-white px-4 text-sm font-bold border border-gray-300 focus:border-primary focus:ring-0 outline-none appearance-none cursor-pointer text-mainText">
                                    <option value="fixed" {{ (old('commission_type', $course->commission_type ?? '') == 'fixed') ? 'selected' : '' }}>Fixed (₹)</option>
                                    <option value="percent" {{ (old('commission_type', $course->commission_type ?? '') == 'percent') ? 'selected' : '' }}>Percent (%)</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-primary">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- RIGHT COLUMN: Certificate & Publish --}}
            <div class="space-y-6" x-data="{ certEnabled: {{ old('certificate_enabled', $course->certificate_enabled ?? 0) ? 'true' : 'false' }}, certType: '{{ old('certificate_type', $course->certificate_type ?? 'completion') }}' }">

                {{-- Certificate Card --}}
                <div class="bg-surface border border-primary/20 p-6 rounded-2xl shadow-sm">
                    <label class="flex items-start gap-4 cursor-pointer group">
                        <div class="mt-1">
                            <input type="checkbox" name="certificate_enabled" value="1" x-model="certEnabled"
                                class="w-6 h-6 rounded-md text-primary focus:ring-primary border-2 border-primary cursor-pointer transition-all">
                        </div>
                        <div>
                            <span class="block text-sm font-bold text-mainText group-hover:text-primary transition-colors">Course Certification</span>
                            <p class="text-[11px] text-mutedText mt-1">Automatically issue a certificate to students who meet the criteria.</p>
                        </div>
                    </label>

                    {{-- Expanded Certificate Options --}}
                    <div x-show="certEnabled" x-transition.opacity.duration.300ms class="mt-6 pl-8 border-l-2 border-primary/10 space-y-5">
                        <div class="space-y-2">
                            <label class="block text-[10px] font-black uppercase text-mutedText">Issuance Criteria</label>
                            <div class="relative">
                                <select name="certificate_type" x-model="certType"
                                    class="w-full h-11 rounded-xl bg-white px-4 text-xs font-bold border border-gray-300 focus:border-primary focus:ring-0 outline-none appearance-none cursor-pointer text-mainText">
                                    <option value="completion">Based on Course Progress (%)</option>
                                    <option value="quiz">Based on Final Quiz Result</option>
                                </select>
                                <div class="absolute inset-y-0 right-0 flex items-center px-3 pointer-events-none text-primary">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Completion Input --}}
                        <div x-show="certType === 'completion'" class="space-y-2">
                            <label class="block text-[10px] font-black uppercase text-mutedText">Completion Threshold (%)</label>
                            <input type="number" name="completion_percentage" value="{{ old('completion_percentage', $course->completion_percentage ?? 80) }}" min="1" max="100"
                                class="w-full h-11 rounded-xl bg-white px-4 text-sm font-bold border border-gray-300 focus:border-primary focus:ring-0 outline-none">
                        </div>

                        {{-- Quiz Notice --}}
                        <div x-show="certType === 'quiz'" class="p-4 bg-blue-50/60 rounded-xl border border-blue-100 flex gap-3 animate-pulse">
                            <svg class="w-5 h-5 text-blue-600 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            <p class="text-[11px] text-blue-800 leading-relaxed">
                                <strong>Quiz Setup:</strong> Please ensure you have added a quiz in the curriculum tab to use this feature.
                            </p>
                            <input type="hidden" name="quiz_required" :value="certType === 'quiz' ? 1 : 0">
                        </div>
                    </div>
                </div>

                {{-- Visibility Status --}}
                <div class="p-6 rounded-2xl border-2 border-primary/10 bg-surface shadow-sm group hover:border-primary/30 transition-all">
                    <label class="flex items-center gap-4 cursor-pointer">
                        <input type="checkbox" name="is_published" value="1" {{ old('is_published', $course->is_published ?? 0) ? 'checked' : '' }}
                            class="w-6 h-6 rounded-md text-primary focus:ring-primary border-2 border-primary cursor-pointer">
                        <div class="flex-1">
                            <span class="block text-sm font-bold text-mainText group-hover:text-primary transition-colors">Public Visibility</span>
                            <span class="text-[11px] text-mutedText">Toggle whether students can see and enroll in this course.</span>
                        </div>
                    </label>
                </div>
            </div>
        </div>

        {{-- Form Footer --}}
        <input type="hidden" name="redirect_tab" value="settings">
        <div class="pt-8 border-t border-primary/10 flex flex-col md:flex-row items-center justify-between gap-4">
            <p class="text-[11px] text-mutedText font-medium italic">Make sure to review all financial terms before publishing.</p>
            <button type="submit"
                class="brand-gradient w-full md:w-auto px-12 py-4 rounded-2xl text-customWhite text-xs font-black uppercase tracking-widest shadow-xl hover:shadow-primary/30 hover:-translate-y-1 transition-all transform active:scale-95">
                Save Course Settings
            </button>
        </div>
    </form>
</div>
