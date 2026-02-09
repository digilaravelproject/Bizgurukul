@extends('layouts.user.app')

@section('content')
<div class="space-y-6">

    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <div>
            <h1 class="text-2xl font-bold text-mainText">Affiliate Links</h1>
            <p class="text-sm text-mutedText">Generate and manage your custom referral links.</p>
        </div>
    </div>

    {{-- Link Generator --}}
    <div class="bg-customWhite rounded-2xl shadow-lg border border-primary/5 p-6" x-data="{ type: 'general' }">
        <h3 class="text-lg font-bold text-mainText border-b border-primary/10 pb-2 mb-4">Create New Link</h3>

        <form action="{{ route('student.affiliate.links.store') }}" method="POST" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            @csrf

            {{-- Type Selection --}}
            <div class="col-span-1">
                <label class="block text-xs font-bold text-mutedText uppercase mb-1">Link Type</label>
                <select name="type" x-model="type" class="w-full px-3 py-2 rounded-xl bg-navy/5 border border-primary/10 focus:ring-primary focus:border-primary text-sm font-bold text-mainText">
                    <option value="general">General (All Products)</option>
                    <option value="specific_course">Specific Course</option>
                    <option value="specific_bundle">Specific Bundle</option>
                </select>
            </div>

            {{-- Target Selection (Conditional) --}}
            <div class="col-span-1" x-show="type !== 'general'" x-cloak>
                <label class="block text-xs font-bold text-mutedText uppercase mb-1">Select Product</label>

                {{-- Course Select --}}
                <select name="target_id" x-show="type === 'specific_course'" :required="type === 'specific_course'"
                    class="w-full px-3 py-2 rounded-xl bg-navy/5 border border-primary/10 focus:ring-primary focus:border-primary text-sm font-bold text-mainText">
                    <option value="">Choose Course...</option>
                    @foreach($courses as $course)
                        <option value="{{ $course->id }}">{{ $course->title }}</option>
                    @endforeach
                </select>

                {{-- Bundle Select --}}
                <select name="target_id" x-show="type === 'specific_bundle'" :required="type === 'specific_bundle'"
                    disabled {{-- Re-enable when bundles available --}}
                    class="w-full px-3 py-2 rounded-xl bg-navy/5 border border-primary/10 focus:ring-primary focus:border-primary text-sm font-bold text-mainText">
                    <option value="">Choose Bundle...</option>
                    @foreach($bundles as $bundle)
                        <option value="{{ $bundle->id }}">{{ $bundle->name }}</option>
                    @endforeach
                </select>
            </div>

            {{-- Expiry Date --}}
            <div class="col-span-1">
                <label class="block text-xs font-bold text-mutedText uppercase mb-1">Expiry Date (Optional)</label>
                <input type="date" name="expiry_date" min="{{ date('Y-m-d') }}"
                    class="w-full px-3 py-2 rounded-xl bg-navy/5 border border-primary/10 focus:ring-primary focus:border-primary text-sm font-bold text-mainText">
            </div>

            {{-- Create Button --}}
            <div class="col-span-1 flex items-end">
                <button type="submit" class="w-full bg-primary hover:bg-secondary text-white font-bold py-2 rounded-xl shadow-lg shadow-primary/20 transition-all">
                    Generate Link
                </button>
            </div>
        </form>
    </div>

    {{-- Existing Links Table --}}
    <div class="bg-customWhite rounded-2xl shadow-lg border border-primary/5 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-navy/5 border-b border-primary/5 text-xs uppercase text-mutedText font-bold">
                        <th class="px-6 py-4">Link</th>
                        <th class="px-6 py-4">Type</th>
                        <th class="px-6 py-4">Target</th>
                        <th class="px-6 py-4">Clicks</th>
                        <th class="px-6 py-4">Expiry</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4">Action</th>
                    </tr>
                </thead>
                <tbody class="text-sm font-medium text-mainText divide-y divide-primary/5">
                    @forelse($links as $link)
                    <tr class="hover:bg-navy/5 transition group">
                        <td class="px-6 py-4">
                            <div class="flex items-center space-x-2">
                                <span class="bg-primary/10 text-primary px-2 py-1 rounded-md text-xs font-bold font-mono">
                                    {{ route('affiliate.redirect', $link->slug) }}
                                </span>
                                <button onclick="navigator.clipboard.writeText('{{ route('affiliate.redirect', $link->slug) }}')"
                                    class="text-mutedText hover:text-primary transition" title="Copy">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                </button>
                            </div>
                        </td>
                        <td class="px-6 py-4 capitalize">
                            {{ str_replace('_', ' ', $link->type) }}
                        </td>
                        <td class="px-6 py-4">
                            @if($link->type === 'general')
                                <span class="text-mutedText">All Products</span>
                            @elseif($link->type === 'specific_course')
                                {{ $link->course->title ?? 'Deleted Course' }}
                            @elseif($link->type === 'specific_bundle')
                                {{ $link->bundle->name ?? 'Deleted Bundle' }}
                            @endif
                        </td>
                        <td class="px-6 py-4 text-center font-bold">
                            {{ $link->click_count }}
                        </td>
                        <td class="px-6 py-4">
                            @if($link->expiry_date)
                                <span class="{{ $link->expiry_date->isPast() ? 'text-red-500 font-bold' : 'text-emerald-600' }}">
                                    {{ $link->expiry_date->format('d M, Y') }}
                                </span>
                            @else
                                <span class="text-mutedText text-xs">Never</span>
                            @endif
                        </td>
                        <td class="px-6 py-4">
                             @if(!$link->is_active)
                                <span class="text-red-500 text-xs font-bold uppercase">Inactive</span>
                             @elseif($link->expiry_date && $link->expiry_date->isPast())
                                <span class="text-red-500 text-xs font-bold uppercase">Expired</span>
                             @else
                                <span class="text-emerald-500 text-xs font-bold uppercase">Active</span>
                             @endif
                        </td>
                        <td class="px-6 py-4">
                            <form action="{{ route('student.affiliate.links.destroy', $link->id) }}" method="POST" onsubmit="return confirm('Are you sure?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="text-red-400 hover:text-red-600 transition">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                </button>
                            </form>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="px-6 py-12 text-center text-mutedText">
                            No affiliate links created yet.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
