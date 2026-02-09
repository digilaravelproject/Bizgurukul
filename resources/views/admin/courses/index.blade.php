@extends('layouts.admin')
@section('title', 'Course Management')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div x-data="courseManager()" class="font-sans text-mainText min-h-screen space-y-8">

        {{-- 1. HEADER SECTION --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 animate-fade-in-down">
            <div>
                <h2 class="text-3xl font-extrabold tracking-tight text-mainText uppercase">My Courses</h2>
                <p class="text-sm text-mutedText mt-1 font-medium italic">
                    Managing <span class="text-primary font-black">{{ $courses->total() }}</span> educational assets
                </p>
            </div>

            <a href="{{ route('admin.courses.create') }}"
                class="group relative inline-flex items-center justify-center gap-3 rounded-2xl brand-gradient px-8 py-4 text-xs font-black text-customWhite uppercase tracking-widest shadow-xl shadow-primary/30 transition-all duration-500 hover:shadow-primary/50 hover:-translate-y-1 active:scale-95 overflow-hidden">
                <span class="relative z-10 flex items-center gap-2">
                    <svg class="h-5 w-5 transition-transform duration-500 group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                    </svg>
                    Create New Course
                </span>
                <div class="absolute inset-0 -translate-x-full group-hover:translate-x-0 bg-white/20 transition-transform duration-700 ease-out skew-x-12"></div>
            </a>
        </div>

        {{-- 2. FILTER & SEARCH BAR --}}
        <div class="p-3 bg-surface border border-primary/10 rounded-[2rem] shadow-2xl shadow-primary/5 grid grid-cols-1 md:grid-cols-12 gap-4 animate-fade-in-up">

            {{-- Search Input --}}
            <div class="md:col-span-8 relative group">
                <div class="absolute inset-y-0 left-0 flex items-center pl-6 pointer-events-none">
                    <svg class="w-5 h-5 text-mutedText group-focus-within:text-primary transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" x-model.debounce.500ms="search" placeholder="Search courses by title..."
                    class="w-full h-14 pl-14 pr-8 bg-primary/5 border-none text-mainText font-bold placeholder-mutedText/40 rounded-2xl focus:ring-0 focus:outline-none transition-all">
            </div>

            {{-- Category Dropdown --}}
            <div class="md:col-span-4 relative border-t md:border-t-0 md:border-l border-primary/10"
                x-data="{ open: false, selectedName: 'All Categories' }">
                <button @click="open = !open" type="button"
                    class="w-full h-14 px-8 flex justify-between items-center text-xs font-black text-mutedText uppercase tracking-widest hover:bg-primary/5 rounded-2xl transition-all">
                    <span class="truncate" x-text="selectedName"></span>
                    <svg class="w-4 h-4 text-primary transition-transform duration-500" :class="open ? 'rotate-180' : ''" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div x-show="open" @click.away="open = false"
                     x-transition:enter="transition ease-out duration-200"
                     x-transition:enter-start="opacity-0 scale-95 translate-y-2"
                     x-transition:enter-end="opacity-100 scale-100 translate-y-0"
                     class="absolute right-0 top-full mt-3 w-full md:w-72 bg-surface border border-primary/10 rounded-3xl shadow-2xl shadow-primary/20 overflow-hidden z-[60] py-3">

                    <div @click="category = ''; selectedName = 'All Categories'; open = false"
                        class="px-6 py-4 hover:bg-primary/10 cursor-pointer text-[10px] font-black uppercase tracking-widest transition-all flex items-center justify-between"
                        :class="category === '' ? 'text-primary bg-primary/5' : 'text-mainText'">
                        All Categories
                        <div x-show="category === ''" class="w-2 h-2 rounded-full bg-primary shadow-[0_0_10px_rgba(247,148,29,0.5)]"></div>
                    </div>

                    @foreach ($categories as $cat)
                        <div @click="category = '{{ $cat->id }}'; selectedName = '{{ $cat->name }}'; open = false"
                            class="px-6 py-4 hover:bg-primary/10 cursor-pointer text-[10px] font-bold uppercase tracking-widest transition-all flex items-center justify-between"
                            :class="category == '{{ $cat->id }}' ? 'text-primary bg-primary/5' : 'text-mutedText'">
                            {{ $cat->name }}
                            <div x-show="category == '{{ $cat->id }}'" class="w-2 h-2 rounded-full bg-primary shadow-[0_0_10px_rgba(247,148,29,0.5)]"></div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        {{-- 3. CONTENT AREA --}}
        <div class="relative min-h-[500px]">

            {{-- Skeleton Loader --}}
            <div x-show="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <template x-for="i in 4">
                    <div class="bg-surface border border-primary/5 rounded-[2.5rem] p-5 h-[420px] flex flex-col animate-pulse">
                        <div class="w-full h-52 bg-primary/5 rounded-[2rem] mb-6"></div>
                        <div class="h-4 w-3/4 bg-primary/5 rounded-full mb-3"></div>
                        <div class="h-3 w-1/2 bg-primary/5 rounded-full mb-8"></div>
                        <div class="mt-auto flex justify-between items-center pt-5 border-t border-primary/5">
                            <div class="h-10 w-24 bg-primary/5 rounded-xl"></div>
                            <div class="flex gap-3">
                                <div class="h-11 w-11 bg-primary/5 rounded-2xl"></div>
                                <div class="h-11 w-11 bg-primary/5 rounded-2xl"></div>
                            </div>
                        </div>
                    </div>
                </template>
            </div>

            {{-- Real Data --}}
            <div x-show="!isLoading"
                 x-transition:enter="transition ease-out duration-500"
                 x-transition:enter-start="opacity-0 translate-y-4"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 id="courses-container" @click="handlePagination($event)">
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
                controller: null,

                init() {
                    this.$watch('search', () => this.fetchCourses());
                    this.$watch('category', () => this.fetchCourses());
                },

                async fetchCourses(url = null) {
                    if (this.controller) this.controller.abort();
                    this.controller = new AbortController();
                    this.isLoading = true;

                    let targetUrl = url ? new URL(url) : new URL("{{ route('admin.courses.index') }}", window.location.origin);
                    if (this.search) targetUrl.searchParams.set('search', this.search);
                    if (this.category) targetUrl.searchParams.set('category_id', this.category);

                    try {
                        let response = await fetch(targetUrl, {
                            headers: { "X-Requested-With": "XMLHttpRequest" },
                            signal: this.controller.signal
                        });
                        if (!response.ok) throw new Error('Failed');
                        let html = await response.text();
                        document.getElementById('courses-container').innerHTML = html;
                    } catch (error) {
                        if (error.name !== 'AbortError') console.error(error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                handlePagination(event) {
                    let link = event.target.closest('.pagination a');
                    if (link) {
                        event.preventDefault();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        this.fetchCourses(link.href);
                    }
                }
            }
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Confirm Deletion',
                text: "All associated lessons and data will be permanently removed.",
                icon: 'warning',
                showCancelButton: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-[2.5rem] p-10 bg-surface border border-primary/10',
                    title: 'text-2xl font-black text-mainText uppercase tracking-tight',
                    htmlContainer: 'text-mutedText font-medium mt-3',
                    confirmButton: 'brand-gradient px-8 py-3 rounded-2xl text-customWhite font-black text-xs uppercase tracking-widest shadow-xl shadow-primary/20 hover:opacity-90 transition-all ml-4',
                    cancelButton: 'px-8 py-3 rounded-2xl font-black text-xs uppercase tracking-widest text-mutedText hover:bg-primary/5 transition-all'
                },
                confirmButtonText: 'Yes, Delete Course',
                cancelButtonText: 'Keep it',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
@endsection
