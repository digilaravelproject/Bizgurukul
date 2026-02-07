@extends('layouts.admin')

@section('title', 'Affiliate Commission Rules')

@section('header')
    <div class="flex justify-between items-center w-full">
        <h2 class="font-bold text-xl text-slate-800 leading-tight">
            {{ __('Affiliate Commission Rules') }}
        </h2>
    </div>
@endsection

@section('content')
    <div class="max-w-7xl mx-auto space-y-6">

        {{-- Success Message --}}
        @if (session('success'))
            <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-xl relative" role="alert">
                <span class="block sm:inline">{{ session('success') }}</span>
            </div>
        @endif

        @if ($errors->any())
            <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-xl relative">
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Create Rule Form --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200">
            <div class="p-6 border-b border-slate-100 bg-slate-50/50">
                <h3 class="text-lg font-bold text-slate-800">Add New Commission Rule</h3>
                <p class="text-slate-500 text-sm">Create global, user-specific, or product-specific commission rules.</p>
            </div>
            <div class="p-6">
                <form action="{{ route('admin.affiliate.rules.store') }}" method="POST">
                    @csrf
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">

                        {{-- Affiliate ID (Optional) --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Affiliate (User ID)</label>
                            <input type="number" name="affiliate_id" placeholder="Leave empty for Global" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                            <p class="text-xs text-slate-400 mt-1">If set, rule applies only to this user.</p>
                        </div>

                         {{-- Product Type --}}
                         <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Product Type</label>
                            <select name="product_type" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm" onchange="toggleProductSelect(this.value)">
                                <option value="">All Products (Global)</option>
                                <option value="App\Models\Course">Specific Course</option>
                                <option value="App\Models\Bundle">Specific Bundle</option>
                            </select>
                        </div>

                        {{-- Product ID --}}
                        <div id="product_id_container" class="hidden">
                            <label class="block text-sm font-medium text-slate-700 mb-1">Product ID</label>
                            <input type="number" name="product_id" placeholder="Course/Bundle ID" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                        </div>

                        {{-- Commission Type --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Commission Type</label>
                            <select name="commission_type" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                                <option value="percent">Percentage (%)</option>
                                <option value="fixed">Fixed Amount (₹)</option>
                            </select>
                        </div>

                        {{-- Amount --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Amount</label>
                            <input type="number" step="0.01" name="amount" required placeholder="e.g. 10 or 500" class="w-full rounded-xl border-slate-200 focus:border-indigo-500 focus:ring-indigo-500 shadow-sm text-sm">
                        </div>

                         {{-- Active Status --}}
                         <div class="flex items-center mt-6">
                            <input type="checkbox" name="is_active" value="1" checked class="rounded border-slate-300 text-indigo-600 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50">
                            <span class="ml-2 text-sm text-slate-600">Active</span>
                        </div>

                    </div>

                    <div class="mt-6 flex justify-end">
                        <button type="submit" class="bg-indigo-600 hover:bg-indigo-700 text-white font-bold py-2 px-6 rounded-xl shadow-lg shadow-indigo-200 transition-all">
                            Save Rule
                        </button>
                    </div>
                </form>
            </div>
        </div>

        {{-- Rules List --}}
        <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200">
            <div class="p-6 border-b border-slate-100">
                <h3 class="text-lg font-bold text-slate-800">Existing Rules</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead class="bg-slate-50 text-xs uppercase text-slate-500 font-bold border-b border-slate-100">
                        <tr>
                            <th class="px-6 py-4">Scope</th>
                            <th class="px-6 py-4">Applied To</th>
                            <th class="px-6 py-4">Commission</th>
                            <th class="px-6 py-4">Status</th>
                            <th class="px-6 py-4">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        @forelse ($rules as $rule)
                            <tr class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    @if ($rule->affiliate_id)
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                            User: {{ $rule->affiliate->name ?? 'ID: '.$rule->affiliate_id }}
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                            Global (All Users)
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if ($rule->product_type)
                                        <span class="font-medium text-slate-800">
                                            {{ class_basename($rule->product_type) }}:
                                            {{ $rule->product->title ?? 'ID: '.$rule->product_id }}
                                        </span>
                                    @else
                                        <span class="text-slate-500 italic">All Products</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 font-bold text-slate-800">
                                    {{ number_format($rule->amount, 2) }}
                                    {{ $rule->commission_type == 'percent' ? '%' : '₹' }}
                                </td>
                                <td class="px-6 py-4">
                                    @if ($rule->is_active)
                                        <span class="text-green-600 flex items-center text-xs font-bold uppercase">
                                            <span class="w-2 h-2 bg-green-500 rounded-full mr-2"></span> Active
                                        </span>
                                    @else
                                        <span class="text-red-600 flex items-center text-xs font-bold uppercase">
                                            <span class="w-2 h-2 bg-red-500 rounded-full mr-2"></span> Inactive
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <form action="{{ route('admin.affiliate.rules.delete', $rule->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="text-red-500 hover:text-red-700 font-medium">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-6 py-8 text-center text-slate-400">
                                    No rules found. Default system settings apply.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
             <div class="p-4 border-t border-slate-100">
                {{ $rules->links() }}
            </div>
        </div>

    </div>

    <script>
        function toggleProductSelect(val) {
            const container = document.getElementById('product_id_container');
            if (val) {
                container.classList.remove('hidden');
            } else {
                container.classList.add('hidden');
            }
        }
    </script>
@endsection
