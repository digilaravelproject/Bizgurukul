@extends('layouts.admin')

@section('title', 'Manage Courses')

@section('header')
    <h2 class="text-xl font-bold text-slate-800 tracking-tight">LMS: Course Management</h2>
@endsection

@section('content')
    <div x-data="courseManagement('{{ route('admin.courses.index') }}')" x-init="init()" class="space-y-6">

        {{-- Add Button (Right Side) --}}
        <div class="flex justify-end w-full">
            <a href="{{ route('admin.courses.create') }}"
                class="inline-flex items-center bg-[#0777be] text-white px-5 py-2.5 rounded-xl font-bold shadow-md hover:bg-[#0777be]/90 transition-all text-sm active:scale-95 group">
                <svg class="w-4 h-4 mr-2 group-hover:rotate-90 transition-transform duration-300" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Course
            </a>
        </div>

        {{-- Search Bar --}}
        <div class="p-1.5 bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>
                <input type="text" x-model="search" @input.debounce.500ms="applyFilter()"
                    class="block w-full py-2.5 pr-3 text-sm font-medium border-0 rounded-lg pl-10 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#0777be]/20 text-gray-700"
                    placeholder="Search course title...">
            </div>
        </div>

        {{-- Table Load Loader --}}
        <div x-show="loading" x-cloak
            class="flex flex-col items-center justify-center py-20 bg-white border border-gray-100 rounded-xl shadow-sm">
            <svg class="w-10 h-10 text-[#0777be] animate-spin" xmlns="http://www.w3.org/2000/svg" fill="none"
                viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                </circle>
                <path class="opacity-75" fill="currentColor"
                    d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z">
                </path>
            </svg>
            <span class="mt-3 text-sm font-medium text-slate-500">Loading courses...</span>
        </div>

        {{-- Table Container --}}
        <div x-show="!loading" id="courses-table-container">
            @include('admin.lms.partials.table')
        </div>
    </div>
@endsection

@push('scripts')
    {{-- Toastr CSS/JS --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // Standard Laravel Session Toastr
        @if (session('success'))
            toastr.success("{{ session('success') }}");
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
                        .then(r => r.text())
                        .then(html => {
                            document.getElementById('courses-table-container').innerHTML = html;
                            this.loading = false;
                        })
                        .catch(err => {
                            console.error(err);
                            this.loading = false;
                        });
                },
                init() {
                    document.getElementById('courses-table-container').addEventListener('click', (e) => {
                        const link = e.target.closest('.pagination-wrapper a');
                        if (link) {
                            e.preventDefault();
                            this.fetchData(new URL(link.href).searchParams.get('page'));
                        }
                    });
                }
            }
        }

        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Are you sure?',
                html: `Delete course: <b>${name}</b>`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            });
        }
    </script>
@endpush
