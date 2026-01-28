@extends('layouts.admin')
@section('title', 'KYC Requests')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div x-data="kycManager()" x-init="init()" class="container-fluid px-4 py-4 font-sans">

        <h2 class="text-2xl font-bold text-slate-800 mb-6">KYC Verifications</h2>

        {{-- Request Table --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden">
            <table class="w-full text-left text-sm text-slate-600">
                <thead class="bg-slate-50 uppercase font-bold text-slate-500 border-b">
                    <tr>
                        <th class="px-6 py-4">User</th>
                        <th class="px-6 py-4">Submitted Date</th>
                        <th class="px-6 py-4">Status</th>
                        <th class="px-6 py-4 text-right">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100">
                    @foreach($kycUsers as $user)
                        <tr class="hover:bg-slate-50 transition">
                            <td class="px-6 py-4">
                                <p class="font-bold text-slate-800">{{ $user->name }}</p>
                                <p class="text-xs text-slate-500">{{ $user->email }}</p>
                            </td>
                            <td class="px-6 py-4">{{ $user->kyc->updated_at->format('d M, Y') }}</td>
                            <td class="px-6 py-4">
                                <span class="px-2 py-1 rounded text-xs font-bold uppercase
                                    {{ $user->kyc->status == 'pending' ? 'bg-amber-100 text-amber-700' : '' }}
                                    {{ $user->kyc->status == 'verified' ? 'bg-green-100 text-green-700' : '' }}
                                    {{ $user->kyc->status == 'rejected' ? 'bg-red-100 text-red-700' : '' }}">
                                    {{ $user->kyc->status }}
                                </span>
                            </td>
                            <td class="px-6 py-4 text-right">
                                <button @click="openModal({{ $user->id }})"
                                    class="bg-indigo-600 text-white px-4 py-2 rounded-lg font-bold hover:bg-indigo-700 transition shadow-sm text-xs">
                                    Review Details
                                </button>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
            <div class="p-4">{{ $kycUsers->links() }}</div>
        </div>

        {{-- IMPROVED REVIEW MODAL --}}
        <div x-show="showModal" style="display: none;" class="fixed inset-0 z-50 overflow-y-auto" x-transition.opacity>
            <div class="fixed inset-0 bg-black/80 backdrop-blur-sm"></div>
            <div class="flex min-h-full items-center justify-center p-4">

                {{-- Modal Container --}}
                <div @click.away="showModal = false"
                    class="bg-white w-full max-w-6xl h-[85vh] rounded-2xl flex flex-col md:flex-row overflow-hidden shadow-2xl relative">

                    {{-- Close Button --}}
                    <button @click="showModal = false"
                        class="absolute top-4 right-4 z-10 bg-white/20 hover:bg-white/40 text-slate-800 md:text-white p-2 rounded-full transition">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12">
                            </path>
                        </svg>
                    </button>

                    {{-- LEFT SIDE: Document Preview (Controlled Size) --}}
                    <div
                        class="w-full md:w-1/2 bg-slate-900 flex flex-col items-center justify-center relative p-6 border-r border-slate-700">
                        <h4 class="absolute top-4 left-4 text-white/50 text-xs font-bold uppercase tracking-widest">Document
                            Preview</h4>

                        {{-- Container for Image/PDF with Max Height --}}
                        <div
                            class="w-full h-full max-h-[500px] flex items-center justify-center bg-black/30 rounded-lg overflow-hidden border border-white/10">
                            <template x-if="data.doc_type === 'pdf'">
                                <iframe :src="data.doc_url" class="w-full h-full border-0"></iframe>
                            </template>
                            <template x-if="data.doc_type !== 'pdf'">
                                <img :src="data.doc_url" class="max-w-full max-h-full object-contain">
                            </template>
                        </div>

                        {{-- Open Original Button --}}
                        <a :href="data.doc_url" target="_blank"
                            class="mt-4 bg-indigo-600 hover:bg-indigo-500 text-white px-4 py-2 rounded-full text-sm font-bold flex items-center gap-2 transition shadow-lg">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                            </svg>
                            View Original / Zoom
                        </a>
                    </div>

                    {{-- RIGHT SIDE: Data Verification --}}
                    <div class="w-full md:w-1/2 flex flex-col bg-slate-50">

                        {{-- Header --}}
                        <div class="p-6 border-b border-slate-200 bg-white">
                            <h3 class="text-xl font-bold text-slate-800">Verification Details</h3>
                            <p class="text-sm text-slate-500">Compare registered details with submitted document.</p>
                        </div>

                        {{-- Comparison Grid --}}
                        <div class="p-6 flex-1 overflow-y-auto">
                            <div class="grid grid-cols-2 gap-6">

                                {{-- SYSTEM DATA --}}
                                <div class="col-span-1 space-y-4">
                                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wide border-b pb-1">
                                        System Profile Data</h4>
                                    <div>
                                        <p class="text-xs text-slate-500">Registered Name</p>
                                        <p class="text-base font-bold text-slate-800" x-text="data.user_name"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500">Date of Birth</p>
                                        <p class="text-base font-bold text-slate-800" x-text="data.user_dob"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500">Email</p>
                                        <p class="text-sm font-medium text-slate-800" x-text="data.user_email"></p>
                                    </div>
                                </div>

                                {{-- SUBMITTED KYC DATA --}}
                                <div class="col-span-1 space-y-4">
                                    <h4 class="text-xs font-bold text-indigo-400 uppercase tracking-wide border-b pb-1">
                                        Submitted KYC Data</h4>
                                    <div>
                                        <p class="text-xs text-slate-500">Name on PAN/Doc</p>
                                        <p class="text-base font-bold text-indigo-700" x-text="data.pan_name"></p>
                                    </div>
                                    <div>
                                        <p class="text-xs text-slate-500">Document Type</p>
                                        <p class="text-sm font-medium text-slate-800 uppercase" x-text="data.doc_type"></p>
                                    </div>
                                </div>

                            </div>

                            {{-- Name Mismatch Alert --}}
                            <div x-show="data.user_name && data.pan_name && data.user_name.toLowerCase() !== data.pan_name.toLowerCase()"
                                class="mt-6 bg-yellow-50 border-l-4 border-yellow-400 p-4 rounded-r-lg">
                                <div class="flex">
                                    <div class="flex-shrink-0">
                                        <svg class="h-5 w-5 text-yellow-400" fill="none" viewBox="0 0 24 24"
                                            stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                                        </svg>
                                    </div>
                                    <div class="ml-3">
                                        <p class="text-sm text-yellow-700 font-bold">Name Mismatch Detected</p>
                                        <p class="text-xs text-yellow-600">Profile name and document name differ. Verify
                                            carefully.</p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        {{-- Actions Footer --}}
                        <div class="p-6 bg-white border-t border-slate-200">

                            {{-- IF PENDING --}}
                            <template x-if="data.kyc_status === 'pending'">
                                <div class="space-y-4">
                                    <div class="flex gap-4">
                                        {{-- Approve Button --}}
                                        <button @click="updateStatus('verified')"
                                            class="flex-1 bg-green-600 hover:bg-green-700 text-white py-3 rounded-lg font-bold shadow-md transition flex justify-center items-center gap-2">
                                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M5 13l4 4L19 7"></path>
                                            </svg>
                                            Verify & Approve
                                        </button>

                                        {{-- Reject Button Toggle --}}
                                        <button @click="showReject = !showReject" x-show="!showReject"
                                            class="flex-1 bg-white border-2 border-red-100 text-red-600 hover:bg-red-50 py-3 rounded-lg font-bold transition">
                                            Reject
                                        </button>
                                    </div>

                                    {{-- Reject Input --}}
                                    <div x-show="showReject"
                                        class="bg-red-50 p-4 rounded-lg border border-red-100 animate-fade-in-down">
                                        <label class="text-xs font-bold text-red-700 uppercase mb-1 block">Reason for
                                            Rejection</label>
                                        <textarea x-model="rejectReason"
                                            class="w-full border-red-200 rounded-md text-sm p-2 focus:ring-red-500 mb-3"
                                            rows="2" placeholder="e.g. Image blurry, Name mismatch..."></textarea>
                                        <div class="flex justify-end gap-2">
                                            <button @click="showReject = false"
                                                class="px-3 py-1.5 bg-white text-slate-600 text-xs font-bold rounded border border-slate-200">Cancel</button>
                                            <button @click="updateStatus('rejected')"
                                                class="px-3 py-1.5 bg-red-600 text-white text-xs font-bold rounded hover:bg-red-700 shadow-sm">Confirm
                                                Reject</button>
                                        </div>
                                    </div>
                                </div>
                            </template>

                            {{-- IF ALREADY PROCESSED --}}
                            <template x-if="data.kyc_status !== 'pending'">
                                <div class="text-center">
                                    <p class="text-sm text-slate-500 mb-2">Current Status</p>
                                    <span class="px-6 py-2 rounded-full font-bold text-sm uppercase tracking-wide border"
                                        :class="data.kyc_status === 'verified' ? 'bg-green-100 text-green-700 border-green-200' : 'bg-red-100 text-red-700 border-red-200'"
                                        x-text="data.kyc_status">
                                    </span>
                                    {{-- Allow Re-action --}}
                                    <button @click="data.kyc_status = 'pending'"
                                        class="block mx-auto mt-4 text-xs text-indigo-600 hover:underline">Change
                                        Status</button>
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

                init() { },

                openModal(id) {
                    this.rejectReason = '';
                    this.showReject = false;
                    this.data = {}; // clear old data

                    axios.get(`/admin/kyc-requests/${id}`).then(res => {
                        this.data = res.data.data;
                        this.showModal = true;
                    }).catch(err => {
                        Swal.fire('Error', 'Could not fetch data', 'error');
                    });
                },

                updateStatus(status) {
                    if (status == 'rejected' && !this.rejectReason) {
                        return Swal.fire('Missing Information', 'Please provide a reason for rejection.', 'warning');
                    }

                    axios.post(`/admin/kyc-requests/${this.data.id}/status`, {
                        status: status,
                        note: this.rejectReason
                    })
                        .then(res => {
                            this.showModal = false;
                            Swal.fire({
                                title: status === 'verified' ? 'Verified!' : 'Rejected!',
                                text: res.data.message,
                                icon: 'success',
                                timer: 2000,
                                showConfirmButton: false
                            }).then(() => location.reload());
                        })
                        .catch(err => Swal.fire('Error', 'Something went wrong', 'error'));
                }
            }
        }
    </script>
@endsection