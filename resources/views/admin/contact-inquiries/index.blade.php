@extends('layouts.admin')
@section('title', 'Contact Inquiries')

@section('content')
    <div x-data="inquiryManager()" x-init="init()" class="container-fluid font-sans antialiased">
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 animate-fade-in">
            <div>
                <h2 class="text-2xl font-black text-mainText tracking-tight">Contact Inquiries</h2>
                <p class="text-sm text-mutedText mt-1 font-medium">View and manage messages sent from the contact form.</p>
            </div>
        </div>

        @if(session('success'))
            <div class="mb-6 p-4 rounded-2xl bg-emerald-500/10 border border-emerald-500/20 text-emerald-500 font-bold text-sm animate-fade-in">
                <i class="fas fa-check-circle mr-2"></i> {{ session('success') }}
            </div>
        @endif

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
                                <th class="px-8 py-5">Date</th>
                                <th class="px-8 py-5">User</th>
                                <th class="px-8 py-5">Subject</th>
                                <th class="px-8 py-5">Status</th>
                                <th class="px-8 py-5 text-right">Actions</th>
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
                
                init() {
                    // Initial load if needed or set defaults
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
