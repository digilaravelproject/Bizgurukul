@extends('layouts.user.app')

@section('content')
    <div class="p-6 lg:p-10 relative">
        {{-- Aesthetic Background Accents --}}
        <div class="absolute -top-24 -right-24 w-96 h-96 bg-primary/5 blur-[120px] rounded-full pointer-events-none"></div>
        <div class="absolute top-1/2 -left-24 w-72 h-72 bg-secondary/5 blur-[100px] rounded-full pointer-events-none"></div>

        <div class="relative z-10 mb-8 flex flex-col md:flex-row md:items-end justify-between gap-4">
            <div>
                <p class="text-primary font-black uppercase tracking-[0.3em] text-[10px] mb-2 px-1">Growth Opportunities</p>
                <h1 class="text-4xl md:text-5xl font-black text-mainText tracking-tight">Explore <span class="text-white brand-gradient bg-clip-text text-transparent">Careers</span></h1>
                <p class="text-mutedText text-sm font-medium mt-2 max-w-xl">Accelerate your professional journey with handpicked opportunities from our top-tier partner companies.</p>
            </div>
            
            <div class="flex items-center gap-3 bg-surface/50 backdrop-blur-md px-4 py-2 rounded-2xl border border-primary/10 shadow-sm">
                <span class="flex h-2.5 w-2.5 rounded-full bg-green-500 animate-pulse"></span>
                <span class="text-[10px] font-black text-mainText uppercase tracking-widest">Live Openings</span>
            </div>
        </div>

        <div x-data="jobFilter()" x-init="fetchJobs()" class="relative z-10 flex flex-col lg:flex-row gap-8">
            <!-- Filters Sidebar -->
            <aside class="w-full lg:w-80 shrink-0">
                <div class="bg-surface p-5 md:p-6 rounded-3xl border border-primary/10 premium-shadow sticky top-24">
                    <div class="flex items-center justify-between mb-6">
                        <h3 class="font-black text-mainText text-sm uppercase tracking-widest flex items-center gap-3">
                            <i class="fas fa-sliders-h text-primary"></i>
                            Refine Search
                        </h3>
                        <button @click="resetFilters()" class="text-[10px] font-black text-primary uppercase tracking-widest hover:text-secondary transition-colors underline decoration-2 underline-offset-4">
                            Reset
                        </button>
                    </div>

                    <div class="space-y-6">
                        <!-- Search -->
                        <div>
                            <label class="text-[10px] font-black text-mutedText/50 uppercase tracking-[0.2em] block mb-2.5 px-1">Keywords</label>
                            <div class="relative group">
                                <i class="fas fa-search absolute left-4 top-1/2 -translate-y-1/2 text-mutedText/40 text-xs transition-colors group-focus-within:text-primary"></i>
                                <input type="text" x-model.debounce.500ms="filters.search"
                                    class="w-full bg-navy/5 border border-primary/10 rounded-2xl py-2.5 pl-11 pr-4 text-xs font-bold text-mainText placeholder-mutedText/40 focus:bg-white focus:border-primary/40 focus:ring-8 focus:ring-primary/5 transition-all outline-none"
                                    placeholder="Company, title, roles...">
                            </div>
                        </div>

                        <!-- Location -->
                        <div>
                            <label class="text-[10px] font-black text-mutedText/50 uppercase tracking-[0.2em] block mb-2.5 px-1">Location</label>
                            <div class="relative">
                                <select x-model="filters.location" @change="fetchJobs()" 
                                    class="w-full bg-navy/5 border border-primary/10 rounded-2xl py-2.5 px-4 text-xs font-bold text-mainText focus:bg-white focus:border-primary/40 focus:ring-8 focus:ring-primary/5 transition-all outline-none appearance-none cursor-pointer">
                                    <option value="">Global / All Locations</option>
                                    @foreach($locations as $loc)
                                        <option value="{{ $loc->id }}">{{ $loc->name }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-mutedText/40 text-[10px] pointer-events-none"></i>
                            </div>
                        </div>

                        <!-- Experience -->
                        <div>
                            <label class="text-[10px] font-black text-mutedText/50 uppercase tracking-[0.2em] block mb-2.5 px-1">Experience Level</label>
                            <div class="relative">
                                <select x-model="filters.experience" @change="fetchJobs()" 
                                    class="w-full bg-navy/5 border border-primary/10 rounded-2xl py-2.5 px-4 text-xs font-bold text-mainText focus:bg-white focus:border-primary/40 focus:ring-8 focus:ring-primary/5 transition-all outline-none appearance-none cursor-pointer">
                                    <option value="">Any Experience</option>
                                    @foreach($experiences as $exp)
                                        <option value="{{ $exp->id }}">{{ $exp->name }}</option>
                                    @endforeach
                                </select>
                                <i class="fas fa-chevron-down absolute right-4 top-1/2 -translate-y-1/2 text-mutedText/40 text-[10px] pointer-events-none"></i>
                            </div>
                        </div>

                        <!-- Skills -->
                        <div>
                            <label class="text-[10px] font-black text-mutedText/50 uppercase tracking-[0.2em] block mb-3 px-1">Top Skills</label>
                            <div class="max-h-64 overflow-y-auto custom-scrollbar space-y-3 pr-2">
                                @foreach($skills as $skill)
                                    <label class="flex items-center group cursor-pointer">
                                        <div class="relative flex items-center">
                                            <input type="checkbox" value="{{ $skill->id }}" x-model="filters.skills" @change="fetchJobs()"
                                                class="peer h-5 w-5 border-2 border-primary/10 rounded-lg checked:bg-primary checked:border-primary transition-all cursor-pointer appearance-none">
                                            <i class="fas fa-check absolute opacity-0 peer-checked:opacity-100 text-[10px] text-white left-1/2 top-1/2 -translate-x-1/2 -translate-y-1/2 transition-opacity pointer-events-none"></i>
                                        </div>
                                        <span class="ml-3 text-xs text-mutedText group-hover:text-primary transition-colors font-bold uppercase tracking-wider">{{ $skill->name }}</span>
                                    </label>
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            </aside>

            <!-- Job List Area -->
            <main class="flex-1">
                <div x-show="loading" class="flex flex-col items-center justify-center py-32 bg-surface rounded-[2.5rem] border border-primary/10 border-dashed">
                    <div class="relative h-20 w-20 mb-6">
                        <div class="absolute inset-0 rounded-3xl border-4 border-primary/5 rotate-45"></div>
                        <div class="absolute inset-0 rounded-3xl border-4 border-primary border-t-transparent animate-spin"></div>
                    </div>
                    <p class="text-mutedText font-black uppercase tracking-[0.4em] text-[10px] animate-pulse">Curating opportunities...</p>
                </div>

                <div x-show="!loading" x-html="jobListHtml" class="space-y-6">
                    <!-- Content will be injected by AJAX -->
                </div>
            </main>
        </div>
    </div>

    <script>
        document.addEventListener('alpine:init', () => {
            Alpine.data('jobFilter', () => ({
                filters: {
                    search: '',
                    location: '',
                    experience: '',
                    skills: []
                },
                loading: false,
                jobListHtml: '',
                init() {
                    this.$watch('filters.search', () => this.fetchJobs());
                },
                resetFilters() {
                    this.filters = {
                        search: '',
                        location: '',
                        experience: '',
                        skills: []
                    };
                    this.fetchJobs();
                },
                fetchJobs() {
                    this.loading = true;
                    const params = new URLSearchParams();
                    if (this.filters.search) params.append('search', this.filters.search);
                    if (this.filters.location) params.append('location', this.filters.location);
                    if (this.filters.experience) params.append('experience', this.filters.experience);
                    this.filters.skills.forEach(s => params.append('skills[]', s));

                    fetch(`{{ route('student.career_jobs.fetch') }}?${params.toString()}`)
                        .then(res => res.text())
                        .then(html => {
                            this.jobListHtml = html;
                            this.loading = false;
                        })
                        .catch(err => {
                            console.error(err);
                            this.loading = false;
                            this.jobListHtml = `
                                <div class="bg-red-50/50 border border-red-100 p-12 rounded-[2.5rem] text-center backdrop-blur-sm">
                                    <div class="h-16 w-16 bg-red-100 text-red-600 rounded-3xl flex items-center justify-center mx-auto mb-6 shadow-lg shadow-red-200/50">
                                        <i class="fas fa-exclamation-circle text-2xl"></i>
                                    </div>
                                    <h3 class="text-xl font-black text-red-800 uppercase tracking-widest mb-2">Sync Error</h3>
                                    <p class="text-red-600/70 text-sm font-medium">We couldn't connect to the opportunities server.</p>
                                    <button @click="fetchJobs()" class="mt-8 px-10 py-3 brand-gradient text-white rounded-2xl font-black text-xs uppercase tracking-widest hover:scale-105 active:scale-95 transition-all shadow-xl shadow-primary/20">Try Again</button>
                                </div>
                            `;
                        });
                }
            }));
        });
    </script>
@endsection