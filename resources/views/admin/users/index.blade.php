@extends('layouts.admin')
@section('title', 'User Management')

@section('content')
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div x-data="userManager()" x-init="init()" class="container-fluid font-sans antialiased">

        {{-- 1. TOP HEADER & ACTIONS --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 animate-fade-in">
            <div>
                <h2 class="text-2xl font-black text-mainText tracking-tight">User Management</h2>
                <p class="text-sm text-mutedText mt-1 font-medium">Manage students, verify KYC, and handle access control.
                </p>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
                {{-- Trash Toggle Switch --}}
                <div class="bg-white p-1 rounded-2xl flex items-center border border-primary/10 shadow-sm">
                    <button @click="toggleTrash(false)"
                        :class="!viewTrash ? 'bg-primary text-white shadow-md shadow-primary/20' :
                            'text-mutedText hover:text-primary'"
                        class="px-5 py-2 text-xs font-bold rounded-xl transition-all duration-300">
                        Active
                    </button>
                    <button @click="toggleTrash(true)"
                        :class="viewTrash ? 'bg-secondary text-white shadow-md shadow-secondary/20' :
                            'text-mutedText hover:text-secondary'"
                        class="px-5 py-2 text-xs font-bold rounded-xl transition-all duration-300 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                            </path>
                        </svg>
                        Trash
                    </button>
                </div>

                {{-- Add User Button --}}
                <button @click="openModal('create')"
                    class="flex-1 md:flex-none brand-gradient inline-flex items-center justify-center gap-2 rounded-xl px-6 py-3 text-xs font-black text-white shadow-lg shadow-primary/25 hover:-translate-y-0.5 transition-all duration-300">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    New User
                </button>
            </div>
        </div>

        {{-- 2. SEARCH BAR --}}
        <div class="mb-8 relative max-w-md animate-fade-in">
            <span class="absolute inset-y-0 left-0 flex items-center pl-4 text-primary">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </span>
            <input type="text" x-model.debounce.300ms="search" placeholder="Search name, email, mobile..."
                class="w-full pl-12 pr-10 py-4 bg-white border border-primary/10 text-mainText placeholder-mutedText/40 rounded-2xl focus:ring-2 focus:ring-primary/5 focus:border-primary outline-none transition shadow-sm font-bold text-sm">

            <button x-show="search.length > 0" @click="search = ''; fetchUsers()"
                class="absolute inset-y-0 right-0 flex items-center pr-4 text-mutedText hover:text-secondary transition"
                style="display: none;">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>

        {{-- 3. CONTENT AREA --}}
        <div class="relative min-h-[400px]">

            {{-- SKELETON LOADER (Shown during isLoading) --}}
            <div x-show="isLoading" x-transition.opacity
                class="bg-white border border-primary/10 rounded-[2rem] overflow-hidden shadow-xl shadow-primary/5">
                <div class="animate-pulse">
                    <div class="h-16 bg-primary/5 border-b border-primary/5"></div>
                    <template x-for="i in 5" :key="i">
                        <div class="flex items-center px-8 py-5 border-b border-primary/5 gap-4">
                            <div class="h-12 w-12 rounded-2xl bg-navy"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-3 w-48 bg-navy rounded"></div>
                                <div class="h-2 w-20 bg-navy rounded"></div>
                            </div>
                            <div class="h-6 w-24 bg-navy rounded hidden md:block"></div>
                            <div class="h-6 w-24 bg-navy rounded"></div>
                            <div class="h-8 w-8 bg-navy rounded-xl ml-auto"></div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- DATA TABLE --}}
            <div x-show="!isLoading"
                class="overflow-hidden rounded-[2rem] border border-primary/10 bg-white shadow-xl relative animate-fade-in">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-mutedText">
                        <thead
                            class="bg-primary/5 text-[10px] uppercase font-black text-primary border-b border-primary/5 tracking-widest">
                            <tr>
                                <th class="px-8 py-6">User Profile</th>
                                <th class="px-6 py-6">Role</th>
                                <th class="px-6 py-6 text-center">KYC Status</th>
                                <th class="px-6 py-6 text-center">Status</th>
                                <th class="px-8 py-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-primary/5">
                            <template x-for="user in users" :key="user.id">
                                <tr class="hover:bg-primary/[0.02] transition-colors group">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="relative h-12 w-12 flex-shrink-0">
                                                <template x-if="user.profile_picture">
                                                    <img :src="'/storage/' + user.profile_picture"
                                                        class="h-full w-full rounded-2xl object-cover ring-2 ring-primary/5 shadow-sm">
                                                </template>
                                                <template x-if="!user.profile_picture">
                                                    <div
                                                        class="h-full w-full rounded-2xl brand-gradient flex items-center justify-center text-white font-black text-sm shadow-sm border border-white">
                                                        <span x-text="user.name.charAt(0).toUpperCase()"></span>
                                                    </div>
                                                </template>
                                            </div>
                                            <div class="min-w-0">
                                                <div class="font-bold text-mainText text-sm truncate max-w-[180px]"
                                                    x-text="user.name"></div>
                                                <div class="text-xs text-mutedText truncate max-w-[180px]"
                                                    x-text="user.email"></div>
                                                <div class="mt-1 text-[9px] font-black text-primary/80 uppercase">REF: <span
                                                        x-text="user.referral_code"></span></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-5">
                                        <template x-for="role in user.roles" :key="role.id">
                                            <span
                                                class="inline-flex items-center rounded-lg bg-navy px-3 py-1 text-[10px] font-black text-mainText border border-primary/5"
                                                x-text="role.name"></span>
                                        </template>
                                    </td>
                                    <td class="px-6 py-5 text-center">
                                        <span
                                            class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-tighter border shadow-sm"
                                            :class="{
                                                'bg-green-50 text-green-600 border-green-200': user
                                                    .kyc_status === 'verified',
                                                'bg-amber-50 text-amber-600 border-amber-200': user
                                                    .kyc_status === 'pending',
                                                'bg-red-50 text-red-600 border-red-200': user.kyc_status === 'rejected',
                                                'bg-navy text-mutedText border-primary/5': user
                                                    .kyc_status === 'not_submitted'
                                            }"
                                            x-text="user.kyc_status.replace('_', ' ')">
                                        </span>
                                    </td>
                                    <td class="px-6 py-5">
                                        <div class="flex items-center justify-center text-xs font-bold"
                                            :class="user.is_banned == 1 ? 'text-secondary' : 'text-green-600'">
                                            <span class="w-1.5 h-1.5 rounded-full mr-2"
                                                :class="user.is_banned == 1 ? 'bg-secondary' : 'bg-green-500'"></span>
                                            <span x-text="user.is_banned == 1 ? 'Banned' : 'Active'"></span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <div
                                            class="flex items-center justify-end gap-1 opacity-90 group-hover:opacity-100 transition-opacity">
                                            <button @click="viewUser(user.id)"
                                                class="p-2 text-mutedText hover:text-primary hover:bg-navy rounded-xl transition group-hover:shadow-sm"
                                                title="View Profile">
                                                <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                    viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                        d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z">
                                                    </path>
                                                </svg>
                                            </button>

                                            <template x-if="!viewTrash">
                                                <div class="flex items-center gap-1">
                                                    <button @click="openModal('edit', user)"
                                                        class="p-2 text-mutedText hover:text-primary hover:bg-navy rounded-xl transition group-hover:shadow-sm"
                                                        title="Edit">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor"
                                                            viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round"
                                                                stroke-width="2"
                                                                d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                            </path>
                                                        </svg>
                                                    </button>
                                                    <button @click="deleteUser(user.id)"
                                                        class="p-2 text-mutedText hover:text-secondary hover:bg-navy rounded-xl transition group-hover:shadow-sm"
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

                                            <template x-if="viewTrash">
                                                <div class="flex items-center gap-2">
                                                    <button @click="restoreUser(user.id)"
                                                        class="px-4 py-2 text-[10px] font-black uppercase tracking-widest text-green-600 bg-green-50 hover:bg-green-100 rounded-xl transition border border-green-200">Restore</button>
                                                    <button @click="forceDelete(user.id)"
                                                        class="px-4 py-2 text-[10px] font-black uppercase tracking-widest text-white bg-secondary rounded-xl shadow-md shadow-secondary/20">Permanent
                                                        Delete</button>
                                                </div>
                                            </template>
                                        </div>
                                    </td>
                                </tr>
                            </template>
                            {{-- Empty State --}}
                            <template x-if="users.length === 0 && !isLoading">
                                <tr>
                                    <td colspan="5" class="px-6 py-12 text-center text-mutedText/50 font-bold">No users
                                        found</td>
                                </tr>
                            </template>
                        </tbody>
                    </table>
                </div>

                {{-- Pagination --}}
                <div class="px-8 py-6 bg-primary/5 border-t border-primary/5 flex items-center justify-between"
                    x-show="pagination.total > 0">
                    <span class="text-xs text-mutedText font-bold">Showing <span class="text-primary"
                            x-text="pagination.from"></span> - <span class="text-primary" x-text="pagination.to"></span>
                        of <span class="text-primary" x-text="pagination.total"></span> users</span>
                    <div class="flex gap-2">
                        <button @click="changePage(pagination.prev_page_url)" :disabled="!pagination.prev_page_url"
                            class="px-5 py-2 text-xs font-black uppercase tracking-widest bg-white border border-primary/10 rounded-xl hover:bg-primary hover:text-white transition disabled:opacity-30 shadow-sm">Prev</button>
                        <button @click="changePage(pagination.next_page_url)" :disabled="!pagination.next_page_url"
                            class="px-5 py-2 text-xs font-black uppercase tracking-widest bg-white border border-primary/10 rounded-xl hover:bg-primary hover:text-white transition disabled:opacity-30 shadow-sm">Next</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- 4. CREATE / EDIT MODAL --}}
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="fixed inset-0 bg-mainText/40 backdrop-blur-sm transition-opacity" x-show="showModal"
                x-transition.opacity></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div @click.away="showModal = false"
                    class="relative w-full max-w-2xl rounded-[2rem] bg-white border border-primary/10 shadow-2xl overflow-hidden transform transition-all"
                    x-show="showModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0">

                    <div class="bg-navy px-8 py-6 border-b border-primary/5 flex justify-between items-center">
                        <div>
                            <h3 class="text-xl font-black text-mainText"
                                x-text="modalMode === 'create' ? 'Add New User' : 'Edit User Profile'"></h3>
                            <p class="text-xs text-mutedText font-medium">Please enter valid student information below.</p>
                        </div>
                        <button @click="showModal = false"
                            class="text-mutedText hover:text-secondary bg-white rounded-2xl p-2 shadow-sm transition">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="p-8">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-2">
                                <label
                                    class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-1.5 ml-1">Full
                                    Name <span class="text-secondary">*</span></label>
                                <input type="text" x-model="form.name" required
                                    class="w-full rounded-2xl bg-navy/50 px-5 py-3.5 text-sm font-bold text-mainText focus:bg-white focus:border-primary focus:ring-2 focus:ring-primary/10 transition-all outline-none"
                                    placeholder="John Doe">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-1.5 ml-1">Email
                                    <span class="text-secondary">*</span></label>
                                <input type="email" x-model="form.email" required
                                    class="w-full rounded-2xl bg-navy/50 px-5 py-3.5 text-sm font-bold text-mainText focus:bg-white focus:border-primary outline-none"
                                    placeholder="student@skillspehle.com">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-1.5 ml-1">Mobile</label>
                                <input type="text" x-model="form.mobile"
                                    class="w-full rounded-2xl bg-navy/50 px-5 py-3.5 text-sm font-bold text-mainText focus:bg-white focus:border-primary outline-none"
                                    placeholder="10-digit number">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-1.5 ml-1">Gender</label>
                                <select x-model="form.gender"
                                    class="w-full rounded-2xl bg-navy/50 px-5 py-3.5 text-sm font-bold text-mainText focus:bg-white focus:border-primary outline-none appearance-none">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-1.5 ml-1">Date
                                    of Birth</label>
                                <input type="date" x-model="form.dob"
                                    class="w-full rounded-2xl bg-navy/50 px-5 py-3.5 text-sm font-bold text-mainText focus:bg-white focus:border-primary outline-none">
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-1.5 ml-1">State</label>
                                <select x-model="form.state_id"
                                    class="w-full rounded-2xl bg-navy/50 px-5 py-3.5 text-sm font-bold text-mainText focus:bg-white focus:border-primary outline-none">
                                    <option value="">Select State</option>
                                    <template x-for="(state, index) in indianStates" :key="index">
                                        <option :value="index + 1" x-text="state"></option>
                                    </template>
                                </select>
                            </div>
                            <div>
                                <label
                                    class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-1.5 ml-1">City</label>
                                <input type="text" x-model="form.city"
                                    class="w-full rounded-2xl bg-navy/50 px-5 py-3.5 text-sm font-bold text-mainText focus:bg-white focus:border-primary outline-none"
                                    placeholder="City Name">
                            </div>
                            <div class="col-span-2 border-t border-primary/5 pt-6 mt-2 grid grid-cols-2 gap-6">
                                <div>
                                    <label
                                        class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-1.5 ml-1">Assign
                                        Role <span class="text-secondary">*</span></label>
                                    <select x-model="form.role" required
                                        class="w-full rounded-2xl bg-navy/50 px-5 py-3.5 text-sm font-bold text-mainText focus:bg-white focus:border-primary outline-none">
                                        <option value="">Select Role</option>
                                        @foreach ($roles as $role)
                                            <option value="{{ $role->name }}">{{ $role->name }}</option>
                                        @endforeach
                                    </select>
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-1.5 ml-1">KYC
                                        Status</label>
                                    <select x-model="form.kyc_status"
                                        class="w-full rounded-2xl bg-navy/50 px-5 py-3.5 text-sm font-bold text-mainText focus:bg-white focus:border-primary outline-none">
                                        <option value="not_submitted">Not Submitted</option>
                                        <option value="pending">Pending</option>
                                        <option value="verified">Verified</option>
                                        <option value="rejected">Rejected</option>
                                    </select>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-3 pt-6 border-t border-primary/5">
                            <button type="button" @click="showModal = false"
                                class="px-8 py-3.5 text-xs font-black uppercase tracking-widest text-mutedText hover:text-secondary transition-all">Cancel</button>
                            <button type="submit" :disabled="isSubmitting"
                                class="brand-gradient px-10 py-3.5 text-xs font-black uppercase tracking-widest text-white rounded-2xl shadow-lg shadow-primary/20 disabled:opacity-50 transition-all flex items-center">
                                <svg x-show="isSubmitting" class="animate-spin -ml-1 mr-3 h-4 w-4 text-white"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10"
                                        stroke="currentColor" stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span x-text="isSubmitting ? 'Processing...' : 'Confirm & Save'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        {{-- 5. USER VIEW MODAL --}}
        <div x-show="viewModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" x-cloak>
            <div class="fixed inset-0 bg-mainText/40 backdrop-blur-sm transition-opacity" x-show="viewModal"
                x-transition.opacity></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div @click.away="viewModal = false"
                    class="relative w-full max-w-lg rounded-[2.5rem] bg-white border border-primary/10 shadow-2xl overflow-hidden"
                    x-show="viewModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 scale-95" x-transition:enter-end="opacity-100 scale-100">

                    <div class="brand-gradient h-32 relative">
                        <button @click="viewModal = false"
                            class="absolute top-6 right-6 bg-white/20 hover:bg-white/40 text-white rounded-2xl p-2 transition-all">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <div class="px-10 pb-10 -mt-16 text-center">
                        <div class="inline-block relative">
                            <template x-if="viewData.profile_picture">
                                <img :src="viewData.profile_picture"
                                    class="h-32 w-32 rounded-[2rem] border-[6px] border-white shadow-xl object-cover">
                            </template>
                            <template x-if="!viewData.profile_picture">
                                <div
                                    class="h-32 w-32 rounded-[2rem] border-[6px] border-white shadow-xl brand-gradient flex items-center justify-center text-white text-4xl font-black">
                                    <span x-text="viewData.initials"></span>
                                </div>
                            </template>
                        </div>

                        <h2 class="mt-6 text-2xl font-black text-mainText tracking-tight" x-text="viewData.name"></h2>
                        <p class="text-sm font-bold text-primary tracking-wide uppercase" x-text="viewData.role"></p>

                        <div class="mt-8 grid grid-cols-2 gap-4 text-left">
                            <div class="bg-navy p-5 rounded-2xl">
                                <p class="text-[9px] font-black uppercase tracking-widest text-mutedText mb-1">Email
                                    Address</p>
                                <p class="text-xs font-bold text-mainText truncate" x-text="viewData.email"></p>
                            </div>
                            <div class="bg-navy p-5 rounded-2xl">
                                <p class="text-[9px] font-black uppercase tracking-widest text-mutedText mb-1">Mobile
                                    Contact</p>
                                <p class="text-xs font-bold text-mainText" x-text="viewData.mobile || '-'"></p>
                            </div>
                            <div class="bg-navy p-5 rounded-2xl">
                                <p class="text-[9px] font-black uppercase tracking-widest text-mutedText mb-1">Location</p>
                                <p class="text-xs font-bold text-mainText truncate"><span
                                        x-text="viewData.city || 'N/A'"></span>, <span
                                        x-text="indianStates[viewData.state_id - 1] || 'N/A'"></span></p>
                            </div>
                            <div class="bg-navy p-5 rounded-2xl border border-primary/10">
                                <p class="text-[9px] font-black uppercase tracking-widest text-primary mb-1">Referral Code
                                </p>
                                <p class="text-xs font-black text-mainText" x-text="viewData.referral_code"></p>
                            </div>
                        </div>

                         {{-- Affiliate Stats Section --}}
                         <div class="mt-6 pt-6 border-t border-primary/5">
                            <h3 class="text-sm font-black text-mainText mb-4">Affiliate Performance</h3>
                            <div class="grid grid-cols-3 gap-4 text-center">
                                <div class="bg-indigo-50 p-4 rounded-xl border border-indigo-100">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-indigo-400 mb-1">Referrals</p>
                                    <p class="text-xl font-black text-indigo-700" x-text="viewData.referral_count"></p>
                                </div>
                                <div class="bg-green-50 p-4 rounded-xl border border-green-100">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-green-400 mb-1">Total Earned</p>
                                    <p class="text-lg font-black text-green-700">₹<span x-text="viewData.total_earnings"></span></p>
                                </div>
                                <div class="bg-orange-50 p-4 rounded-xl border border-orange-100">
                                    <p class="text-[9px] font-black uppercase tracking-widest text-orange-400 mb-1">Pending</p>
                                    <p class="text-lg font-black text-orange-700">₹<span x-text="viewData.pending_earnings"></span></p>
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
                controller: null,
                indianStates: ["Andaman and Nicobar Islands", "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar",
                    "Chandigarh", "Chhattisgarh", "Dadra and Nagar Haveli", "Daman and Diu", "Delhi", "Goa", "Gujarat",
                    "Haryana", "Himachal Pradesh", "Jammu and Kashmir", "Jharkhand", "Karnataka", "Kerala", "Ladakh",
                    "Lakshadweep", "Madhya Pradesh", "Maharashtra", "Manipur", "Meghalaya", "Mizoram", "Nagaland",
                    "Odisha", "Puducherry", "Punjab", "Rajasthan", "Sikkim", "Tamil Nadu", "Telangana", "Tripura",
                    "Uttar Pradesh", "Uttarakhand", "West Bengal"
                ],
                form: {
                    id: null,
                    name: '',
                    email: '',
                    mobile: '',
                    gender: '',
                    dob: '',
                    state_id: '',
                    city: '',
                    referral_code: '',
                    password: '',
                    role: '',
                    kyc_status: 'not_submitted'
                },

                init() {
                    this.fetchUsers();
                    this.$watch('search', () => this.fetchUsers());
                    this.Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                        background: '#FFFFFF',
                        color: '#2D2D2D'
                    });
                },

                toggleTrash(status) {
                    this.viewTrash = status;
                    this.fetchUsers();
                },

                async fetchUsers(url = "{{ route('admin.users.index') }}") {
                    if (this.controller) this.controller.abort();
                    this.controller = new AbortController();

                    this.isLoading = true;
                    try {
                        let targetUrl = new URL(url.includes('http') ? url : window.location.origin + url);
                        targetUrl.searchParams.set('trash', this.viewTrash);
                        if (this.search) {
                            targetUrl.searchParams.set('search', this.search);
                        }
                        // Cache bursting
                        targetUrl.searchParams.set('_t', new Date().getTime());

                        let response = await fetch(targetUrl, {
                            headers: {
                                "X-Requested-With": "XMLHttpRequest",
                                "Accept": "application/json"
                            },
                            signal: this.controller.signal
                        });

                        let result = await response.json();
                        if (result.status) {
                            this.users = result.data.data;
                            this.pagination = result.data;
                        }
                    } catch (error) {
                        if (error.name !== 'AbortError') console.error('Fetch error:', error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                changePage(url) {
                    if (url) this.fetchUsers(url);
                },

                openModal(mode, user = null) {
                    this.modalMode = mode;
                    this.showModal = true;
                    this.form.password = '';
                    if (mode === 'edit' && user) {
                        this.form = {
                            ...this.form,
                            ...user,
                            role: user.roles.length > 0 ? user.roles[0].name : ''
                        };
                        this.form.state_id = user.state_id;
                    } else {
                        this.resetForm();
                    }
                },

                async viewUser(id) {
                    try {
                        let response = await fetch(`/admin/users/${id}/details`);
                        let result = await response.json();
                        if (result.status) {
                            this.viewData = result.data;
                            this.viewModal = true;
                        }
                    } catch (error) {
                        this.Toast.fire({
                            icon: 'error',
                            title: 'Could not fetch details'
                        });
                    }
                },

                resetForm() {
                    this.form = {
                        id: null,
                        name: '',
                        email: '',
                        mobile: '',
                        gender: '',
                        dob: '',
                        state_id: '',
                        city: '',
                        referral_code: '',
                        password: '',
                        role: '',
                        kyc_status: 'not_submitted'
                    };
                },

                async submitForm() {
                    this.isSubmitting = true;
                    let url = this.modalMode === 'create' ? "{{ route('admin.users.store') }}" :
                        `/admin/users/update/${this.form.id}`;

                    try {
                        let response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                "Accept": "application/json"
                            },
                            body: JSON.stringify(this.form)
                        });

                        let result = await response.json();
                        if (!response.ok) throw result;

                        this.showModal = false;
                        this.fetchUsers();
                        this.Toast.fire({
                            icon: 'success',
                            title: result.message
                        });

                    } catch (error) {
                        let msg = error.message || "Validation Error";
                        if (error.errors) msg = Object.values(error.errors).flat().join('<br>');
                        Swal.fire({
                            title: 'Error',
                            html: msg,
                            icon: 'error',
                            background: '#FFFFFF',
                            color: '#2D2D2D'
                        });
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                async postAction(url) {
                    try {
                        let response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').getAttribute(
                                    'content'),
                                "Accept": "application/json"
                            }
                        });
                        let result = await response.json();
                        if (result.status) {
                            this.fetchUsers();
                            this.Toast.fire({
                                icon: 'success',
                                title: result.message
                            });
                        }
                    } catch (e) {
                        this.Toast.fire({
                            icon: 'error',
                            title: 'Action failed'
                        });
                    }
                },

                toggleBan(id, currentStatus) {
                    Swal.fire({
                        title: currentStatus ? 'Unban User?' : 'Ban User?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#F7941D',
                        confirmButtonText: 'Confirm',
                        background: '#FFFFFF',
                        color: '#2D2D2D'
                    }).then(async (result) => {
                        if (result.isConfirmed) await this.postAction(`/admin/users/ban/${id}`);
                    });
                },

                deleteUser(id) {
                    Swal.fire({
                        title: 'Trash User?',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#D04A02',
                        confirmButtonText: 'Move to Trash',
                        background: '#FFFFFF',
                        color: '#2D2D2D'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            try {
                                let response = await fetch(`/admin/users/delete/${id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        "X-CSRF-TOKEN": document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute('content')
                                    }
                                });
                                let res = await response.json();
                                if (res.status) {
                                    this.fetchUsers();
                                    this.Toast.fire({
                                        icon: 'success',
                                        title: res.message
                                    });
                                }
                            } catch (e) {
                                console.error(e);
                            }
                        }
                    });
                },

                async restoreUser(id) {
                    await this.postAction(`/admin/users/restore/${id}`);
                },

                forceDelete(id) {
                    Swal.fire({
                        title: 'Permanent Delete?',
                        icon: 'error',
                        showCancelButton: true,
                        confirmButtonColor: '#D04A02',
                        confirmButtonText: 'Delete Forever',
                        background: '#FFFFFF',
                        color: '#2D2D2D'
                    }).then(async (result) => {
                        if (result.isConfirmed) {
                            try {
                                let response = await fetch(`/admin/users/force-delete/${id}`, {
                                    method: 'DELETE',
                                    headers: {
                                        "X-CSRF-TOKEN": document.querySelector(
                                            'meta[name="csrf-token"]').getAttribute('content')
                                    }
                                });
                                let res = await response.json();
                                if (res.status) {
                                    this.fetchUsers();
                                    this.Toast.fire({
                                        icon: 'success',
                                        title: res.message
                                    });
                                }
                            } catch (e) {
                                console.error(e);
                            }
                        }
                    });
                }
            }
        }
    </script>
@endsection
