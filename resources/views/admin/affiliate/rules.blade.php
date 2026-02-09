@extends('layouts.admin')

@section('title', 'Commission Rules')

@section('content')
<div class="space-y-8 font-sans text-mainText">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-mainText">Commission Rules</h1>
            <p class="text-mutedText mt-1 text-sm">Configure global, product-specific, or user-specific commission rates.</p>
        </div>
    </div>

    {{-- Alerts (Strictly Branded) --}}
    @if (session('success'))
        <div class="bg-primary/10 border border-primary/20 text-primary px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="bg-secondary/10 border border-secondary/20 text-secondary px-4 py-3 rounded-xl shadow-sm">
            <div class="flex items-center gap-2 mb-2 font-bold">
                 <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                 Please fix the following errors:
            </div>
            <ul class="list-disc list-inside text-sm font-medium ml-2">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left Column: Create Rule Form --}}
        <div class="lg:col-span-1">
            <div class="bg-surface rounded-2xl shadow-sm border border-primary/10 sticky top-6">
                <div class="p-6 border-b border-primary/5 bg-navy/30">
                    <h3 class="text-lg font-bold text-mainText flex items-center gap-2">
                        <svg class="w-5 h-5 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Add New Rule
                    </h3>
                </div>

                <form action="{{ route('admin.affiliate.rules.store') }}" method="POST" class="p-6 space-y-5">
                    @csrf

                    {{-- Affiliate ID --}}
                    <div>
                        <label class="block text-xs font-bold uppercase text-mutedText tracking-wider mb-2">Affiliate (User ID)</label>
                        <div class="relative">
                            {{-- Using customWhite background and primary border on focus --}}
                            <input type="number" name="affiliate_id" placeholder="Leave empty for Global"
                                class="w-full bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-primary/20 text-mainText placeholder-mutedText/50 shadow-sm transition-all">
                            <div class="absolute inset-y-0 right-3 flex items-center pointer-events-none text-mutedText/50">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            </div>
                        </div>
                        <p class="text-[10px] text-mutedText mt-1 ml-1">Optional: Apply rule to a specific user only.</p>
                    </div>

                    {{-- Product Selection --}}
                    <div class="space-y-4 p-4 bg-navy/30 rounded-xl border border-primary/5">
                        <div>
                            <label class="block text-xs font-bold uppercase text-mutedText tracking-wider mb-2">Product Scope</label>
                            <select name="product_type" class="w-full bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-primary/20 text-mainText shadow-sm transition-all" onchange="toggleProductSelect(this.value)">
                                <option value="">All Products (Global)</option>
                                <option value="App\Models\Course">Specific Course</option>
                                <option value="App\Models\Bundle">Specific Bundle</option>
                            </select>
                        </div>

                        <div id="product_id_container" class="hidden animate-fade-in-down">
                            <label class="block text-xs font-bold uppercase text-mutedText tracking-wider mb-2">Product ID</label>
                            <input type="number" name="product_id" placeholder="Enter ID e.g. 45" class="w-full bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-primary/20 text-mainText shadow-sm transition-all">
                        </div>
                    </div>

                    {{-- Commission Settings --}}
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-bold uppercase text-mutedText tracking-wider mb-2">Type</label>
                            <select name="commission_type" class="w-full bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-primary/20 text-mainText shadow-sm transition-all">
                                <option value="percent">Percent (%)</option>
                                <option value="fixed">Fixed (₹)</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-bold uppercase text-mutedText tracking-wider mb-2">Amount</label>
                            <input type="number" step="0.01" name="amount" required placeholder="0.00" class="w-full bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-primary/20 text-mainText font-bold shadow-sm transition-all">
                        </div>
                    </div>

                    {{-- Status Toggle (Using only branded colors) --}}
                    <div class="flex items-center gap-3 pt-2 p-4 bg-navy/30 rounded-xl border border-primary/5">
                        <label class="relative inline-flex items-center cursor-pointer">
                            <input type="checkbox" name="is_active" value="1" checked class="sr-only peer">
                            {{-- Toggle Background: mutedText when off, primary when on --}}
                            <div class="w-11 h-6 bg-mutedText/30 peer-focus:outline-none rounded-full peer
                                        peer-checked:after:translate-x-full peer-checked:after:border-customWhite
                                        after:content-[''] after:absolute after:top-[2px] after:left-[2px]
                                        after:bg-customWhite after:border-gray-300 after:rounded-full after:h-5 after:w-5 after:transition-all
                                        peer-checked:bg-primary"></div>
                            <span class="ml-3 text-sm font-bold text-mainText">Active Rule</span>
                        </label>
                    </div>

                    <button type="submit" class="w-full py-3 bg-primary hover:bg-secondary text-customWhite rounded-xl shadow-lg shadow-primary/20 transition-all font-bold flex justify-center items-center gap-2 text-sm uppercase tracking-wider">
                        <span>Save Rule</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path></svg>
                    </button>
                </form>
            </div>
        </div>

        {{-- Right Column: Existing Rules List --}}
        <div class="lg:col-span-2">
            <div class="bg-surface rounded-2xl shadow-sm border border-primary/10 overflow-hidden">
                <div class="p-6 border-b border-primary/5 bg-navy/30">
                    <h3 class="text-lg font-bold text-mainText">Active Rules Configuration</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-primary/5 text-xs uppercase text-primary font-bold tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Scope</th>
                                <th class="px-6 py-4">Applied To</th>
                                <th class="px-6 py-4">Value</th>
                                <th class="px-6 py-4">Status</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-primary/5">
                            @forelse ($rules as $rule)
                                <tr class="hover:bg-navy/50 transition-colors">
                                    <td class="px-6 py-4">
                                        @if ($rule->affiliate_id)
                                            {{-- User Specific: Secondary Color --}}
                                            <div class="flex items-center gap-2">
                                                <div class="p-1.5 rounded-lg bg-secondary/10 text-secondary">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                                </div>
                                                <div>
                                                    <span class="block text-xs font-bold text-mainText">User Specific</span>
                                                    <span class="text-[10px] text-mutedText font-medium">ID: {{ $rule->affiliate_id }}</span>
                                                </div>
                                            </div>
                                        @else
                                            {{-- Global: Primary Color --}}
                                            <div class="flex items-center gap-2">
                                                <div class="p-1.5 rounded-lg bg-primary/10 text-primary">
                                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.055 11H5a2 2 0 012 2v1a2 2 0 002 2 2 2 0 012 2v2.945M8 3.935V5.5A2.5 2.5 0 0010.5 8h.5a2 2 0 012 2 2 2 0 104 0 2 2 0 012-2h1.064M15 20.488V18a2 2 0 012-2h3.064M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </div>
                                                <span class="text-xs font-bold text-mainText">Global Scope</span>
                                            </div>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($rule->product_type)
                                            <div class="flex flex-col">
                                                <span class="text-[10px] font-bold uppercase tracking-wider text-mutedText">
                                                    {{ str_replace('App\\Models\\', '', $rule->product_type) }}
                                                </span>
                                                <span class="text-sm text-mainText font-bold line-clamp-1">
                                                    {{ $rule->product->title ?? 'ID: '.$rule->product_id }}
                                                </span>
                                            </div>
                                        @else
                                            <span class="text-sm text-mutedText font-medium italic flex items-center gap-1">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 10h16M4 14h16M4 18h16"></path></svg>
                                                All Products
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4">
                                        <span class="text-base font-black text-primary">
                                            {{ number_format($rule->amount, 2) }}
                                            <span class="text-xs text-mainText font-bold">{{ $rule->commission_type == 'percent' ? '%' : 'INR' }}</span>
                                        </span>
                                    </td>

                                    <td class="px-6 py-4">
                                        @if ($rule->is_active)
                                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-primary bg-primary/5 px-2.5 py-1 rounded-full border border-primary/10">
                                                <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path></svg>
                                                Active
                                            </span>
                                        @else
                                            <span class="inline-flex items-center gap-1.5 text-xs font-bold text-mutedText bg-mutedText/10 px-2.5 py-1 rounded-full border border-mutedText/20">
                                                 <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path></svg>
                                                Inactive
                                            </span>
                                        @endif
                                    </td>

                                    <td class="px-6 py-4 text-right">
                                        <form action="{{ route('admin.affiliate.rules.delete', $rule->id) }}" method="POST" onsubmit="return confirm('Delete this rule permanently?');">
                                            @csrf
                                            @method('DELETE')
                                            {{-- Use secondary color for destructive action hover --}}
                                            <button type="submit" class="p-2 text-mutedText hover:text-secondary hover:bg-secondary/10 rounded-lg transition-all group" title="Delete Rule">
                                                <svg class="w-5 h-5 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-16 h-16 bg-navy rounded-full flex items-center justify-center mb-4 border border-primary/10">
                                                <svg class="w-8 h-8 text-primary/50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                            </div>
                                            <h3 class="text-lg font-bold text-mainText">No Custom Rules</h3>
                                            <p class="text-sm text-mutedText">The system is currently using default settings.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($rules->hasPages())
                    <div class="p-4 border-t border-primary/5 bg-navy/30">
                        {{ $rules->links() }}
                    </div>
                @endif
            </div>
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
