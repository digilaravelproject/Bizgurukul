@extends('layouts.admin')
@section('title', 'Bundle Management')

@section('content')
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <div x-data="bundleManager()" x-init="init()" class="font-sans text-mainText min-h-screen space-y-8">

        {{-- Top Bar: Header & Create Action --}}
        <div class="flex flex-col lg:flex-row justify-between items-start lg:items-center gap-6 animate-fade-in-down">
            <div class="space-y-1">
                <h2 class="text-3xl font-extrabold tracking-tight text-mainText">Bundle Collection</h2>
                <div class="flex items-center gap-2 text-sm font-medium">
                    <span class="text-mutedText">Curated offerings library</span>
                    <span class="h-1 w-1 rounded-full bg-primary/30"></span>
                    <span class="text-primary font-bold">Total: {{ $bundles->total() }}</span>
                </div>
            </div>

            <a href="{{ route('admin.bundles.create') }}"
                class="group relative inline-flex items-center justify-center gap-3 rounded-2xl brand-gradient px-8 py-4 text-xs font-black text-customWhite uppercase tracking-[2px] shadow-xl shadow-primary/30 transition-all duration-500 hover:shadow-primary/50 hover:-translate-y-1 active:scale-95 overflow-hidden">
                <span class="relative z-10 flex items-center gap-2">
                    <svg class="h-5 w-5 transition-transform duration-500 group-hover:rotate-180" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 4v16m8-8H4" />
                    </svg>
                    New Bundle
                </span>
                <div class="absolute inset-0 -translate-x-full group-hover:translate-x-0 bg-white/20 transition-transform duration-700 ease-out skew-x-12"></div>
            </a>
        </div>

        {{-- Search & Filter Bar --}}
        <div class="p-3 bg-surface border border-primary/10 rounded-[2rem] shadow-2xl shadow-primary/5 animate-fade-in-up">
            <div class="relative group">
                <div class="absolute inset-y-0 left-0 flex items-center pl-6 pointer-events-none">
                    <svg class="w-5 h-5 text-mutedText group-focus-within:text-primary transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                    </svg>
                </div>
                <input type="text" x-model.debounce.500ms="search" placeholder="Search bundles by title or keywords..."
                    class="w-full h-14 pl-14 pr-8 bg-primary/5 border-none text-mainText placeholder-mutedText/40 text-sm font-bold focus:ring-2 focus:ring-primary/20 focus:outline-none rounded-[1.5rem] transition-all">
            </div>
        </div>

        {{-- Content Area --}}
        <div class="relative min-h-[500px]">

            {{-- Modern Skeleton Loader --}}
            <div x-show="isLoading" x-transition.opacity.duration.300ms class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 absolute inset-0 z-10">
                <template x-for="i in 4">
                    <div class="bg-surface border border-primary/5 rounded-[2.5rem] p-5 h-[420px] flex flex-col animate-pulse">
                        <div class="w-full h-52 bg-primary/5 rounded-[2rem] mb-6"></div>
                        <div class="h-5 w-4/5 bg-primary/5 rounded-full mb-3"></div>
                        <div class="h-5 w-2/5 bg-primary/5 rounded-full mb-8"></div>
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
                 id="bundles-container"
                 @click="handlePagination($event)">
                @include('admin.bundles.partials.table')
            </div>
        </div>
    </div>

    <script>
        function bundleManager() {
            return {
                search: '',
                isLoading: false,
                controller: null,

                init() {
                    this.$watch('search', () => this.fetchBundles());
                },

                async fetchBundles(url = null) {
                    if (this.controller) this.controller.abort();
                    this.controller = new AbortController();
                    this.isLoading = true;

                    let targetUrl = url ? new URL(url) : new URL("{{ route('admin.bundles.index') }}", window.location.origin);
                    if (this.search) targetUrl.searchParams.set('search', this.search);

                    try {
                        let response = await fetch(targetUrl, {
                            headers: { "X-Requested-With": "XMLHttpRequest" },
                            signal: this.controller.signal
                        });
                        if (!response.ok) throw new Error('Network error');
                        let html = await response.text();
                        document.getElementById('bundles-container').innerHTML = html;
                    } catch (error) {
                        if (error.name !== 'AbortError') console.error('Fetch error:', error);
                    } finally {
                        this.isLoading = false;
                    }
                },

                handlePagination(event) {
                    let link = event.target.closest('.pagination a');
                    if (link) {
                        event.preventDefault();
                        window.scrollTo({ top: 0, behavior: 'smooth' });
                        this.fetchBundles(link.href);
                    }
                }
            }
        }

        function confirmDelete(id) {
            Swal.fire({
                title: 'Confirm Deletion',
                text: "Removing this bundle will revoke access for new purchases.",
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
                confirmButtonText: 'Delete Bundle',
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
