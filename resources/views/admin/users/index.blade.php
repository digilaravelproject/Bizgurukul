@extends('layouts.admin')
@section('title', 'User Management')

@section('content')
    {{-- SweetAlert2 CDN --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div x-data="userManager()" x-init="init()" class="container-fluid px-4 py-4 font-sans">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-center mb-6 gap-4">
            <div>
                <h2 class="text-2xl font-bold text-slate-800 tracking-tight">User Management</h2>
                <p class="text-sm text-slate-500">Manage students, verify KYC, and handle access control.</p>
            </div>

            <div class="flex items-center gap-3">
                {{-- Trash Toggle --}}
                <div class="bg-slate-100 p-1 rounded-lg flex items-center border border-slate-200">
                    <button @click="toggleTrash(false)"
                        :class="!viewTrash ? 'bg-white text-indigo-600 shadow-sm font-semibold' : 'text-slate-500 hover:text-slate-700'"
                        class="px-4 py-1.5 text-sm rounded-md transition-all">
                        Active
                    </button>
                    <button @click="toggleTrash(true)"
                        :class="viewTrash ? 'bg-white text-red-600 shadow-sm font-semibold' : 'text-slate-500 hover:text-slate-700'"
                        class="px-4 py-1.5 text-sm rounded-md transition-all flex items-center gap-2">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Trash
                    </button>
                </div>

                {{-- Add User Button --}}
                <button @click="openModal('create')"
                    class="inline-flex items-center gap-2 rounded-lg bg-indigo-600 px-5 py-2 text-sm font-semibold text-white shadow-md hover:bg-indigo-700 hover:shadow-lg transition-all transform hover:-translate-y-0.5">
                    <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" />
                    </svg>
                    Add User
                </button>
            </div>
        </div>

        {{-- Search Bar --}}
        <div class="mb-5 relative max-w-md">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3 text-slate-400">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </span>
            <input type="text" x-model.debounce.400ms="search" @input="fetchUsers(true)" placeholder="Search users..."
                class="w-full pl-10 pr-4 py-2.5 border border-slate-300 rounded-xl focus:ring-2 focus:ring-indigo-500 focus:border-indigo-500 outline-none transition shadow-sm bg-white">
        </div>

        {{-- DATA TABLE --}}
        <div class="overflow-hidden rounded-xl border border-slate-200 bg-white shadow-sm relative min-h-[400px]">

            {{-- Soft Loader (Top Progress Bar style) --}}
            <div x-show="isLoading" class="absolute top-0 left-0 w-full h-1 bg-indigo-100 z-20">
                <div class="h-full bg-indigo-600 animate-pulse w-1/3 mx-auto rounded"></div>
            </div>
            {{-- Opacity Overlay on Load --}}
            <div x-show="isLoading" class="absolute inset-0 bg-white/50 z-10 transition-opacity duration-200"></div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-slate-600">
                    <thead
                        class="bg-slate-50 text-xs uppercase font-bold text-slate-500 border-b border-slate-200 tracking-wider">
                        <tr>
                            <th class="px-6 py-4">User Profile</th>
                            <th class="px-6 py-4">Role</th>
                            <th class="px-6 py-4">KYC Status</th>
                            <th class="px-6 py-4">Account</th>
                            <th class="px-6 py-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <template x-for="user in users" :key="user.id">
                            <tr class="hover:bg-slate-50 transition-colors group">

                                {{-- User Info --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <template x-if="user.profile_picture">
                                                <img :src="'/storage/' + user.profile_picture"
                                                    class="h-10 w-10 rounded-full object-cover ring-2 ring-white shadow-sm">
                                            </template>
                                            <template x-if="!user.profile_picture">
                                                <div
                                                    class="h-10 w-10 rounded-full bg-gradient-to-br from-indigo-500 to-purple-600 flex items-center justify-center text-white font-bold ring-2 ring-white shadow-sm text-sm">
                                                    <span x-text="user.name.charAt(0)"></span>
                                                </div>
                                            </template>
                                        </div>
                                        <div>
                                            <div class="font-bold text-slate-800 text-sm" x-text="user.name"></div>
                                            <div class="text-xs text-slate-500" x-text="user.email"></div>
                                            <div
                                                class="text-[10px] text-slate-400 font-mono mt-0.5 bg-slate-100 inline-block px-1 rounded">
                                                Ref: <span x-text="user.referral_code"></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Role --}}
                                <td class="px-6 py-4">
                                    <template x-for="role in user.roles">
                                        <span
                                            class="inline-flex items-center rounded-full bg-indigo-50 px-2.5 py-0.5 text-xs font-bold text-indigo-700 border border-indigo-100"
                                            x-text="role.name"></span>
                                    </template>
                                </td>

                                {{-- KYC --}}
                                <td class="px-6 py-4">
                                    <span
                                        class="px-2.5 py-1 rounded-md text-[11px] font-bold uppercase tracking-wider border shadow-sm"
                                        :class="{
                                            'bg-green-50 text-green-700 border-green-200': user.kyc_status === 'verified',
                                            'bg-amber-50 text-amber-700 border-amber-200': user.kyc_status === 'pending',
                                            'bg-red-50 text-red-700 border-red-200': user.kyc_status === 'rejected',
                                            'bg-slate-50 text-slate-600 border-slate-200': user.kyc_status === 'not_submitted'
                                        }" x-text="user.kyc_status.replace('_', ' ')">
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4">
                                    <template x-if="user.is_banned == 1">
                                        <div
                                            class="flex items-center text-red-600 text-xs font-bold bg-red-50 px-2 py-1 rounded-full w-fit">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-600 mr-2"></span> Banned
                                        </div>
                                    </template>
                                    <template x-if="user.is_banned == 0">
                                        <div
                                            class="flex items-center text-green-600 text-xs font-bold bg-green-50 px-2 py-1 rounded-full w-fit">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-600 mr-2"></span> Active
                                        </div>
                                    </template>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-2 opacity-90">

                                        {{-- View Button (Restored) --}}
                                        <button @click="viewUser(user.id)"
                                            class="p-2 text-slate-500 hover:text-indigo-600 hover:bg-indigo-50 rounded-lg transition"
                                            title="View Profile">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                </path>
                                            </svg>
                                        </button>

                                        {{-- ACTIVE MODE ACTIONS --}}
                                        <template x-if="!viewTrash">
                                            <div class="flex items-center gap-1">
                                                {{-- Edit --}}
                                                <button @click="openModal('edit', user)"
                                                    class="p-2 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition"
                                                    title="Edit User">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                        </path>
                                                    </svg>
                                                </button>

                                                {{-- Ban/Unban --}}
                                                <button @click="toggleBan(user.id, user.is_banned)"
                                                    class="p-2 rounded-lg transition text-slate-500 hover:text-orange-600 hover:bg-orange-50"
                                                    :title="user.is_banned == 1 ? 'Unban' : 'Ban'">
                                                    <svg x-show="user.is_banned == 0" class="w-5 h-5" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636">
                                                        </path>
                                                    </svg>
                                                    <svg x-show="user.is_banned == 1" class="w-5 h-5" fill="none"
                                                        stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                </button>

                                                {{-- Soft Delete --}}
                                                <button @click="deleteUser(user.id)"
                                                    class="p-2 text-slate-500 hover:text-red-600 hover:bg-red-50 rounded-lg transition"
                                                    title="Move to Trash">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                        viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round"
                                                            stroke-width="2"
                                                            d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                        </path>
                                                    </svg>
                                                </button>
                                            </div>
                                        </template>

                                        {{-- TRASH MODE ACTIONS --}}
                                        <template x-if="viewTrash">
                                            <div class="flex items-center gap-2">
                                                <button @click="restoreUser(user.id)"
                                                    class="px-3 py-1.5 text-xs font-bold text-green-700 bg-green-100 hover:bg-green-200 rounded-lg transition">
                                                    Restore
                                                </button>
                                                <button @click="forceDelete(user.id)"
                                                    class="px-3 py-1.5 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-lg transition">
                                                    Delete Forever
                                                </button>
                                            </div>
                                        </template>

                                    </div>
                                </td>
                            </tr>
                        </template>

                        {{-- Empty State --}}
                        <template x-if="users.length === 0 && !isLoading">
                            <tr>
                                <td colspan="5" class="px-6 py-12 text-center">
                                    <div class="flex flex-col items-center justify-center text-slate-400">
                                        <svg class="w-16 h-16 mb-4 opacity-20" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z">
                                            </path>
                                        </svg>
                                        <p class="text-lg font-medium text-slate-600">No users found</p>
                                        <p class="text-sm">We couldn't find anything matching your search.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between"
                x-show="pagination.total > 0">
                <span class="text-xs text-slate-500 font-medium">
                    Showing <span class="text-slate-700" x-text="pagination.from"></span> - <span class="text-slate-700"
                        x-text="pagination.to"></span> of <span class="text-slate-700" x-text="pagination.total"></span>
                </span>
                <div class="flex gap-2">
                    <button @click="changePage(pagination.prev_page_url)" :disabled="!pagination.prev_page_url"
                        class="px-3 py-1 text-xs font-bold border rounded bg-white hover:bg-slate-100 disabled:opacity-50 disabled:cursor-not-allowed">Previous</button>
                    <button @click="changePage(pagination.next_page_url)" :disabled="!pagination.next_page_url"
                        class="px-3 py-1 text-xs font-bold border rounded bg-white hover:bg-slate-100 disabled:opacity-50 disabled:cursor-not-allowed">Next</button>
                </div>
            </div>
        </div>

        {{-- CREATE / EDIT MODAL (Expanded) --}}
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" x-transition.opacity>

            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div @click.away="showModal = false"
                    class="relative w-full max-w-2xl rounded-2xl bg-white shadow-2xl overflow-hidden transform transition-all"
                    x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-4 sm:scale-95"
                    x-transition:enter-end="opacity-100 translate-y-0 sm:scale-100">

                    {{-- Modal Header --}}
                    <div class="bg-slate-50 px-6 py-4 border-b border-slate-200 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-slate-800"
                                x-text="modalMode === 'create' ? 'Add New User' : 'Edit User Details'"></h3>
                            <p class="text-xs text-slate-500">Fill in the required information below.</p>
                        </div>
                        <button @click="showModal = false"
                            class="text-slate-400 hover:text-slate-600 bg-white rounded-full p-1 hover:bg-slate-100 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">

                            {{-- Full Name --}}
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Full Name <span
                                        class="text-red-500">*</span></label>
                                <input type="text" x-model="form.name" required
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition"
                                    placeholder="e.g. John Doe">
                            </div>

                            {{-- Email --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Email <span
                                        class="text-red-500">*</span></label>
                                <input type="email" x-model="form.email" required
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition"
                                    placeholder="john@example.com">
                            </div>

                            {{-- Mobile --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Mobile</label>
                                <input type="text" x-model="form.mobile"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 focus:ring-1 focus:ring-indigo-500 outline-none transition"
                                    placeholder="10-digit number">
                            </div>

                            {{-- Gender --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Gender</label>
                                <select x-model="form.gender"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm bg-white focus:border-indigo-500 outline-none transition">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>

                            {{-- DOB --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Date of Birth</label>
                                <input type="date" x-model="form.dob"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 outline-none transition">
                            </div>

                            {{-- State (From JS Array) --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">State</label>
                                <select x-model="form.state_id"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm bg-white focus:border-indigo-500 outline-none transition">
                                    <option value="">Select State</option>
                                    <template x-for="(state, index) in indianStates" :key="index">
                                        <option :value="index + 1" x-text="state"></option>
                                    </template>
                                </select>
                            </div>

                            {{-- City --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">City</label>
                                <input type="text" x-model="form.city"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 outline-none transition"
                                    placeholder="City Name">
                            </div>

                            {{-- Referral Code --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Referral Code
                                    (Optional)</label>
                                <input type="text" x-model="form.referral_code"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 outline-none transition bg-slate-50"
                                    placeholder="Auto-generated if empty">
                            </div>

                            {{-- Password --}}
                            <div>
                                <label class="block text-xs font-bold text-slate-500 uppercase mb-1">
                                    <span x-text="modalMode === 'create' ? 'Password *' : 'New Password (Optional)'"></span>
                                </label>
                                <input type="password" x-model="form.password" :required="modalMode === 'create'"
                                    class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm focus:border-indigo-500 outline-none transition">
                            </div>

                            <div
                                class="col-span-1 md:col-span-2 border-t border-slate-100 pt-4 mt-2 grid grid-cols-2 gap-4">
                                {{-- Role --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">Role <span
                                            class="text-red-500">*</span></label>
                                    <select x-model="form.role" required
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm bg-white focus:border-indigo-500 outline-none transition">
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>

                                {{-- KYC Status --}}
                                <div>
                                    <label class="block text-xs font-bold text-slate-500 uppercase mb-1">KYC Status</label>
                                    <select x-model="form.kyc_status"
                                        class="w-full rounded-lg border border-slate-300 px-3 py-2.5 text-sm bg-white focus:border-indigo-500 outline-none transition">
                                        <option value="not_submitted">Not Submitted</option>
                                        <option value="pending">Pending</option>
                                        <option value="verified">Verified</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                            </div>

                        </div>

                        <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-slate-100">
                            <button type="button" @click="showModal = false"
                                class="px-5 py-2.5 text-sm font-medium text-slate-700 bg-white border border-slate-300 rounded-lg hover:bg-slate-50 transition shadow-sm">Cancel</button>
                            <button type="submit" :disabled="isSubmitting"
                                class="px-5 py-2.5 text-sm font-bold text-white bg-indigo-600 rounded-lg hover:bg-indigo-700 disabled:opacity-70 transition shadow-md flex items-center">
                                <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                    xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span x-text="isSubmitting ? 'Saving...' : 'Save User'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- VIEW MODAL (Profile Card) --}}
        <div x-show="viewModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" x-transition.opacity>
            <div class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div @click.away="viewModal = false"
                    class="relative w-full max-w-lg rounded-2xl bg-white shadow-2xl overflow-hidden border border-slate-200">

                    {{-- Cover --}}
                    <div class="h-32 bg-gradient-to-r from-indigo-600 to-blue-500 relative">
                        <button @click="viewModal = false"
                            class="absolute top-4 right-4 bg-black/20 hover:bg-black/40 text-white rounded-full p-1.5 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="px-6 pb-6">
                        <div class="flex justify-between items-end -mt-12 mb-4">
                            <div class="relative">
                                <template x-if="viewData.profile_picture">
                                    <img :src="viewData.profile_picture"
                                        class="h-24 w-24 rounded-full border-4 border-white shadow-lg bg-white object-cover">
                                </template>
                                <template x-if="!viewData.profile_picture">
                                    <div
                                        class="h-24 w-24 rounded-full border-4 border-white shadow-lg bg-indigo-600 flex items-center justify-center text-white text-3xl font-bold">
                                        <span x-text="viewData.initials"></span>
                                    </div>
                                </template>
                            </div>
                            <span class="mb-4 px-3 py-1 rounded-full text-xs font-bold border"
                                :class="viewData.status === 'Active' ? 'bg-green-50 text-green-700 border-green-200' : 'bg-red-50 text-red-700 border-red-200'"
                                x-text="viewData.status">
                            </span>
                        </div>

                        <h2 class="text-2xl font-bold text-slate-800" x-text="viewData.name"></h2>
                        <p class="text-sm text-slate-500" x-text="viewData.email"></p>

                        <div class="mt-6 grid grid-cols-2 gap-4 border-t border-slate-100 pt-4">
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase">Mobile</p>
                                <p class="text-sm font-semibold text-slate-700 mt-1" x-text="viewData.mobile || 'N/A'"></p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase">Role</p>
                                <p class="text-sm font-semibold text-slate-700 mt-1" x-text="viewData.role"></p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase">City / State</p>
                                <p class="text-sm font-semibold text-slate-700 mt-1">
                                    <span x-text="viewData.city || 'N/A'"></span>,
                                    <span x-text="indianStates[viewData.state_id - 1] || 'N/A'"></span>
                                </p>
                            </div>
                            <div>
                                <p class="text-xs font-bold text-slate-400 uppercase">Gender / DOB</p>
                                <p class="text-sm font-semibold text-slate-700 mt-1">
                                    <span x-text="viewData.gender || '-'"></span> /
                                    <span x-text="viewData.dob || '-'"></span>
                                </p>
                            </div>
                            <div
                                class="col-span-2 bg-slate-50 p-3 rounded-lg border border-slate-100 flex justify-between items-center">
                                <div>
                                    <p class="text-xs font-bold text-slate-400 uppercase">Referral Code</p>
                                    <p class="text-sm font-mono font-bold text-indigo-600 mt-1"
                                        x-text="viewData.referral_code"></p>
                                </div>
                                <div class="text-right">
                                    <p class="text-xs font-bold text-slate-400 uppercase">Referred By</p>
                                    <p class="text-sm font-semibold text-slate-700 mt-1" x-text="viewData.referred_by"></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <script>
        function userManager() {
            return {
                users: [],
                pagination: {},
                isLoading: false,
                search: '',
                viewTrash: false,
                showModal: false,
                viewModal: false,
                modalMode: 'create',
                isSubmitting: false,
                viewData: {},

                // Indian States List
                indianStates: [
                    "Andaman and Nicobar Islands", "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar",
                    "Chandigarh", "Chhattisgarh", "Dadra and Nagar Haveli", "Daman and Diu", "Delhi", "Goa",
                    "Gujarat", "Haryana", "Himachal Pradesh", "Jammu and Kashmir", "Jharkhand", "Karnataka",
                    "Kerala", "Ladakh", "Lakshadweep", "Madhya Pradesh", "Maharashtra", "Manipur", "Meghalaya",
                    "Mizoram", "Nagaland", "Odisha", "Puducherry", "Punjab", "Rajasthan", "Sikkim", "Tamil Nadu",
                    "Telangana", "Tripura", "Uttar Pradesh", "Uttarakhand", "West Bengal"
                ],

                form: {
                    id: null, name: '', email: '', mobile: '', gender: '', dob: '', state_id: '', city: '',
                    referral_code: '', password: '', role: '', kyc_status: 'not_submitted'
                },

                init() {
                    this.fetchUsers();
                    // Toast Mixin
                    this.Toast = Swal.mixin({
                        toast: true, position: 'top-end', showConfirmButton: false, timer: 2000,
                        timerProgressBar: true, didOpen: (toast) => {
                            toast.onmouseenter = Swal.stopTimer; toast.onmouseleave = Swal.resumeTimer;
                        }
                    });
                },

                toggleTrash(status) {
                    this.viewTrash = status;
                    this.fetchUsers();
                },

                fetchUsers(url = "{{ route('admin.users.index') }}") {
                    this.isLoading = true;
                    axios.get(url, { params: { trash: this.viewTrash, search: this.search } })
                        .then(res => {
                            this.users = res.data.data.data;
                            this.pagination = res.data.data;
                        })
                        .finally(() => this.isLoading = false);
                },

                changePage(url) { if (url) this.fetchUsers(url); },

                openModal(mode, user = null) {
                    this.modalMode = mode;
                    this.showModal = true;
                    this.form.password = '';

                    if (mode === 'edit' && user) {
                        this.form.id = user.id;
                        this.form.name = user.name;
                        this.form.email = user.email;
                        this.form.mobile = user.mobile;
                        this.form.gender = user.gender;
                        this.form.dob = user.dob;
                        this.form.state_id = user.state_id;
                        this.form.city = user.city;
                        this.form.referral_code = user.referral_code;
                        this.form.kyc_status = user.kyc_status;
                        this.form.role = user.roles.length > 0 ? user.roles[0].name : '';
                    } else {
                        this.resetForm();
                    }
                },

                viewUser(id) {
                    this.isLoading = true;
                    axios.get(`/admin/users/${id}/details`)
                        .then(res => {
                            this.viewData = res.data.data;
                            this.viewModal = true;
                        })
                        .finally(() => this.isLoading = false);
                },

                resetForm() {
                    this.form = { id: null, name: '', email: '', mobile: '', gender: '', dob: '', state_id: '', city: '', referral_code: '', password: '', role: '', kyc_status: 'not_submitted' };
                },

                submitForm() {
                    this.isSubmitting = true;
                    let url = this.modalMode === 'create' ? "{{ route('admin.users.store') }}" : `/admin/users/update/${this.form.id}`;
                    axios.post(url, this.form)
                        .then(res => {
                            this.showModal = false;
                            this.fetchUsers();
                            this.Toast.fire({ icon: 'success', title: res.data.message });
                        })
                        .catch(err => Swal.fire('Error', err.response?.data?.message || 'Something went wrong', 'error'))
                        .finally(() => this.isSubmitting = false);
                },

                toggleBan(id, currentStatus) {
                    Swal.fire({
                        title: currentStatus ? 'Unban User?' : 'Ban User?',
                        text: currentStatus ? "User will regain access." : "User will be blocked from logging in.",
                        icon: 'warning', showCancelButton: true, confirmButtonColor: '#3085d6', cancelButtonColor: '#d33',
                        confirmButtonText: 'Yes, proceed!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.post(`/admin/users/ban/${id}`).then(res => {
                                this.fetchUsers();
                                this.Toast.fire({ icon: 'success', title: res.data.message });
                            });
                        }
                    });
                },

                deleteUser(id) {
                    Swal.fire({
                        title: 'Move to Trash?', text: "You can restore this later.", icon: 'warning',
                        showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, trash it!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.delete(`/admin/users/delete/${id}`).then(res => {
                                this.fetchUsers();
                                this.Toast.fire({ icon: 'success', title: res.data.message });
                            });
                        }
                    });
                },

                restoreUser(id) {
                    axios.post(`/admin/users/restore/${id}`).then(res => {
                        this.fetchUsers();
                        this.Toast.fire({ icon: 'success', title: res.data.message });
                    });
                },

                forceDelete(id) {
                    Swal.fire({
                        title: 'Permanent Delete?', text: "This action cannot be undone!", icon: 'error',
                        showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, delete forever!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.delete(`/admin/users/force-delete/${id}`).then(res => {
                                this.fetchUsers();
                                this.Toast.fire({ icon: 'success', title: res.data.message });
                            });
                        }
                    });
                }
            }
        }
    </script>
@endsection
