@extends('layouts.admin')
@section('title', 'Bank Verification Control')

@section('content')
    <div x-data="bankManager()" class="space-y-8 font-sans text-mainText">

        {{-- Header Section --}}
        <div class="flex flex-col md:flex-row justify-between items-end gap-6 px-2">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-mainText uppercase">Payout Infrastructure</h1>
                <p class="text-mutedText mt-1 text-sm font-semibold tracking-wide">Secure bank account validation and detail synchronization.</p>
            </div>

            <div class="flex bg-white/60 backdrop-blur-md p-1 rounded-[24px] border border-primary/10 shadow-sm">
                <button @click="tab = 'initial'" 
                    :class="tab === 'initial' ? 'brand-gradient text-white shadow-lg shadow-primary/20' : 'text-mutedText hover:text-primary hover:bg-primary/5'"
                    class="px-8 py-3 rounded-[20px] text-[11px] font-black uppercase tracking-[1px] transition-all duration-300">
                    Initial Setup <span class="ml-2 px-2 py-0.5 bg-black/10 rounded-lg text-[9px]">{{ $initialRequests->count() }}</span>
                </button>
                <button @click="tab = 'updates'" 
                    :class="tab === 'updates' ? 'bg-secondary text-white shadow-lg shadow-secondary/20' : 'text-mutedText hover:text-secondary hover:bg-secondary/5'"
                    class="px-8 py-3 rounded-[20px] text-[11px] font-black uppercase tracking-[1px] transition-all duration-300">
                    Info Updates <span class="ml-2 px-2 py-0.5 bg-black/10 rounded-lg text-[9px]">{{ $updateRequests->count() }}</span>
                </button>
            </div>
        </div>

        {{-- INITIAL BANK REQUESTS --}}
        <div x-show="tab === 'initial'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4">
            <div class="bg-surface rounded-[40px] shadow-sm border border-primary/10 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-primary/5 text-[11px] uppercase font-black text-primary tracking-[2px] border-b border-primary/10">
                            <tr>
                                <th class="px-8 py-7">User Entity</th>
                                <th class="px-8 py-7">Settlement Pipeline</th>
                                <th class="px-8 py-7">Ingestion Date</th>
                                <th class="px-8 py-7 text-right">Verification</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-primary/5">
                            @forelse($initialRequests as $bank)
                                <tr class="hover:bg-primary/5 transition-all group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-navy text-primary flex items-center justify-center font-black text-lg border border-primary/10 shadow-sm group-hover:scale-110 transition-transform">
                                                {{ substr($bank->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-black text-mainText text-base">{{ $bank->user->name }}</p>
                                                <p class="text-[11px] font-bold text-mutedText/70 tracking-tight">{{ $bank->user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="flex flex-col">
                                            <span class="text-xs font-black text-primary uppercase bg-primary/5 px-3 py-1 rounded-lg inline-block w-fit mb-1">
                                                {{ $bank->bank_name }}
                                            </span>
                                            <span class="text-sm font-black text-mainText tracking-[2px]">
                                                {{ Str::mask($bank->account_number, '*', 4, -4) }}
                                            </span>
                                            <span class="text-[10px] text-mutedText font-bold uppercase mt-1">IFSC: {{ $bank->ifsc_code }}</span>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <span class="text-xs font-black text-mainText block">{{ $bank->updated_at->format('d M, Y') }}</span>
                                        <span class="text-[10px] text-mutedText font-bold uppercase">{{ $bank->updated_at->format('h:i A') }}</span>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <button @click="openInitialModal({{ json_encode($bank) }}, '{{ asset('storage/' . $bank->document_path) }}')"
                                            class="brand-gradient text-white px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-[2px] shadow-lg shadow-primary/20 hover:scale-[1.05] transition-all">
                                            Review Account
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-16 h-16 bg-navy/20 rounded-full flex items-center justify-center text-mutedText/30 mb-2">
                                                <i class="fas fa-university text-2xl"></i>
                                            </div>
                                            <p class="text-mutedText font-black uppercase tracking-widest text-xs">No pending initial payouts setups.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- UPDATE REQUESTS --}}
        <div x-show="tab === 'updates'" x-cloak x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4">
            <div class="bg-surface rounded-[40px] shadow-sm border border-secondary/10 overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm border-collapse">
                        <thead class="bg-secondary/5 text-[11px] uppercase font-black text-secondary tracking-[2px] border-b border-secondary/10">
                            <tr>
                                <th class="px-8 py-7">User Entity</th>
                                <th class="px-8 py-7">Previous Config</th>
                                <th class="px-8 py-7">Requested Sync</th>
                                <th class="px-8 py-7 text-right">Action</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-secondary/5">
                            @forelse($updateRequests as $req)
                                <tr class="hover:bg-secondary/5 transition-all group">
                                    <td class="px-8 py-6">
                                        <div class="flex items-center gap-4">
                                            <div class="w-12 h-12 rounded-2xl bg-navy text-secondary flex items-center justify-center font-black text-lg border border-secondary/10 shadow-sm group-hover:scale-110 transition-transform">
                                                {{ substr($req->user->name, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-black text-mainText text-base">{{ $req->user->name }}</p>
                                                <p class="text-[11px] font-bold text-mutedText/70 tracking-tight">{{ $req->user->email }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="p-4 bg-navy/5 rounded-2xl border border-navy/10 grayscale opacity-60">
                                            <p class="text-[9px] font-black text-mutedText uppercase mb-1">Old Settlement</p>
                                            <p class="text-xs font-black text-mainText uppercase truncate">{{ $req->old_data['bank_name'] }}</p>
                                            <p class="text-[10px] font-bold text-mutedText tracking-wider">...{{ substr($req->old_data['account_number'], -4) }}</p>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6">
                                        <div class="p-4 bg-secondary/5 rounded-2xl border border-secondary/20 shadow-sm">
                                            <p class="text-[9px] font-black text-secondary uppercase mb-1">Incoming Config</p>
                                            <p class="text-xs font-black text-secondary uppercase truncate">{{ $req->new_data['bank_name'] }}</p>
                                            <p class="text-[10px] font-black text-mainText tracking-widest">{{ Str::mask($req->new_data['account_number'], '*', 0, -4) }}</p>
                                        </div>
                                    </td>
                                    <td class="px-8 py-6 text-right">
                                        <button @click="openUpdateModal({{ json_encode($req) }}, '{{ asset('storage/' . $req->document_path) }}')"
                                            class="bg-secondary text-white px-8 py-3 rounded-2xl font-black text-[10px] uppercase tracking-[2px] shadow-lg shadow-secondary/20 hover:scale-[1.05] transition-all">
                                            Sync Updates
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4" class="px-8 py-20 text-center">
                                        <div class="flex flex-col items-center gap-3">
                                            <div class="w-16 h-16 bg-navy/20 rounded-full flex items-center justify-center text-mutedText/30 mb-2">
                                                <i class="fas fa-sync text-2xl"></i>
                                            </div>
                                            <p class="text-mutedText font-black uppercase tracking-widest text-xs">No bank update requests in queue.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        {{-- REVIEW MODAL --}}
        <div x-show="showModal" x-cloak class="fixed inset-0 z-[100] overflow-hidden">
            {{-- Backdrop --}}
            <div class="fixed inset-0 bg-mainText/90 backdrop-blur-xl animate-fadeIn" @click="showModal = false"></div>
            
            {{-- Modal Content --}}
            <div class="flex min-h-screen items-center justify-center p-4 md:p-8 relative">
                <div @click.away="showModal = false" 
                    class="bg-surface w-full max-w-[1400px] h-[90vh] rounded-[56px] flex flex-col md:flex-row shadow-2xl border border-white/20 animate-scaleUp overflow-hidden">
                    
                    {{-- Exit Trigger --}}
                    <button @click="showModal = false" class="absolute top-12 right-12 z-[110] bg-white text-mainText hover:bg-secondary hover:text-white w-14 h-14 rounded-full flex items-center justify-center shadow-2xl transition-all hover:rotate-90">
                        <i class="fas fa-times text-2xl"></i>
                    </button>

                    {{-- Left Side: Document Proof --}}
                    <div class="w-full md:w-1/2 bg-black flex flex-col relative p-12">
                        <div class="absolute top-10 left-10 z-10 flex items-center gap-4">
                            <span class="px-4 py-1.5 bg-primary rounded-xl text-[10px] font-black text-white uppercase tracking-[2px]">Bank Proof Document</span>
                            <div class="flex gap-1">
                                <template x-if="isPdf">
                                    <span class="px-3 py-1 bg-white/10 rounded-lg text-[9px] font-bold text-white uppercase">PDF format</span>
                                </template>
                                <template x-if="!isPdf">
                                    <span class="px-3 py-1 bg-white/10 rounded-lg text-[9px] font-bold text-white uppercase">Image payload</span>
                                </template>
                            </div>
                        </div>

                        <div class="flex-1 w-full rounded-[40px] overflow-hidden bg-white/5 border border-white/10 shadow-inner group relative">
                            <template x-if="isPdf">
                                <iframe :src="currentDoc" class="w-full h-full border-0 grayscale hover:grayscale-0 transition-all duration-700"></iframe>
                            </template>
                            <template x-if="!isPdf">
                                <div class="w-full h-full flex items-center justify-center p-4">
                                    <img :src="currentDoc" class="max-w-full max-h-full object-contain cursor-zoom-in group-hover:scale-105 transition-transform duration-700">
                                </div>
                            </template>
                        </div>

                        <div class="mt-8 flex justify-between items-center text-white/50 text-[10px] font-black uppercase tracking-[2px]">
                            <a :href="currentDoc" target="_blank" class="hover:text-primary transition-colors flex items-center gap-2">
                                <i class="fas fa-download"></i> Download Origin Payload
                            </a>
                            <span x-text="'Processing UID: ' + (reviewType === 'initial' ? initialData.id : updateData.id)"></span>
                        </div>
                    </div>

                    {{-- Right Side: Intelligence & Action --}}
                    <div class="w-full md:w-1/2 flex flex-col bg-surface overflow-hidden">
                        <div class="p-12 border-b border-navy/5 bg-navy/5">
                            <h3 class="text-4xl font-black text-mainText leading-none mb-3" x-text="reviewType === 'initial' ? 'Infrastructure Setup' : 'Infrastructure Sync'"></h3>
                            <p class="text-sm font-semibold text-mutedText" x-text="reviewType === 'initial' ? 'Validating first-time bank integration details.' : 'Supervising settlement detail migration.'"></p>
                        </div>

                        <div class="p-12 flex-1 overflow-y-auto space-y-12">
                            {{-- Data Comparison --}}
                            <div class="space-y-10">
                                <template x-if="reviewType === 'initial'">
                                    <div class="space-y-8 animate-fadeIn">
                                        <h4 class="text-[11px] font-bold text-primary uppercase tracking-[4px] flex items-center gap-3">
                                            <div class="w-8 h-[1px] bg-primary/30"></div> Settlement Data
                                        </h4>
                                        <div class="grid grid-cols-2 gap-y-10 gap-x-12">
                                            <div class="group">
                                                <label class="text-[10px] font-black text-mutedText/40 uppercase tracking-widest block mb-1 group-hover:text-primary transition-colors">Bank Authority</label>
                                                <p class="text-xl font-black text-mainText uppercase" x-text="initialData.bank_name"></p>
                                            </div>
                                            <div class="group">
                                                <label class="text-[10px] font-black text-mutedText/40 uppercase tracking-widest block mb-1">Holder Identity</label>
                                                <p class="text-xl font-black text-mainText" x-text="initialData.holder_name"></p>
                                            </div>
                                            <div class="col-span-2 p-8 bg-primary/5 rounded-[32px] border border-primary/10 relative overflow-hidden group">
                                                <div class="absolute -right-4 -top-4 w-24 h-24 bg-primary/5 rounded-full group-hover:scale-150 transition-transform duration-700"></div>
                                                <label class="text-[10px] font-black text-primary uppercase tracking-[2px] block mb-2">Primary Account Number</label>
                                                <p class="text-3xl font-black text-mainText tracking-[4px] leading-none" x-text="initialData.account_number"></p>
                                                <div class="mt-6 flex items-center justify-between">
                                                    <div>
                                                        <label class="text-[9px] font-black text-mutedText/40 uppercase block">IFSC/Routing Code</label>
                                                        <p class="text-lg font-black text-primary" x-text="initialData.ifsc_code"></p>
                                                    </div>
                                                    <i class="fas fa-shield-alt text-3xl text-primary/10"></i>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="reviewType === 'update'">
                                    <div class="space-y-10 animate-fadeIn">
                                        <div class="p-8 bg-navy/5 rounded-[32px] border border-navy/10 grayscale opacity-40 hover:opacity-70 transition-all">
                                            <h4 class="text-[10px] font-black text-mutedText uppercase tracking-[3px] mb-6 flex items-center gap-2">
                                                <i class="fas fa-history"></i> Current Active State
                                            </h4>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <p class="text-[9px] font-bold text-mutedText">Legacy Bank</p>
                                                    <p class="font-black text-mainText uppercase text-sm" x-text="updateData.old_data?.bank_name"></p>
                                                </div>
                                                <div>
                                                    <p class="text-[9px] font-bold text-mutedText">Legacy A/C</p>
                                                    <p class="font-black text-mainText text-sm tracking-widest" x-text="'...' + updateData.old_data?.account_number.slice(-4)"></p>
                                                </div>
                                            </div>
                                        </div>

                                        <div class="p-10 bg-secondary/5 rounded-[40px] border-2 border-secondary/10 shadow-2xl shadow-secondary/5 relative overflow-hidden group">
                                             <div class="absolute -right-10 -top-10 w-40 h-40 bg-secondary/5 rounded-full group-hover:scale-110 transition-transform duration-1000"></div>
                                            <h4 class="text-[11px] font-black text-secondary uppercase tracking-[4px] mb-8 flex items-center gap-3">
                                                <i class="fas fa-bolt"></i> Incoming Sync Request
                                            </h4>
                                            <div class="space-y-10 relative">
                                                <div class="grid grid-cols-2 gap-8">
                                                    <div>
                                                        <p class="text-[10px] font-black text-secondary/50 uppercase tracking-widest mb-1">Target Bank</p>
                                                        <p class="text-xl font-black text-secondary uppercase" x-text="updateData.new_data?.bank_name"></p>
                                                    </div>
                                                    <div>
                                                        <p class="text-[10px] font-black text-secondary/50 uppercase tracking-widest mb-1">Target Holder</p>
                                                        <p class="text-xl font-black text-secondary uppercase" x-text="updateData.new_data?.holder_name"></p>
                                                    </div>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] font-black text-secondary/50 uppercase tracking-widest mb-2">New Target Account</p>
                                                    <p class="text-4xl font-black text-mainText tracking-[5px] leading-none" x-text="updateData.new_data?.account_number"></p>
                                                </div>
                                                <div class="pt-6 border-t border-secondary/10">
                                                    <p class="text-[10px] font-black text-secondary/50 uppercase tracking-widest mb-1">New IFSC Sync</p>
                                                    <p class="text-xl font-black text-secondary" x-text="updateData.new_data?.ifsc_code"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Footer Action Bar --}}
                        <div class="p-12 bg-navy/10 border-t border-navy/5">
                            <div x-show="!showReject" class="flex gap-6 animate-fadeIn">
                                <button @click="processAction('approve')"
                                    class="flex-1 brand-gradient text-white py-6 rounded-[28px] font-black text-[11px] uppercase tracking-[3px] shadow-2xl shadow-primary/30 hover:scale-[1.02] transition-all">
                                    Approve & Synchronize
                                </button>
                                <button @click="showReject = true"
                                    class="flex-1 bg-surface border-2 border-secondary/20 text-secondary py-6 rounded-[28px] font-black text-[11px] uppercase tracking-[3px] hover:bg-secondary/5 transition-all">
                                    Decline Request
                                </button>
                            </div>

                            <div x-show="showReject" x-transition class="space-y-6 animate-scaleUp">
                                <div class="flex items-center justify-between">
                                    <h5 class="text-[10px] font-black text-secondary uppercase tracking-[3px]">Protocol Rejection Note</h5>
                                    <button @click="showReject = false" class="text-[10px] font-black text-mutedText hover:text-mainText uppercase">Back to Overview</button>
                                </div>
                                <textarea x-model="adminNote" 
                                    class="w-full border-secondary/10 bg-white rounded-[28px] text-sm p-6 font-bold text-mainText shadow-inner focus:ring-secondary/20 focus:border-secondary transition-all" 
                                    rows="4" 
                                    placeholder="State the technical reason for settlement rejection..."></textarea>
                                <button @click="processAction('reject')" 
                                    class="w-full bg-secondary text-white py-6 rounded-[28px] font-black text-[11px] uppercase tracking-[3px] shadow-2xl shadow-secondary/40 hover:bg-secondary/90 transition-all">
                                    Execute Formal Rejection
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        function bankManager() {
            return {
                tab: 'initial',
                showModal: false,
                reviewType: 'initial',
                initialData: {},
                updateData: {},
                currentDoc: '',
                isPdf: false,
                adminNote: '',
                showReject: false,

                openInitialModal(data, docUrl) {
                    this.reviewType = 'initial';
                    this.initialData = data;
                    this.currentDoc = docUrl;
                    this.isPdf = docUrl.toLowerCase().endsWith('.pdf');
                    this.adminNote = '';
                    this.showReject = false;
                    this.showModal = true;
                },

                openUpdateModal(data, docUrl) {
                    this.reviewType = 'update';
                    this.updateData = data;
                    this.currentDoc = docUrl;
                    this.isPdf = docUrl.toLowerCase().endsWith('.pdf');
                    this.adminNote = '';
                    this.showReject = false;
                    this.showModal = true;
                },

                processAction(action) {
                    if (action === 'reject' && !this.adminNote.trim()) {
                        return Swal.fire({
                            title: 'Note Missing',
                            text: 'Formal rejection requires a documented reason.',
                            icon: 'warning',
                            borderRadius: '24px'
                        });
                    }

                    let url = '';
                    if (this.reviewType === 'initial') {
                        url = `{{ route('admin.verifications.bank.initial.process', ':id') }}`.replace(':id', this.initialData.id);
                    } else {
                        url = `{{ route('admin.verifications.bank.update.process', ':id') }}`.replace(':id', this.updateData.id);
                    }

                    Swal.fire({
                        title: `<span class="font-black text-mainText">EXECUTE ${action.toUpperCase()}?</span>`,
                        text: `Are you certain you want to ${action} this infrastructure configuration?`,
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: action === 'approve' ? '#F7941D' : '#e11d48',
                        borderRadius: '24px'
                    }).then(res => {
                        if (res.isConfirmed) {
                            axios.post(url, { action, admin_note: this.adminNote }).then(r => {
                                Swal.fire({
                                    title: 'Sync Complete',
                                    text: r.data.message,
                                    icon: 'success',
                                    borderRadius: '24px'
                                }).then(() => location.reload());
                            }).catch(e => {
                                Swal.fire({
                                    title: 'Sync Fail',
                                    text: e.response.data.message || 'Transmission error',
                                    icon: 'error',
                                    borderRadius: '24px'
                                });
                            });
                        }
                    });
                }
            }
        }
    </script>

    <style>
        [x-cloak] { display: none !important; }
        @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        @keyframes scaleUp { from { opacity: 0; transform: scale(0.95) translateY(20px); } to { opacity: 1; transform: scale(1) translateY(0); } }
        .animate-fadeIn { animation: fadeIn 0.4s ease-out forwards; }
        .animate-scaleUp { animation: scaleUp 0.5s cubic-bezier(0.16, 1, 0.3, 1) forwards; }
        ::-webkit-scrollbar { width: 5px; height: 5px; }
        ::-webkit-scrollbar-thumb { background: #F7941D; border-radius: 10px; }
    </style>
@endsection
