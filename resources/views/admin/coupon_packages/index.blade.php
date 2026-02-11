@extends('layouts.admin')
@section('title', 'Package Manager')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div x-data="packageManager()" x-init="init()"
        class="container-fluid font-sans p-4 md:p-6 bg-navy min-h-screen text-mainText">

        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4">
            <div>
                <h2 class="text-2xl font-black text-mainText tracking-tight uppercase">Package Manager</h2>
                <p class="text-xs text-mutedText mt-1 font-medium uppercase tracking-wider">Premium Bundle Control Panel</p>
            </div>
            <button @click="openModal('create')"
                class="bg-primary px-10 py-4 text-xs font-black text-customWhite rounded-2xl shadow-xl shadow-primary/20 hover:bg-secondary transition-all active:scale-95">
                CREATE PACKAGE
            </button>
        </div>

        <div class="mb-8 w-full md:max-w-sm">
            <input type="text" x-model="search" @input.debounce.500ms="fetchPackages()" placeholder="Search packages..."
                class="w-full px-6 py-3 bg-surface border border-primary/10 text-mainText rounded-2xl outline-none shadow-sm focus:ring-1 focus:ring-primary">
        </div>

        <div id="packages-container">@include('admin.coupon_packages.partials.table_rows')</div>

        {{-- MODAL --}}
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-[60]" x-cloak>
            <div class="fixed inset-0 bg-mainText/40 backdrop-blur-sm" @click="showModal = false"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div
                    class="relative w-full max-w-2xl rounded-[2.5rem] bg-surface border border-primary/10 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">
                    <div class="px-8 py-6 border-b border-primary/5 bg-primary/5 flex items-center justify-between">
                        <h3 class="text-sm font-black text-mainText uppercase"
                            x-text="isEdit ? 'Update Coupon Package' : 'New Package Entry'"></h3>
                        <button @click="showModal = false" class="text-mutedText hover:text-secondary"><svg class="w-6 h-6"
                                fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path d="M6 18L18 6M6 6l12 12"></path>
                            </svg></button>
                    </div>

                    <div class="overflow-y-auto p-8">
                        <form @submit.prevent="submitForm" class="space-y-6">
                            <div>
                                <label
                                    class="block text-[10px] font-black text-mutedText uppercase mb-2 tracking-widest">Package
                                    Code</label>
                                <div class="relative flex items-center">
                                    <input type="text" x-model="form.name" required
                                        class="w-full rounded-2xl bg-[#FFF9F2] border border-[#FFE8CC] px-6 py-4 text-sm font-bold text-primary uppercase tracking-widest outline-none transition-all pr-24">
                                    <button type="button" @click="generateCode()"
                                        class="absolute right-3 px-3 py-1.5 bg-white border border-[#FFE8CC] rounded-xl text-[9px] font-black text-primary hover:bg-primary hover:text-white transition-all">GENERATE</button>
                                </div>
                            </div>

                            <div class="bg-[#FFF9F2] p-6 rounded-2xl border border-[#FFE8CC]">
                                <label
                                    class="block text-[10px] font-black text-mutedText uppercase mb-4 tracking-widest">Application
                                    Scope</label>
                                <div class="flex gap-8">
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="radio" value="general" x-model="form.package_type"
                                            class="w-4 h-4 text-primary focus:ring-primary">
                                        <span
                                            class="text-xs font-bold text-mainText transition-colors group-hover:text-primary">Store-wide
                                            (General)</span>
                                    </label>
                                    <label class="flex items-center gap-3 cursor-pointer group">
                                        <input type="radio" value="specific" x-model="form.package_type"
                                            class="w-4 h-4 text-primary focus:ring-primary">
                                        <span
                                            class="text-xs font-bold text-mainText transition-colors group-hover:text-primary">Specific
                                            (Selection)</span>
                                    </label>
                                </div>
                            </div>

                            <div x-show="form.package_type === 'specific'" x-collapse
                                class="space-y-4 border-l-4 border-primary/20 pl-4 py-2">
                                <div x-data="multiSelect({ options: {{ $courses->map(fn($c) => ['value' => $c->id, 'label' => $c->title])->toJson() }}, selected: form.courses, onChange: (v) => form.courses = v })" class="relative">
                                    <label
                                        class="block text-[10px] font-black text-mutedText uppercase mb-2 tracking-widest">Select
                                        Courses</label>
                                    <div @click="open = !open"
                                        class="w-full min-h-[55px] rounded-2xl bg-[#FFF9F2] border border-[#FFE8CC] px-4 py-3 flex flex-wrap gap-2 cursor-pointer items-center">
                                        <template x-if="selected.length === 0"><span
                                                class="text-sm text-mutedText/50 italic">Click to
                                                select...</span></template>
                                        <template x-for="val in selected" :key="val">
                                            <span
                                                class="bg-primary/10 text-primary text-[10px] font-black px-3 py-1.5 rounded-lg flex items-center gap-2 uppercase">
                                                <span x-text="getLabel(val)"></span>
                                                <button type="button" @click.stop="remove(val)">&times;</button>
                                            </span>
                                        </template>
                                    </div>
                                    <div x-show="open" @click.away="open = false"
                                        class="absolute z-50 w-full mt-2 bg-white border border-[#FFE8CC] rounded-2xl shadow-xl max-h-48 overflow-y-auto p-2">
                                        <template x-for="opt in options" :key="opt.value">
                                            <div @click="toggle(opt.value)"
                                                class="px-4 py-3 rounded-xl hover:bg-primary/5 cursor-pointer flex justify-between items-center transition-all">
                                                <span class="text-xs font-bold text-mainText" x-text="opt.label"></span>
                                                <span x-show="selected.includes(Number(opt.value))"
                                                    class="text-primary font-bold text-lg">✓</span>
                                            </div>
                                        </template>
                                    </div>
                                </div>

                                <div x-data="multiSelect({ options: {{ $bundles->map(fn($b) => ['value' => $b->id, 'label' => $b->title])->toJson() }}, selected: form.bundles, onChange: (v) => form.bundles = v })" class="relative">
                                    <label
                                        class="block text-[10px] font-black text-mutedText uppercase mb-2 tracking-widest">Select
                                        Bundles</label>
                                    <div @click="open = !open"
                                        class="w-full min-h-[55px] rounded-2xl bg-[#FFF9F2] border border-[#FFE8CC] px-4 py-3 flex flex-wrap gap-2 cursor-pointer items-center">
                                        <template x-if="selected.length === 0"><span
                                                class="text-sm text-mutedText/50 italic">Click to
                                                select...</span></template>
                                        <template x-for="val in selected" :key="val">
                                            <span
                                                class="bg-secondary/10 text-secondary text-[10px] font-black px-3 py-1.5 rounded-lg flex items-center gap-2 uppercase">
                                                <span x-text="getLabel(val)"></span>
                                                <button type="button" @click.stop="remove(val)">&times;</button>
                                            </span>
                                        </template>
                                    </div>
                                    <div x-show="open" @click.away="open = false"
                                        class="absolute z-50 w-full mt-2 bg-white border border-[#FFE8CC] rounded-2xl shadow-xl max-h-48 overflow-y-auto p-2">
                                        <template x-for="opt in options" :key="opt.value">
                                            <div @click="toggle(opt.value)"
                                                class="px-4 py-3 rounded-xl hover:bg-secondary/5 cursor-pointer flex justify-between items-center transition-all">
                                                <span class="text-xs font-bold text-mainText" x-text="opt.label"></span>
                                                <span x-show="selected.includes(Number(opt.value))"
                                                    class="text-secondary font-bold text-lg">✓</span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-mutedText uppercase mb-2 tracking-widest">Discount
                                        Type</label>
                                    <select x-model="form.type"
                                        class="w-full rounded-2xl bg-[#FFF9F2] border border-[#FFE8CC] px-6 py-4 text-sm font-bold text-mainText outline-none transition-all appearance-none">
                                        <option value="fixed">Fixed Amount (₹)</option>
                                        <option value="percentage">Percentage (%)</option>
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-mutedText uppercase mb-2 tracking-widest">Original
                                        Price (MRP)</label>
                                    <input type="number" x-model="form.price" required
                                        class="w-full rounded-2xl bg-[#FFF9F2] border border-[#FFE8CC] px-6 py-4 text-sm font-bold text-red-400 outline-none">
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-mutedText uppercase mb-2 tracking-widest">Discount
                                        Value / Price</label>
                                    <input type="number" x-model="form.discount_price" required
                                        class="w-full rounded-2xl bg-[#FFF9F2] border border-[#FFE8CC] px-6 py-4 text-sm font-bold text-green-500 outline-none">
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-black text-mutedText uppercase mb-2 tracking-widest">Description</label>
                                    <textarea x-model="form.description" rows="1"
                                        class="w-full rounded-2xl bg-[#FFF9F2] border border-[#FFE8CC] px-6 py-4 text-sm font-medium text-mainText outline-none"></textarea>
                                </div>
                            </div>

                            <div class="flex items-center justify-between pt-4">
                                <label class="flex items-center cursor-pointer relative">
                                    <input type="checkbox" x-model="form.is_active" class="sr-only peer">
                                    <div
                                        class="w-12 h-6 bg-mutedText/20 rounded-full peer peer-checked:bg-primary after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-6">
                                    </div>
                                    <span class="ml-4 text-[11px] font-black text-mainText uppercase tracking-widest">Live
                                        Status</span>
                                </label>
                                <div class="flex gap-3">
                                    <button type="button" @click="showModal = false"
                                        class="px-6 py-3 text-[10px] font-black text-mutedText uppercase tracking-widest hover:text-primary transition-all">Cancel</button>
                                    <button type="submit" :disabled="isSubmitting"
                                        class="px-10 py-4 bg-primary text-white text-[11px] font-black uppercase tracking-widest rounded-2xl shadow-xl shadow-primary/20 hover:bg-secondary transition-all disabled:opacity-50">
                                        <span
                                            x-text="isSubmitting ? 'Processing...' : (isEdit ? 'Update Package' : 'Create Package')"></span>
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('multiSelect', (config) => ({
                options: config.options,
                selected: config.selected || [],
                open: false,
                getLabel(v) {
                    let o = this.options.find(opt => Number(opt.value) === Number(v));
                    return o ? o.label : v;
                },
                toggle(v) {
                    let val = Number(v);
                    if (this.selected.includes(val)) {
                        this.selected = this.selected.filter(i => i !== val);
                    } else {
                        this.selected.push(val);
                    }
                    config.onChange(this.selected);
                },
                remove(v) {
                    let val = Number(v);
                    this.selected = this.selected.filter(i => i !== val);
                    config.onChange(this.selected);
                },
                init() {
                    this.$watch('config.selected', val => this.selected = val);
                }
            }));
        });

        function packageManager() {
            return {
                showModal: false,
                isEdit: false,
                isSubmitting: false,
                isLoading: false,
                search: '',
                form: {
                    id: null,
                    name: '',
                    description: '',
                    type: 'fixed',
                    price: '',
                    discount_price: '',
                    package_type: 'general',
                    courses: [],
                    bundles: [],
                    is_active: true
                },
                init() {
                    this.Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000
                    });
                    this.fetchPackages();
                },
                generateCode() {
                    const c = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                    let r = '';
                    for (let i = 0; i < 8; i++) r += c.charAt(Math.floor(Math.random() * c.length));
                    this.form.name = r;
                },
                async fetchPackages(url = "{{ route('admin.coupon-packages.index') }}") {
                    this.isLoading = true;
                    try {
                        const urlObj = new URL(url);
                        if (this.search) urlObj.searchParams.set('search', this.search);
                        const res = await fetch(urlObj, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });
                        const data = await res.json();
                        document.getElementById('packages-container').innerHTML = data.html;
                        this.$nextTick(() => {
                            document.querySelectorAll('.pagination a').forEach(l => l.addEventListener('click',
                                e => {
                                    e.preventDefault();
                                    this.fetchPackages(l.href);
                                }));
                        });
                    } catch (e) {
                        console.error(e);
                    }
                    this.isLoading = false;
                },
                openModal(mode, id = null) {
                    this.isEdit = (mode === 'edit');
                    if (this.isEdit) {
                        fetch(`/admin/coupon-packages/${id}/edit`)
                            .then(res => res.json())
                            .then(json => {
                                const d = json.data;
                                // Parse arrays and ensure they are numbers
                                let sCourses = Array.isArray(d.selected_courses) ? d.selected_courses.map(Number) : [];
                                let sBundles = Array.isArray(d.selected_bundles) ? d.selected_bundles.map(Number) : [];

                                this.form = {
                                    id: d.id,
                                    name: d.name,
                                    description: d.description || '',
                                    type: d.type || 'fixed',
                                    price: d.price,
                                    discount_price: d.discount_price,
                                    package_type: (sCourses.length > 0 || sBundles.length > 0) ? 'specific' :
                                        'general',
                                    courses: sCourses,
                                    bundles: sBundles,
                                    is_active: !!d.is_active
                                };
                                this.showModal = true;
                            });
                    } else {
                        this.form = {
                            id: null,
                            name: '',
                            description: '',
                            type: 'fixed',
                            price: '',
                            discount_price: '',
                            package_type: 'general',
                            courses: [],
                            bundles: [],
                            is_active: true
                        };
                        this.generateCode();
                        this.showModal = true;
                    }
                }, // FIXED: Removed the extra comma here
                async submitForm() {
                    this.isSubmitting = true;
                    try {
                        const res = await fetch("{{ route('admin.coupon-packages.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            },
                            body: JSON.stringify(this.form)
                        });
                        if (res.ok) {
                            this.showModal = false;
                            this.Toast.fire({
                                icon: 'success',
                                title: 'Saved'
                            });
                            this.fetchPackages();
                        }
                    } catch (e) {
                        console.error(e);
                    }
                    this.isSubmitting = false;
                },
                async deletePackage(id) {
                    if (confirm('Delete?')) {
                        await fetch(`/admin/coupon-packages/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            }
                        });
                        this.fetchPackages();
                    }
                }
            }
        }
    </script>
@endsection
