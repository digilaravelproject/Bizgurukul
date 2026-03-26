@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ addModal: false, editModal: false, currentCategory: {} }">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-mainText uppercase tracking-widest"><i class="fas fa-tags text-primary"></i> Resource Categories</h2>
            <p class="text-xs text-mutedText font-semibold mt-1">Manage categories for organizing student resources.</p>
        </div>

        <button @click="addModal = true" class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-6 py-3 rounded-2xl font-black uppercase tracking-widest text-xs transition-all active:scale-95 shadow-lg shadow-primary/20">
            <i class="fas fa-plus"></i> Add Category
        </button>
    </div>

    <div class="bg-surface rounded-3xl border border-primary/10 shadow-xl shadow-primary/5 overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left min-w-[700px]">
                <thead class="bg-navy/50 text-[10px] uppercase text-mutedText font-black tracking-widest border-b border-primary/10">
                    <tr>
                        <th class="px-6 py-4">Order / ID</th>
                        <th class="px-6 py-4">Category Name</th>
                        <th class="px-6 py-4 text-center">Resources</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary/5 text-sm font-semibold text-mainText">
                    @forelse($categories as $category)
                    <tr class="hover:bg-primary/5 transition-colors">
                        <td class="px-6 py-5 whitespace-nowrap">
                            <span class="block font-black text-primary">#{{ $category->order_column }}</span>
                            <span class="text-[10px] text-mutedText uppercase">ID: {{ $category->id }}</span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <span class="font-bold text-mainText">{{ $category->name }}</span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-center">
                            <span class="bg-primary/10 text-primary px-3 py-1 rounded-full text-xs font-black">
                                {{ $category->resources_count }} Resources
                            </span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-center">
                            @if($category->status)
                                <span class="bg-emerald-500/10 text-emerald-500 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest">Active</span>
                            @else
                                <span class="bg-red-500/10 text-red-500 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <a href="{{ route('admin.general-resources.index', ['category_id' => $category->id]) }}" class="w-8 h-8 rounded-full bg-blue-500/10 text-blue-500 hover:bg-blue-500 hover:text-white transition flex items-center justify-center shadow-sm" title="View Resources">
                                    <i class="fas fa-eye text-xs"></i>
                                </a>
                                <button @click="currentCategory = {{ json_encode($category) }}; editModal = true" class="w-8 h-8 rounded-full bg-amber-500/10 text-amber-500 hover:bg-amber-500 hover:text-white transition flex items-center justify-center shadow-sm">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <form action="{{ route('admin.resource-categories.destroy', $category->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this category? All resources in it will also be deleted.')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="w-8 h-8 rounded-full bg-red-500/10 text-red-500 hover:bg-red-500 hover:text-white transition flex items-center justify-center shadow-sm">
                                        <i class="fas fa-trash text-xs"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="px-6 py-16 text-center">
                            <i class="fas fa-folder-open text-4xl text-mutedText/30 mb-4"></i>
                            <h3 class="text-xs font-black text-mutedText uppercase tracking-widest">No categories found.</h3>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Add Modal --}}
    <div x-show="addModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
        <div x-show="addModal" x-transition.opacity class="fixed inset-0 bg-navy/80 backdrop-blur-sm" @click="addModal = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="addModal" x-transition.scale class="bg-surface rounded-3xl border border-primary/20 shadow-2xl p-8 w-full max-w-md relative z-10">
                <h3 class="text-xl font-black text-mainText uppercase tracking-widest mb-6">Add New Category</h3>
                <form action="{{ route('admin.resource-categories.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div>
                        <label class="block text-[10px] font-bold text-mutedText uppercase tracking-widest mb-2">Category Name</label>
                        <input type="text" name="name" required placeholder="e.g. Marketing Materials" class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary focus:ring-1 focus:ring-primary transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-mutedText uppercase tracking-widest mb-2">Order (Optional)</label>
                        <input type="number" name="order_column" value="0" class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary focus:ring-1 focus:ring-primary transition-all outline-none">
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="status" value="1" checked class="sr-only peer">
                            <div class="w-11 h-6 bg-navy peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                        <span class="text-[10px] font-bold text-mutedText uppercase tracking-widest">Active Status</span>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="addModal = false" class="flex-1 py-3.5 bg-navy text-mutedText rounded-xl font-black uppercase tracking-widest text-xs border border-primary/10 hover:bg-navy/80 transition-all">Cancel</button>
                        <button type="submit" class="flex-1 py-3.5 bg-primary text-white rounded-xl font-black uppercase tracking-widest text-xs shadow-lg shadow-primary/20 active:scale-95 transition-all">Save Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-show="editModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
        <div x-show="editModal" x-transition.opacity class="fixed inset-0 bg-navy/80 backdrop-blur-sm" @click="editModal = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="editModal" x-transition.scale class="bg-surface rounded-3xl border border-primary/20 shadow-2xl p-8 w-full max-w-md relative z-10">
                <h3 class="text-xl font-black text-mainText uppercase tracking-widest mb-6">Edit Category</h3>
                <form :action="'{{ url('admin/resource-categories') }}/' + currentCategory.id" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')
                    <div>
                        <label class="block text-[10px] font-bold text-mutedText uppercase tracking-widest mb-2">Category Name</label>
                        <input type="text" name="name" x-model="currentCategory.name" required class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary focus:ring-1 focus:ring-primary transition-all outline-none">
                    </div>
                    <div>
                        <label class="block text-[10px] font-bold text-mutedText uppercase tracking-widest mb-2">Order (Optional)</label>
                        <input type="number" name="order_column" x-model="currentCategory.order_column" class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary focus:ring-1 focus:ring-primary transition-all outline-none">
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="status" value="1" :checked="currentCategory.status" class="sr-only peer">
                            <div class="w-11 h-6 bg-navy peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                        <span class="text-[10px] font-bold text-mutedText uppercase tracking-widest">Active Status</span>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="editModal = false" class="flex-1 py-3.5 bg-navy text-mutedText rounded-xl font-black uppercase tracking-widest text-xs border border-primary/10 hover:bg-navy/80 transition-all">Cancel</button>
                        <button type="submit" class="flex-1 py-3.5 bg-amber-500 text-white rounded-xl font-black uppercase tracking-widest text-xs shadow-lg shadow-amber-500/20 active:scale-95 transition-all">Update Category</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
