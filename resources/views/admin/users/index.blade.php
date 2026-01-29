@extends('layouts.admin')
@section('title', 'User Management')

@section('content')
    {{-- SweetAlert2 (Only external lib required for alerts) --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div x-data="userManager()" x-init="init()" class="container-fluid font-sans">

        {{-- Top Header with Action Buttons --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 animate-[fadeIn_0.3s_ease-out]">
            <div>
                <h2 class="text-2xl font-extrabold text-white tracking-tight">User Management</h2>
                <p class="text-xs text-mutedText mt-1">Manage students, verify KYC, and handle access control.</p>
            </div>

            <div class="flex items-center gap-3">
                {{-- Trash Toggle Switch --}}
                <div class="bg-navy/50 p-1 rounded-xl flex items-center border border-white/10 shadow-inner">
                    <button @click="toggleTrash(false)"
                        :class="!viewTrash ? 'bg-primary text-white shadow-lg shadow-primary/30' : 'text-mutedText hover:text-white'"
                        class="px-4 py-2 text-xs font-bold rounded-lg transition-all duration-300">
                        Active
                    </button>
                    <button @click="toggleTrash(true)"
                        :class="viewTrash ? 'bg-red-500 text-white shadow-lg shadow-red-500/30' : 'text-mutedText hover:text-white'"
                        class="px-4 py-2 text-xs font-bold rounded-lg transition-all duration-300 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                        Trash
                    </button>
                </div>

                {{-- Add User Button --}}
                <button @click="openModal('create')"
                    class="inline-flex items-center gap-2 rounded-xl bg-gradient-to-r from-primary to-indigo-600 px-5 py-2.5 text-xs font-bold text-white shadow-lg shadow-primary/25 hover:shadow-primary/40 hover:-translate-y-0.5 transition-all duration-300 border border-white/10">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                    Add New User
                </button>
            </div>
        </div>

        {{-- Search & Filters --}}
        <div class="mb-6 relative max-w-md animate-[fadeIn_0.4s_ease-out]">
            <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-mutedText">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </span>
            <input type="text" x-model.debounce.300ms="search" @input="fetchUsers()" placeholder="Search by name, email, mobile..."
                class="w-full pl-11 pr-4 py-3 bg-white/5 border border-white/10 text-white placeholder-mutedText/50 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary/50 outline-none transition shadow-sm backdrop-blur-sm text-sm">
        </div>

        {{-- DATA TABLE --}}
        <div class="overflow-hidden rounded-2xl border border-white/5 bg-navy/40 backdrop-blur-md shadow-xl relative min-h-[400px] animate-[fadeIn_0.5s_ease-out]">

            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm text-mutedText">
                    <thead class="bg-white/5 text-xs uppercase font-bold text-white border-b border-white/5 tracking-wider">
                        <tr>
                            <th class="px-6 py-5">User Profile</th>
                            <th class="px-6 py-5">Role</th>
                            <th class="px-6 py-5">KYC Status</th>
                            <th class="px-6 py-5">Status</th>
                            <th class="px-6 py-5 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-white/5">

                        {{-- Loading Skeleton (Shown when isLoading is true) --}}
                        <template x-if="isLoading">
                            <template x-for="i in 5">
                                <tr class="animate-pulse">
                                    <td class="px-6 py-4"><div class="flex items-center gap-3"><div class="h-10 w-10 rounded-full bg-white/10"></div><div class="space-y-2"><div class="h-3 w-32 bg-white/10 rounded"></div><div class="h-2 w-20 bg-white/10 rounded"></div></div></div></td>
                                    <td class="px-6 py-4"><div class="h-4 w-16 bg-white/10 rounded-full"></div></td>
                                    <td class="px-6 py-4"><div class="h-4 w-20 bg-white/10 rounded-full"></div></td>
                                    <td class="px-6 py-4"><div class="h-4 w-16 bg-white/10 rounded-full"></div></td>
                                    <td class="px-6 py-4 text-right"><div class="h-8 w-20 bg-white/10 rounded ml-auto"></div></td>
                                </tr>
                            </template>
                        </template>

                        {{-- Real Data Loop --}}
                        <template x-for="user in users" :key="user.id">
                            <tr class="hover:bg-white/[0.02] transition-colors group" x-show="!isLoading">

                                {{-- User Info --}}
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-4">
                                        <div class="relative">
                                            <template x-if="user.profile_picture">
                                                <img :src="'/storage/' + user.profile_picture" class="h-10 w-10 rounded-xl object-cover ring-1 ring-white/10 shadow-sm">
                                            </template>
                                            <template x-if="!user.profile_picture">
                                                <div class="h-10 w-10 rounded-xl bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center text-white font-bold ring-1 ring-white/10 shadow-sm text-sm border border-white/5">
                                                    <span x-text="user.name.charAt(0)"></span>
                                                </div>
                                            </template>
                                        </div>
                                        <div>
                                            <div class="font-bold text-white text-sm" x-text="user.name"></div>
                                            <div class="text-xs text-mutedText" x-text="user.email"></div>
                                            <div class="text-[10px] text-primary/80 font-mono mt-0.5 bg-primary/10 inline-block px-1.5 rounded border border-primary/20">
                                                REF: <span x-text="user.referral_code"></span>
                                            </div>
                                        </div>
                                    </div>
                                </td>

                                {{-- Role --}}
                                <td class="px-6 py-4">
                                    <template x-for="role in user.roles">
                                        <span class="inline-flex items-center rounded-md bg-white/5 px-2.5 py-1 text-xs font-bold text-white border border-white/10" x-text="role.name"></span>
                                    </template>
                                </td>

                                {{-- KYC --}}
                                <td class="px-6 py-4">
                                    <span class="px-2.5 py-1 rounded-md text-[10px] font-bold uppercase tracking-wider border shadow-sm"
                                        :class="{
                                            'bg-green-500/10 text-green-400 border-green-500/20': user.kyc_status === 'verified',
                                            'bg-amber-500/10 text-amber-400 border-amber-500/20': user.kyc_status === 'pending',
                                            'bg-red-500/10 text-red-400 border-red-500/20': user.kyc_status === 'rejected',
                                            'bg-white/5 text-mutedText border-white/10': user.kyc_status === 'not_submitted'
                                        }" x-text="user.kyc_status.replace('_', ' ')">
                                    </span>
                                </td>

                                {{-- Status --}}
                                <td class="px-6 py-4">
                                    <template x-if="user.is_banned == 1">
                                        <div class="flex items-center text-red-400 text-xs font-bold bg-red-500/10 border border-red-500/20 px-2 py-1 rounded-md w-fit">
                                            <span class="w-1.5 h-1.5 rounded-full bg-red-500 mr-2 animate-pulse"></span> Banned
                                        </div>
                                    </template>
                                    <template x-if="user.is_banned == 0">
                                        <div class="flex items-center text-green-400 text-xs font-bold bg-green-500/10 border border-green-500/20 px-2 py-1 rounded-md w-fit">
                                            <span class="w-1.5 h-1.5 rounded-full bg-green-500 mr-2 animate-pulse"></span> Active
                                        </div>
                                    </template>
                                </td>

                                {{-- Actions --}}
                                <td class="px-6 py-4 text-right">
                                    <div class="flex items-center justify-end gap-1 opacity-90">
                                        {{-- View --}}
                                        <button @click="viewUser(user.id)" class="p-2 text-mutedText hover:text-white hover:bg-white/10 rounded-lg transition" title="View Profile">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                        </button>

                                        {{-- Active Actions --}}
                                        <template x-if="!viewTrash">
                                            <div class="flex items-center gap-1">
                                                <button @click="openModal('edit', user)" class="p-2 text-mutedText hover:text-primary hover:bg-primary/10 rounded-lg transition" title="Edit">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg>
                                                </button>
                                                <button @click="toggleBan(user.id, user.is_banned)" class="p-2 text-mutedText hover:text-orange-400 hover:bg-orange-500/10 rounded-lg transition" :title="user.is_banned ? 'Unban' : 'Ban'">
                                                    <svg x-show="user.is_banned == 0" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path></svg>
                                                    <svg x-show="user.is_banned == 1" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                                </button>
                                                <button @click="deleteUser(user.id)" class="p-2 text-mutedText hover:text-red-400 hover:bg-red-500/10 rounded-lg transition" title="Trash">
                                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                                                </button>
                                            </div>
                                        </template>

                                        {{-- Trash Actions --}}
                                        <template x-if="viewTrash">
                                            <div class="flex items-center gap-2">
                                                <button @click="restoreUser(user.id)" class="px-3 py-1.5 text-xs font-bold text-green-400 bg-green-500/10 hover:bg-green-500/20 border border-green-500/20 rounded-lg transition">Restore</button>
                                                <button @click="forceDelete(user.id)" class="px-3 py-1.5 text-xs font-bold text-white bg-red-600 hover:bg-red-700 rounded-lg transition shadow-md shadow-red-600/20">Delete Forever</button>
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
                                    <div class="flex flex-col items-center justify-center text-mutedText/50">
                                        <svg class="w-16 h-16 mb-4 opacity-30" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                                        <p class="text-lg font-bold text-white">No users found</p>
                                        <p class="text-sm">We couldn't find anything matching your search.</p>
                                    </div>
                                </td>
                            </tr>
                        </template>
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="px-6 py-4 border-t border-white/5 bg-white/[0.02] flex items-center justify-between" x-show="pagination.total > 0">
                <span class="text-xs text-mutedText font-medium">
                    Showing <span class="text-white font-bold" x-text="pagination.from"></span> - <span class="text-white font-bold" x-text="pagination.to"></span> of <span class="text-white font-bold" x-text="pagination.total"></span>
                </span>
                <div class="flex gap-2">
                    <button @click="changePage(pagination.prev_page_url)" :disabled="!pagination.prev_page_url" class="px-4 py-1.5 text-xs font-bold border border-white/10 rounded-lg text-white hover:bg-white/5 disabled:opacity-50 disabled:cursor-not-allowed transition">Previous</button>
                    <button @click="changePage(pagination.next_page_url)" :disabled="!pagination.next_page_url" class="px-4 py-1.5 text-xs font-bold border border-white/10 rounded-lg text-white hover:bg-white/5 disabled:opacity-50 disabled:cursor-not-allowed transition">Next</button>
                </div>
            </div>
        </div>

        {{-- CREATE / EDIT MODAL --}}
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" x-show="showModal" x-transition.opacity></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div @click.away="showModal = false"
                    class="relative w-full max-w-2xl rounded-2xl bg-[#1E293B] border border-white/10 shadow-2xl overflow-hidden transform transition-all"
                    x-show="showModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 translate-y-4 scale-95" x-transition:enter-end="opacity-100 translate-y-0 scale-100">

                    {{-- Modal Header --}}
                    <div class="bg-white/5 px-6 py-4 border-b border-white/5 flex justify-between items-center">
                        <div>
                            <h3 class="text-lg font-bold text-white" x-text="modalMode === 'create' ? 'Add New User' : 'Edit User Details'"></h3>
                            <p class="text-xs text-mutedText">Fill in the required information below.</p>
                        </div>
                        <button @click="showModal = false" class="text-mutedText hover:text-white bg-white/5 rounded-full p-1 hover:bg-white/10 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="p-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
                            <div class="col-span-1 md:col-span-2">
                                <label class="block text-xs font-bold text-mutedText uppercase mb-1">Full Name <span class="text-secondary">*</span></label>
                                <input type="text" x-model="form.name" required class="w-full rounded-lg bg-navy border border-white/10 px-3 py-2.5 text-sm text-white focus:border-primary focus:ring-1 focus:ring-primary outline-none transition placeholder-white/20" placeholder="e.g. John Doe">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-mutedText uppercase mb-1">Email <span class="text-secondary">*</span></label>
                                <input type="email" x-model="form.email" required class="w-full rounded-lg bg-navy border border-white/10 px-3 py-2.5 text-sm text-white focus:border-primary focus:ring-1 focus:ring-primary outline-none transition placeholder-white/20" placeholder="john@example.com">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-mutedText uppercase mb-1">Mobile</label>
                                <input type="text" x-model="form.mobile" class="w-full rounded-lg bg-navy border border-white/10 px-3 py-2.5 text-sm text-white focus:border-primary focus:ring-1 focus:ring-primary outline-none transition placeholder-white/20" placeholder="10-digit number">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-mutedText uppercase mb-1">Gender</label>
                                <select x-model="form.gender" class="w-full rounded-lg bg-navy border border-white/10 px-3 py-2.5 text-sm text-white focus:border-primary outline-none transition">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-mutedText uppercase mb-1">Date of Birth</label>
                                <input type="date" x-model="form.dob" class="w-full rounded-lg bg-navy border border-white/10 px-3 py-2.5 text-sm text-white focus:border-primary outline-none transition">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-mutedText uppercase mb-1">State</label>
                                <select x-model="form.state_id" class="w-full rounded-lg bg-navy border border-white/10 px-3 py-2.5 text-sm text-white focus:border-primary outline-none transition">
                                    <option value="">Select State</option>
                                    <template x-for="(state, index) in indianStates" :key="index">
                                        <option :value="index + 1" x-text="state"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-mutedText uppercase mb-1">City</label>
                                <input type="text" x-model="form.city" class="w-full rounded-lg bg-navy border border-white/10 px-3 py-2.5 text-sm text-white focus:border-primary outline-none transition placeholder-white/20" placeholder="City Name">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-mutedText uppercase mb-1">Referral Code (Optional)</label>
                                <input type="text" x-model="form.referral_code" class="w-full rounded-lg bg-navy border border-white/10 px-3 py-2.5 text-sm text-white focus:border-primary outline-none transition placeholder-white/20" placeholder="Auto-generated if empty">
                            </div>
                            <div>
                                <label class="block text-xs font-bold text-mutedText uppercase mb-1">
                                    <span x-text="modalMode === 'create' ? 'Password *' : 'New Password (Optional)'"></span>
                                </label>
                                <input type="password" x-model="form.password" :required="modalMode === 'create'" class="w-full rounded-lg bg-navy border border-white/10 px-3 py-2.5 text-sm text-white focus:border-primary outline-none transition">
                            </div>

                            <div class="col-span-1 md:col-span-2 border-t border-white/10 pt-4 mt-2 grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-mutedText uppercase mb-1">Role <span class="text-secondary">*</span></label>
                                    <select x-model="form.role" required class="w-full rounded-lg bg-navy border border-white/10 px-3 py-2.5 text-sm text-white focus:border-primary outline-none transition">
                                        <option value="">Select Role</option>
                                        @foreach($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-mutedText uppercase mb-1">KYC Status</label>
                                    <select x-model="form.kyc_status" class="w-full rounded-lg bg-navy border border-white/10 px-3 py-2.5 text-sm text-white focus:border-primary outline-none transition">
                                        <option value="not_submitted">Not Submitted</option>
                                        <option value="pending">Pending</option>
                                        <option value="verified">Verified</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-3 pt-4 border-t border-white/10">
                            <button type="button" @click="showModal = false" class="px-5 py-2.5 text-sm font-medium text-mutedText bg-white/5 border border-white/10 rounded-lg hover:bg-white/10 transition">Cancel</button>
                            <button type="submit" :disabled="isSubmitting" class="px-5 py-2.5 text-sm font-bold text-white bg-primary rounded-lg hover:bg-indigo-600 disabled:opacity-70 transition flex items-center shadow-lg shadow-primary/25">
                                <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                <span x-text="isSubmitting ? 'Saving...' : 'Save User'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- VIEW MODAL --}}
        <div x-show="viewModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="fixed inset-0 bg-black/80 backdrop-blur-sm transition-opacity" x-show="viewModal" x-transition.opacity></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div @click.away="viewModal = false"
                    class="relative w-full max-w-lg rounded-2xl bg-[#1E293B] border border-white/10 shadow-2xl overflow-hidden"
                    x-show="viewModal" x-transition:enter="ease-out duration-300" x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

                    <div class="h-32 bg-gradient-to-r from-primary to-indigo-800 relative">
                        <button @click="viewModal = false" class="absolute top-4 right-4 bg-black/30 hover:bg-black/50 text-white rounded-full p-1.5 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                        </button>
                    </div>

                    <div class="px-6 pb-6 relative z-10">
                        <div class="flex justify-between items-end -mt-12 mb-4">
                            <div class="relative">
                                <template x-if="viewData.profile_picture">
                                    <img :src="viewData.profile_picture" class="h-24 w-24 rounded-2xl border-4 border-[#1E293B] shadow-xl bg-[#1E293B] object-cover">
                                </template>
                                <template x-if="!viewData.profile_picture">
                                    <div class="h-24 w-24 rounded-2xl border-4 border-[#1E293B] shadow-xl bg-gradient-to-br from-gray-700 to-gray-800 flex items-center justify-center text-white text-3xl font-bold">
                                        <span x-text="viewData.initials"></span>
                                    </div>
                                </template>
                            </div>
                            <span class="mb-4 px-3 py-1 rounded-full text-xs font-bold border"
                                :class="viewData.status === 'Active' ? 'bg-green-500/10 text-green-400 border-green-500/20' : 'bg-red-500/10 text-red-400 border-red-500/20'"
                                x-text="viewData.status">
                            </span>
                        </div>

                        <h2 class="text-2xl font-bold text-white" x-text="viewData.name"></h2>
                        <p class="text-sm text-mutedText" x-text="viewData.email"></p>

                        <div class="mt-6 grid grid-cols-2 gap-4 border-t border-white/10 pt-4">
                            <div><p class="text-xs font-bold text-mutedText/60 uppercase">Mobile</p><p class="text-sm font-semibold text-white mt-1" x-text="viewData.mobile || 'N/A'"></p></div>
                            <div><p class="text-xs font-bold text-mutedText/60 uppercase">Role</p><p class="text-sm font-semibold text-white mt-1" x-text="viewData.role"></p></div>
                            <div><p class="text-xs font-bold text-mutedText/60 uppercase">City / State</p><p class="text-sm font-semibold text-white mt-1"><span x-text="viewData.city || 'N/A'"></span>, <span x-text="indianStates[viewData.state_id - 1] || 'N/A'"></span></p></div>
                            <div><p class="text-xs font-bold text-mutedText/60 uppercase">Gender / DOB</p><p class="text-sm font-semibold text-white mt-1"><span x-text="viewData.gender || '-'"></span> / <span x-text="viewData.dob || '-'"></span></p></div>
                            <div class="col-span-2 bg-white/5 p-3 rounded-xl border border-white/5 flex justify-between items-center">
                                <div><p class="text-xs font-bold text-mutedText/60 uppercase">Referral Code</p><p class="text-sm font-mono font-bold text-primary mt-1" x-text="viewData.referral_code"></p></div>
                                <div class="text-right"><p class="text-xs font-bold text-mutedText/60 uppercase">Referred By</p><p class="text-sm font-semibold text-white mt-1" x-text="viewData.referred_by"></p></div>
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
                indianStates: ["Andaman and Nicobar Islands", "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chandigarh", "Chhattisgarh", "Dadra and Nagar Haveli", "Daman and Diu", "Delhi", "Goa", "Gujarat", "Haryana", "Himachal Pradesh", "Jammu and Kashmir", "Jharkhand", "Karnataka", "Kerala", "Ladakh", "Lakshadweep", "Madhya Pradesh", "Maharashtra", "Manipur", "Meghalaya", "Mizoram", "Nagaland", "Odisha", "Puducherry", "Punjab", "Rajasthan", "Sikkim", "Tamil Nadu", "Telangana", "Tripura", "Uttar Pradesh", "Uttarakhand", "West Bengal"],
                form: { id: null, name: '', email: '', mobile: '', gender: '', dob: '', state_id: '', city: '', referral_code: '', password: '', role: '', kyc_status: 'not_submitted' },

                init() {
                    this.fetchUsers();
                    this.Toast = Swal.mixin({ toast: true, position: 'top-end', showConfirmButton: false, timer: 2000, timerProgressBar: true, background: '#1E293B', color: '#fff' });
                },

                toggleTrash(status) {
                    this.viewTrash = status;
                    this.fetchUsers();
                },

                // Replaced Axios with native Fetch
                async fetchUsers(url = "{{ route('admin.users.index') }}") {
                    this.isLoading = true;
                    try {
                        let query = new URLSearchParams({ trash: this.viewTrash.toString(), search: this.search });
                        // Check if URL already has query params
                        let fetchUrl = url.includes('?') ? `${url}&${query.toString()}` : `${url}?${query.toString()}`;

                        let response = await fetch(fetchUrl, {
                            headers: { "X-Requested-With": "XMLHttpRequest", "Accept": "application/json" }
                        });
                        let result = await response.json();
                        if(result.status) {
                            this.users = result.data.data;
                            this.pagination = result.data;
                        }
                    } catch(error) {
                        console.error('Fetch error:', error);
                        this.Toast.fire({ icon: 'error', title: 'Failed to load data' });
                    } finally {
                        this.isLoading = false;
                    }
                },

                changePage(url) { if (url) this.fetchUsers(url); },

                openModal(mode, user = null) {
                    this.modalMode = mode;
                    this.showModal = true;
                    this.form.password = '';
                    if (mode === 'edit' && user) {
                        this.form = { ...this.form, ...user, role: user.roles.length > 0 ? user.roles[0].name : '' };
                        this.form.state_id = user.state_id; // ensure ID map
                    } else {
                        this.resetForm();
                    }
                },

                async viewUser(id) {
                    try {
                        let response = await fetch(`/admin/users/${id}/details`);
                        let result = await response.json();
                        if(result.status) {
                            this.viewData = result.data;
                            this.viewModal = true;
                        }
                    } catch(error) {
                        this.Toast.fire({ icon: 'error', title: 'Could not fetch details' });
                    }
                },

                resetForm() {
                    this.form = { id: null, name: '', email: '', mobile: '', gender: '', dob: '', state_id: '', city: '', referral_code: '', password: '', role: '', kyc_status: 'not_submitted' };
                },

                async submitForm() {
                    this.isSubmitting = true;
                    let url = this.modalMode === 'create' ? "{{ route('admin.users.store') }}" : `/admin/users/update/${this.form.id}`;

                    try {
                        let response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                                "Accept": "application/json"
                            },
                            body: JSON.stringify(this.form)
                        });

                        let result = await response.json();

                        if (!response.ok) throw result; // Handle 422/500

                        this.showModal = false;
                        this.fetchUsers(); // Silent update
                        this.Toast.fire({ icon: 'success', title: result.message });

                    } catch (error) {
                        let msg = error.message || "Validation Error";
                        // Handle Laravel Validation Errors
                        if(error.errors) {
                            msg = Object.values(error.errors).flat().join('<br>');
                        }
                        Swal.fire({ title: 'Error', html: msg, icon: 'error', background: '#1E293B', color: '#fff' });
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                toggleBan(id, currentStatus) {
                    Swal.fire({
                        title: currentStatus ? 'Unban User?' : 'Ban User?',
                        text: currentStatus ? "User will regain access." : "User will be blocked.",
                        icon: 'warning', showCancelButton: true, confirmButtonColor: '#6366F1', cancelButtonColor: '#d33', confirmButtonText: 'Yes, proceed!', background: '#1E293B', color: '#fff'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            await this.postAction(`/admin/users/ban/${id}`);
                        }
                    });
                },

                deleteUser(id) {
                    Swal.fire({
                        title: 'Move to Trash?', text: "You can restore this later.", icon: 'warning', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Yes, trash it!', background: '#1E293B', color: '#fff'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            // Using Fetch Delete
                            try {
                                let response = await fetch(`/admin/users/delete/${id}`, {
                                    method: 'DELETE',
                                    headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
                                });
                                let res = await response.json();
                                if(res.status) { this.fetchUsers(); this.Toast.fire({ icon: 'success', title: res.message }); }
                            } catch(e) { console.error(e); }
                        }
                    });
                },

                async restoreUser(id) { await this.postAction(`/admin/users/restore/${id}`); },

                forceDelete(id) {
                    Swal.fire({
                        title: 'Permanent Delete?', text: "Undone action!", icon: 'error', showCancelButton: true, confirmButtonColor: '#d33', confirmButtonText: 'Delete!', background: '#1E293B', color: '#fff'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                             try {
                                let response = await fetch(`/admin/users/force-delete/${id}`, {
                                    method: 'DELETE',
                                    headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content') }
                                });
                                let res = await response.json();
                                if(res.status) { this.fetchUsers(); this.Toast.fire({ icon: 'success', title: res.message }); }
                            } catch(e) { console.error(e); }
                        }
                    });
                },

                // Helper for simple POST actions
                async postAction(url) {
                    try {
                        let response = await fetch(url, {
                            method: 'POST',
                            headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute('content'), "Accept": "application/json" }
                        });
                        let result = await response.json();
                        if(result.status) {
                            this.fetchUsers();
                            this.Toast.fire({ icon: 'success', title: result.message });
                        }
                    } catch(e) {
                         this.Toast.fire({ icon: 'error', title: 'Action failed' });
                    }
                }
            }
        }
    </script>
@endsection
