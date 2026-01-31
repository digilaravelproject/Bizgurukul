<div class="bg-surface rounded-[2rem] border border-primary shadow-xl shadow-primary/5 p-8 animate-fade-in-up">
    <form action="{{ route('admin.courses.update', $course->id) }}" method="POST" class="space-y-8">
        @csrf @method('PUT')

        <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
            {{-- Discount Section --}}
            <div class="bg-primary/5 p-6 rounded-2xl space-y-4 border border-primary">
                <h4 class="text-xs font-black uppercase tracking-widest text-primary flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/></svg>
                    Pricing Strategy
                </h4>
                <div>
                    <label class="block text-[10px] font-black uppercase text-mutedText mb-2">Discount Value</label>
                    <input type="number" name="discount_value" value="{{ $course->discount_value }}" class="w-full h-12 rounded-xl bg-surface px-4 text-sm font-bold border focus:border-primary focus:ring-2 focus:ring-primary outline-none transition-all shadow-sm text-mainText">
                </div>
                <div>
                    <label class="block text-[10px] font-black uppercase text-mutedText mb-2">Type</label>
                    <div class="relative">
                        <select name="discount_type" class="w-full h-12 rounded-xl bg-surface px-4 text-sm font-bold border focus:border-primary focus:ring-2 focus:ring-primary outline-none transition-all shadow-sm appearance-none cursor-pointer text-mainText">
                            <option value="fixed" {{$course->discount_type=='fixed'?'selected':''}}>Fixed Amount (₹)</option>
                            <option value="percent" {{$course->discount_type=='percent'?'selected':''}}>Percentage (%)</option>
                        </select>
                        <div class="absolute inset-y-0 right-0 flex items-center px-4 pointer-events-none text-primary">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Toggles --}}
            <div class="space-y-4">
                {{-- Certificate --}}
                <label class="flex items-center gap-4 p-5 rounded-2xl border border-primary cursor-pointer hover:bg-primary/5 hover:border-primary/30 transition-all group bg-surface shadow-sm">
                    <input type="checkbox" name="certificate_enabled" value="1" {{$course->certificate_enabled?'checked':''}} class="w-6 h-6 rounded-md text-primary focus:ring-primary border-2 border-primary cursor-pointer">
                    <div>
                        <span class="block text-sm font-bold text-mainText group-hover:text-primary transition-colors">Enable Certificate</span>
                        <span class="text-xs text-mutedText">Auto-generate certificate upon completion</span>
                    </div>
                </label>

                {{-- Publish Status --}}
                <label class="flex items-center gap-4 p-5 rounded-2xl border border-primary cursor-pointer hover:bg-primary/5 hover:border-primary transition-all group bg-surface shadow-sm">
                    <input type="checkbox" name="is_published" value="1" {{$course->is_published?'checked':''}} class="w-6 h-6 rounded-md text-primary focus:ring-primary border-2 border-primary cursor-pointer">
                    <div>
                        <span class="block text-sm font-bold text-mainText group-hover:text-primary transition-colors">Publish Course</span>
                        <span class="text-xs text-mutedText">Make this course visible to students</span>
                    </div>
                </label>
            </div>
        </div>

        <div class="pt-6 border-t border-primary/5 flex justify-end">
            <button class="brand-gradient px-10 py-4 rounded-xl text-customWhite text-xs font-black uppercase tracking-widest shadow-lg hover:shadow-primary hover:-translate-y-1 transition-all transform active:scale-95">
                Save & Finish
            </button>
        </div>
    </form>
</div>
