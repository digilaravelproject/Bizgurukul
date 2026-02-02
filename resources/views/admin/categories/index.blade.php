@extends('layouts.admin')
@section('title', 'Category Manager')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    {{-- Added x-init to handle loading state --}}
    <div x-data="categoryManager()" class="container-fluid font-sans p-4 md:p-6 bg-navy min-h-screen text-mainText">

        {{-- 1. HEADER & ACTIONS --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 animate-fade-in">
            <div>
                <h2 class="text-2xl font-bold text-mainText tracking-tight">Category Manager</h2>
                <p class="text-xs text-mutedText mt-1 font-medium uppercase tracking-wider">Structure your educational content</p>
            </div>

            <button @click="openModal('create')"
                class="inline-flex items-center gap-2 rounded-xl bg-primary px-6 py-3 text-xs font-bold text-customWhite shadow-lg shadow-primary/20 hover:bg-secondary transition-all duration-300 active:scale-95">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path d="M12 4v16m8-8H4" /></svg>
                ADD CATEGORY
            </button>
        </div>

        {{-- 2. AJAX SEARCH BAR --}}
        <div class="mb-8 relative w-full md:max-w-sm">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-mutedText">
                {{-- Show Search Icon when not loading --}}
                <svg x-show="!isLoading" class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                {{-- Show Spinner when loading --}}
                <svg x-show="isLoading" class="animate-spin w-4 h-4 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
            </span>

            {{-- Search Input with Debounce --}}
            <input type="text"
                x-model="search"
                @input.debounce.500ms="performSearch"
                placeholder="Search by name..."
                class="w-full pl-10 pr-4 py-2.5 bg-surface border border-primary/10 text-mainText placeholder-mutedText/50 rounded-xl focus:ring-1 focus:ring-primary focus:border-primary outline-none transition text-sm shadow-sm">
        </div>

        {{-- 3. CONTENT AREA (Loaded via Partial) --}}
        <div id="categories-container">
            @include('admin.categories.partials.table')
        </div>

        {{-- MODAL --}}
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-[60]" x-cloak>
            <div class="fixed inset-0 bg-mainText/40 backdrop-blur-sm" x-show="showModal" x-transition.opacity></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div @click.away="showModal = false" class="relative w-full max-w-md rounded-2xl bg-surface border border-primary/10 shadow-2xl overflow-hidden">
                    <div class="px-6 py-4 border-b border-primary/5 bg-primary/5 flex items-center justify-between">
                        <h3 class="text-sm font-bold text-mainText uppercase tracking-wider" x-text="isEdit ? 'Update Category' : 'New Category'"></h3>
                        <button @click="showModal = false" class="text-mutedText hover:text-secondary transition"><svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path d="M6 18L18 6M6 6l12 12"></path></svg></button>
                    </div>

                    <form @submit.prevent="submitForm" class="p-6 space-y-5">
                        <div>
                            <label class="block text-[10px] font-bold text-mutedText uppercase mb-2 tracking-widest">Category Name</label>
                            <input type="text" x-model="form.name" required class="w-full rounded-xl bg-navy border border-primary/10 px-4 py-2.5 text-sm text-mainText focus:border-primary focus:ring-1 focus:ring-primary outline-none transition">
                        </div>

                        <div>
                            <label class="block text-[10px] font-bold text-mutedText uppercase mb-2 tracking-widest">Parent Category</label>
                            <select x-model="form.parent_id" class="w-full rounded-xl bg-navy border border-primary/10 px-4 py-2.5 text-sm text-mainText focus:border-primary focus:ring-1 focus:ring-primary outline-none transition appearance-none">
                                <option value="">None (Top Level)</option>
                                @foreach($allCategories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="flex items-center gap-3 pt-2">
                            <label class="flex items-center cursor-pointer relative">
                                <input type="checkbox" x-model="form.is_active" class="sr-only peer">
                                <div class="w-10 h-5 bg-mutedText/20 rounded-full peer peer-checked:bg-primary after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-customWhite after:rounded-full after:h-4 after:w-4 after:transition-all peer-checked:after:translate-x-5"></div>
                                <span class="ml-3 text-[11px] font-bold text-mainText uppercase tracking-widest">Enable Category</span>
                            </label>
                        </div>

                        <div class="flex items-center justify-between pt-8 border-t border-primary/5">
                            <button type="button" x-show="isEdit" @click="deleteCategory(form.id)" class="text-[10px] font-bold text-secondary hover:text-red-700 uppercase tracking-widest transition">Delete Permanent</button>
                            <div class="flex gap-2 ml-auto">
                                <button type="button" @click="showModal = false" class="px-4 py-2 text-[10px] font-bold text-mutedText uppercase tracking-widest hover:text-mainText transition">Cancel</button>
                                <button type="submit" :disabled="isSubmitting" class="px-6 py-2.5 bg-primary text-customWhite text-[10px] font-bold uppercase tracking-widest rounded-xl hover:bg-secondary transition shadow-lg shadow-primary/20 disabled:opacity-50">
                                    <span x-text="isSubmitting ? 'Saving...' : (isEdit ? 'Update' : 'Confirm')"></span>
                                </button>
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
                        customClass: { popup: 'rounded-xl border border-primary/10 shadow-lg' }
                    });
                },

                // AJAX Search Function
                async performSearch() {
                    this.isLoading = true;
                    try {
                        // Construct the URL with search params
                        const url = new URL("{{ route('admin.categories.index') }}");
                        if(this.search) url.searchParams.set('search', this.search);

                        const response = await fetch(url, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        });

                        if (response.ok) {
                            const html = await response.text();
                            document.getElementById('categories-container').innerHTML = html;
                        }
                    } catch (error) {
                        console.error('Search failed:', error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                openModal(mode, data = null) {
                    this.isEdit = (mode === 'edit');
                    this.showModal = true;
                    if (this.isEdit && data) {
                        this.form = { id: data.id, name: data.name, parent_id: data.parent_id || '', is_active: Boolean(data.is_active) };
                    } else {
                        this.form = { id: null, name: '', parent_id: '', is_active: true };
                    }
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

                        // Refresh the table via AJAX instead of full reload
                        this.performSearch();

                    } catch (error) {
                        Swal.fire({ title: 'System Alert', text: error.message, icon: 'error', confirmButtonColor: '#F7941D' });
                    } finally { this.isSubmitting = false; }
                },

                async deleteCategory(id) {
                    this.showModal = false;
                    const check = await Swal.fire({
                        title: 'Confirm Deletion?',
                        text: "All sub-categories within this group will also be removed!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Delete',
                        confirmButtonColor: '#D04A02',
                        cancelButtonColor: '#555555'
                    });

                    if (check.isConfirmed) {
                        try {
                            let response = await fetch(`{{ url('admin/categories/delete') }}/${id}`, {
                                method: 'DELETE',
                                headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
                            });
                            let res = await response.json();

                            if(res.status) {
                                this.Toast.fire({ icon: 'success', title: res.message });
                                this.performSearch(); // Refresh table via AJAX
                            } else {
                                throw new Error(res.message);
                            }
                        } catch(e) {
                            Swal.fire({ title: 'Error', text: e.message, icon: 'error' });
                        }
                    }
                }
            }
        }
    </script>
@endsection
