@extends('layouts.admin')
@section('title', 'Coupon Manager')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Main Container with Alpine Data --}}
    <div x-data="couponManager()" x-init="init()" class="container-fluid font-sans p-4 md:p-6 bg-navy min-h-screen text-mainText">

        {{-- 1. HEADER & ACTIONS --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 animate-fade-in">
            <div>
                <h2 class="text-2xl font-bold text-mainText tracking-tight">Coupon Manager</h2>
                <p class="text-xs text-mutedText mt-1 font-medium uppercase tracking-wider">Manage discounts and promo codes</p>
            </div>

            <button @click="openModal('create')"
                class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-3 text-xs font-bold text-customWhite shadow-lg shadow-primary/20 hover:bg-secondary transition-all duration-300 active:scale-95">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5">
                    <path d="M12 4v16m8-8H4" />
                </svg>
                CREATE COUPON
            </button>
        </div>

        {{-- 2. AJAX SEARCH BAR --}}
        <div class="mb-8 relative w-full md:max-w-sm">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-mutedText">
                <svg x-show="!isLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <svg x-show="isLoading" class="animate-spin w-4 h-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
            <input type="text"
                x-model="search"
                @input.debounce.500ms="fetchCoupons()"
                placeholder="Search by code..."
                class="w-full pl-10 pr-4 py-2.5 bg-surface border border-primary/10 text-mainText placeholder-mutedText/50 rounded-xl focus:ring-1 focus:ring-primary focus:border-primary outline-none transition text-sm shadow-sm">
        </div>

        {{-- 3. CONTENT AREA (Loaded via Partial) --}}
        <div id="coupons-container" class="animate-fade-in">
            @include('admin.coupons.partials.table_rows')
        </div>

        {{-- MODAL --}}
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-[60]" x-cloak>
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-mainText/40 backdrop-blur-sm" x-show="showModal" x-transition.opacity @click="showModal = false"></div>

            {{-- Modal Content --}}
            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-2xl rounded-2xl bg-surface border border-primary/10 shadow-2xl overflow-hidden flex flex-col max-h-[90vh]">

                    {{-- Modal Header --}}
                    <div class="px-6 py-4 border-b border-primary/5 bg-primary/5 flex items-center justify-between shrink-0">
                        <h3 class="text-sm font-bold text-mainText uppercase tracking-wider" x-text="isEdit ? 'Edit Coupon' : 'Create New Coupon'"></h3>
                        <button @click="showModal = false" class="text-mutedText hover:text-secondary transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    {{-- Scrollable Form Area --}}
                    <div class="overflow-y-auto p-6">
                        <form @submit.prevent="submitForm" class="space-y-6">

                            {{-- Row 1: Code & Generate --}}
                            <div>
                                <label class="block text-[10px] font-bold text-mutedText uppercase mb-2 tracking-widest">Coupon Code</label>
                                <div class="relative">
                                    <input type="text" x-model="form.code" required class="w-full rounded-xl bg-navy border border-primary/10 px-4 py-3 text-sm font-bold text-primary uppercase tracking-widest focus:border-primary focus:ring-1 focus:ring-primary outline-none transition placeholder-mutedText/30" placeholder="e.g. SUMMER2024">
                                    <button type="button" @click="generateCode()" class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-mutedText hover:text-primary bg-surface border border-primary/10 px-2 py-1 rounded-md transition uppercase">
                                        Generate
                                    </button>
                                </div>
                            </div>

                            {{-- Row 2: Type Selection (Radio) --}}
                            <div class="bg-navy p-4 rounded-xl border border-primary/5">
                                <label class="block text-[10px] font-bold text-mutedText uppercase mb-3 tracking-widest">Application Scope</label>
                                <div class="flex gap-6">
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="radio" value="general" x-model="form.coupon_type" class="text-primary focus:ring-primary bg-surface border-primary/20">
                                        <span class="text-xs font-bold text-mainText group-hover:text-primary transition">Store-wide (General)</span>
                                    </label>
                                    <label class="flex items-center gap-2 cursor-pointer group">
                                        <input type="radio" value="specific" x-model="form.coupon_type" class="text-primary focus:ring-primary bg-surface border-primary/20">
                                        <span class="text-xs font-bold text-mainText group-hover:text-primary transition">Specific (Courses/Bundles)</span>
                                    </label>
                                </div>
                            </div>

                            {{-- Conditional: Specific Selectors --}}
                            <div x-show="form.coupon_type === 'specific'" x-collapse class="space-y-4 pl-4 border-l-2 border-primary/20">
                                {{-- Custom Multi-Select for Courses --}}
                                <div x-data="multiSelect({
                                    options: {{ $courses->map(fn($c) => ['value' => $c->id, 'label' => $c->title])->toJson() }},
                                    selected: form.courses,
                                    onChange: (vals) => form.courses = vals
                                })" class="relative">
                                    <label class="block text-[10px] font-bold text-mutedText uppercase mb-2 tracking-widest">Select Courses</label>

                                    {{-- Trigger --}}
                                    <div @click="open = !open" @click.away="open = false" class="w-full min-h-[42px] rounded-xl bg-navy border border-primary/10 px-3 py-2 flex flex-wrap gap-2 cursor-pointer items-center">
                                        <template x-if="selected.length === 0">
                                            <span class="text-sm text-mutedText/50">Select courses...</span>
                                        </template>
                                        <template x-for="val in selected" :key="val">
                                            <span class="bg-primary/10 text-primary text-[10px] font-bold px-2 py-1 rounded-md flex items-center gap-1">
                                                <span x-text="getLabel(val)"></span>
                                                <button type="button" @click.stop="remove(val)" class="hover:text-customWhite">&times;</button>
                                            </span>
                                        </template>
                                    </div>

                                    {{-- Dropdown --}}
                                    <div x-show="open" class="absolute z-10 w-full mt-1 bg-surface border border-primary/10 rounded-xl shadow-xl max-h-48 overflow-y-auto p-1">
                                        <input type="text" x-model="search" placeholder="Search..." class="w-full bg-navy border-none text-xs text-mainText rounded-lg mb-1 focus:ring-0 px-3 py-2">
                                        <template x-for="opt in filteredOptions" :key="opt.value">
                                            <div @click="toggle(opt.value)" class="px-3 py-2 rounded-lg hover:bg-primary/10 cursor-pointer flex items-center justify-between group">
                                                <span class="text-xs font-medium text-mainText group-hover:text-primary" x-text="opt.label"></span>
                                                <span x-show="selected.includes(opt.value)" class="text-primary text-xs">✓</span>
                                            </div>
                                        </template>
                                        <div x-show="filteredOptions.length === 0" class="px-3 py-2 text-xs text-mutedText italic">No results</div>
                                    </div>
                                </div>

                                {{-- Custom Multi-Select for Bundles --}}
                                <div x-data="multiSelect({
                                    options: {{ $bundles->map(fn($b) => ['value' => $b->id, 'label' => $b->title])->toJson() }},
                                    selected: form.bundles,
                                    onChange: (vals) => form.bundles = vals
                                })" class="relative">
                                    <label class="block text-[10px] font-bold text-mutedText uppercase mb-2 tracking-widest">Select Bundles</label>

                                    <div @click="open = !open" @click.away="open = false" class="w-full min-h-[42px] rounded-xl bg-navy border border-primary/10 px-3 py-2 flex flex-wrap gap-2 cursor-pointer items-center">
                                        <template x-if="selected.length === 0">
                                            <span class="text-sm text-mutedText/50">Select bundles...</span>
                                        </template>
                                        <template x-for="val in selected" :key="val">
                                            <span class="bg-secondary/10 text-secondary text-[10px] font-bold px-2 py-1 rounded-md flex items-center gap-1">
                                                <span x-text="getLabel(val)"></span>
                                                <button type="button" @click.stop="remove(val)" class="hover:text-customWhite">&times;</button>
                                            </span>
                                        </template>
                                    </div>

                                    <div x-show="open" class="absolute z-10 w-full mt-1 bg-surface border border-primary/10 rounded-xl shadow-xl max-h-48 overflow-y-auto p-1">
                                        <input type="text" x-model="search" placeholder="Search..." class="w-full bg-navy border-none text-xs text-mainText rounded-lg mb-1 focus:ring-0 px-3 py-2">
                                        <template x-for="opt in filteredOptions" :key="opt.value">
                                            <div @click="toggle(opt.value)" class="px-3 py-2 rounded-lg hover:bg-secondary/10 cursor-pointer flex items-center justify-between group">
                                                <span class="text-xs font-medium text-mainText group-hover:text-secondary" x-text="opt.label"></span>
                                                <span x-show="selected.includes(opt.value)" class="text-secondary text-xs">✓</span>
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </div>

                            {{-- Row 3: Value & Usage --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-mutedText uppercase mb-2 tracking-widest">Discount Type</label>
                                    <select x-model="form.type" class="w-full rounded-xl bg-navy border border-primary/10 px-4 py-2.5 text-sm text-mainText focus:border-primary focus:ring-1 focus:ring-primary outline-none transition appearance-none">
                                        <option value="fixed">Fixed Amount (₹)</option>
                                        <option value="percentage">Percentage (%)</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-mutedText uppercase mb-2 tracking-widest">Discount Value</label>
                                    <input type="number" step="0.01" x-model="form.value" class="w-full rounded-xl bg-navy border border-primary/10 px-4 py-2.5 text-sm text-mainText focus:border-primary focus:ring-1 focus:ring-primary outline-none transition">
                                </div>
                            </div>

                            {{-- Row 4: Limits & Expiry --}}
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-[10px] font-bold text-mutedText uppercase mb-2 tracking-widest">Usage Limit</label>
                                    <input type="number" x-model="form.usage_limit" class="w-full rounded-xl bg-navy border border-primary/10 px-4 py-2.5 text-sm text-mainText focus:border-primary focus:ring-1 focus:ring-primary outline-none transition">
                                </div>
                                <div>
                                    <label class="block text-[10px] font-bold text-mutedText uppercase mb-2 tracking-widest">Expiry Date</label>
                                    <input type="date" x-model="form.expiry_date" class="w-full rounded-xl bg-navy border border-primary/10 px-4 py-2.5 text-sm text-mainText focus:border-primary focus:ring-1 focus:ring-primary outline-none transition [color-scheme:dark]">
                                </div>
                            </div>

                            {{-- Active Toggle --}}
                            <div class="flex items-center gap-3 pt-2">
                                <label class="flex items-center cursor-pointer relative">
                                    <input type="checkbox" x-model="form.is_active" class="sr-only peer">
                                    <div class="w-10 h-5 bg-mutedText/20 rounded-full peer peer-checked:bg-primary after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-customWhite after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                                    <span class="ml-3 text-[11px] font-bold text-mainText uppercase tracking-widest">Activate Coupon</span>
                                </label>
                            </div>

                            {{-- Footer Actions --}}
                            <div class="flex items-center justify-end gap-3 pt-6 border-t border-primary/5">
                                <button type="button" @click="showModal = false" class="px-4 py-2 text-[10px] font-bold text-mutedText uppercase tracking-widest hover:text-mainText transition">Cancel</button>
                                <button type="submit" :disabled="isSubmitting" class="px-6 py-2.5 bg-primary text-customWhite text-[10px] font-bold uppercase tracking-widest rounded-xl hover:bg-secondary transition shadow-lg shadow-primary/20 disabled:opacity-50 flex items-center gap-2">
                                    <span x-show="isSubmitting" class="animate-spin h-3 w-3 border-2 border-white border-t-transparent rounded-full"></span>
                                    <span x-text="isSubmitting ? 'Saving...' : (isEdit ? 'Update Coupon' : 'Create Coupon')"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Multi-Select Logic Component
        document.addEventListener('alpine:init', () => {
            Alpine.data('multiSelect', (config) => ({
                options: config.options,
                selected: config.selected || [], // Array of IDs
                open: false,
                search: '',

                get filteredOptions() {
                    if(this.search === '') return this.options;
                    return this.options.filter(opt => opt.label.toLowerCase().includes(this.search.toLowerCase()));
                },

                getLabel(value) {
                    let opt = this.options.find(o => o.value == value);
                    return opt ? opt.label : value;
                },

                toggle(value) {
                    if (this.selected.includes(value)) {
                        this.selected = this.selected.filter(v => v !== value);
                    } else {
                        this.selected.push(value);
                    }
                    config.onChange(this.selected); // Update parent form data
                },

                remove(value) {
                    this.selected = this.selected.filter(v => v !== value);
                    config.onChange(this.selected);
                },

                // Watch for external changes (like resetting form)
                init() {
                    this.$watch('config.selected', (val) => {
                        this.selected = val;
                    });
                }
            }));
        });

        function couponManager() {
            return {
                showModal: false,
                isEdit: false,
                isSubmitting: false,
                isLoading: false,
                search: '{{ request('search') }}',
                form: {
                    id: null,
                    code: '',
                    coupon_type: 'general',
                    type: 'fixed',
                    value: '',
                    usage_limit: 100,
                    expiry_date: '',
                    courses: [],
                    bundles: [],
                    is_active: true
                },

                init() {
                    this.Toast = Swal.mixin({
                        toast: true, position: 'top-end', showConfirmButton: false, timer: 2000,
                        background: '#FFFFFF', color: '#2D2D2D',
                        customClass: { popup: 'rounded-xl border border-primary/10 shadow-lg' }
                    });
                    this.fetchCoupons();
                },

                async fetchCoupons(url = "{{ route('admin.coupons.index') }}") {
                    this.isLoading = true;
                    try {
                        const urlObj = new URL(url);
                        if(this.search) urlObj.searchParams.set('search', this.search);

                        const response = await fetch(urlObj.toString(), {
                            headers: { 'X-Requested-With': 'XMLHttpRequest' }
                        });
                        const data = await response.json();

                        if(data.status === 'success') {
                            document.getElementById('coupons-container').innerHTML = data.html;
                            // Re-bind pagination links
                            this.$nextTick(() => {
                                document.querySelectorAll('.pagination a').forEach(link => {
                                    link.addEventListener('click', (e) => {
                                        e.preventDefault();
                                        this.fetchCoupons(link.href);
                                    });
                                });
                            });
                        }
                    } catch (e) {
                        console.error("Fetch error", e);
                    } finally {
                        this.isLoading = false;
                    }
                },

                openModal(mode, id = null) {
                    this.isEdit = (mode === 'edit');

                    if (this.isEdit && id) {
                        // Fetch specific coupon data
                        fetch(`/admin/coupons/${id}/edit`)
                            .then(res => res.json())
                            .then(json => {
                                if(json.status === 'success') {
                                    const data = json.data;
                                    this.form = {
                                        id: data.id,
                                        code: data.code,
                                        coupon_type: data.coupon_type,
                                        type: data.type,
                                        value: data.value,
                                        usage_limit: data.usage_limit,
                                        expiry_date: data.expiry_date ? data.expiry_date.substring(0, 10) : '',
                                        courses: Array.isArray(data.selected_courses) ? data.selected_courses.map(Number) : [],
                                        bundles: Array.isArray(data.selected_bundles) ? data.selected_bundles.map(Number) : [],
                                        is_active: Boolean(data.is_active)
                                    };
                                    this.showModal = true;
                                }
                            });
                    } else {
                        // Reset Form
                        this.form = {
                            id: null,
                            code: '',
                            coupon_type: 'general',
                            type: 'fixed',
                            value: '',
                            usage_limit: 100,
                            expiry_date: '',
                            courses: [],
                            bundles: [],
                            is_active: true
                        };
                        this.generateCode();
                        this.showModal = true;
                    }
                },

                generateCode() {
                    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                    let result = '';
                    for (let i = 0; i < 8; i++) result += chars.charAt(Math.floor(Math.random() * chars.length));
                    this.form.code = result;
                },

                async submitForm() {
                    this.isSubmitting = true;
                    try {
                        const response = await fetch("{{ route('admin.coupons.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.form)
                        });

                        const result = await response.json();

                        if (response.ok) {
                            this.showModal = false;
                            this.Toast.fire({ icon: 'success', title: result.message });
                            this.fetchCoupons();
                        } else {
                            throw new Error(result.message || 'Validation failed');
                        }
                    } catch (error) {
                        Swal.fire({ title: 'Error', text: error.message, icon: 'error', confirmButtonColor: '#F7941D' });
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                async deleteCoupon(id) {
                    const check = await Swal.fire({
                        title: 'Delete Coupon?',
                        text: "This action cannot be undone.",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Delete',
                        confirmButtonColor: '#D04A02',
                        cancelButtonColor: '#555555'
                    });

                    if (check.isConfirmed) {
                        try {
                            const response = await fetch(`/admin/coupons/${id}`, {
                                method: 'DELETE',
                                headers: { 'X-CSRF-TOKEN': "{{ csrf_token() }}" }
                            });

                            if (response.ok) {
                                this.Toast.fire({ icon: 'success', title: 'Coupon deleted successfully' });
                                this.fetchCoupons();
                            } else {
                                throw new Error('Delete failed');
                            }
                        } catch (e) {
                            Swal.fire({ title: 'Error', text: e.message, icon: 'error' });
                        }
                    }
                }
            };
        }
    </script>
@endsection
