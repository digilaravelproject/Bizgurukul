@extends('layouts.admin')
@section('title', 'Contact Inquiries')

@section('content')
    <div x-data="inquiryManager()" x-init="init()" class="container-fluid font-sans antialiased">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 animate-fade-in">
            <div>
                <h2 class="text-2xl font-black text-mainText tracking-tight">Contact Inquiries</h2>
                <p class="text-sm text-mutedText mt-1 font-medium">View and manage messages sent from the contact form.</p>
            </div>
            <div x-show="selectedIds.length > 0" x-transition class="flex items-center gap-3">
                <button @click="bulkMarkRead()" 
                        :disabled="isLoading"
                        class="bg-emerald-600 hover:bg-emerald-700 text-white px-5 py-2.5 rounded-xl text-xs font-black uppercase tracking-wider transition-all shadow-lg shadow-emerald-600/20 active:scale-95 flex items-center gap-2">
                    <i class="fas fa-check-double text-xs"></i>
                    <span>Mark Selected as Read (<span x-text="selectedIds.length"></span>)</span>
                </button>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 font-bold text-sm animate-fade-in">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

        {{-- Toast / Alert Notification --}}
        <div x-show="toast.text" 
             x-transition:enter="transition ease-out duration-300"
             x-transition:enter-start="opacity-0 -translate-y-4"
             x-transition:enter-end="opacity-100 translate-y-0"
             x-transition:leave="transition ease-in duration-200"
             x-transition:leave-start="opacity-100 translate-y-0"
             x-transition:leave-end="opacity-0 -translate-y-4"
             class="mb-6 p-4 rounded-2xl font-bold text-sm border shadow-sm flex items-center justify-between"
             :class="toast.type === 'success' ? 'bg-emerald-500/10 border-emerald-500/20 text-emerald-500' : 'bg-secondary/10 border-secondary/20 text-secondary'"
             style="display: none;">
            <div class="flex items-center gap-2">
                <i :class="toast.type === 'success' ? 'fas fa-check-circle' : 'fas fa-exclamation-circle'"></i>
                <span x-text="toast.text"></span>
            </div>
            <button @click="toast.text = ''" class="text-xs opacity-60 hover:opacity-100">&times;</button>
        </div>

        {{-- Filter Bar --}}
        <x-admin.table.filter
            placeholder="Search name, email, subject..."
            :show-date-filter="false"
            :show-export="false"
        />

        <div class="relative min-h-[400px]">
            {{-- Loading Overlay --}}
            <div x-show="isLoading" x-transition.opacity class="absolute inset-0 z-10 bg-white/50 backdrop-blur-[2px] flex items-center justify-center rounded-[2rem]">
                <div class="w-12 h-12 border-4 border-primary/20 border-t-primary rounded-full animate-spin"></div>
            </div>

            <div class="overflow-hidden rounded-[2rem] border border-primary/10 bg-white shadow-xl relative animate-fade-in">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-mutedText">
                        <thead class="bg-primary/5 text-[10px] uppercase font-black text-primary border-b border-primary/5 tracking-widest">
                            <tr>
                                <th class="px-6 py-5 w-10">
                                    <input type="checkbox" 
                                           x-model="selectAll" 
                                           @change="toggleSelectAll()"
                                           class="w-4 h-4 rounded border-primary/20 text-primary focus:ring-primary/20 cursor-pointer">
                                </th>
                                <th class="px-6 py-5">Date</th>
                                <th class="px-6 py-5">User</th>
                                <th class="px-6 py-5">Subject</th>
                                <th class="px-6 py-5">Status</th>
                                <th class="px-6 py-5 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody id="inquiryTableBody" class="divide-y divide-primary/5">
                            @include('admin.contact-inquiries.partials.table', ['inquiries' => $inquiries])
                        </tbody>
                    </table>
                </div>
                <div id="inquiryPagination" class="bg-primary/5 border-t border-primary/5">
                    @include('admin.contact-inquiries.partials.pagination', ['inquiries' => $inquiries])
                </div>
            </div>
        </div>
    </div>

    <script>
        function inquiryManager() {
            return {
                isLoading: false,
                search: '',
                perPage: 20,
                startDate: '',
                endDate: '',
                lastUrl: "{{ route('admin.contact-inquiries.index') }}",
                selectedIds: [],
                selectAll: false,
                toast: { text: '', type: 'success' },
                
                init() {
                    // Initial load if needed or set defaults
                },

                showToast(text, type = 'success') {
                    this.toast = { text, type };
                    setTimeout(() => {
                        if (this.toast.text === text) this.toast.text = '';
                    }, 4000);
                },

                toggleSelectAll() {
                    let checkboxes = document.querySelectorAll('#inquiryTableBody input[type="checkbox"]');
                    this.selectedIds = [];
                    if (this.selectAll) {
                        checkboxes.forEach(cb => {
                            this.selectedIds.push(cb.value);
                        });
                    }
                },

                updateSelectAllState() {
                    let checkboxes = document.querySelectorAll('#inquiryTableBody input[type="checkbox"]');
                    this.selectAll = checkboxes.length > 0 && this.selectedIds.length === checkboxes.length;
                },

                updateTable() {
                    this.fetchInquiries();
                },

                resetFilters() {
                    this.search = '';
                    this.perPage = 20;
                    this.startDate = '';
                    this.endDate = '';
                    this.fetchInquiries("{{ route('admin.contact-inquiries.index') }}");
                },

                goToPage(url) {
                    if (url) this.fetchInquiries(url);
                },

                async markAsRead(id) {
                    this.isLoading = true;
                    try {
                        let response = await fetch("{{ url('admin/contact-inquiries') }}/" + id + "/mark-read", {
                            method: "POST",
                            headers: {
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "X-Requested-With": "XMLHttpRequest",
                                "Accept": "application/json"
                            }
                        });
                        let result = await response.json();
                        if (result.status) {
                            this.showToast(result.message || 'Marked as read successfully.');
                            await this.fetchInquiries();
                        } else {
                            this.showToast(result.message || 'Failed to mark as read.', 'error');
                        }
                    } catch (error) {
                        console.error('Mark read error:', error);
                        this.showToast('Something went wrong.', 'error');
                    } finally {
                        this.isLoading = false;
                    }
                },

                async bulkMarkRead() {
                    if (this.selectedIds.length === 0) return;

                    this.isLoading = true;
                    try {
                        let response = await fetch("{{ route('admin.contact-inquiries.bulk-mark-read') }}", {
                            method: "POST",
                            headers: {
                                "Content-Type": "application/json",
                                "X-CSRF-TOKEN": "{{ csrf_token() }}",
                                "X-Requested-With": "XMLHttpRequest",
                                "Accept": "application/json"
                            },
                            body: JSON.stringify({ ids: this.selectedIds })
                        });
                        let result = await response.json();
                        if (result.status) {
                            this.showToast(result.message || 'Selected inquiries marked as read.');
                            this.selectedIds = [];
                            this.selectAll = false;
                            await this.fetchInquiries();
                        } else {
                            this.showToast(result.message || 'Failed to mark selected inquiries as read.', 'error');
                        }
                    } catch (error) {
                        console.error('Bulk mark read error:', error);
                        this.showToast('Something went wrong.', 'error');
                    } finally {
                        this.isLoading = false;
                    }
                },

                async fetchInquiries(url = null) {
                    let targetUrlRaw = url || this.lastUrl;
                    this.lastUrl = targetUrlRaw;

                    this.isLoading = true;
                    try {
                        let targetUrl = new URL(targetUrlRaw.includes('http') ? targetUrlRaw : window.location.origin + targetUrlRaw);

                        targetUrl.searchParams.set('search', this.search || '');
                        targetUrl.searchParams.set('per_page', this.perPage || 20);
                        if(this.startDate) targetUrl.searchParams.set('start_date', this.startDate);
                        if(this.endDate) targetUrl.searchParams.set('end_date', this.endDate);
                        targetUrl.searchParams.set('_t', new Date().getTime());

                        let response = await fetch(targetUrl, {
                            headers: {
                                "X-Requested-With": "XMLHttpRequest",
                                "Accept": "application/json"
                            }
                        });

                        let result = await response.json();
                        if (result.status) {
                            document.getElementById('inquiryTableBody').innerHTML = result.table;
                            document.getElementById('inquiryPagination').innerHTML = result.pagination;
                            this.selectedIds = [];
                            this.selectAll = false;
                        }
                    } catch (error) {
                        console.error('Fetch error:', error);
                    } finally {
                        this.isLoading = false;
                    }
                }
            }
        }
    </script>
@endsection
