@extends('layouts.admin')

@section('title', 'Coupon Manager')

@section('content')
    <!-- Main Wrapper with Alpine Component -->
    <div x-data="couponManager()" x-init="init()" class="max-w-7xl mx-auto py-8 px-4 font-sans text-mainText">

        {{-- Top Header & Actions --}}
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 mb-8">
            <div>
                <h1 class="text-3xl font-bold text-navy tracking-tight">Coupon Manager</h1>
                <p class="text-mutedText text-sm mt-1">Manage discounts, offers, and promo codes.</p>
            </div>
            <button @click="openModal()"
                class="bg-primary text-customWhite hover:bg-navy px-6 py-3 rounded-xl font-bold text-sm shadow-lg shadow-primary/20 transition-all active:scale-95 flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                </svg>
                Create New Coupon
            </button>
        </div>

        {{-- Search & Filters --}}
        <div class="bg-surface rounded-2xl p-2 mb-6 border border-slate-200 shadow-sm flex items-center">
            <div class="relative w-full md:w-96">
                <svg class="absolute left-4 top-1/2 -translate-y-1/2 w-5 h-5 text-mutedText" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                </svg>
                {{-- SEARCH FIX: @input added ensures clearing triggers fetch immediately --}}
                <input type="text" x-model.debounce.300ms="search" @input.debounce.300ms="fetchCoupons()"
                    placeholder="Search by code..."
                    class="w-full pl-12 pr-4 py-3 bg-transparent border-none focus:ring-0 text-navy font-medium placeholder-slate-400">
            </div>
        </div>

        {{-- Data Table --}}
        <div class="bg-white rounded-3xl border border-slate-200 overflow-hidden shadow-sm">
            <div class="overflow-x-auto">
                <table class="w-full text-left">
                    <thead class="bg-surface border-b border-slate-200">
                        <tr>
                            <th class="px-6 py-4 text-xs font-bold text-mutedText uppercase tracking-wider">Code</th>
                            <th class="px-6 py-4 text-xs font-bold text-mutedText uppercase tracking-wider">Scope</th>
                            <th class="px-6 py-4 text-xs font-bold text-mutedText uppercase tracking-wider">Discount</th>
                            <th class="px-6 py-4 text-xs font-bold text-mutedText uppercase tracking-wider">Usage</th>
                            <th class="px-6 py-4 text-xs font-bold text-mutedText uppercase tracking-wider text-right">
                                Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-if="isLoading">
                            @for ($i = 0; $i < 5; $i++)
                                <tr class="animate-pulse">
                                    <td class="px-6 py-4">
                                        <div class="h-4 bg-surface rounded w-24"></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="h-4 bg-surface rounded w-32"></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="h-4 bg-surface rounded w-16"></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="h-4 bg-surface rounded w-20"></div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="h-8 bg-surface rounded w-8 ml-auto"></div>
                                    </td>
                                </tr>
                            @endfor
                        </template>
                    <tbody x-show="!isLoading" x-html="tableHtml"></tbody>
                    </tbody>
                </table>
            </div>
            <div x-show="!isLoading && (!tableHtml || tableHtml.trim() === '')" class="p-12 text-center"
                style="display: none;">
                <p class="text-mutedText font-medium">No coupons found.</p>
            </div>
        </div>

        <!-- Pagination -->
        <div class="mt-6" x-html="paginationHtml"></div>

        {{-- Slide-Over Modal --}}
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-hidden"
            aria-labelledby="slide-over-title" role="dialog" aria-modal="true">
            <div class="absolute inset-0 overflow-hidden">
                <div class="absolute inset-0 bg-navy/40 backdrop-blur-sm transition-opacity" x-show="showModal"
                    x-transition.opacity @click="showModal = false"></div>

                <div class="fixed inset-y-0 right-0 pl-10 max-w-full flex">
                    <div class="w-screen max-w-2xl transform transition ease-in-out duration-300 sm:duration-500"
                        x-show="showModal" x-transition:enter="translate-x-full" x-transition:enter-end="translate-x-0"
                        x-transition:leave="translate-x-0" x-transition:leave-end="translate-x-full">

                        <form @submit.prevent="submitForm"
                            class="h-full flex flex-col bg-white shadow-2xl overflow-y-scroll">
                            <div class="px-8 py-6 bg-surface border-b border-slate-200 flex items-center justify-between">
                                <h2 class="text-xl font-bold text-navy" x-text="isEdit ? 'Edit Coupon' : 'New Coupon'"></h2>
                                <button type="button" @click="showModal = false" class="text-mutedText hover:text-navy">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M6 18L18 6M6 6l12 12" />
                                    </svg>
                                </button>
                            </div>

                            <div class="flex-1 px-8 py-8 space-y-8">
                                <div x-show="errorMessage" x-transition
                                    class="bg-red-50 text-red-600 p-4 rounded-xl text-sm font-medium border border-red-100">
                                    <span x-text="errorMessage"></span>
                                </div>

                                <div class="space-y-2">
                                    <label class="text-xs font-bold text-navy uppercase tracking-wider">Coupon Code</label>
                                    <div class="relative">
                                        <input type="text" x-model="formData.code" required
                                            class="w-full bg-surface border-slate-200 rounded-xl px-4 py-3 text-navy font-bold uppercase tracking-widest focus:border-primary focus:ring-primary">
                                        <button type="button" @click="generateCode()" x-show="!isEdit"
                                            class="absolute right-3 top-1/2 -translate-y-1/2 text-[10px] font-bold text-primary bg-primary/10 px-2 py-1 rounded hover:bg-primary/20">
                                            GENERATE
                                        </button>
                                    </div>
                                </div>

                                <div class="p-4 bg-surface rounded-2xl border border-slate-200">
                                    <label
                                        class="text-xs font-bold text-navy uppercase tracking-wider block mb-3">Application
                                        Scope</label>
                                    <div class="flex gap-6">
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" value="general" x-model="formData.coupon_type"
                                                class="text-primary focus:ring-primary">
                                            <span class="text-sm font-medium text-navy">General (Store-wide)</span>
                                        </label>
                                        <label class="flex items-center gap-2 cursor-pointer">
                                            <input type="radio" value="specific" x-model="formData.coupon_type"
                                                class="text-primary focus:ring-primary">
                                            <span class="text-sm font-medium text-navy">Specific (Courses/Bundles)</span>
                                        </label>
                                    </div>
                                </div>

                                <!-- Specific Selection Area -->
                                <div x-show="formData.coupon_type === 'specific'" x-transition
                                    class="space-y-6 pl-4 border-l-2 border-primary">

                                    {{-- Course Multi Select --}}
                                    <div class="relative" x-data="{
                                        search: '',
                                        open: false,
                                        options: {{ $courses->toJson() }},
                                        get selectedItems() { return this.options.filter(opt => formData.courses.includes(opt.id)) },
                                        get filteredOptions() { return this.search === '' ? this.options : this.options.filter(opt => opt.title.toLowerCase().includes(this.search.toLowerCase())) }
                                    }">
                                        <label
                                            class="text-xs font-bold text-navy uppercase tracking-wider block mb-2">Courses</label>

                                        {{-- Selected Tags Area --}}
                                        <div @click="open = !open" @click.away="open = false"
                                            class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 min-h-[50px] cursor-text flex flex-wrap gap-2 items-center">
                                            <template x-for="item in selectedItems" :key="item.id">
                                                <span
                                                    class="bg-primary/10 text-primary text-xs font-bold px-2 py-1 rounded-md flex items-center gap-1">
                                                    <span x-text="item.title"></span>
                                                    <button type="button"
                                                        @click.stop="formData.courses = formData.courses.filter(id => id !== item.id)"
                                                        class="hover:text-red-500">&times;</button>
                                                </span>
                                            </template>
                                            <input type="text" x-model="search" placeholder="Select courses..."
                                                class="border-none p-0 focus:ring-0 text-sm flex-1 min-w-[100px]">
                                        </div>

                                        {{-- Dropdown List --}}
                                        <div x-show="open"
                                            class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                                            <template x-for="option in filteredOptions" :key="option.id">
                                                <div @click="if(formData.courses.includes(option.id)) { formData.courses = formData.courses.filter(id => id !== option.id) } else { formData.courses.push(option.id) }"
                                                    class="px-4 py-2 hover:bg-surface cursor-pointer text-sm text-navy font-medium transition-colors flex justify-between items-center"
                                                    :class="formData.courses.includes(option.id) ? 'bg-primary/5 text-primary' :
                                                        ''">
                                                    <span x-text="option.title"></span>
                                                    <span x-show="formData.courses.includes(option.id)">✓</span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                    {{-- Bundle Multi Select --}}
                                    <div class="relative" x-data="{
                                        search: '',
                                        open: false,
                                        options: {{ $bundles->toJson() }},
                                        get selectedItems() { return this.options.filter(opt => formData.bundles.includes(opt.id)) },
                                        get filteredOptions() { return this.search === '' ? this.options : this.options.filter(opt => opt.title.toLowerCase().includes(this.search.toLowerCase())) }
                                    }">
                                        <label
                                            class="text-xs font-bold text-navy uppercase tracking-wider block mb-2">Bundles</label>

                                        <div @click="open = !open" @click.away="open = false"
                                            class="w-full bg-white border border-slate-300 rounded-xl px-4 py-3 min-h-[50px] cursor-text flex flex-wrap gap-2 items-center">
                                            <template x-for="item in selectedItems" :key="item.id">
                                                <span
                                                    class="bg-secondary/20 text-navy text-xs font-bold px-2 py-1 rounded-md flex items-center gap-1">
                                                    <span x-text="item.title"></span>
                                                    <button type="button"
                                                        @click.stop="formData.bundles = formData.bundles.filter(id => id !== item.id)"
                                                        class="hover:text-red-500">&times;</button>
                                                </span>
                                            </template>
                                            <input type="text" x-model="search" placeholder="Select bundles..."
                                                class="border-none p-0 focus:ring-0 text-sm flex-1 min-w-[100px]">
                                        </div>

                                        <div x-show="open"
                                            class="absolute z-10 w-full mt-1 bg-white border border-slate-200 rounded-xl shadow-xl max-h-60 overflow-y-auto">
                                            <template x-for="option in filteredOptions" :key="option.id">
                                                <div @click="if(formData.bundles.includes(option.id)) { formData.bundles = formData.bundles.filter(id => id !== option.id) } else { formData.bundles.push(option.id) }"
                                                    class="px-4 py-2 hover:bg-surface cursor-pointer text-sm text-navy font-medium transition-colors flex justify-between items-center"
                                                    :class="formData.bundles.includes(option.id) ? 'bg-secondary/10' : ''">
                                                    <span x-text="option.title"></span>
                                                    <span x-show="formData.bundles.includes(option.id)">✓</span>
                                                </div>
                                            </template>
                                        </div>
                                    </div>

                                </div>

                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label
                                            class="text-xs font-bold text-navy uppercase tracking-wider block mb-2">Type</label>
                                        <select x-model="formData.type"
                                            class="w-full bg-surface border-slate-200 rounded-xl px-4 py-3 text-navy font-medium focus:border-primary focus:ring-primary">
                                            <option value="fixed">Fixed Amount (₹)</option>
                                            <option value="percentage">Percentage (%)</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label
                                            class="text-xs font-bold text-navy uppercase tracking-wider block mb-2">Value</label>
                                        <input type="number" x-model="formData.value" step="0.01"
                                            class="w-full bg-surface border-slate-200 rounded-xl px-4 py-3 text-navy font-bold focus:border-primary focus:ring-primary">
                                    </div>
                                </div>

                                <div class="grid grid-cols-2 gap-6">
                                    <div>
                                        <label
                                            class="text-xs font-bold text-navy uppercase tracking-wider block mb-2">Usage
                                            Limit</label>
                                        <input type="number" x-model="formData.usage_limit"
                                            class="w-full bg-surface border-slate-200 rounded-xl px-4 py-3 text-navy font-medium focus:border-primary focus:ring-primary">
                                    </div>
                                    <div>
                                        <label
                                            class="text-xs font-bold text-navy uppercase tracking-wider block mb-2">Expiry
                                            Date</label>
                                        <input type="date" x-model="formData.expiry_date"
                                            class="w-full bg-surface border-slate-200 rounded-xl px-4 py-3 text-navy font-medium focus:border-primary focus:ring-primary">
                                    </div>
                                </div>

                            </div>

                            <div
                                class="px-8 py-6 bg-surface border-t border-slate-200 flex items-center justify-end gap-4">
                                <button type="button" @click="showModal = false"
                                    class="px-6 py-3 rounded-xl font-bold text-mutedText hover:bg-slate-200 transition-colors">Cancel</button>
                                <button type="submit" :disabled="isSaving"
                                    class="bg-primary text-customWhite hover:bg-navy px-8 py-3 rounded-xl font-bold shadow-lg shadow-primary/20 transition-all flex items-center gap-2 disabled:opacity-50">
                                    <span x-show="isSaving"
                                        class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                                    <span
                                        x-text="isSaving ? 'Saving...' : (isEdit ? 'Update Coupon' : 'Create Coupon')"></span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function couponManager() {
            return {
                coupons: [],
                tableHtml: '',
                paginationHtml: '',
                isLoading: true,
                search: '',
                showModal: false,
                isEdit: false,
                isSaving: false,
                errorMessage: null,

                // Parent Form Data
                formData: {
                    id: null,
                    code: '',
                    coupon_type: 'general',
                    type: 'fixed',
                    value: '',
                    expiry_date: '',
                    usage_limit: 1,
                    courses: [], // Stores IDs directly
                    bundles: [] // Stores IDs directly
                },

                init() {
                    this.fetchCoupons();
                },

                async fetchCoupons(url = "{{ route('admin.coupons.index') }}") {
                    this.isLoading = true;
                    const urlObj = new URL(url);

                    // Clear search if empty to reset backend logic
                    if (this.search && this.search.trim() !== '') {
                        urlObj.searchParams.set('search', this.search);
                    } else {
                        urlObj.searchParams.delete('search');
                    }

                    try {
                        const res = await fetch(urlObj.toString(), {
                            headers: {
                                "X-Requested-With": "XMLHttpRequest"
                            }
                        });
                        const data = await res.json();

                        if (data.status === 'success') {
                            this.tableHtml = data.html;
                            this.paginationHtml = data.pagination;
                            this.$nextTick(() => {
                                const links = document.querySelectorAll('.pagination a');
                                links.forEach(link => {
                                    link.addEventListener('click', (e) => {
                                        e.preventDefault();
                                        this.fetchCoupons(link.href);
                                    });
                                });
                            });
                        }
                    } catch (e) {
                        console.error(e);
                    } finally {
                        this.isLoading = false;
                    }
                },

                openModal(id = null) {
                    this.errorMessage = null;
                    if (id) {
                        this.isEdit = true;
                        this.loadCouponData(id);
                    } else {
                        this.isEdit = false;
                        this.resetForm();
                        this.generateCode();
                        this.showModal = true;
                    }
                },

                async loadCouponData(id) {
                    try {
                        const res = await fetch(`/admin/coupons/${id}/edit`);
                        const json = await res.json();

                        if (json.status === 'success') {
                            const data = json.data;
                            this.formData = {
                                id: data.id,
                                code: data.code,
                                coupon_type: data.coupon_type,
                                type: data.type,
                                value: data.value,
                                usage_limit: data.usage_limit,
                                expiry_date: data.expiry_date ? data.expiry_date.substring(0, 10) : '',

                                // Ensure these are arrays of IDs for x-model/includes checking
                                courses: Array.isArray(data.selected_courses) ?
                                    data.selected_courses.map(Number) // Convert to numbers if strings
                                    :
                                    [],
                                bundles: Array.isArray(data.selected_bundles) ?
                                    data.selected_bundles.map(Number) :
                                    []
                            };
                            this.showModal = true;
                        }
                    } catch (e) {
                        alert('Error loading coupon');
                    }
                },

                resetForm() {
                    this.formData = {
                        id: null,
                        code: '',
                        coupon_type: 'general',
                        type: 'fixed',
                        value: '',
                        expiry_date: '',
                        usage_limit: 1,
                        courses: [],
                        bundles: []
                    };
                },

                generateCode() {
                    const chars = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789';
                    let result = '';
                    for (let i = 0; i < 8; i++) result += chars.charAt(Math.floor(Math.random() * chars.length));
                    this.formData.code = result;
                },

                async submitForm() {
                    this.isSaving = true;
                    this.errorMessage = null;

                    try {
                        const res = await fetch("{{ route('admin.coupons.store') }}", {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': "{{ csrf_token() }}",
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.formData)
                        });

                        const json = await res.json();

                        if (res.ok) {
                            this.showModal = false;
                            this.fetchCoupons();
                        } else {
                            this.errorMessage = json.message || 'Validation failed. Please check inputs.';
                        }
                    } catch (e) {
                        this.errorMessage = "Network error occurred.";
                    } finally {
                        this.isSaving = false;
                    }
                },

                async deleteItem(id) {
                    if (!confirm('Delete this coupon?')) return;
                    try {
                        const res = await fetch(`/admin/coupons/${id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': "{{ csrf_token() }}"
                            }
                        });
                        if (res.ok) this.fetchCoupons();
                    } catch (e) {
                        alert('Delete failed');
                    }
                }
            }
        }
    </script>
@endsection
