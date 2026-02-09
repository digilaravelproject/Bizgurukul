@extends('layouts.admin')
@section('title', 'Category Manager')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div x-data="categoryManager()" x-init="init()" class="font-sans text-mainText space-y-8 animate-fade-in">

        {{-- 1. HEADER & ACTIONS --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-mainText uppercase">Category Manager</h1>
                <p class="text-xs text-mutedText mt-1 font-bold uppercase tracking-[2px]">Structure your educational ecosystem</p>
            </div>

            <button @click="openModal('create')"
                class="brand-gradient text-customWhite px-8 py-4 rounded-2xl font-black text-xs uppercase tracking-widest shadow-xl shadow-primary/30 hover:shadow-primary/50 hover:-translate-y-1 active:scale-95 transition-all flex items-center gap-2">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="3">
                    <path d="M12 4v16m8-8H4" />
                </svg>
                New Category
            </button>
        </div>

        {{-- 2. SEARCH BAR --}}
        <div class="relative w-full md:max-w-md group">
            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-mutedText group-focus-within:text-primary transition-colors">
                <svg x-show="!isLoading" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
                <svg x-show="isLoading" class="animate-spin w-5 h-5 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                </svg>
            </span>
            <input type="text" x-model="search" @input.debounce.500ms="performSearch" placeholder="Filter categories..."
                class="w-full pl-12 pr-6 py-4 bg-surface border border-primary/10 text-mainText font-bold placeholder-mutedText/40 rounded-2xl focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition shadow-sm">
        </div>

        {{-- 3. CONTENT AREA --}}
        <div id="categories-container" class="relative min-h-[400px]">
            {{-- This is replaced by partials via AJAX --}}
            @include('admin.categories.partials.table')
        </div>

        {{-- 4. MODAL --}}
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-[60] overflow-y-auto" x-cloak x-transition.opacity>
            <div class="fixed inset-0 bg-navy/80 backdrop-blur-md" @click="showModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative w-full max-w-md rounded-[2.5rem] bg-surface border border-primary/10 shadow-2xl overflow-hidden flex flex-col"
                     x-show="showModal" x-transition:enter="transition ease-out duration-300 translate-y-8" x-transition:enter-end="translate-y-0">

                    {{-- Header --}}
                    <div class="px-8 py-6 border-b border-primary/5 bg-primary/5 flex items-center justify-between">
                        <h3 class="text-xl font-black text-mainText tracking-tight uppercase" x-text="isEdit ? 'Update Details' : 'Fresh Category'"></h3>
                        <button @click="showModal = false" class="p-2 rounded-full hover:bg-secondary/10 hover:text-secondary transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    {{-- Form --}}
                    <form @submit.prevent="submitForm" class="p-8 space-y-6">
                        <div>
                            <label class="block text-[10px] font-black text-mutedText uppercase mb-2 tracking-widest ml-1">Category Label</label>
                            <input type="text" x-model="form.name" required
                                class="w-full rounded-2xl bg-navy/20 border border-primary/10 px-5 py-4 text-sm font-black text-mainText focus:ring-4 focus:ring-primary/10 focus:border-primary outline-none transition-all"
                                placeholder="e.g. Graphic Design">
                        </div>

                        <div>
                            <label class="block text-[10px] font-black text-mutedText uppercase mb-2 tracking-widest ml-1">Hierarchy Level</label>
                            <div class="relative">
                                <select x-model="form.parent_id" class="w-full rounded-2xl bg-navy/20 border border-primary/10 px-5 py-4 text-sm font-bold text-mainText focus:border-primary focus:ring-0 appearance-none cursor-pointer">
                                    <option value="">Root Level (Main)</option>
                                    @foreach($allCategories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <div class="absolute right-5 top-1/2 -translate-y-1/2 pointer-events-none text-primary">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path></svg>
                                </div>
                            </div>
                        </div>

                        {{-- Active Toggle --}}
                        <div class="flex items-center justify-between p-5 bg-navy/10 rounded-2xl border border-primary/5">
                            <span class="text-[10px] font-black text-mainText uppercase tracking-widest">Visibility Status</span>
                            <label class="relative inline-flex items-center cursor-pointer">
                                <input type="checkbox" x-model="form.is_active" class="sr-only peer">
                                <div class="w-12 h-6 bg-mutedText/20 rounded-full peer peer-checked:bg-primary after:absolute after:top-[2px] after:left-[2px] after:bg-white after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:after:translate-x-6 shadow-sm"></div>
                            </label>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="flex flex-col gap-3 pt-4 border-t border-primary/5">
                            <button type="submit" :disabled="isSubmitting"
                                class="brand-gradient w-full py-4 text-customWhite text-[11px] font-black uppercase tracking-[2px] rounded-2xl shadow-xl shadow-primary/20 flex justify-center items-center gap-3 active:scale-95 transition-all">
                                <span x-show="isSubmitting" class="animate-spin h-4 w-4 border-2 border-white border-t-transparent rounded-full"></span>
                                <span x-text="isSubmitting ? 'Syncing...' : (isEdit ? 'Push Updates' : 'Confirm Launch')"></span>
                            </button>

                            <div class="flex items-center justify-between mt-2">
                                <button type="button" x-show="isEdit" @click="deleteCategory(form.id)" class="text-[9px] font-black text-secondary hover:underline uppercase tracking-widest transition">Destroy Category</button>
                                <button type="button" @click="showModal = false" class="ml-auto text-[9px] font-black text-mutedText uppercase tracking-widest hover:text-mainText transition">Dismiss</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        function categoryManager() {
            return {
                showModal: false,
                isEdit: false,
                isSubmitting: false,
                isLoading: false,
                search: '{{ request('search') }}',
                form: { id: null, name: '', parent_id: '', is_active: true },

                init() {
                    this.Toast = Swal.mixin({
                        toast: true, position: 'top-end', showConfirmButton: false, timer: 2000,
                        background: '#FFFFFF', color: '#2D2D2D',
                        customClass: { popup: 'rounded-2xl border border-primary/10 shadow-lg font-sans' }
                    });
                },

                async performSearch() {
                    this.isLoading = true;
                    try {
                        const url = new URL("{{ route('admin.categories.index') }}");
                        if(this.search) url.searchParams.set('search', this.search);

                        const response = await fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } });
                        if (response.ok) {
                            const html = await response.text();
                            document.getElementById('categories-container').innerHTML = html;
                        }
                    } catch (error) { console.error('Search failed:', error); }
                    finally { this.isLoading = false; }
                },

                openModal(mode, data = null) {
                    this.isEdit = (mode === 'edit');
                    if (this.isEdit && data) {
                        this.form = { id: data.id, name: data.name, parent_id: data.parent_id || '', is_active: Boolean(data.is_active) };
                    } else {
                        this.form = { id: null, name: '', parent_id: '', is_active: true };
                    }
                    this.showModal = true;
                },

                async submitForm() {
                    this.isSubmitting = true;
                    let url = this.isEdit ? `{{ url('admin/categories/update') }}/${this.form.id}` : `{{ route('admin.categories.store') }}`;
                    try {
                        let response = await fetch(url, {
                            method: this.isEdit ? 'PUT' : 'POST',
                            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
                            body: JSON.stringify(this.form)
                        });
                        let result = await response.json();
                        if (!response.ok) throw new Error(result.message || "Request failed");

                        this.Toast.fire({ icon: 'success', title: result.message });
                        this.showModal = false;
                        this.performSearch();
                    } catch (error) {
                        Swal.fire({ title: 'System Alert', text: error.message, icon: 'error', confirmButtonColor: '#F7941D' });
                    } finally { this.isSubmitting = false; }
                },

                async deleteCategory(id) {
                    const check = await Swal.fire({
                        title: 'Confirm Destruction?',
                        text: "All associated sub-groups will be purged!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Confirm Delete',
                        confirmButtonColor: '#D04A02',
                        cancelButtonColor: '#555555',
                        customClass: { popup: 'rounded-[2rem]' }
                    });

                    if (check.isConfirmed) {
                        try {
                            this.showModal = false;
                            let response = await fetch(`{{ url('admin/categories/delete') }}/${id}`, {
                                method: 'DELETE',
                                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
                            });
                            let res = await response.json();
                            if(res.status) {
                                this.Toast.fire({ icon: 'success', title: res.message });
                                this.performSearch();
                            } else { throw new Error(res.message); }
                        } catch(e) { Swal.fire({ title: 'Error', text: e.message, icon: 'error' }); }
                    }
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        .custom-scrollbar::-webkit-scrollbar { width: 4px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(247, 148, 29, 0.2); border-radius: 10px; }
    </style>
@endsection
