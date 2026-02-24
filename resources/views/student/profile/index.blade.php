@extends('layouts.user.app')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div x-data="studentProfile()" x-init="init()" class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8 font-sans">

        <div class="md:grid md:grid-cols-4 md:gap-6">

            {{-- Sidebar Tabs --}}
            <div class="md:col-span-1">
                <div class="bg-customWhite overflow-hidden shadow-sm rounded-3xl sticky top-6 border border-primary/10">
                    <div class="p-4 bg-navy/5 border-b border-primary/5 flex items-center gap-3">
                        <div
                            class="h-12 w-12 rounded-xl bg-primary text-white flex items-center justify-center font-bold text-xl shadow-md">
                            {{ substr($user->name, 0, 1) }}
                        </div>
                        <div class="overflow-hidden">
                            <h3 class="font-bold text-mainText truncate">{{ $user->name }}</h3>
                            <p class="text-xs text-mutedText truncate">{{ $user->email }}</p>
                        </div>
                    </div>
                    <nav class="flex flex-col p-2 space-y-1">
                        <button @click="activeTab = 'profile'"
                            :class="activeTab === 'profile' ? 'bg-primary/10 text-primary' : 'text-mutedText hover:bg-navy/5'"
                            class="px-4 py-3 rounded-xl text-sm font-bold text-left flex items-center gap-3 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                            </svg>
                            Edit Profile
                        </button>

                        <button @click="activeTab = 'kyc'"
                            :class="activeTab === 'kyc' ? 'bg-primary/10 text-primary' : 'text-mutedText hover:bg-navy/5'"
                            class="px-4 py-3 rounded-xl text-sm font-bold text-left flex items-center gap-3 transition justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                KYC Verification
                            </div>
                            {{-- Status Dot --}}
                            <span class="h-2.5 w-2.5 rounded-full"
                                :class="{
                                    'bg-red-500': kycStatus == 'rejected',
                                    'bg-emerald-500': kycStatus == 'verified',
                                    'bg-amber-500': kycStatus == 'pending',
                                    'bg-slate-300': kycStatus == 'not_submitted'
                                }"></span>
                        </button>

                        <button @click="activeTab = 'bank'"
                            :class="activeTab === 'bank' ? 'bg-primary/10 text-primary' : 'text-mutedText hover:bg-navy/5'"
                            class="px-4 py-3 rounded-xl text-sm font-bold text-left flex items-center gap-3 transition justify-between">
                            <div class="flex items-center gap-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z">
                                    </path>
                                </svg>
                                Bank Details
                            </div>
                            {{-- Status Dot --}}
                            <span class="h-2.5 w-2.5 rounded-full"
                                :class="{
                                    'bg-red-500': bankStatus == 'rejected',
                                    'bg-emerald-500': bankStatus == 'verified',
                                    'bg-amber-500': bankStatus == 'pending' || hasUpdatePending,
                                    'bg-slate-300': bankStatus == 'not_submitted'
                                }"></span>
                        </button>

                        <button @click="activeTab = 'password'"
                            :class="activeTab === 'password' ? 'bg-primary/10 text-primary' : 'text-mutedText hover:bg-navy/5'"
                            class="px-4 py-3 rounded-xl text-sm font-bold text-left flex items-center gap-3 transition">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z">
                                </path>
                            </svg>
                            Change Password
                        </button>
                    </nav>
                </div>
            </div>

            {{-- Content Area --}}
            <div class="mt-6 md:mt-0 md:col-span-3">
                <div class="bg-customWhite shadow-sm rounded-3xl border border-primary/10 p-6 min-h-[500px]">

                    {{-- Sponsor Details --}}
                    @if($user->referrer)
                    <div class="mb-8 p-6 bg-navy/5 rounded-[2rem] border border-primary/10 relative overflow-hidden group">
                        <div class="absolute top-0 right-0 p-4 opacity-10 group-hover:opacity-20 transition-opacity">
                            <i class="fas fa-user-friends text-5xl text-primary"></i>
                        </div>
                        <h3 class="text-[10px] font-black uppercase tracking-[0.2em] text-primary mb-4 flex items-center gap-2">
                             <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                             Your Sponsor Details
                        </h3>
                        <div class="flex flex-col md:flex-row md:items-center gap-6">
                            <div class="flex items-center gap-4">
                                <div class="w-12 h-12 rounded-2xl bg-primary/10 flex items-center justify-center text-primary font-black border border-primary/20">
                                    {{ substr($user->referrer->name, 0, 1) }}
                                </div>
                                <div>
                                    <p class="text-xs font-black text-mutedText uppercase tracking-widest">Sponsor Name</p>
                                    <p class="text-sm font-bold text-mainText">{{ $user->referrer->name }}</p>
                                </div>
                            </div>
                            <div class="h-8 w-[1px] bg-primary/10 hidden md:block"></div>
                            <div>
                                <p class="text-xs font-black text-mutedText uppercase tracking-widest">Email Address</p>
                                <p class="text-sm font-bold text-mainText">{{ $user->referrer->email }}</p>
                            </div>
                            <div class="h-8 w-[1px] bg-primary/10 hidden md:block"></div>
                            <div>
                                <p class="text-xs font-black text-mutedText uppercase tracking-widest">Mobile Number</p>
                                <p class="text-sm font-bold text-mainText">
                                    {{ substr($user->referrer->mobile, 0, 1) . str_repeat('X', 6) . substr($user->referrer->mobile, -1) }}
                                </p>
                            </div>
                        </div>
                    </div>
                    @endif

                    {{-- 1. Custom Profile Edit --}}
                    <div x-show="activeTab === 'profile'" x-transition.opacity>
                        <h2 class="text-xl font-bold text-mainText mb-1">Personal Information</h2>
                        <p class="text-sm text-mutedText mb-6">Update your account preferences. Core details are locked for security.</p>

                        <form @submit.prevent="updateProfile">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                {{-- Name (Locked) --}}
                                <div class="col-span-2 md:col-span-1">
                                    <label class="text-sm font-bold text-mutedText mb-1 flex items-center gap-2">
                                        Full Name
                                        <i class="fas fa-lock text-[10px] text-primary/50"></i>
                                    </label>
                                    <input type="text" x-model="profile.name" readonly
                                        class="w-full rounded-xl bg-navy/30 border-primary/5 text-mutedText/60 cursor-not-allowed focus:ring-0 focus:border-primary/5">
                                </div>

                                {{-- Email Address (Locked) --}}
                                <div class="col-span-2 md:col-span-1">
                                    <label class="text-sm font-bold text-mutedText mb-1 flex items-center gap-2">
                                        Email Address
                                        <i class="fas fa-lock text-[10px] text-primary/50"></i>
                                    </label>
                                    <input type="email" x-model="profile.email" readonly
                                        class="w-full rounded-xl bg-navy/30 border-primary/5 text-mutedText/60 cursor-not-allowed focus:ring-0 focus:border-primary/5">
                                </div>

                                {{-- Mobile (Locked) --}}
                                <div>
                                    <label class="text-sm font-bold text-mutedText mb-1 flex items-center gap-2">
                                        Mobile Number
                                        <i class="fas fa-lock text-[10px] text-primary/50"></i>
                                    </label>
                                    <input type="text" x-model="profile.mobile" readonly
                                        class="w-full rounded-xl bg-navy/30 border-primary/5 text-mutedText/60 cursor-not-allowed focus:ring-0 focus:border-primary/5">
                                </div>

                                {{-- DOB (Locked) --}}
                                <div>
                                    <label class="text-sm font-bold text-mutedText mb-1 flex items-center gap-2">
                                        Date of Birth
                                        <i class="fas fa-lock text-[10px] text-primary/50"></i>
                                    </label>
                                    <input type="date" x-model="profile.dob" readonly
                                        class="w-full rounded-xl bg-navy/30 border-primary/5 text-mutedText/60 cursor-not-allowed focus:ring-0 focus:border-primary/5">
                                </div>

                                {{-- Gender --}}
                                <div>
                                    <label class="block text-sm font-bold text-mutedText mb-1">Gender</label>
                                    <select x-model="profile.gender"
                                        class="w-full rounded-xl bg-navy border-primary/10 text-mainText focus:ring-primary focus:border-primary">
                                        <option value="">Select</option>
                                        <option value="male">Male</option>
                                        <option value="female">Female</option>
                                        <option value="other">Other</option>
                                    </select>
                                </div>
                                {{-- State --}}
                                <div>
                                    <label class="block text-sm font-bold text-mutedText mb-1">State</label>
                                    <select x-model="profile.state_id"
                                        class="w-full rounded-xl bg-navy border-primary/10 text-mainText focus:ring-primary focus:border-primary">
                                        <template x-for="(state, index) in indianStates" :key="index">
                                            <option :value="index + 1" x-text="state" :selected="profile.state_id == (index+1)"></option>
                                        </template>
                                    </select>
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end">
                                <button type="submit" :disabled="isLoading"
                                    class="bg-primary hover:bg-primary/90 text-white px-6 py-2.5 rounded-xl font-bold transition flex items-center shadow-lg shadow-primary/20">
                                    <svg x-show="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Update Profile
                                </button>
                            </div>
                        </form>
                    </div>

                    {{-- 2. KYC Verification --}}
                    <div x-show="activeTab === 'kyc'" x-transition.opacity>
                        <h2 class="text-xl font-bold text-mainText mb-1">KYC Verification</h2>
                        <p class="text-sm text-mutedText mb-6">Submit your documents to verify your identity on the platform.</p>

                        {{-- REJECTED STATE --}}
                        @if ($user->kyc_status === 'rejected')
                            <div class="bg-red-500/10 border-l-4 border-red-500 p-4 mb-6 rounded-r-xl">
                                <div class="flex">
                                    <div class="flex-shrink-0"><i class="fas fa-exclamation-circle text-red-500"></i></div>
                                    <div class="ml-3">
                                        <h3 class="text-sm font-bold text-red-500">KYC Rejected</h3>
                                        <p class="text-sm text-red-400 mt-1">Reason: <span
                                                class="font-bold">{{ $user->kyc->admin_note }}</span></p>
                                        <button @click="reapplyMode = true"
                                            class="text-red-500 underline text-xs mt-2 font-bold hover:text-red-600">Click
                                            here to Re-apply</button>
                                    </div>
                                </div>
                            </div>
                        @endif

                        {{-- VERIFIED STATE --}}
                        @if ($user->kyc_status === 'verified')
                            <div class="text-center py-12 bg-emerald-500/5 rounded-2xl border border-emerald-500/10">
                                <i class="fas fa-check-circle text-emerald-500 text-5xl mb-4"></i>
                                <h3 class="text-lg font-bold text-emerald-600">KYC Verified</h3>
                                <p class="text-emerald-500 text-sm">Your identity has been successfully verified.</p>
                                <div class="mt-4 text-left inline-block bg-customWhite p-4 rounded-xl shadow-sm border border-emerald-500/20">
                                    <p class="text-xs text-mutedText font-bold uppercase">Name on Document</p>
                                    <p class="font-bold text-mainText">{{ $user->kyc->pan_name }}</p>
                                </div>
                            </div>
                        @endif

                        {{-- PENDING STATE --}}
                        @if ($user->kyc_status === 'pending')
                            <div class="text-center py-12 bg-amber-500/5 rounded-2xl border border-amber-500/10">
                                <i class="fas fa-clock text-amber-500 text-5xl mb-4"></i>
                                <h3 class="text-lg font-bold text-amber-600">Verification Pending</h3>
                                <p class="text-amber-500 text-sm">Our team is currently reviewing your documents.</p>
                            </div>
                        @endif

                        {{-- FORM (Show if Not Submitted OR Reapplying) --}}
                        <div x-show="'{{ $user->kyc_status }}' == 'not_submitted' || reapplyMode" class="mt-4">
                            <form @submit.prevent="submitKyc">
                                <div class="space-y-4">
                                    <div>
                                        <label class="block text-sm font-bold text-mutedText mb-1">Full Name (As per ID Document)</label>
                                        <input type="text" x-model="kyc.pan_name" required
                                            class="w-full rounded-xl bg-navy border-primary/10 text-mainText focus:ring-primary focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-mutedText mb-1">Upload ID Proof (PAN / Aadhar)</label>
                                        <div class="flex items-center justify-center w-full">
                                            <label class="flex flex-col items-center justify-center w-full h-32 border-2 border-primary/10 border-dashed rounded-xl cursor-pointer bg-navy/5 hover:bg-navy/10 transition-colors">
                                                <div class="flex flex-col items-center justify-center pt-5 pb-6 text-center px-4">
                                                    <i class="fas fa-cloud-upload-alt text-2xl text-mutedText mb-2"></i>
                                                    <p class="text-sm text-mutedText"><span class="font-semibold text-primary">Click to upload</span> or drag and drop</p>
                                                    <p class="text-xs text-mutedText/70 mt-1">Supported: JPG, PNG, PDF (Max 3MB)</p>
                                                </div>
                                                <input type="file" @change="handleKycFile" class="hidden" required />
                                            </label>
                                        </div>
                                        <div x-show="kyc.file" class="mt-2 text-sm text-emerald-500 font-bold flex items-center">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            <span x-text="kyc.file.name"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-6">
                                    <button type="submit" :disabled="isLoading"
                                        class="bg-primary hover:bg-primary/90 text-white px-6 py-2.5 rounded-xl font-bold transition w-full shadow-lg shadow-primary/20">
                                        Submit for Verification
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>

                    {{-- 3. Bank Details --}}
                    <div x-show="activeTab === 'bank'" x-transition.opacity>
                        <h2 class="text-xl font-bold text-mainText mb-1">Bank Account Details</h2>
                        <p class="text-sm text-mutedText mb-6">Manage your payout settings. Changes require document proof and admin approval.</p>

                        @php
                            $currentBank = $user->bank;
                            $dbBankStatus = $currentBank->status ?? 'not_submitted';
                            $hasPendingUpdate = $user->bankUpdateRequests()->where('status', 'pending')->exists();
                        @endphp

                        {{-- Status Banners --}}
                        @if($dbBankStatus === 'verified' && !$hasPendingUpdate)
                            <div class="mb-8 p-6 bg-emerald-500/5 rounded-2xl border border-emerald-500/10 flex justify-between items-center">
                                <div>
                                    <h3 class="text-emerald-600 font-bold mb-1">Verified Account</h3>
                                    <p class="text-sm text-emerald-500">Your bank details are approved. Commission will be sent here.</p>
                                </div>
                                <button @click="changeBankMode = !changeBankMode"
                                    class="bg-navy text-mainText px-4 py-2 rounded-xl text-sm font-bold border border-primary/10 hover:bg-navy/50 transition"
                                    x-text="changeBankMode ? 'Cancel Edit' : 'Change Details'">
                                </button>
                            </div>
                        @elseif($dbBankStatus === 'pending' || $hasPendingUpdate)
                            <div class="mb-8 p-6 bg-amber-500/5 rounded-2xl border border-amber-500/10">
                                <h3 class="text-amber-600 font-bold mb-1">Approval Pending</h3>
                                <p class="text-sm text-amber-500">Your details are under review. Verification usually takes 24 hours.</p>
                                @if($hasPendingUpdate)
                                    <p class="mt-2 text-xs text-amber-400 font-bold italic">* You have a pending update request.</p>
                                @endif
                            </div>
                        @elseif($currentBank && $dbBankStatus === 'rejected')
                            <div class="mb-8 p-6 bg-red-500/10 rounded-2xl border border-red-500/20">
                                <h3 class="text-red-500 font-bold mb-1">Verification Rejected</h3>
                                <p class="text-sm text-red-400">Reason: {{ $currentBank->admin_note }}</p>
                                <button @click="changeBankMode = true" class="mt-3 text-red-500 underline text-xs font-bold">Try again with correct details</button>
                            </div>
                        @endif

                        {{-- 1. Submission Form --}}
                        <div x-show="changeBankMode || bankStatus === 'not_submitted' || bankStatus === 'rejected'"
                            x-cloak class="space-y-6">
                            <form @submit.prevent="submitBank">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div class="col-span-2">
                                        <label class="block text-sm font-bold text-mutedText mb-1">Account Holder Name (As per Bank)</label>
                                        <input type="text" x-model="bank.holder_name" required
                                            class="w-full rounded-xl bg-navy border-primary/10 text-mainText focus:ring-primary focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-mutedText mb-1">Account Number</label>
                                        <input type="password" x-model="bank.account_number" required
                                            class="w-full rounded-xl bg-navy border-primary/10 text-mainText focus:ring-primary focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-mutedText mb-1">Confirm Account Number</label>
                                        <input type="text" x-model="bank.account_number_confirmation" required
                                            class="w-full rounded-xl bg-navy border-primary/10 text-mainText focus:ring-primary focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-mutedText mb-1">Bank Name</label>
                                        <input type="text" x-model="bank.bank_name" required
                                            class="w-full rounded-xl bg-navy border-primary/10 text-mainText focus:ring-primary focus:border-primary">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-bold text-mutedText mb-1">IFSC Code</label>
                                        <input type="text" x-model="bank.ifsc_code" required
                                            class="w-full rounded-xl bg-navy border-primary/10 text-mainText focus:ring-primary focus:border-primary uppercase">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-sm font-bold text-mutedText mb-1">UPI ID (Optional)</label>
                                        <input type="text" x-model="bank.upi_id"
                                            class="w-full rounded-xl bg-navy border-primary/10 text-mainText focus:ring-primary focus:border-primary">
                                    </div>
                                    <div class="col-span-2">
                                        <label class="block text-sm font-bold text-mutedText mb-1">Upload Bank Proof (Passbook/Cheque/Statement)</label>
                                        <div class="flex items-center justify-center w-full">
                                            <label class="flex flex-col items-center justify-center w-full h-24 border-2 border-primary/10 border-dashed rounded-xl cursor-pointer bg-navy/5 hover:bg-navy/10 transition-colors">
                                                <div class="flex items-center gap-3">
                                                    <i class="fas fa-file-invoice text-mutedText"></i>
                                                    <p class="text-sm text-mutedText">Click to upload account proof</p>
                                                </div>
                                                <input type="file" @change="handleBankFile" class="hidden" required />
                                            </label>
                                        </div>
                                        <div x-show="bank.file" class="mt-2 text-sm text-emerald-500 font-bold flex items-center">
                                            <i class="fas fa-check-circle mr-1"></i>
                                            <span x-text="bank.file.name"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="mt-8 flex justify-end gap-4">
                                    <button type="submit" :disabled="isLoading"
                                        class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-xl font-bold transition shadow-lg shadow-primary/20">
                                        <span x-text="bankStatus === 'not_submitted' || bankStatus === 'rejected' ? 'Submit for Verification' : 'Update & Request Approval'"></span>
                                    </button>
                                </div>
                            </form>
                        </div>

                        {{-- 2. Active Details View --}}
                        @if($currentBank && ($dbBankStatus === 'verified' || $dbBankStatus === 'pending'))
                            <div x-show="!changeBankMode && bankStatus !== 'not_submitted' && bankStatus !== 'rejected'"
                                x-cloak class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div class="bg-navy/5 p-4 rounded-2xl border border-primary/5">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-mutedText mb-1">Account Holder</p>
                                    <p class="font-bold text-mainText">{{ $currentBank->account_holder_name }}</p>
                                </div>
                                <div class="bg-navy/5 p-4 rounded-2xl border border-primary/5">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-mutedText mb-1">Bank Name</p>
                                    <p class="font-bold text-mainText">{{ $currentBank->bank_name }}</p>
                                </div>
                                <div class="bg-navy/5 p-4 rounded-2xl border border-primary/5">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-mutedText mb-1">Account Number</p>
                                    <p class="font-bold text-mainText">****{{ substr($currentBank->account_number, -4) }}</p>
                                </div>
                                <div class="bg-navy/5 p-4 rounded-2xl border border-primary/5">
                                    <p class="text-[10px] uppercase font-black tracking-widest text-mutedText mb-1">IFSC</p>
                                    <p class="font-bold text-mainText">{{ $currentBank->ifsc_code }}</p>
                                </div>
                            </div>
                        @elseif($dbBankStatus === 'not_submitted')
                            <div x-show="!changeBankMode && bankStatus === 'not_submitted'" class="text-center py-10 bg-navy/5 rounded-2xl border border-dashed border-primary/10">
                                <i class="fas fa-university text-3xl text-mutedText/30 mb-3"></i>
                                <p class="text-mutedText">No bank details submitted yet.</p>
                            </div>
                        @endif
                    </div>

                    {{-- 4. Change Password --}}
                    <div x-show="activeTab === 'password'" x-transition.opacity>
                        <h2 class="text-xl font-bold text-mainText mb-1">Change Password</h2>
                        <p class="text-sm text-mutedText mb-6">Update your account password for security.</p>

                        <form @submit.prevent="changePassword">
                            <div class="space-y-6 max-w-md">
                                <div>
                                    <label class="block text-sm font-bold text-mutedText mb-1">Current Password</label>
                                    <input type="password" x-model="passwordData.current_password" required
                                        class="w-full rounded-xl bg-navy border-primary/10 text-mainText focus:ring-primary focus:border-primary"
                                        placeholder="Enter current password">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-mutedText mb-1">New Password</label>
                                    <input type="password" x-model="passwordData.new_password" required
                                        class="w-full rounded-xl bg-navy border-primary/10 text-mainText focus:ring-primary focus:border-primary"
                                        placeholder="Enter new password">
                                    <p class="text-xs text-mutedText mt-1">Must be at least 6 characters long</p>
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-mutedText mb-1">Confirm New Password</label>
                                    <input type="password" x-model="passwordData.new_password_confirmation" required
                                        class="w-full rounded-xl bg-navy border-primary/10 text-mainText focus:ring-primary focus:border-primary"
                                        placeholder="Re-enter new password">
                                </div>
                            </div>

                            <div class="mt-8 flex justify-end">
                                <button type="submit" :disabled="isLoading"
                                    class="bg-primary hover:bg-primary/90 text-white px-6 py-2.5 rounded-xl font-bold transition flex items-center shadow-lg shadow-primary/20">
                                    <svg x-show="isLoading" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white"
                                        fill="none" viewBox="0 0 24 24">
                                        <circle class="opacity-25" cx="12" cy="12" r="10"
                                            stroke="currentColor" stroke-width="4"></circle>
                                        <path class="opacity-75" fill="currentColor"
                                            d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                        </path>
                                    </svg>
                                    Update Password
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
                activeTab: '{{ request()->get('section', 'profile') }}',
                isLoading: false,
                reapplyMode: false,
                changeBankMode: false,
                bankStatus: '{{ $user->bank->status ?? 'not_submitted' }}',
                kycStatus: '{{ $user->kyc_status ?? 'not_submitted' }}',
                hasUpdatePending: {{ ($user->bankUpdateRequests()->where('status', 'pending')->exists()) ? 'true' : 'false' }},

                indianStates: ["Andaman and Nicobar Islands", "Andhra Pradesh", "Arunachal Pradesh", "Assam", "Bihar",
                    "Chandigarh", "Chhattisgarh", "Dadra and Nagar Haveli", "Daman and Diu", "Delhi", "Goa", "Gujarat",
                    "Haryana", "Himachal Pradesh", "Jammu and Kashmir", "Jharkhand", "Karnataka", "Kerala", "Ladakh",
                    "Lakshadweep", "Madhya Pradesh", "Maharashtra", "Manipur", "Meghalaya", "Mizoram", "Nagaland",
                    "Odisha", "Puducherry", "Punjab", "Rajasthan", "Sikkim", "Tamil Nadu", "Telangana", "Tripura",
                    "Uttar Pradesh", "Uttarakhand", "West Bengal"
                ],

                // Profile Data
                profile: {
                    name: '{{ $user->name }}',
                    email: '{{ $user->email }}',
                    mobile: '{{ $user->mobile }}',
                    gender: '{{ $user->gender }}',
                    dob: '{{ $user->dob ? $user->dob->format('Y-m-d') : '' }}',
                    state_id: '{{ $user->state_id }}',
                    zip_code: '{{ $user->zip_code }}',
                    address: '{{ $user->address }}'
                },

                // KYC Data
                kyc: {
                    pan_name: '{{ $user->kyc->pan_name ?? '' }}',
                    file: null
                },

                // Bank Data
                bank: {
                    holder_name: '{{ $user->bank->account_holder_name ?? '' }}',
                    account_number: '', // Reset for security
                    account_number_confirmation: '',
                    bank_name: '{{ $user->bank->bank_name ?? '' }}',
                    ifsc_code: '{{ $user->bank->ifsc_code ?? '' }}',
                    upi_id: '{{ $user->bank->upi_id ?? '' }}',
                    file: null
                },

                // Password Data
                passwordData: {
                    current_password: '',
                    new_password: '',
                    new_password_confirmation: ''
                },

                init() {},

                handleKycFile(e) {
                    this.kyc.file = e.target.files[0];
                },

                handleBankFile(e) {
                    this.bank.file = e.target.files[0];
                },

                updateProfile() {
                    this.isLoading = true;
                    axios.post("{{ route('student.profile.update') }}", this.profile)
                        .then(res => Swal.fire('Updated!', 'Your profile has been updated.', 'success'))
                        .catch(err => {
                            let msg = 'Failed to update';
                            if(err.response?.data?.message) msg = Object.values(err.response.data.message).flat().join(', ');
                            Swal.fire('Error', msg, 'error');
                        })
                        .finally(() => this.isLoading = false);
                },

                submitKyc() {
                    if (!this.kyc.file && !this.reapplyMode) return Swal.fire('Error', 'Upload Document', 'error');

                    this.isLoading = true;
                    let fd = new FormData();
                    fd.append('pan_name', this.kyc.pan_name);
                    if(this.kyc.file) fd.append('document', this.kyc.file);

                    axios.post("{{ route('student.kyc.submit') }}", fd)
                        .then(res => Swal.fire('Submitted', 'KYC sent for approval.', 'success').then(() => location.reload()))
                        .catch(err => Swal.fire('Error', 'Failed to submit KYC', 'error'))
                        .finally(() => this.isLoading = false);
                },

                submitBank() {
                    if (this.bank.account_number !== this.bank.account_number_confirmation) {
                        return Swal.fire('Error', 'Account numbers do not match', 'error');
                    }
                    if (!this.bank.file) return Swal.fire('Error', 'Please upload account proof document.', 'error');

                    this.isLoading = true;
                    let fd = new FormData();
                    fd.append('bank_name', this.bank.bank_name);
                    fd.append('holder_name', this.bank.holder_name);
                    fd.append('account_number', this.bank.account_number);
                    fd.append('account_number_confirmation', this.bank.account_number_confirmation);
                    fd.append('ifsc_code', this.bank.ifsc_code);
                    if(this.bank.upi_id) fd.append('upi_id', this.bank.upi_id);
                    if(this.bank.file) fd.append('document', this.bank.file);

                    axios.post("{{ route('student.bank.save') }}", fd)
                        .then(res => Swal.fire('Submitted!', res.data.message, 'success').then(() => location.reload()))
                        .catch(err => {
                             let msg = 'Failed to submit';
                             if(err.response?.data?.message) msg = typeof err.response.data.message === 'string' ? err.response.data.message : Object.values(err.response.data.message).flat().join(', ');
                             Swal.fire('Error', msg, 'error');
                        })
                        .finally(() => this.isLoading = false);
                },

                changePassword() {
                    if (this.passwordData.new_password !== this.passwordData.new_password_confirmation) {
                        return Swal.fire('Error', 'New passwords do not match', 'error');
                    }

                    if (this.passwordData.new_password.length < 6) {
                        return Swal.fire('Error', 'Password must be at least 6 characters', 'error');
                    }

                    this.isLoading = true;
                    axios.post("{{ route('student.password.change') }}", this.passwordData)
                        .then(res => {
                            Swal.fire('Success!', res.data.message, 'success');
                            this.passwordData = {
                                current_password: '',
                                new_password: '',
                                new_password_confirmation: ''
                            };
                        })
                        .catch(err => {
                            Swal.fire('Error', err.response.data.message || 'Failed to update password', 'error');
                        })
                        .finally(() => this.isLoading = false);
                }
            }
        }
    </script>
@endsection
