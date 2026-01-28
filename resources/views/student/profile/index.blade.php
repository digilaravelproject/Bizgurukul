@extends('layouts.app')

@section('content')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<div x-data="studentProfile()" x-init="init()" class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 font-sans">

    <div class="md:grid md:grid-cols-4 md:gap-6">

        {{-- Sidebar Tabs --}}
        <div class="md:col-span-1">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg sticky top-6">
                <div class="p-4 bg-slate-50 border-b border-slate-100 flex items-center gap-3">
                    <div class="h-12 w-12 rounded-full bg-indigo-600 flex items-center justify-center text-white font-bold text-xl">
                        {{ substr($user->name, 0, 1) }}
                    </div>
                    <div class="overflow-hidden">
                        <h3 class="font-bold text-slate-800 truncate">{{ $user->name }}</h3>
                        <p class="text-xs text-slate-500 truncate">{{ $user->email }}</p>
                    </div>
                </div>
                <nav class="flex flex-col p-2 space-y-1">
                    <button @click="activeTab = 'profile'"
                        :class="activeTab === 'profile' ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50'"
                        class="px-4 py-3 rounded-md text-sm font-medium text-left flex items-center gap-3 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Edit Profile
                    </button>

                    <button @click="activeTab = 'kyc'"
                        :class="activeTab === 'kyc' ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50'"
                        class="px-4 py-3 rounded-md text-sm font-medium text-left flex items-center gap-3 transition justify-between">
                        <div class="flex items-center gap-3">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            KYC Verification
                        </div>
                        {{-- Status Dot --}}
                        <span class="h-2.5 w-2.5 rounded-full"
                            :class="{
                                'bg-red-500': '{{ $user->kyc_status }}' == 'rejected',
                                'bg-green-500': '{{ $user->kyc_status }}' == 'verified',
                                'bg-amber-500': '{{ $user->kyc_status }}' == 'pending',
                                'bg-slate-300': '{{ $user->kyc_status }}' == 'not_submitted'
                            }"></span>
                    </button>

                    <button @click="activeTab = 'bank'"
                        :class="activeTab === 'bank' ? 'bg-indigo-50 text-indigo-700' : 'text-slate-600 hover:bg-slate-50'"
                        class="px-4 py-3 rounded-md text-sm font-medium text-left flex items-center gap-3 transition">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path></svg>
                        Bank Details
                    </button>
                </nav>
            </div>
        </div>

        {{-- Content Area --}}
        <div class="mt-6 md:mt-0 md:col-span-3">
            <div class="bg-white shadow-sm sm:rounded-lg border border-slate-200 p-6 min-h-[500px]">

                {{-- 1. Custom Profile Edit --}}
                <div x-show="activeTab === 'profile'" x-transition.opacity>
                    <h2 class="text-xl font-bold text-slate-800 mb-1">Personal Information</h2>
                    <p class="text-sm text-slate-500 mb-6">Update your personal details and contact info.</p>

                    <form @submit.prevent="updateProfile">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            {{-- Name --}}
                            <div class="col-span-2 md:col-span-1">
                                <label class="block text-sm font-bold text-slate-600 mb-1">Full Name</label>
                                <input type="text" x-model="profile.name" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500">
                            </div>
                            {{-- Email --}}
                            <div class="col-span-2 md:col-span-1">
                                <label class="block text-sm font-bold text-slate-600 mb-1">Email Address</label>
                                <input type="email" x-model="profile.email" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500">
                            </div>
                            {{-- Mobile --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-600 mb-1">Mobile Number</label>
                                <input type="text" x-model="profile.mobile" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500">
                            </div>
                            {{-- Gender --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-600 mb-1">Gender</label>
                                <select x-model="profile.gender" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500">
                                    <option value="">Select</option>
                                    <option value="male">Male</option>
                                    <option value="female">Female</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            {{-- DOB --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-600 mb-1">Date of Birth</label>
                                <input type="date" x-model="profile.dob" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500">
                            </div>
                            {{-- State --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-600 mb-1">State</label>
                                <select x-model="profile.state_id" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500">
                                    <template x-for="(state, index) in indianStates" :key="index">
                                        <option :value="index + 1" x-text="state"></option>
                                    </template>
                                </select>
                            </div>
                            {{-- City --}}
                            <div>
                                <label class="block text-sm font-bold text-slate-600 mb-1">City</label>
                                <input type="text" x-model="profile.city" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500">
                            </div>
                             {{-- Password (Optional) --}}
                             <div>
                                <label class="block text-sm font-bold text-slate-600 mb-1">New Password (Optional)</label>
                                <input type="password" x-model="profile.password" placeholder="Leave blank to keep current" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500">
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end">
                            <button type="submit" :disabled="isLoading" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-bold transition flex items-center shadow-md">
                                <svg x-show="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>

                {{-- 2. KYC Verification --}}
                <div x-show="activeTab === 'kyc'" x-transition.opacity>
                    <h2 class="text-xl font-bold text-slate-800 mb-1">KYC Verification</h2>
                    <p class="text-sm text-slate-500 mb-6">Submit your documents to verify your identity.</p>

                     {{-- REJECTED STATE --}}
                    @if($user->kyc_status === 'rejected')
                        <div class="bg-red-50 border-l-4 border-red-500 p-4 mb-6 rounded-r-lg">
                            <div class="flex">
                                <div class="flex-shrink-0"><svg class="h-5 w-5 text-red-500" viewBox="0 0 20 20" fill="currentColor"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"/></svg></div>
                                <div class="ml-3">
                                    <h3 class="text-sm font-bold text-red-800">Application Rejected</h3>
                                    <p class="text-sm text-red-700 mt-1">Admin Note: <span class="font-bold">{{ $user->kyc->admin_note }}</span></p>
                                    <button @click="reapplyMode = true" class="text-red-700 underline text-xs mt-2 font-bold hover:text-red-900">Click here to Re-apply</button>
                                </div>
                            </div>
                        </div>
                    @endif

                    {{-- VERIFIED STATE --}}
                    @if($user->kyc_status === 'verified')
                        <div class="text-center py-12 bg-green-50 rounded-xl border border-green-100">
                            <svg class="w-16 h-16 text-green-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h3 class="text-lg font-bold text-green-700">KYC Verified Successfully</h3>
                            <p class="text-green-600 text-sm">You are a verified member.</p>
                            <div class="mt-4 text-left inline-block bg-white p-4 rounded shadow-sm border border-green-200">
                                <p class="text-xs text-slate-400 font-bold uppercase">Pan Name</p>
                                <p class="font-bold text-slate-800">{{ $user->kyc->pan_name }}</p>
                            </div>
                        </div>
                    @endif

                    {{-- PENDING STATE --}}
                    @if($user->kyc_status === 'pending')
                         <div class="text-center py-12 bg-amber-50 rounded-xl border border-amber-100">
                            <svg class="w-16 h-16 text-amber-500 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            <h3 class="text-lg font-bold text-amber-700">Verification Pending</h3>
                            <p class="text-amber-600 text-sm">We are reviewing your documents. This usually takes 24-48 hours.</p>
                        </div>
                    @endif

                    {{-- FORM (Show if Not Submitted OR Reapplying) --}}
                    <div x-show="'{{ $user->kyc_status }}' == 'not_submitted' || reapplyMode" class="mt-4">
                        <form @submit.prevent="submitKyc">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-bold text-slate-600 mb-1">Full Name (As per PAN)</label>
                                    <input type="text" x-model="kyc.pan_name" required class="w-full rounded-lg border-slate-300 focus:ring-indigo-500">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-600 mb-1">Upload PAN Card / Document</label>
                                    <div class="flex items-center justify-center w-full">
                                        <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-slate-300 border-dashed rounded-lg cursor-pointer bg-slate-50 hover:bg-slate-100">
                                            <div class="flex flex-col items-center justify-center pt-5 pb-6">
                                                <svg class="w-8 h-8 mb-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                                <p class="text-sm text-slate-500"><span class="font-semibold">Click to upload</span> or drag and drop</p>
                                                <p class="text-xs text-slate-500">SVG, PNG, JPG or PDF (MAX. 2MB)</p>
                                            </div>
                                            <input type="file" @change="handleFile" class="hidden" required />
                                        </label>
                                    </div>
                                    <div x-show="kyc.file" class="mt-2 text-sm text-green-600 font-bold flex items-center">
                                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                                        File Selected
                                    </div>
                                </div>
                            </div>
                            <div class="mt-6">
                                <button type="submit" :disabled="isLoading" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-bold transition w-full shadow-md">
                                    Submit for Verification
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                {{-- 3. Bank Details --}}
                <div x-show="activeTab === 'bank'" x-transition.opacity>
                    <h2 class="text-xl font-bold text-slate-800 mb-1">Bank Details</h2>
                    <p class="text-sm text-slate-500 mb-6">For payouts and transactions.</p>

                    <form @submit.prevent="submitBank">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="col-span-2">
                                <label class="block text-sm font-bold text-slate-600 mb-1">Account Holder Name</label>
                                <input type="text" x-model="bank.holder_name" required class="w-full rounded-lg border-slate-300 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-600 mb-1">Account Number</label>
                                <input type="password" x-model="bank.account_number" required class="w-full rounded-lg border-slate-300 focus:ring-indigo-500">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-600 mb-1">Confirm Account Number</label>
                                <input type="text" x-model="bank.account_number_confirmation" required class="w-full rounded-lg border-slate-300 focus:ring-indigo-500">
                            </div>
                             <div>
                                <label class="block text-sm font-bold text-slate-600 mb-1">Bank Name</label>
                                <input type="text" x-model="bank.bank_name" required class="w-full rounded-lg border-slate-300 focus:ring-indigo-500">
                            </div>
                             <div>
                                <label class="block text-sm font-bold text-slate-600 mb-1">IFSC Code</label>
                                <input type="text" x-model="bank.ifsc_code" required class="w-full rounded-lg border-slate-300 focus:ring-indigo-500 uppercase">
                            </div>
                             <div>
                                <label class="block text-sm font-bold text-slate-600 mb-1">UPI ID (Optional)</label>
                                <input type="text" x-model="bank.upi_id" class="w-full rounded-lg border-slate-300 focus:ring-indigo-500">
                            </div>
                        </div>
                        <div class="mt-8 flex justify-end">
                            <button type="submit" :disabled="isLoading" class="bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-2.5 rounded-lg font-bold transition shadow-md">
                                Save Bank Details
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </div>
</div>

<script>
    function studentProfile() {
        return {
            activeTab: 'profile',
            isLoading: false,
            reapplyMode: false,

            indianStates: ["Andaman and Nicobar Islands", "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar", "Chandigarh", "Chhattisgarh", "Dadra and Nagar Haveli", "Daman and Diu", "Delhi", "Goa", "Gujarat", "Haryana", "Himachal Pradesh", "Jammu and Kashmir", "Jharkhand", "Karnataka", "Kerala", "Ladakh", "Lakshadweep", "Madhya Pradesh", "Maharashtra", "Manipur", "Meghalaya", "Mizoram", "Nagaland", "Odisha", "Puducherry", "Punjab", "Rajasthan", "Sikkim", "Tamil Nadu", "Telangana", "Tripura", "Uttar Pradesh", "Uttarakhand", "West Bengal"],

            // Profile Data
            profile: {
                name: '{{ $user->name }}',
                email: '{{ $user->email }}',
                mobile: '{{ $user->mobile }}',
                gender: '{{ $user->gender }}',
                dob: '{{ $user->dob ? $user->dob->format("Y-m-d") : "" }}',
                state_id: '{{ $user->state_id }}',
                city: '{{ $user->city }}',
                password: ''
            },

            // KYC Data
            kyc: { pan_name: '', file: null },

            // Bank Data
            bank: {
                holder_name: '{{ $user->bank->account_holder_name ?? "" }}',
                account_number: '{{ $user->bank->account_number ?? "" }}',
                account_number_confirmation: '{{ $user->bank->account_number ?? "" }}',
                bank_name: '{{ $user->bank->bank_name ?? "" }}',
                ifsc_code: '{{ $user->bank->ifsc_code ?? "" }}',
                upi_id: '{{ $user->bank->upi_id ?? "" }}'
            },

            init() {},

            handleFile(e) { this.kyc.file = e.target.files[0]; },

            updateProfile() {
                this.isLoading = true;
                axios.post("{{ route('student.profile.update') }}", this.profile)
                    .then(res => Swal.fire('Updated!', res.data.message, 'success'))
                    .catch(err => Swal.fire('Error', 'Check fields', 'error'))
                    .finally(() => this.isLoading = false);
            },

            submitKyc() {
                if(!this.kyc.file) return Swal.fire('Error', 'Upload Document', 'error');

                this.isLoading = true;
                let fd = new FormData();
                fd.append('pan_name', this.kyc.pan_name);
                fd.append('document', this.kyc.file);

                axios.post("{{ route('student.kyc.submit') }}", fd)
                    .then(res => Swal.fire('Submitted', res.data.message, 'success').then(() => location.reload()))
                    .catch(err => Swal.fire('Error', err.response.data.message, 'error'))
                    .finally(() => this.isLoading = false);
            },

            submitBank() {
                if(this.bank.account_number !== this.bank.account_number_confirmation) return Swal.fire('Error', 'Account numbers do not match', 'error');

                this.isLoading = true;
                axios.post("{{ route('student.bank.save') }}", this.bank)
                    .then(res => Swal.fire('Saved!', res.data.message, 'success'))
                    .catch(err => Swal.fire('Error', 'Failed to save', 'error'))
                    .finally(() => this.isLoading = false);
            }
        }
    }
</script>
@endsection
