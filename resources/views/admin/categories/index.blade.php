@extends('layouts.admin')
@section('title', 'Category Manager')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div x-data="categoryManager()" class="container-fluid font-sans p-4 md:p-6">

        {{-- 1. HEADER & ACTIONS --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 animate-[fadeIn_0.3s_ease-out]">
            <div>
                <h2 class="text-2xl font-extrabold text-white tracking-tight">Category Manager</h2>
                <p class="text-xs text-mutedText mt-1">Organize courses into parents and sub-categories.</p>
            </div>

            <button @click="openModal('create')"
                class="w-full md:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-primary to-indigo-600 px-5 py-2.5 text-xs font-bold text-white shadow-lg shadow-primary/25 hover:shadow-primary/40 hover:-translate-y-0.5 transition-all duration-300 border border-white/10">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Add Category
            </button>
        </div>

        {{-- 2. SEARCH BAR --}}
        <div class="mb-6 relative w-full md:max-w-md animate-[fadeIn_0.4s_ease-out]">
            <form action="{{ route('admin.categories.index') }}" method="GET">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-mutedText">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search categories..."
                    class="w-full pl-11 pr-4 py-3 bg-white/5 border border-white/10 text-white placeholder-mutedText/50 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary/50 outline-none transition shadow-sm backdrop-blur-sm text-sm">
            </form>
        </div>

        {{-- 3A. DESKTOP TABLE (Hidden on Mobile) --}}
        <div class="hidden md:block overflow-hidden rounded-2xl border border-white/5 bg-navy/40 backdrop-blur-md shadow-xl relative min-h-[400px] animate-[fadeIn_0.5s_ease-out]">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-mutedText">
                    <thead class="bg-white/5 text-xs uppercase font-bold text-white border-b border-white/5 tracking-wider">
                        <tr>
                            <th class="px-6 py-5">Category Name</th>
                            <th class="px-6 py-5">Type</th>
                            <th class="px-6 py-5 w-[40%]">Sub-Categories (Click to Edit)</th>
                            <th class="px-6 py-5">Status</th>
                            <th class="px-6 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">
                        @forelse($categories as $category)
                            <tr class="hover:bg-white/[0.02] transition-colors group">

                                {{-- Name --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 flex items-center justify-center text-white font-bold border border-white/10 text-lg">
                                            {{ substr($category->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <div class="font-bold text-white text-sm">{{ $category->name }}</div>
                                            <div class="text-xs text-mutedText">{{ $category->slug }}</div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Type --}}
                                <td class="px-6 py-4">
                                    <span class="inline-flex items-center rounded-md bg-purple-500/10 px-2.5 py-1 text-xs font-bold text-purple-400 border border-purple-500/20">
                                        Parent
                                    </span>
                                </td>

                                {{-- Sub Categories (Chips without Cross) --}}
                                <td class="px-6 py-4">
                                    @if($category->subCategories->count() > 0)
                                        <div class="flex flex-wrap gap-2">
                                            @foreach($category->subCategories as $sub)
                                                <button @click.stop="openModal('edit', {{ $sub }})"
                                                    class="px-2.5 py-1 rounded-md text-xs font-medium border transition cursor-pointer
                                                    {{ $sub->is_active
                                                        ? 'bg-white/5 border-white/10 text-slate-300 hover:bg-primary/20 hover:text-white hover:border-primary/30'
                                                        : 'bg-red-500/10 border-red-500/20 text-red-400 hover:bg-red-500/20'
                                                    }}"
                                                    title="Click to Edit">
                                                    {{ $sub->name }}
                                                </button>
                                            @endforeach
                                        </div>
                                    @else
                                        <span class="text-xs text-mutedText/50 italic">No sub-categories</span>
                                    @endif
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4">
                                    @if($category->is_active)
                                        <span class="inline-flex items-center rounded-md bg-green-500/10 px-2.5 py-1 text-xs font-bold text-green-400 border border-green-500/20">Active</span>
                                    @else
                                        <span class="inline-flex items-center rounded-md bg-red-500/10 px-2.5 py-1 text-xs font-bold text-red-400 border border-red-500/20">Inactive</span>
                                    @endif
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-90">
                                        <button @click="openModal('edit', {{ $category }})" class="p-2 text-mutedText hover:text-primary hover:bg-primary/10 rounded-lg transition" title="Edit Parent">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center text-mutedText">No categories found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- 3B. MOBILE CARDS (Visible on Mobile) --}}
        <div class="md:hidden space-y-4 animate-[fadeIn_0.5s_ease-out]">
            @forelse($categories as $category)
                <div class="bg-[#1E293B] border border-white/10 rounded-2xl p-4 shadow-lg relative">
                    {{-- Header --}}
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-3">
                            <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-indigo-500/20 to-purple-500/20 flex items-center justify-center text-white font-bold border border-white/10 text-lg">
                                {{ substr($category->name, 0, 1) }}
                            </div>
                            <div>
                                <h3 class="text-white font-bold text-sm">{{ $category->name }}</h3>
                                <p class="text-[10px] text-mutedText">Parent Category</p>
                            </div>
                        </div>
                        <button @click="openModal('edit', {{ $category }})" class="p-2 text-mutedText hover:text-white bg-white/5 rounded-lg border border-white/10">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                        </button>
                    </div>

                    {{-- Status Badge --}}
                    <div class="mb-3">
                        @if($category->is_active)
                            <span class="inline-flex items-center rounded-md bg-green-500/10 px-2 py-0.5 text-[10px] font-bold text-green-400 border border-green-500/20">Active</span>
                        @else
                            <span class="inline-flex items-center rounded-md bg-red-500/10 px-2 py-0.5 text-[10px] font-bold text-red-400 border border-red-500/20">Inactive</span>
                        @endif
                    </div>

                    {{-- Sub Categories Grid --}}
                    <div class="bg-white/5 rounded-xl p-3 border border-white/5">
                        <p class="text-[10px] uppercase text-mutedText font-bold mb-2">Sub-Categories</p>
                        @if($category->subCategories->count() > 0)
                            <div class="flex flex-wrap gap-2">
                                @foreach($category->subCategories as $sub)
                                    <button @click.stop="openModal('edit', {{ $sub }})"
                                        class="px-2.5 py-1.5 rounded-lg text-xs font-medium border w-full text-left flex justify-between items-center
                                        {{ $sub->is_active
                                            ? 'bg-navy border-white/10 text-slate-300'
                                            : 'bg-red-500/10 border-red-500/20 text-red-400'
                                        }}">
                                        {{ $sub->name }}
                                        <svg class="w-3 h-3 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                                    </button>
                                @endforeach
                            </div>
                        @else
                            <span class="text-xs text-mutedText/50 italic">None</span>
                        @endif
                    </div>
                </div>
            @empty
                <div class="text-center text-mutedText py-10">No categories found.</div>
            @endforelse
        </div>

        {{-- Pagination --}}
        @if($categories->hasPages())
            <div class="mt-4 px-4 py-3 border border-white/5 bg-navy/40 rounded-xl flex items-center justify-between shadow-lg">
                <span class="text-xs text-mutedText">Page <span class="text-white font-bold">{{ $categories->currentPage() }}</span></span>
                <div class="flex gap-2">
                    @if($categories->onFirstPage())
                        <button disabled class="h-8 w-8 flex items-center justify-center rounded-lg border border-white/5 text-mutedText opacity-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></button>
                    @else
                        <a href="{{ $categories->previousPageUrl() }}" class="h-8 w-8 flex items-center justify-center rounded-lg border border-white/10 text-white hover:bg-white/5 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg></a>
                    @endif

                    @if($categories->hasMorePages())
                        <a href="{{ $categories->nextPageUrl() }}" class="h-8 w-8 flex items-center justify-center rounded-lg border border-white/10 text-white hover:bg-white/5 transition"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></a>
                    @else
                        <button disabled class="h-8 w-8 flex items-center justify-center rounded-lg border border-white/5 text-mutedText opacity-50"><svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg></button>
                    @endif
                </div>
            </div>
        @endif

        {{-- CREATE / EDIT MODAL --}}
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" x-show="showModal" x-transition.opacity></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div @click.away="showModal = false"
                    class="relative w-full max-w-lg rounded-2xl bg-[#1E293B] border border-white/10 shadow-2xl overflow-hidden transform transition-all"
                    x-show="showModal"
                    x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100">

                    <div class="bg-white/5 px-6 py-4 border-b border-white/5 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-white" x-text="isEdit ? 'Edit Details' : 'Create Category'"></h3>
                            <p class="text-xs text-mutedText">Manage category info.</p>
                        </div>
                        <button @click="showModal = false" class="text-mutedText hover:text-white bg-white/5 rounded-full p-1 hover:bg-white/10 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="p-6">
                        <div class="space-y-5">
                            {{-- Name --}}
                            <div>
                                <label class="block text-xs font-bold text-mutedText uppercase mb-1">Category Name <span class="text-secondary">*</span></label>
                                <input type="text" x-model="form.name" required
                                    class="w-full rounded-lg bg-navy border border-white/10 px-3 py-2.5 text-sm text-white focus:border-primary focus:ring-1 focus:ring-primary outline-none transition placeholder-white/20">
                            </div>

                            {{-- Parent Dropdown --}}
                            <div>
                                <label class="block text-xs font-bold text-mutedText uppercase mb-1">Parent Category</label>
                                <select x-model="form.parent_id"
                                    class="w-full rounded-lg bg-navy border border-white/10 px-3 py-2.5 text-sm text-white focus:border-primary outline-none transition">
                                    <option value="">None (Main Category)</option>
                                    @foreach($allCategories as $cat)
                                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                    @endforeach
                                </select>
                                <p class="text-[10px] text-mutedText mt-1">Select a parent to make this a sub-category.</p>
                            </div>

                            {{-- Status Toggle --}}
                            <div class="flex items-center gap-3 border-t border-white/5 pt-4">
                                <label class="flex items-center cursor-pointer relative">
                                    <input type="checkbox" x-model="form.is_active" class="sr-only peer">
                                    <div class="w-11 h-6 bg-white/10 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-primary"></div>
                                    <span class="ml-3 text-sm font-medium text-white">Active Status</span>
                                </label>
                            </div>
                        </div>

                        {{-- Modal Footer with Delete Button (Left) and Actions (Right) --}}
                        <div class="mt-8 flex justify-between items-center pt-4 border-t border-white/10">

                            {{-- Delete Button (Only Show on Edit) --}}
                            <div>
                                <button type="button" x-show="isEdit" @click="deleteCategory(form.id)"
                                    class="text-red-400 hover:text-red-300 text-xs font-bold uppercase tracking-wider flex items-center gap-1 transition">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                    Delete
                                </button>
                            </div>

                            <div class="flex gap-3">
                                <button type="button" @click="showModal = false" class="px-5 py-2.5 text-sm font-medium text-mutedText bg-white/5 border border-white/10 rounded-lg hover:bg-white/10 transition">Cancel</button>
                                <button type="submit" :disabled="isSubmitting"
                                    class="px-5 py-2.5 text-sm font-bold text-white bg-primary rounded-lg hover:bg-indigo-600 disabled:opacity-70 transition flex items-center shadow-lg shadow-primary/25">
                                    <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                    <span x-text="isSubmitting ? 'Saving...' : (isEdit ? 'Update' : 'Create')"></span>
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
                form: { id: null, name: '', parent_id: '', is_active: true },

                init() {
                    this.Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true, background: '#1E293B', color: '#fff' });
                },

                openModal(mode, data = null) {
                    this.isEdit = (mode === 'edit');
                    this.showModal = true;

                    if (this.isEdit && data) {
                        this.form = {
                            id: data.id,
                            name: data.name,
                            parent_id: data.parent_id || '',
                            is_active: Boolean(data.is_active)
                        };
                    } else {
                        this.resetForm();
                    }
                },

                resetForm() {
                    this.form = { id: null, name: '', parent_id: '', is_active: true };
                },

                async submitForm() {
                    this.isSubmitting = true;
                    let url = this.isEdit
                        ? `{{ url('admin/categories/update') }}/${this.form.id}`
                        : `{{ route('admin.categories.store') }}`;

                    let method = this.isEdit ? 'PUT' : 'POST';

                    try {
                        let response = await fetch(url, {
                            method: method,
                            headers: { "Content-Type": "application/json", "X-CSRF-TOKEN": "{{ csrf_token() }}", "Accept": "application/json" },
                            body: JSON.stringify(this.form)
                        });

                        let result = await response.json();

                        if (!response.ok) {
                            let errorMsg = result.message || "Something went wrong";
                            if(result.errors) errorMsg = Object.values(result.errors).flat().join('<br>');
                            throw new Error(errorMsg);
                        }

                        this.showModal = false;
                        this.Toast.fire({ icon: 'success', title: result.message });
                        setTimeout(() => location.reload(), 1000);

                    } catch (error) {
                        Swal.fire({ title: 'Error', html: error.message, icon: 'error', background: '#1E293B', color: '#fff' });
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                deleteCategory(id) {
                    // Safety check: close modal if deleting from inside modal
                    this.showModal = false;

                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This action cannot be undone!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        confirmButtonText: 'Yes, delete it!',
                        background: '#1E293B',
                        color: '#fff'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            try {
                                let response = await fetch(`{{ url('admin/categories/delete') }}/${id}`, {
                                    method: 'DELETE',
                                    headers: { "X-CSRF-TOKEN": "{{ csrf_token() }}" }
                                });
                                let res = await response.json();
                                if(res.status) {
                                    this.Toast.fire({ icon: 'success', title: res.message });
                                    setTimeout(() => location.reload(), 1000);
                                } else {
                                    throw new Error(res.message);
                                }
                            } catch(e) {
                                Swal.fire({ title: 'Error', text: e.message, icon: 'error', background: '#1E293B', color: '#fff' });
                            }
                        }
                    });
                }
            }
        }
    </script>
@endsection
