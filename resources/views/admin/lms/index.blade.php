@extends('layouts.admin')

@section('title', 'LMS Management')

@section('header')
    <h2 class="text-xl font-bold text-slate-800 tracking-tight">LMS: Management Dashboard</h2>
@endsection

@section('content')
    {{-- Tabs Logic with Alpine.js --}}
    <div x-data="{ tab: 'courses' }" class="space-y-6">

        {{-- Navigation Tabs --}}
        <div class="flex space-x-2 bg-slate-200/50 p-1 rounded-2xl w-fit">
            <button @click="tab = 'courses'"
                :class="tab === 'courses' ? 'bg-white text-[#0777be] shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                class="px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest transition-all">
                Single Courses
            </button>
            <button @click="tab = 'bundles'"
                :class="tab === 'bundles' ? 'bg-white text-[#0777be] shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                class="px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest transition-all">
                Course Bundles
            </button>
        </div>

        {{-- 1. SINGLE COURSES TAB --}}
        <div x-show="tab === 'courses'" x-transition:enter="transition ease-out duration-300" class="space-y-6">
            <div x-data="courseManagement('{{ route('admin.courses.index') }}')" x-init="init()" class="space-y-6">

                {{-- Top Bar --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="p-1.5 bg-white border border-gray-200 shadow-sm rounded-2xl flex-1 max-w-md">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" x-model="search" @input.debounce.500ms="applyFilter()"
                                class="block w-full py-2.5 pr-3 text-sm font-medium border-0 rounded-xl pl-10 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#0777be]/20 text-gray-700 transition-all"
                                placeholder="Search single courses...">
                        </div>
                    </div>

                    <a href="{{ route('admin.courses.create') }}"
                        class="inline-flex items-center bg-[#0777be] text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-blue-100 hover:bg-[#0666a3] transition-all text-sm active:scale-95 group">
                        <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-300" fill="none"
                            stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                        </svg>
                        Create New Course
                    </a>
                </div>

                {{-- Loader --}}
                <div x-show="loading" x-transition x-cloak
                    class="flex flex-col items-center justify-center py-24 bg-white border border-slate-100 rounded-[2rem]">
                    <div class="animate-spin rounded-full h-12 w-12 border-t-4 border-b-4 border-[#0777be]"></div>
                    <span class="mt-4 text-sm font-bold text-slate-500 uppercase tracking-widest">Updating Course
                        List...</span>
                </div>

                {{-- Table --}}
                <div x-show="!loading" x-transition id="courses-table-container">
                    @include('admin.lms.partials.table')
                </div>
            </div>
        </div>

        {{-- 2. COURSE BUNDLES TAB --}}
        <div x-show="tab === 'bundles'" x-transition:enter="transition ease-out duration-300" x-cloak class="space-y-6">
            <div class="flex justify-between items-center">
                <h3 class="text-lg font-black text-slate-800 tracking-tight uppercase">Smart Package Bundles</h3>
                <a href="{{ route('admin.bundles.create') }}"
                    class="inline-flex items-center bg-[#0777be] text-white px-6 py-3 rounded-2xl font-bold shadow-lg shadow-blue-100 hover:bg-[#0666a3] transition-all text-sm active:scale-95 group">
                    <svg class="w-5 h-5 mr-2 group-hover:rotate-90 transition-transform duration-300" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                    </svg>
                    Create New Bundle
                </a>
            </div>

            {{-- Bundles Table Container --}}
            <div id="bundles-table-container">
                @include('admin.lms.partials.bundle_table')
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        function courseManagement(urlIndex) {
            return {
                search: '',
                loading: false,
                baseUrl: urlIndex,
                applyFilter() {
                    this.fetchData(1);
                },
                fetchData(page = 1) {
                    this.loading = true;
                    const params = new URLSearchParams({
                        page: page,
                        search: this.search
                    });
                    fetch(`${this.baseUrl}?${params.toString()}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            if (data.courses) document.getElementById('courses-table-container').innerHTML = data
                                .courses;
                            if (data.bundles && document.getElementById('bundles-table-container')) {
                                document.getElementById('bundles-table-container').innerHTML = data.bundles;
                            }
                            this.loading = false;
                        }).catch(err => {
                            console.error(err);
                            toastr.error("Failed to sync with server.");
                            this.loading = false;
                        });
                },
                init() {
                    document.getElementById('courses-table-container').addEventListener('click', (e) => {
                        const link = e.target.closest('.pagination a');
                        if (link) {
                            e.preventDefault();
                            this.fetchData(new URL(link.href).searchParams.get('page'));
                            window.scrollTo({
                                top: 0,
                                behavior: 'smooth'
                            });
                        }
                    });
                }
            }
        }

        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Are you sure?',
                html: `Item <b>${name}</b> will be permanently removed!`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, Delete it'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }

        function confirmBundleDelete(id, name) {
            Swal.fire({
                title: 'Delete Bundle?',
                html: `Are you sure you want to delete <b>${name}</b>?<br><small class="text-slate-400">Courses inside this bundle will become available again.</small>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, Delete Bundle'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('bundle-delete-form-' + id).submit();
                }
            });
        }
    </script>
@endpush
