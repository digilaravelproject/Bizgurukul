@extends('layouts.admin')

@section('content')
<div class="mb-6 flex flex-col md:flex-row justify-between items-start md:items-center gap-4">
    <div>
        <h2 class="text-3xl font-black text-mainText tracking-tight">Tax Management</h2>
        <p class="text-sm text-mutedText font-medium mt-1">Configure global taxes applied during checkout.</p>
    </div>
    <a href="{{ route('admin.taxes.create') }}" class="bg-primary text-white hover:bg-primary/90 px-6 py-2.5 rounded-xl font-bold transition-all shadow-lg flex items-center gap-2">
        <i class="fas fa-plus"></i> Add New Tax
    </a>
</div>

<div class="bg-surface rounded-2xl border border-primary/10 shadow-sm overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-navy/50 text-xs uppercase tracking-widest text-mutedText border-b border-primary/10">
                    <th class="p-4 font-bold">Tax Name</th>
                    <th class="p-4 font-bold">Type</th>
                    <th class="p-4 font-bold">Value</th>
                    <th class="p-4 font-bold">Status</th>
                    <th class="p-4 font-bold text-right">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-primary/5 text-sm">
                @forelse($taxes as $tax)
                    <tr class="hover:bg-primary/5 transition-colors">
                        <td class="p-4 font-bold text-mainText">{{ $tax->name }}</td>
                        <td class="p-4">
                            <span class="px-2 py-1 bg-navy text-primary rounded-md text-xs font-bold uppercase tracking-wider">
                                {{ $tax->type }}
                            </span>
                        </td>
                        <td class="p-4 font-black text-mainText">
                            @if($tax->type == 'percentage')
                                {{ rtrim(rtrim((string)$tax->value, '0'), '.') }}%
                            @else
                                ₹{{ number_format($tax->value) }}
                            @endif
                        </td>
                        <td class="p-4">
                            @if($tax->is_active)
                                <span class="px-2 py-1 bg-green-500/10 text-green-600 rounded-md text-xs font-bold uppercase tracking-wider">Active</span>
                            @else
                                <span class="px-2 py-1 bg-red-500/10 text-red-600 rounded-md text-xs font-bold uppercase tracking-wider">Inactive</span>
                            @endif
                        </td>
                        <td class="p-4 text-right">
                            <div class="flex items-center justify-end gap-3">
                                <a href="{{ route('admin.taxes.edit', $tax->id) }}" class="text-secondary hover:text-secondary/80 transition-colors p-2" title="Edit">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <form action="{{ route('admin.taxes.destroy', $tax->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this tax?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-400 p-2 transition-colors" title="Delete">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="p-8 text-center text-mutedText font-semibold">
                            No taxes configured yet. Let's create one.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($taxes->hasPages())
        <div class="p-4 border-t border-primary/10">
            {{ $taxes->links() }}
        </div>
    @endif
</div>
@endsection
