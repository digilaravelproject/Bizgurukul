@extends('layouts.admin')

@section('content')
<div x-data="{ 
    search: '{{ request('search', '') }}', 
    perPage: '{{ request('per_page', 20) }}', 
    loading: false,
    updateTable() {
        let url = new URL(window.location.href);
        url.searchParams.set('search', this.search);
        url.searchParams.set('per_page', this.perPage);
        url.searchParams.set('page', 1);
        window.location.href = url.toString();
    },
    goToPage(url) {
        if (url) window.location.href = url;
    },
    resetFilters() {
        this.search = '';
        this.perPage = 20;
        this.updateTable();
    }
}">
    <div class="mb-8 flex justify-between items-center">
        <div>
            <h1 class="text-2xl font-extrabold text-mainText tracking-tight">Manage <span class="text-primary">{{ $title }}s</span></h1>
            <p class="text-[10px] text-mutedText uppercase tracking-[0.2em] font-bold">Configure {{ strtolower($title) }} master data</p>
        </div>
        <button onclick="document.getElementById('addModal').classList.remove('hidden')" 
                class="brand-gradient text-white px-6 py-2.5 rounded-xl font-bold text-xs uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-105 transition-transform">
            Add New {{ $title }}
        </button>
    </div>

    @if(session('success'))
        <div class="mb-6 px-4 py-3 rounded-xl bg-green-500/10 border border-green-500/20 text-green-600 text-xs font-bold uppercase tracking-wider flex items-center gap-3">
            <i class="fas fa-check-circle text-sm"></i>
            {{ session('success') }}
        </div>
    @endif

    {{-- Filter Bar --}}
    <x-admin.table.filter
        placeholder="Search {{ strtolower($title) }}s..."
        :show-date-filter="false"
        :show-export="false"
    />

    <div class="bg-customWhite rounded-2xl border border-primary/5 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-50/50 border-b border-gray-100">
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-mutedText">ID</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-mutedText">Name</th>
                        <th class="px-6 py-5 text-[10px] font-black uppercase tracking-widest text-mutedText text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-50">
                    @forelse($items as $item)
                    <tr class="hover:bg-gray-50/30 transition-colors text-sm">
                        <td class="px-6 py-4 text-mutedText font-bold">#{{ $item->id }}</td>
                        <td class="px-6 py-4 font-bold text-mainText">{{ $item->name }}</td>
                        <td class="px-6 py-4 text-right">
                            <div class="flex justify-end gap-2">
                                <button onclick="editItem({{ $item->id }}, '{{ addslashes($item->name) }}')" 
                                        class="p-2.5 text-primary hover:bg-primary/10 rounded-xl transition-all" title="Edit">
                                    <i class="fas fa-edit text-sm"></i>
                                </button>
                                
                                <form action="{{ route('admin.' . $routeName . '.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this {{ strtolower($title) }}?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="p-2.5 text-secondary hover:bg-secondary/10 rounded-xl transition-all" title="Delete">
                                        <i class="fas fa-trash text-sm"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="3" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center justify-center">
                                <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center mb-4 text-gray-300">
                                    <i class="fas fa-database text-2xl"></i>
                                </div>
                                <p class="text-mutedText font-bold text-xs uppercase tracking-widest">No {{ strtolower($title) }}s found</p>
                                <p class="text-[10px] text-mutedText/60 mt-1 uppercase tracking-widest font-bold">Add one to get started!</p>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination links --}}
        <x-admin.table.pagination :records="$items" />
    </div>

    <!-- Add Modal -->
    <div id="addModal" class="fixed inset-0 bg-navy/60 backdrop-blur-sm hidden flex items-center justify-center z-[9999] p-4">
        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl border border-gray-100 overflow-hidden transform transition-all">
            <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                <h2 class="text-sm font-black uppercase tracking-widest text-mainText">Add New {{ $title }}</h2>
                <button onclick="document.getElementById('addModal').classList.add('hidden')" class="w-8 h-8 flex items-center justify-center rounded-full bg-white shadow-sm text-mutedText hover:text-secondary transition-all">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <form action="{{ route('admin.' . $routeName . '.store') }}" method="POST" class="p-8">
                @csrf
                <div class="mb-8">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-3 ml-1">Name / Label</label>
                    <input type="text" name="name" required placeholder="Enter {{ strtolower($title) }} name..."
                           class="w-full bg-white border border-gray-200 rounded-2xl px-5 py-4 text-sm text-mainText focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all">
                </div>
                <div class="flex gap-4">
                    <button type="button" onclick="document.getElementById('addModal').classList.add('hidden')" 
                            class="flex-1 px-6 py-4 rounded-2xl font-bold text-[10px] uppercase tracking-widest text-mutedText bg-gray-50 hover:bg-gray-100 transition-all border border-gray-100">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 brand-gradient text-white px-6 py-4 rounded-2xl font-bold text-[10px] uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.02] transition-all">
                        Save {{ $title }}
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Edit Modal -->
    <div id="editModal" class="fixed inset-0 bg-navy/60 backdrop-blur-sm hidden flex items-center justify-center z-[9999] p-4">
        <div class="bg-white rounded-3xl w-full max-w-md shadow-2xl border border-gray-100 overflow-hidden transform transition-all">
            <div class="px-8 py-6 border-b border-gray-50 flex justify-between items-center bg-gray-50/50">
                <h2 class="text-sm font-black uppercase tracking-widest text-mainText">Edit {{ $title }}</h2>
                <button onclick="document.getElementById('editModal').classList.add('hidden')" class="w-8 h-8 flex items-center justify-center rounded-full bg-white shadow-sm text-mutedText hover:text-secondary transition-all">
                    <i class="fas fa-times text-xs"></i>
                </button>
            </div>
            <form id="editForm" method="POST" class="p-8">
                @csrf
                @method('PUT')
                <div class="mb-8">
                    <label class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-3 ml-1">Name / Label</label>
                    <input type="text" name="name" id="editName" required 
                           class="w-full bg-white border border-gray-200 rounded-2xl px-5 py-4 text-sm text-mainText focus:outline-none focus:border-primary focus:ring-4 focus:ring-primary/5 transition-all">
                </div>
                <div class="flex gap-4">
                    <button type="button" onclick="document.getElementById('editModal').classList.add('hidden')" 
                            class="flex-1 px-6 py-4 rounded-2xl font-bold text-[10px] uppercase tracking-widest text-mutedText bg-gray-50 hover:bg-gray-100 transition-all border border-gray-100">
                        Cancel
                    </button>
                    <button type="submit" class="flex-1 brand-gradient text-white px-6 py-4 rounded-2xl font-bold text-[10px] uppercase tracking-widest shadow-lg shadow-primary/20 hover:scale-[1.02] transition-all">
                        Update {{ $title }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    function editItem(id, name) {
        const form = document.getElementById('editForm');
        form.action = "{{ url('admin') }}/" + "{{ $routeName }}" + "/" + id;
        document.getElementById('editName').value = name;
        document.getElementById('editModal').classList.remove('hidden');
    }
</script>
@endsection
