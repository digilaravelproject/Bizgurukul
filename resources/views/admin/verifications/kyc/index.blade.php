@extends('layouts.admin')
@section('title', 'KYC Verifications')

@section('content')
    <div x-data="kycManager()" class="space-y-8 font-sans text-mainText">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-end gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-mainText">KYC Verifications</h1>
                <p class="text-mutedText mt-1 text-sm font-medium">Review and approve user identity documents.</p>
            </div>
        </div>

        {{-- Request Table --}}
        <div class="bg-surface rounded-2xl shadow-sm border border-primary/10 overflow-hidden">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-primary/5 text-[11px] uppercase font-black text-primary tracking-widest border-b border-primary/5">
                        <tr>
                            <th class="px-6 py-5">User Details</th>
                            <th class="px-6 py-5">Submitted Date</th>
                            <th class="px-6 py-5">Status</th>
                            <th class="px-6 py-5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary/5">
                        @forelse($pendingKyc as $user)
                            <tr class="hover:bg-primary/5 transition-colors group">
                                <td class="px-6 py-4">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 rounded-xl bg-navy/30 flex items-center justify-center text-primary font-black border border-primary/10">
                                            {{ substr($user->name, 0, 1) }}
                                        </div>
                                        <div>
                                            <p class="font-bold text-mainText">{{ $user->name }}</p>
                                            <p class="text-[10px] text-mutedText font-medium tracking-tight">{{ $user->email }}</p>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-4 font-bold text-mutedText">
                                    {{ $user->kyc->updated_at->format('d M, Y') }}
                                </td>
                                <td class="px-6 py-4">
                                    <span class="px-3 py-1 rounded-full text-[10px] font-black uppercase tracking-widest border
                                        {{ $user->kyc->status == 'pending' ? 'bg-amber-100 text-amber-700 border-amber-200' : '' }}
                                        {{ $user->kyc->status == 'verified' ? 'bg-primary/10 text-primary border-primary/20' : '' }}
                                        {{ $user->kyc->status == 'rejected' ? 'bg-secondary/10 text-secondary border-secondary/20' : '' }}">
                                        {{ $user->kyc->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="openModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->dob ? $user->dob->format('d M, Y') : 'N/A' }}', '{{ $user->kyc->pan_name }}', '{{ asset('storage/' . $user->kyc->document_path) }}', '{{ pathinfo($user->kyc->document_path, PATHINFO_EXTENSION) }}')"
                                        class="bg-surface border border-primary/20 text-primary px-4 py-2 rounded-xl font-black hover:bg-primary hover:text-white transition-all shadow-sm text-[10px] uppercase tracking-widest">
                                        Review
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-mutedText font-bold italic">No pending KYC requests found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if($pendingKyc->hasPages())
                <div class="p-4 bg-primary/5 border-t border-primary/5">
                    {{ $pendingKyc->links() }}
                </div>
            @endif
        </div>

        {{-- History Table (Optional, for recent actions) --}}
        @if($recentKyc->count() > 0)
        <div class="pt-8">
            <h2 class="text-xl font-black text-mainText mb-4">Recent Actions</h2>
            <div class="bg-surface rounded-2xl shadow-sm border border-primary/10 overflow-hidden opacity-80">
                <table class="w-full text-left text-sm">
                    <thead class="bg-navy/5 text-[10px] uppercase font-black text-mutedText tracking-widest border-b border-primary/5">
                        <tr>
                            <th class="px-6 py-4">User</th>
                            <th class="px-6 py-4">Decision</th>
                            <th class="px-6 py-4">Date</th>
                            <th class="px-6 py-4">Note</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary/5">
                        @foreach($recentKyc as $user)
                            <tr>
                                <td class="px-6 py-3 font-bold text-mainText">{{ $user->name }}</td>
                                <td class="px-6 py-3">
                                    <span class="px-2 py-0.5 rounded-md text-[9px] font-black uppercase tracking-wider
                                        {{ $user->kyc->status == 'verified' ? 'text-green-600 bg-green-50' : 'text-red-600 bg-red-50' }}">
                                        {{ $user->kyc->status }}
                                    </span>
                                </td>
                                <td class="px-6 py-3 text-xs text-mutedText">{{ $user->kyc->updated_at->format('d M, H:i') }}</td>
                                <td class="px-6 py-3 text-xs text-mutedText italic">{{ Str::limit($user->kyc->admin_note, 30) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
        @endif

        {{-- MODERN REVIEW MODAL --}}
        <div x-show="showModal"
             x-cloak
             class="fixed inset-0 z-50 overflow-y-auto"
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0"
             x-transition:enter-end="opacity-100">

            <div class="fixed inset-0 bg-navy/80 backdrop-blur-md"></div>

            <div class="flex min-h-full items-center justify-center p-4">
                <div @click.away="showModal = false"
                    class="bg-surface w-full max-w-6xl h-[85vh] rounded-3xl flex flex-col md:flex-row overflow-hidden shadow-2xl relative border border-primary/10">

                    <button @click="showModal = false"
                        class="absolute top-4 right-4 z-10 bg-white/10 hover:bg-secondary text-mainText md:text-white p-2 rounded-full transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    {{-- LEFT SIDE: Doc --}}
                    <div class="w-full md:w-1/2 bg-black flex flex-col items-center justify-center relative p-8">
                        <h4 class="absolute top-6 left-6 text-primary text-[10px] font-black uppercase tracking-[2px]">ID Proof Document</h4>

                        <div class="w-full h-full flex items-center justify-center rounded-2xl overflow-hidden border border-white/5 bg-navy/20">
                            <template x-if="data.ext === 'pdf'">
                                <iframe :src="data.url" class="w-full h-full border-0"></iframe>
                            </template>
                            <template x-if="data.ext !== 'pdf'">
                                <img :src="data.url" class="max-w-full max-h-full object-contain">
                            </template>
                        </div>

                        <a :href="data.url" target="_blank"
                            class="mt-6 brand-gradient text-white px-6 py-3 rounded-full text-xs font-black flex items-center gap-2 uppercase tracking-widest">
                            <i class="fas fa-expand"></i> View Full Resolution
                        </a>
                    </div>

                    {{-- RIGHT SIDE: Check --}}
                    <div class="w-full md:w-1/2 flex flex-col bg-surface">
                        <div class="p-8 border-b border-primary/5">
                            <h3 class="text-2xl font-black text-mainText">Identity Verification</h3>
                            <p class="text-sm text-mutedText">Compare system profile with submitted document.</p>
                        </div>

                        <div class="p-8 flex-1 overflow-y-auto space-y-8">
                            <div class="grid grid-cols-2 gap-8">
                                <div class="space-y-5">
                                    <h4 class="text-[10px] font-black text-mutedText uppercase tracking-widest border-b border-primary/5 pb-2">Profile Data</h4>
                                    <div>
                                        <p class="text-[10px] text-mutedText uppercase font-bold mb-1">Name</p>
                                        <p class="text-base font-black text-mainText" x-text="data.system_name"></p>
                                    </div>
                                    <div>
                                        <p class="text-[10px] text-mutedText uppercase font-bold mb-1">DOB</p>
                                        <p class="text-sm font-bold text-mainText" x-text="data.system_dob"></p>
                                    </div>
                                </div>
                                <div class="space-y-5">
                                    <h4 class="text-[10px] font-black text-primary uppercase tracking-widest border-b border-primary/5 pb-2">Submitted Proof</h4>
                                    <div>
                                        <p class="text-[10px] text-primary uppercase font-bold mb-1">Name on ID</p>
                                        <p class="text-base font-black text-primary" x-text="data.id_name"></p>
                                    </div>
                                </div>
                            </div>

                            <div x-show="data.system_name && data.id_name && data.system_name.toLowerCase() !== data.id_name.toLowerCase()"
                                class="bg-secondary/5 border border-secondary/20 p-5 rounded-2xl flex gap-4 animate-pulse">
                                <i class="fas fa-exclamation-triangle text-secondary text-2xl"></i>
                                <div>
                                    <p class="text-sm text-secondary font-black uppercase">Name Conflict Detected</p>
                                    <p class="text-xs text-mutedText mt-1">Names differ between profile and ID. Verification might fail.</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-8 bg-navy/10 border-t border-primary/5">
                            <div class="space-y-4">
                                <div class="flex gap-4" x-show="!showReject">
                                    <button @click="confirmAction('approve')"
                                        class="flex-1 brand-gradient text-white py-4 rounded-2xl font-black shadow-lg shadow-primary/20 hover:opacity-90 flex justify-center items-center gap-2 uppercase tracking-widest text-xs">
                                        Approve
                                    </button>
                                    <button @click="showReject = true"
                                        class="flex-1 bg-surface border border-secondary/30 text-secondary py-4 rounded-2xl font-black hover:bg-secondary/5 uppercase tracking-widest text-xs">
                                        Reject
                                    </button>
                                </div>

                                <div x-show="showReject" x-transition class="space-y-4">
                                    <textarea x-model="adminNote"
                                        class="w-full border-primary/10 bg-white rounded-xl text-sm p-4 focus:ring-secondary focus:border-secondary font-medium"
                                        rows="3" placeholder="Reason for rejection..."></textarea>
                                    <div class="flex justify-end gap-3">
                                        <button @click="showReject = false" class="px-4 py-2 text-xs font-bold text-mutedText uppercase tracking-widest">Cancel</button>
                                        <button @click="confirmAction('reject')" class="px-6 py-2 bg-secondary text-white text-xs font-black rounded-xl shadow-lg shadow-secondary/20 uppercase tracking-widest">Confirm Reject</button>
                                    </div>
                                </div>
                            </div>
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
                adminNote: '',
                data: {},

                openModal(id, systemName, systemDob, idName, url, ext) {
                    this.data = { id, system_name: systemName, system_dob: systemDob, id_name: idName, url, ext };
                    this.adminNote = '';
                    this.showReject = false;
                    this.showModal = true;
                },

                confirmAction(action) {
                    if (action === 'reject' && !this.adminNote) {
                        return Swal.fire('Error', 'Please provide a reason for rejection.', 'error');
                    }

                    const url = action === 'approve'
                        ? `{{ route('admin.verifications.kyc.approve', ':id') }}`.replace(':id', this.data.id)
                        : `{{ route('admin.verifications.kyc.reject', ':id') }}`.replace(':id', this.data.id);

                    Swal.fire({
                        title: 'Are you sure?',
                        text: `You are about to ${action} this KYC request.`,
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: action === 'approve' ? '#F7941D' : '#e11d48',
                        confirmButtonText: 'Yes, proceed!'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            axios.post(url, { admin_note: this.adminNote })
                                .then(res => {
                                    Swal.fire('Success', res.data.message, 'success').then(() => location.reload());
                                })
                                .catch(err => {
                                    Swal.fire('Error', err.response.data.message || 'Something went wrong', 'error');
                                });
                        }
                    });
                }
            }
        }
    </script>
@endsection
