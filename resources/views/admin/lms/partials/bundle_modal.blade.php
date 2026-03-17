<div x-data="bundleCreator()" @open-bundle-modal.window="openModal($event.detail)" x-show="open" x-cloak
    class="fixed inset-0 z-[9999] flex items-center justify-center p-4 bg-slate-900/60 backdrop-blur-sm"
    style="display: none;">

    <div class="bg-white rounded-[2.5rem] shadow-2xl w-full max-w-2xl border border-slate-200 overflow-hidden"
        @click.away="open = false">
        <div class="p-8">
            <div class="flex justify-between items-center mb-6">
                <h3 class="text-xl font-black text-slate-800 tracking-tight uppercase"
                    x-text="editMode ? 'Edit Package' : 'New Smart Package'"></h3>
                <button type="button" @click="open = false" class="text-slate-400 hover:text-slate-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M18 6L6 18M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <form action="{{ route('admin.bundles.store') }}" method="POST">
                @csrf
                <template x-if="editMode">
                    <input type="hidden" name="id" :value="bundleId">
                </template>

                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Package Title</label>
                        <input type="text" name="title" x-model="title" required
                            class="w-full rounded-2xl border-slate-200 px-5 py-4 focus:ring-4 focus:ring-[#0777be]/10">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2 uppercase text-[10px]">
                            Available Courses (Bundle Total: ₹<span x-text="totalValue">0</span>)
                        </label>
                        <div
                            class="grid grid-cols-1 md:grid-cols-2 gap-3 max-h-52 overflow-y-auto p-4 bg-slate-50 rounded-2xl border border-slate-100 no-scrollbar">
                            @php
                                // Fetch all published courses with their bundles relationship
                                $allCourses = \App\Models\Course::where('is_published', true)->with('bundles')->get();
                            @endphp

                            @foreach ($allCourses as $course)
                                @php
                                    // Check if this course is already part of any bundle
                                    $isAlreadyBundled = $course->bundles->isNotEmpty();
                                @endphp

                                {{-- Logic: Show if the course is free (not in any bundle) --}}
                                {{-- OR if we are in EDIT mode and this course is part of the current bundle --}}
                                <template
                                    x-if="!{{ $isAlreadyBundled ? 'true' : 'false' }} || isCourseInCurrentBundle({{ $course->id }})">
                                    <label
                                        class="relative flex items-center p-3 bg-white border border-slate-200 rounded-xl cursor-pointer hover:border-[#0777be] transition-all">
                                        <input type="checkbox" name="course_ids[]" value="{{ $course->id }}"
                                            :checked="selectedCourseIds.includes({{ $course->id }})"
                                            @change="toggleCourse($event, {{ $course->id }}, {{ $course->price }})"
                                            class="w-5 h-5 rounded border-slate-300 text-[#0777be]">
                                        <div class="ml-3">
                                            <p class="text-[11px] font-black text-slate-800 line-clamp-1">
                                                {{ $course->title }}</p>
                                            <p class="text-[9px] text-slate-500 font-bold uppercase">
                                                ₹{{ number_format($course->price) }}</p>
                                        </div>
                                    </label>
                                </template>
                            @endforeach
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-2">Final Package Price (INR)</label>
                        <input type="number" name="price" x-model="bundlePrice" required
                            class="w-full rounded-2xl border-slate-200 px-5 py-4 font-black text-[#0777be] focus:ring-4 focus:ring-[#0777be]/10">
                    </div>

                    <button type="submit"
                        class="w-full bg-[#0777be] text-white py-5 rounded-3xl font-black shadow-lg uppercase tracking-widest active:scale-95 transition-all">
                        <span x-text="editMode ? 'Update Package' : 'Create Package'"></span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function bundleCreator() {
        return {
            open: false,
            editMode: false,
            bundleId: null,
            title: '',
            totalValue: 0,
            bundlePrice: 0,
            selectedCourseIds: [],

            openModal(data = null) {
                this.open = true;
                if (data && data.id) {
                    this.editMode = true;
                    this.bundleId = data.id;
                    this.title = data.title;
                    this.bundlePrice = data.price;
                    // Convert string IDs to numbers for proper comparison
                    this.selectedCourseIds = Array.isArray(data.courseIds) ? data.courseIds.map(Number) : [];
                    // Recalculate total value in edit mode
                    this.totalValue = Array.isArray(data.coursePrices) ? data.coursePrices.map(Number).reduce((a, b) =>
                        a + b, 0) : 0;
                } else {
                    this.editMode = false;
                    this.bundleId = null;
                    this.title = '';
                    this.totalValue = 0;
                    this.bundlePrice = 0;
                    this.selectedCourseIds = [];
                }
            },

            isCourseInCurrentBundle(id) {
                return this.selectedCourseIds.includes(Number(id));
            },

            toggleCourse(e, id, price) {
                const p = parseFloat(price);
                const courseId = Number(id);

                if (e.target.checked) {
                    if (!this.selectedCourseIds.includes(courseId)) {
                        this.selectedCourseIds.push(courseId);
                        this.totalValue += p;
                    }
                } else {
                    this.selectedCourseIds = this.selectedCourseIds.filter(item => item !== courseId);
                    this.totalValue -= p;
                }

                this.totalValue = parseFloat(this.totalValue.toFixed(2));
                // Auto-update package price to match the total
                this.bundlePrice = this.totalValue;
            }
        }
    }
</script>
