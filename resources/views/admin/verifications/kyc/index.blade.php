@extends('layouts.admin')
@section('title', 'KYC Verification Management')

@section('content')
    <div x-data="kycManager()" class="space-y-8 font-sans text-mainText">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-end gap-4 px-2">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-mainText uppercase">KYC Center</h1>
                <p class="text-mutedText mt-1 text-sm font-semibold tracking-wide">High-priority identity verification and validation.</p>
            </div>

            <div class="flex bg-white/50 backdrop-blur-sm p-1 rounded-2xl border border-primary/10 shadow-sm">
                <div class="flex items-center gap-1 px-4 py-2 bg-primary/5 rounded-xl border border-primary/5">
                    <span class="w-2 h-2 rounded-full bg-primary animate-pulse"></span>
                    <span class="text-[11px] font-black uppercase text-primary tracking-tighter">
                        {{ $pendingKyc->count() }} PENDING REQUESTS
                    </span>
                </div>
            </div>
        </div>

        {{-- Pending Verifications Section --}}
        <section class="space-y-4">
            <div class="flex items-center justify-between px-2">
                <h2 class="text-sm font-black uppercase tracking-[2px] text-primary flex items-center gap-2">
                    <i class="fas fa-clock"></i>
                    Pending Approval
                </h2>
            </div>
            
            <div class="bg-surface rounded-[32px] shadow-sm border border-primary/10 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-primary/5 text-[11px] uppercase font-black text-primary tracking-[1px] border-b border-primary/10">
                            <tr>
                                <th class="px-8 py-6">Identity Profile</th>
                                <th class="px-8 py-6">ID Document Name</th>
                                <th class="px-8 py-6">Date Submitted</th>
                                <th class="px-8 py-6 text-right">Verification</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-primary/5">
                            @forelse($pendingKyc as $user)
                                <tr class="hover:bg-primary/5 transition-all group">
                                    <td class="px-8 py-5">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-navy text-primary flex items-center justify-center font-black text-lg border border-primary/10 shadow-sm group-hover:scale-110 transition-transform">
                                                {{ substr($user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-black text-mainText text-base">{{ $user->name }}</p>
                                                <p class="text-[11px] font-bold text-mutedText/70 tracking-tight">{{ $user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-mainText uppercase bg-navy/30 px-3 py-1 rounded-lg inline-block w-fit">
                                                {{ $user->kyc->pan_name }}
                                            </span>
                                            <span class="text-[10px] text-mutedText font-bold mt-1 px-1">Legal Name Match Required</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-mainText">{{ $user->kyc->created_at->format('d M, Y') }}</span>
                                            <span class="text-[10px] text-mutedText font-bold">{{ $user->kyc->created_at->format('h:i A') }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-5 text-right">
                                        <button @click="openModal({{ json_encode($user) }}, '{{ asset('storage/' . $user->kyc->document_path) }}', '{{ pathinfo($user->kyc->document_path, PATHINFO_EXTENSION) }}')"
                                            class="brand-gradient text-white px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-[2px] shadow-lg shadow-primary/20 hover:shadow-primary/40 hover:scale-[1.03] transition-all">
                                            Start Review
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-16 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-16 h-16 bg-navy/20 rounded-full flex items-center justify-center text-mutedText/30">
                                                <i class="fas fa-check-double text-2xl"></i>
                                            </div>
                                            <p class="text-mutedText font-black uppercase tracking-widest text-xs">All clear! No pending KYC requests.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- Verification History --}}
        <section class="space-y-4">
            <h2 class="text-sm font-black uppercase tracking-[2px] text-mutedText/60 px-2 flex items-center gap-2">
                <i class="fas fa-history"></i>
                Review Archive
            </h2>
            <div class="bg-surface rounded-[32px] shadow-sm border border-navy/5 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-navy/10 text-[10px] uppercase font-black text-mutedText tracking-[1px]">
                            <tr>
                                <th class="px-8 py-5">User Account</th>
                                <th class="px-8 py-5">ID Ref</th>
                                <th class="px-8 py-5">Final Status</th>
                                <th class="px-8 py-5 text-right">View Archive</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-navy/5">
                            @foreach($recentKyc as $user)
                                <tr class="hover:bg-navy/5 transition-colors">
                                    <td class="px-8 py-4">
                                        <p class="font-bold text-mainText text-sm">{{ $user->name }}</p>
                                        <p class="text-[10px] text-mutedText/60 font-bold uppercase">{{ $user->email }}</p>
                                    </td>
                                    <td class="px-8 py-4">
                                        <p class="text-[10px] font-black text-mainText uppercase bg-white border border-navy/10 px-2 py-0.5 rounded shadow-sm w-fit">
                                            {{ $user->kyc->pan_name }}
                                        </p>
                                    </td>
                                    <td class="px-8 py-4">
                                        @if ($user->kyc->status === 'verified')
                                            <span class="bg-emerald-50 text-emerald-600 px-4 py-1.5 rounded-xl font-black text-[10px] uppercase tracking-widest border border-emerald-100 shadow-sm shadow-emerald-500/5">Verified</span>
                                        @else
                                            <span class="bg-secondary/5 text-secondary px-4 py-1.5 rounded-xl font-black text-[10px] uppercase tracking-widest border border-secondary/10 shadow-sm shadow-secondary/5">Rejected</span>
                                        @endif
                                    </td>
                                    <td class="px-8 py-4 text-right">
                                        <button @click="openModal({{ json_encode($user) }}, '{{ asset('storage/' . $user->kyc->document_path) }}', '{{ pathinfo($user->kyc->document_path, PATHINFO_EXTENSION) }}')"
                                            class="bg-navy text-mainText border border-navy/10 px-6 py-2 rounded-xl text-[10px] font-black uppercase tracking-widest hover:bg-navy/80 hover:text-white transition-all shadow-sm">
                                            View Details
                                        </button>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </section>

        {{-- Verification Reality View Modal --}}
        <div x-show="modalOpen" x-cloak class="fixed inset-0 z-[100] overflow-hidden">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-mainText/90 backdrop-blur-xl transition-opacity animate-fadeIn" @click="modalOpen = false"></div>
            
            {{-- Modal Content --}}
            <div class="flex min-h-screen items-center justify-center p-4 md:p-8 relative">
                <div @click.away="modalOpen = false" 
                    class="bg-surface w-full max-w-[1400px] h-[90vh] rounded-[48px] flex flex-col md:flex-row overflow-hidden shadow-2xl border border-white/10 animate-scaleUp">
                    
                    {{-- Exit Trigger --}}
                    <button @click="modalOpen = false" class="fixed top-12 right-12 z-[110] bg-white text-mainText hover:bg-secondary hover:text-white w-12 h-12 rounded-full flex items-center justify-center shadow-2xl transition-all hover:rotate-90">
                        <i class="fas fa-times text-xl"></i>
                    </button>

                    {{-- Left View: Document Evidence --}}
                    <div class="w-full md:w-3/5 bg-black flex flex-col relative p-8 md:p-12">
                        <div class="absolute top-8 left-8 z-10 flex items-center gap-3">
                            <span class="px-3 py-1 bg-primary rounded-lg text-[9px] font-black text-white uppercase tracking-widest">Document Evidence</span>
                            <span class="text-white/40 text-[9px] font-bold uppercase tracking-widest" x-text="'Format: ' + kycData.ext"></span>
                        </div>

                        <div class="flex-1 w-full rounded-[32px] overflow-hidden bg-navy/10 border border-white/5 shadow-inner group relative">
                            <template x-if="kycData.ext === 'pdf'">
                                <iframe :src="kycData.docUrl" class="w-full h-full border-0"></iframe>
                            </template>
                            <template x-if="kycData.ext !== 'pdf'">
                                <div class="w-full h-full flex items-center justify-center">
                                    <img :src="kycData.docUrl" class="max-w-full max-h-full object-contain cursor-zoom-in transition-transform duration-500 hover:scale-110">
                                </div>
                            </template>
                        </div>

                        <div class="mt-8 flex justify-between items-center px-4">
                            <a :href="kycData.docUrl" target="_blank" class="text-white hover:text-primary text-[10px] font-black uppercase tracking-[2px] flex items-center gap-2 transition-colors">
                                <i class="fas fa-external-link-alt"></i> Open Full Quality Original
                            </a>
                            <div class="flex gap-2">
                                <span class="w-2 h-2 rounded-full bg-white/20"></span>
                                <span class="w-2 h-2 rounded-full bg-white/20"></span>
                                <span class="w-2 h-2 rounded-full bg-white/20"></span>
                            </div>
                        </div>
                    </div>

                    {{-- Right View: Decision Matrix --}}
                    <div class="w-full md:w-2/5 flex flex-col bg-surface border-l border-navy/5 overflow-hidden">
                        {{-- Identity Title --}}
                        <div class="p-10 border-b border-navy/5 bg-navy/5">
                            <h3 class="text-3xl font-black text-mainText leading-none" x-text="kycData.user?.name"></h3>
                            <div class="flex items-center gap-2 mt-3">
                                <span class="text-[10px] font-black text-primary uppercase tracking-widest bg-primary/5 px-2 py-0.5 rounded border border-primary/10" x-text="'UID: #' + kycData.user?.id"></span>
                                <span class="text-[10px] font-black text-mutedText uppercase tracking-widest">Pending Verification</span>
                            </div>
                        </div>

                        {{-- Metadata Scroll --}}
                        <div class="p-10 flex-1 overflow-y-auto space-y-10">
                            {{-- Section 1: Official Data --}}
                            <div class="space-y-6">
                                <h4 class="text-[11px] font-bold text-mutedText uppercase tracking-[3px] flex items-center gap-2">
                                    <div class="w-5 h-[1px] bg-mutedText/30"></div>
                                    System Profile
                                </h4>
                                <div class="grid grid-cols-2 gap-8">
                                    <div>
                                        <label class="text-[9px] font-black text-mutedText/50 uppercase tracking-widest block mb-1">Email Authority</label>
                                        <p class="font-black text-mainText text-xs truncate" x-text="kycData.user?.email"></p>
                                    </div>
                                    <div>
                                        <label class="text-[9px] font-black text-mutedText/50 uppercase tracking-widest block mb-1">Mobile Line</label>
                                        <p class="font-black text-mainText text-xs" x-text="kycData.user?.mobile ? kycData.user.mobile : 'N/A'"></p>
                                    </div>
                                    <div class="col-span-2 p-5 bg-navy/5 border border-navy/10 rounded-3xl">
                                        <label class="text-[9px] font-black text-mutedText/50 uppercase tracking-widest block mb-2 text-center">Referral Source (Sponsor)</label>
                                        <div class="flex items-center justify-center gap-4">
                                            <div class="w-10 h-10 rounded-full bg-mainText text-white flex items-center justify-center font-black">
                                                S
                                            </div>
                                            <div>
                                                <p class="text-xs font-black text-mainText uppercase" x-text="kycData.sponsor?.name || 'Self-Registered'"></p>
                                                <p class="text-[9px] font-bold text-mutedText" x-text="kycData.sponsor?.email || 'N/A'"></p>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Section 2: Submitted Identification --}}
                            <div class="space-y-6">
                                <h4 class="text-[11px] font-bold text-emerald-600 uppercase tracking-[3px] flex items-center gap-2">
                                    <div class="w-5 h-[1px] bg-emerald-600/30"></div>
                                    Official Evidence
                                </h4>
                                <div class="p-8 bg-emerald-50/50 border border-emerald-500/10 rounded-[32px] text-center group hover:bg-emerald-50 transition-all duration-300 transform hover:-translate-y-1">
                                    <label class="text-[10px] font-bold text-emerald-600/70 uppercase tracking-widest block mb-2">Legal Identity Name</label>
                                    <p class="text-4xl font-black text-emerald-700 tracking-tight leading-none group-hover:scale-105 transition-transform" x-text="kycData.user?.kyc?.pan_name"></p>
                                    <p class="text-[9px] font-black text-emerald-600 uppercase tracking-widest mt-4 opacity-50 italic">Verify spelling matches document exactly</p>
                                </div>
                            </div>
                        </div>

                        {{-- Section 3: Final Action --}}
                        <div class="p-10 bg-navy/10 border-t border-navy/5">
                            <div x-show="!showRejectForm" class="flex gap-4 animate-fadeIn">
                                <button @click="processApproval()" 
                                    class="flex-1 brand-gradient text-white py-5 rounded-[24px] font-black text-[11px] uppercase tracking-[2px] shadow-xl shadow-primary/20 hover:scale-[1.02] transition-all">
                                    Approve Identification
                                </button>
                                <button @click="showRejectForm = true"
                                    class="flex-1 bg-surface border-2 border-secondary/20 text-secondary py-5 rounded-[24px] font-black text-[11px] uppercase tracking-[2px] hover:bg-secondary/5 transition-all">
                                    Reject Request
                                </button>
                            </div>

                            <div x-show="showRejectForm" x-transition class="space-y-4 animate-scaleUp">
                                <div class="flex items-center justify-between mb-2">
                                    <h5 class="text-[10px] font-black text-secondary uppercase tracking-widest">Rejection Reason</h5>
                                    <button @click="showRejectForm = false" class="text-[10px] font-bold text-mutedText hover:text-mainText">Cancel</button>
                                </div>
                                <textarea x-model="adminNote" 
                                    class="w-full rounded-[24px] border-secondary/10 bg-white p-5 text-sm font-bold text-mainText focus:ring-secondary/20 focus:border-secondary transition-all" 
                                    rows="4" 
                                    placeholder="Provide a professional explanation for rejection..."></textarea>
                                <button @click="processRejection()" 
                                    class="w-full bg-secondary text-white py-5 rounded-[24px] font-black text-[11px] uppercase tracking-[2px] shadow-xl shadow-secondary/20 hover:bg-secondary/90 transition-all">
                                    Confirm Formal Rejection
                                </button>
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
                modalOpen: false,
                showRejectForm: false,
                adminNote: '',
                kycData: {},

                openModal(userData, docUrl, ext) {
                    this.kycData = {
                        user: userData,
                        sponsor: userData.referrer || null,
                        docUrl: docUrl,
                        ext: ext.toLowerCase()
                    };
                    this.adminNote = '';
                    this.showRejectForm = false;
                    this.modalOpen = true;
                },

                processApproval() {
                    Swal.fire({
                        title: '<span class="font-black text-mainText">APPROVE KYC?</span>',
                        text: "This will officially verify the user's identity in the system.",
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonText: 'Yes, Verify Now',
                        confirmButtonColor: '#F7941D',
                        cancelButtonText: 'Discard',
                        borderRadius: '24px',
                        customClass: {
                            title: 'font-sans',
                            content: 'font-sans text-xs font-bold'
                        }
                    }).then((result) => {
                        if (result.isConfirmed) {
                            this.sendRequest('{{ route('admin.verifications.kyc.approve', ':id') }}'.replace(':id', this.kycData.user.id), 'approve');
                        }
                    });
                },

                processRejection() {
                    if (!this.adminNote.trim()) {
                        return Swal.fire({
                            title: 'Note Required',
                            text: 'Please provide a reason for the rejection.',
                            icon: 'warning',
                            borderRadius: '24px'
                        });
                    }
                    this.sendRequest('{{ route('admin.verifications.kyc.reject', ':id') }}'.replace(':id', this.kycData.user.id), 'reject');
                },

                sendRequest(url, action) {
                    Swal.showLoading();
                    axios.post(url, { admin_note: this.adminNote })
                        .then(response => {
                            Swal.fire({
                                title: 'Success',
                                text: 'KYC status has been updated.',
                                icon: 'success',
                                borderRadius: '24px'
                            }).then(() => location.reload());
                        })
                        .catch(error => {
                            Swal.fire({
                                title: 'System Error',
                                text: 'Failed to process request. Please try again.',
                                icon: 'error',
                                borderRadius: '24px'
                            });
                        });
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes scaleUp { from { opacity: 0; transform: scale(0.95); } to { opacity: 1; transform: scale(1); } }
        .animate-fadeIn { animation: fadeIn 0.4s ease-out forwards; }
        .animate-scaleUp { animation: scaleUp 0.4s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-thumb { background: #F7941D; border-radius: 10px; }
    </style>
@endsection
