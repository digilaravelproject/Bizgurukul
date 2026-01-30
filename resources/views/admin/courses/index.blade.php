@extends('layouts.admin')
@section('title', 'Course Management')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div x-data="courseManager()" class="font-sans antialiased">

        {{-- 1. HEADER --}}
        <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8 gap-4 animate-fade-in">
            <div>
                <h2 class="text-2xl font-black text-mainText tracking-tight">Course Management</h2>
                <p class="text-sm text-mutedText mt-1 font-medium">Create and manage your educational content effortlessly.
                </p>
            </div>

            <a href="{{ route('admin.courses.create') }}"
                class="w-full md:w-auto inline-flex items-center justify-center gap-2 rounded-[1.25rem] brand-gradient px-6 py-3 text-xs font-black text-white uppercase tracking-widest shadow-lg shadow-primary/20 hover:shadow-primary/40 hover:-translate-y-0.5 transition-all duration-300">
                <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                </svg>
                Create New Course
            </a>
        </div>

        {{-- 2. FILTERS --}}
        <div class="mb-8 grid grid-cols-1 md:grid-cols-12 gap-4 animate-fade-in">

            {{-- Search Input --}}
            <div class="md:col-span-8 relative">
                <span class="absolute inset-y-0 left-0 flex items-center pl-5 text-primary">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </span>
                <input type="text" x-model.debounce.500ms="search" placeholder="Search courses by title..."
                    class="w-full pl-14 pr-6 py-4 bg-white border border-primary/10 text-mainText placeholder-mutedText/40 rounded-2xl focus:ring-2 focus:ring-primary/5 focus:border-primary transition-all outline-none shadow-sm text-sm font-bold">
            </div>

            {{-- Category Filter --}}
            <div class="md:col-span-4 relative" x-data="{ open: false, selectedName: 'All Categories' }">
                <button @click="open = !open" type="button"
                    class="w-full pl-6 pr-12 py-4 bg-white border border-primary/10 text-mainText font-bold rounded-2xl focus:ring-2 focus:ring-primary/5 focus:border-primary transition-all outline-none shadow-sm text-sm text-left flex justify-between items-center">
                    <span x-text="selectedName"></span>
                    <svg class="w-5 h-5 text-primary transition-transform" :class="open ? 'rotate-180' : ''" fill="none"
                        stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div x-show="open" @click.away="open = false"
                    class="absolute z-50 w-full mt-2 bg-white border border-primary/10 rounded-2xl shadow-xl overflow-hidden p-2">

                    <div @click="category = ''; selectedName = 'All Categories'; open = false"
                        class="px-4 py-3 hover:bg-primary/5 rounded-xl cursor-pointer transition-colors text-sm font-medium">
                        All Categories
                    </div>

                    @foreach ($categories as $cat)
                        <div @click="category = '{{ $cat->id }}'; selectedName = '{{ $cat->name }}'; open = false"
                            class="px-4 py-3 hover:bg-primary/5 rounded-xl cursor-pointer transition-colors text-sm font-medium">
                            {{ $cat->name }}
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- 3. CONTENT AREA --}}
        <div class="relative min-h-[400px]">

            {{-- SKELETON LOADING UI --}}
            <div x-show="isLoading"
                class="bg-white border border-primary/5 rounded-[2rem] overflow-hidden shadow-xl shadow-primary/5">
                <div class="animate-pulse">
                    <div class="h-16 bg-primary/5 border-b border-primary/5"></div>
                    <template x-for="i in 5">
                        <div class="flex items-center px-8 py-5 border-b border-primary/5 gap-4">
                            <div class="h-12 w-16 bg-navy rounded-xl"></div>
                            <div class="flex-1 space-y-2">
                                <div class="h-3 w-48 bg-navy rounded"></div>
                                <div class="h-2 w-20 bg-navy rounded"></div>
                            </div>
                            <div class="h-4 w-24 bg-navy rounded hidden md:block"></div>
                            <div class="h-4 w-16 bg-navy rounded"></div>
                            <div class="h-8 w-8 bg-navy rounded-xl ml-auto"></div>
                        </div>
                    </template>
                </div>
            </div>

            {{-- REAL DATA CONTAINER --}}
            <div x-show="!isLoading" id="courses-container" @click="handlePagination($event)">
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
                controller: null, // To cancel previous requests

                init() {
                    this.$watch('search', value => {
                        this.fetchCourses();
                    });
                    this.$watch('category', value => {
                        this.fetchCourses();
                    });
                },

                async fetchCourses(url = null) {
                    // Abort previous request to prevent race conditions
                    if (this.controller) this.controller.abort();
                    this.controller = new AbortController();

                    this.isLoading = true;

                    let targetUrl = url ? new URL(url) : new URL("{{ route('admin.courses.index') }}", window.location
                        .origin);
                    if (this.search) targetUrl.searchParams.set('search', this.search);
                    if (this.category) targetUrl.searchParams.set('category_id', this.category);

                    try {
                        let response = await fetch(targetUrl, {
                            headers: {
                                "X-Requested-With": "XMLHttpRequest"
                            },
                            signal: this.controller.signal
                        });

                        if (!response.ok) throw new Error('Failed to fetch');

                        let html = await response.text();
                        document.getElementById('courses-container').innerHTML = html;
                    } catch (error) {
                        if (error.name !== 'AbortError') {
                            console.error(error);
                            Swal.fire({
                                icon: 'error',
                                title: 'Error',
                                text: 'Could not load courses.',
                                confirmButtonColor: '#F7941D'
                            });
                        }
                    } finally {
                        this.isLoading = false;
                    }
                },

                handlePagination(event) {
                    let link = event.target.closest('.pagination a');
                    if (link) {
                        event.preventDefault();
                        this.fetchCourses(link.href);
                    }
                }
            }
        }
    </script>
@endsection
