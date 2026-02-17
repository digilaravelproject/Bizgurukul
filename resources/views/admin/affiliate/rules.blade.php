@extends('layouts.admin')

@section('title', 'Affiliate Rules & Permissions')

@section('content')
<div class="space-y-8 font-sans text-mainText">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-mainText">Affiliate Rules & Permissions</h1>
            <p class="text-mutedText mt-1 text-sm">Manage global commission rules and user specific overrides.</p>
        </div>
    </div>

    @if (session('success'))
        <div class="bg-primary/10 border border-primary/20 text-primary px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm animate-fade-in-down">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-bold">{{ session('success') }}</span>
        </div>
    @endif

    @if (session('error'))
        <div class="bg-secondary/10 border border-secondary/20 text-secondary px-4 py-3 rounded-xl flex items-center gap-2 shadow-sm animate-fade-in-down">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
            <span class="font-bold">{{ session('error') }}</span>
        </div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

        {{-- Left Column: Create Rule / Permission Form --}}
        <div class="lg:col-span-1">
            <div class="bg-surface rounded-3xl shadow-xl shadow-primary/5 border border-primary/10 sticky top-6 overflow-hidden">
                <div class="p-6 border-b border-primary/5 bg-navy/30">
                    <h3 class="text-sm font-black text-mainText uppercase tracking-wider flex items-center gap-2">
                        <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"></path></svg>
                        Configure Rule
                    </h3>
                </div>

                <div x-data="{ ruleType: 'commission' }"> {{-- Toggle between Commission Rule and Permission Override --}}

                    {{-- Tabs --}}
                    <div class="flex border-b border-primary/10">
                        <button @click="ruleType = 'commission'" :class="ruleType === 'commission' ? 'text-primary border-primary bg-primary/5' : 'text-mutedText border-transparent hover:text-mainText'" class="flex-1 py-3 text-xs font-black uppercase tracking-widest border-b-2 transition-all">
                            Commission
                        </button>
                        <button @click="ruleType = 'permission'" :class="ruleType === 'permission' ? 'text-primary border-primary bg-primary/5' : 'text-mutedText border-transparent hover:text-mainText'" class="flex-1 py-3 text-xs font-black uppercase tracking-widest border-b-2 transition-all">
                            User Access
                        </button>
                    </div>

                    <div class="p-6">
                        {{-- Commission Rule Form --}}
                        <form x-show="ruleType === 'commission'" action="{{ route('admin.affiliate.rules.store') }}" method="POST" class="space-y-5 animate-fade-in">
                            @csrf

                            {{-- Affiliate Select --}}
                            <div x-data="{ search: '', open: false, selectedId: '', selectedName: '' }">
                                <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2">Apply To User</label>
                                <div class="relative">
                                    <input type="hidden" name="affiliate_id" x-model="selectedId">
                                    <input type="text" x-model="search" @focus="open = true" @click.away="open = false"
                                           placeholder="Type to search (Leave empty for Global)"
                                           class="w-full text-xs font-bold bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-0 text-mainText placeholder-mutedText/50">

                                    <div x-show="open && search.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-primary/10 rounded-xl shadow-xl max-h-40 overflow-y-auto" style="display: none;">
                                        @foreach($affiliates as $affiliate)
                                            <div x-show="'{{ strtolower($affiliate->name) }}'.includes(search.toLowerCase())"
                                                 @click="selectedId = '{{ $affiliate->id }}'; search = '{{ $affiliate->name }}'; open = false"
                                                 class="px-4 py-2 hover:bg-primary/5 cursor-pointer text-xs font-bold text-mainText border-b border-primary/5 last:border-0">
                                                {{ $affiliate->name }} <span class="text-[10px] text-mutedText">({{ $affiliate->email }})</span>
                                            </div>
                                        @endforeach
                                    </div>
                                    <div x-show="selectedId" @click="selectedId = ''; search = ''" class="absolute right-3 top-2.5 cursor-pointer text-mutedText hover:text-red-500">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                                    </div>
                                </div>
                                <p class="text-[10px] text-mutedText mt-1 ml-1" x-text="selectedId ? 'Targeting Specific User' : 'Creating Global Rule'"></p>
                            </div>

                            {{-- Product Scope --}}
                             <div x-data="{ scope: '' }">
                                <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2">Scope</label>
                                <select name="product_type" x-model="scope" class="w-full text-xs font-bold bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-0 mb-3 text-mainText">
                                    <option value="">All Products</option>
                                    <option value="course">Specific Course</option>
                                    <option value="bundle">Specific Bundle</option>
                                </select>

                                <div x-show="scope === 'course'" class="animate-fade-in" style="display: none;">
                                    <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2">Select Course</label>
                                    <select name="product_id" class="w-full text-xs font-bold bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-0 mb-3 text-mainText">
                                        <option value="">-- Select Course --</option>
                                        @foreach($courses as $c) <option value="{{ $c->id }}">{{ $c->title }}</option> @endforeach
                                    </select>
                                </div>
                                 <div x-show="scope === 'bundle'" class="animate-fade-in" style="display: none;">
                                    <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2">Select Bundle</label>
                                    <select name="product_id" class="w-full text-xs font-bold bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-0 mb-3 text-mainText">
                                        <option value="">-- Select Bundle --</option>
                                        @foreach($bundles as $b) <option value="{{ $b->id }}">{{ $b->title }}</option> @endforeach
                                    </select>
                                </div>
                            </div>

                             <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2">Type</label>
                                    <select name="commission_type" class="w-full text-xs font-bold bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-0 text-mainText">
                                        <option value="percent">Percent %</option>
                                        <option value="fixed">Fixed ₹</option>
                                    </select>
                                </div>
                                 <div>
                                    <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2">Amount</label>
                                    <input type="number" step="0.01" name="amount" placeholder="0.00" class="w-full text-xs font-bold bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-0 text-mainText">
                                </div>
                            </div>

                            <button type="submit" class="w-full py-3.5 bg-secondary hover:bg-navy text-customWhite rounded-xl shadow-lg transition-all font-black text-[10px] uppercase tracking-widest mt-2">
                                Save Commission Rule
                            </button>
                        </form>

                        {{-- Permission Override Form --}}
                         <form x-show="ruleType === 'permission'" action="{{ route('admin.affiliate.users.update', ['id' => 'placeholder']) }}" method="POST" class="space-y-5 animate-fade-in" id="permissionForm">
                            @csrf
                            @method('PUT')

                            {{-- User Select (Mandatory for Permission) --}}
                            <div x-data="{ search: '', open: false, selectedId: '', selectedName: '' }">
                                <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2">Select User <span class="text-red-500">*</span></label>
                                <div class="relative">
                                    <input type="hidden" name="user_id_override" x-model="selectedId" required> {{-- Logic for JS to update form action --}}
                                    <input type="text" x-model="search" @focus="open = true" @click.away="open = false"
                                           placeholder="Search User..."
                                           class="w-full text-xs font-bold bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-0 text-mainText placeholder-mutedText/50">

                                    <div x-show="open && search.length > 0" class="absolute z-10 w-full mt-1 bg-white border border-primary/10 rounded-xl shadow-xl max-h-40 overflow-y-auto" style="display: none;">
                                        @foreach($affiliates as $affiliate)
                                            <div x-show="'{{ strtolower($affiliate->name) }}'.includes(search.toLowerCase())"
                                                 @click="selectedId = '{{ $affiliate->id }}'; search = '{{ $affiliate->name }}'; open = false; updateFormAction('{{ $affiliate->id }}')"
                                                 class="px-4 py-2 hover:bg-primary/5 cursor-pointer text-xs font-bold text-mainText border-b border-primary/5 last:border-0">
                                                {{ $affiliate->name }}
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                                <script>
                                    function updateFormAction(id) {
                                        document.getElementById('permissionForm').action = "/admin/affiliate/users/" + id + "/update";
                                    }
                                </script>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2">Course Selling Access</label>
                                <select name="can_sell_courses" class="w-full text-xs font-bold bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-0 text-mainText">
                                    <option value="1">Allow Selling</option>
                                    <option value="0">Deny Selling</option>
                                </select>
                            </div>

                            <div>
                                <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2">Base Commission % (Legacy)</label>
                                <input type="number" step="0.01" name="custom_commission_percentage" placeholder="Default System %" class="w-full text-xs font-bold bg-customWhite border-primary/20 rounded-xl focus:border-primary focus:ring-0 text-mainText">
                                <p class="text-[9px] text-mutedText mt-1">Optional. Use "Commission Rules" for more control.</p>
                            </div>

                            {{-- Hidden / Simplified for now, or allow bundle selection --}}
                             <div>
                                <label class="block text-[10px] font-black uppercase text-mutedText tracking-widest mb-2">Allowed Bundles</label>
                                <div class="h-32 overflow-y-auto border border-primary/20 rounded-xl p-2 bg-customWhite">
                                    @foreach($bundles as $bundle)
                                        <label class="flex items-center space-x-2 p-1 hover:bg-navy/5 rounded cursor-pointer">
                                            <input type="checkbox" name="allowed_bundles[]" value="{{ $bundle->id }}" class="rounded text-primary focus:ring-primary/20 border-gray-300">
                                            <span class="text-xs font-bold text-mainText">{{ $bundle->title }}</span>
                                        </label>
                                    @endforeach
                                </div>
                            </div>

                            <button type="submit" class="w-full py-3.5 bg-primary hover:bg-navy text-customWhite rounded-xl shadow-lg transition-all font-black text-[10px] uppercase tracking-widest mt-2">
                                Update User Permissions
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>

        {{-- Right Column: Rules & Permissions List --}}
        <div class="lg:col-span-2 space-y-8">

            {{-- 1. Active Commission Rules --}}
            <div class="bg-surface rounded-3xl border border-primary/10 shadow-xl shadow-primary/5 overflow-hidden">
                <div class="p-6 border-b border-primary/5 bg-navy/30 flex justify-between items-center">
                    <h3 class="text-sm font-black text-mainText uppercase tracking-wider">Commission Rules</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-primary/5 text-[10px] uppercase text-primary font-black tracking-wider">
                            <tr>
                                <th class="px-6 py-4">Applied To</th>
                                <th class="px-6 py-4">Scope</th>
                                <th class="px-6 py-4">Rate</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-primary/5">
                            @forelse ($rules as $rule)
                                <tr class="hover:bg-navy/5 transition-colors">
                                    <td class="px-6 py-4">
                                        @if ($rule->affiliate_id)
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-lg bg-secondary/10 text-secondary flex items-center justify-center text-xs font-bold">U</div>
                                                <div>
                                                    <span class="block text-xs font-bold text-mainText">{{ $rule->affiliate->name ?? 'User #'.$rule->affiliate_id }}</span>
                                                    <span class="text-[10px] text-mutedText">Specific User</span>
                                                </div>
                                            </div>
                                        @else
                                            <div class="flex items-center gap-2">
                                                <div class="w-8 h-8 rounded-lg bg-primary/10 text-primary flex items-center justify-center text-xs font-bold">G</div>
                                                 <div>
                                                    <span class="block text-xs font-bold text-mainText">Global</span>
                                                    <span class="text-[10px] text-mutedText">All Users</span>
                                                </div>
                                            </div>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                         @if ($rule->product_type)
                                            <span class="text-xs font-bold text-mainText">{{ $rule->product->title ?? 'Specific Product' }}</span>
                                            <span class="block text-[10px] text-mutedText uppercase">{{ str_contains($rule->product_type, 'Bundle') ? 'Bundle' : 'Course' }} Only</span>
                                        @else
                                            <span class="text-xs font-bold text-mutedText italic">All Products</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        <span class="px-3 py-1 rounded-lg bg-green-100 text-green-700 text-xs font-black">
                                            {{ number_format($rule->amount, 2) }} {{ $rule->commission_type == 'percent' ? '%' : '₹' }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <form action="{{ route('admin.affiliate.rules.delete', $rule->id) }}" method="POST" onsubmit="return confirm('Delete this rule?');">
                                            @csrf @method('DELETE')
                                            <button class="text-mutedText hover:text-red-500 transition-colors">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-8 text-center text-xs font-bold text-mutedText">No commission rules defined.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            {{-- 2. User Permission Overrides --}}
            <div class="bg-surface rounded-3xl border border-primary/10 shadow-xl shadow-primary/5 overflow-hidden">
                <div class="p-6 border-b border-primary/5 bg-navy/30 flex justify-between items-center">
                    <h3 class="text-sm font-black text-mainText uppercase tracking-wider">User Access Overrides</h3>
                    <span class="text-[10px] text-mutedText font-bold">Users with non-default settings</span>
                </div>

                 <div class="overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-primary/5 text-[10px] uppercase text-primary font-black tracking-wider">
                            <tr>
                                <th class="px-6 py-4">User</th>
                                <th class="px-6 py-4">Selling Access</th>
                                <th class="px-6 py-4">Bundle Access</th>
                                <th class="px-6 py-4 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-primary/5">
                            @forelse ($userOverrides as $setting)
                                <tr class="hover:bg-navy/5 transition-colors">
                                    <td class="px-6 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-8 h-8 rounded-full bg-navy flex items-center justify-center text-white text-xs font-bold">
                                                 {{ substr($setting->user->name ?? 'U', 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="text-xs font-bold text-mainText">{{ $setting->user->name ?? 'Unknown' }}</p>
                                                <p class="text-[10px] text-mutedText">{{ $setting->user->email ?? '' }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4">
                                        @if($setting->can_sell_courses)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-green-100 text-green-700">Allowed</span>
                                        @else
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-red-100 text-red-700">Denied</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4">
                                        @if(count($setting->allowed_bundle_ids ?? []) > 0)
                                            <span class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold bg-blue-100 text-blue-700">
                                                {{ count($setting->allowed_bundle_ids) }} Bundles
                                            </span>
                                        @else
                                            <span class="text-[10px] text-mutedText italic">Default</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 text-right">
                                        <a href="{{ route('admin.affiliate.users.edit', $setting->user_id) }}" class="text-primary hover:text-secondary text-xs font-bold underline">Edit Full Profile</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="4" class="px-6 py-8 text-center text-xs font-bold text-mutedText">No user overrides found. Defaults apply.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                     @if($userOverrides->hasPages())
                        <div class="p-4 border-t border-primary/5">
                            {{ $userOverrides->links() }}
                        </div>
                    @endif
                </div>
            </div>

        </div>
    </div>

</div>
@endsection
