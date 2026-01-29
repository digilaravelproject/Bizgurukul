@extends('layouts.admin')
@section('title', 'Course Management')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div x-data="courseManager()" x-init="init()" class="container-fluid font-sans p-4 md:p-6">

        {{-- 1. HEADER & ACTIONS --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-6 gap-4 animate-[fadeIn_0.3s_ease-out]">
            <div>
                <h2 class="text-2xl font-extrabold text-white tracking-tight">Course Management</h2>
                <p class="text-xs text-mutedText mt-1">Create, edit and manage your LMS courses.</p>
            </div>

            <a href="{{ route('admin.courses.create') }}"
                class="w-full md:w-auto inline-flex items-center justify-center gap-2 rounded-xl bg-gradient-to-r from-primary to-indigo-600 px-5 py-2.5 text-xs font-bold text-white shadow-lg shadow-primary/25 hover:shadow-primary/40 hover:-translate-y-0.5 transition-all duration-300 border border-white/10">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4" /></svg>
                Create Course
            </a>
        </div>

        {{-- 2. FILTERS (Search + Category) --}}
        <div class="mb-6 grid grid-cols-1 md:grid-cols-3 gap-4 animate-[fadeIn_0.4s_ease-out]">

            {{-- Search Bar --}}
            <div class="relative md:col-span-2">
                <span class="absolute inset-y-0 left-0 flex items-center pl-3.5 text-mutedText">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
                </span>
                <input type="text" x-model.debounce.500ms="search" @input="fetchCourses()" placeholder="Search by course title..."
                    class="w-full pl-11 pr-4 py-3 bg-white/5 border border-white/10 text-white placeholder-mutedText/50 rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary/50 outline-none transition shadow-sm backdrop-blur-sm text-sm">
            </div>

            {{-- Category Dropdown --}}
            <div class="relative">
                <select x-model="category" @change="fetchCourses()"
                    class="w-full pl-4 pr-10 py-3 bg-white/5 border border-white/10 text-white rounded-xl focus:ring-2 focus:ring-primary/50 focus:border-primary/50 outline-none transition shadow-sm backdrop-blur-sm text-sm appearance-none">
                    <option value="">All Categories</option>
                    @foreach($categories as $cat)
                        <option value="{{ $cat->id }}">{{ $cat->name }}</option>
                    @endforeach
                </select>
                <div class="absolute inset-y-0 right-0 flex items-center pr-3 pointer-events-none text-mutedText">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path></svg>
                </div>
            </div>
        </div>

        {{-- 3. CONTENT AREA --}}
        <div class="relative min-h-[400px]">

            {{-- Loading Overlay --}}
            <div x-show="isLoading" class="absolute inset-0 z-20 flex items-center justify-center bg-navy/50 backdrop-blur-sm rounded-2xl transition-opacity">
                <div class="flex flex-col items-center">
                    <svg class="animate-spin h-10 w-10 text-primary" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <span class="text-xs text-white mt-2 font-bold tracking-wide">LOADING...</span>
                </div>
            </div>

            {{-- The Content (Loaded via AJAX) --}}
            <div id="courses-container" @click="handlePagination($event)">
                @include('admin.courses.partials.table')
            </div>
        </div>

    </div>

    <script>
        function courseManager() {
            return {
                search: '',
                category: '',
                isLoading: false,

                init() {},

                async fetchCourses(url = null) {
                    this.isLoading = true;
                    let fetchUrl = url ? url : "{{ route('admin.courses.index') }}";

                    const separator = fetchUrl.includes('?') ? '&' : '?';
                    const params = new URLSearchParams({ search: this.search, category_id: this.category }).toString();

                    if(!url) {
                        fetchUrl = `${fetchUrl}${separator}${params}`;
                    } else {
                        if(!fetchUrl.includes('search=')) fetchUrl += `&search=${this.search}`;
                        if(!fetchUrl.includes('category_id=')) fetchUrl += `&category_id=${this.category}`;
                    }

                    try {
                        let response = await fetch(fetchUrl, { headers: { "X-Requested-With": "XMLHttpRequest" } });
                        let html = await response.text();
                        document.getElementById('courses-container').innerHTML = html;
                    } catch (error) {
                        console.error(error);
                        Swal.fire({ icon: 'error', title: 'Error', text: 'Failed to load courses.', background: '#1E293B', color: '#fff' });
                    } finally {
                        this.isLoading = false;
                    }
                },

                handlePagination(event) {
                    let link = event.target.closest('.pagination a');
                    if (link) { event.preventDefault(); this.fetchCourses(link.href); }
                }
            }
        }
    </script>
@endsection
