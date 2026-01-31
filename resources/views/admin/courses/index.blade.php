@extends('layouts.admin')
@section('title', 'Course Management')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        .brand-gradient {
            background: linear-gradient(90deg, rgb(var(--color-primary) / 1) 0%, rgb(var(--color-secondary) / 1) 100%);
        }

        @@supports (background: linear-gradient(90deg, var(--color-primary) 0%, var(--color-secondary) 100%)) {
            .brand-gradient {
                background: linear-gradient(90deg, var(--color-primary) 0%, var(--color-secondary) 100%);
            }
        }
    </style>

    <div x-data="courseManager()" class="font-sans text-mainText min-h-screen">

        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center mb-10 gap-6 animate-fade-in-down">
            <div>
                <h2 class="text-3xl font-extrabold tracking-tight text-mainText">My Courses</h2>
                <p class="text-sm text-mutedText mt-1 font-medium">
                    Manage your educational content. Total Courses: <span
                        class="text-primary font-bold">{{ $courses->total() }}</span>
                </p>
            </div>

            <a href="{{ route('admin.courses.create') }}"
                class="group relative inline-flex items-center justify-center gap-2 rounded-2xl brand-gradient px-8 py-3.5 text-sm font-bold text-customWhite uppercase tracking-widest shadow-lg shadow-primary/30 transition-all duration-300 hover:shadow-primary/50 hover:-translate-y-1 overflow-hidden">
                <span class="relative z-10 flex items-center gap-2">
                    <svg class="h-5 w-5 transition-transform duration-300 group-hover:rotate-90" fill="none"
                        viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4" />
                    </svg>
                    Create Course
                </span>
                <div
                    class="absolute inset-0 -translate-x-full group-hover:translate-x-0 bg-white/20 transition-transform duration-500 ease-out skew-x-12">
                </div>
            </a>
        </div>

        <div
            class="mb-10 p-2 bg-surface border border-primary/10 rounded-[1.5rem] shadow-xl shadow-primary/5 grid grid-cols-1 md:grid-cols-12 gap-4 animate-fade-in-up">

            <div class="md:col-span-8 relative group">
                <div class="absolute inset-y-0 left-0 flex items-center pl-5 pointer-events-none">
                    <svg class="w-5 h-5 text-mutedText group-focus-within:text-primary transition-colors duration-300"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" x-model.debounce.500ms="search" placeholder="Search courses by title..."
                    class="w-full h-12 pl-12 pr-6 bg-transparent border-none text-mainText placeholder-mutedText/50 text-sm font-bold focus:ring-0 focus:outline-none rounded-xl hover:bg-primary/5 transition-colors">
            </div>

            <div class="md:col-span-4 relative border-t md:border-t-0 md:border-l border-primary/10"
                x-data="{ open: false, selectedName: 'All Categories' }">
                <button @click="open = !open" type="button"
                    class="w-full h-12 px-6 flex justify-between items-center text-sm font-bold text-mainText hover:bg-primary/5 rounded-xl transition-colors">
                    <span class="truncate" x-text="selectedName"></span>
                    <svg class="w-4 h-4 text-primary transition-transform duration-300" :class="open ? 'rotate-180' : ''"
                        fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M19 9l-7 7-7-7"></path>
                    </svg>
                </button>

                <div x-show="open" @click.away="open = false" x-transition.origin.top.right
                    class="absolute right-0 top-full mt-2 w-full md:w-64 bg-surface border border-primary/10 rounded-2xl shadow-2xl shadow-primary/10 overflow-hidden z-50 py-2">

                    <div @click="category = ''; selectedName = 'All Categories'; open = false"
                        class="px-5 py-3 hover:bg-primary/5 cursor-pointer text-sm font-bold text-mainText hover:text-primary transition-colors flex items-center justify-between">
                        All Categories
                        <span x-show="category === ''" class="w-2 h-2 rounded-full bg-primary"></span>
                    </div>

                    @foreach ($categories as $cat)
                        <div @click="category = '{{ $cat->id }}'; selectedName = '{{ $cat->name }}'; open = false"
                            class="px-5 py-3 hover:bg-primary/5 cursor-pointer text-sm font-medium text-mutedText hover:text-primary transition-colors flex items-center justify-between">
                            {{ $cat->name }}
                            <span x-show="category == '{{ $cat->id }}'" class="w-2 h-2 rounded-full bg-primary"></span>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="relative min-h-[400px]">

            <div x-show="isLoading" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8">
                <template x-for="i in 8">
                    <div
                        class="bg-surface border border-primary/5 rounded-[2rem] p-4 shadow-sm h-[400px] flex flex-col animate-pulse">
                        <div class="w-full h-48 bg-primary/5 rounded-[1.5rem] mb-5"></div>
                        <div class="h-4 w-3/4 bg-primary/5 rounded-full mb-3"></div>
                        <div class="h-3 w-1/2 bg-primary/5 rounded-full mb-6"></div>
                        <div class="mt-auto flex justify-between items-center border-t border-primary/5 pt-4">
                            <div class="h-8 w-24 bg-primary/5 rounded-lg"></div>
                            <div class="flex gap-2">
                                <div class="h-9 w-9 bg-primary/5 rounded-xl"></div>
                                <div class="h-9 w-9 bg-primary/5 rounded-xl"></div>
                            </div>
                        </div>
                    </div>
                </template>
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
                controller: null,

                init() {
                    this.$watch('search', () => this.fetchCourses());
                    this.$watch('category', () => this.fetchCourses());
                },

                async fetchCourses(url = null) {
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
                        this.fetchCourses(link.href);
                    }
                }
            }
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Delete Course?',
                text: "All lessons and resources will be removed permanently.",
                icon: 'warning',
                showCancelButton: true,
                buttonsStyling: false,
                customClass: {
                    popup: 'rounded-[2rem] p-8 bg-surface font-sans',
                    title: 'text-2xl font-bold text-mainText',
                    htmlContainer: 'text-mutedText font-medium mt-2',
                    confirmButton: 'inline-flex items-center justify-center px-6 py-3 rounded-xl bg-primary text-customWhite font-bold shadow-lg shadow-primary/20 hover:bg-secondary transition-all duration-300 ml-3',
                    cancelButton: 'inline-flex items-center justify-center px-6 py-3 rounded-xl border border-transparent font-bold text-mainText hover:bg-primary/5 transition-all duration-300'
                },

                confirmButtonText: 'Yes, Delete it!',
                cancelButtonText: 'Cancel',
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('delete-form-' + id).submit();
                }
            })
        }
    </script>
@endsection
