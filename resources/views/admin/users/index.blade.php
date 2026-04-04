@extends('layouts.admin')
@section('title', 'User Management')

@section('content')
    {{-- SweetAlert2 --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div x-data="userManager()" x-init="init()" class="container-fluid font-sans antialiased">

        {{-- 1. TOP HEADER & ACTIONS --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 animate-fade-in text-mainText">
            <div>
                <h2 class="text-2xl font-black tracking-tight">User Management</h2>
                <p class="text-sm text-mutedText mt-1 font-medium">Manage students, verify KYC, and handle access control.</p>
            </div>

            <div class="flex items-center gap-3 w-full md:w-auto">
                {{-- Trash Toggle --}}
                <div class="bg-white p-1 rounded-2xl flex items-center border border-primary/10 shadow-sm">
                    <button @click="toggleTrash(false)"
                        :class="!viewTrash ? 'bg-primary text-white shadow-md shadow-primary/20' : 'text-mutedText hover:text-primary'"
                        class="px-5 py-2 text-xs font-bold rounded-xl transition-all duration-300">
                        Active
                    </button>
                    <button @click="toggleTrash(true)"
                        :class="viewTrash ? 'bg-secondary text-white shadow-md shadow-secondary/20' : 'text-mutedText hover:text-secondary'"
                        class="px-5 py-2 text-xs font-bold rounded-xl transition-all duration-300 flex items-center gap-2">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                        </svg>
                        Trash
                    </button>
                </div>

                <button @click="openModal('create')"
                    class="flex-1 md:flex-none brand-gradient inline-flex items-center justify-center gap-2 rounded-xl px-6 py-3 text-xs font-black text-white shadow-lg shadow-primary/25 hover:-translate-y-0.5 transition-all duration-300">
                    <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    New User
                </button>
            </div>
        </div>

        {{-- 2. FILTER BAR --}}
        <x-admin.table.filter
            id="userFilter"
            placeholder="Search name, email, mobile, ref..."
            :show-date-filter="false"
            :show-export="true"
            export-route="admin.users.export"
        />

        {{-- 3. CONTENT AREA --}}
        <div class="relative min-h-[400px]">
            {{-- SKELETON LOADER --}}
            <div x-show="isLoading" x-transition.opacity class="bg-white border border-primary/10 rounded-[2rem] overflow-hidden shadow-xl shadow-primary/5 mb-8">
                <div class="animate-pulse">
                    <div class="h-16 bg-primary/5 border-b border-primary/5"></div>
                    @foreach(range(1, 5) as $i)
                        <div class="flex items-center px-8 py-5 border-b border-primary/5 gap-4">
                            <div class="h-12 w-12 rounded-2xl bg-navy"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-3 w-48 bg-navy rounded"></div>
                                <div class="h-2 w-20 bg-navy rounded"></div>
                            </div>
                            <div class="h-8 w-8 bg-navy rounded-xl ml-auto"></div>
                        </div>
                    @endforeach
                </div>
            </div>

            {{-- DATA TABLE --}}
            <div x-show="!isLoading" class="overflow-hidden rounded-[2rem] border border-primary/10 bg-white shadow-xl relative animate-fade-in mb-8">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-mutedText">
                        <thead class="bg-primary/5 text-[10px] uppercase font-black text-primary border-b border-primary/5 tracking-widest">
                            <tr>
                                <th class="px-8 py-6">User Profile</th>
                                <th class="px-6 py-6">Sponsor</th>
                                <th class="px-6 py-6">Role</th>
                                <th class="px-6 py-6 text-center">KYC</th>
                                <th class="px-6 py-6 text-center">Bank</th>
                                <th class="px-6 py-6 text-right">Earnings</th>
                                <th class="px-6 py-6 text-center">Status</th>
                                <th class="px-8 py-6 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="userTableBody" class="divide-y divide-primary/5">
                            @include('admin.users.partials.users_table', ['users' => $users])
                        </tbody>
                    </table>
                </div>

                {{-- Pagination Container --}}
                <div id="userPagination" class="px-8 py-6 bg-primary/5 border-t border-primary/5 flex items-center justify-between">
                    <x-admin.table.pagination :records="$users" />
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
                                    class="w-full rounded-2xl bg-navy/50 px-5 py-3.5 text-sm font-bold text-mainText focus:bg-white focus:border-primary outline-none">
                                    <option value="">Select Gender</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label
                                     class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-1.5 ml-1">Date of Birth</label>
                                <input type="date" x-model="form.dob"
                                    class="w-full rounded-2xl bg-navy/50 px-5 py-3.5 text-sm font-bold text-mainText focus:bg-white focus:border-primary outline-none">
                            </div>
                            <div>
                                <label
                                     class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-1.5 ml-1">State</label>
                                <select x-model="form.state_id"
                                    class="w-full rounded-2xl bg-navy/50 px-5 py-3.5 text-sm font-bold text-mainText focus:bg-white focus:border-primary outline-none">
                                    <option value="">Select State</option>
                                    <template x-for="state in states" :key="state.id">
                                        <option :value="state.id" x-text="state.name"></option>
                                    </template>
                                </select>
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
                            <!-- Leaderboard Control -->
                            <div class="col-span-2 border-t border-primary/5 pt-6 mt-2">
                                <label class="flex items-center gap-3 cursor-pointer group">
                                    <div class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2"
                                        :class="form.hide_from_leaderboard ? 'bg-secondary' : 'bg-navy/50'"
                                        @click="form.hide_from_leaderboard = !form.hide_from_leaderboard">
                                        <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                                            :class="form.hide_from_leaderboard ? 'translate-x-5' : 'translate-x-0'"></span>
                                    </div>
                                    <span class="text-xs font-bold text-mainText group-hover:text-secondary transition-colors">Hide User from Leaderboard</span>
                                </label>
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
                        <p class="text-xs font-bold text-primary tracking-widest uppercase mb-6" x-text="viewData.role"></p>

                        <div class="grid grid-cols-2 gap-4 text-left">
                            <div class="bg-navy p-5 rounded-2xl border border-primary/5">
                                <p class="text-[9px] font-black uppercase tracking-widest text-mutedText mb-1">Email</p>
                                <p class="text-xs font-bold text-mainText truncate" x-text="viewData.email"></p>
                            </div>
                            <div class="bg-navy p-5 rounded-2xl border border-primary/5">
                                <p class="text-[9px] font-black uppercase tracking-widest text-mutedText mb-1">Mobile</p>
                                <p class="text-xs font-bold text-mainText" x-text="viewData.mobile || '-'"></p>
                            </div>
                            <div class="bg-navy p-5 rounded-2xl border border-primary/5">
                                <p class="text-[9px] font-black uppercase tracking-widest text-mutedText mb-1">Gender</p>
                                <p class="text-xs font-bold text-mainText capitalize" x-text="viewData.gender || 'N/A'"></p>
                            </div>
                            <div class="bg-navy p-5 rounded-2xl border border-primary/5">
                                <p class="text-[9px] font-black uppercase tracking-widest text-mutedText mb-1">Date of Birth</p>
                                <p class="text-xs font-bold text-mainText" x-text="viewData.dob || 'N/A'"></p>
                            </div>
                             <div class="bg-navy p-5 rounded-2xl border border-primary/5">
                                 <p class="text-[9px] font-black uppercase tracking-widest text-mutedText mb-1">State</p>
                                 <p class="text-xs font-bold text-mainText" x-text="viewData.state_name || 'N/A'"></p>
                             </div>
                            <div class="bg-navy p-5 rounded-2xl border border-primary/5">
                                <p class="text-[9px] font-black uppercase tracking-widest text-mutedText mb-1">Joined On</p>
                                <p class="text-xs font-bold text-mainText" x-text="viewData.joined_at"></p>
                            </div>
                        </div>

                        {{-- Earnings & KYC --}}
                        <div class="mt-6 grid grid-cols-3 gap-3">
                            <div class="brand-gradient p-4 rounded-2xl text-left text-white shadow-lg shadow-primary/20">
                                <p class="text-[8px] font-black uppercase tracking-widest opacity-80 mb-0.5">Total Earnings</p>
                                <p class="text-base font-black">₹<span x-text="viewData.total_earnings"></span></p>
                            </div>
                            <div class="p-4 rounded-2xl text-left border shadow-sm flex flex-col justify-center"
                                :class="{
                                    'bg-green-50 border-green-200 text-green-600': viewData.kyc_status === 'verified',
                                    'bg-amber-50 border-amber-200 text-amber-600': viewData.kyc_status === 'pending',
                                    'bg-red-50 border-red-200 text-red-600': viewData.kyc_status === 'rejected',
                                    'bg-navy border-primary/5 text-mutedText': viewData.kyc_status === 'not_submitted'
                                }">
                                <p class="text-[8px] font-black uppercase tracking-widest opacity-80 mb-0.5">KYC Status</p>
                                <p class="text-[10px] font-black x-text" x-text="viewData.kyc_status.replace('_', ' ').toUpperCase()"></p>
                            </div>
                            <div class="p-4 rounded-2xl text-left border shadow-sm flex flex-col justify-center"
                                :class="{
                                    'bg-green-50 border-green-200 text-green-600': viewData.bank && viewData.bank.status === 'verified',
                                    'bg-amber-50 border-amber-200 text-amber-600': viewData.bank && viewData.bank.status === 'pending',
                                    'bg-red-50 border-red-200 text-red-600': viewData.bank && viewData.bank.status === 'rejected',
                                    'bg-navy border-primary/5 text-mutedText': !viewData.bank || viewData.bank.status === 'not_submitted'
                                }">
                                <p class="text-[8px] font-black uppercase tracking-widest opacity-80 mb-0.5">Bank Status</p>
                                <p class="text-[10px] font-black x-text" x-text="(viewData.bank ? viewData.bank.status : 'not_submitted').replace('_', ' ').toUpperCase()"></p>
                            </div>
                        </div>

                        {{-- Bank Details Section --}}
                        <div class="mt-8 pt-6 border-t border-primary/5 text-left">
                            <h3 class="text-xs font-black text-mainText uppercase tracking-widest mb-4 flex items-center gap-2">
                                <svg class="w-4 h-4 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                                Bank Account Details
                            </h3>

                            <template x-if="viewData.bank">
                                <div class="grid grid-cols-2 gap-y-4 gap-x-6 bg-navy/30 p-5 rounded-2xl border border-primary/5">
                                    <div>
                                        <p class="text-[9px] font-black uppercase tracking-widest text-mutedText/60 mb-0.5">Bank Name</p>
                                        <p class="text-xs font-bold text-mainText" x-text="viewData.bank.name"></p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black uppercase tracking-widest text-mutedText/60 mb-0.5">Account Holder</p>
                                        <p class="text-xs font-bold text-mainText" x-text="viewData.bank.holder"></p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black uppercase tracking-widest text-mutedText/60 mb-0.5">Account Number</p>
                                        <p class="text-xs font-bold text-mainText" x-text="viewData.bank.account"></p>
                                    </div>
                                    <div>
                                        <p class="text-[9px] font-black uppercase tracking-widest text-mutedText/60 mb-0.5">IFSC Code</p>
                                        <p class="text-xs font-bold text-mainText" x-text="viewData.bank.ifsc"></p>
                                    </div>
                                    <div class="col-span-2" x-show="viewData.bank.upi">
                                        <p class="text-[9px] font-black uppercase tracking-widest text-mutedText/60 mb-0.5">UPI ID</p>
                                        <p class="text-xs font-bold text-primary" x-text="viewData.bank.upi"></p>
                                    </div>
                                </div>
                            </template>

                            <template x-if="!viewData.bank">
                                <div class="bg-navy p-6 rounded-2xl border border-dashed border-primary/20 text-center">
                                    <p class="text-xs font-bold text-mutedText/50 italic text-center">No bank details added or verified yet.</p>
                                </div>
                            </template>
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
                perPage: 20,
                viewTrash: false,
                startDate: '',
                endDate: '',
                lastUrl: "{{ route('admin.users.index') }}",
                showModal: false,
                viewModal: false,
                modalMode: 'create',
                isSubmitting: false,
                viewData: {},
                controller: null,
                states: @json($states),
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
                    kyc_status: 'not_submitted',
                    hide_from_leaderboard: false
                },

                init() {
                    this.fetchUsers();

                    window.addEventListener('filter-applied', (e) => {
                        if (e.detail.id === 'userFilter') this.fetchUsers();
                    });

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

                goToPage(url) {
                    if (url) {
                        this.lastUrl = url;
                        this.fetchUsers(url);
                    }
                },

                toggleTrash(status) {
                    this.viewTrash = status;
                    this.fetchUsers("{{ route('admin.users.index') }}");
                },

                updateTable() {
                    this.fetchUsers("{{ route('admin.users.index') }}");
                },

                async fetchUsers(url = null) {
                    let targetUrlRaw = url || this.lastUrl;
                    this.lastUrl = targetUrlRaw;

                    if (this.controller) this.controller.abort();
                    this.controller = new AbortController();

                    this.isLoading = true;
                    try {
                        let targetUrl = new URL(targetUrlRaw.includes('http') ? targetUrlRaw : window.location.origin + targetUrlRaw);

                        targetUrl.searchParams.set('trash', this.viewTrash);
                        targetUrl.searchParams.set('search', this.search || '');
                        targetUrl.searchParams.set('per_page', this.perPage || 20);
                        targetUrl.searchParams.set('start_date', this.startDate || '');
                        targetUrl.searchParams.set('end_date', this.endDate || '');
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
                            document.getElementById('userTableBody').innerHTML = result.table;
                            document.getElementById('userPagination').innerHTML = result.pagination;
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
                            dob: user.dob ? user.dob.split('T')[0] : '',
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
                        kyc_status: 'not_submitted',
                        hide_from_leaderboard: false
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
                            await this.fetchUsers();
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
                                    await this.fetchUsers();
                                    this.Toast.fire({
                                        icon: 'success',
                                        title: res.message
                                    });
                                } else {
                                    this.Toast.fire({
                                        icon: 'error',
                                        title: res.message || 'Action failed'
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
                                    await this.fetchUsers();
                                    this.Toast.fire({
                                        icon: 'success',
                                        title: res.message
                                    });
                                } else {
                                    this.Toast.fire({
                                        icon: 'error',
                                        title: res.message || 'Action failed'
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
