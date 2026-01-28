@extends('layouts.admin')

@section('title', 'LMS Management')

@section('header')
    <h2 class="text-xl font-bold text-slate-800 tracking-tight">LMS: Management Dashboard</h2>
@endsection

@section('content')
    <div x-data="{ mainTab: 'courses' }" class="space-y-6">
        {{-- Navigation Tabs --}}
        <div class="flex space-x-2 bg-slate-200/50 p-1.5 rounded-2xl w-fit">
            <button @click="mainTab = 'courses'"
                :class="mainTab === 'courses' ? 'bg-white text-[#0777be] shadow-sm' : 'text-slate-500'"
                class="px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest transition-all">Single
                Courses</button>
            <button @click="mainTab = 'bundles'"
                :class="mainTab === 'bundles' ? 'bg-white text-[#0777be] shadow-sm' : 'text-slate-500'"
                class="px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest transition-all">Course
                Bundles</button>
            <button @click="mainTab = 'lessons'"
                :class="mainTab === 'lessons' ? 'bg-white text-[#0777be] shadow-sm' : 'text-slate-500'"
                class="px-8 py-3 rounded-xl font-black text-xs uppercase tracking-widest transition-all">Lessons (Video
                HLS)</button>
        </div>

        @foreach (['courses', 'bundles', 'lessons'] as $type)
            <div x-show="mainTab === '{{ $type }}'" x-data="tabHandler('{{ route('admin.courses.index') }}', '{{ $type }}')" x-init="init()"
                class="space-y-6">

                {{-- Search Bar --}}
                <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="p-1.5 bg-white border border-gray-200 shadow-sm rounded-2xl flex-1 max-w-md">
                        <div class="relative">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-3">
                                <svg class="w-5 h-5 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
                                </svg>
                            </div>
                            <input type="text" x-model="search" @input.debounce.500ms="applyFilter()"
                                class="block w-full py-2.5 pr-3 text-sm font-medium border-0 rounded-xl pl-10 bg-gray-50 focus:bg-white focus:ring-2 focus:ring-[#0777be]/20 transition-all"
                                placeholder="Search {{ $type }}...">
                        </div>
                    </div>

                    @if ($type === 'courses')
                        <a href="{{ route('admin.courses.create') }}"
                            class="bg-[#0777be] text-white px-6 py-3 rounded-2xl font-bold text-sm shadow-lg shadow-blue-100">+
                            Create Course</a>
                    @elseif($type === 'bundles')
                        <a href="{{ route('admin.bundles.create') }}"
                            class="bg-[#0777be] text-white px-6 py-3 rounded-2xl font-bold text-sm shadow-lg shadow-blue-100">+
                            Create Bundle</a>
                    @elseif($type === 'lessons')
                        <a href="{{ route('admin.lessons.create', ['course_id' => $courses->first()->id ?? 0]) }}"
                            class="bg-[#0777be] text-white px-6 py-3 rounded-2xl font-bold text-sm shadow-lg shadow-blue-100">+
                            Add Lesson</a>
                    @endif
                </div>

                {{-- Loader --}}
                <div x-show="loading"
                    class="flex flex-col items-center justify-center py-20 bg-white border border-slate-100 rounded-[2rem]">
                    <div class="animate-spin rounded-full h-10 w-10 border-t-2 border-[#0777be]"></div>
                    <p class="mt-4 text-[10px] font-black text-slate-400 uppercase tracking-widest">Refreshing
                        {{ $type }}...</p>
                </div>

                <div x-show="!loading" id="{{ $type }}-container">
                    @if ($type === 'courses')
                        @include('admin.lms.partials.table', ['courses' => $courses])
                    @elseif($type === 'bundles')
                        @include('admin.lms.partials.bundle_table', ['bundles' => $bundles])
                    @elseif($type === 'lessons')
                        @include('admin.lessons.partials.all_table', ['lessons' => $lessons])
                    @endif
                </div>
            </div>
        @endforeach
    </div>
@endsection

@push('scripts')
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <script>
        // 1. Toastr Global Configuration
        toastr.options = {
            "closeButton": true,
            "progressBar": true,
            "positionClass": "toast-top-right",
            "timeOut": "3000"
        };

        // 2. Flash Messages on Load
        @if (session('success'))
            toastr.success("{{ session('success') }}");
        @endif
        @if (session('error'))
            toastr.error("{{ session('error') }}");
        @endif

        // 3. Delete Confirmation Functions
        function confirmDelete(id, name) {
            Swal.fire({
                title: 'Delete Course?',
                text: `Are you sure you want to delete ${name}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, Delete'
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('delete-form-' + id).submit();
            });
        }

        function confirmBundleDelete(id, name) {
            Swal.fire({
                title: 'Delete Bundle?',
                text: `Are you sure you want to delete ${name}?`,
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, Delete Bundle'
            }).then((result) => {
                if (result.isConfirmed) document.getElementById('bundle-delete-form-' + id).submit();
            });
        }

        function confirmLessonDelete(id, name) {
            Swal.fire({
                title: 'Delete Lesson?',
                text: "Are you sure you want to delete " + name + "? This will remove the video permanently.",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#ef4444',
                confirmButtonText: 'Yes, Delete'
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('lesson-delete-form-' + id).submit();
                }
            });
        }

        function tabHandler(url, tabType) {
            return {
                search: '',
                loading: false,
                baseUrl: url,
                currentTab: tabType,
                applyFilter() {
                    this.fetchData(1);
                },
                fetchData(page = 1) {
                    this.loading = true;
                    const params = new URLSearchParams({
                        page: page,
                        search: this.search,
                        tab: this.currentTab
                    });

                    fetch(`${this.baseUrl}?${params.toString()}`, {
                            headers: {
                                'X-Requested-With': 'XMLHttpRequest'
                            }
                        })
                        .then(r => r.json())
                        .then(data => {
                            const container = document.getElementById(this.currentTab + '-container');
                            if (container && data[this.currentTab]) {
                                container.innerHTML = data[this.currentTab];

                                // --- FIX: Yahan se toastr.info() hata diya gaya hai ---
                                // Ab search karte waqt baar-baar popup nahi aayega.
                            }
                            this.loading = false;
                        }).catch(() => {
                            // Sirf error hone par hi message dikhayenge
                            toastr.error("Failed to sync " + this.currentTab);
                            this.loading = false;
                        });
                },
                init() {
                    const container = document.getElementById(this.currentTab + '-container');
                    if (container) {
                        container.addEventListener('click', (e) => {
                            const link = e.target.closest('.pagination a');
                            if (link) {
                                e.preventDefault();
                                this.fetchData(new URL(link.href).searchParams.get('page') || 1);
                                window.scrollTo({
                                    top: 0,
                                    behavior: 'smooth'
                                });
                            }
                        });
                    }
                }
            }
        }
    </script>
@endpush
