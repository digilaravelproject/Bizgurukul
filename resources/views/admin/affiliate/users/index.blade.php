@extends('layouts.admin')

@section('title', 'Affiliate Users')

@section('content')
<div class="space-y-8 font-sans text-mainText">

    {{-- Header Section --}}
    <div class="flex flex-col md:flex-row justify-between items-end gap-4">
        <div>
            <h1 class="text-3xl font-extrabold tracking-tight text-mainText">Affiliate User Manager</h1>
            <p class="text-mutedText mt-1 text-sm">Manage user permissions and commission settings.</p>
        </div>
        <form action="{{ route('admin.affiliate.users.index') }}" method="GET" class="flex gap-3">
             <div class="relative">
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Search users by name or email..." class="pl-10 pr-4 py-2 bg-surface mp-input border border-primary/10 rounded-xl text-sm focus:ring-primary focus:border-primary w-64 shadow-sm">
                <svg class="w-4 h-4 text-mutedText absolute left-3 top-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
            <button type="submit" class="bg-primary text-white px-4 py-2 rounded-xl text-sm font-bold shadow-md hover:bg-secondary transition-colors">Search</button>
        </form>
    </div>

    {{-- Main Content Card --}}
    <div class="bg-surface rounded-2xl shadow-sm border border-primary/10 overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left">
                <thead class="bg-primary/5 text-xs uppercase text-primary font-bold tracking-wider">
                    <tr>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Email</th>
                        <th class="px-6 py-4">Course Selling</th>
                        <th class="px-6 py-4">Bundle Restrictions</th>
                        <th class="px-6 py-4">Custom Commission</th>
                        <th class="px-6 py-4 text-center">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-primary/5">
                    @forelse ($users as $user)
                        <tr class="hover:bg-navy transition-colors group">
                            <td class="px-6 py-4 text-sm font-bold text-mainText">{{ $user->name }}</td>
                            <td class="px-6 py-4 text-sm text-mutedText">{{ $user->email }}</td>
                            <td class="px-6 py-4">
                                @php $settings = $user->affiliateSettings; @endphp
                                @if($settings && !is_null($settings->can_sell_courses))
                                    @if($settings->can_sell_courses)
                                        <span class="text-green-600 font-bold text-xs bg-green-100 px-2 py-1 rounded-full">Allowed</span>
                                    @else
                                        <span class="text-red-600 font-bold text-xs bg-red-100 px-2 py-1 rounded-full">Denied</span>
                                    @endif
                                @else
                                    <span class="text-gray-500 text-xs italic">Global Default</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @php
                                    $allowed = $settings->allowed_bundle_ids ?? null;
                                @endphp
                                @if(is_null($allowed))
                                    <span class="text-green-600 font-bold text-xs">All Bundles</span>
                                @else
                                    <span class="text-orange-600 font-bold text-xs">Restricted ({{ count(is_array($allowed) ? $allowed : json_decode($allowed, true) ?? []) }})</span>
                                @endif
                            </td>
                            <td class="px-6 py-4">
                                @if($settings && !is_null($settings->custom_commission_percentage))
                                    <span class="text-primary font-bold">{{ $settings->custom_commission_percentage }}%</span>
                                @else
                                    <span class="text-gray-500 text-xs italic">Standard</span>
                                @endif
                            </td>
                            <td class="px-6 py-4 text-center">
                                <a href="{{ route('admin.affiliate.users.edit', $user->id) }}" class="text-xs bg-primary hover:bg-secondary text-white px-3 py-1 rounded-lg font-bold transition-all shadow-md">
                                    Edit Permissions
                                </a>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="px-6 py-12 text-center text-mutedText">No users found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        @if($users->hasPages())
            <div class="p-4 border-t border-primary/5">
                {{ $users->links() }}
            </div>
        @endif
    </div>
</div>
@endsection
