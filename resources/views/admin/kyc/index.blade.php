@extends('layouts.admin')
@section('title', 'KYC Requests')

@section('content')
    {{-- SweetAlert loading handle karne ke liye --}}
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div x-data="kycManager()" class="space-y-8 font-sans text-mainText">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-end gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-mainText">KYC Verifications</h1>
                <p class="text-mutedText mt-1 text-sm font-medium">Review and approve affiliate identity documents.</p>
            </div>

            {{-- Status Tabs --}}
            <div class="bg-primary/5 p-1 rounded-xl flex items-center gap-1">
                <a href="{{ route('admin.kyc.requests', ['status' => 'pending']) }}"
                    class="px-6 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ $status === 'pending' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-mutedText hover:text-primary hover:bg-primary/5' }}">
                    Pending
                </a>
                <a href="{{ route('admin.kyc.requests', ['status' => 'verified']) }}"
                    class="px-6 py-2 rounded-lg text-xs font-black uppercase tracking-widest transition-all {{ $status === 'verified' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-mutedText hover:text-primary hover:bg-primary/5' }}">
                    Verified
                </a>
            </div>
        </div>

        {{-- Request Table --}}
        <div class="bg-surface rounded-2xl shadow-sm border border-primary/10 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead
                        class="bg-primary/5 text-[11px] uppercase font-black text-primary tracking-widest border-b border-primary/5">
                        <tr>
                            <th class="px-6 py-5">User Details</th>
                            <th class="px-6 py-5">Submitted Date</th>
                            <th class="px-6 py-5">Status</th>
                            <th class="px-6 py-5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary/5">
                        @foreach($kycUsers as $user)
                            <tr class="hover:bg-primary/5 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div
                                            class="w-10 h-10 rounded-xl bg-navy/30 flex items-center justify-center text-primary font-black border border-primary/10">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-mainText">{{ $user->name }}</p>
                                            <p class="text-[10px] text-mutedText font-medium tracking-tight">{{ $user->email }}
                                            </p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-bold text-mutedText">
                                    {{ $user->kyc->updated_at->format('d M, Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span
                                        class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border
                                                {{ $user->kyc->status == 'pending' ? 'bg-amber-100 text-amber-700 border-amber-200' : '' }}
                                                {{ $user->kyc->status == 'verified' ? 'bg-primary/10 text-primary border-primary/20' : '' }}
                                                {{ $user->kyc->status == 'rejected' ? 'bg-secondary/10 text-secondary border-secondary/20' : '' }}">
                                        {{ $user->kyc->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="openModal({{ $user->id }})"
                                        class="bg-surface border border-primary/20 text-primary px-4 py-2 rounded-xl font-black hover:bg-primary hover:text-white transition-all shadow-sm text-[10px] uppercase tracking-widest">
                                        Review Details
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            <div class="p-4 bg-primary/5 border-t border-primary/5">
                {{ $kycUsers->appends(['status' => $status])->links() }}
            </div>
        </div>

        {{-- MODERN REVIEW MODAL --}}
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto"
            x-transition:enter="transition ease-out duration-300" x-transition:enter-start="opacity-0"
            x-transition:enter-end="opacity-100">

            <div class="fixed inset-0 bg-navy/80 backdrop-blur-md"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                {{-- Modal Container --}}
                <div @click.away="showModal = false"
                    class="bg-surface w-full max-w-6xl h-[85vh] rounded-3xl flex flex-col md:flex-row overflow-hidden shadow-2xl relative border border-primary/10">

                    {{-- Close Button --}}
                    <button @click="showModal = false"
                        class="absolute top-4 right-4 z-10 bg-white/10 hover:bg-secondary text-mainText md:text-white p-2 rounded-full transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>

                    {{-- LEFT SIDE: Document Preview --}}
                    <div
                        class="w-full md:w-1/2 bg-black flex flex-col items-center justify-center relative p-8 border-r border-primary/5">
                        <h4 class="absolute top-6 left-6 text-primary text-[10px] font-black uppercase tracking-[2px]">
                            Document Preview</h4>

                        {{-- Toggle Tab for Front / Back --}}
                        <template x-if="data.doc_back_url">
                            <div class="flex bg-white/10 rounded-xl p-1 mb-4 border border-white/10 z-10 mt-8">
                                <button @click="previewSide = 'front'"
                                    :class="previewSide === 'front' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-white/60 hover:text-white'"
                                    class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all">
                                    Front Side
                                </button>
                                <button @click="previewSide = 'back'"
                                    :class="previewSide === 'back' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-white/60 hover:text-white'"
                                    class="px-4 py-1.5 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all">
                                    Back Side
                                </button>
                            </div>
                        </template>

                        <div
                            class="w-full h-full max-h-[500px] flex items-center justify-center rounded-2xl overflow-hidden border border-white/5 shadow-2xl bg-navy/20"
                            :class="data.doc_back_url ? '' : 'mt-8'">
                            <template x-if="previewSide === 'front' && data.doc_type === 'pdf'">
                                <iframe :src="data.doc_url" class="w-full h-full border-0"></iframe>
                            </template>
                            <template x-if="previewSide === 'front' && data.doc_type !== 'pdf'">
                                <img :src="data.doc_url" class="max-w-full max-h-full object-contain">
                            </template>
                            <template x-if="previewSide === 'back' && data.doc_back_type === 'pdf'">
                                <iframe :src="data.doc_back_url" class="w-full h-full border-0"></iframe>
                            </template>
                            <template x-if="previewSide === 'back' && data.doc_back_type !== 'pdf'">
                                <img :src="data.doc_back_url" class="max-w-full max-h-full object-contain">
                            </template>
                        </div>

                        <a :href="previewSide === 'front' ? data.doc_url : data.doc_back_url" target="_blank"
                            class="mt-6 brand-gradient text-white px-6 py-3 rounded-full text-xs font-black flex items-center gap-2 transition-all shadow-xl shadow-primary/20 uppercase tracking-widest">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            View Full Resolution
                        </a>
                    </div>

                    {{-- RIGHT SIDE: Data Verification --}}
                    <div class="w-full md:w-1/2 flex flex-col bg-surface">
                        <div class="p-8 border-b border-primary/5">
                            <h3 class="text-2xl font-black text-mainText tracking-tight">Identity Check</h3>
                            <p class="text-sm text-mutedText font-medium">Cross-verify profile info with the document below.
                            </p>
                        </div>

                        <div class="p-8 flex-1 overflow-y-auto space-y-8">
                            <div class="grid grid-cols-2 gap-8">
                                {{-- SYSTEM DATA --}}
                                <div class="space-y-5">
                                    <h4
                                        class="text-[10px] font-black text-mutedText uppercase tracking-widest border-b border-primary/5 pb-2">
                                        Internal Profile</h4>
                                    <div>
                                        <p class="text-[10px] text-mutedText uppercase font-bold mb-1">Registered Name</p>
                                        <p class="text-base font-black text-mainText" x-text="data.user_name"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-mutedText uppercase font-bold mb-1">System DOB</p>
                                        <p class="text-sm font-bold text-mainText" x-text="data.user_dob"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-mutedText uppercase font-bold mb-1">Bank Name</p>
                                        <p class="text-xs font-black text-primary uppercase tracking-[1px]"
                                            x-text="data.bank_name || 'N/A'"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-mutedText uppercase font-bold mb-1">Bank Type (Account Type)</p>
                                        <p class="text-xs font-black text-secondary uppercase mt-0.5"
                                            x-text="data.account_type || 'N/A'"></p>
                                    </div>
                                </div>

                                {{-- SUBMITTED KYC DATA --}}
                                <div class="space-y-5">
                                    <h4
                                        class="text-[10px] font-black text-primary uppercase tracking-widest border-b border-primary/5 pb-2">
                                        Submitted Data</h4>
                                    <div>
                                        <p class="text-[10px] text-primary uppercase font-bold mb-1">Name on Document</p>
                                        <p class="text-base font-black text-primary" x-text="data.pan_name"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-primary uppercase font-bold mb-1">ID Type</p>
                                        <p class="text-sm font-bold text-mainText uppercase" x-text="data.doc_type"></p>
                                    </div>
                                </div>
                            </div>

                            {{-- Name Mismatch Alert --}}
                            <div x-show="data.user_name && data.pan_name && data.user_name.toLowerCase() !== data.pan_name.toLowerCase()"
                                class="bg-secondary/5 border border-secondary/20 p-5 rounded-2xl flex gap-4 animate-fade-in-down">
                                <div
                                    class="w-10 h-10 rounded-full bg-secondary/10 flex items-center justify-center text-secondary shrink-0">
                                    <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                    </svg>
                                </div>
                                <div>
                                    <p class="text-sm text-secondary font-black uppercase tracking-tight">Name Mismatch
                                        Detected</p>
                                    <p class="text-xs text-mutedText font-medium mt-1">The name on the document does not
                                        match the system profile. Please check for spelling errors.</p>
                                </div>
                            </div>
                        </div>

                        {{-- Actions Footer --}}
                        <div class="p-8 bg-navy/10 border-t border-primary/5">
                            <template x-if="data.kyc_status === 'pending'">
                                <div class="space-y-4">
                                    <div class="flex gap-4" x-show="!showReject">
                                        <button @click="updateStatus('verified')"
                                            class="flex-1 brand-gradient text-white py-4 rounded-2xl font-black shadow-xl shadow-primary/20 hover:opacity-90 transition-all flex justify-center items-center gap-2 uppercase tracking-widest text-xs">
                                            Approve & Verify
                                        </button>
                                        <button @click="showReject = true"
                                            class="flex-1 bg-surface border border-secondary/30 text-secondary py-4 rounded-2xl font-black hover:bg-secondary/5 transition-all uppercase tracking-widest text-xs">
                                            Reject
                                        </button>
                                    </div>

                                    {{-- Reject Input --}}
                                    <div x-show="showReject" x-transition
                                        class="bg-surface p-6 rounded-2xl border border-secondary/20 shadow-inner">
                                        <label
                                            class="text-[10px] font-black text-secondary uppercase mb-2 block tracking-widest">Rejection
                                            Reason</label>
                                        <textarea x-model="rejectReason"
                                            class="w-full border-primary/10 bg-navy/5 rounded-xl text-sm p-4 focus:ring-secondary focus:border-secondary mb-4 font-medium"
                                            rows="3" placeholder="Explain why the KYC was rejected..."></textarea>
                                        <div class="flex justify-end gap-3">
                                            <button @click="showReject = false"
                                                class="px-5 py-2 text-xs font-bold text-mutedText uppercase">Cancel</button>
                                            <button @click="updateStatus('rejected')"
                                                class="px-6 py-2 bg-secondary text-white text-xs font-black rounded-xl shadow-lg shadow-secondary/20 uppercase tracking-widest">Confirm
                                                Rejection</button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="data.kyc_status === 'verified'">
                                <div class="space-y-4">
                                    <div
                                        class="text-center p-6 rounded-2xl border border-primary/20 bg-primary/5 shadow-sm">
                                        <div
                                            class="w-12 h-12 rounded-full bg-primary/10 flex items-center justify-center text-primary mx-auto mb-4 border border-primary/20">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                        </div>
                                        <p
                                            class="text-[10px] text-mutedText font-black uppercase tracking-[2px] mb-1 text-center">
                                            User Identity Verified</p>
                                        <p class="text-xs text-mainText font-bold mb-4 text-center">Verification completed
                                            on <span x-text="data.submitted_at"></span></p>

                                        <button @click="confirmRejectVerified()"
                                            class="block mx-auto text-[10px] font-black text-secondary hover:text-white hover:bg-secondary border border-secondary/30 px-6 py-2 rounded-xl transition-all uppercase tracking-widest">
                                            Reject Verified User
                                        </button>
                                    </div>

                                    {{-- Reject Input (for Verified users) --}}
                                    <div x-show="showReject" x-transition
                                        class="bg-surface p-6 rounded-2xl border border-secondary/20 shadow-inner">
                                        <label
                                            class="text-[10px] font-black text-secondary uppercase mb-2 block tracking-widest">Reason
                                            for Rejection (Required)</label>
                                        <textarea x-model="rejectReason"
                                            class="w-full border-primary/10 bg-navy/5 rounded-xl text-sm p-4 focus:ring-secondary focus:border-secondary mb-4 font-medium"
                                            rows="3"
                                            placeholder="Explain why this verification is being revoked..."></textarea>
                                        <div class="flex justify-end gap-3">
                                            <button @click="showReject = false; rejectReason = ''"
                                                class="px-5 py-2 text-xs font-bold text-mutedText uppercase">Cancel</button>
                                            <button @click="updateStatus('rejected')"
                                                class="px-6 py-2 bg-secondary text-white text-xs font-black rounded-xl shadow-lg shadow-secondary/20 uppercase tracking-widest">Confirm
                                                & Reject</button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            <template x-if="data.kyc_status === 'rejected'">
                                <div
                                    class="text-center p-8 rounded-2xl border border-secondary/20 bg-secondary/5 shadow-sm">
                                    <div
                                        class="w-12 h-12 rounded-full bg-secondary/10 flex items-center justify-center text-secondary mx-auto mb-4 border border-secondary/20">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </div>
                                    <p class="text-[10px] text-secondary font-black uppercase tracking-[2px] mb-2">
                                        Verification Rejected</p>
                                    <p class="text-xs text-mutedText font-medium mb-4"
                                        x-text="data.admin_note || 'No reason provided'"></p>
                                    <button @click="data.kyc_status = 'pending'"
                                        class="text-[10px] font-black text-primary hover:underline uppercase tracking-widest">
                                        Re-evaluate Request
                                    </button>
                                </div>
                            </template>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function kycManager() {
            return {
                showModal: false,
                showReject: false,
                data: {},
                rejectReason: '',
                previewSide: 'front',

                openModal(id) {
                    this.rejectReason = '';
                    this.showReject = false;
                    this.data = {};
                    this.previewSide = 'front';

                    axios.get(`/admin/kyc-requests/${id}`).then(res => {
                        this.data = res.data.data;
                        this.showModal = true;
                    }).catch(err => {
                        Swal.fire('Error', 'Could not fetch data', 'error');
                    });
                },

                confirmRejectVerified() {
                    Swal.fire({
                        title: 'Are you sure?',
                        text: "This user is already verified. Rejecting them now will revoke their verification status. Do you want to proceed?",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#F7941D',
                        cancelButtonColor: '#2D2D2D',
                        confirmButtonText: 'Yes, reject them',
                        background: '#FFFFFF',
                        color: '#2D2D2D'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.showReject = true;
                            // Scroll to reject reason box
                            this.$nextTick(() => {
                                const el = document.querySelector('textarea');
                                if (el) el.focus();
                            });
                        }
                    });
                },

                updateStatus(status) {
                    if (status == 'rejected' && !this.rejectReason) {
                        return Swal.fire('Reason Required', 'Please provide a reason for rejection.', 'warning');
                    }

                    axios.post(`/admin/kyc-requests/${this.data.id}/status`, {
                        status: status,
                        note: this.rejectReason
                    })
                        .then(res => {
                            this.showModal = false;
                            Swal.fire({
                                title: status === 'verified' ? 'Approved!' : 'Rejected!',
                                text: res.data.message,
                                icon: 'success',
                                confirmButtonColor: '#F7941D'
                            }).then(() => location.reload());
                        })
                        .catch(err => Swal.fire('Error', 'Something went wrong', 'error'));
                }
            }
        }
    </script>
@endsection