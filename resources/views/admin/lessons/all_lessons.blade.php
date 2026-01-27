@extends('layouts.admin')



@section('content')
    <div x-data="allLessonsManagement('{{ route('admin.lessons.all') }}')" x-init="init()" class="space-y-6">

        <div class="flex justify-between items-center px-2">
            <div>
                <h2 class="text-xl font-bold text-slate-800 tracking-tight">All Lessons List</h2>
                <p class="text-sm text-slate-500 font-medium">Total: {{ $lessons->total() }} Lessons</p>
            </div>

            <a href="{{ route('admin.lessons.create', 0) }}"
                class="inline-flex items-center bg-[#0777be] text-white px-5 py-2.5 rounded-xl font-bold shadow-md hover:bg-[#0777be]/90 transition-all text-sm active:scale-95">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Lesson
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
                <input type="text" x-model="search" @input.debounce.500ms="fetchData()"
                    class="block w-full py-2.5 pr-3 text-sm font-medium border-0 rounded-lg pl-10 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#0777be]/20 text-gray-700"
                    placeholder="Search lessons by title or course...">
            </div>
        </div>

        {{-- Loader --}}
        <div x-show="loading" x-cloak class="flex flex-col items-center justify-center py-20 bg-white border rounded-xl">
            <div class="animate-spin rounded-full h-10 w-10 border-b-2 border-[#0777be]"></div>
            <p class="mt-4 text-sm font-medium text-slate-400 uppercase tracking-widest">Searching Lessons...</p>
        </div>

        {{-- Table Container --}}
        <div x-show="!loading" id="all-lessons-container">
            @include('admin.lessons.partials.all_table')
        </div>
    </div>
@endsection

@push('scripts')
    <script>
        // 1. Toastr Options Configure karein
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "5000",
            "extendedTimeOut": "2000"
        };

        // 2. Session Messages logic
        $(document).ready(function() {
            @if (session('success'))
                toastr.success("{{ session('success') }}");
            @endif

            @if (session('error'))
                toastr.error("{{ session('error') }}");
            @endif

            @if ($errors->any())
                @foreach ($errors->all() as $error)
                    toastr.error("{{ $error }}");
                @endforeach
            @endif
        });

        // 3. Ajax/Alpine Logic
        function allLessonsManagement(apiUrl) {
            return {
                loading: false,
                search: '',
                baseUrl: apiUrl,
                fetchData(page = 1) {
                    this.loading = true;
                    fetch(`${this.baseUrl}?page=${page}&search=${this.search}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.text())
                        .then(html => {
                            document.getElementById('all-lessons-container').innerHTML = html;
                            this.loading = false;
                        })
                        .catch(err => {
                            this.loading = false;
                            toastr.error("Something went wrong while fetching data.");
                        });
                },
                init() {
                    document.getElementById('all-lessons-container').addEventListener('click', (e) => {
                        const link = e.target.closest('.pagination a');
                        if (link) {
                            e.preventDefault();
                            this.fetchData(new URL(link.href).searchParams.get('page'));
                        }
                    });
                }
            }
        }

        // 4. Delete Confirmation logic
        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Are you sure?',
                text: `You want to delete: ${name}`,
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
