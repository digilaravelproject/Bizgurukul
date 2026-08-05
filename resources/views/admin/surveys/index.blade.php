@extends('layouts.admin')
@section('title', 'Survey Management')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div x-data="surveyManager()" x-init="init()" class="container-fluid font-sans antialiased text-mainText">

        {{-- TOP HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 animate-fade-in">
            <div>
                <h2 class="text-2xl font-black tracking-tight">Survey Management</h2>
                <p class="text-sm text-mutedText mt-1 font-medium">Create and manage feedback surveys for students.</p>
            </div>

            <button @click="openModal('create')"
                class="brand-gradient inline-flex items-center justify-center gap-2 rounded-xl px-6 py-3 text-xs font-black text-white shadow-lg shadow-primary/25 hover:-translate-y-0.5 transition-all duration-300">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                New Question
            </button>
        </div>

        {{-- TABLE --}}
        <div class="bg-white rounded-[2rem] border border-primary/10 shadow-xl overflow-hidden animate-fade-in">
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead
                        class="bg-primary/5 text-[10px] uppercase font-black text-primary border-b border-primary/5 tracking-widest">
                        <tr>
                            <th class="px-8 py-6">Question</th>
                            <th class="px-6 py-6 text-center">Type</th>
                            <th class="px-6 py-6 text-center">Options</th>
                            <th class="px-6 py-6 text-center">Status</th>
                            <th class="px-8 py-6 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-primary/5">
                        <template x-for="q in questions" :key="q.id">
                            <tr class="hover:bg-primary/[0.02] transition-colors group">
                                <td class="px-8 py-5">
                                    <div class="font-bold text-mainText max-w-md truncate" x-text="q.question"></div>
                                    <div class="text-[10px] text-mutedText mt-0.5"
                                        x-text="q.is_required ? 'Required' : 'Optional'"></div>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider"
                                        :class="q.type === 'options' ? 'bg-indigo-50 text-indigo-600' : 'bg-amber-50 text-amber-600'"
                                        x-text="q.type">
                                    </span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <span class="text-xs font-bold text-mutedText" x-text="q.options_count || 0"></span>
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <button @click="toggleStatus(q.id)"
                                        class="px-3 py-1 rounded-lg text-[10px] font-black uppercase tracking-wider transition-all"
                                        :class="q.is_active ? 'bg-green-50 text-green-600' : 'bg-red-50 text-red-600'"
                                        x-text="q.is_active ? 'Active' : 'Inactive'">
                                    </button>
                                </td>
                                <td class="px-8 py-5 text-right">
                                    <div
                                        class="flex justify-end gap-2 opacity-0 group-hover:opacity-100 transition-opacity">
                                        <button @click="openModal('edit', q)"
                                            class="p-2 hover:bg-primary/5 rounded-xl text-primary transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z">
                                                </path>
                                            </svg>
                                        </button>
                                        <button @click="deleteQuestion(q.id)"
                                            class="p-2 hover:bg-secondary/5 rounded-xl text-secondary transition-all">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16">
                                                </path>
                                            </svg>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        </template>
                        <tr x-show="questions.length === 0">
                            <td colspan="5" class="py-20 text-center">
                                <div class="flex flex-col items-center gap-4">
                                    <div class="bg-navy p-6 rounded-[2rem] border border-primary/5">
                                        <svg class="w-12 h-12 text-primary/20" fill="none" stroke="currentColor"
                                            viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z">
                                            </path>
                                        </svg>
                                    </div>
                                    <p class="text-sm font-bold text-mutedText/50 italic">No survey questions found.</p>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        {{-- CREATE / EDIT MODAL --}}
        <div x-show="showModal" x-cloak class="fixed inset-0 z-50 overflow-y-auto">
            <div class="fixed inset-0 bg-black/60 backdrop-blur-sm" @click="showModal = false"></div>
            <div class="flex min-h-full items-center justify-center p-4 sm:p-6">
                <div @click.away="showModal = false"
                    class="relative w-full max-w-2xl rounded-[2.5rem] bg-white border-2 border-primary/20 shadow-2xl overflow-hidden my-auto max-h-[90vh] flex flex-col"
                    x-show="showModal" x-transition:enter="ease-out duration-300"
                    x-transition:enter-start="opacity-0 translate-y-8" x-transition:enter-end="opacity-100 translate-y-0">

                    <div class="brand-gradient h-2.5 flex-shrink-0"></div>

                    <div class="px-8 py-6 border-b-2 border-primary/10 flex justify-between items-center bg-amber-500/5">
                        <div>
                            <h3 class="text-xl font-black text-mainText"
                                x-text="modalMode === 'create' ? 'Add New Question' : 'Edit Question'"></h3>
                            <p class="text-xs text-mutedText font-bold mt-0.5">Define your survey question and criteria.</p>
                        </div>
                        <button @click="showModal = false"
                            class="text-mutedText hover:text-secondary bg-white border border-primary/20 hover:border-secondary rounded-2xl p-2.5 shadow-sm transition-all">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                    d="M6 18L18 6M6 6l12 12"></path>
                            </svg>
                        </button>
                    </div>

                    <form @submit.prevent="submitForm" class="p-6 sm:p-8 overflow-y-auto flex-1 custom-scrollbar">
                        <div class="space-y-6">
                            <div>
                                <label
                                    class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Question
                                    Text <span class="text-secondary">*</span></label>
                                <textarea x-model="form.question" required rows="3"
                                    class="w-full rounded-2xl bg-amber-500/5 px-5 py-4 text-sm font-bold text-mainText border-2 border-primary/20 focus:border-primary focus:bg-white transition-all outline-none"
                                    placeholder="e.g., How satisfied are you with our platform?"></textarea>
                            </div>

                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 items-start">
                                <div>
                                    <label
                                        class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Question
                                        Type</label>
                                    <div class="relative">
                                        <select x-model="form.type"
                                            class="w-full rounded-2xl bg-amber-500/5 px-5 h-14 text-sm font-bold text-mainText border-2 border-primary/20 focus:border-primary focus:bg-white outline-none appearance-none cursor-pointer pr-10">
                                            <option value="options">Multiple Choice (Options)</option>
                                            <option value="text">Free Text Input</option>
                                        </select>
                                        <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none text-mutedText">
                                            <i class="fas fa-chevron-down text-xs"></i>
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    <label
                                        class="block text-[10px] font-black uppercase tracking-widest text-mutedText mb-2 ml-1">Requirement</label>
                                    <div class="flex items-center h-14 bg-amber-500/5 border-2 border-primary/20 rounded-2xl px-5 hover:border-primary/50 transition-all">
                                        <label class="inline-flex items-center cursor-pointer group w-full">
                                            <input type="checkbox" x-model="form.is_required" class="hidden">
                                            <div class="w-5 h-5 rounded-lg border-2 border-primary/40 flex items-center justify-center transition-all group-hover:border-primary mr-3 flex-shrink-0"
                                                :class="form.is_required ? 'bg-primary border-primary' : 'bg-white'">
                                                <svg x-show="form.is_required" class="w-3.5 h-3.5 text-white" fill="none"
                                                    stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3"
                                                        d="M5 13l4 4L19 7"></path>
                                                </svg>
                                            </div>
                                            <span class="text-sm font-bold text-mainText">Mandatory Field</span>
                                        </label>
                                    </div>
                                </div>
                            </div>

                            <div x-show="form.type === 'options'" x-transition
                                class="space-y-4 pt-4 border-t-2 border-primary/10">
                                <div class="flex justify-between items-center mb-2">
                                    <label
                                        class="block text-[10px] font-black uppercase tracking-widest text-mutedText ml-1">Answer
                                        Options</label>
                                    <button type="button" @click="addOption()"
                                        class="text-primary hover:text-secondary text-[10px] font-black uppercase tracking-widest flex items-center gap-1 bg-primary/10 hover:bg-primary/20 px-3 py-1.5 rounded-xl transition-all border border-primary/20">
                                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                                                d="M12 4v16m8-8H4"></path>
                                        </svg>
                                        Add Option
                                    </button>
                                </div>

                                <template x-for="(opt, index) in form.options" :key="index">
                                    <div class="flex gap-3 items-center">
                                        <input type="text" x-model="opt.text" required
                                            class="flex-1 rounded-2xl bg-amber-500/5 px-5 py-3.5 text-sm font-bold text-mainText border-2 border-primary/20 focus:border-primary focus:bg-white outline-none transition-all"
                                            placeholder="Enter option text...">
                                        <button type="button" @click="removeOption(index)"
                                            class="p-3 text-secondary hover:text-white hover:bg-secondary rounded-2xl border-2 border-secondary/20 transition-all shadow-sm">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                                    d="M6 18L18 6M6 6l12 12"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </template>

                                <div x-show="form.options.length === 0"
                                    class="text-center py-6 bg-amber-500/5 rounded-2xl border-2 border-dashed border-primary/20">
                                    <p class="text-xs font-bold text-mutedText">No options added yet. Click '+ Add Option'
                                        above.</p>
                                </div>
                            </div>
                        </div>

                        <div class="mt-8 flex justify-end gap-3 pt-6 border-t-2 border-primary/10">
                            <button type="button" @click="showModal = false"
                                class="px-6 py-3.5 text-xs font-black uppercase tracking-widest text-mutedText hover:text-secondary transition-all">Cancel</button>
                            <button type="submit" :disabled="isSubmitting"
                                class="brand-gradient px-8 py-3.5 text-xs font-black uppercase tracking-widest text-white rounded-2xl shadow-lg shadow-primary/20 disabled:opacity-50 transition-all flex items-center gap-2">
                                <svg x-show="isSubmitting" class="animate-spin -ml-1 h-4 w-4 text-white"
                                    viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor"
                                        stroke-width="4"></circle>
                                    <path class="opacity-75" fill="currentColor"
                                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                                    </path>
                                </svg>
                                <span x-text="isSubmitting ? 'Processing...' : 'Save Question'"></span>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>

    <script>
        function surveyManager() {
            return {
                questions: @json($questions->items()),
                showModal: false,
                modalMode: 'create',
                isSubmitting: false,
                form: {
                    id: null,
                    question: '',
                    type: 'options',
                    is_required: true,
                    options: []
                },

                init() {
                    this.Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 2000,
                        timerProgressBar: true,
                    });
                },

                openModal(mode, q = null) {
                    this.modalMode = mode;
                    this.showModal = true;
                    if (mode === 'edit' && q) {
                        this.form = {
                            id: q.id,
                            question: q.question,
                            type: q.type,
                            is_required: q.is_required,
                            options: (q.options || []).map(o => ({ id: o.id, text: o.option_text }))
                        };
                    } else {
                        this.form = {
                            id: null,
                            question: '',
                            type: 'options',
                            is_required: true,
                            options: [{ text: '' }]
                        };
                    }
                },

                addOption() {
                    this.form.options.push({ text: '' });
                },

                removeOption(index) {
                    this.form.options.splice(index, 1);
                },

                async submitForm() {
                    this.isSubmitting = true;
                    const url = this.modalMode === 'create'
                        ? "{{ route('admin.surveys.store') }}"
                        : `/admin/surveys/update/${this.form.id}`;

                    try {
                        const response = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(this.form)
                        });

                        const result = await response.json();
                        if (!response.ok) throw result;

                        this.showModal = false;
                        location.reload(); // Simple reload to refresh table
                    } catch (error) {
                        Swal.fire({
                            icon: 'error',
                            title: 'Validation Error',
                            text: error.message || 'Something went wrong'
                        });
                    } finally {
                        this.isSubmitting = false;
                    }
                },

                async toggleStatus(id) {
                    try {
                        const response = await fetch(`/admin/surveys/toggle-status/${id}`, {
                            method: 'POST',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                        const result = await response.json();
                        if (result.status) {
                            this.questions = this.questions.map(q => {
                                if (q.id === id) q.is_active = !q.is_active;
                                return q;
                            });
                        }
                    } catch (e) {
                        this.Toast.fire({ icon: 'error', title: 'Action failed' });
                    }
                },

                async deleteQuestion(id) {
                    const result = await Swal.fire({
                        title: 'Are you sure?',
                        text: "This question and all user responses will be deleted!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#secondary',
                        confirmButtonText: 'Yes, delete it!'
                    });

                    if (result.isConfirmed) {
                        try {
                            const response = await fetch(`/admin/surveys/delete/${id}`, {
                                method: 'DELETE',
                                headers: {
                                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                    'Accept': 'application/json'
                                }
                            });
                            location.reload();
                        } catch (e) {
                            this.Toast.fire({ icon: 'error', title: 'Delete failed' });
                        }
                    }
                }
            }
        }
    </script>
@endsection
