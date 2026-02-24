@extends('layouts.admin')
@section('title', 'Bank Verifications')

@section('content')
    <div x-data="bankManager()" class="space-y-8 font-sans text-mainText">

        {{-- Header --}}
        <div class="flex flex-col md:flex-row justify-between items-end gap-4">
            <div>
                <h1 class="text-3xl font-extrabold tracking-tight text-mainText">Bank Verifications</h1>
                <p class="text-mutedText mt-1 text-sm font-medium">Approve initial bank details and update requests.</p>
            </div>

            <div class="flex bg-navy/20 p-1 rounded-2xl border border-primary/10">
                <button @click="tab = 'initial'" :class="tab === 'initial' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-mutedText hover:text-primary'"
                    class="px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                    Initial Setup <span class="ml-2 px-1.5 py-0.5 bg-navy/30 rounded-md text-[10px]">{{ $initialRequests->count() }}</span>
                </button>
                <button @click="tab = 'updates'" :class="tab === 'updates' ? 'bg-primary text-white shadow-lg shadow-primary/20' : 'text-mutedText hover:text-primary'"
                    class="px-6 py-2 rounded-xl text-xs font-black uppercase tracking-widest transition-all">
                    Detail Updates <span class="ml-2 px-1.5 py-0.5 bg-navy/30 rounded-md text-[10px]">{{ $updateRequests->count() }}</span>
                </button>
            </div>
        </div>

        {{-- INITIAL BANK REQUESTS --}}
        <div x-show="tab === 'initial'" x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4">
            <div class="bg-surface rounded-2xl shadow-sm border border-primary/10 overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-primary/5 text-[11px] uppercase font-black text-primary tracking-widest border-b border-primary/5">
                        <tr>
                            <th class="px-6 py-5">User</th>
                            <th class="px-6 py-5">Bank Details</th>
                            <th class="px-6 py-5">Submitted At</th>
                            <th class="px-6 py-5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary/5">
                        @forelse($initialRequests as $bank)
                            <tr class="hover:bg-primary/5 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-mainText">{{ $bank->user->name }}</p>
                                    <p class="text-[10px] text-mutedText">{{ $bank->user->email }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="font-black text-primary text-xs uppercase">{{ $bank->bank_name }}</p>
                                    <p class="text-[10px] text-mutedText font-bold">A/C: {{ Str::mask($bank->account_number, '*', 4, -4) }} | IFSC: {{ $bank->ifsc_code }}</p>
                                </td>
                                <td class="px-6 py-4 text-mutedText font-bold text-xs">
                                    {{ $bank->updated_at->format('d M, Y') }}
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="openInitialModal({{ json_encode($bank) }}, '{{ asset('storage/' . $bank->document_path) }}')"
                                        class="bg-surface border border-primary/20 text-primary px-4 py-2 rounded-xl font-black hover:bg-primary hover:text-white transition-all text-[10px] uppercase tracking-widest">
                                        Review
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-mutedText font-bold italic">No pending initial bank setups.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- UPDATE REQUESTS --}}
        <div x-show="tab === 'updates'" x-cloak x-transition:enter="transition ease-out duration-300 transform" x-transition:enter-start="opacity-0 translate-y-4">
            <div class="bg-surface rounded-2xl shadow-sm border border-primary/10 overflow-hidden">
                <table class="w-full text-left text-sm">
                    <thead class="bg-secondary/5 text-[11px] uppercase font-black text-secondary tracking-widest border-b border-secondary/5">
                        <tr>
                            <th class="px-6 py-5">User</th>
                            <th class="px-6 py-5">Old Details</th>
                            <th class="px-6 py-5">New Details</th>
                            <th class="px-6 py-5 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary/5">
                        @forelse($updateRequests as $req)
                            <tr class="hover:bg-secondary/5 transition-colors">
                                <td class="px-6 py-4">
                                    <p class="font-bold text-mainText">{{ $req->user->name }}</p>
                                    <p class="text-[10px] text-mutedText">{{ $req->user->email }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-[10px] text-mutedText font-black uppercase">{{ $req->old_data['bank_name'] }}</p>
                                    <p class="text-[9px] text-mutedText opacity-60">A/C: ...{{ substr($req->old_data['account_number'], -4) }}</p>
                                </td>
                                <td class="px-6 py-4">
                                    <p class="text-[10px] text-secondary font-black uppercase">{{ $req->new_data['bank_name'] }}</p>
                                    <p class="text-[9px] text-secondary font-bold">A/C: {{ Str::mask($req->new_data['account_number'], '*', 0, -4) }}</p>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="openUpdateModal({{ json_encode($req) }}, '{{ asset('storage/' . $req->document_path) }}')"
                                        class="bg-surface border border-secondary/20 text-secondary px-4 py-2 rounded-xl font-black hover:bg-secondary hover:text-white transition-all text-[10px] uppercase tracking-widest">
                                        Review Update
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-6 py-10 text-center text-mutedText font-bold italic">No pending bank update requests.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        {{-- REVIEW MODAL --}}
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-navy/80 backdrop-blur-md"></div>
            <div class="flex min-h-full items-center justify-center p-4">
                <div @click.away="showModal = false" class="bg-surface w-full max-w-6xl h-[90vh] rounded-3xl flex flex-col md:flex-row overflow-hidden shadow-2xl relative border border-primary/10">

                    <button @click="showModal = false" class="absolute top-4 right-4 z-10 bg-white/10 hover:bg-secondary text-mainText md:text-white p-2 rounded-full transition-all">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>

                    {{-- Left Side: Document --}}
                    <div class="w-full md:w-1/2 bg-black flex flex-col items-center justify-center p-8 border-r border-primary/5">
                        <h4 class="absolute top-6 left-6 text-primary text-[10px] font-black uppercase tracking-[2px]">Submitted Document</h4>
                        <div class="w-full h-full flex items-center justify-center rounded-2xl overflow-hidden border border-white/5 bg-navy/20">
                            <iframe :src="currentDoc" class="w-full h-full border-0" x-show="isPdf"></iframe>
                            <img :src="currentDoc" class="max-w-full max-h-full object-contain" x-show="!isPdf">
                        </div>
                    </div>

                    {{-- Right Side: Details & Action --}}
                    <div class="w-full md:w-1/2 flex flex-col bg-surface">
                        <div class="p-8 border-b border-primary/5">
                            <h3 class="text-2xl font-black text-mainText" x-text="reviewType === 'initial' ? 'Initial Bank Verification' : 'Bank Update Request'"></h3>
                            <p class="text-sm text-mutedText" x-text="reviewType === 'initial' ? 'Verify first-time bank setup details.' : 'Review changes to existing bank account.'"></p>
                        </div>

                        <div class="p-8 flex-1 overflow-y-auto space-y-6">
                            {{-- Data Comparison --}}
                            <div class="grid grid-cols-1 gap-6">
                                <template x-if="reviewType === 'initial'">
                                    <div class="bg-navy/5 p-6 rounded-2xl border border-primary/10">
                                        <h4 class="text-[10px] font-black text-primary uppercase tracking-widest mb-4">Account Details</h4>
                                        <div class="grid grid-cols-2 gap-4">
                                            <div>
                                                <p class="text-[10px] text-mutedText uppercase font-bold">Bank Name</p>
                                                <p class="font-black text-mainText" x-text="initialData.bank_name"></p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-mutedText uppercase font-bold">Holder Name</p>
                                                <p class="font-black text-mainText" x-text="initialData.holder_name"></p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-mutedText uppercase font-bold">A/C Number</p>
                                                <p class="font-black text-mainText tracking-widest uppercase" x-text="initialData.account_number"></p>
                                            </div>
                                            <div>
                                                <p class="text-[10px] text-mutedText uppercase font-bold">IFSC Code</p>
                                                <p class="font-black text-mainText" x-text="initialData.ifsc_code"></p>
                                            </div>
                                        </div>
                                    </div>
                                </template>

                                <template x-if="reviewType === 'update'">
                                    <div class="flex flex-col gap-4">
                                        <div class="bg-navy/5 p-5 rounded-2xl border border-primary/5 opacity-60">
                                            <h4 class="text-[10px] font-black text-mutedText uppercase tracking-widest mb-3">Current Verified Details</h4>
                                            <p class="text-xs font-bold text-mainText" x-text="updateData.old_data.bank_name"></p>
                                            <p class="text-[10px] text-mutedText uppercase">A/C: <span x-text="updateData.old_data.account_number"></span></p>
                                        </div>
                                        <div class="bg-secondary/5 p-6 rounded-2xl border border-secondary/20 shadow-lg shadow-secondary/5">
                                            <h4 class="text-[10px] font-black text-secondary uppercase tracking-widest mb-4">Requested New Details</h4>
                                            <div class="grid grid-cols-2 gap-4">
                                                <div>
                                                    <p class="text-[10px] text-mutedText uppercase font-bold">Bank Name</p>
                                                    <p class="font-black text-secondary" x-text="updateData.new_data.bank_name"></p>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] text-mutedText uppercase font-bold">Holder Name</p>
                                                    <p class="font-black text-secondary" x-text="updateData.new_data.holder_name"></p>
                                                </div>
                                                <div class="col-span-2">
                                                    <p class="text-[10px] text-mutedText uppercase font-bold">New Account Number</p>
                                                    <p class="text-lg font-black text-secondary tracking-widest uppercase" x-text="updateData.new_data.account_number"></p>
                                                </div>
                                                <div>
                                                    <p class="text-[10px] text-mutedText uppercase font-bold">New IFSC</p>
                                                    <p class="font-black text-secondary" x-text="updateData.new_data.ifsc_code"></p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- Footer Actions --}}
                        <div class="p-8 bg-navy/10 border-t border-primary/5">
                            <div class="space-y-4">
                                <div class="flex gap-4" x-show="!showReject">
                                    <button @click="processAction('approve')"
                                        class="flex-1 brand-gradient text-white py-4 rounded-2xl font-black shadow-lg shadow-primary/20 uppercase tracking-widest text-xs">
                                        Approve
                                    </button>
                                    <button @click="showReject = true"
                                        class="flex-1 bg-surface border border-secondary/30 text-secondary py-4 rounded-2xl font-black hover:bg-secondary/5 uppercase tracking-widest text-xs">
                                        Reject
                                    </button>
                                </div>

                                <div x-show="showReject" x-transition class="space-y-4">
                                    <textarea x-model="adminNote" class="w-full border-primary/10 bg-white rounded-xl text-sm p-4 font-medium" rows="3" placeholder="Rejection reason..."></textarea>
                                    <div class="flex justify-end gap-3">
                                        <button @click="showReject = false" class="px-4 py-2 text-xs font-bold text-mutedText uppercase">Cancel</button>
                                        <button @click="processAction('reject')" class="px-6 py-2 bg-secondary text-white text-xs font-black rounded-xl uppercase tracking-widest">Confirm Reject</button>
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
                    if (action === 'reject' && !this.adminNote) return Swal.fire('Error', 'Note required', 'error');

                    let url = '';
                    if (this.reviewType === 'initial') {
                        url = `{{ route('admin.verifications.bank.initial.process', ':id') }}`.replace(':id', this.initialData.id);
                    } else {
                        url = `{{ route('admin.verifications.bank.update.process', ':id') }}`.replace(':id', this.updateData.id);
                    }

                    Swal.fire({
                        title: 'Confirm ' + action.toUpperCase(),
                        text: 'Are you sure you want to ' + action + ' this bank request?',
                        icon: 'question',
                        showCancelButton: true,
                        confirmButtonColor: action === 'approve' ? '#F7941D' : '#e11d48'
                    }).then(res => {
                        if (res.isConfirmed) {
                            axios.post(url, { action, admin_note: this.adminNote }).then(r => {
                                Swal.fire('Done', r.data.message, 'success').then(() => location.reload());
                            }).catch(e => {
                                Swal.fire('Error', e.response.data.message || 'Error processing', 'error');
                            });
                        }
                    });
                }
            }
        }
    </script>
@endsection
