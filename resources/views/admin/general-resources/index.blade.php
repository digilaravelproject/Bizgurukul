@extends('layouts.admin')

@section('content')
<div class="space-y-6" x-data="{ addModal: false, editModal: false, currentResource: {} }">

    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h2 class="text-2xl font-black text-mainText uppercase tracking-widest"><i class="fas fa-link text-primary"></i> All Resources</h2>
            <p class="text-xs text-mutedText font-semibold mt-1">Manage individual resource links for students.</p>
        </div>

        <div class="flex items-center gap-3">
             <form action="{{ route('admin.general-resources.index') }}" method="GET" class="flex items-center gap-2 bg-surface px-4 py-2 rounded-2xl border border-primary/10 shadow-sm">
                <span class="text-[10px] font-black uppercase tracking-widest text-mutedText">Filter</span>
                <select name="category_id" onchange="this.form.submit()" class="bg-transparent text-sm font-black text-primary outline-none cursor-pointer">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                    @endforeach
                </select>
            </form>

            <button @click="addModal = true" class="flex items-center gap-2 bg-primary hover:bg-primary/90 text-white px-6 py-3 rounded-2xl font-black uppercase tracking-widest text-xs transition-all active:scale-95 shadow-lg shadow-primary/20">
                <i class="fas fa-plus"></i> Add Resource
            </button>
        </div>
    </div>

    <div class="bg-surface rounded-3xl border border-primary/10 shadow-xl shadow-primary/5 overflow-hidden">
        <div class="overflow-x-auto w-full">
            <table class="w-full text-left min-w-[800px]">
                <thead class="bg-navy/50 text-[10px] uppercase text-mutedText font-black tracking-widest border-b border-primary/10">
                    <tr>
                        <th class="px-6 py-4">Order / Title</th>
                        <th class="px-6 py-4">Category</th>
                        <th class="px-6 py-4">Link Details</th>
                        <th class="px-6 py-4 text-center">Status</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary/5 text-sm font-semibold text-mainText">
                    @forelse($resources as $resource)
                    <tr class="hover:bg-primary/5 transition-colors">
                        <td class="px-6 py-5 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-10 h-10 rounded-xl bg-primary/10 flex items-center justify-center text-primary font-black shadow-sm">
                                    <i class="fas {{ $resource->icon }}"></i>
                                </div>
                                <div>
                                    <span class="block font-bold text-mainText">{{ $resource->title }}</span>
                                    <span class="text-[10px] text-mutedText uppercase tracking-widest">Order: {{ $resource->order_column }}</span>
                                </div>
                            </div>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <span class="bg-navy px-3 py-1 rounded-full text-[10px] font-black uppercase text-primary border border-primary/5">
                                {{ $resource->category->name }}
                            </span>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap">
                            <a href="{{ $resource->link_url }}" target="_blank" class="text-blue-500 hover:text-blue-400 font-medium text-xs flex items-center gap-1 max-w-[200px] truncate">
                                <i class="fas fa-external-link-alt text-[10px]"></i> {{ $resource->link_url }}
                            </a>
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-center">
                            @if($resource->status)
                                <span class="bg-emerald-500/10 text-emerald-500 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest">Active</span>
                            @else
                                <span class="bg-red-500/10 text-red-500 px-3 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-widest">Inactive</span>
                            @endif
                        </td>
                        <td class="px-6 py-5 whitespace-nowrap text-right">
                            <div class="flex items-center justify-end gap-2">
                                <button @click="currentResource = {{ json_encode($resource) }}; editModal = true" class="w-8 h-8 rounded-full bg-amber-500/10 text-amber-500 hover:bg-amber-500 hover:text-white transition flex items-center justify-center shadow-sm">
                                    <i class="fas fa-edit text-xs"></i>
                                </button>
                                <form action="{{ route('admin.general-resources.destroy', $resource->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this resource?')">
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
                            <i class="fas fa-link text-4xl text-mutedText/30 mb-4"></i>
                            <h3 class="text-xs font-black text-mutedText uppercase tracking-widest">No resources found.</h3>
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
            <div x-show="addModal" x-transition.scale class="bg-surface rounded-3xl border border-primary/20 shadow-2xl p-8 w-full max-w-lg relative z-10">
                <h3 class="text-xl font-black text-mainText uppercase tracking-widest mb-6">Add New Resource</h3>
                <form action="{{ route('admin.general-resources.store') }}" method="POST" class="space-y-5">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-mutedText uppercase tracking-widest mb-2">Category</label>
                            <select name="category_id" required class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none">
                                <option value="">Select Category</option>
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-mutedText uppercase tracking-widest mb-2">Resource Title</label>
                            <input type="text" name="title" required placeholder="e.g. Marketing PPT" class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-mutedText uppercase tracking-widest mb-2">Link URL</label>
                            <input type="url" name="link_url" required placeholder="https://drive.google.com/..." class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-mutedText uppercase tracking-widest mb-2">Icon Class (FontAwesome)</label>
                            <input type="text" name="icon" value="fa-link" placeholder="fa-file-pdf" class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-mutedText uppercase tracking-widest mb-2">Order Column</label>
                            <input type="number" name="order_column" value="0" class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none">
                        </div>
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
                        <button type="submit" class="flex-1 py-3.5 bg-primary text-white rounded-xl font-black uppercase tracking-widest text-xs shadow-lg shadow-primary/20 active:scale-95 transition-all">Save Resource</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    {{-- Edit Modal --}}
    <div x-show="editModal" class="fixed inset-0 z-[100] overflow-y-auto" style="display: none;">
        <div x-show="editModal" x-transition.opacity class="fixed inset-0 bg-navy/80 backdrop-blur-sm" @click="editModal = false"></div>
        <div class="flex items-center justify-center min-h-screen p-4">
            <div x-show="editModal" x-transition.scale class="bg-surface rounded-3xl border border-primary/20 shadow-2xl p-8 w-full max-w-lg relative z-10">
                <h3 class="text-xl font-black text-mainText uppercase tracking-widest mb-6">Edit Resource</h3>
                <form :action="'{{ url('admin/general-resources') }}/' + currentResource.id" method="POST" class="space-y-5">
                    @csrf
                    @method('PUT')
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-mutedText uppercase tracking-widest mb-2">Category</label>
                            <select name="category_id" x-model="currentResource.category_id" required class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none">
                                @foreach($categories as $cat)
                                    <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-mutedText uppercase tracking-widest mb-2">Resource Title</label>
                            <input type="text" name="title" x-model="currentResource.title" required class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none">
                        </div>
                        <div class="md:col-span-2">
                            <label class="block text-[10px] font-bold text-mutedText uppercase tracking-widest mb-2">Link URL</label>
                            <input type="url" name="link_url" x-model="currentResource.link_url" required class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-mutedText uppercase tracking-widest mb-2">Icon Class</label>
                            <input type="text" name="icon" x-model="currentResource.icon" class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none">
                        </div>
                        <div>
                            <label class="block text-[10px] font-bold text-mutedText uppercase tracking-widest mb-2">Order Column</label>
                            <input type="number" name="order_column" x-model="currentResource.order_column" class="w-full bg-navy border border-primary/10 rounded-xl px-4 py-3 text-sm font-bold text-mainText focus:border-primary outline-none">
                        </div>
                    </div>
                    <div class="flex items-center gap-3">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="status" value="1" :checked="currentResource.status" class="sr-only peer">
                            <div class="w-11 h-6 bg-navy peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-gray-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-emerald-500"></div>
                        </label>
                        <span class="text-[10px] font-bold text-mutedText uppercase tracking-widest">Active Status</span>
                    </div>
                    <div class="flex gap-3 pt-2">
                        <button type="button" @click="editModal = false" class="flex-1 py-3.5 bg-navy text-mutedText rounded-xl font-black uppercase tracking-widest text-xs border border-primary/10 hover:bg-navy/80 transition-all">Cancel</button>
                        <button type="submit" class="flex-1 py-3.5 bg-amber-500 text-white rounded-xl font-black uppercase tracking-widest text-xs shadow-lg shadow-amber-500/20 active:scale-95 transition-all">Update Resource</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
