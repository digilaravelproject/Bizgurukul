@extends('layouts.admin')

@section('title', 'Manage Courses')

@section('content')
    <div x-data="courseManagement('{{ route('admin.courses.index') }}')" x-init="init()" class="space-y-6">

        {{-- Top Action Bar --}}
        <div class="flex justify-end">
            <a href="{{ route('admin.courses.create') }}"
                class="inline-flex items-center bg-[#0777be] text-white px-5 py-2 rounded-xl font-bold shadow-md hover:bg-[#0777be]/90 transition-all text-sm">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path>
                </svg>
                Add New Course
            </a>
        </div>

        {{-- Filter/Search --}}
        <div class="p-1.5 bg-white border border-gray-200 shadow-sm rounded-xl">
            <div class="relative w-full">
                <div class="absolute inset-y-0 left-0 flex items-center pl-3 pointer-events-none">
                    <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                    </svg>
                </div>

                <input type="text" x-model="search" @input.debounce.500ms="applyFilter()"
                    class="block w-full py-2.5 pr-3 text-sm font-medium placeholder-gray-400 transition-all border-0 rounded-lg pl-10 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#0777be]/20 text-gray-700"
                    placeholder="Search course title...">
            </div>
        </div>

        {{-- Loading Spinner --}}
        <div x-show="loading" class="flex justify-center py-20 bg-white border border-gray-100 rounded-xl"
            style="display: none;">
            <div class="flex flex-col items-center gap-3">
                <svg class="w-10 h-10 text-[#0777be] animate-spin" fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4">
                    </circle>
                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                <span class="text-sm font-medium text-gray-500">Updating Results...</span>
            </div>
        </div>

        {{-- Table Container --}}
        <div x-show="!loading" id="courses-table-container">
            @include('admin.lms.partials.table')
        </div>

    </div>
@endsection

@push('scripts')
    <script>
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

                    const params = new URLSearchParams();
                    params.append('page', page);
                    if (this.search) params.append('search', this.search);

                    fetch(`${this.baseUrl}?${params.toString()}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(res => res.text())
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
                    document.getElementById('courses-table-container')
                        .addEventListener('click', (e) => {
                            const link = e.target.closest('.pagination-wrapper a');
                            if (link) {
                                e.preventDefault();
                                const url = new URL(link.href);
                                this.fetchData(url.searchParams.get('page'));
                            }
                        });
                }
            }
        }
    </script>
@endpush
