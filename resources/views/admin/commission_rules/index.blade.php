@extends('layouts.admin')

@section('content')
    <div class="p-6" x-data="{
        showModal: false,
        productType: 'all',
        commissionType: 'percent'
    }">
        {{-- Header --}}
        <div class="flex justify-between items-center mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-800">Commission Rules</h1>
                <p class="text-sm text-slate-500">Manage affiliate commission structure</p>
            </div>
            <button @click="showModal = true" class="bg-indigo-600 hover:bg-indigo-700 text-white px-4 py-2 rounded-lg font-medium transition-colors flex items-center gap-2">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Add New Rule
            </button>
        </div>

        {{-- Rules Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 text-xs uppercase font-semibold text-slate-500">
                    <tr>
                        <th class="px-6 py-4">Prioritized Scope</th>
                        <th class="px-6 py-4">Affiliate</th>
                        <th class="px-6 py-4">Product Scope</th>
                        <th class="px-6 py-4">Commission</th>
                        <th class="px-6 py-4 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @forelse($rules as $rule)
                        <tr class="hover:bg-slate-50 transition-colors">
                            <td class="px-6 py-4">
                                @if($rule->affiliate_id && $rule->product_type)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-purple-100 text-purple-800">
                                        Highest (User + Product)
                                    </span>
                                @elseif($rule->affiliate_id)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        User Specific
                                    </span>
                                @elseif($rule->product_type)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        Product Specific
                                    </span>
                                @else
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                        Global Default
                                    </span>
                                @endif
                            </td>
                            <td class="px-6 py-4 font-medium text-slate-800">
                                {{ $rule->affiliate ? $rule->affiliate->name : 'All Affiliates' }}
                            </td>
                            <td class="px-6 py-4">
                                @if($rule->product_type)
                                    <span class="font-bold text-slate-700">
                                        {{ class_basename($rule->product_type) }}:
                                    </span>
                                    {{ $rule->product ? $rule->product->title : 'Unknown' }}
                                @else
                                    All Products
                                @endif
                            </td>
                            <td class="px-6 py-4 font-bold text-indigo-600">
                                {{ $rule->amount }}{{ $rule->commission_type === 'percent' ? '%' : ' INR' }}
                            </td>
                            <td class="px-6 py-4 text-right">
                                <form action="{{ route('admin.affiliate.rules.delete', $rule->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-red-500 hover:text-red-700 font-medium text-xs uppercase tracking-wide">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-6 py-12 text-center text-slate-400">
                                No specific rules found. System defaults will apply.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
            <div class="px-6 py-4 border-t border-slate-100">
                {{ $rules->links() }}
            </div>
        </div>

        {{-- Create Modal --}}
        <div x-show="showModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
            <div class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div class="relative transform overflow-hidden rounded-2xl bg-white p-6 shadow-xl transition-all w-full max-w-lg">
                    <h3 class="text-lg font-bold text-slate-900 mb-4">Create Commission Rule</h3>

                    <form action="{{ route('admin.affiliate.rules.store') }}" method="POST" class="space-y-4">
                        @csrf

                        {{-- Affiliate --}}
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Affiliate (Optional)</label>
                            <select name="affiliate_id" class="w-full rounded-lg border-slate-300 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                <option value="">Apply to All Affiliates (Global)</option>
                                @foreach($affiliates as $affiliate)
                                    <option value="{{ $affiliate->id }}">{{ $affiliate->name }} ({{ $affiliate->email }})</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-slate-500 mt-1">Leave empty to apply to everyone.</p>
                        </div>

                        {{-- Product Scope --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Scope</label>
                                <select name="product_type" x-model="productType" class="w-full rounded-lg border-slate-300 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="">All Products</option>
                                    <option value="course">Specific Course</option>
                                    <option value="bundle">Specific Bundle</option>
                                </select>
                            </div>

                            {{-- Product Select (Dynamic) --}}
                            <div x-show="productType !== 'all'" style="display: none;">
                                <label class="block text-sm font-medium text-slate-700 mb-1">Select Item</label>
                                <select name="product_id" class="w-full rounded-lg border-slate-300 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">

                                    <template x-if="productType === 'course'">
                                        <optgroup label="Courses">
                                            @foreach($courses as $course)
                                                <option value="{{ $course->id }}">{{ $course->title }}</option>
                                            @endforeach
                                        </optgroup>
                                    </template>

                                    <template x-if="productType === 'bundle'">
                                        <optgroup label="Bundles">
                                            @foreach($bundles as $bundle)
                                                <option value="{{ $bundle->id }}">{{ $bundle->title }}</option>
                                            @endforeach
                                        </optgroup>
                                    </template>

                                </select>
                            </div>
                        </div>

                        {{-- Commission --}}
                        <div class="grid grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Type</label>
                                <select name="commission_type" x-model="commissionType" class="w-full rounded-lg border-slate-300 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                                    <option value="percent">Percentage (%)</option>
                                    <option value="fixed">Fixed Amount (₹)</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Value</label>
                                <input type="number" name="amount" step="0.01" required placeholder="0.00"
                                    class="w-full rounded-lg border-slate-300 py-2.5 text-sm focus:ring-indigo-500 focus:border-indigo-500">
                            </div>
                        </div>

                        <div class="pt-4 flex justify-end gap-3">
                            <button type="button" @click="showModal = false" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800">Cancel</button>
                            <button type="submit" class="px-6 py-2 bg-indigo-600 hover:bg-indigo-700 text-white rounded-lg text-sm font-medium transition-colors">Save Rule</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
